<include file="admin@header" />
<script type="text/javascript">
    //得到多选框ID集合
    function get_ids() {
        var ids = '';
        $("input[name='ids[]']").each(function() {
            if($(this).attr('checked')=='checked') {
                ids += '|'+$(this).val();
            };
        });
        if(ids!=''){
            return ids.substring(1);
        }else{
            return '';
        }
    }
    //批量设置
    function setStatus(v){
        var ids = get_ids();
        if(ids==''){
            alert('请选择待设置的id');
            return false;
        }
        $("input[name='status']").val(v);
        $('#myform').attr('action','<{:url("setStatus")}>');
        $('#myform').submit();
    };

    $(function(){
	

        //批量锁定
        $("#btn_lock").click(function(){
            var ids = get_ids();
            if(ids==''){
                alert('请选择待锁定的用户');
                return false;
            }
            if(!confirm('请确认是否锁定用户？')){
                return false;
            }
            $("input[name='do']").val('lock');
            $('#myform').attr('action','<{:url("edit")}>');
            $('#myform').submit();
        });

    });
</script>
<div class="pad-10">
    <div class="content-menu line-x blue"><{$topnav}></div>
    <div class="explain-col mar-b8" id="search_form">
        <form name="search_form" action="" method="post" id="search_form">
            软件厂商：
            <input name="search[company]" type="text" class="input-text" value="<{$company}>" size="10" />
            软件名称：
            <input name="search[name]" type="text" class="input-text" value="<{$name}>" size="15" />
            联系人：
            <input name="search[contact_name]" type="text" class="input-text" value="<{$contact_name}>" size="15" />
             联系电话：
            <input name="search[contact_phone]" type="text" class="input-text" value="<{$contact_phone}>" size="15" />  
            &nbsp; 状态：
            <{$status_select}>
             &nbsp; 添加时间：
            <{$input_add_time}>
             &nbsp; 更新时间：
            <{$input_update_time}>
            &nbsp;	
            <input type="submit" name="dosubmit" class="button" value="搜索" />
        </form>
    </div>
    <div class="table-list">
        <form name="myform" action="#" method="post" id="myform">
            <table width="100%" cellspacing="0">
                <tr>
                    <th width="3%"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
                    <th width="10%">软件厂商</th>
                    <th width="16%">软件名称</th>
                    <th width="16%">官网地址</th>
                    <th width="10%">联系人</th>
                    <th width="16%">联系电话</th>
                    <th width="5%">状态</th>
                    <th width="13%">添加时间/更新时间</th>
                    <th width="50%">管理操作</th>
                    <th width="10%"></th>
                </tr>
                <foreach from="lists" item="v">
                    <tr>
                        <td><if><input name="ids[]" value="<{$v.id}>" type="checkbox" /></if></td>
                    <td><{$v.company}></td>
                    <td><{$v.name}></td>
                    <td><{$v.url}></td>
                    <td><{$v.contact_name}></td>
                    <td><{$v.contact_phone}></td>
                    <td><{$v.status_tips}></td>
                <td><{$v.add_time|date='Y-m-d H:i:s',###}><br/>
                <{$v.update_time|date='Y-m-d H:i:s',###}></td>
                    <td>
                        <a href="<{:url('edit?id='.$v['id'])}>">修改</a>&nbsp;&nbsp;
                        <a href="<{:url('versionIndex?sort_id='.$v['id'])}>/menu_id/30">查看版本</a>&nbsp;&nbsp;
                    </td>
                    <td></td>
                    </tr>
                </foreach>
            </table>
            <div class="btn">
                <label for="check_box">全选/取消</label>
                <input type="hidden" class="button" name="status"  id="status"  value="" />
                <input type="button" class="button" name="btn_close" id="btn_close" onclick="setStatus(0)" value="关闭" />
                <input type="button" class="button" name="btn_open" id="btn_open" onclick="setStatus(1)"  value="开启" />
            </div>
        </form>
    </div>
    <div class="pages"><{$pages}></div>
</div>

<include file="admin@footer" />
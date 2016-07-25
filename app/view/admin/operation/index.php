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
</script>
<div class="pad-10">
    <div class="content-menu line-x blue"><{$topnav}></div>
    <div class="explain-col mar-b8" id="search_form">
        <form name="search_form" action="" method="post" id="search_form">
            操作ID：
            <input name="search[id]" type="text" class="input-text" value="<{$id}>" size="3" />
            名称：
            <input name="search[type]" type="text" class="input-text" value="<{$type}>" size="15" />
            回调ID：
            <input name="search[cid]" type="text" class="input-text" value="<{$cid}>" size="3" />
            状态：
            <{$status_select}>
            &nbsp;	
            <input type="submit" name="dosubmit" class="button" value="搜索" />
        </form>
    </div>
    <div class="table-list">
        <form name="myform" action="#" method="post" id="myform">
            <table width="100%" cellspacing="0">
                <tr>
                    <th width="3%"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
                    <th width="1%">ID</th>
                    <th width="10%" style="text-align:left">名称</th>
                    <th width="18%" style="text-align:left">action</th>
                    <th width="5%">回调ID</th>
                    <th width="5%">状态</th>
                    <th width="8%">操作</th>
                    <th width="10%"></th>
                </tr>
                <foreach from="lists" item="v">
                    <tr>
                    <td><if><input name="ids[]" value="<{$v.id}>" type="checkbox" /></if></td>
                    <td><{$v.id}></td>
                    <td style="text-align:left"><{$v.type}></td>
                    <td style="text-align:left"><{$v.action}></td>
                    <td><{$v.cid}></td>
                    <td><{$v.status_tips}></td>
                    <td>
                      <a href="<{:url('edit?id='.$v['id'])}>">编辑</a>
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
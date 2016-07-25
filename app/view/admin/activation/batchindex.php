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
            激活码：
            <input name="search[bid]" type="hidden" class="input-text" value="<{$bid}>" size="32" />
            <input name="search[code]" type="text" class="input-text" value="<{$code}>" size="32" />
             时间：
            <{$input_start_time}>
            - 
            <{$input_end_time}>
            
            使用状态：
             <{$status_select}>
            制卡状态：
            <{$cstatus_select}>
            出售状态：
            <{$sstatus_select}>
            &nbsp;	
            <input type="submit" name="dosubmit" class="button" value="搜索" />
        </form>
    </div>
    <div class="table-list">
        <form name="myform" action="#" method="post" id="myform">
            <input name="bid" type="hidden" class="input-text" value="<{$bid}>" size="32" />
            <table width="100%" cellspacing="0">
                <tr>
                    <th width="3%"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
                    <th width="3%">批次ID</th>
                    <th width="25%">激活码</th>
                    <th width="8%">开始时间</th>
                    <th width="8%">结束时间</th>
                    <th width="6%">制卡状态</th>
                    <th width="6%">出售状态</th>
                    <th width="6%">使用状态</th>
                    <th width="3%">软件ID</th>
                    <th width="3%">用户ID</th>
                    <th width="3%">客户端ID</th>
                    <th width="6%">使用次数</th>
                    <th width="20%">操作</th>
                    <th width="15%"></th>
                </tr>
                <foreach from="lists" item="v">
                    <tr>
                    <td><if><input name="ids[]" value="<{$v.id}>" type="checkbox" /></if></td>
                    <td><{$v.bid}></td>
                    <td><{$v.code}></td>
                    <td><{$v.start_time|date='Y-m-d',###}></td>
                    <td><{$v.end_time|date='Y-m-d',###}></td>
                    <td><{$v.cstatus_tips}></td>
                    <td><{$v.sstatus_tips}></td>
                    <td><{$v.status_tips}></td>
                    <td><{$v.sort_id}></td>
                    <td><{$v.uid}></td>
                    <td><{$v.sort_client_id}></td>
                    <td><{$v.num}>次</td>
                    <td>
                         <a href="<{:url('codeEdit?bid='.$v['bid'].'&id='.$v['id'].'&code='.$v['code'])}>"><if condition="$v.status3 eq 0">编辑</if></a>
                        <a href="<{:url('setStatus?id='.$v['id'].'&code='.$v['code'].'&status=-1')}>"><if condition="$v.status3 eq 0">删除</if></a>
                        <a href="<{:url('setStatus?id='.$v['id'].'&code='.$v['code'].'&status=2')}>"><if condition="$v.status3 eq 1">禁用</if></a>
                          <a href="<{:url('setStatus?id='.$v['id'].'&code='.$v['code'].'&status=1')}>"><if condition="$v.status3 eq 2">启用</if></a>
                    </td>
                    <td></td>
                    </tr>
                </foreach>
            </table>
            <div class="btn">
                <label for="check_box">全选/取消</label>
                <input type="hidden" class="button" name="status"  id="status"  value="" />
                <input type="button" class="button" name="btn_close" id="btn_close" onclick="setStatus(2)" value="禁用" />
                <input type="button" class="button" name="btn_open" id="btn_open" onclick="setStatus(1)"  value="开启" />
            </div>
        </form>
    </div>
    <div class="pages"><{$pages}></div>
</div>

<include file="admin@footer" />
<script type="text/javascript">
    $(".content-menu").prepend("<a class='on' href='<{:url('Index?menu_id=33')}>'><em>软件激活码</em></a>");
</script>
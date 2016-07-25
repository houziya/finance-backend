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
    function setStatus(o,v){
        var ids = get_ids();
        if(ids==''){
            alert('请选择待设置的id');
            return false;
        }
        if(o == 'sstatus'){
           $("input[name='sstatus']").val(v);
        }else{
           $("input[name='cstatus']").val(v); 
        }
        $('#myform').attr('action','<{:url("setBatchStatus")}>');
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
            批次ID：
            <input name="search[id]" type="text" class="input-text" value="<{$id}>" size="2" />
            批次号：
            <input name="search[batch_id]" type="text" class="input-text" value="<{$batch_id}>" size="15" />
            标题：
            <input name="search[title]" type="text" class="input-text" value="<{$title}>" size="15" />
            时间：
            <{$input_start_time}>
            - 
            <{$input_end_time}>
            
            生成状态：
             <{$mstatus_select}>
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
            <table width="100%" cellspacing="0">
                <tr>
                    <th width="3%"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
                    <th width="5%">ID</th>
                    <th width="16%">批次号</th>
                    <th width="16%">标题</th>
                    <th width="16%">开始时间</th>
                    <th width="10%">结束时间</th>
                    <th width="5%">个数</th>
                    <th width="6%">生成状态</th>
                    <th width="6%">制卡状态</th>
                    <th width="6%">出售状态</th>
                    <th width="50%">管理操作</th>
                    <th width="10%"></th>
                </tr>
                <foreach from="lists" item="v">
                    <tr>
                    <td><if><input name="ids[]" value="<{$v.id}>" type="checkbox" /></if></td>
                    <td><{$v.id}></td>
                    <td><{$v.batch_id}></td>
                    <td><{$v.title}></td>
                    <td><{$v.start_time|date='Y-m-d',###}></td>
                    <td><{$v.end_time|date='Y-m-d',###}></td>
                    <td><{$v.num}></td>
                    <td><{$v.mstatus_tips}></td>
                    <td><{$v.cstatus_tips}></td>
                    <td><{$v.sstatus_tips}></td>
                    <td>
                        <a href="<{:url('batchIndex?bid='.$v['id'])}>">查看批次</a>&nbsp;&nbsp;
                        <a href="<{:url('Edit?id='.$v['id'])}>">编辑</a>&nbsp;&nbsp;
                    </td>
                    <td></td>
                    </tr>
                </foreach>
            </table>
            <div class="btn">
                <label for="check_box">全选/取消</label>
                <input type="hidden" class="button" name="cstatus"  id="status"  value="" />
                <input type="hidden" class="button" name="sstatus"  id="status"  value="" />
                <input type="button" class="button" name="btn_close" id="btn_close" onclick="setStatus('cstatus',1)" value="已制卡" />
                <input type="button" class="button" name="btn_open" id="btn_open" onclick="setStatus('sstatus',1)"  value="已出售" />
            </div>
        </form>
    </div>
    <div class="pages"><{$pages}></div>
</div>

<include file="admin@footer" />
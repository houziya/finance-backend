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

    $(function(){
        //批量删除
        $("#btn_del").click(function(){
            var ids = get_ids();
            if(ids==''){
                alert('请选择待删除的会员');
                return false;
            }
            if(!confirm('将会删除对应的会员信息！\n\n请确认是否删除？')){
                return false;
            }
            $("input[name='do']").val('dosubmit');
            $('#myform').attr('action','<{:url("delete?do=all")}>');
            $('#myform').submit();
        });

    });

    function unbindcard(uid)
    {
        if(uid)
        {
            $.ajax({
                url:'<?php echo url('tender/unbindcard'); ?>',
                data:{    
                    uid : uid
                },
                async: false,
                type:'post',
                success:function(data) {
                    var sdata = eval('(' + data + ')');
                    alert(sdata.msg);
                }
            });
        }
        else
            return 
    }
</script>
<div class="pad-10">
    <div class="content-menu line-x blue"><{$topnav}></div>
    <div class="explain-col mar-b8" id="search_form">
        <form name="search_form" action="" method="post" id="search_form">
            &nbsp;	用户uid：
            <input name="search[id]" type="text" class="input-text" value="<{$id}>" size="8" />
            &nbsp;	用户名：
            <input name="search[name]" type="text" class="input-text" value="<{$name}>" size="8" />


            <input type="submit" name="dosubmit" class="button" value="搜索" />
        </form>
    </div>
    <div class="table-list">
        <form name="myform" action="#" method="post" id="myform">
            <table width="100%" cellspacing="0">
                <tr>
                    <th width="3%"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
                    <th width="3%">ID</th>
                    <th width="10%">用户名</th>
                    <th width="15%">公司</th>
                    <th width="5%">限流</th>
                    <th width="12%">添加时间</th>
                    <th width="8%">管理操作</th>
                    <th width="40%"></th>
                </tr>
                <foreach from="lists" item="v">
                   <tr>
                    <td><if condition="$v.is_admin neq 1"><input name="ids[]" value="<{$v.id}>" type="checkbox" /></if></td>	
                    <td><{$v.id}></td>
                    <td><{$v.username}></td>
                    <td><{$v.company}></td>
                    <td><{$v.astrict_num}></td>
                    <td><{$v.regist_time|date='Y-m-d H:i:s',###}></td>
                    <td><a href="<{:url('edit?id='.$v['id'])}>" title="修改">修改</a>  </td>
                    <td></td>
                    </tr>
                </foreach>
            </table>
            <div class="btn">
                <label for="check_box" style="cursor:pointer;">全选/取消</label>
                <!--<input name="do" type="hidden" value="" />
                <input type="button" class="button" name="btn_del" id="btn_del" value="批量删除" />
            </div>
        </form>
    </div>
    <div class="pages"><{$pages}></div>
</div>

<include file="admin@footer" />
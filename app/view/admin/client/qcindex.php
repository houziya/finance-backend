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
        $('#myform').attr('action','<{:url("setQueryConfigStatus")}>');
        $('#myform').submit();
    };
    $(function(){
       $(".content-menu>a").click(function(){
                var text = $(this).text();
                if(text == '添加配置'){
                  var url = "<{:url('QueryConfigAdd?cid='.$cid)}>";
                  window.location.href = url;
                }
	});	
});
</script>
<div class="pad-10">
    <div class="content-menu line-x blue"><{$topnav}></div>
    <div class="table-list">
        <form name="myform" action="#" method="post" id="myform">
            <table width="100%" cellspacing="0">
                <tr>
                    <th width="3%"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
                    <th width="4%">CID</th>
                    <th width="5%">库类型</th>
                    <th width="10%">库名称</th>
                    <th width="10%">用户名</th>
                    <th width="8%">密码</th>
                    <th width="14%">数据库地址</th>
                    <th width="6%">查询类型</th>
                    <th width="6%">查询频率</th>
                    <th width="15%">时间</th>
                    <th width="5%">状态</th>
                    <th width="15%">操作</th>
                </tr>
                <foreach from="lists" item="v">
                    <tr>
                    <td><if><input name="ids[]" value="<{$v.id}>" type="checkbox" /></if></td>
                    <td><{$v.cid}></td>
                    <td><if condition="$v.db_type eq '1'">mysql<elseif condition="$v.db_type eq '2'"/>sqlserver<elseif condition="$v.db_type eq '3'"/>oracle<elseif condition="$v.db_type eq '4'"/>interbase</if></td>
                    <td><{$v.db_name}></td>
                    <td><{$v.db_username}></td>
                    <td><{$v.db_pwd}></td>
                    <td><{$v.db_address}></td>
                    <td><if condition="$v.qu_type eq '1'">分钟<elseif condition="$v.qu_type eq '2'"/>小时<elseif condition="$v.qu_type eq '3'"/>天<elseif condition="$v.qu_type eq '4'"/>周<elseif condition="$v.qu_type eq '5'"/>月</if></td>
                    <td><if condition="$v.qu_type eq '1'"><{$v.qu_frequency}><elseif condition="$v.qu_type eq '2'"/><{$v.qu_frequency}><elseif condition="$v.qu_type eq '3'"/><{$v.qu_frequency}><elseif condition="$v.qu_type eq '4'"/><{$v.qu_frequency}><elseif condition="$v.qu_type eq '5'"/><{$v.qu_frequency}></if></td>
                    <td><if condition="$v.update_time neq '0'"><{$v.update_time|date='Y-m-d H:i:s',###}><else /><{$v.add_time|date='Y-m-d H:i:s',###}></if></td>
                    <td><if condition="$v.status eq '1'">启用<else />禁用</if></td>
                    <td>
                      <a href="<{:url('queryConfigEdit?id='.$v['id'])}>">编辑</a>
                    </tr>
                </foreach>
            </table>
            <div class="btn">
                <label for="check_box">全选/取消</label>
               <input type="hidden" class="button" name="status"  id="status"  value="" />
                <input type="button" class="button" name="btn_close" id="btn_close" onclick="setStatus(0)" value="禁用" />
                <input type="button" class="button" name="btn_open" id="btn_open" onclick="setStatus(1)"  value="开启" />
            </div>
        </form>
    </div>
    <div class="pages"><{$pages}></div>
</div>
<include file="admin@footer" />
<script type="text/javascript">
    $(".content-menu").prepend("<a class='on' href='<{:url('Index?menu_id=35')}>'><em>软件客户端</em></a> <span>|</span>");
</script>
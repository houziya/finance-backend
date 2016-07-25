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
function setStatus(v,ids){
    if(ids == 0){
      var ids = get_ids();  
       if(ids==''){
        alert('请选择待设置的id');
        return false;
       }
    }else{
        $("[id = 'check_"+ids+"']:checkbox").attr("checked", true);
    }
    
    $("input[name='status']").val(v);
    $('#myform').attr('action','<{:url("setQueryConfigStatus")}>');
    $('#myform').submit();
};

$(function(){
       $(".content-menu>a").click(function(){
                var text = $(this).text();
                if(text == '添加配置'){
                  var url = "<{:url('QueryConfigAdd?vid='.$row['id'].'&sort_id='.$row[sort_id])}>";
                  window.location.href = url;
                }
	});
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
		
		$('#myform').attr('action','<{:url("edit")}>');
		$('#myform').submit();
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
                                <th width="10%">查询ID</th>
				<th width="5%">版本ID</th>
				<th width="6%">版本名称</th>
				<th width="6%">查询类型</th>
				<th width="6%">查询频率</th>
				<th width="8%">数据库类型</th>
                                <th width="9%">数据库名称</th>
                                <th width="9%">数据库地址</th>
                                <th width="8%">用户名称</th>
                                <th width="6%">用户密码</th>
				<th width="5%">状态</th>
				<th width="15%">管理操作</th>
			</tr>
			<foreach from="lists" item="v">
			<tr>
				<td><if><input name="ids[]" id="check_<{$v.id}>" value="<{$v.id}>" type="checkbox" /></if></td>
                                <td><{$v['id']}></td>
                                <td><{$v.vid}></td>
                                <td><{$row['version']}></td>
				<td><if condition="$v.qu_type eq '1'">分钟<elseif condition="$v.qu_type eq '2'"/>小时<elseif condition="$v.qu_type eq '3'"/>天<elseif condition="$v.qu_type eq '4'"/>周<elseif condition="$v.qu_type eq '5'"/>月</if></td>
				<td><if condition="$v.qu_type eq '1'"><{$v.qu_frequency}><elseif condition="$v.qu_type eq '2'"/><{$v.qu_frequency}><elseif condition="$v.qu_type eq '3'"/><{$v.qu_frequency}><elseif condition="$v.qu_type eq '4'"/><{$v.qu_frequency}><elseif condition="$v.qu_type eq '5'"/><{$v.qu_frequency}></if></td>
				<td><if condition="$v.db_type eq '1'">mysql<elseif condition="$v.db_type eq '2'"/>sqlserver<elseif condition="$v.db_type eq '3'"/>oracle<elseif condition="$v.db_type eq '4'"/>interbase</if></td>
                                <td><{$v.db_name}></td>
                                <td><{$v.db_address}></td>
                                <td><{$v.db_username}></td>
                                <td><{$v.db_pwd}></td>
				<td><{$v.status_tips}></if>
                                    </td>
				<td>
				    <a href="<{:url('QueryConfigEdit?id='.$v['id'].'&sort_id='.$row[sort_id])}>">修改</a>&nbsp;&nbsp;
                                    <a onclick="setStatus(2,'<{$v.id}>')" style="cursor:pointer;">设为默认</a>&nbsp;&nbsp;
				</td>
			</tr>
			</foreach>
		</table>
		<div class="btn">
                <label for="check_box">全选/取消</label>
                <input type="hidden" class="button" name="status"  id="status"  value="" />
                <input type="hidden" class="button" name="vid"  id="vid"  value="<{$row['id']}>" />
                <input type="hidden" class="button" name="sort_id"  id="sort_id"  value="<{$row['sort_id']}>" />
                <input type="button" class="button" name="btn_close" id="btn_close" onclick="setStatus(0,0)" value="禁用" />
                <input type="button" class="button" name="btn_open" id="btn_open" onclick="setStatus(1,0)"  value="启用" />
                </div>
		</form>
	</div>
	<div class="pages"><{$pages}></div>
</div>

<include file="admin@footer" />
<script type="text/javascript">
    $(".content-menu").prepend("<a class='on' href='<{:url('Index?id='.$row['sort_id'].'&menu_id=28')}>'><em>软件列表</em></a> <span>|</span><a class='on' href='<{:url('versionIndex?id='.$row['sort_id'].'&menu_id=30')}>'><em>版本列表</em></a> <span>|</span>");
</script>
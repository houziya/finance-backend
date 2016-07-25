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
	//批量排序
	$("#btn_sort").click(function(){
		$("input[name='do']").val('sort');
		$('#myform').attr('action','<{:url("usergroupedit")}>');
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
				<th width="6%">排序</th>
				<th width="5%">ID</th>
				<th>会员组名称</th>
				<th>时间/IP</th>
				<th>管理操作</th>
			</tr>
			<foreach from="lists" item="v">
			<tr>
				<td><input type="text" name="sort[<{$v.id}>]" class="input-text text-c" size="2" value="<{$v.sort}>" /> </td>
				<td><{$v.id}></td>
				<td><{$v.name}></td>
				<td><{$v.add_time}> | <{$v.ip}></td>
				<td>
					<a href="<{:url('usergroupedit?id='.$v['id'])}>">修改</a> 
					|
					<a href="<{:url('usergroupdelete?id='.$v['id'])}>" class="confirm">删除</a>
					
					<!--<if condition="$v.is_show eq 1">
							|
						<a href="<{:url('ishow?type=unshow&id='.$v['id'])}>" style="color:red;" title="隐藏会员组" alt="隐藏会员组">隐藏</a>
					<else />
							|
						<a href="<{:url('ishow?type=show&id='.$v['id'])}>" style="color:green;" title="隐藏会员组" alt="隐藏会员组">显示</a>
					</if>
				
				--></td>
			</tr>
			</foreach>
		</table>
		<div class="btn">
			<input name="do" type="hidden" id="do" value="sort" />
			<input type="submit" class="button" name="dosubmit" id="btn_sort" value="排序" />
		</div>
		</form>
	</div>
	<div class="pages"><{$pages}></div>
</div>

<include file="admin@footer" />
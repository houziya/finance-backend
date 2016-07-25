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
			alert('请选择待删除的用户');
			return false;
		}
		if(!confirm('将会删除对应的前后台用户，删除后不可恢复！\n\n请确认是否删除？')){
			return false;
		}
		$("input[name='do']").val('dosubmit');
		$('#myform').attr('action','<{:url("delete")}>');
		$('#myform').submit();
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
		$("input[name='do']").val('lock');
		$('#myform').attr('action','<{:url("edit")}>');
		$('#myform').submit();
	});

});
</script>
<div class="pad-10">
	<div class="table-list">
		<form name="myform" action="#" method="post" id="myform">
		<table width="100%" cellspacing="0">
			<tr>
				<th width="50%">ios扫描下面二维码</th>
				<th>Android扫描下面2个二维码，2个都要安装</th>
			</tr>
			<tr>
				<td style="padding:30px 0;"><img src="<{:url('adminuser/publicqrcode?key='.$key1)}>" width="240px" height="240px" /></td>
				<td style="padding:30px 0;"><img src="<{:url('adminuser/publicqrcode?key='.$key2)}>" width="240px" height="240px" /><br /><br /><img src="<{:url('adminuser/publicqrcode?key='.$key3)}>" width="240px" height="240px" /></td>
			</tr>
		</table>
		</form>
	</div>
</div>

<include file="admin@footer" />
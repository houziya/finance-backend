<include file="admin@header" />
<script type="text/javascript">
function checknode(obj){
	var chk = $("input[type='checkbox']");
	var count = chk.length;
	var num = chk.index(obj);
	var level_top = level_bottom =  chk.eq(num).attr('level');
	for (var i=num; i>=0; i--){
		var le = chk.eq(i).attr('level');
		if(eval(le) < eval(level_top)) {
			chk.eq(i).attr("checked",'checked');
			var level_top = level_top-1;
		}
	}
	for (var j=num+1; j<count; j++){
		var le = chk.eq(j).attr('level');
		if(chk.eq(num).attr("checked")=='checked') {
			if(eval(le) > eval(level_bottom)) chk.eq(j).attr("checked",'checked');
			else if(eval(le) == eval(level_bottom)) break;
		} else {
			if(eval(le) > eval(level_bottom)) chk.eq(j).removeAttr("checked");
			else if(eval(le) == eval(level_bottom)) break;
		}
	}
}
</script>
<div class="pad-10">
	<div class="table-list">
		<form action="" method="post" id="form1">
		<table width="100%" cellspacing="0">
			<tr>
				<th><strong>请选择角色权限</strong><div class="fl"><label><input type="checkbox" value="" id="check_box" onclick="selectall('menu_ids[]');">&nbsp;全选/取消</label></div></th>
			</tr>
			<{$menuSelect}>
		</table>
		<div class="btn">
			<input name="do" type="hidden" id="do" value="submit" /><input name="id" type="hidden" id="id" value="<{$info.id}>" />
			<input type="submit" class="button" name="dosubmit" id="dosubmit" value="确认" />
		</div>
		</form>
	</div>
</div>

<include file="admin@footer" />
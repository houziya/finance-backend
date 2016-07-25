<include file="admin@header" />
<script type="text/javascript">
$(document).ready(function(){
	$.formValidator.initConfig({submitonce:true,formid:"form1"});
	$("#name").formValidator({onshow:"请输入角色名称",onfocus:"请输入角色名称",oncorrect:"正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"请确认您的输入是否正确"});
});
</script>
<div class="pad-10">
	<div class="content-menu line-x blue"><{$topnav}></div>
	<div class="table-form">
		<form action="" method="post" id="form1">
		<table width="100%">
			<tr>
				<th width="120">角色名称</th>
				<td><input name="data[name]" type="text" class="input-text" id="name" value="<{$info.name}>" /><div id="nameTip"></div></td>
			</tr>
			<tr>
				<th>排序</th>
				<td><input name="data[sort]" type="text" class="input-text" id="sort" value="<{$info.sort}>" /></td>
			</tr>
			<tr>
				<th>描述</th>
				<td><textarea rows="10" name="data[description]" style="width:80%;height:80px;"><{$info.description}></textarea></td>
			</tr>
			<tr>
				<th>状态</th>
				<td><input name="data[status]" type="radio" value="1" <if condition="$info['status'] eq 1"> checked="checked"</if> /> 
					正常 
						<input name="data[status]" type="radio" value="0" <if condition="$info['status'] eq 0"> checked="checked"</if> /> 
						待审</td>
			</tr>
		</table>
		<div class="btn">
			<input name="do" type="hidden" value="dosubmit" /><input name="data[id]" type="hidden" value="<{$info.id}>" />
			<input type="submit" class="button" name="dosubmit" value="确定" />&nbsp;&nbsp;<input type="button" class="button" name="goback" value="返回上页" onclick="javascript:window.history.back(-1);" />
		</div>
		</form>
	</div>
</div>

<include file="admin@footer" />
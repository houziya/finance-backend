<include file="admin@header" />

<script type="text/javascript">
$(function(){
	$.formValidator.initConfig({formid:"form1", autotip:true, submitonce:true});
	$("#name").formValidator({onshow:"请输入分类名称",onfocus:"请输入分类名称",oncorrect:"输入正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"分类名称不能为空"});
})
</script>
<div class="pad-10">
	<div class="content-menu line-x blue"><{$topnav}></div>
	<div class="table-form">
		<form action="" method="post" id="form1">
		<table width="100%">
			<tr>
				<th width="120">名称</th>
				<td><input name="data[name]" type="text" class="input-text" id="name" value="<{$info.name}>" /></td>
			</tr>
		</table>
		<div class="btn">
			<input name="id" type="hidden" value="<{$info.id}>" />
			<input name="do" type="hidden" value="dosubmit" />
			<input type="submit" class="button" name="dosubmit" value="确定" />&nbsp;&nbsp;<input type="button" class="button" name="goback" value="返回上页" onclick="javascript:window.history.back(-1);" />
		</div>
		</form>
	</div>
</div>

<include file="admin@footer" />
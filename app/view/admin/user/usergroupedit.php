<include file="admin@header" />
<script type="text/javascript">
$(document).ready(function(){
	$.formValidator.initConfig({formid:"form1", autotip:true});
	$("#name").formValidator({onshow:"请输入用户组名称",onfocus:"请输入用户组名称",oncorrect:"正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"请确认您的输入是否正确"});
});
</script>
<div class="pad-10">
	<div class="content-menu line-x blue"><{$topnav}></div>
	<div class="table-form">
		<form action="" method="post" id="form1">
		<table width="100%">
			<tr>
				<th>用户组名称</th>
				<td width="80%"><input name="data[name]" type="text" class="input-text" id="name" value="<{$group_info.name}>"/></td>
			</tr>
			
			<!--<tr>
				<th>是否显示</th>
				<td width="80%">
					<input name="data[is_delete]" type="radio" value="1" <{:radio($group_info['is_delete'],1)}>/> 
					显示 &nbsp;&nbsp;
					<input name="data[is_delete]" type="radio" value="0" <{:radio($group_info['is_delete'],0)}>/>
					隐藏
				</td>
			</tr>-->
		</table>
		<div class="btn">
			<input name="data[id]" type="hidden" value="<{$group_info.id}>" />
			<input name="do" type="hidden" value="dosubmit" />
			<input type="submit" class="button" name="dosubmit" value="确定" />&nbsp;&nbsp;<input type="button" class="button" name="goback" value="返回上页" onclick="javascript:window.history.back(-1);" />
		</div>
		</form>
	</div>
</div>

<include file="admin@footer" />
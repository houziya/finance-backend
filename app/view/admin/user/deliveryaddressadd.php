<include file="admin@header" />
<script type="text/javascript">
$(document).ready(function(){
	$.formValidator.initConfig({formid:"form1", autotip:true});
	//$("#username").formValidator({onshow:"请输入用户名",onfocus:"请输入用户名",oncorrect:"正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"请确认您的输入是否正确"});
	$("#mobile").formValidator({onshow:"请输入手机号",onfocus:"请输入手机号",oncorrect:"输入正确"}).regexValidator({regexp:"mobile",datatype:"enum",onerror:"手机号错误"});
//	$("#tel").formValidator({onshow:"请输入电话",onfocus:"请输入电话",oncorrect:"正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"请确认您的输入是否正确"});
	$("#address").formValidator({onshow:"请输入用户详细地址",onfocus:"请输入用户详细地址",oncorrect:"正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"请确认您的输入是否正确"});
	//$("#zip").formValidator({onshow:"请输入邮政编码",onfocus:"请输入邮政编码",oncorrect:"正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"请确认您的输入是否正确"});
});
</script>
<div class="pad-10">
	<div class="content-menu line-x blue"><{$topnav}></div>
	<div class="table-form">
		<form action="" method="post" id="form1">
		<table width="100%">
			<tr>
				<th width="120">用户ID</th>
				<td><{$info.uid}><input name="data[uid]" type="hidden" class="input-text" id="uid" value="<{$info.uid}>"/></td>
			</tr>
			<tr>
				<th>用户名</th>
				<td><{$info.username}><input name="data[username]" type="hidden" class="input-text" id="username" value="<{$info.username}>"/></td>
			</tr>
			
			<tr>
				<th>手机号</th>
				<td><input name="data[mobile]" type="text" class="input-text" id="mobile" /></td>
			</tr>
			
			<tr>
				<th>电话</th>
				<td><input name="data[tel]" type="text" class="input-text" id="tel" /></td>
			</tr>
			
			<tr>
				<th>收货地址</th>
				<td>
					<input name="data[address]" type="text" class="input-text" id="address" />
				</td>
			</tr>
			
			<tr>
				<th>邮政编码</th>
				<td><input name="data[zip]" type="text" class="input-text" id="zip" /></td>
			</tr>
		</table>
		<div class="btn">
			<input type="hidden" name="uid" value="<{$info.uid}>" />
			<input name="do" type="hidden" value="dosubmit" />
			<input type="submit" class="button" name="dosubmit" value="确定" />&nbsp;&nbsp;<input type="button" class="button" name="goback" value="返回上页" onclick="javascript:window.history.back(-1);" />
		</div>
		</form>
	</div>
</div>

<include file="admin@footer" />
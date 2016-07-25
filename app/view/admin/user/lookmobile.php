<include file="admin@header" />
<script type="text/javascript">
$(function(){
	$.formValidator.initConfig({formid:"form1", autotip:true});
	$("#username").formValidator({onshow:"请输入用户名",onfocus:"请输入用户名",oncorrect:"正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"请确认您的输入是否正确"});
	$("#password").formValidator({empty:true,onshow:"如需修改密码，请填写新的密码",onfocus:"密码应该为6-20位之间",oncorrect:"输入正确",onempty:"密码为空，将不会修改原密码"}).inputValidator({min:6,max:20,onerror:"密码应该为6-20位之间"});
	$("#repass").formValidator({onshow:"请输入确认密码",onfocus:"请输入确认密码",oncorrect:"输入正确"}).compareValidator({desid:"password",operateor:"=",onerror:"两次密码输入不一致"});
	$("#realname").formValidator({empty:true,onshow:"请输入真实姓名",onfocus:"请输入真实姓名",oncorrect:"输入正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"真实姓名不能为空"}).defaultPassed();
	$("#mobile").formValidator({empty:true,onshow:"请输入手机号",onfocus:"请输入手机号",oncorrect:"输入正确"}).regexValidator({regexp:"mobile",datatype:"enum",onerror:"手机号错误"}).defaultPassed();
	$("#email").formValidator({empty:true,onshow:"请输入邮箱",onfocus:"请输入邮箱",oncorrect:"输入正确"}).regexValidator({regexp:"email",datatype:"enum",onerror:"邮箱格式错误"}).defaultPassed();
});
</script>
<div class="pad-10">
	<div class="table-form">
		<form action="" method="post" id="form1">
		<table width="100%">
			<tr>
				<th width="120">用户uid</th>
				<td><{$info.uid}></td>
			</tr>
			<tr>
				<th>用户名</th>
				<td><{$info.username}></td>
			</tr>
			<tr>
				<th>手机号</th>
				<td class="red"><{$info.mobile}></td>
			</tr>
		</table>
		</form>
	</div>
</div>

<include file="admin@footer" />
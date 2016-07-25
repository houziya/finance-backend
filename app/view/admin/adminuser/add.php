<include file="admin@header" />
<script type="text/javascript">
$(function(){
	//$("#mobile").formValidator({onshow:"请输入手机号",onfocus:"请输入手机号",oncorrect:"输入正确"}).regexValidator({regexp:"mobile",datatype:"enum",onerror:"手机号错误"});
	$("#password").formValidator({onshow:"请输入密码",onfocus:"密码应该为6-20位之间",oncorrect:"输入正确"}).inputValidator({min:6,max:20,onerror:"密码应该为6-20位之间"});
	$("#password2").formValidator({onshow:"请输入确认密码",onfocus:"请输入确认密码",oncorrect:"输入正确"}).compareValidator({desid:"password",operateor:"=",onerror:"两次密码输入不一致"});
	$("#realname").formValidator({onshow:"请输入真实姓名",onfocus:"请输入真实姓名",oncorrect:"输入正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"真实姓名不能为空"});
})
</script>
<div class="pad-10">
	<div class="content-menu line-x blue"><{$topnav}></div>
	<div class="table-form">
		<form action="" method="post" id="form1">
		<table width="100%">
			<tr>
				<th width="120">用户名</th>
				<td><input name="data[username]" type="text" class="input-text" id="username" /></td>
			</tr>			
			<tr>
				<th>所属角色</th>
				<td><{$roleSelect}></td>
			</tr>
			<tr>
				<th>密码</th>
				<td><input name="data[password]" type="password" class="input-text" id="password" /></td>
			</tr>
			<tr>
				<th>确认密码</th>
				<td><input name="password2" type="password" class="input-text" id="password2" /></td>
			</tr>
			<tr>
				<th>真实姓名</th>
				<td><input name="data[realname]" type="text" class="input-text" id="realname" /></td>
			</tr>
			<tr>
				<th width="120">手机</th>
				<td><input name="data[mobile]" type="text" class="input-text" id="mobile" /></td>
			</tr>
			<tr>
				<th>备注</th>
				<td><textarea rows="10" cols="90" name="data[remark]" id="remark" style="width:80%;height:80px;"></textarea></td>
			</tr>
			<tr>
				<th>下次登录改密</th>
				<td><input name="data[reset_pwd]" type="radio" value="1" /> 
					是
					<input name="data[reset_pwd]" type="radio" value="0" checked="checked" /> 
					否&nbsp;&nbsp;<span class="gray4">下次登录后台要求强制修改登录密码</span></td>
			</tr>
			<tr>
				<th>状态</th>
				<td><input name="data[status]" type="radio" value="1" checked="checked" /> 
					正常 
					<input name="data[status]" type="radio" value="0" /> 
					待审
					<input name="data[status]" type="radio" value="2" /> 
					锁定
				</td>
			</tr>
		</table>
		<div class="btn">
			<input name="do" type="hidden" value="dosubmit" />
			<input type="submit" class="button" name="dosubmit" value="确定" id="dosubmit" />&nbsp;&nbsp;<input type="button" class="button" name="goback" value="返回上页" onclick="javascript:window.history.back(-1);" />
		</div>
		</form>
	</div>
</div>

<include file="admin@footer" />
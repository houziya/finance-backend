<include file="admin@header" />
<script type="text/javascript">
$(function(){
	$.formValidator.initConfig({formid:"form1", autotip:true, submitonce:true});
	$("#password").formValidator({empty:true,onshow:"如需修改密码，请填写新的密码",onfocus:"密码应该为6-20位之间",oncorrect:"输入正确",onempty:"密码为空，将不会修改原密码"}).inputValidator({min:6,max:20,onerror:"密码应该为6-20位之间"});
	$("#password2").formValidator({onshow:"请输入确认密码",onfocus:"请输入确认密码",oncorrect:"输入正确"}).compareValidator({desid:"password",operateor:"=",onerror:"两次密码输入不一致"});
	$("#realname").formValidator({onshow:"请输入真实姓名",onfocus:"请输入真实姓名",oncorrect:"输入正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"真实姓名不能为空"});
	//$("#mobile").formValidator({onshow:"请输入手机号",onfocus:"请输入手机号",oncorrect:"输入正确"}).regexValidator({regexp:"mobile",datatype:"enum",onerror:"手机号错误"});
})
</script>
<div class="pad-10">
	<div class="content-menu line-x blue"><{$topnav}></div>
	<div class="table-form">
		<form action="" method="post" id="form1">
		<table width="100%">
			<tr>
				<th width="120">用户名</th>
				<td><{$info.username}></td>
			</tr>			
			<tr>
				<th>所属角色</th>
				<td><{$roleSelect}></td>
			</tr>
			<tr>
				<th>密码</th>
				<td><input name="password" type="password" class="input-text" id="password" /></td>
			</tr>
			<tr>
				<th>确认密码</th>
				<td><input name="password2" type="password" class="input-text" id="password2" /></td>
			</tr>
			<tr>
				<th>真实姓名</th>
				<td><input name="data[realname]" type="text" class="input-text" id="realname" value="<{$info.realname}>" /></td>
			</tr>
			<tr>
				<th>手机</th>
				<td><input name="data[mobile]" type="text" class="input-text" id="mobile" value="<{$info.mobile}>" /></td>
			</tr>
			<tr>
				<th>备注</th>
				<td><textarea rows="10" cols="90" name="data[remark]" id="remark" style="width:80%;height:80px;"><{$info.remark}></textarea></td>
			</tr>
			<tr>
				<th>下次登录改密</th>
				<td><input name="data[reset_pwd]" type="radio" value="1"<if condition="$info.reset_pwd eq 1"> checked="checked"</if> /> 
					是
					<input name="data[reset_pwd]" type="radio" value="0"<if condition="$info.reset_pwd eq 0"> checked="checked"</if> /> 
					否&nbsp;&nbsp;<span class="gray4">下次登录后台要求强制修改登录密码</span></td>
			</tr>
			<tr>
				<th>开启动态密码</th>
				<td><input name="data[is_token]" type="radio" value="1"<if condition="$info.is_token eq 1"> checked="checked"</if> /> 
					是
					<input name="data[is_token]" type="radio" value="0"<if condition="$info.is_token eq 0"> checked="checked"</if> /> 
					否&nbsp;&nbsp;</td>
			</tr>
			<tr>
				<th>动态密钥</th>
				<td><input name="data[token]" type="text" class="input-text" id="token" size="40" value="<{$info.token}>" /></td>
			</tr>
			<tr>
				<th>状态</th>
				<td><input name="data[status]" type="radio" value="1" <if condition="$info.status eq 1"> checked="checked"</if> /> 
					正常&nbsp;
					<input name="data[status]" type="radio" value="0" <if condition="$info.status eq 0"> checked="checked"</if> /> 
					待审&nbsp;
					<input name="data[status]" type="radio" value="2" <if condition="$info.status eq 2"> checked="checked"</if> /> 
					锁定
					</td>
			</tr>
		</table>
		<div class="btn">
			<input name="data[uid]" type="hidden" value="<{$info.uid}>" />
			<input name="do" type="hidden" value="dosubmit" />
			<input type="submit" class="button" name="dosubmit" value="确定" id="dosubmit" />&nbsp;&nbsp;<input type="button" class="button" name="goback" value="返回上页" onclick="javascript:window.history.back(-1);" />
		</div>
		</form>
	</div>
</div>

<include file="admin@footer" />
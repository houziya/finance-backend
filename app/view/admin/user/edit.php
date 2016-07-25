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

<ul id="errorlist"></ul>
<div class="pad-10">
	<div class="content-menu line-x blue"><{$topnav}></div>
	<div class="table-form">
		<form action="" method="post" id="form1">
		<table width="100%">
			<!--<tr>
				<th width="120">用户组别</th>
				<td><{$userGroup}></td>
			</tr>-->
			<tr>
				<th>用户名</th>
				<td width="80%"><{$info.username}></td>
			</tr>
			<tr>
				<th>密码</th>
				<td width="80%"><input name="password" type="password" class="input-text" id="password" value="" /></td>
			</tr>
			<tr>
				<th>确认密码</th>
				<td width="80%"><input name="repass" type="password" class="input-text" id="repass" value="" /></td>
			</tr>
			<!--<tr>
				<th>邮箱</th>
				<td width="80%"><input name="data[email]" type="text" class="input-text" id="email"  value="<{$info.email}>" /></td>
			</tr>-->
			
			<tr>
				<th>手机号</th>
				<td width="80%"><input name="data[mobile]" type="text" class="input-text" id="mobile" value="<{$info.mobile}>" /></td>
			</tr>
                         <tr>
				<th>公司名</th>
				<td><input name="data[company]" type="text" class="input-text" id="company"  size="30"  value="<{$info.company}>"/></td>
			</tr>
                         <tr>
				<th>限流</th>
				<td><input name="data[astrict_num]" type="text" class="input-text" id="astrict_num"  value="<{$info.astrict_num}>" /></td>
			</tr>

			<!--<tr>
				<th>真实姓名</th>
				<td width="80%"><input name="data[realname]" type="text" class="input-text" id="realname" value="<{$info.realname}>"/></td>
			</tr>

			<tr>
				<th>性别</th>
				<td width="80%">
					<input name="data[sex]" type="radio" value="1" <{:radio($info['sex'],1)}>/> 
					男 &nbsp;&nbsp;
					<input name="data[sex]" type="radio" value="2" <{:radio($info['sex'],2)}>/>
					女
					<input name="data[sex]" type="radio" value="0" <{:radio($info['sex'],0)}>/> 
					保密 &nbsp;&nbsp;
				</td>
			</tr>

			<tr>
				<th>审核状态</th>
				<td width="80%">
					<input name="data[status]" type="radio" value="1" <{:radio($info['status'],1)}>/> 
					已审核 &nbsp;&nbsp;
					<input name="data[status]" type="radio" value="0" <{:radio($info['status'],0)}>/>
					未审核
				</td>
			</tr>-->

			<!-- <tr>
				<th>用户分组</th>
				<td>
					<select name="group_id" id="group_id">
						<option value="">请选择组别</option>
						<option value="0">组1</option>
						<option value="1">组2</option>
					</select>
				</td>
			</tr> -->
		</table>
		<div class="btn">
			<input name="data[uid]" type="hidden" value="<{$info.uid}>" />
			<input name="do" type="hidden" value="dosubmit" />
			<input type="submit" class="button" name="dosubmit" value="确定" />&nbsp;&nbsp;<input type="button" class="button" name="goback" value="返回上页" onclick="javascript:window.history.back(-1);" />
		</div>
		</form>
	</div>
</div>

<include file="admin@footer" />
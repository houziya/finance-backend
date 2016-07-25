<include file="admin@header" />
<script type="text/javascript">
$(function(){
	$.formValidator.initConfig({formid:"form1", autotip:true, submitonce:true});
	$("#username").formValidator({onshow:"请输入用户名",onfocus:"用户名是以英文、数字和下划线组成的5-30位字符",oncorrect:"输入正确"}).inputValidator({
		min:5,max:20,onerror:"用户名只能是以英文、数字和下划线组成的5-30位字符"
	}).ajaxValidator({
		type : "get",
		url : "<{:url('ajaxchusername')}>",
		async:'true',
		success : function(data){
            if( data == "0" ){
                return true;
			}else{
                return false;
			}
		},
		buttons: $("#dosubmit"),
		onerror : "用户已存在",
		onwait : "请稍候..."	
	});
	$("#password").formValidator({onshow:"请填写新的密码",onfocus:"密码应该为6-20位之间",oncorrect:"输入正确"}).inputValidator({min:6,max:20,onerror:"密码应该为6-20位之间"});
	$("#repass").formValidator({onshow:"请输入确认密码",onfocus:"请输入确认密码",oncorrect:"输入正确"}).compareValidator({desid:"password",operateor:"=",onerror:"两次密码输入不一致"});
	//$("#realname").formValidator({onshow:"请输入真实姓名",onfocus:"请输入真实姓名",oncorrect:"输入正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"真实姓名不能为空"});
	/*$("#mobile").formValidator({onshow:"请输入手机号",onfocus:"请输入手机号",oncorrect:"输入正确"}).regexValidator({regexp:"mobile",datatype:"enum",onerror:"手机号错误"}).ajaxValidator({
		type : "get",
		url : "<{:url('ajaxchmobile')}>",
		async:'true',
		success : function(data){
            if( data == "0" ){
                return true;
			}else{
                return false;
			}
		},
		buttons: $("#dosubmit"),
		onerror : "手机号已存在",
		onwait : "请稍候..."	
	});*/

	/*$("#email").formValidator({onshow:"请输入邮箱",onfocus:"请输入邮箱",oncorrect:"输入正确"}).regexValidator({regexp:"email",datatype:"enum",onerror:"邮箱格式错误"}).ajaxValidator({
		type : "get",
		url : "<{:url('ajaxchkemail')}>",
		async:'true',
		success : function(data){
            if( data == "0" ){
                return true;
			}else{
                return false;
			}
		},
		buttons: $("#dosubmit"),
		onerror : "邮箱已存在",
		onwait : "请稍候..."	
	});*/
})
</script>
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
				<th width="120">用户名</th>
				<td><input name="data[username]" type="text" class="input-text" id="username" /></td>
			</tr>
			<tr>
				<th>密码</th>
				<td><input name="data[password]" type="password" class="input-text" id="password" /></td>
			</tr>
			<tr>
				<th>重复密码</th>
				<td><input name="data[repass]" type="password" class="input-text" id="repass" /></td>
			</tr>
			<!--<tr>
				<th>邮箱</th>
				<td><input name="data[email]" type="text" class="input-text" id="email" size="30" /></td>
			</tr>-->
			<tr>
				<th>手机号</th>
				<td><input name="data[mobile]" type="text" class="input-text" id="mobile" /></td>
			</tr>
                      <tr>
				<th>公司名</th>
				<td><input name="data[company]" type="text" class="input-text" id="company"  size="30"/></td>
			</tr>
                         <tr>
				<th>限流</th>
				<td><input name="data[astrict_num]" type="text" class="input-text" id="astrict_num" /></td>
			</tr>
			<!--<tr>
				<th>真实姓名</th>
				<td><input name="data[realname]" type="text" class="input-text" id="realname" /></td>
			</tr>-->

			<!--<tr>
				<th>性别</th>
				<td>
					<input name="data[sex]" type="radio" value="1" checked="checked" /> 
					男 &nbsp;&nbsp;
					<input name="data[sex]" type="radio" value="2"  />
					女&nbsp;&nbsp;
					<input name="data[sex]" type="radio" value="0"  /> 
					保密 &nbsp;&nbsp;
				</td>
			</tr>
			<tr>
				<th>状态</th>
				<td><input name="data[status]" type="radio" value="0" />
				待审
				&nbsp;&nbsp;
					<input name="data[status]" type="radio" value="1" checked="checked"  />
					正常</td>
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
			<input name="do" type="hidden" value="dosubmit" />
			<input type="submit" class="button" name="dosubmit" value="确定" />&nbsp;&nbsp;<input type="button" class="button" name="goback" value="返回上页" onclick="javascript:window.history.back(-1);" />
		</div>
		</form>
	</div>
</div>

<include file="admin@footer" />
<include file="admin@header" />
<script type="text/javascript">
$(function(){
	$.formValidator.initConfig({formid:"form1", autotip:true, submitonce:true});
	$("#password").formValidator({empty:true,onshow:"不修改密码请留空",onfocus:"密码应该为6-20位之间",oncorrect:"输入正确",onempty:"密码为空，将不会修改原密码"}).inputValidator({min:6,max:20,onerror:"密码应该为6-20位之间"});
	$("#password2").formValidator({onshow:"不修改密码请留空",onfocus:"请输入确认密码",oncorrect:"输入正确"}).compareValidator({desid:"password",operateor:"=",onerror:"两次密码输入不一致"});
	$("#realname").formValidator({onshow:"请输入真实姓名",onfocus:"请输入真实姓名",oncorrect:"输入正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"真实姓名不能为空"});
	$("#mobile").formValidator({onshow:"请输入手机号",onfocus:"请输入手机号",oncorrect:"输入正确"}).regexValidator({regexp:"mobile",datatype:"enum",onerror:"手机号错误"});
	$("#tokenpwd").formValidator({onshow:"请输入手机上的动态密码",onfocus:"请输入手机上的动态密码",oncorrect:"输入正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"动态密码不能为空"});
})
</script>
<div class="pad-10">
<!--	<div class="content-menu line-x blue"><{$topnav}></div>-->
	<div class="table-form">
		<form action="" method="post" id="form1">
		<table width="100%">
			<tr>
				<th width="120">用户名</th>
				<td><{$info.username}>&nbsp;&nbsp;&nbsp;<if condition="$type eq 'resetpwd'"><span class="red">您只有修改了密码才能进行其他操作！请先修改密码！</span></if></td>
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
				<th>手机号</th>
				<td><input name="data[mobile]" type="text" class="input-text" id="mobile" value="<{$info.mobile}>" /></td>
			</tr>
			<tr>
				<th>真实姓名</th>
				<td><input name="data[realname]" type="text" class="input-text" id="realname" value="<{$info.realname}>" /></td>
			</tr>
			<if condition="$info['token'] eq ''">
			<tr>
				<th>二维码扫描</th>
				<td>打开谷歌动态密码软件扫描下面二维码<br /><a href="javascript:;" onclick="opendialog('<{:url('publicgoogleauthenticator')}>','谷歌动态密码');" style="color:#F00">如果手机上还没有安装谷歌动态密码软件，请点此下载安装</a></td>
			</tr>
			<tr>
				<th>&nbsp;</th>
				<td><img src="<{:url('adminuser/publicqrcode?key='.$key)}>" width="300px" height="300px" /></td>
			</tr>
			<tr>
				<th>动态密码<input name="data[token]" type="hidden" value="<{$token}>" /></th>
				<td><input name="tokenpwd" type="text" class="input-text" id="tokenpwd" value="" /></td>
			</tr>
			</if>
		</table>
		<div class="btn">			
			<input name="do" type="hidden" value="dosubmit" />
			<input type="submit" class="button" name="dosubmit" value="确定" id="dosubmit" />&nbsp;&nbsp;<input type="button" class="button" name="goback" value="返回上页" onclick="javascript:window.history.back(-1);" />
		</div>
		</form>
	</div>
</div>
<include file="admin@footer" />
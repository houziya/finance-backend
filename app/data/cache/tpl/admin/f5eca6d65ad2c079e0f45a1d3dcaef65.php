<?php if (!defined('FEE_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>人人投后台管理系统</title>
<script type="text/javascript" src="<?php echo ($url["admin_tpl"]); ?>/js/jquery-1.8.3.min.js"></script>
<style type="text/css">
<!--
*{
	padding:0px;
	margin:0px;
	font-family:Verdana, Arial, Helvetica, sans-serif;
}
body {
	margin: 0px;
	background:#F7F7F7;
	font-size:12px;
}
input{
	vertical-align:middle;
}
img{
	border:none;
	vertical-align:middle;
}
a{
	color:#333333;
}
a:hover{
	color:#FF3300;
	text-decoration:none;
}
.main{
	width:640px;
	margin:40px auto 0px;
	border:4px solid #EEE;
	background:#FFF;
	padding-bottom:10px;
}

.main .title{
	width:600px;
	height:50px;
	margin:0px auto;
	background:url(<?php echo ($url["admin_tpl"]); ?>/images/login_toptitle.jpg) -10px 0px no-repeat;
	text-indent:50px;
	line-height:46px;
	font-size:14px;
	letter-spacing:2px;
	color:#F60;
	font-weight:bold;
}

.main .login{
	width:560px;
	margin:20px auto 0px;
	overflow:hidden;
}
.main .login .inputbox{
	width:260px;
	float:left;
	background:url(<?php echo ($url["admin_tpl"]); ?>/images/login_input_hr.gif) right center no-repeat;
}
.main .login .inputbox dl{
	width:230px;
	height:41px;
	clear:both;
}
.main .login .inputbox dl dt{
	float:left;
	width:60px;
	height:31px;
	line-height:31px;
	text-align:right;
	font-weight:bold;
}
.main .login .inputbox dl dd{
	width:160px;
	float:right;
	padding-top:1px;
}
.main .login .inputbox dl dd input{
	font-size:12px;
	font-weight:bold;
	border:1px solid #888;
	padding:4px;
	background:url(<?php echo ($url["admin_tpl"]); ?>/images/login_input_bg.gif) left top no-repeat;
}


.main .login .butbox{
	float:left;
	width:200px;
	margin-left:26px;
}
.main .login .butbox dl{
	width:200px;
}
.main .login .butbox dl dt{
	width:160px;
	height:41px;
	padding-top:5px;
}
.main .login .butbox dl dt input{
	width:98px;
	height:33px;
	background:url(<?php echo ($url["admin_tpl"]); ?>/images/login_submit.gif) no-repeat;
	border:none;
	cursor:pointer;
}
.main .login .butbox dl dd{
	height:21px;
	line-height:21px;
}
.main .login .butbox dl dd a{
	margin:5px;
}



.main .msg{
	width:560px;
	margin:10px auto;
	clear:both;
	line-height:17px;
	padding:6px;
	border:1px solid #FC9;
	background:#FFFFCC;
	color:#666;
}

.copyright{
	width:640px;
	text-align:right;
	margin:10px auto;
	font-size:10px;
	color:#999999;
}
.copyright a{
	font-weight:bold;
	color:#F63;
	text-decoration:none;
}
.copyright a:hover{
	color:#000;
}
-->
</style>
<script language="javascript" type="text/javascript">
function SubCheck()
{
	if ($("#username").val()=="")
	{
		alert('用户名不能为空！');
		return false;
	}
	if ($("#password").val()=="")
	{
		alert('密码不能为空！');
		return false;
	}
	return true;
}

</script>
</head>
<body>


<div class="main">
		<div class="title">
			管理登陆
		</div>

		<div class="login">
			<form action="<?php echo url('login/index');?>" method="post" onSubmit="return SubCheck()">
			<input name="do" type="hidden" id="do" value="login">
            <div class="inputbox">
				<dl>
					<dt>用户名：</dt>
					<dd><input type="text" name="username" id="username" size="20" onfocus="this.style.borderColor='#F93'" onblur="this.style.borderColor='#888'" />
					</dd>
				</dl>
				<dl>
					<dt>密码：</dt>
					<dd><input type="password" name="password" size="20" onfocus="this.style.borderColor='#F93'" onblur="this.style.borderColor='#888'" id="password" />
					</dd>
				</dl>
				
				<?php if($is_open_captcha){ ?>
				<dl>
					<dt>验证码：</dt>
					<dd><input name="verify" type="text" id="verify" onfocus="this.style.borderColor='#F93'" onblur="this.style.borderColor='#888'" size="4" maxlength="4" /> <img id="fresh_valicode" src="<?php echo url('login/imgcode');?>?t=<?php echo mt_rand(100000,999999);?>" onclick="document.getElementById('fresh_valicode').src='<?php echo url('login/imgcode');?>?t=' + Math.random();" />
					</dd>
				</dl>
                <?php } ?>
            </div>
            <div class="butbox">
            <dl>
					<dt><input name="submit" type="submit" value="" /></dt>
			  </dl>
			</div>
		</form>
		</div>

		<div class="msg">
			<?php if($error_msg): ?><span style="font-weight:bold; color:#F00"><?php echo ($error_msg); ?></span> <?php else: ?>请输入用户名和密码登录<?php endif; ?>
		</div>
		
</div>

</body>
</html>
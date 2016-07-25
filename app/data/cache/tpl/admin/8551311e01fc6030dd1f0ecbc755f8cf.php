<?php if (!defined('FEE_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo ($appini["sysconfig"]["web_name"]); ?>后台管理中心</title>
<?php 
echo helper_view::addCss(array('admin/css/base.css','admin/css/style.css'),1,1);
echo helper_view::addJs(array('admin/js/jquery-1.8.3.min.js', 'admin/js/common.js','admin/js/formvalidator/formvalidator.js','admin/js/formvalidator/formvalidator_regex.js'),1,1);
echo helper_view::addJsCode('',1,1);
?>
<script type="text/javascript" src="<?php echo ($url["admin_tpl"]); ?>/js/dialog/dialog.js?_v=<?php echo ($appini["web_version"]); ?>"></script>
</head>
<body<?php echo empty($body_style) ? '' : $body_style; ?>>
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
<!--	<div class="content-menu line-x blue"><?php echo ($topnav); ?></div>-->
	<div class="table-form">
		<form action="" method="post" id="form1">
		<table width="100%">
			<tr>
				<th width="120">用户名</th>
				<td><?php echo ($info["username"]); ?>&nbsp;&nbsp;&nbsp;<?php if($type == 'resetpwd'): ?><span class="red">您只有修改了密码才能进行其他操作！请先修改密码！</span><?php endif; ?></td>
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
				<td><input name="data[mobile]" type="text" class="input-text" id="mobile" value="<?php echo ($info["mobile"]); ?>" /></td>
			</tr>
			<tr>
				<th>真实姓名</th>
				<td><input name="data[realname]" type="text" class="input-text" id="realname" value="<?php echo ($info["realname"]); ?>" /></td>
			</tr>
			<?php if($info['token'] == ''): ?><tr>
				<th>二维码扫描</th>
				<td>打开谷歌动态密码软件扫描下面二维码<br /><a href="javascript:;" onclick="opendialog('<?php echo url('publicgoogleauthenticator');?>','谷歌动态密码');" style="color:#F00">如果手机上还没有安装谷歌动态密码软件，请点此下载安装</a></td>
			</tr>
			<tr>
				<th>&nbsp;</th>
				<td><img src="<?php echo url('adminuser/publicqrcode?key='.$key);?>" width="300px" height="300px" /></td>
			</tr>
			<tr>
				<th>动态密码<input name="data[token]" type="hidden" value="<?php echo ($token); ?>" /></th>
				<td><input name="tokenpwd" type="text" class="input-text" id="tokenpwd" value="" /></td>
			</tr><?php endif; ?>
		</table>
		<div class="btn">			
			<input name="do" type="hidden" value="dosubmit" />
			<input type="submit" class="button" name="dosubmit" value="确定" id="dosubmit" />&nbsp;&nbsp;<input type="button" class="button" name="goback" value="返回上页" onclick="javascript:window.history.back(-1);" />
		</div>
		</form>
	</div>
</div>
</body>
</html>
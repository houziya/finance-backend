<?php if (!defined('FEE_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>系统提示</title>
<base target='_self'/>
<link href="<?php echo ($url["admin_tpl"]); ?>/css/public_tips.css?_v=<?php echo ($appini["web_version"]); ?>" rel="stylesheet" type="text/css" />
<?php if(!empty($dialog)): ?><script type="text/javascript" src="<?php echo ($url["admin_tpl"]); ?>/js/dialog/dialog.js?_v=<?php echo ($appini["web_version"]); ?>"></script>
<script type="text/javascript">
if(typeof(window.top.right) != 'undefined'){
	window.top.right.location.reload();
}else{
	window.top.location.reload();
}
setTimeout('window.top.art.dialog({ id:"<?php echo ($dialog); ?>"}).close();',1000);
</script>
<?php else: ?>
<script type="text/javascript">
var pgo=0;
function JumpUrl(){
	if(pgo==0){
		location='<?php echo ($jumpUrl); ?>';
		pgo=1;
	}	
}
setTimeout('JumpUrl()',1000 * <?php echo ($waitSecond); ?>);
</script><?php endif; ?>
</head>
<body>
<div class="wrap">
	<div class="content">
		<div class="tit">系统提示信息</div>
		<div class="wbox">
			<div class="w_l"><div class="success"></div></div>
			<div class="w_r">
				<?php if(isset($message)): ?><div class="msg"><?php echo ($message); ?></div><?php endif; ?>
				<?php if(isset($closeWin)): ?><div class="go">系统将在 <span class="b1"><?php echo ($waitSecond); ?></span> 秒后自动关闭，点击 <a href="<?php echo ($jumpUrl); ?>">这里</a> 直接关闭</div>
				<?php else: ?>
				<div class="go">系统将在 <span class="b1"><?php echo ($waitSecond); ?></span> 秒后自动返回，点击 <a href="<?php echo ($jumpUrl); ?>">这里</a> 快速返回</div><?php endif; ?>
			</div>
			<div class="clear"></div>
		</div>		
	</div>
</div>
</body>
</html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>系统提示</title>
<base target='_self'/>
<link href="<{$url.admin_tpl}>/css/public_tips.css?_v=<{$appini.web_version}>" rel="stylesheet" type="text/css" />
<if condition="!empty($dialog)">
<script type="text/javascript" src="<{$url.admin_tpl}>/js/dialog/dialog.js?_v=<{$appini.web_version}>"></script>
<script type="text/javascript">
if(typeof(window.top.right) != 'undefined'){
	window.top.right.location.reload();
}else{
	window.top.location.reload();
}
setTimeout('window.top.art.dialog({ id:"<{$dialog}>"}).close();',1000);
</script>
<else />
<script type="text/javascript">
var pgo=0;
function JumpUrl(){
	if(pgo==0){
		location='<{$jumpUrl}>';
		pgo=1;
	}	
}
setTimeout('JumpUrl()',1000 * <{$waitSecond}>);
</script>
</if>
</head>
<body>
<div class="wrap">
	<div class="content">
		<div class="tit">系统提示信息</div>
		<div class="wbox">
			<div class="w_l"><div class="success"></div></div>
			<div class="w_r">
				<present name="message">
				<div class="msg"><{$message}></div>
				</present>
				<present name="closeWin">
				<div class="go">系统将在 <span class="b1"><{$waitSecond}></span> 秒后自动关闭，点击 <a href="<{$jumpUrl}>">这里</a> 直接关闭</div>
				<else />
				<div class="go">系统将在 <span class="b1"><{$waitSecond}></span> 秒后自动返回，点击 <a href="<{$jumpUrl}>">这里</a> 快速返回</div>
				</present>
			</div>
			<div class="clear"></div>
		</div>		
	</div>
</div>
</body>
</html>
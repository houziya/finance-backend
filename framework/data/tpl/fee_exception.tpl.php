<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>系统发生错误</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
<style>
body{font-family: 'Microsoft Yahei', Verdana, arial, sans-serif;font-size:14px;}
.notice{padding:0 20px 20px 20px;margin:0 auto; width:780px;color:#666;background:#FCFCFC;border:1px solid #E0E0E0;}
a{text-decoration:none;color:#174B73;}
a:hover{ text-decoration:none;color:#FF6600;}
h2{border-bottom:1px solid #DDD;padding:8px 0;font-size:20px;margin:10px 0 10px 0;}
.title{margin:15px 0 0 0;color:#F60;font-weight:bold;}
.message{line-height:150%;padding:5px 10px;margin:10px 0 0 0;border:1px solid #E0E0E0;background:#F5F5F5;}
.error{line-height:150%;padding:15px;margin:10px 0 0 0;border:1px dotted #F90; border-left:6px solid #F60; background:#FFC};
.red{color:red;}
</style>
</head>
<body>
<div class="notice">
<h2>系统发生错误</h2>
<p class="title">错误信息</p>
<p class="error"><strong><?php echo $e['message'];?></strong></p>
<?php if(isset($e['file'])) {?>
<p class="title">错误位置</p>
<p class="message">FILE: <span class="red"><?php echo $e['file'] ;?></span>　LINE: <span class="red"><?php echo $e['line'];?></span></p>
<?php }?>
<?php if(isset($e['trace'])) {?>
<p class="title">TRACE</p>
<p class="message">
<?php echo nl2br($e['trace']);?>
</p>
<?php }?>
<div style="margin-top:10px">您可以选择 [ <A HREF="<?php echo($_SERVER['PHP_SELF'])?>">重试</A> ] [ <A HREF="javascript:history.back()">返回</A> ] 或者 [ <A HREF="<?php echo(_APP_);?>">回到首页</A> ]</div>
</div>
</body>
</html>

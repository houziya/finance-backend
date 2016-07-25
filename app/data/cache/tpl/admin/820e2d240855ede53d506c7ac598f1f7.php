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
<style type="text/css">
	.ul{
		border:#c7d8ea solid 1px;
		width:300px;
		float:left;
		list-style:none;
		margin-left:15px;
		padding:5px;
	}
	.ul li{
		height:28px;
		line-height:28px;
		
	}
</style>
<div class="pad-10">
	<div class="explain-col mar-b10" style="display:">暂无待处理信息!</div>
	<div class="col-2 fl mar-r10" style="width:48%">
		<h6>我的个人信息</h6>
		<div class="content"> 您好，<?php echo ($auth["username"]); ?><br />
			所属角色：<?php echo ($role_name); ?> <br />
			<div class="bk20 hr">
				<hr />
			</div>
			上次登录时间：<?php echo ($lastlogin["addtime_tips"]); ?><br />
			上次登录IP：<?php echo ($lastlogin["ip"]); ?><br />
		</div>
	</div>
	<div class="col-2 col-auto">
		<h6>网站待处理信息</h6>
		<div class="content">
		
			<ul class="ul">
			<?php if(is_array($websiteInfo['the_first_line'])): foreach($websiteInfo['the_first_line'] as $key=>$v): ?><li><a href="<?php echo ($v["url"]); ?>"><?php echo ($v["name"]); ?>(<?php echo ($v["pending_sums_tips"]); ?>)</a></li><?php endforeach; endif; ?>
			</ul>
			
			<ul class="ul">
			<?php if(is_array($websiteInfo['the_second_line'])): foreach($websiteInfo['the_second_line'] as $key=>$v): ?><li><a href="<?php echo ($v["url"]); ?>"><?php echo ($v["name"]); ?>(<?php echo ($v["pending_sums_tips"]); ?>)</a></li><?php endforeach; endif; ?>
			</ul>
			
			<div class="bk20 hr">
				<hr />
			</div>
		</div>
	</div>
	<div class="bk10"></div>
	<div class="col-2 fl mar-r10" style="width:48%">
		<h6>快捷方式</h6>
		<div class="content" id="admin_panel">
			<a href="<?php echo url('project/index');?>" class="button3">项目管理</a>
			<a href="<?php echo url('user/index');?>" class="button3">会员管理</a>
		 </div>
	</div>
	<div class="col-2 col-auto">
		<h6>系统公告</h6>
		<div class="content"> 
			※ 暂无系统公告
		</div>
	</div>
	<div class="bk10"></div>
</div>

</body>
</html>
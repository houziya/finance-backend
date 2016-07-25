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
<div class="pad-10">
	<div class="content-menu line-x blue"><?php echo ($topnav); ?></div>
	<div class="table-list">
		<form action="<?php echo url('edit');?>" method="post" id="form1">
		<table width="100%" cellspacing="0">
			<tr>				
				<th width="6%">排序</th>
				<th width="5%">ID</th>
				<th>菜单名称</th>
				<th width="20%">管理操作</th>
			</tr>
			<?php if(is_array($lists)): foreach($lists as $key=>$v): ?><tr>				
				<td><input type="text" name="sort[<?php echo ($v["id"]); ?>]" class="input-text text-c" size="2" value="<?php echo ($v["sort"]); ?>" /> </td>
				<td><?php echo ($v["id"]); ?></td>
				<td class="text-l"><?php echo ($v["spacer"]); ?> <?php echo ($v["name"]); ?> <?php echo ($v["code"]); ?> <?php echo ($v["isshow_tips"]); ?></td>
				<td><?php if($v['level'] <= 3): ?><a href="<?php echo url('add?pid='.$v['id']);?>">添加子菜单</a> |&nbsp;<?php endif; ?><a href="<?php echo url('edit?id='.$v['id']);?>">修改</a> | <a href="<?php echo url('delete?id='.$v['id']);?>" class="confirm">删除</a></td>
			</tr><?php endforeach; endif; ?>
		</table>
		<div class="btn">
			<input name="do" type="hidden" id="do" value="sort" />
			<input type="submit" class="button" name="dosubmit" value="排序" />
		</div>		
		</form>
	</div>
</div>

</body>
</html>
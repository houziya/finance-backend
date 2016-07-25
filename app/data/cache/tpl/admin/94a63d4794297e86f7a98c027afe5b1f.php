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
	<div class="table-form">
		<form action="" method="post" id="form1">
		<table width="100%">
			<tr>
				<th width="120">所属菜单</th>
				<td>
					<select name="data[pid]" id="pid">
						<option value="0">顶级菜单</option>
						<?php echo ($menuSelect); ?>
					</select>
				</td>
			</tr>
			<tr>
				<th>菜单名称</th>
				<td><input name="data[name]" type="text" class="input-text" id="name" /></td>
			</tr>
			<tr>
				<th>模块</th>
				<td><input name="data[module]" type="text" class="input-text" id="module" /></td>
			</tr>
			<tr>
				<th>控制器</th>
				<td><input name="data[controller]" type="text" class="input-text" id="controller" /></td>
			</tr>
			<tr>
				<th>方法</th>
				<td><input name="data[action]" type="text" class="input-text" id="action" /></td>
			</tr>
			<tr>
				<th>额外参数</th>
				<td><input name="data[args]" type="text" class="input-text" id="args" /></td>
			</tr>
			<tr>
				<th>外部链接</th>
				<td><input name="data[url]" type="text" class="input-text" id="url" size="50" /></td>
			</tr>
			<tr>
				<th>描述</th>
				<td><textarea rows="10" name="data[description]" style="width:80%;height:80px;"></textarea></td>
			</tr>
			<tr>
				<th>排序值</th>
				<td><input name="data[sort]" type="text" class="input-text" id="sort" value="0" /></td>
			</tr>
			<tr>
				<th>是否显示</th>
				<td><input name="data[is_show]" type="radio" value="1" checked="checked" /> 
					显示 
						<input name="data[is_show]" type="radio" value="0" />
				隐藏</td>
			</tr>
		</table>
		<div class="btn">
			<input name="do" type="hidden" value="dosubmit" />
			<input type="submit" class="button" name="dosubmit" value="确定" />&nbsp;&nbsp;<input type="button" class="button" name="goback" value="返回上页" onclick="javascript:window.history.back(-1);" />
		</div>
		</form>
	</div>
</div>

</body>
</html>
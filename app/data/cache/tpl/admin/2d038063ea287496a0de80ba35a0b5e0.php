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
				<th width="10%">角色名称</th>
				<th>成员列表</th>
				<th>角色描述</th>
				<th width="6%">状态</th>
				<th width="25%">管理操作</th>
			</tr>
			<?php if(is_array($lists)): foreach($lists as $key=>$v): ?><tr>				
				<td><input type="text" name="order[<?php echo ($v["id"]); ?>]" class="input-text text-c" size="2" value="<?php echo ($v["sort"]); ?>" /> </td>
				<td><?php echo ($v["id"]); ?></td>
				<td><?php echo ($v["name"]); ?></td>
				<td class="text-l">
				<?php if(is_array($v['users'])): foreach($v['users'] as $i=>$v2): ?><?php if($i != 0): ?>&nbsp;|&nbsp;<?php endif; ?>
				<?php if($v2['status'] == 0): ?><a href="<?php echo url('adminuser/edit?uid='.$v2['uid']);?>" class="gray4" title="待审用户"><?php echo ($v2["realname"]); ?></a>
				<?php else: ?>
				<a href="<?php echo url('adminuser/edit?uid='.$v2['uid']);?>" title="正常用户"><?php echo ($v2["realname"]); ?></a><?php endif; ?><?php endforeach; endif; ?>
				</td>
				<td><?php echo ($v["description"]); ?></td>
				<td><?php if($v['status'] == 0): ?><span class='red' title='待审核'>待审</span><?php elseif($v['status'] == 1): ?><span class='green' title='正常'>正常</span><?php elseif($v['status'] == 2): ?><span class='gray4' title='用户已锁定'>锁定</span><?php endif; ?></td>
				<td><a href="javascript:;" onclick="opendialog('<?php echo url('rolepriv?id='.$v['id']);?>','权限设置',1,'edit');">权限设置</a> | <a href="<?php echo url('adminuser/index?role_id='.$v['id']);?>">成员管理</a> | <a href="<?php echo url('edit?id='.$v['id']);?>">修改</a><?php if($v['id'] > 1): ?>| <a href="<?php echo url('delete?id='.$v['id']);?>" class="confirm">删除</a><?php endif; ?></td>
			</tr><?php endforeach; endif; ?>
		</table>
		<div class="btn">
			<input name="do" type="hidden" id="do" value="order" />
			<input type="submit" class="button" name="dosubmit" value="排序" />
		</div>		
		</form>
	</div>
	<div class="pages"><?php echo ($pages); ?></div>
</div>

</body>
</html>
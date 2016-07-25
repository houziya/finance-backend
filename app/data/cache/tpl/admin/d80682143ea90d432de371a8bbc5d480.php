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
//得到多选框ID集合
function get_ids() {
	var ids = '';
	$("input[name='ids[]']").each(function() {
		if($(this).attr('checked')=='checked') {
			ids += '|'+$(this).val();
		};
	});
	if(ids!=''){
		return ids.substring(1);
	}else{
		return '';
	}
}

$(function(){

	//批量删除
	$("#btn_del").click(function(){
		var ids = get_ids();
		if(ids==''){
			alert('请选择待删除的用户');
			return false;
		}
		if(!confirm('将会删除对应的前后台用户，删除后不可恢复！\n\n请确认是否删除？')){
			return false;
		}
		$("input[name='do']").val('dosubmit');
		$('#myform').attr('action','<?php echo url("delete");?>');
		$('#myform').submit();
	});

	//批量锁定
	$("#btn_lock").click(function(){
		var ids = get_ids();
		if(ids==''){
			alert('请选择待锁定的用户');
			return false;
		}
		if(!confirm('请确认是否锁定用户？')){
			return false;
		}
		$("input[name='do']").val('lock');
		$('#myform').attr('action','<?php echo url("edit");?>');
		$('#myform').submit();
	});

});
</script>
<div class="pad-10">
	<div class="content-menu line-x blue"><?php echo ($topnav); ?></div>
	<div class="explain-col mar-b8" id="search_form">
		<form name="search_form" action="" method="post" id="search_form">
		uid：
		<input name="search[uid]" type="text" class="input-text" value="<?php echo ($uid); ?>" size="6" />
		&nbsp;	用户名：
		<input name="search[username]" type="text" class="input-text" value="<?php echo ($username); ?>" size="10" />
		<input type="submit" name="dosubmit" class="button" value="搜索" />
		</form>
	</div>
	<div class="table-list">
		<form name="myform" action="#" method="post" id="myform">
		<table width="100%" cellspacing="0">
			<tr>
				<th width="3%"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
				<th width="5%">uid</th>
				<th>用户名</th>
				<th>真实姓名</th>
				<th>所属角色</th>
				<th>手机</th>
				<th>备注</th>
				<th>登录改密</th>
				<th>动态密码</th>
				<th>状态</th>
				<th width="15%">管理操作</th>
			</tr>
			<?php if(is_array($lists)): foreach($lists as $key=>$v): ?><tr>
				<td><?php if($v["uid"] != 1): ?><input name="ids[]" value="<?php echo ($v["uid"]); ?>" type="checkbox" /><?php endif; ?></td>
				<td><?php echo ($v["uid"]); ?></td>
				<td><?php echo ($v["username"]); ?></td>
				<td><?php echo ($v["realname"]); ?></td>
				<td><?php echo ($v["rolename"]); ?></td>
				<td><?php echo ($v["mobile"]); ?></td>
				<td><?php echo ($v["remark"]); ?></td>
				<td><?php echo getStatusTips($v['reset_pwd']);?></td>
				<td><?php echo getStatusTips($v['is_token']);?></td>
				<td><?php if($v["status"] == 0): ?><span class='red' title='待审核'>待审</span><?php elseif($v["status"] == 1): ?><span class='green' title='正常'>正常</span><?php elseif($v["status"] == 2): ?><span class='gray4' title='用户已锁定'>锁定</span><?php endif; ?></td>
				<td>
				    <a href="<?php echo url('edit?uid='.$v['uid']);?>">修改</a>&nbsp;&nbsp;
				    <a href="<?php echo url('cleanloginlimit?username='.$v['username']);?>" title="输错密码次数达到5次，一天内不能登录后台">清空输错密码次数</a>
				</td>
			</tr><?php endforeach; endif; ?>
		</table>
		<div class="btn">
	    	<label for="check_box">全选/取消</label>
			<input name="do" type="hidden" value="" />
			<input type="button" class="button" name="btn_del" id="btn_del" value="删除" />
			<input type="button" class="button" name="btn_lock" id="btn_lock" value="锁定" />
		</div>
		</form>
	</div>
	<div class="pages"><?php echo ($pages); ?></div>
</div>

</body>
</html>
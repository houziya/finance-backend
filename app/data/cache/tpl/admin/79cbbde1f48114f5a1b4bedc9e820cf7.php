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
//批量设置
function setStatus(v){
    var ids = get_ids();
    if(ids==''){
        alert('请选择待设置的id');
        return false;
    }
    $("input[name='status']").val(v);
    $('#myform').attr('action','<?php echo url("setVersionStatus");?>');
    $('#myform').submit();
};

$(function(){
       $(".content-menu>a").click(function(){
                var text = $(this).text();
                if(text == '添加版本'){
                  var url = "<?php echo url('versionAdd?sort_id='.$row['id']);?>";
                  window.location.href = url;
                }
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
                <input name="search[sort_id]" type="hidden" class="input-text" value="<?php echo ($row['id']); ?>" size="10" />
                版本ID：
		<input name="search[id]" type="text" class="input-text" value="<?php echo ($id); ?>" size="10" />
		版本：
                <input name="search[version]" type="text" class="input-text" value="<?php echo ($version); ?>" size="15" />
                &nbsp; 状态：
                <?php echo ($status_select); ?>
                 &nbsp; 添加时间：
                <?php echo ($input_add_time); ?>
                 &nbsp; 更新时间：
                <?php echo ($input_update_time); ?>
		&nbsp;	
		<input type="submit" name="dosubmit" class="button" value="搜索" />
		</form>
	</div>
	<div class="table-list">
		<form name="myform" action="#" method="post" id="myform">
		<table width="100%" cellspacing="0">
			<tr>
				<th width="3%"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
                                <th width="10%">软件名称</th>
				<th width="5%">版本ID</th>
				<th width="10%">版本名称</th>
				<th width="5%">状态</th>
                                <th>时间</th>
				<th width="15%">管理操作</th>
                                <th width="35%"></th>
			</tr>
			<?php if(is_array($lists)): foreach($lists as $key=>$v): ?><tr>
				<td><if><input name="ids[]" value="<?php echo ($v["id"]); ?>" type="checkbox" /></if></td>
                                <td><?php echo ($row['name']); ?></td>
                                <td><?php echo ($v["id"]); ?></td>
                                <td><?php echo ($v["version"]); ?></td>
                                <td><?php echo ($v["status_tips"]); ?></td>
                                <td><?php echo (date('Y-m-d H:i:s',$v["add_time"])); ?><br/>
                                     <?php echo (date('Y-m-d H:i:s',$v["update_time"])); ?><br/></td>
				<td>
				    <a href="<?php echo url('versionEdit?id='.$v['id']);?>">修改</a>&nbsp;&nbsp;
                                    <a href="<?php echo url('queryConfig?id='.$v['id'].'&menu_id=37');?>">查询配置</a>&nbsp;&nbsp;
				</td>
                                <td></td>
			</tr><?php endforeach; endif; ?>
		</table>
		<div class="btn">
                <label for="check_box">全选/取消</label>
                <input type="hidden" class="button" name="status"  id="status"  value="" />
                <input type="hidden" class="button" name="sort_id"  id="sort_id"  value="<?php echo ($row['id']); ?>" />
                <input type="button" class="button" name="btn_close" id="btn_close" onclick="setStatus(0)" value="禁用" />
                <input type="button" class="button" name="btn_open" id="btn_open" onclick="setStatus(1)"  value="启用" />
                </div>
		</form>
	</div>
	<div class="pages"><?php echo ($pages); ?></div>
</div>

</body>
</html>
<script type="text/javascript">
    $(".content-menu").prepend("<a class='on' href='<?php echo url('Index?id='.$row['id'].'&menu_id=28');?>'><em>软件列表</em></a> <span>|</span>");
</script>
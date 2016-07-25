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
        $("#name").formValidator({onshow:"请输入软件版本",onfocus:"请输入软件版本",oncorrect:"输入正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"软件版本不能为空"});
    })
</script>
<div class="pad-10">
    <div class="content-menu line-x blue"><?php echo ($topnav); ?></div>
    <div class="table-form">
        <form action="" method="post" id="form1">
            <input name="sort_id" type="hidden" class="input-text" id="sort_id" value="<?php echo ($info["sort_id"]); ?>" />
            <input name="id" type="hidden" class="input-text" id="id" value="<?php echo ($info["id"]); ?>" />
            <table width="100%">		
              <!--  <tr>
                    <th>软件ID</th>
                    <td><input name="data[sort_id]" type="text" class="input-text" id="sort_id" value="<?php echo ($info["sort_id"]); ?>" /></td>
                </tr>-->
                 <tr>
                    <th>版本名称</th>
                    <td><input name="data[version]" type="text" class="input-text" id="version" value="<?php echo ($info["version"]); ?>" /></td>
                </tr>
                <tr>
                    <th>下载地址</th>
                    <td><input name="data[download]" type="text" class="input-text" id="download" value="<?php echo ($info["download"]); ?>" /></td>
                </tr>
                <tr>
                    <th>版本描叙</th>
                    <td><textarea rows="10" cols="90" name="data[description]" id="description" style="width:80%;height:80px;"><?php echo ($info["description"]); ?></textarea></td>
                </tr>
                <tr>
                    <th>状态</th>
                    <td>
                <input name="data[status]" type="radio" value="1" <?php if($info["status"] == 1): ?>checked="checked"<?php endif; ?> /> 
                开启&nbsp;
                <input name="data[status]" type="radio" value="0" <?php if($info["status"] == 0): ?>checked="checked"<?php endif; ?> /> 
                禁用&nbsp;
                </td>
                </tr>
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
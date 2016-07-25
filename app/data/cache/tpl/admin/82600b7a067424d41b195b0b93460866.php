<?php if (!defined('FEE_PATH')) exit();?><?php
/**
 * 消费统计视图
 * User: wangmengmeng
 * Date: 2016/7/6
 * Time: 10:47
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
    <!--<div class="explain-col mar-b8" id="search_form">
        <form name="search_form" action="" method="post" id="search_form">
            软件名称：
            <input name="name" type="text" class="input-text" value="" size="10" />
            手机号：
            <input name="mobile" type="text" class="input-text" value="" size="10" />
            客户端：
            <input name="company" type="text" class="input-text" value="" size="10" />
            &nbsp;
            <input type="submit" name="dosubmit" class="button" value="搜索" />
        </form>
    </div>-->
    <div class="table-list">
        <form name="myform" action="#" method="post" id="myform">
            <table width="100%" cellspacing="0">
                <tr>
                    <th width="3%"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
                    <th width="4%">软件</th>
                    <th width="4%">版本</th>
                    <th width="4%">客户端</th>
                    <th width="4%">手机号</th>
                    <th width="10%">消费金额</th>
                    <th width="10%">时间</th>
                    <th width="10%">详情</th>
                </tr>
                <?php if(is_array($lists)): foreach($lists as $key=>$v): ?><tr>
                        <td><if><input name="ids[]" value="<?php echo ($v["id"]); ?>" type="checkbox" /></if></td>
                        <td><?php echo ($v["sort_id"]); ?></td>
                        <td><?php echo ($v["sort_ver_id"]); ?></td>
                        <td><?php echo ($v["company"]); ?></td>
                        <td><?php echo ($v["mobile"]); ?></td>
                        <td><?php echo ($v["amount"]); ?></td>
                        <td><?php echo ($v["start_time"]); ?>-<?php echo ($v["end_time"]); ?></td>
                        <td><a href="<?php echo url('detail?id='.$v['id']);?>">查看详情</a></td>
                    </tr><?php endforeach; endif; ?>
            </table>
            <div class="btn">
                <label for="check_box">全选/取消</label>
                <!--<input type="hidden" class="button" name="status"  id="status"  value="" />
                <input type="button" class="button" name="btn_close" id="btn_close" onclick="setStatus(0)" value="关闭" />
                <input type="button" class="button" name="btn_open" id="btn_open" onclick="setStatus(1)"  value="开启" />-->
            </div>
        </form>
    </div>
    <div class="pages"><?php echo ($pages); ?></div>
</div>
</body>
</html>
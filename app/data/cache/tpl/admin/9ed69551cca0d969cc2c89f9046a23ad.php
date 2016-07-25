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
    <div class="explain-col mar-b8" id="search_form">
        <form name="search_form" action="" method="post" id="search_form">
            周期类型:
            <select name="search[type]" id="stat-cycel">
                <?php if(is_array($stat_cycel)): foreach($stat_cycel as $k=>$v): ?><option value="<?php echo ($k); ?>" <?= $k == $type ? "selected" : ""?>><?php echo ($v); ?></option><?php endforeach; endif; ?>
            </select>
            手机号：
            <input name="search[mobile]" type="text" class="input-text" value="<?php echo ($mobile); ?>" size="16" />
            店名：
            <input name="search[subbranch]" type="text" class="input-text" value="<?php echo ($subbranch); ?>" size="16" />
            开始日期：
            <?= helper_form::date("search[start_time]") ?>
            结束日期：
            <?= helper_form::date("search[end_time]") ?>
            &nbsp;
            <input type="submit" name="dosubmit" class="button" value="搜索" />
        </form>
    </div>
    <div class="table-list">
        <form name="myform" action="#" method="post" id="myform">
            <table width="100%" cellspacing="0">
                <tr>
                    <th width="3%"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
                    <th width="4%">软件</th>
                    <th width="4%">版本</th>
                    <th width="4%">店名</th>
                    <th width="6%">手机号</th>
                    <th width="4%">消费金额</th>
                    <th width="10%">时间</th>
                    <th width="4%">抓取次数</th>
                    <th width="4%">详情</th>
                </tr>
                <?php if(is_array($lists)): foreach($lists as $key=>$v): ?><tr>
                        <td><if><input name="ids[]" value="<?php echo ($v["id"]); ?>" type="checkbox" /></if></td>
                        <td><?php echo ($v["name"]); ?></td>
                        <td><?php echo ($v["version"]); ?></td>
                        <td><?php echo ($v["subbranch"]); ?></td>
                        <td><?php echo ($v["mobile"]); ?></td>
                        <td><?php echo ($v["amount"]); ?></td>
                        <td><?php echo ($v["topdate"]); ?></td>
                        <td><?php echo ($v["degreen"]); ?></td>
                        <td><a href="<?php echo url('collectDetail?sort_client_id='.$v['sort_client_id'].'&date='.$v['topdate']);?>">查看详情</a> | <a href="<?php echo url('expenseStat?sort_client_id='.$v['sort_client_id'].'&name='.$v['name'].'&type='.($type|0));?>">查看报表</a></td>
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
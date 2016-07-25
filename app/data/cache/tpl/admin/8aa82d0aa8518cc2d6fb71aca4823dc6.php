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
        $('#myform').attr('action','<?php echo url("setStatus");?>');
        $('#myform').submit();
    };
</script>
<div class="pad-10">
    <div class="content-menu line-x blue"><?php echo ($topnav); ?></div>
    <div class="explain-col mar-b8" id="search_form">
        <form name="search_form" action="" method="post" id="search_form">
            客户端id：
            <input name="search[id]" type="text" class="input-text" value="<?php echo ($id); ?>" size="3" />
            手机号：
            <input name="search[mobile]" type="text" class="input-text" value="<?php echo ($mobile); ?>" size="15" />
            激活码：
            <input name="search[code]" type="text" class="input-text" value="<?php echo ($code); ?>" size="32" />
            识别码：
            <input name="search[identification]" type="text" class="input-text" value="<?php echo ($identification); ?>" size="15" />
            分店名：
            <input name="search[company]" type="text" class="input-text" value="<?php echo ($company); ?>" size="20" />
            分店号：
            <input name="search[subbranch]" type="text" class="input-text" value="<?php echo ($subbranch); ?>" size="3" />
            添加时间：<?php echo ($input_add_time); ?>
            更新时间：<?php echo ($input_update_time); ?>
            状态：
            <?php echo ($status_select); ?>
            &nbsp;	
            <input type="submit" name="dosubmit" class="button" value="搜索" />
        </form>
    </div>
    <div class="table-list">
        <form name="myform" action="#" method="post" id="myform">
            <table width="100%" cellspacing="0">
                <tr>
                    <th width="3%"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
                    <th width="4%">ID</th>
                    <th width="10%">激活码</th>
                    <th width="8%">手机号</th>
                    <th width="10%">识别码</th>
                    <th width="16%">分店名</th>
                    <th width="5%">分店号</th>
                    <th width="5%">状态</th>
                    <th width="11%">添加时间/更新时间</th>
                    <th width="8%">操作</th>
                </tr>
                <?php if(is_array($lists)): foreach($lists as $key=>$v): ?><tr>
                    <td><if><input name="ids[]" value="<?php echo ($v["id"]); ?>" type="checkbox" /></if></td>
                    <td><?php echo ($v["id"]); ?></td>
                    <td><?php echo ($v["code"]); ?></td>
                    <td><?php echo ($v["mobile"]); ?></td>
                    <td><?php echo ($v["identification"]); ?></td>
                    <td><?php echo ($v["company"]); ?></td>
                    <td><?php echo ($v["subbranch"]); ?></td>
                    <td><?php if($v["status"] == 1): ?>启用<?php else: ?>禁用<?php endif; ?></td>
                    <td><?php echo (date('Y-m-d H:i:s',$v["add_time"])); ?><br/><?php echo (date('Y-m-d H:i:s',$v["update_time"])); ?></td>
                    <td>
                      <a href="<?php echo url('edit?id='.$v['id']);?>">编辑</a>
                      <a href="<?php echo url('queryConfig?id='.$v['id'].'&menu_id=39');?>">查询配置</a>
                      <a href="<?php echo url('statistics/ClientStatus?id='.$v['id']);?>">客户端状态报表</a>
                    </td>
                    </tr><?php endforeach; endif; ?>
            </table>
            <div class="btn">
                <label for="check_box">全选/取消</label>
               <input type="hidden" class="button" name="status"  id="status"  value="" />
                <input type="button" class="button" name="btn_close" id="btn_close" onclick="setStatus(0)" value="关闭" />
                <input type="button" class="button" name="btn_open" id="btn_open" onclick="setStatus(1)"  value="开启" />
            </div>
        </form>
    </div>
    <div class="pages"><?php echo ($pages); ?></div>
</div>

</body>
</html>
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

    $(function(){
	

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
            软件厂商：
            <input name="search[company]" type="text" class="input-text" value="<?php echo ($company); ?>" size="10" />
            软件名称：
            <input name="search[name]" type="text" class="input-text" value="<?php echo ($name); ?>" size="15" />
            联系人：
            <input name="search[contact_name]" type="text" class="input-text" value="<?php echo ($contact_name); ?>" size="15" />
             联系电话：
            <input name="search[contact_phone]" type="text" class="input-text" value="<?php echo ($contact_phone); ?>" size="15" />  
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
                    <th width="10%">软件厂商</th>
                    <th width="16%">软件名称</th>
                    <th width="16%">官网地址</th>
                    <th width="10%">联系人</th>
                    <th width="16%">联系电话</th>
                    <th width="5%">状态</th>
                    <th width="13%">添加时间/更新时间</th>
                    <th width="50%">管理操作</th>
                    <th width="10%"></th>
                </tr>
                <?php if(is_array($lists)): foreach($lists as $key=>$v): ?><tr>
                        <td><if><input name="ids[]" value="<?php echo ($v["id"]); ?>" type="checkbox" /></if></td>
                    <td><?php echo ($v["company"]); ?></td>
                    <td><?php echo ($v["name"]); ?></td>
                    <td><?php echo ($v["url"]); ?></td>
                    <td><?php echo ($v["contact_name"]); ?></td>
                    <td><?php echo ($v["contact_phone"]); ?></td>
                    <td><?php echo ($v["status_tips"]); ?></td>
                <td><?php echo (date('Y-m-d H:i:s',$v["add_time"])); ?><br/>
                <?php echo (date('Y-m-d H:i:s',$v["update_time"])); ?></td>
                    <td>
                        <a href="<?php echo url('edit?id='.$v['id']);?>">修改</a>&nbsp;&nbsp;
                        <a href="<?php echo url('versionIndex?sort_id='.$v['id']);?>/menu_id/30">查看版本</a>&nbsp;&nbsp;
                    </td>
                    <td></td>
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
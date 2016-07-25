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
        $('#myform').attr('action','<?php echo url("setQueryConfigStatus");?>');
        $('#myform').submit();
    };
    $(function(){
       $(".content-menu>a").click(function(){
                var text = $(this).text();
                if(text == '添加配置'){
                  var url = "<?php echo url('QueryConfigAdd?cid='.$cid);?>";
                  window.location.href = url;
                }
	});	
});
</script>
<div class="pad-10">
    <div class="content-menu line-x blue"><?php echo ($topnav); ?></div>
    <div class="table-list">
        <form name="myform" action="#" method="post" id="myform">
            <table width="100%" cellspacing="0">
                <tr>
                    <th width="3%"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
                    <th width="4%">CID</th>
                    <th width="5%">库类型</th>
                    <th width="10%">库名称</th>
                    <th width="10%">用户名</th>
                    <th width="8%">密码</th>
                    <th width="14%">数据库地址</th>
                    <th width="6%">查询类型</th>
                    <th width="6%">查询频率</th>
                    <th width="15%">时间</th>
                    <th width="5%">状态</th>
                    <th width="15%">操作</th>
                </tr>
                <?php if(is_array($lists)): foreach($lists as $key=>$v): ?><tr>
                    <td><if><input name="ids[]" value="<?php echo ($v["id"]); ?>" type="checkbox" /></if></td>
                    <td><?php echo ($v["cid"]); ?></td>
                    <td><?php if($v["db_type"] == '1'): ?>mysql<?php elseif($v["db_type"] == '2'): ?>sqlserver<?php elseif($v["db_type"] == '3'): ?>oracle<?php elseif($v["db_type"] == '4'): ?>interbase<?php endif; ?></td>
                    <td><?php echo ($v["db_name"]); ?></td>
                    <td><?php echo ($v["db_username"]); ?></td>
                    <td><?php echo ($v["db_pwd"]); ?></td>
                    <td><?php echo ($v["db_address"]); ?></td>
                    <td><?php if($v["qu_type"] == '1'): ?>分钟<?php elseif($v["qu_type"] == '2'): ?>小时<?php elseif($v["qu_type"] == '3'): ?>天<?php elseif($v["qu_type"] == '4'): ?>周<?php elseif($v["qu_type"] == '5'): ?>月<?php endif; ?></td>
                    <td><?php if($v["qu_type"] == '1'): ?><?php echo ($v["qu_frequency"]); ?><?php elseif($v["qu_type"] == '2'): ?><?php echo ($v["qu_frequency"]); ?><?php elseif($v["qu_type"] == '3'): ?><?php echo ($v["qu_frequency"]); ?><?php elseif($v["qu_type"] == '4'): ?><?php echo ($v["qu_frequency"]); ?><?php elseif($v["qu_type"] == '5'): ?><?php echo ($v["qu_frequency"]); ?><?php endif; ?></td>
                    <td><?php if($v["update_time"] != '0'): ?><?php echo (date('Y-m-d H:i:s',$v["update_time"])); ?><?php else: ?><?php echo (date('Y-m-d H:i:s',$v["add_time"])); ?><?php endif; ?></td>
                    <td><?php if($v["status"] == '1'): ?>启用<?php else: ?>禁用<?php endif; ?></td>
                    <td>
                      <a href="<?php echo url('queryConfigEdit?id='.$v['id']);?>">编辑</a>
                    </tr><?php endforeach; endif; ?>
            </table>
            <div class="btn">
                <label for="check_box">全选/取消</label>
               <input type="hidden" class="button" name="status"  id="status"  value="" />
                <input type="button" class="button" name="btn_close" id="btn_close" onclick="setStatus(0)" value="禁用" />
                <input type="button" class="button" name="btn_open" id="btn_open" onclick="setStatus(1)"  value="开启" />
            </div>
        </form>
    </div>
    <div class="pages"><?php echo ($pages); ?></div>
</div>
</body>
</html>
<script type="text/javascript">
    $(".content-menu").prepend("<a class='on' href='<?php echo url('Index?menu_id=35');?>'><em>软件客户端</em></a> <span>|</span>");
</script>
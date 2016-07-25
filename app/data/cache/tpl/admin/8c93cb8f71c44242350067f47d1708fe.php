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
    function setStatus(o,v){
        var ids = get_ids();
        if(ids==''){
            alert('请选择待设置的id');
            return false;
        }
        if(o == 'sstatus'){
           $("input[name='sstatus']").val(v);
        }else{
           $("input[name='cstatus']").val(v); 
        }
        $('#myform').attr('action','<?php echo url("setBatchStatus");?>');
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
            批次ID：
            <input name="search[id]" type="text" class="input-text" value="<?php echo ($id); ?>" size="2" />
            批次号：
            <input name="search[batch_id]" type="text" class="input-text" value="<?php echo ($batch_id); ?>" size="15" />
            标题：
            <input name="search[title]" type="text" class="input-text" value="<?php echo ($title); ?>" size="15" />
            时间：
            <?php echo ($input_start_time); ?>
            - 
            <?php echo ($input_end_time); ?>
            
            生成状态：
             <?php echo ($mstatus_select); ?>
            制卡状态：
            <?php echo ($cstatus_select); ?>
            出售状态：
            <?php echo ($sstatus_select); ?>
            &nbsp;	
            <input type="submit" name="dosubmit" class="button" value="搜索" />
        </form>
    </div>
    <div class="table-list">
        <form name="myform" action="#" method="post" id="myform">
            <table width="100%" cellspacing="0">
                <tr>
                    <th width="3%"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
                    <th width="5%">ID</th>
                    <th width="16%">批次号</th>
                    <th width="16%">标题</th>
                    <th width="16%">开始时间</th>
                    <th width="10%">结束时间</th>
                    <th width="5%">个数</th>
                    <th width="6%">生成状态</th>
                    <th width="6%">制卡状态</th>
                    <th width="6%">出售状态</th>
                    <th width="50%">管理操作</th>
                    <th width="10%"></th>
                </tr>
                <?php if(is_array($lists)): foreach($lists as $key=>$v): ?><tr>
                    <td><if><input name="ids[]" value="<?php echo ($v["id"]); ?>" type="checkbox" /></if></td>
                    <td><?php echo ($v["id"]); ?></td>
                    <td><?php echo ($v["batch_id"]); ?></td>
                    <td><?php echo ($v["title"]); ?></td>
                    <td><?php echo (date('Y-m-d',$v["start_time"])); ?></td>
                    <td><?php echo (date('Y-m-d',$v["end_time"])); ?></td>
                    <td><?php echo ($v["num"]); ?></td>
                    <td><?php echo ($v["mstatus_tips"]); ?></td>
                    <td><?php echo ($v["cstatus_tips"]); ?></td>
                    <td><?php echo ($v["sstatus_tips"]); ?></td>
                    <td>
                        <a href="<?php echo url('batchIndex?bid='.$v['id']);?>">查看批次</a>&nbsp;&nbsp;
                        <a href="<?php echo url('Edit?id='.$v['id']);?>">编辑</a>&nbsp;&nbsp;
                    </td>
                    <td></td>
                    </tr><?php endforeach; endif; ?>
            </table>
            <div class="btn">
                <label for="check_box">全选/取消</label>
                <input type="hidden" class="button" name="cstatus"  id="status"  value="" />
                <input type="hidden" class="button" name="sstatus"  id="status"  value="" />
                <input type="button" class="button" name="btn_close" id="btn_close" onclick="setStatus('cstatus',1)" value="已制卡" />
                <input type="button" class="button" name="btn_open" id="btn_open" onclick="setStatus('sstatus',1)"  value="已出售" />
            </div>
        </form>
    </div>
    <div class="pages"><?php echo ($pages); ?></div>
</div>

</body>
</html>
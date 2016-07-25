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
    <div class="table-form">
       <form action="" method="post" id="form1">
        <input name="id" type="hidden" class="input-text" id="id" value="<?php echo ($info["id"]); ?>" /> 
        <input name="cid" type="hidden" class="input-text" id="cid" value="<?php echo ($info["cid"]); ?>" /> 
        <table width="100%">
              <tr>
                <th width="8%"><b>查询类型:</b></th>
                <td width="80%" colspan="3"> <input name="data[qu_type]" onchange="frequencyA()" type="radio" value="1" <?php if($info["qu_type"] == 1): ?>checked="checked"<?php endif; ?> /> 
					分钟&nbsp;
					<input name="data[qu_type]" onchange="frequencyA()"  type="radio" value="2" <?php if($info["qu_type"] == 2): ?>checked="checked"<?php endif; ?> /> 
					小时&nbsp;
					<input name="data[qu_type]" onchange="frequencyA()" type="radio" value="3" <?php if($info["qu_type"] == 3): ?>checked="checked"<?php endif; ?> /> 
					天
                                        <input name="data[qu_type]" onchange="frequencyA()"  type="radio" value="4" <?php if($info["qu_type"] == 4): ?>checked="checked"<?php endif; ?> /> 
					周
                                        <input name="data[qu_type]" onchange="frequencyA()" type="radio" value="5" <?php if($info["qu_type"] == 5): ?>checked="checked"<?php endif; ?> /> 
					月
                                        <input name="data[qu_type]" onchange="frequencyA()"  type="radio" value="6" <?php if($info["qu_type"] == 6): ?>checked="checked"<?php endif; ?> /> 
				        其他
        </td>
            </tr>
            <tr id="frequency" style='display:<?php if($info["qu_type"] == 6): ?>none<?php endif; ?>'>
                <th width="5%"><b>查询次数:</b></th>
                <td width="5%"><input  type="text"  name="data[qu_num]" id="qu_num"  onchange="frequency()" value="<?php echo ($info["qu_num"]); ?>"   class="input-text" /></td>
                 <th width="5%"><b>频率:</b></th>
                <td width="88%" style="padding-left:0px;"><input    type="text"  name="data[qu_frequency]" id="qu_frequency"  readOnly="true" value="<?php echo ($info["qu_frequency"]); ?>"   class="input-text" /><!--<span id="msg" style="color:red"></span>--></td>
            </tr>
            <tr>
                <th width="8%"><b>数据库类型:</b></th>
                <td width="80%" colspan="3">
                    <input name="data[db_type]" type="radio" value="1" checked="checked"/> 
                    mysql&nbsp;
            <input name="data[db_type]" type="radio" value="2" <?php if($info["db_type"] == 2): ?>checked="checked"<?php endif; ?> /> 
            sql server &nbsp;
            &nbsp;
            <input name="data[db_type]" type="radio" value="3" <?php if($info["db_type"] == 3): ?>checked="checked"<?php endif; ?> /> 
            oracle&nbsp;
            &nbsp;
            <input name="data[db_type]" type="radio" value="4" <?php if($info["db_type"] == 4): ?>checked="checked"<?php endif; ?> /> 
            InterBase&nbsp;
            &nbsp;
            </td>
            </tr>
            <tr>
                <th width="8%"><b>数据库名:</b></th>
                <td width="80%" colspan="3"><input name="data[db_name]" type="text" class="input-text" id="db_name" value="<?php echo ($info["db_name"]); ?>" /></td>
            </tr>
            <tr>
                <th width="8%"><b>数据库用户名:</b></th>
                <td width="80%" colspan="3"><input name="data[db_username]" type="text" class="input-text" id="db_username" value="<?php echo ($info["db_username"]); ?>" /></td>
            </tr>

            <tr>
                <th width="8%"><b>数据库密码:</b></th>
                <td width="80%" colspan="3"><input name="data[db_pwd]" type="text" class="input-text" id="db_pwd" value="<?php echo ($info["db_pwd"]); ?>" /></td>
            </tr>
            <tr>
                <th width="8%"><b>数据库地址:</b></th>
                <td width="80%" colspan="3"><input name="data[db_address]" type="text" class="input-text" id="db_address" value="<?php echo ($info["db_address"]); ?>" /></td>
            </tr>
            <tr>
                <th width="8%"><b>查询SQL:</b></th>
                <td width="80%" colspan="3"><textarea rows="10" cols="90" name="data[db_sql]" id="db_sql" style="width:80%;height:80px;"><?php echo ($info["db_sql"]); ?></textarea></td>
            </tr>
            <?php if($info["update_time"] != '0'): ?><tr>
                <th width="8%"><b>修改时间:</b></th>
                <td width="80%" colspan="3"><?php echo (date('Y-m-d H:i:s',$info["update_time"])); ?></td>
               </tr>
            <else/>
             <tr>
                <th width="8%"><b>添加时间:</b></th>
                <td width="80%" colspan="3"><?php echo (date('Y-m-d H:i:s',$info["add_time"])); ?></td>
            </tr><?php endif; ?>
        </table>
        <div class="btn">
          <input name="do" type="hidden" value="dosubmit" />
            <input type="submit" class="button" name="dosubmit" value="确定" id="dosubmit" />&nbsp;&nbsp;
            <input type="button" class="button" name="goback" value="返回上页" onclick="javascript:window.history.back(-1);" />
        </div>
      </form>
    </div>
</div>

</body>
</html>
<script type="text/javascript">  
     function frequency(){
        var qu_type = $('input:radio[name="data[qu_type]"]:checked ').val();
        var qu_num = parseFloat($('#qu_num').val()); 
        if(qu_type > 1 && qu_type <6){
             var frequency = 1/qu_num;
             var frequencyA  = frequency.toFixed(4);
             $("#qu_frequency").val(frequencyA);
             $("#frequency").show();
        }else if(qu_type == 6){
            $("#qu_num").val(0);
            $("#qu_frequency").val(0);
            $("#frequency").hide();
        }else{
             $("#qu_num").val(1);
             $("#qu_frequency").val(10);
             $("#frequency").show();  
        } 
     } 
     
    //切换查询类型，设置默认值
    function frequencyA(){
      var qu_type = $('input:radio[name="data[qu_type]"]:checked ').val();
      var qu_num = parseFloat($('#qu_num').val()); 
      if(qu_type == 2){
            qu_num = 6;
           $("#qu_num").val(qu_num);
      }
      if(qu_type == 2){
            qu_num = 6;
           $("#qu_num").val(qu_num);
      }else if(qu_type == 3){
            qu_num = 144;
           $("#qu_num").val(qu_num);
      }else if(qu_type == 4){
            qu_num = 1008;
           $("#qu_num").val(qu_num);
      }else if(qu_type == 5){
            qu_num = 5040;
           $("#qu_num").val(qu_num);
      }
      if(qu_type > 1 && qu_type <6){
             var frequency = 1/qu_num;
             var frequencyA  = frequency.toFixed(4);
             $("#qu_frequency").val(frequencyA);
             $("#frequency").show();
        }else if(qu_type == 6){
            $("#qu_num").val(1);
            $("#qu_frequency").val(0);
            $("#frequency").hide();
        }else{
             $("#qu_num").val(1);
             $("#qu_frequency").val(10);
             $("#frequency").show();  
        } 
    }    
</script>
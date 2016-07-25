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
            <input name="sort_id" type="hidden" class="input-text" id="sort_id" value="<?php echo ($sort_id); ?>" />
            <input name="vid" type="hidden" class="input-text" id="vid" value="<?php echo ($info["vid"]); ?>" />
            <input name="id" type="hidden" class="input-text" id="id" value="<?php echo ($info["id"]); ?>" />
            <table width="100%">		
                <tr>
                    <th>查询类型</th>
                    <td colspan="3">
                        <input name="data[qu_type]"  onchange="frequencyA()" type="radio" value="1" checked="checked" /> 
					分钟&nbsp;
					<input name="data[qu_type]" onchange="frequencyA()" type="radio" value="2" <?php if($info["qu_type"] == 2): ?>checked="checked"<?php endif; ?> /> 
					小时&nbsp;
					<input name="data[qu_type]" onchange="frequencyA()" type="radio" value="3" <?php if($info["qu_type"] == 3): ?>checked="checked"<?php endif; ?> /> 
					天
                                        <input name="data[qu_type]" onchange="frequencyA()" type="radio" value="4" <?php if($info["qu_type"] == 4): ?>checked="checked"<?php endif; ?> /> 
					周
                                        <input name="data[qu_type]" onchange="frequencyA()" type="radio" value="5" <?php if($info["qu_type"] == 5): ?>checked="checked"<?php endif; ?> /> 
					月
                                        <input name="data[qu_type]" onchange="frequencyA()"  type="radio" value="6" <?php if($info["qu_type"] == 6): ?>checked="checked"<?php endif; ?> /> 
				        其他
                        </td>
                </tr>
                 <tr id="frequency"  style='display:<?php if($info["qu_type"] == 6): ?>none<?php endif; ?>'>
                    <th  width="8%">查询次数</th>
                    <td  width="5%"><input  type="text"  name="data[qu_num]" id="qu_num"  onchange="frequency()" value="<?php echo ($info["qu_num"]); ?>"   class="input-text" /></td>
                    <td  width="5%">频率:</td>
                    <td width="80%"><input    type="text"  name="data[qu_frequency]" id="qu_frequency"  readOnly="true" value="<?php echo ($info["qu_frequency"]); ?>"   class="input-text" /></td>
                </tr>
                <tr>
                    <th>数据库类型</th>
                    <td colspan="3"><input name="data[db_type]" type="radio" value="1" <?php if($info["db_type"] == 1): ?>checked="checked"<?php endif; ?> /> 
                Mysql&nbsp;
                <input name="data[db_type]" type="radio" value="2" <?php if($info["db_type"] == 2): ?>checked="checked"<?php endif; ?> /> 
                sql server&nbsp;
                <input name="data[db_type]" type="radio" value="3" <?php if($info["db_type"] == 3): ?>checked="checked"<?php endif; ?> /> 
                oracle
                <input name="data[db_type]" type="radio" value="4" <?php if($info["db_type"] == 4): ?>checked="checked"<?php endif; ?> /> 
                InterBase
                </td>
                </tr>
                <tr>
                    <th>数据库名</th>
                    <td colspan="3"><input type="text" name="data[db_name]" id="db_name" value="<?php echo ($info["db_name"]); ?>"  class="input-text" /></td>
                </tr>
                <tr>
                    <th>数据库用户名</th>
                    <td colspan="3"><input type="text" name="data[db_username]" id="db_username" value="<?php echo ($info["db_username"]); ?>" class="input-text" /></td>
                </tr>
                <tr>
                    <th>数据库密码</th>
                    <td colspan="3"><input type="text" name="data[db_pwd]" id="db_pwd" value="<?php echo ($info["db_pwd"]); ?>"  class="input-text" /></td>
                </tr>
                <tr>
                    <th>数据库地址</th>
                    <td colspan="3"><input type="text" name="data[db_address]" id="db_address" value="<?php echo ($info["db_address"]); ?>"  class="input-text" /></td>
                </tr>
                <tr>
                    <th>数据库查询SQL</th>
                    <td colspan="3"><textarea rows="10" cols="90" name="data[db_sql]" id="db_sql" style="width:80%;height:80px;"><?php echo ($info["db_sql"]); ?></textarea></td>
                </tr>
                <tr>
                    <th>状态</th>
                    <td colspan="3">
                <input name="data[status]" type="radio" value="1" <?php if($info["status"] == 1): ?>checked="checked"<?php endif; ?> /> 
                开启&nbsp;
                <input name="data[status]" type="radio" value="0" <?php if($info["status"] == 0): ?>checked="checked"<?php endif; ?> /> 
                禁用&nbsp;
                 <input name="data[status]" type="radio" value="2" <?php if($info["status"] == 2): ?>checked="checked"<?php endif; ?> /> 
                默认&nbsp;
                </td>
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
                <input name="data[uid]" type="hidden" value="<?php echo ($info["uid"]); ?>" />
                <input name="do" type="hidden" value="dosubmit" />
                <input type="submit" class="button" name="dosubmit" value="确定" id="dosubmit" />&nbsp;&nbsp;<input type="button" class="button" name="goback" value="返回上页" onclick="javascript:window.history.back(-1);" />
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
<include file="admin@header" />
<script type="text/javascript">
    $(function(){
        $.formValidator.initConfig({formid:"form1", autotip:true, submitonce:true});
        $("#version").formValidator({onshow:"请输入软件版本",onfocus:"请输入软件版本",oncorrect:"输入正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"软件版本不能为空"});
    })
</script>
<div class="pad-10">
    <div class="content-menu line-x blue"><{$topnav}></div>
    <div class="table-form">
        <form action="" method="post" id="form1">
            <input name="data[vid]" type="hidden" class="input-text" id="vid" value="<{$vid}>" />
            <input name="sort_id" type="hidden" class="input-text" id="sort_id" value="<{$sort_id}>" />
            <table width="100%">		
                 <tr>
                    <th>查询类型</th>
                    <td colspan="3">
                        <input name="data[qu_type]" type="radio" value="1" checked="checked" /> 
					分钟&nbsp;
					<input name="data[qu_type]" onchange="frequencyA()" type="radio" value="2" <if condition="$info.qu_type eq 2"> checked="checked"</if> /> 
					小时&nbsp;
					<input name="data[qu_type]" onchange="frequencyA()" type="radio" value="3" <if condition="$info.qu_type eq 3"> checked="checked"</if> /> 
					天
                                        <input name="data[qu_type]" onchange="frequencyA()" type="radio" value="4" <if condition="$info.qu_type eq 4"> checked="checked"</if> /> 
					周
                                        <input name="data[qu_type]" onchange="frequencyA()" type="radio" value="5" <if condition="$info.qu_type eq 5"> checked="checked"</if> /> 
					月
                                        <input name="data[qu_type]" onchange="frequencyA()"  type="radio" value="6" <if condition="$info.qu_type eq 6"> checked="checked"</if> /> 
				        其他
                        </td>
                </tr>
                 <tr>
                    <th  width="8%">查询次数</th>
                    <td  width="5%"><input  type="text"  name="data[qu_num]" id="qu_num"  onchange="frequency()" value="<{$info.qu_num}>"   class="input-text" /></td>
                    <td  width="5%">频率:</td>
                    <td width="80%"><input    type="text"  name="data[qu_frequency]" id="qu_frequency"  readOnly="true" value="<{$info.qu_frequency}>"   class="input-text" /></td>
                </tr>
                 <tr id="frequency"  style='display:<if condition="$info.qu_type eq 6">none</if>'>
                    <th>数据库类型</th>
                    <td colspan="3"><input name="data[db_type]" type="radio" value="1"  checked="checked"/> 
					Mysql&nbsp;
					<input name="data[db_type]" type="radio" value="2" <if condition="$info.db_type eq 2"> checked="checked"</if> /> 
					sql server&nbsp;
					<input name="data[db_type]" type="radio" value="3" <if condition="$info.db_type eq 3"> checked="checked"</if> /> 
					oracle
                                        <input name="data[db_type]" type="radio" value="4" <if condition="$info.db_type eq 4"> checked="checked"</if> /> 
					InterBase
                                      </td>
                </tr>
                 <tr>
                    <th>数据库名</th>
                    <td colspan="3"><input type="text" name="data[db_name]" id="db_name" value="<{$info.db_name}>"  class="input-text" /></td>
                </tr>
               <tr>
                    <th>数据库用户名</th>
                    <td colspan="3"><input type="text" name="data[db_username]" id="db_username" value="<{$info.db_username}>" class="input-text" /></td>
                </tr>
                   <tr>
                    <th>数据库密码</th>
                    <td colspan="3"><input type="text" name="data[db_pwd]" id="db_pwd" value="<{$info.db_pwd}>"  class="input-text" /></td>
                </tr>
                 <tr>
                    <th>数据库地址</th>
                    <td colspan="3"><input type="text" name="data[db_address]" id="db_address" value="<{$info.db_address}>"  class="input-text" /></td>
                </tr>
                  <tr>
                    <th>数据库查询SQL</th>
                    <td colspan="3"><textarea rows="10" cols="90" name="data[db_sql]" id="db_sql" style="width:80%;height:80px;"><{$info.db_sql}></textarea></td>
                </tr>
                  <tr>
                    <th>状态</th>
                    <td colspan="3">
                        <input name="data[status]" type="radio" value="1" <if condition="$info.status eq 1"> checked="checked"</if> /> 
					开启&nbsp;
					<input name="data[status]" type="radio" value="0" <if condition="$info.status eq 0"> checked="checked"</if> /> 
					禁用&nbsp;
                                        <input name="data[status]" type="radio" value="2" <if condition="$info.status eq 2"> checked="checked"</if> /> 
                默认&nbsp;
                    </td>
                </tr>
            </table>
            <div class="btn">
              <input name="data[uid]" type="hidden" value="<{$info.uid}>" />
                <input name="do" type="hidden" value="dosubmit" />
                <input type="submit" class="button" name="dosubmit" value="确定" id="dosubmit" />&nbsp;&nbsp;<input type="button" class="button" name="goback" value="返回上页" onclick="javascript:window.history.back(-1);" />
            </div>
        </form>
    </div>
</div>

<include file="admin@footer" />

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
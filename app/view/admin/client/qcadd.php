<include file="admin@header" />
<div class="pad-10">
    <div class="content-menu line-x blue"><{$topnav}></div>
    <div class="table-form">
       <form action="" method="post" id="form1">
        <input name="data[cid]" type="hidden" class="input-text"  value="<{$cid}>" /> 
        <table width="100%">
             <tr>
                <th width="8%"><b>查询类型:</b></th>
                <td width="80%" colspan="3"> <input name="data[qu_type]" onchange="frequencyA()"  type="radio" value="1" checked="checked"/> 
					分钟&nbsp;
					<input name="data[qu_type]" onchange="frequencyA()" type="radio" value="2" <if condition="$info.qu_type eq 2"> checked="checked"</if> /> 
					小时&nbsp;
					<input name="data[qu_type]" onchange="frequencyA()" type="radio" value="3" <if condition="$info.qu_type eq 3"> checked="checked"</if> /> 
					天
                                        <input name="data[qu_type]" onchange="frequencyA()" type="radio" value="4" <if condition="$info.qu_type eq 4"> checked="checked"</if> /> 
					周
                                        <input name="data[qu_type]" onchange="frequencyA()"  type="radio" value="5" <if condition="$info.qu_type eq 5"> checked="checked"</if> /> 
					月
                                        <input name="data[qu_type]" onchange="frequencyA()"  type="radio" value="6" <if condition="$info.qu_type eq 6"> checked="checked"</if> /> 
				        其他
                  </td>
        
            </tr>
                <tr id="frequency" style='display:<if condition="$info.qu_type eq 6">none</if>'>
                <td width="5%"><b>查询次数:</b></td>
                <td width="5%"><b><input  type="text"  name="data[qu_num]" id="qu_num"  onchange="frequency()" value="<{$info.qu_num}>"   class="input-text" /></b></td>
                <td width="5%">频率:</td>
                <td width="88%"><input    type="text"  name="data[qu_frequency]" id="qu_frequency"  readOnly="true" value="<{$info.qu_frequency}>"   class="input-text" /></td>
                 
            </tr>
            <tr>
                <th width="8%"><b>数据库类型:</b></th>
                <td width="80%" colspan="3">
                    <input name="data[db_type]" type="radio" value="1" checked="checked"/> 
                    mysql&nbsp;
            <input name="data[db_type]" type="radio" value="2" <if condition="$info.db_type eq 2"> checked="checked"</if> /> 
            sql server &nbsp;
            &nbsp;
            <input name="data[db_type]" type="radio" value="3" <if condition="$info.db_type eq 3"> checked="checked"</if> /> 
            oracle&nbsp;
            &nbsp;
            <input name="data[db_type]" type="radio" value="4" <if condition="$info.db_type eq 4"> checked="checked"</if> /> 
            InterBase&nbsp;
            &nbsp;
            </td>
            </tr>
            <tr>
                <th width="8%"><b>数据库名:</b></th>
                <td width="80%" colspan="3"><input name="data[db_name]" type="text" class="input-text" id="db_name" value="<{$info.db_name}>" /></td>
            </tr>
            <tr>
                <th width="8%"><b>数据库用户名:</b></th>
                <td width="80%" colspan="3"><input name="data[db_username]" type="text" class="input-text" id="db_username" value="<{$info.db_username}>" /></td>
            </tr>

            <tr>
                <th width="8%"><b>数据库密码:</b></th>
                <td width="80%" colspan="3"><input name="data[db_pwd]" type="text" class="input-text" id="db_pwd" value="<{$info.db_pwd}>" /></td>
            </tr>
            <tr>
                <th width="8%"><b>数据库地址:</b></th>
                <td width="80%" colspan="3"><input name="data[db_address]" type="text" class="input-text" id="db_address" value="<{$info.db_address}>" /></td>
            </tr>
            <tr>
                <th width="8%"><b>查询SQL:</b></th>
                <td width="80%" colspan="3"><textarea rows="10" cols="90" name="data[db_sql]" id="db_sql" style="width:80%;height:80px;"><{$info.db_sql}></textarea></td>
            </tr>
        </table>
        <div class="btn">
          <input name="do" type="hidden" value="dosubmit" />
            <input type="submit" class="button" name="dosubmit" value="确定" id="dosubmit" />&nbsp;&nbsp;
            <input type="button" class="button" name="goback" value="返回上页" onclick="javascript:window.history.back(-1);" />
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
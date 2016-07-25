<include file="admin@header" />
<script type="text/javascript">
    $(function(){
        $.formValidator.initConfig({formid:"form1", autotip:true, submitonce:true});
        $("#title").formValidator({onshow:"请输入激活码标题",onfocus:"请输入激活码标题",oncorrect:"输入正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"激活码标题不能为空"});
    })
</script>
<div class="pad-10">
    <div class="content-menu line-x blue"><{$topnav}></div>
    <div class="table-form">
        <form action="" method="post" id="form1">
            <input  type="hidden" name="id" value="<{$id}>" />
            <input  type="hidden" name="bid" value="<{$bid}>" />
            <input  type="hidden" name="code" value="<{$info.code}>" />
            <table width="100%">
                <tr>
                    <th width="8%">激活码</th>
                    <td width="80%"><{$info.code}></td>
                </tr>
                <tr>
                    <th width="8%">开始时间</th>
                    <td width="80%"><{$info.start_time}></td>
                </tr>
                  <tr>
                    <th width="8%">结束时间</th>
                    <td width="80%"><{$info.end_time}></td>
                </tr>
                <tr>
                    <th>是否制卡</th>
                    <td><input name="data[cstatus]" type="radio" value="1"<if condition="$info.cstatus eq 1"> checked="checked"</if>/> 
                       已制卡&nbsp;
                <input name="data[cstatus]" type="radio" value="0" <if condition="$info.cstatus eq 0"> checked="checked"</if> /> 
                未制卡&nbsp;</td>
                </tr>
                <tr>
                    <th>是否出售</th>
                    <td><input name="data[sstatus]" type="radio" value="1"<if condition="$info.sstatus eq 1"> checked="checked"</if>/> 
                       已出售&nbsp;
                <input name="data[sstatus]" type="radio" value="0" <if condition="$info.sstatus eq 0"> checked="checked"</if> /> 
                未出售&nbsp;</td>
                </tr>
                
            </table>
            <div class="btn">
                <input name="do" type="hidden" value="dosubmit" />
                <input type="submit" class="button" name="dosubmit" value="确定" id="dosubmit" />&nbsp;&nbsp;<input type="button" class="button" name="goback" value="返回上页" onclick="javascript:window.history.back(-1);" />
            </div>
        </form>
    </div>
</div>

<include file="admin@footer" />
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
            <input name="data[sort_id]" type="hidden" class="input-text" id="sort_id" value="<{$sort_id}>" />
            <table width="100%">		
              <!--  <tr>
                    <th>软件ID</th>
                    <td><input name="data[sort_id]" type="text" class="input-text" id="sort_id" value="<{$info.sort_id}>" /></td>
                </tr>-->
                
                 <tr>
                    <th>版本名称</th>
                    <td><input name="data[version]" type="text" class="input-text" id="version" value="<{$info.version}>" /></td>
                </tr>
                <tr>
                    <th>下载地址</th>
                    <td><input name="data[download]" type="text" class="input-text" id="download" value="<{$info.download}>" /></td>
                </tr>
                <tr>
                    <th>版本描叙</th>
                    <td><textarea rows="10" cols="90" name="data[description]" id="description" style="width:80%;height:80px;"><{$info.description}></textarea></td>
                </tr>
                  <tr>
                    <th>状态</th>
                    <td>
                        <input name="data[status]" type="radio" value="1" <if condition="$info.status eq 1"> checked="checked"</if> /> 
					开启&nbsp;
					<input name="data[status]" type="radio" value="0" <if condition="$info.status eq 0"> checked="checked"</if> /> 
					禁用&nbsp;
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
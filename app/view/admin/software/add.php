<include file="admin@header" />
<script type="text/javascript">
    $(function(){
        $.formValidator.initConfig({formid:"form1", autotip:true, submitonce:true});
        $("#name").formValidator({onshow:"请输入软件名称",onfocus:"请输入软件名称",oncorrect:"输入正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"软件名称不能为空"});
        $("#company").formValidator({onshow:"请输入软件厂商",onfocus:"请输入软件厂商",oncorrect:"输入正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"软件厂商不能为空"});
      //  $("#contact_phone").formValidator({onshow:"请输入联系人电话",onfocus:"请输入联系人电话",oncorrect:"输入正确"}).regexValidator({regexp:"mobile",datatype:"enum",onerror:"联系人电话错误"});
    })
</script>
<div class="pad-10">
    <div class="content-menu line-x blue"><{$topnav}></div>
    <div class="table-form">
        <form action="" method="post" id="form1">
            <table width="100%">
                 <tr>
                    <th>软件名称</th>
                    <td><input name="data[name]" type="text" class="input-text" id="name" value="<{$info.name}>" /></td>
                </tr>
                 <tr>
                    <th>软件厂商</th>
                    <td><input name="data[company]" type="text" class="input-text" id="company" value="<{$info.company}>" /></td>
                </tr>
                 <tr>
                    <th>软件官网</th>
                    <td><input name="data[url]" type="text" class="input-text" id="url" value="<{$info.url}>" /></td>
                </tr>
                <tr>
                    <th>联系人</th>
                    <td><input name="data[contact_name]" type="text" class="input-text" id="contact_name" value="<{$info.contact_name}>" /></td>
                </tr>
                  <tr>
                    <th>电话</th>
                    <td><input name="data[contact_phone]" type="text" class="input-text" id="contact_phone" value="<{$info.contact_phone}>" /></td>
                </tr>
                <tr>
                    <th>软件描叙</th>
                    <td><textarea rows="10" cols="90" name="data[description]" id="description" style="width:80%;height:80px;"><{$info.description}></textarea></td>
                </tr>
                  <tr>
                    <th>状态</th>
                    <td>
                        <input name="data[status]" type="radio" value="1" checked="checked"/> 
					开启&nbsp;
					<input name="data[status]" type="radio" value="0" <if condition="$info.status eq 0"> checked="checked"</if> /> 
					禁用&nbsp;
                    </td>
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
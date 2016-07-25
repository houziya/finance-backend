<include file="admin@header" />
<script type="text/javascript">
    $(function(){
        $.formValidator.initConfig({formid:"form1", autotip:true, submitonce:true});
        $("#type").formValidator({onshow:"请输入操作标题",onfocus:"请输入激活码标题",oncorrect:"输入正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"操作标题不能为空"});
      //  $("#company").formValidator({onshow:"请输入软件厂商",onfocus:"请输入软件厂商",oncorrect:"输入正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"软件厂商不能为空"});
      //  $("#contact_phone").formValidator({onshow:"请输入联系人电话",onfocus:"请输入联系人电话",oncorrect:"输入正确"}).regexValidator({regexp:"mobile",datatype:"enum",onerror:"联系人电话错误"});
    })
</script>
<div class="pad-10">
    <div class="content-menu line-x blue"><{$topnav}></div>
    <div class="table-form">
        <form action="" method="post" id="form1">
            <table width="100%">
                 <tr>
                    <th width="8%">操作名称</th>
                    <td width="80%"><input name="data[type]" type="text" class="input-text" id="type" value="<{$info.type}>" /></td>
                </tr>
                 <tr>
                    <th width="8%">动作</th>
                    <td width="80%"><input name="data[action]" type="text" class="input-text" id="action" value="<{$info.action}>" /></td>
                </tr>
                 <tr>
                    <th>回调ID</th>
                    <td><input name="data[cid]" type="text" class="input-text" id="cid" value="<{$info.cid}>" /></td>
                </tr>
                <tr>
                    <th>附件地址</th>
                    <td><input name="data[attach_url]" type="text"  size="50" class="input-text" id="attach_url" value="<{$info.attach_url}>" /></td>
                </tr>
                 <tr>
                    <th>签名字段</th>
                    <td><input name="data[field]" type="text"  size="50" class="input-text" id="field" value="<{$info.field}>" /></td>
                </tr>
                <tr>
                    <th>状态</th>
                    <td>
                       <input name="data[status]" type="radio" value="1"<if condition="$info.status eq 1"> checked="checked"</if>/> 
                       启用&nbsp;
                <input name="data[status]" type="radio" value="0" <if condition="$info.status eq 0"> checked="checked"</if> />
                       关闭&nbsp;
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
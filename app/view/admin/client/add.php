<include file="admin@header" />
<script type="text/javascript">
    $(function(){
        $.formValidator.initConfig({formid:"form1", autotip:true, submitonce:true});
        $("#title").formValidator({onshow:"请输入激活码标题",onfocus:"请输入激活码标题",oncorrect:"输入正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"激活码标题不能为空"});
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
                    <th width="8%">标题</th>
                    <td width="80%"><input name="data[title]" type="text" class="input-text" id="title" value="<{$info.title}>" /></td>
                </tr>
                 <tr>
                    <th width="8%">软件</th>
                    <td width="80%"><select name="sort_id" id="sort_id">
					<option value="0">选择软件</option>
					<{$sort_id_select}>
				</select></td>
                </tr>
                 <tr>
                    <th>开始时间</th>
                    <td><{$info.start_time}></td>
                </tr>
                 <tr>
                    <th>结束时间</th>
                    <td><{$info.end_time}></td>
                </tr>
                <tr>
                    <th>生成张数</th>
                    <td><input name="data[num]" type="text" class="input-text" id="num" value="<{$info.num}>" /></td>
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
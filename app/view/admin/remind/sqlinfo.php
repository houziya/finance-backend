<include file="admin@header" />

<script type="text/javascript">
$(function(){
	$.formValidator.initConfig({formid:"form1", autotip:true, submitonce:true});
	$("#name").formValidator({onshow:"请输入提醒名称",onfocus:"请输入提醒名称",oncorrect:"输入正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"提醒名称不能为空"});
	$("#en_name").formValidator({onshow:"请输入标识符",onfocus:"请输入标识符",oncorrect:"输入正确"}).regexValidator({
		regexp:"^[a-z0-9_]+$",datatype:"string",onerror:"标识符只能为字母、数字、下划线组成"
	}).ajaxValidator({
		type : "get",
		url : "<{'/ajaxchkenname'|url}>",
		async:'true',
		success : function(data){
            if( data == "0" ){
                return true;
			}else{
                return false;
			}
		},
		buttons: $("#dosubmit"),
		onerror : "标识符已存在",
		onwait : "请稍候..."	
	});
})
</script>
<div class="pad-10">
	<div class="content-menu line-x blue"><{$topnav}></div>
	<div class="table-form">
		<form action="" method="post" id="form1">
		<table width="100%">
			<tr>
				<th width="120">名称</th>
				<td><{$info.name}></td>
			</tr>
			<tr>
				<th>SQL</th>
				<td><{$info.sql}> <br /><strong>共 <{$sql_count}> 条数据</strong></td>
			</tr>
			<tr>
				<th>描述</th>
				<td><{$info.content}></td>
			</tr>
			<tr>
				<th>是否站内信</th>
				<td><input name="data[enable_message]" type="radio" value="1" <if condition="$info['enable_message'] eq 1"> checked="checked"</if> /> 
					是
					<input name="data[enable_message]" type="radio" value="0" <if condition="$info['enable_message'] eq 0"> checked="checked"</if> />
					否</td>
			</tr>
			<tr>
				<th>站内信模版</th>
				<td><span class="blue"><{$info.tpl_message_title}></span><br /><{$info.tpl_message}></td>
			</tr>
			<tr>
				<th>是否邮件</th>
				<td><input name="data[enable_email]" type="radio" value="1" <if condition="$info['enable_email'] eq 1"> checked="checked"</if> /> 
					是
					<input name="data[enable_email]" type="radio" value="0" <if condition="$info['enable_email'] eq 0"> checked="checked"</if> />
					否</td>
			</tr>
			<tr>
				<th>邮件模版</th>
				<td><span class="blue"><{$info.tpl_email_title}></span><br /><{$info.tpl_email}></td>
			</tr>
			<tr>
				<th>是否短信</th>
				<td><input name="data[enable_mobile]" type="radio" value="1" <if condition="$info['enable_mobile'] eq 1"> checked="checked"</if> /> 
					是
					<input name="data[enable_mobile]" type="radio" value="0" <if condition="$info['enable_mobile'] eq 0"> checked="checked"</if> />
					否</td>
			</tr>
			<tr>
				<th>短信模版</th>
				<td><{$info.tpl_mobile}></td>
			</tr>
			<tr>
				<th>免打扰</th>
				<td><input name="data[is_work]" type="radio" value="1" /> 
					是 
					<input name="data[is_work]" type="radio" value="0" checked="checked" />
					否</td>
			</tr>
		</table>
		<div class="btn">
			<input name="id" type="hidden" value="<{$info.id}>" />
			<input name="do" type="hidden" value="dosubmit" />
			<input type="submit" class="button" name="dosubmit" value="开始发送" />&nbsp;&nbsp;<input type="button" class="button" name="goback" value="返回上页" onclick="javascript:window.history.back(-1);" />
		</div>
		</form>
	</div>
</div>

<include file="admin@footer" />
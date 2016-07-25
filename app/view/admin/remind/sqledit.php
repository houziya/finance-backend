<include file="admin@header" />

<script type="text/javascript">
$(function(){
	$.formValidator.initConfig({formid:"form1", autotip:true, submitonce:true});
	$("#name").formValidator({onshow:"请输入提醒名称",onfocus:"请输入提醒名称",oncorrect:"输入正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"提醒名称不能为空"});
	$("#en_name").formValidator({onshow:"请输入标识符",onfocus:"请输入标识符",oncorrect:"输入正确"}).regexValidator({
		regexp:"^[a-z0-9_]+$",datatype:"string",onerror:"标识符只能为字母、数字、下划线组成"
	}).ajaxValidator({
		type : "get",
		url : "<{url:('ajaxchkenname')}>",
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
				<td><input name="data[name]" type="text" class="input-text" id="name" value="<{$info.name}>" /></td>
			</tr>			
			<tr>
				<th>模版全局变量</th>
				<td>网站名：<span class="red">{$sys_name}</span>|<{$sys_name}><br />客服电话：<span class="red">{$sys_mobile}</span>|<{$sys_mobile}><br />网站邮箱：<span class="red">{$sys_email}</span>|<{$sys_email}><br />系统时间1：<span class="red">{$sys_time}</span>|<{$sys_time}><br />系统时间2：<span class="red">{$sys_time2}</span>|<{$sys_time2}><br />系统时间3：<span class="red">{$sys_time3}</span>|<{$sys_time3}><br />系统时间4：<span class="red">{$sys_time4}</span>|<{$sys_time4}><br />网站LOGO：<span class="red">{$sys_logo}</span>|<{$sys_logo}><br />用户中心：<span class="red">{$sys_user}</span>|<{$sys_user}><br />联系我们：<span class="red">{$sys_contactus}</span>|<{$sys_contactus}></td>
			</tr>
			<tr>
				<th>SQL</th>
				<td><textarea name="data[sql]" style="width:90%; height:60px"><{$info.sql}></textarea><br /><strong>注意：SQL里面必须包含uid字段</strong></td>
			</tr>
			<tr style="background:#E8E8E8">
				<th>站内信提醒</th>
				<td><input name="data[enable_message]" type="radio" value="1" <if condition="$info['enable_message'] eq 1"> checked="checked"</if> /> 
					开启 
						<input name="data[enable_message]" type="radio" value="0" <if condition="$info['enable_message'] eq 0"> checked="checked"</if> />
				关闭</td>
			</tr>
			<tr style="background:#E8E8E8">
				<th>站内信标题模版</th>
				<td><input name="data[tpl_message_title]" type="text" class="input-text" style="width:80%" value="<{$info.tpl_message_title}>" /></td>
			</tr>
			<tr style="background:#E8E8E8">
				<th>站内信内容模版</th>
				<td><textarea name="data[tpl_message]" style="width:90%; height:60px"><{$info.tpl_message}></textarea></td>
			</tr>
			<tr style="background:#FFDFE7">
				<th>邮件提醒</th>
				<td><input name="data[enable_email]" type="radio" value="1" <if condition="$info['enable_email'] eq 1"> checked="checked"</if> /> 
					开启 
						<input name="data[enable_email]" type="radio" value="0" <if condition="$info['enable_email'] eq 0"> checked="checked"</if> />
				关闭</td>
			</tr>
			<tr style="background:#FFDFE7">
				<th>邮件标题模版</th>
				<td><input name="data[tpl_email_title]" type="text" class="input-text" style="width:80%" value="<{$info.tpl_email_title}>" /></td>
			</tr>
			<tr style="background:#FFDFE7">
				<th>邮件内容模版</th>
				<td><textarea name="data[tpl_email]" style="width:90%; height:200px"><{$info.tpl_email}></textarea></td>
			</tr>
			<tr style="background:#D8FBFE">
				<th>手机提醒</th>
				<td><input name="data[enable_mobile]" type="radio" value="1" <if condition="$info['enable_mobile'] eq 1"> checked="checked"</if> /> 
					开启 
						<input name="data[enable_mobile]" type="radio" value="0" <if condition="$info['enable_mobile'] eq 0"> checked="checked"</if> />
				关闭</td>
			</tr>
			<tr style="background:#D8FBFE">
				<th>短信模版</th>
				<td><textarea name="data[tpl_mobile]" style="width:90%; height:60px"><{$info.tpl_mobile}></textarea></td>
			</tr>
			<tr>
				<th>描述</th>
				<td><textarea name="data[content]" style="width:90%; height:60px"><{$info.content}></textarea></td>
			</tr>
			<tr>
				<th>状态</th>
				<td><input name="data[status]" type="radio" value="1" <if condition="$info['status'] eq 1"> checked="checked"</if> /> 
					正常 
					<input name="data[status]" type="radio" value="0" <if condition="$info['status'] eq 0"> checked="checked"</if> />
					待审</td>
			</tr>
		</table>
		<div class="btn">
			<input name="id" type="hidden" value="<{$info.id}>" />
			<input name="do" type="hidden" value="dosubmit" />
			<input type="submit" class="button" name="dosubmit" value="确定" />&nbsp;&nbsp;<input type="button" class="button" name="goback" value="返回上页" onclick="javascript:window.history.back(-1);" />
		</div>
		</form>
	</div>
</div>

<include file="admin@footer" />
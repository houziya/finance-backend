<include file="admin@header" />

<script type="text/javascript">
$(document).ready(function(){
	$.formValidator.initConfig({ submitonce:true,formid:"form1",autotip:true});
	$("#mobile").formValidator({onshow:"请输入手机号码",onfocus:"请输入手机号码",oncorrect:"输入正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"请输入手机号码"});
});

</script>
<div class="pad-10">
	<div class="content-menu line-x blue"><{$topnav}></div>
	<div class="table-form">
		<form action="" method="post" id="form1">
		<table width="100%">
			<tr>
				<th>手机号码</th>
				<td>
					<input name="data[mobile]" type="text" class="input-text" id="mobile" />
				</td>
			</tr>
			<tr>
				<th width="120">短信服务商</th>
				<td>
				<select name="data[server]" id="server">
						<option value="1" selected="">亿美短信</option>
						<option value="2">容联云通讯</option>
				</select>
				
				</td>
			</tr>
			
			
			
			<tr>
				<th>短信内容</th>
				<td>
					<textarea name="data[content]" id="content" rows="5" cols="30" class="text"></textarea>
				</td>
			</tr>
			
		</table>
		<div class="btn">
			<input name="do" type="hidden" value="dosubmit" />
			<input type="submit" class="button" name="dosubmit" value="确定" />&nbsp;&nbsp;<input type="button" class="button" name="goback" value="返回上页" onclick="javascript:window.history.back(-1);" />
		</div>
		</form>
	</div>
</div>

<include file="admin@footer" />
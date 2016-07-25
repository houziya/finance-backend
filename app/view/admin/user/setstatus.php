<include file="admin@header" />
<script type="text/javascript">
$(function(){
	$.formValidator.initConfig({formid:"form1", autotip:true, submitonce:true});
	$("#remark").formValidator({onshow:"请输入审核内容",onfocus:"请输入审核内容",oncorrect:"输入正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"审核内容不能为空"});
})
</script>
<div class="pad-10">
	<div class="content-menu line-x blue"><{$topnav}></div>
	
	<include file="admin@user/identitycommoninfo" />
	
	<?php if($indentity_info['is_idcard'] > 0 && $indentity_info['is_idcard'] < 3): ?>
	<div class="table-form">
		<form name="form1" action="_SELF_" method="post" id="form1">
		<fieldset class="mar-b10">
			<legend>审核处理</legend>
			<table width="100%">
				<tr>
					<th>审核备注</th>
					<td><textarea rows="10" name="remark" style="width:80%;height:60px;" id="remark"><if condition="$checkLogInfo['remark'] neq ''"><{$checkLogInfo['remark']}></if></textarea></td>
				</tr>
				<tr>
					<th>审核状态</th>
					<td><label><input type="radio" name="is_idcard" value="-1" <{:radio($indentity_info['is_idcard'],-1)}>/>
						重新进入待审（驳回重审）</label>&nbsp;&nbsp;&nbsp;&nbsp;
						<label><input name="is_idcard" type="radio" value="1"  <{:radio($indentity_info['is_idcard'],1)}>/>
							待审核
						</label>&nbsp;&nbsp;&nbsp;&nbsp;
						<label><input type="radio" name="is_idcard" value="2"  <{:radio($indentity_info['is_idcard'],2)}>/>
						审核通过</label>&nbsp;&nbsp;&nbsp;&nbsp;
						
					</td>
				</tr>
			</table>
			<div class="btn">
				<input type="hidden" name="id" id="id" value="<{$indentity_info.id}>" />
				<input type="hidden" name="do" value="dosubmit" /> 
				<input type="submit" class="button" name="verify" value="确定提交" />&nbsp;&nbsp;<input type="button" class="button" name="goback" value="返回上页" onclick="javascript:window.history.back(-1);" />
			</div>
		</fieldset>
		</form>
	</div>
	<?php endif;?>
	
</div>
<include file="admin@footer" />
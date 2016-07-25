<include file="admin@header" />
<script type="text/javascript">
//得到多选框ID集合
function get_ids() {
	var ids = '';
	$("input[name='ids[]']").each(function() {
		if($(this).attr('checked')=='checked') {
			ids += '|'+$(this).val();
		};
	});
	if(ids!=''){
		return ids.substring(1);
	}else{
		return '';
	}
}

$(function(){
	//批量删除
	$("#btn_del").click(function(){
		var ids = get_ids();
		if(ids==''){
			alert('请选择待删除的会员身份验证记录');
			return false;
		}
		if(!confirm('将会删除对应的会员身份验证记录信息！\n\n请确认是否删除？')){
			return false;
		}
		$("input[name='do']").val('dosubmit');
		$('#myform').attr('action','<{:url("useridentitydelete?do=all")}>');
		$('#myform').submit();
	});

});
</script>
<div class="pad-10">
<!--<div class="content-menu line-x blue"><{$topnav}></div>-->
	<div class="explain-col mar-b8" id="search_form">
		<form name="search_form" action="" method="post" id="search_form">
		&nbsp;	用户uid：
		<input name="search[uid]" type="text" class="input-text" value="<{$uid}>" size="8" />
		&nbsp;	真实姓名：
		<input name="search[person_name]" type="text" class="input-text" value="<{$person_name}>" size="20" />
        &nbsp;	身份证号：
        <input name="search[person_cardid]" type="text" class="input-text" value="<{$person_cardid}>" size="20" />
        &nbsp;	手机号：
        <input name="search[person_mobile]" type="text" class="input-text" value="<{$person_mobile}>" size="20" />
        &nbsp;  机构领投状态
        <{$company_select}>
        &nbsp;  明星领投状态
        <{$person_select}>
        <br />&nbsp;	公司名称：
        <input name="search[company_name]" type="text" class="input-text" value="<{$company_name}>" size="20" />
        &nbsp;	营业执照编号：
        <input name="search[company_business_licence]" type="text" class="input-text" value="<{$company_business_licence}>" size="20" />
		&nbsp;时间：
		<{$input_starttime}>
		 -&nbsp;
		<{$input_endtime}>
        &nbsp;	IP：
		<input name="search[ip]" type="text" class="input-text" value="<{$ip}>" size="10" />
		<input type="submit" name="dosubmit" class="button" value="搜索" />
		</form>
	</div>
	<div class="table-list">
	<form name="myform" action="#" method="post" id="myform">
		<table width="100%" cellspacing="0">
			<tr>
				<th width="5%">ID</th>
				<th width="8%">用户ID</th>
                <th width="10%">用户名</th>
				<th width="10%">机构领投审核状态</th>
				<th width="10%">明星领投审核状态</th>
				<th width="12%">添加时间/IP</th>
				<th width="20%">管理操作</th>
			</tr>
			<foreach from="lists" item="v">
			<tr>
				<td><{$v.id}></td>	
				<td><{$v.uid}></td>
                <td><{$v.username}></td>
                <td><{$v.company_status}></td>
				<td><{$v.person_status}></td>
				<td><{$v.add_time}><br /><{$v.ip}></td>
				<td>
					<a onclick="opendialog('<{:url('user/authentication?uid='.$v['uid'])}>/types/1','审核处理',0,'info','90%','90%');" title="审核处理" alt="审核处理">审核处理</a>
				</td>
			</tr>
			</foreach>
		</table>
		<div class="btn">
	    	<!--<label for="check_box" style="cursor:pointer;">全选/取消</label>
			<input name="do" type="hidden" value="" />
			<input type="button" class="button" name="btn_del" id="btn_del" value="批量删除" />
		--></div>
		</form>
	</div>
	<div class="pages"><{$pages}></div>
</div>

<include file="admin@footer" />
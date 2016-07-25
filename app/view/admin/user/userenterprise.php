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
		&nbsp;	企业名称：
		<input name="search[enterprise_name]" type="text" class="input-text" value="<{$enterprise_name}>" size="8" />
		&nbsp;	企业联系人：
		<input name="search[contact]" type="text" class="input-text" value="<{$contact}>" size="20" />
        &nbsp;	法人姓名：
        <input name="search[legal]" type="text" class="input-text" value="<{$legal}>" size="20" />
        &nbsp;	法人身份证号：
        <input name="search[legal_id_no]" type="text" class="input-text" value="<{$legal_id_no}>" size="20" />
        &nbsp;	组织机构代码：
        <input name="search[org_no]" type="text" class="input-text" value="<{$org_no}>" size="20" />
        &nbsp;	营业执照编号：
        <input name="search[business_license]" type="text" class="input-text" value="<{$business_license}>" size="20" />
        &nbsp;	税务登记号：
        <input name="search[tax_no]" type="text" class="input-text" value="<{$tax_no}>" size="20" />
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
				<th width="10%">企业名称</th>
				<th width="10%">企业联系人</th>
                <th width="10%">法人姓名/身份证</th>
                <th width="10%">开户银行许可证<br />组织机构代码<br />营业执照编号<br />税务登记号</th>
                <!--<th width="10%">组织机构代码</th>
                <th width="10%">营业执照编号</th>
                <th width="10%">税务登记号</th>-->
                <th width="10%">类型</th>
				<th width="6%">审核状态</th>
				<th width="12%">添加时间/IP</th>
				<th width="10%">审核时间</th>
				<th width="20%">管理操作</th>
			</tr>
			<foreach from="lists" item="v">
			<tr>
				<td><{$v.id}></td>	
				<td><a href="javascript:;" onclick="opendialog('<{:url('user/info?uid='.$v['uid'])}>','用户详情','','userinfo','100%');"><{$v.uid}></a></td>
				<td><{$v.enterprise_name}></td>
                <td><{$v.contact}></td>
                <td><{$v.legal}><br /><{$v.legal_id_no}></td>
                <td><{$v.bank_license}><br /><{$v.org_no}><br /><{$v.business_license}><br /><{$v.tax_no}></td>
                <td><{$v.type_tips}></td>
				<td><{$v.enterprise_tips}></td>
				<td><{$v.add_time}><br /><{$v.ip}></td>
				<td><?php if($v['check_time']):?><{$v.check_time}><?php else:?>--<?php endif;?></td>
				<td>
					<?php if(($v['is_enterprise'] > '-2') && ($v['is_enterprise'] != 3)):?>
					<a href="<{:url('user/setenterprisestatus?id='.$v['id'])}>" title="审核处理" alt="审核处理">审核处理</a>
					<?php else:?>
					<a href="<{:url('user/setenterprisestatus?id='.$v['id'])}>" title="审核处理" alt="审核处理">详情</a>
					<?php endif;?>
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
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
			alert('请选择待删除的会员收货地址');
			return false;
		}
		if(!confirm('将会删除对应的会员收货地址信息！\n\n请确认是否删除？')){
			return false;
		}
		$("input[name='do']").val('dosubmit');
		$('#myform').attr('action','<{:url("deliveryaddressDelete?do=all")}>');
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
		&nbsp;	用户名：
		<input name="search[username]" type="text" class="input-text" value="<{$username}>" size="8" />
		&nbsp;	手机号：
		<input name="search[phone]" type="text" class="input-text" value="<{$phone}>" size="8" />
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
				<th width="3%"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
				<th width="5%">ID</th>
				<th width="8%">用户ID</th>
				<th width="8%">用户名</th>
				<th width="6%">电话</th>
				<th width="6%">手机号</th>
				<th width="6%">收货地址</th>
				<th width="6%">邮政编码</th>
				<th width="12%">时间/IP</th>
				<th width="20%">管理操作</th>
			</tr>
			<foreach from="lists" item="v">
			<tr>
				<td><input name="ids[]" value="<{$v.id}>" type="checkbox" /></td>	
				<td><{$v.id}></td>
				<td><{$v.uid}></td>
				<td><a href="javascript:;" onclick="opendialog('<{:url('user/info?uid='.$v['uid'])}>','用户详情','','userinfo','100%');"><{$v.username}></a></td>
				<td><{$v.tel}></td>
				<td><{$v.mobile}></td>
				<td><{$v.address}></td>
				<td><{$v.zip}></td>
				<td><{$v.add_time}><br /><{$v.ip}></td>
				<td>
					<a href="<{:url('deliveryaddressedit?id='.$v['id'])}>">修改</a> 
					|
					<a href="<{:url('deliveryaddressdelete?id='.$v['id'])}>" class="confirm">删除</a>
				</td>
			</tr>
			</foreach>
		</table>
		<div class="btn">
	    	<label for="check_box" style="cursor:pointer;">全选/取消</label>
			<input name="do" type="hidden" value="" />
			<input type="button" class="button" name="btn_del" id="btn_del" value="批量删除" />
		</div>
		</form>
	</div>
	<div class="pages"><{$pages}></div>
</div>

<include file="admin@footer" />
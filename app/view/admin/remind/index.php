<include file="admin@header" />

<div class="pad-10">
	<div class="content-menu line-x blue"><{$topnav}></div>
	<div class="explain-col mar-b8" id="search_form">
		<form name="search_form" action="" method="post" id="search_form">
		提醒类型：<{$type_select}>
		&nbsp; 前台显示：<{$show_select}>
		&nbsp; 状态：<{$status_select}>
		&nbsp;	站内提醒：<{$message_select}>
		&nbsp;	邮件提醒：<{$email_select}>
		&nbsp;	手机提醒：<{$mobile_select}>
		&nbsp;<input type="submit" name="dosubmit" class="button" value="搜索" />
		</form>
	</div>
	<div class="table-list">		
		<form name="myform" action="" method="post" id="myform">
		<table width="100%" cellspacing="0">
			<tr>
				<th width="4%">ID</th>
				<th>状态</th>
				<th>前台显示</th>
				<th>提醒名称</th>
				<th>标识</th>
				<th>站内信</th>
				<th>邮箱</th>
				<th>手机</th>
				<th>免打扰</th>				
				<th>添加时间</th>
				<th>IP</th>
				<th>管理操作</th>
			</tr>
			<foreach from="lists" item="v">
			<tr>
				<td><{$v.id}></td>
				<td><{$v.status_tips}></td>
				<td><{$v.show_tips}></td>
				<td class="text-l">【<{$v.type_name}>】<{$v.name}></td>
				<td><{$v.en_name}></td>
				<td><{$v.message_tips}></td>
				<td><{$v.email_tips}></td>
				<td><{$v.mobile_tips}></td>
				<td><{$v.work_tips}></td>				
				<td><{$v.add_time}></td>
				<td><{$v.ip}></td>
				<td><a href="<{:url('edit?id='.$v['id'])}>">修改</a> | <a href="<{:url('delete?id='.$v['id'])}>" class="confirm" msg="您确认要删除吗？\n删除后涉及到的提醒标识符将会失效！">删除</a></td>
			</tr>
			</foreach>
		</table>
		</form>
	</div>
	<div class="pages"><{$pages}></div>
</div>

<include file="admin@footer" />
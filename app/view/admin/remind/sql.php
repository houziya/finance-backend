<include file="admin@header" />

<div class="pad-10">
	<div class="content-menu line-x blue"><{$topnav}></div>
	<div class="explain-col mar-b8" id="search_form">
		<form name="search_form" action="" method="post" id="search_form">
		状态：<{$status_select}>
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
				<th width="600">提醒名称</th>
				<th>站内信</th>
				<th>邮箱</th>
				<th>手机</th>
				<th>发送次数</th>
				<th>添加时间</th>
				<th>最后发送</th>
				<th>管理操作</th>
			</tr>
			<foreach from="lists" item="v">
			<tr>
				<td><{$v.id}></td>
				<td><{$v.status_tips}></td>
				<td class="text-l"><{$v.name}><br /><span class="gray4"><{$v.content}><br /><{$v.sql}></span></td>
				<td><{$v.message_tips}></td>
				<td><{$v.email_tips}></td>
				<td><{$v.mobile_tips}></td>
				<td><{$v.num}></td>
				<td><{$v.add_time}></td>
				<td><{$v.update_time}></td>
				<td><a href="<{:url('sqlinfo?id='.$v['id'])}>">开始发送</a> | <a href="<{:url('sqledit?id='.$v['id'])}>">修改</a> | <a href="<{:url('sqldelete?id='.$v['id'])}>" class="confirm" msg="您确认要删除吗？">删除</a></td>
			</tr>
			</foreach>
		</table>
		</form>
	</div>
	<div class="pages"><{$pages}></div>
</div>

<include file="admin@footer" />
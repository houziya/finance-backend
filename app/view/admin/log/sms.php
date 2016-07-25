<include file="admin@header" />

<div class="pad-10">
	<div class="explain-col mar-b8" id="search_form">
		<form name="search_form" action="" method="post" id="search_form">
		&nbsp;	用户uid：
		<input name="search[uid]" type="text" class="input-text" value="<{$uid}>" size="6" />
		&nbsp;	用户名：
		<input name="search[username]" type="text" class="input-text" value="<{$username}>" size="8" />
		&nbsp;	手机：
		<input name="search[mobile]" type="text" class="input-text" value="<{$mobile}>" size="8" />
		&nbsp;	内容：
		<input name="search[content]" type="text" class="input-text" value="<{$content}>" size="8" />
		&nbsp;	状态：
		<{$status_select}>
		&nbsp;	类型：
		<{$remind_select}>
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
		<table width="100%" cellspacing="0">
			<tr>
			    <th width="5%">ID</th>
				<th width="8%">标识</th>
				<th width="8%">用户</th>
				<th width="12%">接收手机</th>
				<th>内容</th>
				<th width="15%">时间</th>
				<th width="6%">IP</th>
				<th width="6%">状态</th>
			</tr>
			<foreach from="lists" item="v">
			<tr>
			    <td><{$v.id}></td>
				<td><{$v.remind_name}></td>
				<td><{$v.username}></td>
				<td><{$v.mobile}></td>
				<td class="text-l"><{$v.content}></td>
				<td><{$v.add_time}></td>
				<td><{$v.ip}></td>
				<td><{$v.status_tips}></td>
			</tr>
			</foreach>
		</table>
	</div>
	<div class="pages"><{$pages}></div>
</div>

<include file="admin@footer" />
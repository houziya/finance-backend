<include file="admin@header" />

<div class="pad-10">
<!--	<div class="content-menu line-x blue"><{$topnav}></div>-->
	<div class="explain-col mar-b8" id="search_form">
		<form name="search_form" action="" method="post" id="search_form">
		&nbsp;	用户名：
		<input name="search[username]" type="text" class="input-text" value="<{$username}>" size="8" />
		&nbsp;	类型：
		<{$type_select}>
		&nbsp;时间：
		<{$input_starttime}>
		 -&nbsp;
		<{$input_endtime}>
        &nbsp;	IP：
		<input name="search[addip]" type="text" class="input-text" value="<{$addip}>" size="10" />
		<input type="submit" name="dosubmit" class="button" value="搜索" />
		</form>
	</div>
	<div class="table-list">
		<table width="100%" cellspacing="0">
			<tr>				
				<th width="5%">ID</th>
				<th>用户名</th>
				<th>登录类型</th>
				<th>时间</th>
				<th>IP</th>
			</tr>
			<foreach from="lists" item="v">
			<tr>	
				<td><{$v.id}></td>
				<td><{$v.admin_tips}> <{$v.username}></td>
				<td><{$v.type_tips}></td>
				<td><{$v.add_time}></td>
				<td><{$v.ip}></td>
			</tr>
			</foreach>
		</table>
	</div>
	<div class="pages"><{$pages}></div>
</div>

<include file="admin@footer" />
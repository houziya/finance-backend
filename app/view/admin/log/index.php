<include file="admin@header" />

<div class="pad-10">
	<div class="content-menu line-x blue"><{$topnav}></div>
	<div class="explain-col mar-b8" id="search_form">
		<form name="search_form" action="" method="post" id="search_form">
		&nbsp;	用户名：
		<input name="search[username]" type="text" class="input-text" value="<{$username}>" size="8" />
		&nbsp;	备注：
		<input name="search[remark]" type="text" class="input-text" value="<{$remark}>" />
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
				<th width="8%">用户名</th>
				<th>URL</th>
				<th>备注</th>
				<th width="15%">时间</th>
				<th width="6%">IP</th>
			</tr>
			<foreach from="lists" item="v">
			<tr>	
				<td><{$v.id}></td>
				<td><{$v.username}></td>
				<td class="text-l"><{$v.url}></td>
				<td class="text-l"><a href="javascript:;" onclick="opendialog('<{:url('info?id='.$v['id'])}>','日志详情',0,'info','95%','520px');"><{$v.remark}></a></td>
				<td><{$v.add_time}></td>
				<td><{$v.ip}></td>
			</tr>
			</foreach>
		</table>
	</div>
	<div class="pages"><{$pages}></div>
</div>

<include file="admin@footer" />
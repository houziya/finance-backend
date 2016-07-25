<include file="admin@header" />

<div class="pad-10">
	<div class="content-menu line-x blue"><{$topnav}></div>
	<div class="table-list">		
		<form name="myform" action="" method="post" id="myform">
		<table width="100%" cellspacing="0">
			<tr>
				<th width="4%">分类ID</th>
				<th>分类名称</th>
				<th width="12%">添加时间</th>
				<th width="10%">IP</th>
				<th width="10%">管理操作</th>
			</tr>
			<foreach from="lists" item="v">
			<tr>
				<td><{$v.id}></td>
				<td><{$v.name}></td>
				<td><{:date('Y-m-d H:i:s',$v['add_time'])}></td>
				<td><{$v.ip}></td>
				<td><a href="<{:url('typeedit?id='.$v['id'])}>">修改</a> | <a href="<{:url('typedelete?id='.$v['id'])}>" class="confirm" msg="您确认要删除吗？">删除</a></td>
			</tr>
			</foreach>
		</table>
		</form>
	</div>
	<div class="pages"><{$pages}></div>
</div>

<include file="admin@footer" />
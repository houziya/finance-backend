<include file="admin@header" />
<div class="pad-10">
	<div class="content-menu line-x blue"><{$topnav}></div>
	<div class="table-list">
		<form action="<{:url('edit')}>" method="post" id="form1">
		<table width="100%" cellspacing="0">
			<tr>				
				<th width="6%">排序</th>
				<th width="5%">ID</th>
				<th>菜单名称</th>
				<th width="20%">管理操作</th>
			</tr>
			<foreach from="lists" item="v">
			<tr>				
				<td><input type="text" name="sort[<{$v.id}>]" class="input-text text-c" size="2" value="<{$v.sort}>" /> </td>
				<td><{$v.id}></td>
				<td class="text-l"><{$v.spacer}> <{$v.name}> <{$v.code}> <{$v.isshow_tips}></td>
				<td><if condition="$v['level'] elt 3"><a href="<{:url('add?pid='.$v['id'])}>">添加子菜单</a> |&nbsp;</if><a href="<{:url('edit?id='.$v['id'])}>">修改</a> | <a href="<{:url('delete?id='.$v['id'])}>" class="confirm">删除</a></td>
			</tr>
			</foreach>
		</table>
		<div class="btn">
			<input name="do" type="hidden" id="do" value="sort" />
			<input type="submit" class="button" name="dosubmit" value="排序" />
		</div>		
		</form>
	</div>
</div>

<include file="admin@footer" />
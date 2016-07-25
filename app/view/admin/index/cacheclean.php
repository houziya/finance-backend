<include file="admin@header" />

<div class="pad-10">
	<div class="content-menu line-x blue"><a href="<{:url('cacheclean?type=all')}>" class="confirm" msg="确认要删除全部缓存吗？\n\n删除后可能系统瞬间会出现性能问题！\n\n请确认是否有必要全部删除！">删除全部缓存</a></div>
	<div class="table-list">
		<table width="100%" cellspacing="0">
			<tr>				
				<th>缓存名称</th>
				<th>键值</th>
				<th>操作</th>
			</tr>
			<foreach from='lists' key='k' item="v">
			<tr>				
				<td><{$v}></td>
				<td><{$k}></td>
				<td><a href="<{:url('cacheclean?type='.$k)}>">删除缓存</a></td>
			</tr>
			</foreach>
		</table>
	</div>
</div>

<include file="admin@footer" />
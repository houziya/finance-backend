<include file="admin@header" />
<div class="pad-10">
	<div class="content-menu line-x blue"><{$topnav}></div>
	<div class="table-list">
		<form action="" method="post" id="form1">
		<table width="100%" cellspacing="0">
			<tr>				
				<th width="20%">待加入权限方法</th>
				<th>菜单标识符</th>
				<th>操作</th>
			</tr>
			<foreach from="lists" key="k" item="v">
			<tr>				
				<td class="text-l"><{$v.class}> -&gt; <{$v.method}></td>
				<td class="text-l"><{$k}></td>
				<td class="text-l">上级：<{$v.menu_select}> &nbsp;
					菜单名称：<input name="menus[<{$k}>][name]" type="text" />&nbsp;&nbsp;是否显示：
					<input type="checkbox" name="menus[<{$k}>][is_show]" value="1" /></td>
				</tr>
			</foreach>
		</table>
		<div class="btn">
			<input name="do" type="hidden" value="dosubmit" />
			<input type="submit" class="button" name="dosubmit" value="将上述菜单加入菜单列表" />
		</div>		
		</form>
	</div>
</div>

<include file="admin@footer" />
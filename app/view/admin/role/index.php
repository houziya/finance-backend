<include file="admin@header" />
<div class="pad-10">
	<div class="content-menu line-x blue"><{$topnav}></div>
	<div class="table-list">
		<form action="<{:url('edit')}>" method="post" id="form1">
		<table width="100%" cellspacing="0">
			<tr>				
				<th width="6%">排序</th>
				<th width="5%">ID</th>
				<th width="10%">角色名称</th>
				<th>成员列表</th>
				<th>角色描述</th>
				<th width="6%">状态</th>
				<th width="25%">管理操作</th>
			</tr>
			<foreach from='lists' item='v'>
			<tr>				
				<td><input type="text" name="order[<{$v.id}>]" class="input-text text-c" size="2" value="<{$v.sort}>" /> </td>
				<td><{$v.id}></td>
				<td><{$v.name}></td>
				<td class="text-l">
				<foreach from="v['users']" item="v2" key="i">
				<if condition="$i neq 0">&nbsp;|&nbsp;</if>
				<if condition="$v2['status'] eq 0">
				<a href="<{:url('adminuser/edit?uid='.$v2['uid'])}>" class="gray4" title="待审用户"><{$v2.realname}></a>
				<else />
				<a href="<{:url('adminuser/edit?uid='.$v2['uid'])}>" title="正常用户"><{$v2.realname}></a>
				</if>
				</foreach>
				</td>
				<td><{$v.description}></td>
				<td><if condition="$v['status'] eq 0"><span class='red' title='待审核'>待审</span><elseif condition="$v['status'] eq 1" /><span class='green' title='正常'>正常</span><elseif condition="$v['status'] eq 2" /><span class='gray4' title='用户已锁定'>锁定</span></if></td>
				<td><a href="javascript:;" onclick="opendialog('<{:url('rolepriv?id='.$v['id'])}>','权限设置',1,'edit');">权限设置</a> | <a href="<{:url('adminuser/index?role_id='.$v['id'])}>">成员管理</a> | <a href="<{:url('edit?id='.$v['id'])}>">修改</a><if condition="$v['id'] gt 1"> | <a href="<{:url('delete?id='.$v['id'])}>" class="confirm">删除</a></if></td>
			</tr>
			</foreach>
		</table>
		<div class="btn">
			<input name="do" type="hidden" id="do" value="order" />
			<input type="submit" class="button" name="dosubmit" value="排序" />
		</div>		
		</form>
	</div>
	<div class="pages"><{$pages}></div>
</div>

<include file="admin@footer" />
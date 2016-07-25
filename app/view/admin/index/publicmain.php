<include file="admin@header" />
<style type="text/css">
	.ul{
		border:#c7d8ea solid 1px;
		width:300px;
		float:left;
		list-style:none;
		margin-left:15px;
		padding:5px;
	}
	.ul li{
		height:28px;
		line-height:28px;
		
	}
</style>
<div class="pad-10">
	<div class="explain-col mar-b10" style="display:">暂无待处理信息!</div>
	<div class="col-2 fl mar-r10" style="width:48%">
		<h6>我的个人信息</h6>
		<div class="content"> 您好，<{$auth.username}><br />
			所属角色：<{$role_name}> <br />
			<div class="bk20 hr">
				<hr />
			</div>
			上次登录时间：<{$lastlogin.addtime_tips}><br />
			上次登录IP：<{$lastlogin.ip}><br />
		</div>
	</div>
	<div class="col-2 col-auto">
		<h6>网站待处理信息</h6>
		<div class="content">
		
			<ul class="ul">
			<foreach from="websiteInfo['the_first_line']" item="v">
				<li><a href="<{$v.url}>"><{$v.name}>(<{$v.pending_sums_tips}>)</a></li>
			</foreach>
			</ul>
			
			<ul class="ul">
			<foreach from="websiteInfo['the_second_line']" item="v">
				<li><a href="<{$v.url}>"><{$v.name}>(<{$v.pending_sums_tips}>)</a></li>
			</foreach>
			</ul>
			
			<div class="bk20 hr">
				<hr />
			</div>
		</div>
	</div>
	<div class="bk10"></div>
	<div class="col-2 fl mar-r10" style="width:48%">
		<h6>快捷方式</h6>
		<div class="content" id="admin_panel">
			<a href="<{:url('project/index')}>" class="button3">项目管理</a>
			<a href="<{:url('user/index')}>" class="button3">会员管理</a>
		 </div>
	</div>
	<div class="col-2 col-auto">
		<h6>系统公告</h6>
		<div class="content"> 
			※ 暂无系统公告
		</div>
	</div>
	<div class="bk10"></div>
</div>

<include file="admin@footer" />
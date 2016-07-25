<include file="admin@header" />

<div class="pad-10">
	<div class="content-menu line-x blue"><{$topnav}></div>
	<div class="explain-col mar-b8" id="search_form">
		<form name="search_form" action="" method="post" id="form_search">
		        &nbsp;	ID：
		        <input name="search[id]" type="text" class="input-text" value="<?php echo $name;?>" size="10" />
		        &nbsp;  是否查看
		        <?php  echo $error_level;?>
		        &nbsp;  发生时间
		        <?php echo $start_date;?>--   <?php echo $end_date;?>		 	
		        <input type="submit" name="dosubmit" class="button" value="搜索" />
		</form>
	</div>
	<div class="table-list">
<!-- 	<form action="<{:url('edit')}>" method="post" id="form1">	 -->
		<table width="100%" cellspacing="0">
			<tr>
<!--				<th width="6%">排序</th>				-->
				<th width="5%">ID</th>
				<th>位置</th>
				<th>错误级别</th>
				<th>备注</th>
				<th>时间/IP</th>	
			</tr>
			<foreach from='lists' item='v'>
			<tr>	
<!--				<td><input type="text" name="sort[<{$v.id}>]" class="input-text text-c" size="2" value="<{$v.sort}>" /> </td>-->
				<td><?php echo $v['id'];?></td>
				<td><?php echo $v['module'].'/'.$v['controller'].'/'.$v['action'];?></td>
				<td><?php echo $v['level'];?></td>
				<td><?php echo $v['message'];?></td>
				<td><?php echo $v['add_date'].'<br />'.$v['ip'];?></td>
				</td>
			</tr>
			</foreach>
		</table>
<!-- 		<div class="btn"> -->
<!-- 			<input name="do" type="hidden" id="do" value="sort" /> -->
<!-- 			<input type="submit" class="button" name="dosubmit" value="排序" /> -->
<!-- 		</div>		 -->
<!-- 		</form> -->
	</div>
	<div class="pages"><?php echo $pages;?></div>
</div>

<include file="admin@footer" />
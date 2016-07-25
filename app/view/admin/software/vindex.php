<include file="admin@header" />
<script type="text/javascript">
//得到多选框ID集合
function get_ids() {
	var ids = '';
	$("input[name='ids[]']").each(function() {
		if($(this).attr('checked')=='checked') {
			ids += '|'+$(this).val();
		};
	});
	if(ids!=''){
		return ids.substring(1);
	}else{
		return '';
	}
}
//批量设置
function setStatus(v){
    var ids = get_ids();
    if(ids==''){
        alert('请选择待设置的id');
        return false;
    }
    $("input[name='status']").val(v);
    $('#myform').attr('action','<{:url("setVersionStatus")}>');
    $('#myform').submit();
};

$(function(){
       $(".content-menu>a").click(function(){
                var text = $(this).text();
                if(text == '添加版本'){
                  var url = "<{:url('versionAdd?sort_id='.$row['id'])}>";
                  window.location.href = url;
                }
	});
	//批量锁定
	$("#btn_lock").click(function(){
		var ids = get_ids();
		if(ids==''){
			alert('请选择待锁定的用户');
			return false;
		}
		if(!confirm('请确认是否锁定用户？')){
			return false;
		}
		$("input[name='do']").val('lock');
		$('#myform').attr('action','<{:url("edit")}>');
		$('#myform').submit();
	});

});
</script>
<div class="pad-10">
	<div class="content-menu line-x blue"><{$topnav}></div>
	<div class="explain-col mar-b8" id="search_form">
		<form name="search_form" action="" method="post" id="search_form">
                <input name="search[sort_id]" type="hidden" class="input-text" value="<{$row['id']}>" size="10" />
                版本ID：
		<input name="search[id]" type="text" class="input-text" value="<{$id}>" size="10" />
		版本：
                <input name="search[version]" type="text" class="input-text" value="<{$version}>" size="15" />
                &nbsp; 状态：
                <{$status_select}>
                 &nbsp; 添加时间：
                <{$input_add_time}>
                 &nbsp; 更新时间：
                <{$input_update_time}>
		&nbsp;	
		<input type="submit" name="dosubmit" class="button" value="搜索" />
		</form>
	</div>
	<div class="table-list">
		<form name="myform" action="#" method="post" id="myform">
		<table width="100%" cellspacing="0">
			<tr>
				<th width="3%"><input type="checkbox" value="" id="check_box" onclick="selectall('ids[]');"></th>
                                <th width="10%">软件名称</th>
				<th width="5%">版本ID</th>
				<th width="10%">版本名称</th>
				<th width="5%">状态</th>
                                <th>时间</th>
				<th width="15%">管理操作</th>
                                <th width="35%"></th>
			</tr>
			<foreach from="lists" item="v">
			<tr>
				<td><if><input name="ids[]" value="<{$v.id}>" type="checkbox" /></if></td>
                                <td><{$row['name']}></td>
                                <td><{$v.id}></td>
                                <td><{$v.version}></td>
                                <td><{$v.status_tips}></td>
                                <td><{$v.add_time|date='Y-m-d H:i:s',###}><br/>
                                     <{$v.update_time|date='Y-m-d H:i:s',###}><br/></td>
				<td>
				    <a href="<{:url('versionEdit?id='.$v['id'])}>">修改</a>&nbsp;&nbsp;
                                    <a href="<{:url('queryConfig?id='.$v['id'].'&menu_id=37')}>">查询配置</a>&nbsp;&nbsp;
				</td>
                                <td></td>
			</tr>
			</foreach>
		</table>
		<div class="btn">
                <label for="check_box">全选/取消</label>
                <input type="hidden" class="button" name="status"  id="status"  value="" />
                <input type="hidden" class="button" name="sort_id"  id="sort_id"  value="<{$row['id']}>" />
                <input type="button" class="button" name="btn_close" id="btn_close" onclick="setStatus(0)" value="禁用" />
                <input type="button" class="button" name="btn_open" id="btn_open" onclick="setStatus(1)"  value="启用" />
                </div>
		</form>
	</div>
	<div class="pages"><{$pages}></div>
</div>

<include file="admin@footer" />
<script type="text/javascript">
    $(".content-menu").prepend("<a class='on' href='<{:url('Index?id='.$row['id'].'&menu_id=28')}>'><em>软件列表</em></a> <span>|</span>");
</script>
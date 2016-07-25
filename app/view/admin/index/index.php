<include file="admin@header" />

<!--头部开始-->
<div class="header">
	<div class="logo fl"><a href="<{$url.www}>" target="_blank"><span class="invisible"><{$appini.sysconfig.web_name}>后台管理系统</span></a></div>
	<div class="tab_style white text-r fr"> <a href="<{$url.www}>" target="_blank">网站首页</a><span>|</span><a href="<{:url('login/logout')}>">安全退出</a>
	</div>
	<div class="col-auto" style="overflow: visible;">
		<div class="log white">
			<p>您好！<{$user.username}><span>|</span><a href="<{$url.www}>" target="_blank">站点首页</a><span>|</span> <a href="javascript:void(0);" target="_blank">会员中心</a></p>
		</div>
		<ul class="nav white" id="top-nav">
			<foreach from="menus" item="menu">
			<li id="_M<{$menu.id}>"<if condition="$menu_id == $menu['id']"> class="on"</if>><a href="javascript:_M(<{$menu.id}>,'<{$menu.url}>')" hidefocus="true"><{$menu.name}></a></li>
			</foreach>
		</ul>
	</div>
</div>
<!--头部结束-->

<div id="content">
	<!--左边导航开始-->
	<div class="fl left_menu">

		<foreach from="menus" item="v" key="i">
		<div id="leftMain<{$v.id}>" class="leftMain"<if condition="$i eq 1"> style="display:block"</if> >
			<foreach from="v['child']" item="v2">
			<h3 class="switchs on"><span title="展开与收缩"></span><{$v2.name}></h3>
			<ul>
				<foreach from="v2['child']" item="v3">
				<li id="_MP<{$v3.id}>" class="sub_menu"><a href="<{$v3.url}>" onclick="javascript:_MP(<{$v3.id}>,'<{$v3.url}>');" hidefocus="true" target="right"><{$v3.name}></a></li>
				</foreach>
			</ul>
			</foreach>
		</div>
		</foreach>

		<a href="javascript:;" id="openClose" style="outline-style: none; outline-color: invert; outline-width: medium;" hideFocus="hidefocus" class="open" title="展开与关闭"><span class="hidden">展开</span></a>
	</div>
	<!--左边导航结束-->

	<!--中间框架开始-->
	<div class="fl cat-menu col-1" id="display_center_id" style="display:none" height="100%">
		<div class="content">
			<iframe name="center_frame" id="center_frame" src="" frameborder="false" scrolling="auto" style="border:none" width="100%" height="auto" allowtransparency="true"></iframe>
		</div>
	</div>
	<!--中间框架结束-->

	<!--右边框架开始-->
	<div class="col-auto mar-r8">
		<div class="crumbs">
			<div class="fl">当前位置：<span id="current_pos"><span id="current_pos_attr"></span></span></div>
			<div class="shortcut"><a href="<{:url('index/publicmain')}>" target="right"><span>后台首页</span></a><a href="<{:url('cacheclean')}>" target="right"><span>更新缓存</span></a></div>
		</div>
		<div class="col-1">
			<div class="content" style="position:relative; overflow:hidden">
				<iframe name="right" id="rightMain" src="<{:url('index/publicmain')}>" frameborder="false" scrolling="auto" style="overflow-x:hidden;border:none; margin-bottom:30px" width="100%" height="auto" allowtransparency="true" frameborder="0" allowfullscreen></iframe>

				<!--底部收藏栏开始-->
				<div class="fav-nav">
					<div id="panellist">
						<foreach from="my_menus" item="v">
						<span id="panel_<{$v.id}>"><a href="<{$v.url}>" target="right" onclick="paneladdclass(this);"><{$v.name}></a>  <a href="javascript:delete_panel(<{$v.id}>);" class="panel-delete"></a></span>
						</foreach>
					</div>
					<div id="paneladd"><a href="javascript:add_panel();" class="panel-add"><em>添加</em></a></div>
					<input type="hidden" value="10" id="menu_id">
					<input type="hidden" value="1" id="bigid">
                    <div class="fav-help" id="help" style="display: none;"><a target="_blank" href="#">最新公告</a><a onclick="$('#help').slideUp('slow')" href="javascript:;" class="panel-delete"></a></div>
				</div>
				<!--底部收藏栏结束-->

			</div>
		</div>
	</div>
	<!--右边框架开始-->

	<div class="clear"></div>
</div>

<script type="text/javascript">
//左侧子菜单展开与隐藏
$(".switchs").each(function(i){
	var ul = $(this).next();
	$(this).click(function(){
		if(ul.is(':visible')){
			ul.hide();
			$(this).removeClass('on');
		}else{
			ul.show();
			$(this).addClass('on');
		}
	})
});

//clientHeight-0; 空白值 iframe自适应高度
function windowW(){
	if($(window).width()<980){
		$('.header').css('width',980+'px');
		$('#content').css('width',980+'px');
		$('body').attr('scroll','');
		$('body').css('overflow','');
	}
}
$(function(){
	windowW();
});

$(window).resize(function(){
	if($(window).width()<980){
		windowW();
	}else{
		$('.header').css('width','auto');
		$('#content').css('width','auto');
		$('body').attr('scroll','no');
		$('body').css('overflow','hidden');
	}
});
window.onresize = function(){
	var heights = document.documentElement.clientHeight-150;
	document.getElementById('rightMain').height = heights;
	var openClose = heights+39;
	$('#center_frame').height(openClose+11);
	$("#openClose").height(openClose+30);
	$('.leftMain').height(openClose+6);
}
window.onresize();

//左侧开关
$("#openClose").click(function(){
	if($(this).data('clicknum')==1) {
		$("html").removeClass("on");
		$(".left_menu").removeClass("left_menu_on");
		$(this).removeClass("close");
		$(this).data('clicknum', 0);
	} else {
		$(".left_menu").addClass("left_menu_on");
		$(this).addClass("close");
		$("html").addClass("on");
		$(this).data('clicknum', 1);
	}
	return false;
});

//添加底部链接面板
function add_panel() {
	var menu_id = $("#menu_id").val();
	//var url = $('#rightMain').attr('src');
	var url = parent.document.getElementById("rightMain").contentWindow.location.href ;
	$.ajax({
		type: "POST",
		url: "<{:url('ajaxaddpanel')}>",
		data: {menu_id:menu_id,url:url},
		success: function(data){
			if(data) {
				$("#panellist").append(data);
			}
		}
	});
}

//删除底部链接面板
function delete_panel(id){
	$.ajax({
		type: "POST",
		url: "<{:url('ajaxdeletepanel')}>",
		data: {id:id},
		success: function(data){
			if(data == '1'){
				$("#panel_"+id).remove();
			}
		}
	});
}

//突出显示当前点击的面板链接
function paneladdclass(id) {
	$("#panellist span a[class='on']").removeClass();
	$(id).addClass('on')
}

//session保持
setInterval("session_life()", 600000);
function session_life() {
	$.get("<{:url('publicsessionlife')}>");
}

function _M(menu_id,targetUrl) {
	$("#menu_id").val(menu_id);
	$("#bigid").val(menu_id);
	$("#paneladd").html('<a class="panel-add" href="javascript:add_panel();"><em>添加</em></a>');
	$(".leftMain").css('display','none');
	$("#leftMain"+menu_id).css('display','block');
	$("#leftMain3").css({
		'overflow-y':'scroll',
		'overflow-x':'hidden'
	});
	$("#leftMain4").css({
		'overflow-y':'scroll',
		'overflow-x':'hidden'
	});
	if(targetUrl){
		$("#rightMain").attr('src', targetUrl);
	}
	$('#top-nav li').removeClass("on");
	$('#_M'+menu_id).addClass("on");
	$.get("<{:url('ajaxcurrentpos')}>/menu_id/"+menu_id, function(data){
		$("#current_pos").html(data+'<span id="current_pos_attr"></span>');
	});
	//当点击顶部菜单后，隐藏中间的框架
	$('#display_center_id').css('display','none');
	//显示左侧菜单，当点击顶部时，展开左侧
	$(".left_menu").removeClass("left_menu_on");
	$("#openClose").removeClass("close");
	$("html").removeClass("on");
	$("#openClose").data('clicknum', 0);
	$("#current_pos").data('clicknum', 1);
}
function _MP(menu_id,targetUrl) {
	$("#menu_id").val(menu_id);
	$("#paneladd").html('<a class="panel-add" href="javascript:add_panel();"><em>添加</em></a>');

	//$("#rightMain").attr('src', targetUrl+'&menu_id='+menu_id);
	$('.sub_menu').removeClass("on");
	$('#_MP'+menu_id).addClass("on");
	if(targetUrl){
		$("#rightMain").attr('src', targetUrl);
	}
	$.get("<{:url('ajaxcurrentpos')}>/menu_id/"+menu_id, function(data){
		$("#current_pos").html(data+'<span id="current_pos_attr"></span>');
	});
	$("#current_pos").data('clicknum', 1);
	//当点击左边菜单后，隐藏中间的框架
	$('#display_center_id').css('display','none');
}
</script>

<include file="admin@footer" />
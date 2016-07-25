// JavaScript Document
/*pc_v4.0右侧导航*/
$(function() {
	$(".right-top-ul li").live('mouseover', function() {	
			$(this).addClass('on');
		}).live("mouseleave",function() {
			$(this).removeClass('on');
	});	
	$(".right-top-ul li").live('mouseover', function() {	
			$('.right-show').eq($(this).index()).show();
		}).live("mouseleave",function() {
			$('.right-show').eq($(this).index()).hide();
	});	
	$(".right-top-ul2 li").live('mouseover', function() {	
			$(this).addClass('on');
			$('.right-show2').eq($(this).index()).show();
		}).live("mouseleave",function() {
			$(this).removeClass('on');
			$('.right-show2').eq($(this).index()).hide();
	});	

});	

//pc_v4.0返回顶部
$(function(){  
   //当滚动条的位置处于距顶部100像素以下时，跳转链接出现，否则消失  
   $(function () {  
		$(window).scroll(function(){  
			if ($(window).scrollTop()>100){  
				$("#top-btn-li").fadeIn(1500);  
			}  
			else  
			{  
				$("#top-btn-li").fadeOut(1500);  
			}  
		});  
		//当点击跳转链接后，回到页面顶部位置  
		$("#top-btn-li").click(function(){ 
			$('body,html').animate({scrollTop:0},500);  
			return false;  
		});  
	});  
}); 

/*pc_v4.0登录后*/
$(function(){
	$('.after-landing-ul li').hover(function(){
		$(this).addClass('on').siblings().removeClass('on');
	});
});
$(function(){
	$('.after-landing-list').hover(function(){
		$('.after-landing-ul').toggle();
	});
});
$(function(){
	$('.entry-a2').hover(function(){
		$('.right-show1').toggle();
	});
});

$(function(){
	$('.topBtn').hover(function(){
		$('.topBtnP').toggle();
	});
});

$(function(){
	$('.headSearch').click(function(){
		$('.search-show').toggle();
	});
});
$(function(){
	$('.head-search a').click(function(){
		$('.search-show').hide();
	});
});
//顶部通览可展开收起效果
function AdvAuto(){
	if($(".dt_big").length>0){
		var a=1500;
		var b=3*1000;
		$('.dt_toSmall').click(function(){
			$(".dt_small").show();	
			$(".dt_big").hide();
		});
		$(".dt_big").delay(b).slideUp(a,function(){
			$(".dt_small").slideDown(a);
			//$(".dt_toBig").delay(a).fadeIn(0)
		});
		$(".dt_toSmall").delay(b).fadeOut(0)
	}
}




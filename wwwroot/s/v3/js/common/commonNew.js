// JavaScript Document

//pc_v5.0返回顶部
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

/*pc_v5.0登录后*/
$(function(){
	/*$('.header-c-r i').hover(function(){
		$('.header-c-r img').toggle();
	});*/
	$(".header-c-r i").live('mouseover', function() {	
			$('.header-c-r img').show();
		}).live("mouseleave",function() {
			$('.header-c-r img').hide();
	});	
});
$(function(){
	/*$('.login-register2').hover(function(){
		$('.login-list').toggle();
	});*/
	$(".login-register2").live('mouseover', function() {	
			$('.login-list').show();
		}).live("mouseleave",function() {
			$('.login-list').hide();
	});	
});

$(function(){
	$('.head-search').click(function(){
		$(this).addClass('on');
	});
	$(document).click(function(event){
		  if(!$(event.target).parents('.head-search').length && !$(event.target).parents('.head-search').length){
			  $(".head-search").removeClass('on');
		  };
	});
});

/*右侧回顶部*/
$(function(){
	$(window).on('scroll',function(){
		var st = $(document).scrollTop();
		if( st>0 ){
			if( $('#main-container').length != 0  ){
				var w = $(window).width(),mw = $('#main-container').width();
				if( (w-mw)/2 > 70 )
					$('#go-top').css({'left':(w-mw)/2+mw+20});
				else{
					$('#go-top').css({'left':'auto'});
				}
			}
			$('#go-top .go').fadeIn(function(){
				$(this).show('');
			});
		}else{
			$('#go-top .go').fadeOut(function(){
				$(this).hide('');
			});
		}	
	});
	$('#go-top .go').on('click',function(){
		$('html,body').animate({'scrollTop':0},500);
	});
	
	$('#go-top .uc-2vm').hover(function(){
		$('#go-top .uc-2vm-pop').show('');
	},function(){
		$('#go-top .uc-2vm-pop').hide('');
	});
	
	$('#go-top .phone').hover(function(){
		$('#go-top .phone-pop').show('');
	},function(){
		$('#go-top .phone-pop').hide('');
	});
	
	$('#go-top .appload').hover(function(){
		$('#go-top .appload-pop').show('');
	},function(){
		$('#go-top .appload-pop').hide('');
	});
	
	$('#go-top .feedback').hover(function(){
		$('#go-top .feedback-pop').show('');
	},function(){
		$('#go-top .feedback-pop').hide('');
	});
	
});






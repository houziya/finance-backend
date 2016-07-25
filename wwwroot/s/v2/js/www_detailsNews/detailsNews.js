// JavaScript Document



$(".r-text3 li").live('mouseover', function() {	
        $(this).addClass('on');
		$(this).find(".text-content").show()
	}).live("mouseleave",function() {	
		$(this).removeClass('on');
});



$(function(){
	$('.shop-list li').click(function(){
		$(this).addClass('on').siblings().removeClass('on');
		//$('.process-list').hide();
		//$('.process-list').eq($(this).index()).show();
		
	});
});

$(function(){
	$('.hover').hover(function(){
		$('.shop-list-span').toggle();	
	});
});
$(function(){
	$('.shop-list-span span').click(function(){
		$(this).addClass('on').siblings().removeClass('on');
	});
});


$(function(){
	$('.comment-show').click(function(){
		$('.respond-box').show();
	});
});

$(function(){
	$('.arrow-top p').hover(function(){
		$(this).find("span").toggle();	
	});
	$(".arrow-top").click(function(){  
	    $('body,html').animate({scrollTop:550},500);  
		return false;  
	}); 
});

$(function () {  
	$(window).scroll(function(){  
		if ($(window).scrollTop()>500){  
			$(".arrow-top").fadeIn(500);  
		}  
		else  
		{  
			$(".arrow-top").fadeOut(500);  
		}  
	});  
	//当点击跳转链接后，回到页面顶部位置  
	$(".arrow-top").click(function(){   
		$('body,html').animate({scrollTop:550},500);   
		return false;  
	});  
});


//等级
$(function(){
	$('.grade-img').hover(function(){
		$(this).find('div').toggle();	
	});
});
$(function(){
	$('.purple-img').hover(function(){
		$(this).find('div').toggle();	
	});
});


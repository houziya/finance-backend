// JavaScript Document

$(function() {
    var index = 0;  //图片序号
    var adTimer;
    $(".flow_line .flow_img").click(function() {
        index = $(".flow_line .flow_img").index(this);  //获取鼠标悬浮 li 的index
        showImg(index);
    }).eq(0).click();
    //滑入停止动画，滑出开始动画.
    /*$('.ul_out').hover(function() {
        clearInterval(adTimer);
    }, function() {
        adTimer = setInterval(function() {
            showImg(index)
            index++;
            if (index == length) { //最后一张图片之后，转到第一张
                index = 0;
            }
        }, 15000);
    }).trigger("mouseleave");*/

    function showImg(index) {
        var adWidth = 779;
        $(".tv_inner").stop(true, false).animate({
            "marginLeft": -adWidth * index + "px" //改变 marginLeft 值实现轮播效果
        }, 500);
        $(".flow_line .flow_img").removeClass("on")
            .eq(index).addClass("on");
    }
	//左右翻转
	//右翻转
	$(".arrow_r").click(function(e) {
       index = $(".flow_line li.on").index()/2+1;
	   if (index == length) {
                index = 0;
            }
	   r_arrowImg(index);
	   
	  
    });
	
	function r_arrowImg(index) {
        var adWidth = 779;
		var left_margin = adWidth*index;
		if(left_margin <= 3895){
			$(".tv_inner").stop(true, false).animate({
				"marginLeft": -left_margin+ "px" //改变 marginLeft 值实现轮播效果
			}, 500);
			$(".flow_line .flow_img").removeClass("on")
				.eq(index).addClass("on");	
		}  
    }
	//左翻转
	$(".arrow_l").click(function(e) {
       index = $(".flow_line li.on").index()/2-1;
	   if (index < 0) {
                index = 0;
            }
	   l_arrowImg(index);
	  
    });
	
	function l_arrowImg(index) {
        var adWidth = 779;
		var left_margin = adWidth*index;
		//alert(left_margin);
		if(left_margin >= 0){
			$(".tv_inner").stop(true, false).animate({
				"marginLeft": -left_margin+ "px" //改变 marginLeft 值实现轮播效果
			}, 500);
			$(".flow_line .flow_img").removeClass("on")
				.eq(index).addClass("on");	
		}  
    }
});


/*招商系统*/
$(function(){
	$('.merchants-ul li').click(function(){
		$(this).addClass('on').siblings().removeClass('on');
		$('.merchants_list').hide();
		$('.merchants_list').eq($(this).index()).show();
	});
});




$(function() {
    var index = 0;  //图片序号
    var adTimer;
    $(".flow_line2 .flow_img2").click(function() {
        index = $(".flow_line2 .flow_img2").index(this);  //获取鼠标悬浮 li 的index
        showImg(index);
    }).eq(0).click();
    //滑入停止动画，滑出开始动画.
    /*$('.ul_out').hover(function() {
        clearInterval(adTimer);
    }, function() {
        adTimer = setInterval(function() {
            showImg(index)
            index++;
            if (index == length) { //最后一张图片之后，转到第一张
                index = 0;
            }
        }, 15000);
    }).trigger("mouseleave");*/

    function showImg(index) {
        var adWidth = 779;
        $(".tv_inner2").stop(true, false).animate({
            "marginLeft": -adWidth * index + "px" //改变 marginLeft 值实现轮播效果
        }, 500);
        $(".flow_line2 .flow_img2").removeClass("on")
            .eq(index).addClass("on");
    }
	//左右翻转
	//右翻转
	$(".arrow_r2").click(function(e) {
       index = $(".flow_line2 li.on").index()/2+1;
	   if (index == length) {
                index = 0;
            }
	   r_arrowImg(index);
	   
	  
    });
	
	function r_arrowImg(index) {
        var adWidth = 779;
		var left_margin = adWidth*index;
		if(left_margin <= 2337){
			$(".tv_inner2").stop(true, false).animate({
				"marginLeft": -left_margin+ "px" //改变 marginLeft 值实现轮播效果
			}, 500);
			$(".flow_line2 .flow_img2").removeClass("on")
				.eq(index).addClass("on");	
		}  
    }
	//左翻转
	$(".arrow_l2").click(function(e) {
       index = $(".flow_line2 li.on").index()/2-1;
	   if (index < 0) {
                index = 0;
            }
	   l_arrowImg(index);
	  
    });
	
	function l_arrowImg(index) {
        var adWidth = 779;
		var left_margin = adWidth*index;
		//alert(left_margin);
		if(left_margin >= 0){
			$(".tv_inner2").stop(true, false).animate({
				"marginLeft": -left_margin+ "px" //改变 marginLeft 值实现轮播效果
			}, 500);
			$(".flow_line2 .flow_img2").removeClass("on")
				.eq(index).addClass("on");	
		}  
    }
});

$(function() {
    var index = 0;  //图片序号
    var adTimer;
    $(".flow_line3 .flow_img3").click(function() {
        index = $(".flow_line3 .flow_img3").index(this);  //获取鼠标悬浮 li 的index
        showImg(index);
    }).eq(0).click();
    //滑入停止动画，滑出开始动画.
    /*$('.ul_out').hover(function() {
        clearInterval(adTimer);
    }, function() {
        adTimer = setInterval(function() {
            showImg(index)
            index++;
            if (index == length) { //最后一张图片之后，转到第一张
                index = 0;
            }
        }, 15000);
    }).trigger("mouseleave");*/

    function showImg(index) {
        var adWidth = 779;
        $(".tv_inner3").stop(true, false).animate({
            "marginLeft": -adWidth * index + "px" //改变 marginLeft 值实现轮播效果
        }, 500);
        $(".flow_line3 .flow_img3").removeClass("on")
            .eq(index).addClass("on");
    }
	//左右翻转
	//右翻转
	$(".arrow_r3").click(function(e) {
       index = $(".flow_line3 li.on").index()/2+1;
	   if (index == length) {
                index = 0;
            }
	   r_arrowImg(index);
	   
	  
    });
	
	function r_arrowImg(index) {
        var adWidth = 779;
		var left_margin = adWidth*index;
		if(left_margin <= 3116){
			$(".tv_inner3").stop(true, false).animate({
				"marginLeft": -left_margin+ "px" //改变 marginLeft 值实现轮播效果
			}, 500);
			$(".flow_line3 .flow_img3").removeClass("on")
				.eq(index).addClass("on");	
		}  
    }
	//左翻转
	$(".arrow_l3").click(function(e) {
       index = $(".flow_line3 li.on").index()/2-1;
	   if (index < 0) {
                index = 0;
            }
	   l_arrowImg(index);
	  
    });
	
	function l_arrowImg(index) {
        var adWidth = 779;
		var left_margin = adWidth*index;
		//alert(left_margin);
		if(left_margin >= 0){
			$(".tv_inner3").stop(true, false).animate({
				"marginLeft": -left_margin+ "px" //改变 marginLeft 值实现轮播效果
			}, 500);
			$(".flow_line3 .flow_img3").removeClass("on")
				.eq(index).addClass("on");	
		}  
    }
});


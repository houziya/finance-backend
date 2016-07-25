/*头部model*/
$(function(){
	$('#mask-icon').click(function(){
		  $('#header-mask').toggle();
		  //$("body").css("overflow","hidden");		  
	});
	$('#header-mask').click(function(){
		  $(this).toggle();		  
	});
});

/*客服弹层*/
$(function(){
	$('.custom').click(function(){
		  $('#custom-mask').show();
	});
	$('#custom-close').click(function(){
		  $('#custom-mask').hide();
	});
});


/* 项目筛选 */
$(function(){

	$(".choice li").click(function(){  
		$("#choice-bigbox .choice-mask").eq($(this).index()).show().siblings().hide();
		$("body,html").css({"overflow":"hidden"}); 
		
	});

	$("#choice-bigbox .choice-mask").click(function(){
		$(".choice-mask").hide();
		$("body,html").css({"overflow":"auto"}); 
	});


	$("#choice-bigbox .choice-mask a").click(function(){
	    var menu_str = 	$(this).attr("id");
        var choice_name = $(this).text();
        var select_ids = new Array();
        select_ids = menu_str.split("_"); 
        var select_type = select_ids[0];//选择框类型
        var selected_id = select_ids[1];
  
		if(select_type == 'area'){
            $("#scroller .choice li").eq(0).html(choice_name+'<b></b>');
            $("#scroller .choice").attr('data-city',selected_id);
            
		}
		if(select_type == 'status'){
			 $("#scroller .choice li").eq(1).html(choice_name+'<b></b>');
             $("#scroller .choice").attr('data-status',selected_id);
             
		}
		if(select_type == 'trades'){
			 $("#scroller .choice li").eq(2).html(choice_name+'<b></b>');
             $("#scroller .choice").attr('data-trade',selected_id);
             
		}    
        
	});

    
});



//密码是否可见
$(function(){

	$(".input_box input").focus(function(){
		$(this).nextAll("span").show();
	});
	
	$(".password_img").click(function(){
        var pwd_text_id = $(this).parent().children("input:eq(0)").attr('id');
        var pwd_id = $(this).parent().children("input:eq(1)").attr('id');
		if($(".password_img").attr("isshow")=="false")
		{
            $('#'+pwd_id+'Tip').attr('id',pwd_text_id+'Tip');
            $('#password_textTip').show();
            $('#password_textTip').show();
            $(".password").hide();
			$(".password-text").val($(".password").val());
			$(".password-text").show();
			$(".password_img").attr("isshow","true");
            $('#'+pwd_id).val('');
		}
		else
		{
            $('#'+pwd_text_id+'Tip').attr('id',pwd_id+'Tip');
            //$('.password').parent('li').eq(1).attr('id','password');
			$(".password-text").hide();
			$(".password").val($(".password-text").val());
			$(".password").show();
			$(".password_img").attr("isshow","false");
            $('#'+pwd_text_id).val('')
		}
	});

});


/*平台公示和明星榜tab展示*/
$(function(){
    $("#tab_tit li").on("touchstart",function(event){
	    $(this).addClass("active").siblings().removeClass("active");
	    $("#tab_cont_box .tab_cont").eq($(this).index()).show().siblings().hide();
	});
})


//文字变搜索框
$(function(){
    $("#search-rignt-icon").on("touchstart",function(event){
	    $(".header-c").addClass("active");
		$(this).unbind("touchstart");
	});
})


//底部广告
$(function(){
    $(".supClose").click(function(){
		$('.sup-btn').hide();
	});
})


//回到顶部

$(function(){  
   //当滚动条的位置处于距顶部70像素以下时，跳转链接出现，否则消失  
   $(function () {  
		$(window).scroll(function(){  
			if ($(window).scrollTop()>70){  
				$("#Top").fadeIn(1500);  
			}  
			else  
			{  
				$("#Top").fadeOut(1500);  
			}  
		});  
		//当点击跳转链接后，回到页面顶部位置  
		$("#Top").click(function(){ 
			$('body,html').animate({scrollTop:0},500);  
			return false;  
		});  
	});  
});

//注册协议弹层
$(function(){
    $("#myModa_xieyi_p").click(function(){
		$('#myModa_xieyi').show();
		$('#myModa_xieyi .risk-report').css({height:"20em",overflow:"auto"});
	});
	 $(".close-div").click(function(){
		$('#myModa_xieyi').hide();
	});
})


/*红包雨弹层*/
$(function(){
	$('.red-rain-close').click(function(){
		  $('#red-rain').hide();
		  $('.red-rain-b').show();
	});
	$('.rain-line').click(function(){
		  $('.red-rain-b').show();
	});
});
$(function(){
	$('.red-close').click(function(){
		  $('.red-rain-b').hide();
	});
});


/*文本框获取焦点*/
$(function(){
	$(".opinions-text textarea").focus(function(){
		  $(this).addClass("focus");
		  if($(this).val() ==this.defaultValue){  
			  $(this).val("");           
		  } 
	}).blur(function(){
		 $(this).removeClass("focus");
		 if ($(this).val() == '') {
			$(this).val(this.defaultValue);
		 }
	});
})


/*签到弹层*/
$(function(){
	$('.signed').click(function(){
		  $('#sellShow1').show();
		  $('.signed').hide();
	});
	$('.sell_close').click(function(){
		  $('#sellShow1').hide();
	});
});










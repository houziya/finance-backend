/*客服弹层*/
$(function(){
	$('#serviceShow').click(function(){
		  $('#customShow').show();
	});
	$('#customClose').click(function(){
		  $('#customShow').hide();
	});
});

$(function(){
    $(".blacklist-qy").click(function(){
		$(this).toggleClass('blacklistShow');
		$(this).nextAll().toggleClass('companyList');
	});
})


/*搜索*/
$(function(){
	$('.search-ul li').click(function(){
		$(this).addClass('on').siblings().removeClass('on');
	});
});

/*认购分数*/
$(function(){

   $('.number_up').click(function(){
	 var sum = 1;
	 sum = sum + Number($('.inputVal').val());
	 $('.inputVal').val(sum);
  })
  $('.number_down').click(function(){
		 if($('.inputVal').val() <= 1) {
			 return false;
		 }
		 var sum = 1;
		 sum = Number($('.inputVal').val()) - 1;
		 $('.inputVal').val(sum);
	 })
	 $('.inputVal').blur(function(){
		 var giftNum = 8;
		 if(($('.inputVal').val() <= 1) || ($('.inputVal').val() == '')) {
			 $('.inputVal').val(1)
			 return false;
		 }
	 })
});

/*详情介绍*/
$(function(){
	$('.image-text').find('li').click(function(){
		  $(this).addClass('on').siblings().removeClass('on');
	  });
});
function ShowContent(NowNum,MaxNum){
	for (var i=1;i<MaxNum+1;i++)
	{
		$("#ShowContent" + i).hide();
	}	
	$("#ShowContent" + NowNum).show();
		
};

/*项目列表筛选*/
$(function(){
    $(".item-ul li").click(function(){
		//$('.itemShow .item-down').eq($(this).index()).toggleClass("on").siblings().removeClass('on');
		$(this).toggleClass("on").siblings().removeClass('on'); 
		$('.itemShow_div .itemShow').eq($(this).index()).toggleClass("on").siblings().removeClass('on');
		
	}); 

	$('.item-down a').click(function(){
		$(this).addClass('on').siblings().removeClass('on');
	});
});



//项目列表筛选时黑色层不可滑动
$(function(){

	$(".item-ul li").click(function () {
		if($(this).hasClass('on')){
	    $("body").css("overflow","hidden");
	    //alert(526469)
	 }else{
	 	$("body").css("overflow","visible");
	 }
	});

});





/*项目列表弹层*/
$(function(){
	$('#letterClick').click(function(){
		  $('#letterShow').show();
	});
	$('#letterClose').click(function(){
		  $('#letterShow').hide();
	});
});


// $(function(){
// 	$('.tranche-div span').click(function(){
// 		$(this).addClass('on');
// 	});
// 	$('.tranche-div p').click(function(){
// 		$(this).addClass('on');
// 	});
// });

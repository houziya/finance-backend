// JavaScript Document
/*列表页*/
$(function(){
	$('.newList-list-ul li').click(function(){
//		$(this).addClass('on').siblings().removeClass('on');
	});
});

$(function(){
	$('#cityShow').click(function(){
		$(".newList-city").show();
	});
});
$(function(){
	$('.cityClose').click(function(){
		$(".newList-city").hide();
	});
});
/*$(".newList-list-ul li").live('mouseover', function() {	
	    $(this).find(".list-div").eq(0).show();
	}).live("mouseleave",function() {
		$(this).find(".list-div").eq(0).hide();
});*/

$(function(){
	$('#listDiv').click(function(){
		$("#showList").toggle();
		$(this).find("i").toggle();
		$(this).find("span").toggle();
	});
});
$(function(){
	$('#showS').click(function(){
		$("#showDiv").toggle();
		$(this).find("i").toggle();
		$(this).find("span").toggle();
	});
});
$(function(){
	$('.list-div a').click(function(){
		$(this).addClass('on').siblings().removeClass('on');
	});
});
// JavaScript Document
$(function(){
	$('.star-project-ul li').click(function(){
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

$(function(){ 
	$(".reviews-begin").click(function(){ 
	    $(this).toggleClass('on');
	    $(this).next().toggle();
	});
	$(".reviews-begin").click(function(){ 
	    if($(this).find('span').html()=="我要点赞"){
			$(this).find('span').html("收起");
		}else{
			$(this).find('span').html("我要点赞");
		}
	});
});


$(function(){
	$('.dividends-dl dd').click(function(){
		$(this).addClass('on').siblings().removeClass('on');
	});
});
function ShowDividends(NowNum,MaxNum){
	for (var i=1;i<MaxNum+1;i++)
	{
		$("#ShowDividends" + i).hide();         
	}	
	$("#ShowDividends" + NowNum).show();		
};
function ShowProject(NowNum,MaxNum){
	for (var i=1;i<MaxNum+1;i++)
	{
		$("#ShowProject" + i).hide();         
	}	
	$("#ShowProject" + NowNum).show();		
};

$(function(){
	$('.rankings-ul li').click(function(){
		$(this).addClass('on').siblings().removeClass('on');
		$('.rankings-project').hide();
		$('.rankings-project').eq($(this).index()).show();
		$('.rankings-project2').hide();
		$('.rankings-project2').eq($(this).index()).show();
		$('.rankings-project3').hide();
		$('.rankings-project3').eq($(this).index()).show();
	});
});










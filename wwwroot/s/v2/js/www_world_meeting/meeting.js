// JavaScript Document

/*图文直播*/
$(function(){
	$('.expect-list').click(function(){
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
	$('.nav-body li').click(function(){
		$(this).addClass('on').siblings().removeClass('on');		
	});
});


$('.rules-ul li').live("hover", function(){
	$(this).addClass('on').siblings().removeClass('on');
	$('.rules-list').hide();
	$('.rules-list').eq($(this).index()).show();
});
	
$('.rules-ul li').live("click", function(){
	$(this).addClass('on').siblings().removeClass('on');
	$('.rules-list').hide();
	$('.rules-list').eq($(this).index()).show();
});	
	
	

// JavaScript Document

//首页导航
$(function(){
	$('.index-news-nav li').click(function(){
		$(this).addClass('on').siblings().removeClass('on');
		$('.shop-list-span').show();
	});
});


//首页banner轮播图
$(function() {
	var sWidth = $("#focus").width(); //获取焦点图的宽度（显示面积）
	var len = $("#focus ul li").length; //获取焦点图个数
	var index = 0;
	var picTimer;
	
	//鼠标移入显示左右按钮
	$("#focus").mouseover(function() {
			$(".pre").show();
			$(".next").show();
		}).live("mouseleave",function() {		
			$(".pre").hide();
			$(".next").hide();
	});
	
	//以下代码添加数字按钮和按钮后的半透明条，还有上一页、下一页两个按钮
	var btn = "<div class='btnBg'></div><div class='btn'>";
	for(var i=0; i < len; i++) {
		btn += "<span></span>";
	}
	btn += "</div><div class='preNext pre'></div><div class='preNext next'></div>";
	$("#focus").append(btn);
	
    
	//为小按钮添加鼠标滑入事件，以显示相应的内容
	$("#focus .bg-btn-news  span").css("opacity",0.5).mouseover(function() {
		index = $("#focus .bg-btn-news  span").index(this);
		showPics(index);
	}).eq(0).trigger("mouseover");
	
	

	//上一页、下一页按钮透明度处理
	$("#focus .preNext").css("opacity",0.2).hover(function() {
		$(this).stop(true,false).animate({"opacity":"0.5"},300);
	},function() {
		$(this).stop(true,false).animate({"opacity":"0.2"},300);
	});

	//上一页按钮
	$("#focus .pre").click(function() {
		index -= 1;
		if(index == -1) {index = len - 1;}
		showPics(index);
	});

	//下一页按钮
	$("#focus .next").click(function() {
		index += 1;
		if(index == len) {index = 0;}
		showPics(index);
	});

	//本例为左右滚动，即所有li元素都是在同一排向左浮动，所以这里需要计算出外围ul元素的宽度
	$("#focus ul").css("width",sWidth * (len));
	
	//鼠标滑上焦点图时停止自动播放，滑出时开始自动播放
	$("#focus").hover(function() {
		clearInterval(picTimer);
	},function() {
		picTimer = setInterval(function() {
			showPics(index);
			index++;
			if(index == len) {index = 0;}
		},4000); //此4000代表自动播放的间隔，单位：毫秒
	}).trigger("mouseleave");
	
	//显示图片函数，根据接收的index值显示相应的内容
	function showPics(index) { //普通切换
		var nowLeft = -index*sWidth; //根据index值计算ul元素的left值
		$("#focus ul").stop(true,false).animate({"left":nowLeft},300); //通过animate()调整ul元素滚动到计算出的position
		//$("#focus .btn span").removeClass("on").eq(index).addClass("on"); //为当前的按钮切换到选中的效果
		$("#focus .bg-btn-news  span").stop(true,false).animate({"opacity":"0.2"},300).eq(index).stop(true,false).animate({"opacity":"1"},300); //为当前的按钮切换到选中的效果
		
	}
});


$(function(){
	$('.online-time li').click(function(){
		$(this).addClass('on').siblings().removeClass('on');
	});
});

$(function(){
	$('.city-list li').click(function(){
		$(this).addClass('on').siblings().removeClass('on');
	});
});


/*分类项目*/
$(document).ready(function(){
	$('li[data-eventname=name]').hover(function(){
		$(this).addClass('on').siblings().removeClass('on');
		$('.sort-list').hide();
		$('.sort-list').eq($(this).index()).show();
	});
});


//$(function(){
//	$('.after-landing-ul li').hover(function(){
//		$(this).addClass('on').siblings().removeClass('on');
//	});
//});

//$(function(){
//	$('.after-landing-list').hover(function(){
//		$('.after-landing-ul').toggle();
//	});
//});



//项目浮层
$(function(){
   $(".hot-project").live('mouseover', function() {	
	    $(this).find(".hot-project-b").eq(0).slideDown();
	}).live("mouseleave",function() {
		$(this).find(".hot-project-b").eq(0).slideUp();
	});	
	
	$(".prefecture-ul li").live('mouseover', function() {	
	    $(this).find(".hot-project-b").eq(0).slideDown();
		$(this).find("p").eq(0).slideDown();
	}).live("mouseleave",function() {
		$(this).find(".hot-project-b").eq(0).slideUp();
		$(this).find("p").eq(0).slideUp();
	});	
	
	$(".dividends-list-c a").live('mouseover', function() {	
	    $(this).find("div").eq(0).slideDown();
	}).live("mouseleave",function() {
		$(this).find("div").eq(0).slideUp();
	});
	
	$(".financing-l").live('mouseover', function() {	
	    $(this).find("div").eq(0).slideDown();
	}).live("mouseleave",function() {
		$(this).find("div").eq(0).slideUp();
	});	
	
});



//黑名单轮播
$(function(){
	/*$(".now-recommend-r").live('mouseover', function() {	
		clearInterval(recommendScore);
		$(this).find("div").show();
	}).live("mouseleave",function() {		
		$(this).find("div").hide();
	});*/
	$(".blacklist-c").live('mouseover', function() {	
		clearInterval(blacklistScore);		
	});
	/*$(".recommend-next").click(function() {
		var liConut = $(".now-recommend-r ul li").length;
		var nowLi = $(".now-recommend-r ul").attr("now");
		
		var newLeft = 0-(960*(parseInt(nowLi)))+"px";
		
		if(parseInt(nowLi) != parseInt(liConut)){			
			$(".now-recommend-r").find("ul").animate({ 
				marginLeft: newLeft
			 },500);
			$(".now-recommend-r ul").attr("now",parseInt(nowLi)+1);
		}else{
			var newLeft = "0px";
			$(".now-recommend-r").find("ul").animate({ 
				marginLeft: newLeft
			 },500);
			 $(".now-recommend-r ul").attr("now",1);
		}
	});	
	$(".recommend-pre").click(function() {
		var liConut = $(".now-recommend-r ul li").length;
		var nowLi = $(".now-recommend-r ul").attr("now");		
			
		if(parseInt(nowLi) != 1){
			var newLeft = 0-(960*(parseInt(nowLi) - 2))+"px";	
			$(".now-recommend-r").find("ul").animate({ 
				marginLeft: newLeft
			 },500);
			$(".now-recommend-r ul").attr("now",parseInt(nowLi)-1);
		}else{
			var newLeft = 0-(960*(parseInt(liConut)-1))+"px";	
			$(".now-recommend-r").find("ul").animate({ 
				marginLeft: newLeft
			 },500);
			$(".now-recommend-r ul").attr("now",parseInt(liConut));
		}
	});*/
	$(".blacklist-r").click(function() {
		var liConut = $(".blacklist-c ul li").length;
		var nowLi = $(".blacklist-c ul").attr("now");
		
		var newLeft = 0-(1052*(parseInt(nowLi)))+"px";
		
		if(parseInt(nowLi) != parseInt(liConut)){
			var newLeft = 0-(1052*(parseInt(nowLi)))+"px";
			$(".blacklist-c").find("ul").animate({ 
				marginLeft: newLeft
			 },500);
			$(".blacklist-c ul").attr("now",parseInt(nowLi)+1);
		}else{
			var newLeft = "0px";
			$(".blacklist-c").find("ul").animate({ 
				marginLeft: newLeft
			 },500);
			 $(".blacklist-c ul").attr("now",1);
		}
	});
	$(".blacklist-l").click(function() {			
		var nowLi = $(".blacklist-c ul").attr("now");	
		var liConut = $(".blacklist-c ul li").length;
		if(parseInt(nowLi) != 1){
			var newLeft = 0-(1052*(parseInt(nowLi) - 2))+"px";		
			$(".blacklist-c").find("ul").animate({ 
				marginLeft: newLeft
			 },500);
			$(".blacklist-c ul").attr("now",parseInt(nowLi)-1);
		}else{
			var newLeft = 0-(1052*(parseInt(liConut) - 1))+"px";		
			$(".blacklist-c").find("ul").animate({ 
				marginLeft: newLeft
			 },500);
			$(".blacklist-c ul").attr("now",parseInt(liConut));
		}
	});		
	/*var recommendScore = setInterval(function(){
		var nowType = $(".now-recommend-r ul").attr("type");
		if(nowType == 0){
			var liConut = $(".now-recommend-r ul li").length;
			var nowLi = $(".now-recommend-r ul").attr("now");
			var newLeft = 0-(960*(parseInt(nowLi) + 1))+"px";
			
			if(parseInt(nowLi)+1 != parseInt(liConut)){			
				$(".now-recommend-r").find("ul").animate({ 
					marginLeft: newLeft
				 },1000);
				$(".now-recommend-r ul").attr("now",parseInt(nowLi)+1);
			}else{				
				$(".now-recommend-r ul").attr("type",1);
				$(".now-recommend-r ul").attr("now",parseInt(nowLi));
			}	
		}else{	
			var nowLi = $(".now-recommend-r ul").attr("now");		
			var newLeft = 0-(960*(parseInt(nowLi) - 1))+"px";		
			if(parseInt(nowLi) != 0){			
				$(".now-recommend-r").find("ul").animate({ 
					marginLeft: newLeft
				 },1000);
				$(".now-recommend-r ul").attr("now",parseInt(nowLi)-1);
			}else{
				$(".now-recommend-r ul").attr("type",0);
				$(".now-recommend-r ul").attr("now",0);
			}
		}		
	},1000);*/
	var blacklistScore = setInterval(function(){
		var nowType = $(".blacklist-c ul").attr("type");
		if(nowType == 0){
			var liConut = $(".blacklist-c ul li").length;
			var nowLi = $(".blacklist-c ul").attr("now");
			
			var newLeft = 0-(1052*(parseInt(nowLi)))+"px";
			
			if(parseInt(nowLi) != parseInt(liConut)){			
				$(".blacklist-c").find("ul").animate({ 
					marginLeft: newLeft
				 },500);
				$(".blacklist-c ul").attr("now",parseInt(nowLi)+1);
			}else{				
				$(".blacklist-c ul").attr("type",1);
				$(".blacklist-c ul").attr("now",parseInt(nowLi));
			}	
		}else{	
			var nowLi = $(".blacklist-c ul").attr("now");		
			var newLeft = 0-(1052*(parseInt(nowLi) - 2))+"px";			
			if(parseInt(nowLi) != 1){			
				$(".blacklist-c").find("ul").animate({ 
					marginLeft: newLeft
				 },500);
				$(".blacklist-c ul").attr("now",parseInt(nowLi)-1);
			}else{
				$(".blacklist-c ul").attr("type",0);
				$(".blacklist-c ul").attr("now",1);
			}
		}		
	},1000);	
})


/*左侧飘窗*/
//左侧导航
/*$(function(){
	$('.left-nav li').hover(function(){
		$(this).addClass('on').siblings().removeClass('on');
	});
});*/
$(document).ready(function(){
	window.onresize = window.onscroll;
	$(window).scroll(function(){
		var top = $("#divSuspended").offset().top;
		$("div[data-eventname=content]").each(function(){
			if ($(this).offset().top - top - 50 < 0) {
				$(".left-nav-list").find("li").removeClass("on");
				$(".left-nav-list").find("li").eq($(this).attr("data-index")).addClass("on");
			}
		});
	})
});
/*左侧飘窗 当滚动条的位置处于距顶部300像素以下时，跳转链接出现，否则消失  */
$(function(){  
   $(function () {  
		$(window).scroll(function(){  
			if ($(window).scrollTop()>100){  
				$(".left-nav").fadeIn(500);  
			} 
			else  
			{  
				$(".left-nav").fadeOut(500);  
			}  
		}); 
		$(window).scroll(function(){
		　　var scrollTop = $(this).scrollTop();
		　　var scrollHeight = $(document).height();
		　　var windowHeight = $(this).height();
		　　if(scrollTop + windowHeight == scrollHeight){
		　　　　$(".left-nav").fadeOut(500); 
		　　}
		}); 
	});  
}); 

$.fn.extend({
    //TODO 获取元素距离可视区高度
    disScreenTop:function(){
        return this.eq(0).offset().top-$(window).scrollTop();
    }
});
//TODO 页面滚动函数
function winScrollFn(){
	var leftNav=$('#leftNav');
	var pListH3_01=$('#new');
	var ZJLL_mark=$('#ZJLL_mark');
	var RNavZJLL=$('#RNavZJLL');
	var winScrollTop=$(window).scrollTop();
	try{
		var topDis=function(){
			var topDis=pListH3_01.disScreenTop();
			if(topDis<=80){
				topDis=80;
			}
			return topDis;
		}();
		leftNav.stop(true,true).animate({
			'top':topDis-1
		},100);
	}catch (ex){
   
	}
	if(!window.RNavZJLL_NOT_SCROLL){
	try{
		if(ZJLL_mark.disScreenTop()<=extraTopDis){
			RNavZJLL.addClass('fixLayout_'+extraTopDis);
		}else{
			RNavZJLL.removeClass('fixLayout_'+extraTopDis);
		}
	}catch (ex){

	}}
	//TODO 左侧导航同步高亮
	try{
		var leftNavItems=leftNav.find('li');
		var itemLen=leftNavItems.length;

		for(var i=itemLen;i>=1;i--){
			if(winScrollTop>=$('#new'+i).offset().top-extraTopDis){
				leftNavItems.removeClass('on');
				leftNavItems.eq(i-1).addClass('on');
				break;
			}
		}
	}catch(ex){
		
	}
}
winScrollFn();
$(window).scroll(function(){
	winScrollFn();
});
//TODO 左侧导航
$('#leftNav').delegate('li','click',function(){
	var curIndex=$(this).index();
	$('html, body').stop(true,true).animate({
		scrollTop:$('#new'+(curIndex+1)).offset().top-80
	}, 200);
});



//返回顶部
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
//右侧导航
$(".right-nav-list li").live('mouseover', function() {	
	    $(this).addClass('on');
		$('.nav-content').eq($(this).index()).show();
	}).live("mouseleave",function() {
		$(this).removeClass('on');
		$('.nav-content').eq($(this).index()).hide();
});	


/*新手专区*/
var foucsbox = function (time) {
    var time = time || 3500
    , $ = function (id) { return document.getElementById(id); }
    , topCon = $('imgplay')
    , big = $('imglist')
    , small = $('pagelist')
    , tip = $('title')
    , bigimgs = big.getElementsByTagName('li')
    , smallimgs = small.getElementsByTagName('li')
    , imglink = tip.getElementsByTagName('a')[0]
    , slide = function (z) {
        smallimgs[lastIndex].className = '';
        smallimgs[z].className = 'current';
        bigimgs[lastIndex].style.display = 'none';
        bigimgs[z].style.display = 'block';
        try {
            imglink.innerHTML = smallimgs[z].getElementsByTagName('img')[0].alt;
        }
        catch (e) {
            imglink.innerText = smallimgs[z].firstChild.firstChild.alt;
        }
        lastIndex = i = z;
    }
    , helper = function (z) {
        return function (e) {
            var na;
            if (!e) {
                e = window.event;
                na = e.srcElement.nodeName;
            }
            else {
                na = e.target.nodeName;
            }
            if (na === 'IMG') {
                slide(z);
            }
        }
    }
    , lastIndex = i = 0, x, y = bigimgs.length
    , getPrevI = function (q) { return i - q < 0 ? y - q : i - 1; }
    , getNextI = function (q) { return i + q >= y ? i + q - y : i + 1; }
    var s = setInterval(function () {
        slide(i);
        i = getNextI(1);
    }, time);
    try {
        imglink.innerText = smallimgs[0].getElementsByTagName('img')[0].alt;
    }
    catch (e) {
        imglink.innerText = smallimgs[0].firstChild.firstChild.alt;
    }
    for (x = 1; x < y; x += 1) {
        bigimgs[x].style.display = 'none';
    }
    for (x = 0; x < y; x += 1) {
        smallimgs[x].onmouseover = helper(x);
    }
    topCon.children[2].onclick = function (e) {
        i = lastIndex;
        var t;
        if (!e) {
            e = window.event;
            t = e.srcElement;
        } else {
            t = e.target;
        }
        switch (t.className) {
            case 'icon_prev':
                slide(getPrevI(1));
                break;
            case 'icon_next':
                slide(getNextI(1));
                break;
        }
    };
    topCon.onmouseover = function () {
        clearInterval(s);
    };
    topCon.onmouseout = function () {
        s = setInterval(function () {
            slide(i);
            i = getNextI(1);
        }, time);
    };
};

$(function(){
	var sWidth = $("#slider_name").width();
	var len = $("#slider_name .silder_panel").length;
	var index = 0;
	var picTimer;
	
	var btn = "<a class='prev'>Prev</a><a class='next'>Next</a>";
	$("#slider_name").append(btn);

	$("#slider_name .focus_list li").mouseenter(function() {																		
		index = $("#slider_name .focus_list li").index(this);
		showPics(index);
	}).eq(0).trigger("mouseenter");
	// 
	$("#slider_name .silder_con").css("width",sWidth * (len));
	
	// mouse 
	$("#slider_name").hover(function() {
		clearInterval(picTimer);
	},function() {
		picTimer = setInterval(function() {
			showPics(index);
			index++;
			if(index == len) {index = 0;}
		},3000); 
	}).trigger("mouseleave");
	
	// showPics
	function showPics(index) {
		var nowLeft = -index*sWidth;
		$('.prefecture-show').eq(index).toggleClass('on').siblings().removeClass('on');
		$('.prefecture-show').eq(index).show().siblings().hide();
		$("#slider_name .silder_con").stop(true,false).animate({"left":nowLeft},300);
		$("#slider_name .focus_list li").removeClass("current").eq(index).addClass("current");
		$("#slider_name .focus_list li").stop(true,false).animate({"opacity":"0.5"},300).eq(index).stop(true,false).animate({"opacity":"1"},300);
		$("#slider_name .silder_intro").stop(true,false).animate({"opacity":"0"},300).eq(index).stop(true,false).animate({"opacity":"1"},300);
		
	}
});



/*投资人黑名单*/
$(function(){
	$('.blacklist-span span').click(function(){
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
//轮播
$(function(){
	$(".blacklist-c").live('mouseover', function() {	
		clearInterval(blacklistScore);		
	});
	$(".blacklist-r2").click(function() {
		var liConut = $(".blacklist-c2 ul li").length;
		var nowLi = $(".blacklist-c2 ul").attr("now");
		var newLeft = 0-(1052*(parseInt(nowLi)))+"px";
		
		if(parseInt(nowLi) != parseInt(liConut)){
			var newLeft = 0-(1052*(parseInt(nowLi)))+"px";
			$(".blacklist-c2").find("ul").animate({ 
				marginLeft: newLeft
			 },500);
			$(".blacklist-c2 ul").attr("now",parseInt(nowLi)+1);
		}else{
			var newLeft = "0px";
			$(".blacklist-c2").find("ul").animate({ 
				marginLeft: newLeft
			 },500);
			 $(".blacklist-c2 ul").attr("now",1);
		}
	});
	$(".blacklist-l2").click(function() {			
		var nowLi = $(".blacklist-c2 ul").attr("now");	
		var liConut = $(".blacklist-c2 ul li").length;
		if(parseInt(nowLi) != 1){
			var newLeft = 0-(1052*(parseInt(nowLi) - 2))+"px";		
			$(".blacklist-c2").find("ul").animate({ 
				marginLeft: newLeft
			 },500);
			$(".blacklist-c2 ul").attr("now",parseInt(nowLi)-1);
		}else{
			var newLeft = 0-(1052*(parseInt(liConut) - 1))+"px";		
			$(".blacklist-c2").find("ul").animate({ 
				marginLeft: newLeft
			 },500);
			$(".blacklist-c2 ul").attr("now",parseInt(liConut));
		}
	});	
	var blacklistScore = setInterval(function(){
		var nowType = $(".blacklist-c2 ul").attr("type");
		if(nowType == 0){
			var liConut = $(".blacklist-c2 ul li").length;
			var nowLi = $(".blacklist-c2 ul").attr("now");
			
			var newLeft = 0-(1052*(parseInt(nowLi)))+"px";
			
			if(parseInt(nowLi) != parseInt(liConut)){			
				$(".blacklist-c2").find("ul").animate({ 
					marginLeft: newLeft
				 },500);
				$(".blacklist-c2 ul").attr("now",parseInt(nowLi)+1);
			}else{				
				$(".blacklist-c2 ul").attr("type",1);
				$(".blacklist-c2 ul").attr("now",parseInt(nowLi));
			}	
		}else{	
			var nowLi = $(".blacklist-c2 ul").attr("now");		
			var newLeft = 0-(1052*(parseInt(nowLi) - 2))+"px";			
			if(parseInt(nowLi) != 1){			
				$(".blacklist-c2").find("ul").animate({ 
					marginLeft: newLeft
				 },500);
				$(".blacklist-c2 ul").attr("now",parseInt(nowLi)-1);
			}else{
				$(".blacklist-c2 ul").attr("type",0);
				$(".blacklist-c2 ul").attr("now",1);
			}
		}		
	},1000);	
})


//今日推荐
$(".now-recommend-r").live('mouseover', function() {	
	  $(".hd a").show();
  }).live("mouseleave",function() {		
	  $(".hd a").hide();
});


























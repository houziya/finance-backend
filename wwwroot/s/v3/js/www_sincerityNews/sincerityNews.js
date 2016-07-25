// JavaScript Document

//首页banner轮播图
$(function() {
	var sWidth = $("#focus").width(); //获取焦点图的宽度（显示面积）
	var len = $("#focus ul li").length; //获取焦点图个数
	var index = 0;
	var picTimer;
	
	
	//以下代码添加数字按钮和按钮后的半透明条，还有上一页、下一页两个按钮
	var btn = "<div class='btnBg'></div><div class='btn'>";
	for(var i=0; i < len; i++) {
		btn += "<span></span>";
	}
	btn += "</div><div class='preNext pre'></div><div class='preNext next'></div>";
	$("#focus").append(btn);
	
    
	//为小按钮添加鼠标滑入事件，以显示相应的内容
	$("#focus .bg-btn-news  span").mouseover(function() {
		index = $("#focus .bg-btn-news  span").index(this);
		showPics(index);
	}).eq(0).trigger("mouseover");
	
	

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
		$("#focus .btn span").removeClass("on").eq(index).addClass("on"); //为当前的按钮切换到选中的效果
		$("#focus .bg-btn-news  span").stop(true,false).animate({"opacity":"0.5"},300).eq(index).stop(true,false).animate({"opacity":"1"},300); //为当前的按钮切换到选中的效果
		
	}
});


$(function(){
    $('.hot-search-ul li').live("hover", function(){
		$(this).addClass('on').siblings().removeClass('on');
		$('.hot-box').hide();
		$('.hot-box').eq($(this).index()).show();
	});

});

$(function(){
    $('.engines-ul li').live("click", function(){
		$(this).addClass('on').siblings().removeClass('on');
	});

});

$(function(){
	$(".engines-dl2").live('mouseover', function() {	
	      $(this).addClass('on');
		  $('.engines-dlh2').show();
	  }).live("mouseleave",function() {		
		  $(this).removeClass('on');
		  $('.engines-dlh2').hide();
	});

});

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


























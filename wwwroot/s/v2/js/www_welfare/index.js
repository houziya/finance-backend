
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
	$("#focus .bg-btn-news  span").mouseover(function() {
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
		$("#focus .bg-btn-news span").removeClass("on").eq(index).addClass("on"); //为当前的按钮切换到选中的效果
		/*$("#focus .bg-btn-news  span").stop(true,false).animate({"opacity":"0.2"},300).eq(index).stop(true,false).animate({"opacity":"1"},300);*/ //为当前的按钮切换到选中的效果
		
	}
});



/*分享*/
window._bd_share_config={"common":{"bdSnsKey":{},"bdText":"","bdMini":"2","bdMiniList":false,"bdPic":"","bdStyle":"0","bdSize":"16"},"share":{},"selectShare":{"bdContainerClass":null,"bdSelectMiniList":["qzone","tsina","weixin","renren"]}};with(document)0[(getElementsByTagName('head')[0]||body).appendChild(createElement('script')).src='http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion='+~(-new Date()/36e5)];

/*跑马灯效果*/
$(document).ready(function(){
	if ($("#scrollDiv").html() != undefined) {
		$("#scrollDiv").textSlider({line:1,speed:500,timer:2000});
	}
});


/*右侧固定滑动*/


$(function() {
    fixedTopObj = function(e, c) {
        var b = $(e).offset().top;
        var f = parseInt($(e).parent().css("padding-bottom"));
        var a = f + parseInt($(e).outerHeight());
        $(window).bind("scroll", function() {
            d()
        });

        function d() {
            if ($(document).scrollTop() >= b) {
                $(e).parent().css("padding-bottom", a);
                $(e).css("position", "fixed").css("top", "0").css("bottom", "auto")
            } else {
                $(e).parent().css("padding-bottom", f);
                $(e).css("position", "static")
            }
            if ($(document).scrollTop() > ($(c).offset().top + $(c).height())) {
                $(e).parent().css("padding-bottom", f);
                $(e).css("position", "static")
            }
        }
    };
    floatbyleft = function(c, e) {
        var b = document.createElement("div");
        $(b).insertBefore($(c));
        var a = 0;
        var d = 0;

        function f() {
            var k = $(document).scrollTop();
            var i = $(window).innerHeight();
            var h = $(e).offset().top;
            var o = $(e).height();
            var m = $(c).height();
            var n = $(b).offset().top;
            if (m + n > o + h || k < n) {
                a = k;
                d = 0;
                $(c).css("position", "static");
                return
            }
            var g = Math.min(o + h - k, i);
            var j = n;
            var l = j;
            if (k > a) {
                l = Math.max(k + g - m, j);
                d = d + 1 * (k - a)
            } else {
                l = Math.max(k, j)
            }
            d = Math.min(l, Math.max(d, j));
            a = k;
            $(c).css("position", "fixed").css("top", d - a)
        }
        $(window).bind("scroll", function() {
            setTimeout(f(), 10)
        });
        $(window).bind("resize", function() {
            setTimeout(f(), 10)
        });
        f()
    };
    if ($("#wrap-right").length == 1) {
        $("#wrap-right").css("width", "299px");
        $("#wrap-right").css("display", "inline-block");
        $("#wrap-right").css("margin-left", "21px");
        floatbyleft("#wrap-right", ".wrap-left")
    }
});
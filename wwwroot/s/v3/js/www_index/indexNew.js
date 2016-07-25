
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
	/*$("#focus .preNext").css("opacity",0.2).hover(function() {
		$(this).stop(true,false).animate({"opacity":"0.5"},300);
	},function() {
		$(this).stop(true,false).animate({"opacity":"0.2"},300);
	});*/

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


/*导航固定*/
/*$(function(){   
	$(window).scroll(function() {
		if($(window).scrollTop()>=50){
			$(".head-box").addClass("fixedNav");
		}else{
			$(".head-box").removeClass("fixedNav");
		} 
	});
});*/


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
    //, getPrevI = function (q) { return i - q < 0 ? y - q : i - 1; }
    //, getNextI = function (q) { return i + q >= y ? i + q - y : i + 1; }
    /*var s = setInterval(function () {
        slide(i);
        i = getNextI(1);
    }, time);*/
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
        smallimgs[x].onclick = helper(x);
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
    /*topCon.onmouseover = function () {
        clearInterval(s);
    };
    topCon.onmouseout = function () {
        s = setInterval(function () {
            slide(i);
            i = getNextI(1);
        }, time);
    };*/
};



$(function(){
	$('.head-nav li').click(function(){
		$(this).addClass('on').siblings().removeClass('on');
	});
});


/*$(function(){
	$('.compare').click(function(){
		$(this).toggleClass('on');
	});
	$('.nice').click(function(){
		$(this).toggleClass('on');
	});
});*/

/*新闻资讯*/
$(function(){
	$('.news-carry-ul li').click(function(){
		$(this).addClass('on').siblings().removeClass('on');
		$('.carry-list').hide();
		$('.carry-list').eq($(this).index()).show();
	});
});


$(".img-text-li li").live('mouseover', function() {	
	    $(this).addClass('on').siblings().removeClass('on');
	}).live("mouseleave",function() {
		$(this).removeClass('on');
});


//$(function(){
//	$('.rzUlS1 .bg5').hover(function(){
//		$('.bailS1').toggle();
//	});
//	$('.rzUlS2 .bg5').hover(function(){
//		$('.bailS2').toggle();
//	});
//});

$(function(){
	$('.suc-yi a').click(function(){
		$(this).addClass('on').siblings().removeClass('on');
	});
});
function ShowContent(NowNum){
	$("#ShowContent" + NowNum).show().siblings('.suc-list').hide();
		
};

































// JavaScript Document
//
//$(function(){
//	$('.reviewsUl').find('li').click(function(){
//		  $(this).addClass('on').siblings().removeClass('on');
//		  $('.reviewsList').hide();
//		  $('.reviewsList').eq($(this).index()).show();
//	  });
//});
//$(function(){
//	$('.reviews-ul2').find('li').click(function(){
//		  $(this).addClass('on').siblings().removeClass('on');
//		  $('.reviewsBox1').hide();
//		  $('.reviewsBox1').eq($(this).index()).show();
//	  });
//});
$(function(){
	$('.reviews-ul3').find('li').click(function(){
		  $(this).addClass('on').siblings().removeClass('on');
		  $('.reviewsBox2').hide();
		  $('.reviewsBox2').eq($(this).index()).show();
	  });
});
$(function(){
	$('.reviews-btn').click(function(){
		  $(this).parents().next('.reviews-xl').toggle();
	  });
});

$(function(){
	$('.project-marks-r span').click(function(){
		  $(this).children('span').addClass('on');
	  });
});


$(function(){
	$('.left_bar li').click(function(){
		 $(this).find('span').addClass('now').siblings().removeClass('now');
		 $(this).siblings().find('span').removeClass('now');
	  });
});

$(function(){
	$('.news-fb').find('a').click(function(){
		  $(this).addClass('on').siblings().removeClass('on');
	  });
});

$(function(){
	$('.sync-yibao').find('a').click(function(){
		  $(this).addClass('on').siblings().removeClass('on');
	  });
});


$('.reviews_bg').find('li').click(function(){
		$(this).addClass('on');
		var index=$(this).index();
		$('.reviews_bg').find('li:lt('+index+')').addClass('on');
		$('.reviews_bg').find('li:gt('+index+')').removeClass('on');		
	});


$(function(){
	$('.site-ul2').find('li').click(function(){
		  $(this).addClass('on').siblings().removeClass('on');
		  $('.siteShow').hide();
		  $('.siteShow').eq($(this).index()).show();
	  });
});




/*店铺照轮播图*/
$(function() {
    function i(e) {
        $("#originalpic img").eq(e).stop(!0, !0).fadeIn(800).siblings("img").hide(),
        $(".thumbpic li").eq(e).addClass("hover").siblings().removeClass("hover"),
        $.browser.msie && $.browser.version == "6.0" && !$.support.style && $("#aPrev,#aNext").css("height", $("#originalpic img").eq(e).height() + "px")
    }
    function s() {
        e >= 0 && e < n - 1 ? (++e, i(e)) : $.dialog({
            title: "\u63d0\u793a",
            icon: "face-sad",
            content: "\u5df2\u7ecf\u662f\u6700\u540e\u4e00\u5f20,\u70b9\u51fb\u786e\u5b9a\u91cd\u65b0\u6d4f\u89c8\uff01",
            lock: !0,
            opacity: "0.5",
            okVal: "\u786e\u5b9a",
            ok: function() {
                e = 0,
                i(0),
                n >= t && (aniPx = (n - t) * 110 + "px", $("#piclist ul").animate({
                    left: "+=" + aniPx
                },
                200)),
                r = 1
            },
            cancelVal: "\u53d6\u6d88",
            cancel: function() {}
        });
        if (r < 0 || r > n - t) return ! 1;
        $("#piclist ul").animate({
            left: "-=110px"
        },
        200),
        r++
    }
    function o() {
        if (e <= 0) return $.message({
            content: "\u5df2\u7ecf\u662f\u7b2c\u4e00\u5f20",
            time: 3e3
        }),
        !1;
        e >= 1 && (--e, i(e));
        if (r < 2 || r > n + t) return ! 1;
        $("#piclist ul").animate({
            left: "+=110px"
        },
        200),
        r--
    }
    var e = 0,
    t = 5,
    n = $("#originalpic img").length,
    r = 1;
    $("#originalpic img").eq(0).show(),
    $.browser.msie && $.browser.version == "6.0" && !$.support.style && $("#aPrev,#aNext").css("height", $("#originalpic img").eq(0).height() + "px"),
    $(".thumbpic li").eq(0).addClass("hover"),
    $(".thumbpic tt").each(function(e) {
        var t = e + 1 + "/" + n;
        $(this).html(t)
    }),
    $(".bntnext,#aNext").click(function() {
        s()
    }),
    $(".bntprev,#aPrev").click(function() {
        o()
    }),
    $(".thumbpic li").click(function() {
        e = $(".thumbpic li").index(this),
        i(e)
    })
})


/*周边照轮播图*/
$(function() {
    function i(e) {
        $("#originalpic2 img").eq(e).stop(!0, !0).fadeIn(800).siblings("img").hide(),
        $(".thumbpic2 li").eq(e).addClass("hover2").siblings().removeClass("hover2"),
        $.browser.msie && $.browser.version == "6.0" && !$.support.style && $("#aPrev2,#aNext2").css("height", $("#originalpic2 img").eq(e).height() + "px")
    }
    function s() {
        e >= 0 && e < n - 1 ? (++e, i(e)) : $.dialog({
            title: "\u63d0\u793a",
            icon: "face-sad",
            content: "\u5df2\u7ecf\u662f\u6700\u540e\u4e00\u5f20,\u70b9\u51fb\u786e\u5b9a\u91cd\u65b0\u6d4f\u89c8\uff01",
            lock: !0,
            opacity: "0.5",
            okVal: "\u786e\u5b9a",
            ok: function() {
                e = 0,
                i(0),
                n >= t && (aniPx = (n - t) * 110 + "px", $("#piclist2 ul").animate({
                    left: "+=" + aniPx
                },
                200)),
                r = 1
            },
            cancelVal: "\u53d6\u6d88",
            cancel: function() {}
        });
        if (r < 0 || r > n - t) return ! 1;
        $("#piclist2 ul").animate({
            left: "-=110px"
        },
        200),
        r++
    }
    function o() {
        if (e <= 0) return $.message({
            content: "\u5df2\u7ecf\u662f\u7b2c\u4e00\u5f20",
            time: 3e3
        }),
        !1;
        e >= 1 && (--e, i(e));
        if (r < 2 || r > n + t) return ! 1;
        $("#piclist2 ul").animate({
            left: "+=110px"
        },
        200),
        r--
    }
    var e = 0,
    t = 5,
    n = $("#originalpic2 img").length,
    r = 1;
    $("#originalpic2 img").eq(0).show(),
    $.browser.msie && $.browser.version == "6.0" && !$.support.style && $("#aPrev2,#aNext2").css("height", $("#originalpic2 img").eq(0).height() + "px"),
    $(".thumbpic2 li").eq(0).addClass("hover2"),
    $(".thumbpic2 tt").each(function(e) {
        var t = e + 1 + "/" + n;
        $(this).html(t)
    }),
    $(".bntnext2,#aNext2").click(function() {
        s()
    }),
    $(".bntprev2,#aPrev2").click(function() {
        o()
    }),
    $(".thumbpic2 li").click(function() {
        e = $(".thumbpic2 li").index(this),
        i(e)
    })
})

/*地图轮播图*/
$(function() {
    function i(e) {
        $("#originalpic3 img").eq(e).stop(!0, !0).fadeIn(800).siblings("img").hide(),
        $(".thumbpic3 li").eq(e).addClass("hover3").siblings().removeClass("hover3"),
        $.browser.msie && $.browser.version == "6.0" && !$.support.style && $("#aPrev3,#aNext3").css("height", $("#originalpic3 img").eq(e).height() + "px")
    }
    function s() {
        e >= 0 && e < n - 1 ? (++e, i(e)) : $.dialog({
            title: "\u63d0\u793a",
            icon: "face-sad",
            content: "\u5df2\u7ecf\u662f\u6700\u540e\u4e00\u5f20,\u70b9\u51fb\u786e\u5b9a\u91cd\u65b0\u6d4f\u89c8\uff01",
            lock: !0,
            opacity: "0.5",
            okVal: "\u786e\u5b9a",
            ok: function() {
                e = 0,
                i(0),
                n >= t && (aniPx = (n - t) * 110 + "px", $("#piclist3 ul").animate({
                    left: "+=" + aniPx
                },
                200)),
                r = 1
            },
            cancelVal: "\u53d6\u6d88",
            cancel: function() {}
        });
        if (r < 0 || r > n - t) return ! 1;
        $("#piclist3 ul").animate({
            left: "-=110px"
        },
        200),
        r++
    }
    function o() {
        if (e <= 0) return $.message({
            content: "\u5df2\u7ecf\u662f\u7b2c\u4e00\u5f20",
            time: 3e3
        }),
        !1;
        e >= 1 && (--e, i(e));
        if (r < 2 || r > n + t) return ! 1;
        $("#piclist3 ul").animate({
            left: "+=110px"
        },
        200),
        r--
    }
    var e = 0,
    t = 5,
    n = $("#originalpic3 img").length,
    r = 1;
    $("#originalpic3 img").eq(0).show(),
    $.browser.msie && $.browser.version == "6.0" && !$.support.style && $("#aPrev3,#aNext3").css("height", $("#originalpic3 img").eq(0).height() + "px"),
    $(".thumbpic3 li").eq(0).addClass("hover2"),
    $(".thumbpic3 tt").each(function(e) {
        var t = e + 1 + "/" + n;
        $(this).html(t)
    }),
    $(".bntnext3,#aNext3").click(function() {
        s()
    }),
    $(".bntprev3,#aPrev3").click(function() {
        o()
    }),
    $(".thumbpic3 li").click(function() {
        e = $(".thumbpic3 li").index(this),
        i(e)
    })
})

// 改左侧菜单
$(function(){
    init()
    function init(){
        // 初始化子导航的展开
        $('.left_bar a').each(function(){
            $(this).attr('data-sel','true')
            $(this).parent().parent().find('span a').addClass('up')
        })
        // 控制导航的初始图标
        $('.left_bar li').each(function(){
            if($(this).children().length == 1){
                $(this).find('span a').css('background','none')
            }else{
                $(this).find('span a').attr('data-link','true')
            }
        })
        // 屏蔽原来默认的链接
        $('.left_bar ul span a[data-link="true"]').click(function(e){
            e.preventDefault();
            if($(this).attr('data-sel') != 'true'){
                $(document).trigger('obtain.show',$(this))
            }else{
                $(document).trigger('obtain.hide',$(this));
            }
        })
    }
    // 折叠所有的导航
    $(document).on('obtain.hide',function(e,param){
        var _this = $(param);
        var _height = 36*_this.parents('li').find('.news-fb a').length,
            _dom = _this.parents('li').find('.news-fb');
        _dom.show().css('overflow','hidden').animate({'height':'0','padding':'0'}).parent().find('span a').removeClass('up')
        _this.attr('data-sel','')
    })
    // 展开特定导航
    $(document).on('obtain.show',function(e,param){
        var _this = $(param);
        var _height = 36*_this.parents('li').find('.news-fb a').length,
            _dom = _this.parents('li').find('.news-fb');
        _dom.show().animate({'height':_height,'padding':'10px 0'}).parent().find('span a').addClass('up')
        _this.attr('data-sel','true')
    })
})
// JavaScript Document
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


$(function(){
	$('.reviewsUl').find('li').click(function(){
		  $(this).addClass('on').siblings().removeClass('on');
		  $('.reviewsList').hide();
		  $('.reviewsList').eq($(this).index()).show();
	  });
});



$(function(){
	$('.pShow').find('i').click(function(){
		  $('.dataB').show();
		  $('.pShow').hide();
	 });
	 $('.dataB').find('i').click(function(){
		  $('.pShow').show();
		  $('.dataB').hide();
	 });
});



$(function(){
	$('.dl-select2 dd').find('a').click(function(){
		  $(this).addClass('on');
	  });
});















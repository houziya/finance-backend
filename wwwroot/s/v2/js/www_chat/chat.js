// JavaScript Document
$(function(){
	$('.left_bar li').click(function(){
		  $(this).addClass('on').siblings().removeClass('on');
	});
});

$(function(){
    init()
    function init(){
        // 初始化子导航的展开
        /*$('.left_bar a').each(function(){
            $(this).attr('data-sel','true')
            $(this).parent().parent().find('span a').addClass('up')
        })*/
        // 控制导航的初始图标
        $('.left_bar li').each(function(){
            if($(this).children().length == 1){
                $(this).find('span a').css('background','block')
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


    //默认表情框隐藏
    window.onload = function() { 
        $(".icons").hide();
    }

    $(function(){
        //表情框弹出及隐藏
        $(".img-list-li01").click(function(){
            $(".icons").toggle();
        })
      
        //好友列表鼠标划上显示及隐藏删除等按钮
        $(".r-ul li").hover(function(){
            $(this).find("a").toggle();
        })

    })



})
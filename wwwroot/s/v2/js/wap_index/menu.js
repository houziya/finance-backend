/*菜单弹层*/
function sub_form(){
    $('#search_form').submit();
}
$(function(){
    $('#menu-show').click(function(){
        $('#menuShow').show();
    });
    $('.mask_close').click(function(){
        $('#menuShow').hide();
    });
    $('#dosubmit').click(function(){
        sub_form();
    });
    $('.menu-list-city a').click(function(){
        $('#city').val($(this).attr('data-city'));
        new sub_form();
    });
    $('.menu-list-ul li a').click(function(){
        $('#trade_one').val($(this).attr('data-trade'));
        new sub_form();
    });

    $('#screening-show').click(function(){
        $('#screeningShow').show();
    });
    $('.mask-close').click(function(){
        $('#screeningShow').hide();
    });
    $('#screeningShow dl dd a').click(function(){
        $('#order').val($(this).attr('data-order'));
        $('#sort').val($(this).attr('data-sort'));
        $('#search_form').submit();
    });

    $('.index-nav li').click(function(){
        $(this).addClass('on').siblings().removeClass('on');
        $('.index-box').hide();
        $('.index-box').eq($(this).index()).show();
    });

    $('.menu-list-nav li').click(function(){
        $(this).addClass('on').siblings().removeClass('on');
        $('.menu-list').hide();
        $('.menu-list').eq($(this).index()).show();
    });

});
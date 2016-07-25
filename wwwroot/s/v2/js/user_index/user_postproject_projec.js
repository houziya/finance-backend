/*我的账户安全设置手机验证——倒计时*/
function mobile_text()
{
    var time=60,
        timer=null;
    $('.mobile_code').hide();
    $('.m_yanzheng').show().text(time+'秒后重试');
    timer=setInterval(function(){
        time--;
        if(time<1){
            clearInterval(timer);
            $('.m_yanzheng').hide();
            $('.mobile_code').show().text('重新获取验证码');
            time=60;
        }else{
            $('.m_yanzheng').text(time+'秒后重试');
        }

    },1000)
}
/*我的账户安全设置邮箱验证——倒计时*/
function email_text()
{
    var time=60,
        timer=null;
    $('.email_code').hide();
    $('.e_yanzheng').show().text(time+'秒后重试');
    timer=setInterval(function(){
        time--;
        if(time<1){
            clearInterval(timer);
            $('.e_yanzheng').hide();
            $('.email_code').show().text('重新获取验证码');
            time=60;
        }else{
            $('.e_yanzheng').text(time+'秒后重试');
        }
    },1000)
}



$(function(){
    $(".tab_pie li").on('click',function(){
        var index=$(this).index();
        $(this).addClass('tab_hei').siblings().removeClass('tab_hei');
        $('.dian_intro').eq(index).addClass('tab_show').siblings().removeClass('tab_show');
    });

    $('.radio_list').on('click','label',function(){
        $(this).addClass('lab_hi').siblings().removeClass('lab_hi');
        /*$(this).prev('input')*/
    });

	/*
    $('.more_box a').on('mouseover',function(){
        var index=$(this).index();
        $(this).addClass('now').siblings().removeClass('now');
        $('.information_tab').eq(index).addClass('information_show').siblings().removeClass('information_show');
    });
	*/

    $('.check').on('click','span',function(){
        if($(this).hasClass('on')){
            $(this).addClass('check').removeClass('on');
        }else{
            $(this).removeClass('check').addClass('on');
        }
    });




    /*$("#coorproject_box li").on("mouseover",function(){
        var k=$(this).index();
        $(this).addClass('li_on').siblings().removeClass('li_on');
        $("#list_nei").stop().animate({"left":-k*750})
    });*/
    




    /*我的账户安全设置验证——倒计时*/
    $('.yanzheng').on('click',function(){
        var time=60,
            timer=null;
        timer=setInterval(function(){
            time--;
            if(time<0){
                clearInterval(timer);
                $('.yanzheng').text('重新获取验证码');
                time=60;
            }else{
                $('.yanzheng').text(time+'秒后重试');
            }
        },1000)
    });

    /*消费明细*/
    $('.fen_list a').on('click',function(){
        $(this).addClass('curhei').siblings().removeClass('curhei');
    });


        /*消息中心tab切换*/
    $("#coorproject_box li").on("click",function(){
        $(this).addClass('li_on').siblings().removeClass('li_on');
        $("#list_nei table").eq($(this).index()).show().siblings().hide();
        $('.correlation_list').css('height',$("#list_nei table").eq($(this).index()).height());
        $('.project_correlation').css('height',$("#list_nei table").eq($(this).index()).height());
    });


});




/*消息中心tab切换*/
$(document).ready(function(){
    $('.correlation_list').css('height',$("#list_nei table:eq(0)").height());
    $('.correlation_list').css('min-height',($("#coorproject_box li").index()+1) * 55);
    $('.project_correlation').css('height',$("#list_nei table:eq(0)").height());
    $('.project_correlation').css('min-height',($("#coorproject_box li").index()+1) * 55);
});
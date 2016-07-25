
$(function(){
    $('#tab_list li').on('click',function(){
        var index=$(this).index();
        $(this).addClass('current').siblings().removeClass('current');
        $('.show').eq(index).addClass('tab_show').siblings().removeClass('tab_show');
    });

    $('#list_p p').on('click',function(){
        $(this).next().slideToggle(0);
    });

	/*
    $(window).on('scroll',function(){
        var top=$(document).scrollTop(),
            fixedLayer=$('#fixed-layer'),
            pos=parseInt(($('body').width()-$('#row').outerWidth())/2);

        var isIE=!!window.ActiveXObject;
        var isIE6=isIE&&!window.XMLHttpRequest;
        if (isIE){
            if (isIE6){
                if(top>=345){
                    fixedLayer.addClass('pos').css({
                        'right':pos+'px',
                        'top':top+'px'
                    });
                }else{
                    fixedLayer.removeClass('pos');
                }
            }
        }else{
            if(top>=345){
                fixedLayer.addClass('fixed').css('right',pos+'px');
            }else{
                fixedLayer.removeClass('fixed').css('right',0);
            }
        }
    })
	*/

})


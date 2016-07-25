
$(function(){
	$('.user-nav li').click(function(){
		$(this).addClass('on').siblings().removeClass('on');
	});
});

$(function(){
	$('.state-nav a').click(function(){
		$(this).addClass('on').siblings().removeClass('on');
	});
});

$(function(){
	$('.bank-card2 em').click(function(){
		$('.bank-card2').hide();
	});
});


$(function(){
	$('.cancel-btn').click(function(){
		$(".modal").hide();
		$(".modal-backdrop").hide();
	});
});


$(function(){
   $('.check').on('click','span',function(){
        if($(this).hasClass('on')){
            $(this).addClass('check').removeClass('on');
        }else{
            $(this).removeClass('check').addClass('on');
        }
    });
});













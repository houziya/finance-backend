// JavaScript Document

//$(function(){
//	$('.details-nav li').click(function(){
//		$(this).addClass('on').siblings().removeClass('on');
//		$('.content-list').hide();
//		$('.content-list').eq($(this).index()).show();
//	});
//});

$(function(){
	$('.deliver-text span').click(function(){
		$(this).parent().siblings(".reply-box ").toggle();
	});
});


/*预约认购加减份数*/
//$(function(){
//    $('.number_up').click(function(){
//         var sum = 1;
//         sum = sum + Number($('.inputVal').val());
//         $('.inputVal').val(sum);
//    });
//    $('.number_down').click(function(){
//         if($('.inputVal').val() <= 1) {
//               return false;
//          }
//          var sum = 1;
//          sum = Number($('.inputVal').val()) - 1;
//          $('.inputVal').val(sum);
//    });
//    $('.inputVal').blur(function(){
//         var giftNum = 8;
//         if(($('.inputVal').val() <= 1) || ($('.inputVal').val() == '')) {
//              $('.inputVal').val(1)
//              return false;
//         }
//    });
//})

/*$(function(){
	$('.cancel-btn').click(function(){
		$(".modal").hide();
		$(".modal-backdrop").hide();
	});
});
*/























// JavaScript Document

$(function(){
	$('.touch-fz li').click(function(){
		  $(this).toggleClass('on').siblings().removeClass('on');
	});
});


/*开关闭按钮*/
window.onload=function(){
	var div2=document.getElementById("div2");
	var div1=document.getElementById("div1");
	div2.onclick=function(){
	  div1.className=(div1.className=="close1")?"open1":"close1";
	  div2.className=(div2.className=="close2")?"open2":"close2";
	}
}


/*预约认购加减份数*/
$(function(){
    $('.number_up').click(function(){
         var sum = 1;
         sum = sum + Number($('.inputVal').val());
         $('.inputVal').val(sum);
    });
    $('.number_down').click(function(){
         if($('.inputVal').val() <= 1) {
               return false;
          }
          var sum = 1;
          sum = Number($('.inputVal').val()) - 1;
          $('.inputVal').val(sum);
    });
    $('.inputVal').blur(function(){
         var giftNum = 8;
         if(($('.inputVal').val() <= 1) || ($('.inputVal').val() == '')) {
              $('.inputVal').val(1)
              return false;
         }
    });
})


/*我要认购弹层*/
$(function(){	
	$(".sellBtn2").click(function(){
		$("#sellShow2").show();
	});
	$(".sellBtn1").click(function(){
		$("#sellShow1").show();
	});
	$(".sellBtn3").click(function(){
		$("#sellShow3").show();
	});
	$(".sellBtn4").click(function(){
		$("#sellShow4").show();
	});
	$(".sell_close").click(function(){
		$("#sellShow1").hide();
		$("#sellShow2").hide();
		$("#sellShow3").hide();
		$("#sellShow4").hide();
	});
	$(".close-div").click(function(){
		$("#sellShow1").hide();
		$("#sellShow2").hide();
		$("#sellShow3").hide();
		$("#sellShow4").hide();
	});
	$(".yesShow").click(function(){
		$("#sellShow2").show();
		$("#sellShow1").hide();
	});
});

/*签到*/
$(function(){	
	$(".signed-box span").click(function(){
		$(".signed-box").hide();
	});
});


/*拼人脉专题*/
$(function(){
	$('.invite-ul li').click(function(){
		$(this).addClass('on').siblings().removeClass('on');
		$('.speak-list').hide();
		$('.speak-list').eq($(this).index()).show();
	});
});
$(function(){
	$(".btn-invite").click(function(){
		$("#sellShow8").show();
	});
	$("#sellShow8").click(function(){
		$("#sellShow8").hide();
	});
});

/*投票*/









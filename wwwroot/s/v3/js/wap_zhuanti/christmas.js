// JavaScript Document


$(function(){
    $(".crus-list li").click(function(){
		$(this).addClass("on");
		$("#sellShow1").show();
	});
})


$(function(){
	$(".crusShow").click(function(){
		$("#sellShow3").show();
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
});






















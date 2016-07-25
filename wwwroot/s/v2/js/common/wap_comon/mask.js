$(function(){
	$(".mask_menu_icon").on("touchstart",function(event){
		$('.mask').show();
		$('.big_box_cont').hide();
		return false;
	});
	$(".mask_close").on("touchstart",function(event){
		$('.big_box_cont').show();
		$('.mask').hide();
		return false;
	});
})

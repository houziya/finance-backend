// JavaScript Document

/*弹层*/
$(function(){
	
	$(".sellBtn2").click(function(){
		$("#sellShow2").show();
	});
	
	$(".sell_close").click(function(){
		$("#sellShow").hide();
		$("#sellShow2").hide();
		$("#sellShow3").hide();
	});
	$(".close-div").click(function(){
		$("#sellShow").hide();
		$("#sellShow2").hide();
		$("#sellShow3").hide();
	});
});





/*评论弹层*/
$(function(){
	$('.reply').click(function(){
		  $('#gradeShow').show();
	});
	$('.grade-close').click(function(){
		  $('#gradeShow').hide();
	});
});

$(function(){
	$('#gradeSoon2').click(function(){
		  $('#gradeShow2').show();
	});
	$('.grade-close').click(function(){
		  $('#gradeShow2').hide();
	});
});






function scoreSelecter(o){
		var this_select = o.index()+1;
		var last_select = o.parent().find('.on').length; //统计元素个数
		if(o.attr('class') == 'on'){
			//全部取消样式
			if(this_select < last_select){
				o.nextAll().removeClass('on');
		  		//$(this).prevAll().addClass('on');
			}
		}else{
		 	o.toggleClass('on');
			o.nextAll().removeClass('on');
		  	o.prevAll().addClass('on');
		}
		
}




/*马上评级*/
$(function(){
	$('.gradeSoon').click(function(){
		  $('#gradeShow').show();
	});
	$('.grade-close').click(function(){
		  $('#gradeShow').hide();
	});
});










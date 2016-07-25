// JavaScript Document

$(function(){
	$('#gradeSoon').click(function(){
		  $('#gradeShow').show();
	});
	$('#gradeClose').click(function(){
		  $('#gradeShow').hide();
	});
});

/*$(function(){
	$('.starUl li').click(function(){
		  $(this).toggleClass('on');
		  $(this).prevAll().toggleClass('on');
	});
});*/

$(function(){
	$('.sell-city p').click(function(){
		  $('.city-xl').toggle();
	});
	
});
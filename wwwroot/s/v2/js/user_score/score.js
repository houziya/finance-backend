$(function(){
	$(".check_re").change(function(){
		var scoretype = $("#score_type").val();
		var pid = $("#score_pid").val();
		window.location.href = "/score/scoreuser/pid/"+pid+"/scoretype/"+scoretype+"/#md";
	})
	$(".check_se").change(function(){
		var type = 2;
		var pid = $("#score_pid").val();
		window.location.href = "/score/scoreuser/pid/"+pid+"/type/2/#md";
	})
	$("#score_pro_re").change(function(){
		var pid_p = $("#score_pro_re").val();
		var pid_i = $("#score_pro_se").val();
		if(pid_i > 0)
		{
			window.location.href = "/score/scoreproject/pid_p/"+pid_p+"/pid_i/"+pid_i+"/#md";
		}else
		{
			window.location.href = "/score/scoreproject/pid_p/"+pid_p+"/#md";
		}
	})
	$("#score_pro_se").change(function(){
		var pid_i = $("#score_pro_se").val();
		var pid_p = $("#score_pro_re").val();
		if(pid_p > 0)
		{
			window.location.href = "/score/scoreproject/pid_i/"+pid_i+"/pid_p/"+pid_p+"/#md";
		}else
		{
			window.location.href = "/score/scoreproject/pid_i/"+pid_i+"/#md";
		}
	})
});

//防止重复提交
var _id = "";


// 回复
function ajaxreply(id)
{
	if(id == false)
	{
		popBox('参数不全','error');
	}
	if(_id == id)
	{
		return ;
	}
	var content = $("#content_"+id).val();
	_id = id;
	$.post('/score/ajaxscorereply',{'id':id,'content':content},function(data){
		if(data.status == 1)
		{
	//		popBox(data.msg,'success');
			$("#replybtn_"+id).click();
			$("#replybtn_"+id).hide();
			$("#reply_"+id).html(data.info);
			_id = "";
		}else
		{
			popBox(data.msg,'error');
			_id = "";
		}
	})
}

//给项目打分
function ajaxproscore(id,type,score)
{
	if(id == false || type == false || score == false)
	{
		popBox('参数不全','error');
	}
	if(_id == id)
	{
		return ;
	}
	if(!confirm('此项打'+score+'分，是否确定'))
	{
		return;
	}
	_id = id;
	$.post('/score/ajaxprojectscore',{'id':id,'type':type,'score':score},function(data){
		if(data.status == 1)
		{
	//		popBox(data.msg,'success');
			$("#html_"+type).html(data.html);
			$("#img_"+type).html(data.img);
			_id = "";
		}else
		{
			popBox(data.msg,'error');
			_id = "";
		}
	})
}

//评分
function ajaxscore(id)
{
	if(_id == id)
	{
		return ;
	}
	if(id == false)
	{
		popBox('参数不全','error');
	}
	var score=$('input:radio[name="scoretype_'+id+'"]:checked').val();
	if(score == null)
	{
		popBox('评级后才能提交','error');
		return;
	}
	var content = $("#content_"+id).val();
	_id = id;
	$.post('/score/ajaxscoreprouser',{'id':id,'score':score,'content':content},function(data){
		if(data.status == 1)
		{
	//		popBox(data.msg,'success');
			$("#scorecon_"+id).html(data.info);
			$("#scoredate_"+id).html(data.date);
			$("#scorebtn_"+id).click();
			$("#scorebtn_"+id).hide();
			_id = "";
		}else
		{
			popBox(data.msg,'error');
			_id = "";
		}
	})
}


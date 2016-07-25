jQuery.fn.uploadFile=function(data,callback) {
	var params = {
		'data': '',//上传参数
		'field': 'attach', //上传对象（上传文本框）
		'image': 'image',// 回调图片对象
		'resize':[0] //重设剪切图片
	};
	if (typeof callback != 'undefined' & callback instanceof Function) {
		
	}else {
		callback();
	}
	$.extend(params, data);	
	$(this).live('change',function(){

		$.ajaxFileUpload ({
			url:'/file/ajaxupload/'+'?random='+parseInt(100000*Math.random()),
			secureuri:false,
			fileElementId:params.field,
			dataType: 'json',
			data:params,
			success: function(res,status){
				
				//回调函数处理
				callback(res);
			},error: function (data, status, e){
				alert(e);
			}
		});
	});
}

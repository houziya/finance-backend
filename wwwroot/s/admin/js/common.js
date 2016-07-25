//确认操作
$(function(){
	$('.confirm').click(function(){
		var url = $(this).attr('href');
		var msg = $(this).attr('msg');
		if(!msg) msg = '请确认是否进行此操作？';		
		msg = msg.replace(/\\n/g,String.fromCharCode(10));
		if(confirm(msg)) return true;
		return false;
	});
});

//确认操作
function confirmurl(url,message) {
	if(confirm(message)) {
		redirect(url)
	}else{
		return false;
	}
}

function redirect(url) {
	location.href = url;
}

//滚动条
/*
$(function(){
	$(":text").addClass('input-text');
});
*/

//选项卡切换
function swap_tab(name,cls_show,cls_hide,count,cur){
    for(i=1;i<=count;i++){
		if(i==cur){
			 $('#div_'+name+'_'+i).show();
			 $('#tab_'+name+'_'+i).attr('class',cls_show);
		}else{
			 $('#div_'+name+'_'+i).hide();
			 $('#tab_'+name+'_'+i).attr('class',cls_hide);
		}
	}
}

/**
 * 全选checkbox,注意：标识checkbox id固定为为check_box
 * @param string name 列表check名称,如 uid[]
 */
function selectall(name) {
	if ($("#check_box").attr("checked")!='checked') {
		$("input[name='"+name+"']").each(function(i) {
			this.checked=false;
		});
	} else {
		$("input[name='"+name+"']").each(function(i) {
			this.checked=true;
		});
	}
}

//打开新窗口
function openwinx(url,name,w,h) {
	if(!w) w=screen.width-4;
	if(!h) h=screen.height-95;
    window.open(url,name,"top=100,left=400,width=" + w + ",height=" + h + ",toolbar=no,menubar=no,scrollbars=yes,resizable=yes,location=no,status=no");
}

//打开页面内新弹窗
function opendialog(url,name,issubmit,id,w,h) {
	w = w ? w : 700;
	h = h ? h : 500;
	issubmit = issubmit ? issubmit : 0;
	id = id ? id : 'info';
	window.top.art.dialog({title:name, id:id, iframe:url, width:w, height:h, lock:true},
	function(){
		if(issubmit==0) {
			window.top.art.dialog({id:id}).close();
			return true;
		} else {
			var d = window.top.art.dialog({id:id}).data.iframe;
			var form = d.document.getElementById('dosubmit');
			form.click();
			return false;
		}		
	},
	function(){
			window.top.art.dialog({id:id}).close();
	});void(0);
}

//字符长度
function strlen(str) {
	return ($.browser.msie && str.indexOf('\n') != -1) ? str.replace(/\r?\n/g, '_').length : str.length;
}

//字符判断
function strlen_verify(obj, checklen, maxlen) {
	var v = obj.value, charlen = 0, maxlen = !maxlen ? 200 : maxlen, curlen = maxlen, len = strlen(v);
	var n = charset == 'utf-8' ? 1 : 1;
	for(var i = 0; i < v.length; i++) {
		if(v.charCodeAt(i) < 0 || v.charCodeAt(i) > 255) {
			curlen -= n;
		}
	}
	if(curlen >= len) {
		$('#'+checklen).html(curlen - len);
	} else {
		obj.value = mb_cutstr(v, maxlen, true);
	}
}

//字符截取
function mb_cutstr(str, maxlen, dot) {
	var len = 0;
	var ret = '';
	var dot = !dot ? '...' : '';
	maxlen = maxlen - dot.length;
	for(var i = 0; i < str.length; i++) {
		len += str.charCodeAt(i) < 0 || str.charCodeAt(i) > 255 ? (charset == 'utf-8' ? 3 : 2) : 1;
		if(len > maxlen) {
			ret += dot;
			break;
		}
		ret += str.substr(i, 1);
	}
	return ret;
}

//检查后缀是否合法，默认只检查是否图片后缀
function isext(url,opt){
	var sTemp;
	var b = false;
	opt = opt ? opt : "jpg|gif|png|bmp|jpeg";
	var s=opt.toUpperCase().split("|");
	for (var i=0;i<s.length ;i++ ){
		sTemp=url.substr(url.length-s[i].length-1);
		sTemp=sTemp.toUpperCase();
		s[i]="."+s[i];
		if (s[i]==sTemp){
			b=true;
			break;
		}
	}
	return b;
}


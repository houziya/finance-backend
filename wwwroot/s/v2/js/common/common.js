/**
 * 弹窗提示
 * @param msg 提示内容
 * @param type 提示类型(success,error,info)
 * @param time_num 倒计时关闭
 * @param call_fun 回调函数
 */
 var callfun = null;
function popBox(msg,type,time_num,call_fun,is_static){
	callfun = call_fun; // 直接使用在页面中多个弹出框的时候会出现回调函数无法正确调用的情况 modify by quanzhijie at 2015/4/27 21:07
    is_static = is_static||false;
	if(!msg) return false;
	if(!time_num) time_num = 3;
	var _match = /^[0-9]+.?[0-9]*$/;
	var _str = '';
	_str = '<div class="modal fade" id="popBox" tabindex="-1" role="dialog" aria-hidden="true">';
	_str += '<div class="modal-dialog modal-alert">';
	_str += '<div class="modal-content">';
	_str += '<div class="modal-body">';
	_str += '<button type="button" class="modal-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
	_str += '<table>';
	_str += '<tr id="popBoxContent">';
	_str += '</tr>';
	_str += '</table>';
	_str += '</div>';
	_str += '</div>';
	_str += '</div>';
	_str += '</div>';
	if($('#popBox').is('#popBox') == false){
		$('body').append(_str);
        if(is_static){
            $("#popBox").modal({
                keyboard: false,
                backdrop: 'static',
                show: false
            });
        }
		if(typeof(callfun) == 'function'){
			$('#popBox').on('hidden.bs.modal', function (e) {callfun();});
		}
	}

	var _tips,_tips2;
	_tips2 = '<p>'+ msg +'</p>';
	if(_match.test(time_num)){
		_tips2 += '<p class="text-muted">消息将在 <span id="popTimeNum">'+time_num+'</span> 秒后消失。</p>';
	}
	if(type == 'error'){
		_tips = '<td width="80" class="pop-error">&nbsp;</td><td><h2>失败</h2>'+ _tips2 +'</td>';
	}else if(type == 'info'){
		_tips = '<td width="80" class="pop-info">&nbsp;</td><td><h2>提示</h2>'+ _tips2 +'</td>';
	}else{
		_tips = '<td width="80" class="pop-success">&nbsp;</td><td><h2>成功</h2>'+ _tips2 +'</td>';
	}
	$('#popBoxContent').html(_tips);
	$('#popBox').modal('toggle');
	if(_match.test(time_num)){
		var timer = '';
		$("#popTimeNum").html(time_num);
		timer = setInterval(function(){
			var count = parseInt(parseInt($("#popTimeNum").html()) - 1);
			if(count > 0){
				$("#popTimeNum").html(count);
			}else{
				clearInterval(timer);
				$('#popBox').modal('hide');
			}
		},1000);
	}else{
		
	}

}

function appPopBox(msg,type){
    var is_static = false;
    var _str = '';
    _str = '<div class="modal fade" id="popBox" tabindex="-1" role="dialog" aria-hidden="true">';
    _str += '<div class="modal-dialog modal-alert">';
    _str += '<div class="modal-content">';
    _str += '<div class="modal-body">';
    //_str += '<button type="button" class="modal-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
    _str += '<table>';
    _str += '<tr id="popBoxContent">';
    _str += '</tr>';
    _str += '</table>';
    _str += '</div>';
    _str += '</div>';
    _str += '</div>';
    _str += '</div>';
    if($('#popBox').is('#popBox') == false){
        $('body').append(_str);
        if(is_static){
            $("#popBox").modal({
                keyboard: false,
                backdrop: 'static',
                show: false
            });
        }
        if(typeof(callfun) == 'function'){
            $('#popBox').on('hidden.bs.modal', function (e) {callfun();});
        }
    }
    var _tips,_tips2;
    _tips2 = '<p>'+ msg +'</p>';
    if(type == 'error'){
        _tips = '<td width="80" class="pop-error">&nbsp;</td><td><h2>失败</h2>'+ _tips2 +'</td>';
    }else if(type == 'info'){
        _tips = '<td width="80" class="pop-info">&nbsp;</td><td><h2>提示</h2>'+ _tips2 +'</td>';
    }else{
        _tips = '<td width="80" class="pop-success">&nbsp;</td><td><h2>成功</h2>'+ _tips2 +'</td>';
    }
    $('#popBoxContent').html(_tips);
    $('#popBox').modal('toggle');
}

/*tab切换插件*/
TabClass=function(a){this.tabName=a.tabName;this.cntName=a.cntName;this.number=a.number;this.tabShowCls=a.tabShowCls;if(a.tabHiddenCls){this.tabHiddenCls=a.tabHiddenCls}else{this.tabHiddenCls=""}if(a.cntShowCss){this.cntShowCss=a.cntShowCss}else{this.cntShowCss="block"}this.show=function(b){for(var c=0;c<this.number;c++){if(c!=b){$("#"+this.tabName+"_"+c).removeClass(this.tabShowCls);if(this.tabHiddenCls){$("#"+this.tabName+"_"+c).addClass(this.tabHiddenCls)}$("#"+this.cntName+"_"+c).css("display","none")}else{if(this.tabHiddenCls){$("#"+this.tabName+"_"+c).removeClass(this.tabHiddenCls)}$("#"+this.tabName+"_"+c).addClass(this.tabShowCls);$("#"+this.cntName+"_"+c).css("display",this.cntShowCss)}}}};


/*pc_v3.0登录后*/
$(function(){
	$('.after-landing-ul li').hover(function(){
		$(this).addClass('on').siblings().removeClass('on');
	});
});
$(function(){
	$('.after-landing-list').hover(function(){
		$('.after-landing-ul').toggle();
	});
});

/*pc_v3.0底部友情链接*/
//$(function(){ 
//	$(".footer-list-b span").toggle(function(){ 
//	    $(this).addClass('on');
//	    $('.links-list').show(); 
//	    var t = $(window).scrollTop();
//        $('body,html').animate({'scrollTop':t+200},100);
//	},function(){ 
//	    $(this).removeClass('on');
//	    $('.links-list').hide(); 
//	}); 
//}); 




//pc_v3.0返回顶部
$(function(){  
   //当滚动条的位置处于距顶部100像素以下时，跳转链接出现，否则消失  
   $(function () {  
		$(window).scroll(function(){  
			if ($(window).scrollTop()>100){  
				$("#top-btn-li").fadeIn(1500);  
			}  
			else  
			{  
				$("#top-btn-li").fadeOut(1500);  
			}  
		});  
		//当点击跳转链接后，回到页面顶部位置  
		$("#top-btn-li").click(function(){ 
			$('body,html').animate({scrollTop:0},500);  
			return false;  
		});  
	});  
});  
//pc_v3.0右侧导航
$(".right-nav-list li").live('mouseover', function() {	
	    $(this).addClass('on');
		$('.nav-content').eq($(this).index()).show();
	}).live("mouseleave",function() {
		$(this).removeClass('on');
		$('.nav-content').eq($(this).index()).hide();
});	
/*pc_v3.0右侧导航*/
$(".right-top-ul li").live('mouseover', function() {	
	    $(this).addClass('on');
		$('.right-show').eq($(this).index()).show();
	}).live("mouseleave",function() {
		$(this).removeClass('on');
		$('.right-show').eq($(this).index()).hide();
});	
$(".right-top-ul2 li").live('mouseover', function() {	
	    $(this).addClass('on');
		$('.right-show2').eq($(this).index()).show();
	}).live("mouseleave",function() {
		$(this).removeClass('on');
		$('.right-show2').eq($(this).index()).hide();
});	


/*wap_v2.0头部*/
$(function(){
	$('.header-r p').click(function(){
		  $('.header-nav').toggle();
		  $('#header-mask').toggle();
		  $("body").css("overflow","hidden");		  
	});
});

/*wap_v2.0底部*/
$(function(){
	$('.footer-ul li').click(function(){
		$(this).addClass('on').siblings().removeClass('on');
	});
});

/**
 * 网站注册时候同意条框内容
 */
function getAgreeContent()
{
	var agreeContent = "";
		agreeContent += "<b>第1条  协议内容及签署</b>";
		agreeContent += "<p>1.1  本协议构成您与人人投网站（由北京飞度网络科技有限公司开发、运营）就网站使用服务所达成的协议。当您完成注册并在本服务协议前方“□”打√时，即表示您认可协议条款和条件，已同意受人人投网站网站服务协议约束，包括但不限于本协议及所有人人投网站已经发布的或将来可能发布的各类规则等。如果您不同意本协议，请不要在“□”打√，同时您将无权使用人人投网站的服务。</p>";
		agreeContent += "<p>1.2  您应当在使用人人投网站服务之前认真阅读全部协议内容。如您对协议有任何疑问的，可向人人投网站咨询。但无论您事实上是否在使用人人投网站服务之前认真阅读了本协议内容，只要您使用人人投网站服务，则本协议即对您产生约束，届时您不应以未阅读本协议的内容或者未获得人人投对您问询的解答等理由，主张本协议无效或要求撤销本协议。</p>";
		agreeContent += "<p>1.3  人人投网站有权根据需要不时地制订、修改本协议或各类规则，并以网站公示的方式进行公告，不再单独通知您。变更后的协议和规则一经在网站公布后，即时生效。如您不同意相关变更，应当立即停止使用人人投网站服务。如果您继续使用人人投网站服务，即表示您接受经修订的协议和规则。</p>";
		agreeContent += "<p>1.4  人人投网站提供的网络服务的所有权和最终解释权归北京飞度网络科技有限公司所有。本协议中，被许可使用人人投网站服务的注册会员均简称为“注册会员”。</p>";
		agreeContent += "<b>第2条  服务内容</b>";
		agreeContent += "<p>2.1  注册会员可以在网站上浏览公告、项目介绍、项目评论等网站开放信息，经过网站审核的认证投资人还可以查看项目内容页的详细信息、评论项目并进行在线投资。</p>";
		agreeContent += "<p>2.2  注册会员有权在指定板块发布符合人人投网站要求的信息。</p>";
		agreeContent += "<p>2.3  人人投网站运用自己的操作系统通过互联网使得注册会员可以在人人投网站上成功发布信息。</p>";
		agreeContent += "<b>第3条  注册会员的权利与义务</b>";
		agreeContent += "<p>3.1  注册会员有权在人人投网站的指定版块发布符合规定的信息。</p>";
		agreeContent += "<p>3.2  注册会员有义务确保其发布、评论的信息符合中华人民共和国的法律法规及人人投网站的相关规则的要求。</p>";
		agreeContent += "<p>3.3  注册会员有义务保证其向人人投网站提交及发布的信息是真实有效的，并将承担由于发布错误信息而造成的损失。</p>";
		agreeContent += "<p>3.4  除非取得人人投网站的事先书面同意。注册会员不得将其在本协议项下享有的权利与义务转让给任何第三方。</p>";
		agreeContent += "<b>第4条  人人投网站的权利和义务</b>";
		agreeContent += "<p>4.1  人人投网站将对每一个申请成为认证投资人的注册会员进行审核，并且有权拒绝注册会员申请成为认证投资人或者发布项目的要求。</p>";
		agreeContent += "<p>4.2  人人投网站会竭力确保特定信息（比如项目内容页的详细信息）只对有查看权限的注册会员开放。</p>";
		agreeContent += "<b>第5条  免责声明</b>";
		agreeContent += "<p>5.1  人人投网站仅为注册会员提供网络空间及技术服务，我们并不能完全确保注册会员提交的信息是完全无误的。</p>";
		agreeContent += "<p>5.2  如因人人投网站系统维护或升级而需暂停服务时，将事先公告。若因线路故障及非本公司控制范围内的其他硬件故障或其它不可抗力而导致暂停服务，那么暂停服务期间造成的一切不便与损失，本网站不负任何责任。</p>";
		agreeContent += "<p>5.3  人人投业务系统因下列状况无法正常运作，使您无法使用各项服务时，人人投不承担损害赔偿责任，该状况包括但不限于：</p>";
		agreeContent += "<p>5.3.1  人人投在门户网站公告之系统停机维护期间；</p>";
		agreeContent += "<p>5.3.2  服务器、电信设备、线路等硬件设施出现故障导致交易数据不能正常产生、传输；</p>";
		agreeContent += "<p>5.3.3  因台风、地震、海啸、洪水、停电、战争、恐怖袭击等不可抗力事件，导致人人投系统障碍不能正常为您提供服务；</p>";
		agreeContent += "<p>5.3.4  由于黑客攻击、电力系统问题、电信部门技术调整等其他方非因人人投自身所能控制原因而造成的服务中断或者延迟。</p>";
		agreeContent += "<p>5.4  本协议未涉及的问题参见国家有关法律法规，当本协议与国家法律法规冲突时，以国家法律法规为准。</p>";
		agreeContent += "<b>第6条  隐私权政策及安全</b>";
		agreeContent += "<p>6.1  人人投网站尊重并保护所有注册会员的个人隐私权。为了给您提供更准确、更有个性化的服务，人人投网站会按照本隐私权政策的规定使用和披露您的个人信息。但我们将以高度审慎的态度对待这些信息。除本隐私权政策另有规定外，在未征得您事先许可的情况下，人人投网站不会将这些信息对外披露或向第三方提供。但以下状况除外：</p>";
		agreeContent += "<p>6.1.1  根据法律的有关规定，或者行政或司法机构的要求，向第三方或者行政、司法机构披露；</p>";
		agreeContent += "<p>6.1.2  如您出现违反中国有关法律、法规或相关规则的情况，需要向第三方披露；</p>";
		agreeContent += "<p>6.2  人人投网站会不时更新本隐私权政策。</p>";
		agreeContent += "<p>6.3  为了更好地服务注册会员，人人投网站可能通过使用您的个人信息，向您提供您感兴趣的信息。</p>";
		agreeContent += "<p>6.4  人人投网站通过对注册会员登录密码进行加密等安全措施确保注册会员的隐私安全。</p>";
		agreeContent += "<b>第7条  知识产权</b>";
		agreeContent += "<p>7.1  注册会员的上传行为代表着注册会员或其代表的公司授权人人投网站对上传的信息享有不可撤销的永久的使用权和收益权，但注册会员或其代表的公司仍保有上传信息的所属权。</p>";
		agreeContent += "<p>7.2  注册会员有义务确保其发布的信息不侵犯任何第三方知识产权及其他权利，否则由此给第三方及人人投网站造成的损失将由注册会员承担。</p>";
		agreeContent += "<b>第8条  法律适用及争议解决</b>";
		agreeContent += "<p>8.1  本协议签署、效力、解释和执行以及争议之解决均应适用中国法律。</p>";
		agreeContent += "<p>8.2  对本协议任何条款的执行或解释所引起的任何争议，人人投网站和注册会员应尽最大努力友好协商解决；协商不成的，双方均可向人人投网站所在地的人民法院起诉。</p>";
		agreeContent += "<b>第9条  有效期及其他</b>";
		agreeContent += "<p>9.1  本协议有效期自注册会员点击同意之日起至注册会员注销账号之日止。</p>";
		agreeContent += "<p>9.2  如本协议的任何条款被视作无效或无法执行，不影响其余条款的效力。</p>";
		agreeContent += "<p>9.3  本协议标题是为了方便阅读所设，并非是对条款的定义、限制和解释。</p>";
	return agreeContent;
}



//顶部通览可展开收起效果
function AdvAuto(){
	if($(".dt_big").length>0){
		var a=1500;
		var b=3*1000;
		$('.dt_toSmall').click(function(){
			$(".dt_small").show();	
			$(".dt_big").hide();
		});
		$(".dt_big").delay(b).slideUp(a,function(){
			$(".dt_small").slideDown(a);
			//$(".dt_toBig").delay(a).fadeIn(0)
		});
		$(".dt_toSmall").delay(b).fadeOut(0)
	}
}


/**
 * 检测flash播放器插件安装情况
 */
function flashChecker()
{
    var hasFlash = 0;
    if(document.all) {
        try {
            new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
            hasFlash = 1;
        } catch (e) {}
    } else {
        if (navigator.plugins && navigator.plugins.length > 0) {
            if (navigator.plugins["Shockwave Flash"]) {
                hasFlash = 1;
            }
        }
    }
    return hasFlash;
}

$(function(){
	$('.enroll-list a').click(function(){
		$(this).toggleClass('a-bg');
	});
});











$(document).ready(function(){

	$.formValidator.initConfig({formid:"form1",onerror:function(msg){/*popBox(msg,'error');*/return false;},onsuccess:function(){
	    if(!$("input[type='checkbox'][id='xieyi_check']").is(':checked')) {
	        popBox('请选择注册协议后再提交','info');return false;
	    }
	    $('.btn_zhuce').attr('disabled', 'disabled');
	    $.post('/register/add',{realname:$('#realname').val(),u_body_num:$('#u_body_num').val(),mobile:$('#mobile').val(),mobilecode:$('#telcode').val(),password:$('#password').val(),password2:$('#repassword').val(),code:$('#verifyCode').val(),xieyi:$('#xieyi_check').val(),btn_submit:'register'},function(data){
	        if(data.status==1) {
	        	//执行回调地址
	        	$("body").append(data.data);
	            //$('#myModa_header_box_login button span').click();
	            $('#myModal_header_box_form_qrcodes_id').click();
	            setTimeout(function(){ window.location.href="/index/index"; },35000)
	            //popBox(data.msg,'success',2);successJump
	        } else {
	        	$('.btn_zhuce').removeAttr('disabled');
	            popBox(data.msg,'error');
	        }
	    })
	    return false;
	}});
	/*$("#username").formValidator({onshow:"用户名由大小写字母6-30位组成",onfocus:"用户名由大小写字母6-30位组成",oncorrect:"该用户名可以注册"})
	    .inputValidator({min:6,max:30,onerror:"你输入的用户名非法,请确认"})
	    .functionValidator({
	        fun:function(val,obj){
	            var reg2 = /^[a-zA-Z0-9]{6,30}/;
	            if(!reg2.test($.trim(val))) {
	                return false;
	            }
	            return true;
	        },
	        onerror : "用户名由大小写字母6-30位组成"
	    })
	    .ajaxValidator({
	    type : "get",
		url : "/register/checkusername",
		datatype : "json",
		success : function(data){
	        if( data.status == "1" ) {
	            return true;
			} else {
	            return false;
			}
		},
		buttons: $("#button"),
		error: function(){popBox('服务器没有返回数据，可能服务器忙，请重试','error');},
		onerror : "该用户名已存在，请更换用户名",
		onwait : "正在对用户名进行合法性校验，请稍候..."
	});*/
	$("#mobile").formValidator({onshow:"请输正确的11位手机号码",onfocus:"请输入正确的手机号码",oncorrect:"该手机号可以注册"})
	    .inputValidator({min:11,max:11,onerror:"你输入的手机号非法,请确认"})
	    .functionValidator({
	        fun:function(val,obj){
	            if(!/^1[0-9]\d{9}$/i.test(val)) {
	                return false;
	            }
	            return true;
	        },
	        onerror : "手机号码格式不正确"
	    })
	    .ajaxValidator({
	        type : "get",
	        url : "/register/checkmobile",
	        datatype : "json",
	        success : function(data){
	            if( data.status == "1" ) {
	                return true;
	            } else {
	                return false;
	            }
	        },
	        buttons: $("#button"),
	        error: function(){ popBox('服务器没有返回数据，可能服务器忙，请重试','error');},
	        onerror : "手机号已存在,请重新输入",
	        onwait : "正在对手机号进行合法性校验，请稍候..."
	});
	$("#telcode").formValidator({onshow:"请输入手机验证码",onfocus:"手机验证码不能为空",oncorrect:" "}).inputValidator({min:1,empty:{leftempty:false,rightempty:false,emptyerror:"手机验证码不能有空符号"},onerror:"手机验证码不能为空,请确认"});
	$("#verifyCode").formValidator({onshow:"请输入验证码",onfocus:"验证码不能为空",oncorrect:" "}).inputValidator({min:1,empty:{leftempty:false,rightempty:false,emptyerror:"验证码不能有空符号"},onerror:"验证码不能为空,请确认"});
	$("#password").formValidator({onshow:"请输入密码",onfocus:"密码不能为空",oncorrect:" "}).inputValidator({min:6,empty:{leftempty:false,rightempty:false,emptyerror:"密码两边不能有空符号"},onerror:"密码不能为空,请确认"});
	$("#realname").formValidator({onshow:"请输入真实姓名",onfocus:"请输入真实姓名",oncorrect:" "})
	  .regexValidator({regexp:"chinese2",datatype:"enum",onerror:"请输入真实姓名"})
	$("#u_body_num").formValidator({onshow:"请输入身份证号",onfocus:"请输入15或18位身份证号",oncorrect:" "})
	  .ajaxValidator({
		    type : "get",
		    url : "/register/checkidcard",
		    datatype : "json",
		    success : function(data){
				if(data.status == 1)
				{
					return true;
				}else
				{
					return false;
				}
			},
			buttons: $("#button"),
//			error: "",
		    onerror : "身份证不正确或已被认证",
		    onwait : " ",
		});
	//$("#repassword").formValidator({onshow:"请输入重复密码",onfocus:"两次密码必须一致哦",oncorrect:"密码一致"}).inputValidator({min:1,empty:{leftempty:false,rightempty:false,emptyerror:"重复密码两边不能有空符号"},onerror:"重复密码不能为空,请确认"}).compareValidator({desid:"password",operateor:"=",onerror:"2次密码不一致,请确认"});
	});


/**
 * 发送手机验证码
 * @param obj 发送验证码按钮jq对象用于锁定按钮
 * @returns {boolean}
 */
function sendMobileCode(obj, msg_type) {
    var mobile = $('#mobile').val();
    var code  = $('#code').val();
    msg_type = msg_type ? msg_type : 'txt';
    //var obj = $(obj);
    if(!/^1[0-9]\d{9}$/.test(mobile)) {popBox('请填写正确的手机号码','info');return false;}
    if(!$.trim(code)){popBox('请填写验证码','info');return false;}
    $.post("/register/sendmobilecode",{mobile:mobile,code:code,msg_type:msg_type},function(data){
        if(data.status==1) {
            $('#myModal3').modal('hide');
            popBox(data.msg,'success',2);
            obj.attr('disabled', 'disabled');
            var time = 60;
            var settime = setInterval(function () {
                time--;obj.val(time + 's后重新发送');
                if (time <= 0) {clearInterval(settime);obj.removeAttr('disabled');obj.val('重新发送'); }
            }, 1000);
        } else if(data.status!=-5) {
            $('#myModal3').modal('hide');
            popBox(data.msg,'error');
        } else {
            popBox(data.msg,'error');
        }
        $('#code').val('');
        updateCode();
        return false;
    })
}

/**
 * 弹出发送验证码框前检查手机号是否合法
 * @returns {boolean}
 */
function showMobileSend() {
    var mobile = $('#mobile').val();
    if(!/^1[0-9]\d{9}$/.test(mobile)) {popBox('请填写正确的手机号码','info');return false;}
    if(!checkMobile(mobile)){popBox('手机号码已存在,请更换手机号码','info');return false;}
    $('#myModal3').modal('show');
}

/**
 * 检查手机号是否存在
 * @param mobile 手机号
 * @returns {boolean}
 */
function checkMobile(mobile) {
    var is = false;
    $.ajax({
        url:'/register/checkmobile',
        data:{mobile:mobile},
        dataType:'json',
        async:false,
        success:function(data) {
            if(data.status==1) {
                is = true;
            }
        }
    })
    return is;
}

/**
 * 刷新发送手机验证码
 */
function updateCode() {
    document.getElementById('mobile_fresh_valicode').src='/register/mobilecode?t=' + Math.random();
}

/**
 * 注册成功跳转函数
 */
function successJump() {
    setTimeout(function(){window.location.href="/index/index";},2000)
}

/**
 * 推广注册页面点击项目跳转锚点
 */
function tm_projectRegister() {
    popBox('请先注册账号并登陆之后再查看项目详情!','info',5);
    location.hash='_register';$('#username').focus();
}
$(document).ready(function(){
	$.formValidator.initConfig({formid:"form1",onerror:function(msg){/*popBox(msg,'error');*/return false;},onsuccess:function(){
        /*if(!$("input[type='checkbox']").is(':checked')) {
            popBox('请选择注册协议后再提交','info');return false;
        }*/
        $.post('/register/AjaxAdd',{mobile:$('#mobile').val(),mobilecode:$('#telcode').val(),password:$('#password').val(),code:$('#verifyCode').val(),xieyi:$('#xieyi_check').val(),btn_submit:'register'},function(data){
            if(data.status==1) {
            	//执行回调地址
            	$("body").append(data.data);
                popBox(data.msg,'success',2);successJump();
            } else {
                $('.btn_zhuce').removeAttr('disabled');
                popBox(data.msg,'error');
            }
        })
        return false;
    }});
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
            onerror : "手机号不正确或手机号已存在",
            onwait : "正在对手机号进行合法性校验，请稍候..."
    });

	$("#telcode").formValidator({onshow:"请输入手机验证码",onfocus:"手机验证码不能为空",oncorrect:" "}).inputValidator({min:1,empty:{leftempty:false,rightempty:false,emptyerror:"手机验证码不能有空符号"},onerror:"手机验证码不能为空,请确认"});
	//$("#verifyCode").formValidator({onshow:"请输入验证码",onfocus:"验证码不能为空",oncorrect:" "}).inputValidator({min:1,empty:{leftempty:false,rightempty:false,emptyerror:"验证码不能有空符号"},onerror:"验证码不能为空,请确认"});
	$("#password").formValidator({onshow:"请输入密码",onfocus:"密码不能为空",oncorrect:" "}).inputValidator({min:6,empty:{leftempty:false,rightempty:false,emptyerror:"密码两边不能有空符号"},onerror:"密码不能为空,请确认"});

});

/**
 * 发送手机验证码
 * @param obj 发送验证码按钮jq对象用于锁定按钮
 * @returns {boolean}
 */
function sendMobileCode(obj) {
    var mobile = $('#mobile').val();
    var code  = $('#code').val();
    //var obj = $(obj);
    if(!/^1[0-9]\d{9}$/.test(mobile)) {popBox('请填写正确的手机号码','info');return false;}
    if(!$.trim(code)){popBox('请填写验证码','info');return false;}
    $.post('/register/sendmobilecode/',{mobile:mobile,code:code},function(data){
        if(data.status==1) {
            $('#myModal3').modal('hide');
            popBox('验证码已发送到您填写的手机上','success',2);
            obj.attr('disabled', 'disabled');
            var time = 60;
            var settime = setInterval(function () {
                time--;obj.val(time + '后重新发送');
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
    setTimeout(function(){window.location.href="/humanhub/show";},2000);
}
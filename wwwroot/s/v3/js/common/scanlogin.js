/**
* 二维码扫描登录
*/
var ScanLogin = {
	// 提示信息
	msg: {
		"CONFIG_ERROR"			: {msg: "配置文件不完整"},
		"OVER_MAX_LISTEN_COUNT"	: {msg: "超出最大限定监听次数，请刷新页面重新登录"},
		"NO_SET_REQUEST_URL"	: {msg: "没有设置监听的service的URL"},
		"NO_SET_CHECK_URL"		: {msg: "没有设置检查状态的service的URL"},
		"NO_SET_LOGIN_URL"		: {msg: "没有设置登录的service的URL"},
		"NO_SET_REGISTER_URL"	: {msg: "没有设为注册的service的URL"}
	},
	// 
	listenStatus: {
		"SUCCESS"			: "-101", // 成功
		"FIALD_OR_LISTEN"	: "-102", // 失败/监听中
		"REGISTER"			: "-103"  // 待注册
	},
	// 状态是否可以运行
	isRunStatus: true,
    // 计数
    count: 0,
	// 最大监听
	maxListenCount: 200,
    // 是否已经监听状态
    isListened: 0,
    // 设置监听的service的URL
    requestUrl: "",
    // 检查状态的service的URL
    checkUrl: "",
    // 设为登录的service的URL
    setLoginUrl: "",
    // 成功回调
    successCallback: "",
    // 失败回调
    errorCallback: "",
    // 配置
    config: function(config){
		ScanLogin.isRunStatus = true;
		if (config === undefined) {
			ScanLogin.isRunStatus = false;
		}
		if (config.maxListenCount !== undefined) {
			ScanLogin.maxListenCount = config.maxListenCount;
		}
		if (config.successCallback !== undefined) {
			ScanLogin.successCallback = config.successCallback;
		}
		if (config.errorCallback !== undefined) {
			ScanLogin.errorCallback = config.errorCallback;
		}
		if (config.requestUrl === undefined) {
			ScanLogin.error(ScanLogin.msg.NO_SET_REQUEST_URL);
			ScanLogin.isRunStatus = false;
		} else {
			ScanLogin.requestUrl = config.requestUrl;
		}
		if (config.checkUrl === undefined) {
			ScanLogin.error(ScanLogin.msg.NO_SET_CHECK_URL);
			ScanLogin.isRunStatus = false;
		} else {
			ScanLogin.checkUrl = config.checkUrl;
		}
		if (config.setLoginUrl === undefined) {
			ScanLogin.error(ScanLogin.msg.NO_SET_LOGIN_URL);
			ScanLogin.isRunStatus = false;
		} else {
			ScanLogin.setLoginUrl = config.setLoginUrl;
		}
    },
    // 开始监听
    listen: function(config){
        // 配置
        if (ScanLogin.count == 0) {
            ScanLogin.config(config);
        }
		if (!ScanLogin.isRunStatus) {
			return false;
		}
        ++ScanLogin.count;
        if (ScanLogin.isListened == 0) {
            // 设置监听状态
            ScanLogin.isListened = 1;
            // 开始请求
            ScanLogin.request();
        } else {
            ScanLogin.check();
        }
    },
    // 请求: service必须返回 status、msg属性
    request: function(){
        $.ajax({
            type: "get",
            url: ScanLogin.requestUrl,
            dataType: "jsonp",
            jsonp: "callback",
            success: function(data){
                if (data.status == ScanLogin.listenStatus.SUCCESS) {
                    // 持续监听
                    ScanLogin.sto = setTimeout(ScanLogin.listen, 3000);
                } else {
                    ScanLogin.error(data);
                }
            }
        });
    },
    // 检测: service 必须返回 status、msg、sign属性
    check: function(){
        $.ajax({
            type: "get",
            url: ScanLogin.checkUrl,
            dataType: "jsonp",
            jsonp: "callback",
            success: function(data){
                if (data.status == ScanLogin.listenStatus.SUCCESS) {
                    clearTimeout(ScanLogin.sto);
					ScanLogin.setLogin(data.sign);
                } else if (data.status == ScanLogin.listenStatus.REGISTER) {
					ScanLogin.error(data);
				} else {
					if (ScanLogin.count <= ScanLogin.maxListenCount) {
						ScanLogin.sto = setTimeout(ScanLogin.listen, 1500);
					} else {
						ScanLogin.error(ScanLogin.msg.OVER_MAX_LISTEN_COUNT);
					}
                }
            }
        });
    },
    // 设为登录
    setLogin: function(sign){
        $.ajax({
            type: "get",
            url: ScanLogin.setLoginUrl + "/sign/" + sign,
            dataType: "jsonp",
            jsonp: "callback",
            success: function(data){
                if (data.status == ScanLogin.listenStatus.SUCCESS) {
                    ScanLogin.success(data);
                } else {
                    ScanLogin.error(data);
                }
            }
        });
    },
    // 成功回调
    success: function(data){
        if (ScanLogin.successCallback) {
            ScanLogin.data = data;
            ScanLogin.callback(ScanLogin.successCallback, ScanLogin);
        } else {
			ScanLogin.debug(data.msg, "info");
		}
    },
    // 失败回调
    error: function(data){
        if (ScanLogin.errorCallback) {
            ScanLogin.callback(ScanLogin.errorCallback, data);
        } else {
			ScanLogin.debug(data.msg, "error");
		}
    },
    // 回调
    callback: function(callbackType, data){
        eval(callbackType + "(data)");
    },
	// 停止监听
	stop: function(){
		clearTimeout(ScanLogin.sto);
		ScanLogin.isRunStatus = false;
		ScanLogin.count = 0;
	},
	// 调试信息
	debug: function(msg, type){
		if (type == "error") {
			console.error(msg);
		} else {
			console.info(msg);
		}
	}
};

/* ============================= @Demo =============================

function success(data)
{
    alert("第" + data.count + "次监听登录成功");
}

function error(data)
{
    alert(data.msg);
}

$(document).ready(function(){
    var config = {
        // 最大监听次数
        maxListenCount: 200,
        // 设置监听的service的URL
        requestUrl: "http://www.weixin.renrentou.com/scanlogin/listen",
        // 检查状态的service的URL
        checkUrl: "http://www.weixin.renrentou.com/scanlogin/check",
        // 设为登录的service的URL
        setLoginUrl: "http://www.weixin.renrentou.com/scanlogin/doLogin",
        // 成功回调
        successCallback: "success",
        // 失败回调
        errorCallback: "error"
    };
    ScanLogin.listen(config);
});
*/
<include file="app/layout@header" />
<style>
    .box_zhifu{ width: 100%; height: 100%; text-align: center;}
    .box_zhifu img{ width: 100%; padding-top: 40%;}
    .box_zhifu p{ font-size: 1.1rem; line-height: 2rem;}
</style>
<script>
var num = 1;
var timer;
$(function(){
	timer = setInterval(isPaySuccess,1000);
});

function isPaySuccess(){
    var id = '<?php echo $pid;?>';
    var sign = '<?php echo $token['sign'];?>';
    var signtime = '<?php echo $token['signtime'];?>';
    var access_token = '<?php echo $token['access_token'];?>';
    var pid = {pid:id,sign:sign,signtime:signtime,access_token:access_token};
    $.ajax({
        url: "<?php echo url('pay/AjaxPay');?>",
        type: "post",
        data: pid,
        async:false,
        success: function(data) {
            var sdata = eval('(' + data + ')');
            if(sdata.status == 0){

            }else if(sdata.status == 1){
                clearInterval(timer);
                //appPopBox(sdata.msg,'success');
                $('#buyShowSuccess').show();
                //window.demo.pushAppParam('UserProject', 1);
                //setTimeout("locationU()", 4000);
            }else if(sdata.status == -1){
                clearInterval(timer);
                //appPopBox(sdata.msg,'info');
                //setTimeout("locationU()", 4000);
                $('#buyShowError p').html(sdata.msg);
                $('#buyShowError').show();
            }
            else
            {
                clearInterval(timer);
                //appPopBox(sdata.msg,'info');
                //setTimeout("locationU()", 5000);
                $('#buyShowError p').html(sdata.msg);
                $('#buyShowError').show();
            }
        }
        
    });
//    $.post(', , function(data){
//            var sdata = eval('(' + data + ')');
//            if(sdata.status == 0){
//                
//            }else if(sdata.status == 1){
//                    clearInterval(timer);
//                    popBox(sdata.msg,'success',5); 
//                    location.href = '<?php echo url('wap-index/index');?>';
//            }else if(sdata.status == -1){
//                    clearInterval(timer);
//                    popBox(sdata.msg,'info',5);
//                    location.href = '<?php echo url('pay/buyinfo',array('pid'=>$pid));?>';
//            }
//
//    });
}

$(document).ready(function (e) {
    //分页
    $('.backappbutton').click(function(){
        var type = $(this).attr('data-type');
        var pid = '<?php echo $pid;?>';
        //一个名称为submitFromWeb的方法 注册位置在java代码中 在这里将参数{'param': str1}和回调function传递给Java方法 Java方法处理后将数据responseData并调用该方法
        //调用本地java方法
        window.WebViewJavascriptBridge.callHandler(
            'pushAppParam'
            , {'action': type,'pid': pid,'page': 1}
            , function(responseData) {
                document.getElementById("show").innerHTML = "send get responseData from java, data = " + responseData
            }
        );
    });

});

/*这段代码是固定的，必须要放到js中*/
function setupWebViewJavascriptBridge(callback) {
    if (window.WebViewJavascriptBridge) { return callback(WebViewJavascriptBridge); }
    if (window.WVJBCallbacks) { return window.WVJBCallbacks.push(callback); }
    window.WVJBCallbacks = [callback];
    var WVJBIframe = document.createElement('iframe');
    WVJBIframe.style.display = 'none';
    WVJBIframe.src = 'wvjbscheme://__BRIDGE_LOADED__';
    document.documentElement.appendChild(WVJBIframe);
    setTimeout(function() { document.documentElement.removeChild(WVJBIframe) }, 0)
}

/*与OC交互的所有JS方法都要放在此处注册，才能调用通过JS调用OC或者让OC调用这里的JS*/
setupWebViewJavascriptBridge(function(bridge) {
    var callbackButton = document.getElementsByClassName('backappbutton');

    callbackButton.onclick = function(e) {
        var type = $(this).attr('data-type');
        var pid = '<?php echo $pid;?>';
        bridge.callHandler('pushAppParam', {'action': type,'pid': pid,'page': 1}, function(response) {

        })
    }
})
    
</script>
<div class="box_zhifu">
    <img src="<{$url.web_tpl}>/images/www_bill/zhifu/img.gif" />
    <p>正在支付中......</p>
</div>
<!--确认弹层-->
<div class="mask" id="buyShowSuccess">
    <div class="sell-tc" style="top: 12em">
        <div class="space-40"></div>
        <p>恭喜您认购成功！去查看我投资的项目？</p>
        <div class="space-20"></div>
        <div class="tc-btn">
            <a href="javascript:void(0);" class="btn-qx sell_close backappbutton" data-type="projectList" >返回</a>
            <a href="javascript:void(0);" class="btn-qd backappbutton" data-type="userProject" >去查看</a>
        </div>
        <div class="space-20"></div>
    </div>
</div>
<div class="mask" id="buyShowError">
    <div class="sell-tc" style="top: 12em">
        <div class="space-40"></div>
        <p>认购失败！</p>
        <div class="space-20"></div>
        <div class="sell-tc">
            <!--<a href="javascript:void(0);" class="btn-qx sell_close" onclick="window.demo.pushAppParam('projectList', 1);">取消</a>-->
            <a href="javascript:void(0);" class="sell-submit backappbutton" data-type="buyProject">返回重试</a>
        </div>
        <div class="space-20"></div>
    </div>
</div>
<include file="wap/layout@footer" />
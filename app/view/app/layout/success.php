<include file="app@layout/header" />

<style>
    .border_c{ width: 90%; margin-left: 5%; border: 1px solid #ccc; margin-top: 2rem; text-align: center;}
    .border_c b{ font-size: 1rem; line-height: 2rem; margin-top: 1rem; display: block;}
    .border_c p{ font-size: 0.8rem; line-height: 2rem;}
    .border_c p span{ color: #f63; margin-right: 0.5rem;}
    .border_c .bt_click{ display: block; background: #f63; width: 5.5rem; height: 1.5rem; line-height: 1.5rem; text-align: center; font-size: 0.9rem; margin: 1rem auto;}
</style>
<?php if($system != 'ios'){?>
<div class="border_c" style="border:0">
    <b style="color: #f00; margin-bottom: 20px"><{$message}></b>
</div>
<?php }else{?>
<div class="mask" id="errorMessage" style="display: block">
    <div class="sell-tc" style="top: 12em">
        <div class="space-40"></div>
        <p><{$message}></p>
        <div class="space-20"></div>
        <div class="tc-btn">
            <a href="javascript:void(0);" class="btn-qx sell_close backappbutton" data-type="backPage" >返回</a>
            <a href="javascript:void(0);" class="btn-qd backappbutton" data-type="<{$jumpUrl}>" >确定</a>
        </div>
        <div class="space-20"></div>
    </div>
</div>
<?php }?>
<include file="app@layout/footer" />
<?php if($system == 'ios'){?>
<script>
    $(document).ready(function (e) {
        //安卓
        $('.backappbutton').click(function(){
            var type = $(this).attr('data-type');
            //一个名称为submitFromWeb的方法 注册位置在java代码中 在这里将参数{'param': str1}和回调function传递给Java方法 Java方法处理后将数据responseData并调用该方法
            //调用本地java方法
            window.WebViewJavascriptBridge.callHandler(
                'pushAppParam'
                , {'action': type}
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
            bridge.callHandler('pushAppParam', {'action': type}, function(response) {

            })
        }
    })

</script>
<?php }?>
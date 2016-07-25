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
                appPopBox(sdata.msg,'success');
                //setTimeout("locationU()", 4000);
            }else if(sdata.status == -1){
                clearInterval(timer);
                appPopBox(sdata.msg,'info');
                //setTimeout("locationU()", 4000);
            }
            else
            {
                clearInterval(timer);
                appPopBox(sdata.msg,'info');
                //setTimeout("locationU()", 5000);
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
    
</script>
<div class="box_zhifu">
    <img src="<{$url.web_tpl}>/images/www_bill/zhifu/img.gif" />
    <p>正在支付中......</p>
</div>
<include file="app/layout@footer" />
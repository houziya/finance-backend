<include file="app@layout/header" />
<link rel="stylesheet" type="text/css" href="<{$url.web_tpl_v3}>/css/wap_personalData/personalData.css" />

<div id="wrapper" style="top:-0.8em">
    <div id="scroller">
        <form class="list_form" action="<?php echo $data['path'];?>" method="post" id="recharge_form">
            <p>用户编号：<?php echo $user['uid'];?></p>
            <p>真实姓名：<?php echo $ubodyInfo['realname'];?></p>
            <p>证件号：<?php echo $ubodyInfo['u_body_num'];?></p>
            <p>手机号码：<?php echo $user['mobile'];?></p>
            <!--<p>电子邮件：<?php /*echo $user['email'];*/?></p>-->
            <?php if($amount){?>
                <p>充值金额：<?php echo $amount;?></p>
            <?php }?>
            <!--<p><span>继续认购请点击确定，如放弃认购，需要重新排队购买</span></p>-->
            <textarea name="req"  style= " width:500px;height:200px;display:none;"  ><?=$data['req']?></textarea>
            <input type="hidden" name="sign" value="<?=$data['sign']?>">
            <div class="data02"><button id="sub_recharge">确定</button></div>
        </form>
    </div>
</div>
<include file="app@layout/footer" />
<include file="app@layout/header" />

<div class="news-box">
    <?php if (!empty($projects[0]['id'])){ ?>
    <div class="news-section">
        <a href="project/<?php echo $projects[0]['id']; ?>">
            <div class="project-details" onclick="window.demo.pushAppParam('project', <?php echo $projects[0]['id']; ?>)">
                <a href="project/<?php echo $projects[0]['id']; ?>"><img src="<{$url.web_tpl}>/images/app_base/news/app_img3.jpg" width="100%" /></a>
                <div class="project-text">
                    <h4><?php echo helper_string::msubstr($projects[0]['name'], 16);?></h4>
                    <span><?php echo helper_string::msubstr($projects[0]['oneword'], 16);?></span>
                </div>
            </div>
        </a>
        <div class="pandect-money">
            <p class="addres-p1"><?php echo $projects[0]['address2']; ?></p>
            <p class="addres-p2"><?php echo $projects[0]['trade']; ?></p>
            <div class="percent-bg">
                <span><?php echo $projects[0]['percent']; ?>%</span>
                <p><i style=" width:<?php echo $projects[0]['percent']; ?>%;"></i></p>
            </div>
            <ul class="percent-ul">
                <li>
                    <span><?php echo $projects[0]['days'] > 0 ? $projects[0]['days'] : 0;?>天</span>
                    <p>剩余时间</p>
                </li>
                <li>
                    <span>￥<?php if($projects[0]['finance_total'] > 10000){ echo ($projects[0]['finance_total']/10000).'万'; }else{ echo $projects[0]['finance_total'].'元'; } ?></span>
                    <p>目标金额</p>
                </li>
                <li>
                    <span>￥<?php if($projects[0]['lest_finance'] > 10000){ echo ($projects[0]['lest_finance']/10000).'万'; }else{ echo $projects[0]['lest_finance'].'元'; } ?></span>
                    <p>单笔出资额</p>
                </li>
            </ul>
        </div>
    </div>
    <?php } ?>

    <div class="finance-box">
        <h4>人人投是什么？</h4>
        <p>人人投，国内首家专注于实体店铺股权众筹网络服务平台。主要业务是为中小实体企业融资开分店，为天使投资人寻找优质实体企业项目。旨在为投资人和融资者搭建一个公平、透明、高效的互联网金融服务平台。为实体店铺提供开分店众筹融资服务，为草根天使投资人提供优质融资实体店铺项目。</p>
        <h4>我们的运作流程</h4>
        <img src="<{$url.web_tpl}>/images/app_base/news/app_img1.jpg" width="100%" />
        <h4>投资操作流程</h4>
        <div class="simulate">找项目</div>
        <div class="simulate-img"><img src="<{$url.web_tpl}>/images/app_base/news/app_img2.jpg" width="100%" /></div>
        <div class="simulate">项目详情</div>
        <div class="simulate-img"><img src="<{$url.web_tpl}>/images/app_base/news/app_img2.jpg" width="100%" /></div>
        <div class="simulate">我要认购</div>
        <div class="simulate-img"><img src="<{$url.web_tpl}>/images/app_base/news/app_img2.jpg" width="100%" /></div>
        <div class="simulate">支付</div>
        <div class="simulate-img"><img src="<{$url.web_tpl}>/images/app_base/news/app_img2.jpg" width="100%" /></div>
        <div class="simulate">分红</div>
    </div>
    <div class="space-40"></div>
</div>

<include file="wap@layout/footer" />
<script type="text/javascript">
    function pushAppParam(act, type) {
        var arr = new Array()
        arr[act] = type;
        return arr;
    }
</script>
<include file="app@layout/header" />

<div class="news-box">
    <div class="space-10"></div>
    <div class="black-list">
        <div class="black-bt">黑名单机制</div>
        <div class="black-main">
            <a href="heimingdan://black/3" onclick="window.demo.pushAppParam('black', 3)">
                <img src="<{$url.web_tpl}>/images/app_base/news/gfxxm_.png" width="100%" />
                <div class="black-nmb">
                    <span>高风险项目 </span>
                    <!--<i>3</i>-->
                </div>
            </a>
            <a href="heimingdan://black/1" onclick="window.demo.pushAppParam('black', 1)">
                <img src="<{$url.web_tpl}>/images/app_base/news/qyhmd_.png" width="100%" />
                <div class="black-nmb">
                    <span>黑名单企业 </span>
                    <!--<i>3</i>-->
                </div>
            </a>
            <a href="heimingdan://black/2" onclick="window.demo.pushAppParam('black', 2)">
                <img src="<{$url.web_tpl}>/images/app_base/news/tzrhmd_.png" width="100%" />
                <div class="black-nmb">
                    <span>黑名单投资人 </span>
                    <!--<i>2</i>-->
                </div>
            </a>
        </div>
    </div>
    <div class="finance-box black-box">
        <h4>项目风控</h4>
        <p>客观严格的项目审核机制，上线前七层审核，把每个项目的风险降到最低，为投资人服务。</p>
        <h4>资金保障</h4>
        <p>人人投携手易宝——第三方支付系统，资金安全有保障，人人投1000万元做您投资保护伞</p>
        <h4>财务监管系统</h4>
        <p>人人投财务监管系统，让投资人清楚知道所投款项目的每一笔花销，收支明细心中有数。</p>
        <h4>技术保障</h4>
        <p>人人投专业的技术团队持续更新和改进网站的安全策略，以保证网站安全策略的有效性，确保用户信息安全。</p>
    </div>
    <div class="space-40"></div>
</div>

<include file="wap@layout/footer" />
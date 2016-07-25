<include file="app@layout/header" />

<div class="news-box">
    <div class="space-10"></div>
    <div class="shop-box">
        <div class="shop-list">
            <?php echo model_project::html_dispose($gift['details']); ?>
            <p>提示：礼品兑换后不接受退换货申请，如有质量或使用问题，请拨打售后服务热线：400-070-5286。</p>
            <div class="space-10"></div>
        </div>
    </div>
    <div class="space-40"></div>
</div>

<include file="wap@layout/footer" />
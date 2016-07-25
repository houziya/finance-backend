<div id="fee_page_trace" style="background:white;font-size:14px;">
<fieldset style="margin:10px;">
<legend style="color:gray;font-weight:bold">页面Trace信息</legend>
<div style="overflow:auto;height:300px;text-align:left;">
<?php foreach ($_trace as $key=>$info){
echo $key.' : '.$info.'<br/>';
}?>
</div>
</fieldset>
</div>
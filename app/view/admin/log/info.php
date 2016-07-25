<include file="admin@header" />

<script type="text/javascript">
$(document).ready(function(){
	$.formValidator.initConfig({ submitonce:true,formid:"form1",autotip:true});
	$("#gift_name").formValidator({onshow:"请输入礼品名称",onfocus:"请输入礼品名称",oncorrect:"输入正确"}).regexValidator({regexp:"notempty",datatype:"enum",onerror:"请输入礼品名称"});
	$("#num").formValidator({onshow:"请输入礼品总数",onfocus:"请输入礼品总数",oncorrect:"输入正确"}).regexValidator({regexp:"num",datatype:"enum",onerror:"礼品总数输入错误"});
	$("#score").formValidator({onshow:"请输入礼品兑换积分",onfocus:"请输入礼品兑换积分",oncorrect:"输入正确"}).regexValidator({regexp:"num",datatype:"enum",onerror:"礼品兑换积分输入错误"});
});

</script>
<div class="pad-10">
	<div class="content-menu line-x blue"><{$topnav}></div>
	<div class="table-list">
		<form action="" method="post" id="form1">
		<table width="100%">
			<tr>
				<td class="text-l"><{$info}></td>
			</tr>
			</table>
		</form>
	</div>
</div>

<include file="admin@footer" />
<script>
function initSeckill(showType)
{
    if (showType == "show") {
        $("#seckillSpan").show();
    } else {
        $("#seckillSpan").hide();
    }
}

$(document).ready(function(){
    $("#seckillYes").bind("click", function(){
        initSeckill("show");
    });
    $("#seckillNo").bind("click", function(){
        initSeckill("hide");
    });

    if ($("#seckillYes").attr("checked")) {
        initSeckill("show");
    } else {
        initSeckill("hide");
    }
});
</script>
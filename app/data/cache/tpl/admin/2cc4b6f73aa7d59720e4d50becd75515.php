<?php if (!defined('FEE_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo ($appini["sysconfig"]["web_name"]); ?>后台管理中心</title>
<?php 
echo helper_view::addCss(array('admin/css/base.css','admin/css/style.css'),1,1);
echo helper_view::addJs(array('admin/js/jquery-1.8.3.min.js', 'admin/js/common.js','admin/js/formvalidator/formvalidator.js','admin/js/formvalidator/formvalidator_regex.js'),1,1);
echo helper_view::addJsCode('',1,1);
?>
<script type="text/javascript" src="<?php echo ($url["admin_tpl"]); ?>/js/dialog/dialog.js?_v=<?php echo ($appini["web_version"]); ?>"></script>
</head>
<body<?php echo empty($body_style) ? '' : $body_style; ?>>
<script type="text/javascript" src="<?php echo ($url["admin_tpl"]); ?>/js/highcharts/highcharts.js?_v=<?php echo ($appini["web_version"]); ?>"></script>
<div class="pad-10">
    <div class="content-menu line-x blue"><?php echo ($topnav); ?>
        |类型：
        <select id="charttype">
            <option value="line" <?php if ($charttype == 'line') echo 'selected="selected"' ?>>线状图</option>
            <option value="area" <?php if ($charttype == 'area') echo 'selected="selected"' ?>>面积图</option>
            <option value="column" <?php if ($charttype == 'column') echo 'selected="selected"' ?>>柱状图(纵向)</option>
            <option value="bar" <?php if ($charttype == 'bar') echo 'selected="selected"' ?>>柱状图(横向)</option>
            <!--<option value="pie" <?php /* if ($charttype == 'pie') echo 'selected="selected"' */ ?>>饼图</option>-->
            <option value="scatter" <?php if ($charttype == 'scatter') echo 'selected="selected"' ?>>点状图</option>
        </select>
        客户端ID:<input name="id" id="id"  type="text" class="input-text" value="<?php echo ($id); ?>" size="3" />
         <input type="submit" name="dosubmit" onclick="search()" class="button" value="搜索" />
    </div>
    <fieldset>
        <legend>在线状态日统计</legend>
        <div class="table-form">
            <div id="container_day" style="min-width:700px;height:400px"></div>
        </div>
    </fieldset>
    <div class="bk10"></div>
    <fieldset>
        <legend>在线状态周统计</legend>
        <div class="table-form">
            <div id="container_week" style="min-width:700px;height:400px"></div>
        </div>
    </fieldset>
    <div class="bk10"></div>
    <fieldset>
        <legend>在线状态月统计</legend>
        <div class="table-form">
            <div id="container_month" style="min-width:700px;height:400px"></div>
        </div>
    </fieldset>
    <div class="bk10"></div>
</div>
<script type="text/javascript">
        function search (){
            var href = window.location.href;
            href = href.slice(0,href.indexOf('?') == -1 ? href.length : href.indexOf('?'));
            var charttype = $("#charttype").val();
            var id = $("#id").val();
            window.location.href = href + '?charttype=' + charttype+'&id='+id;
        }
    $(function () {
    
        var charttype = '<?php echo ($charttype); ?>';
        var day_data = <?php echo ($dayData); ?>;
        var week_data = <?php echo ($weekData); ?>;
        var month_data = <?php echo ($monthData); ?>;
        //当天数据
        if(day_data){
            $('#container_day').highcharts({ 
                chart: {
                    type: charttype
                },
                title: {
                    text: '',
                    x: -20 //center
                },
                subtitle: {
                    text: '',
                    x: -20
                },
                xAxis: {
                    categories: day_data.thedate
                },
                yAxis: {
                    title: {
                        text: '',
                        x: -20 //center
                    },
                    title: {
                        text: '30日内趋势图'
                    },
                    labels: {
                        format: '{value}次数'
                    },
                    plotLines: [{
                            value: 0,
                            width: 1,
                            color: '#808080'
                        }]
                },
                tooltip: {
                    valueSuffix: '次'
                },
                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle',
                    borderWidth: 0
                },
                credits: {
                    text: '人人投',
                    href: 'http://www.renrentou.com/'
                },
                series: [{
                        name: '成功查询次数',
                        data: day_data.success_num
                    }, {
                        name: '失败查询次数',
                        data: day_data.fail_num
                    }]
            })
        }else{
            $('#container_day').html('<h4 style="text-align: center;">暂时没有日统计数据</h4>');
        }
        
        //曲线显示周
        if(week_data) {
            $('#container_week').highcharts({
                chart: {
                    type: charttype
                },
                title: {
                    text: '',
                    x: -20 //center
                },
                subtitle: {
                    text: '',
                    x: -20
                },
                xAxis: {
                    categories: week_data.thedate
                },
                yAxis: {
                    title: {
                        text: '30周内趋势图'
                    },
                    labels: {
                        format: '{value} 个'
                    },
                    plotLines: [{
                            value: 0,
                            width: 1,
                            color: '#808080'
                        }]
                },
                tooltip: {
                    valueSuffix: ' 次'
                },
                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle',
                    borderWidth: 0
                },
                credits: {
                    text: '人人投',
                    href: 'http://www.renrentou.com/'
                },
                series: [{
                        name: '成功查询次数',
                        data: week_data.success_num
                    }, {
                        name: '失败查询次数',
                        data: week_data.fail_num
                    }]
            });
        } else {
            $('#container_week').html('<h4 style="text-align: center;">暂时没有周统计数据</h4>');
        }
        //显示月统计数据
         if(month_data) {
            $('#container_month').highcharts({
                chart: {
                    type: charttype
                },
                title: {
                    text: '',
                    x: -20 //center
                },
                subtitle: {
                    text: '',
                    x: -20
                },
                xAxis: {
                    categories: month_data.thedate
                },
                yAxis: {
                    title: {
                        text: '12月内趋势图'
                    },
                    labels: {
                        format: '{value} 个'
                    },
                    plotLines: [{
                            value: 0,
                            width: 1,
                            color: '#808080'
                        }]
                },
                tooltip: {
                    valueSuffix: ' 次'
                },
                legend: {
                    layout: 'vertical',
                    align: 'right',
                    verticalAlign: 'middle',
                    borderWidth: 0
                },
                credits: {
                    text: '人人投',
                    href: 'http://www.renrentou.com/'
                },
                series: [{
                        name: '成功查询次数',
                        data: month_data.success_num
                    }, {
                        name: '失败查询次数',
                        data: month_data.fail_num
                    }]
            });
        } else {
            $('#container_month').html('<h4 style="text-align: center;">暂时没有周统计数据</h4>');
        }
    });
</script>
</body>
</html>
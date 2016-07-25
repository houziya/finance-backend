<include file="admin@header" />
<script type="text/javascript" src="<{$url.admin_tpl}>/js/highcharts/highcharts.js?_v=<{$appini.web_version}>"></script>
<div class="pad-10">
    <div class="content-menu line-x blue"><{$topnav}>
        |
        <select id="charttype">
            <option value="line" <?php if ($charttype == 'line') echo 'selected="selected"' ?>>线状图</option>
            <option value="area" <?php if ($charttype == 'area') echo 'selected="selected"' ?>>面积图</option>
            <option value="column" <?php if ($charttype == 'column') echo 'selected="selected"' ?>>柱状图(纵向)</option>
            <option value="bar" <?php if ($charttype == 'bar') echo 'selected="selected"' ?>>柱状图(横向)</option>
            <!--<option value="pie" <?php /* if ($charttype == 'pie') echo 'selected="selected"' */ ?>>饼图</option>-->
            <option value="scatter" <?php if ($charttype == 'scatter') echo 'selected="selected"' ?>>点状图</option>
        </select>
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
    $(function () {
        $("#charttype").change(function(){
            var href = window.location.href;
            href = href.slice(0,href.indexOf('?') == -1 ? href.length : href.indexOf('?'));
            window.location.href = href + '?charttype=' + $(this).val();

        });
        var charttype = '<{$charttype}>';
        var day_data = <{$dayData}>;
        var week_data = <{$weekData}>;
        var month_data = <{$monthData}>;
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
                        format: '{value}个'
                    },
                    plotLines: [{
                            value: 0,
                            width: 1,
                            color: '#808080'
                        }]
                },
                tooltip: {
                    valueSuffix: '个'
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
                        name: '在线客户端个数',
                        data: day_data.online_num
                    }, {
                        name: '离线客户端个数',
                        data: day_data.offline_num
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
                    valueSuffix: ' 个'
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
                        name: '在线客户端个数',
                        data: week_data.online_num
                    }, {
                        name: '离线客户端个数',
                        data: week_data.offline_num
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
                    valueSuffix: ' 个'
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
                        name: '在线客户端个数',
                        data: month_data.online_num
                    }, {
                        name: '离线客户端个数',
                        data: month_data.offline_num
                    }]
            });
        } else {
            $('#container_month').html('<h4 style="text-align: center;">暂时没有周统计数据</h4>');
        }
    });
</script>
<include file="admin@footer" />
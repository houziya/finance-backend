window.onload=function(){
	var myChart = echarts.init(document.getElementById('main'));
 	var option = {
	    title: {
	    },
	    tooltip: {
	        trigger: 'axis'
	    },
	    legend: {
	    },
	    grid:{
	    	top:40,
	    	bottom:20,
	    	containLabel:true
	    },
	    toolbox: {
	        show: true,
	        feature: {
	            dataZoom: {},
	            dataView: {readOnly: false},
	            magicType: {type: ['line', 'bar']},
	            restore: {},
	            saveAsImage: {}
	        }
	    },
	    xAxis:  {
	    	
	        type: 'category',
	        position:'bottom',
	       	axisLine:{
	       		show:false,
	       	},
	       	axisTick:{
	       		length:0,
	       		lineStyle:{
	       			type:'dashed',
	       		}
	       	},
	        boundaryGap: false,
	        data: ['','','','','','','','','','','','','','','','','','','','','','']
	    },
	    yAxis: {
	        type: 'value',
	        axisLabel: {
	            formatter: '{value}(元)'
	        }
	    },
	    color:['#30d8a7','#ff6817'],
	    series: [
	        {
	            name:'营业额',
	            type:'line',
	            data:[3.2000, 1000, 3.3000, 3.7000, 4.0000, 3.5000, '','','','','','','','','','','','','','','',''],
	        },
	        {
	            name:'客流量',
	            type:'line',
	            data:[1, 20, 30, 50, 3.6500, 3.2000, 3.4000],
	        }
	    ],
	};
	myChart.setOption(option);
	var oUl=document.getElementById('main_ul');
	var oMain=document.getElementById('main');
	oUl.style.height=parseInt(oMain.offsetHeight-60)+'px';

}





























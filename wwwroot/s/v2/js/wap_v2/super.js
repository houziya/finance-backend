// JavaScript Document
	
$(function(){
	var _clickObj = {}
	_clickObj = {'dom':$('.super-list').eq(0)}

	// 初始化滑动载入控件
	refresher.init({id:"wrapper",box:"wp01",pullDownAction:Refresh,pullUpAction:Load});
	// 切换tab
	$('.super-ul li').live("click", function(){
	 	$(this).addClass('on').siblings().removeClass('on');
	 	$('.super-list').hide().attr('data-on','');
	 	$('.super-list').eq($(this).index()).show().attr('data-on','true');
	 	_clickObj = {
	 		'dom':$('.super-list').eq($(this).index())
	 	}
	 	$('.pullUpLabel').html('')
	 	myScroll.refresh();
	});

  	var generatedCount = 0;
  	function Refresh() {
  		//myScroll.refresh();
  	}
  	function Load(){
  		var _timeout = true,_page,
		_invitePage = $('[data-type="invite"]').attr('data-page'),
		_investPage = $('[data-type="invest"]').attr('data-page');
  		// 超时操作
		var _timer = setTimeout(function(){
			$('.loader').hide()
			$('.pullUpLabel').html('网络故障')
			_timeout = false;
			myScroll.refresh();
						
		},5000)
		// 单独维护各自独立的页码
		if(_clickObj.dom.attr('data-type') == 'invite'){
			_page = ++_invitePage;
		}else if(_clickObj.dom.attr('data-type') == 'invest'){
			_page = ++_investPage;
		}
    	$.post(
      		'http://wap.weixin.renrentou.com/weixin/invitelist',
      		{type:_clickObj.dom.attr('data-type'),p:_page,isajax:'1'},
      		function(data){
      			clearTimeout(_timer)
      			if(_timeout){
	      			var _data = data,invited_img = '',username = '',income_tag = '',income_amount = '',add_time = '',wx_nickname = '';
	 				if(_data.code == 0){
	 					 //总页数 
	 					 //_data.data.total = 10;
						if(_clickObj.dom.find('dl').size() < _data.data.total){
	 						// 循环渲染模板
	 						renderTemplate(_clickObj.dom,_data)
	 						// 只有确认渲染了新的页面才能设置页码
		 					if(_clickObj.dom.attr('data-type') == 'invite'){
								$('[data-type="invite"]').attr('data-page',_invitePage)
							}else if(_clickObj.dom.attr('data-type') == 'invest'){
								$('[data-type="invest"]').attr('data-page',_investPage)
							}
							myScroll.refresh();
	 					}else{
	 						myScroll.refresh();
	 						//到达页底
	 						$('.pullUpLabel').html('到达页底')
	 					}
	 					$('.loader').hide()
	        			
	 				}else{
	 					// net error
	 					// console.log(data.msg)
	 				}
	 				///renderTemplate(_clickObj.dom,_data)
 				}
    	})
    	// 渲染模板
    	function renderTemplate(pDom,pData){
    		var _dom = pDom,_data = pData;
    		for(i=0;i<_data.data.itemlist.length;i++){
    			var _userinfo = _data.data.itemlist[i].invited_userinfo,
    				_itemlist = _data.data.itemlist[i]

    			invited_img = _userinfo.wx_headimgurl; 
				username = _userinfo.username;
				wx_nickname = _userinfo.wx_nickname;
				add_time = new Date(+_data.data.itemlist[i].add_time);
				income_amount = _itemlist.income_amount;
				income_tag = _itemlist.income_tag || '';

				// 处理时间日期
				var _getMonth = formatTimeZero((add_time.getMonth()+1)),
					_getDay = formatTimeZero(add_time.getDay()),
					_getHour = formatTimeZero(add_time.getHours()),
					_getMinute = formatTimeZero(add_time.getMinutes()),
					_getSecond = formatTimeZero(add_time.getSeconds());
				_template = '<dl class="super-dl"><dt><img src="'+ invited_img +'"'+
		 			'width="100%"></dt><dd><span>'+ username +'</span><b>微信昵称:'+ wx_nickname +'</b>'+
		 			'<p>奖励金额：<i>'+ income_amount +'元</i><i> '+ income_tag +'</i></p><p>注册时间：'+ add_time.getFullYear()+'-'+ _getMonth + '-' + _getDay +' '+ _getHour +':'+ _getMinute +':'+ _getSecond +'</p></dd></dl>'
				_dom.append(_template);
			}
    	}

    	// 格式化一位的日期将前面补上0
    	function formatTimeZero(pAram){
    		return +pAram < 10 ? '0'+pAram : pAram;
    	}
  	}
})


$('#yeePayClose').live("click", function(){
	 $('.yeePay-box').hide();
});



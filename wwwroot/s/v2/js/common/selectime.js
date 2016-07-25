(function(w){
			var selecttimer = function(sObj){
				var _dom = sObj.dom,
					_year = sObj.dom.find('.tsel-year'),
					_month = sObj.dom.find('.tsel-month'),
					_day = sObj.dom.find('.tsel-day'),
					_timerObj = {},
					DA = [0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31],
					_start = sObj.startimer,
					_end = sObj.endtimer,
					_submit = sObj.submit;
				// 启动入口
				init()
				function init(){
					_timer = getTimer();
					setTime(_timer);
					_dom.find('.tsel-btn').on('touchstart',tselBtn);
					_submit.on('touchstart',submitTime);
					_start.on('touchstart',setStartTime);
					_end.on('touchstart',setEndTime);
				}

				// 触发时间控制按钮
				function tselBtn(){

					var _tag = $(this).parent().attr('data-c'),
						_upadown = $(this).index() == 0 ? 'up' : 'down';
					switch(_tag){
						case 'tsel-year': setYear(_upadown);break;
						case 'tsel-month': setMonth(_upadown);break;
						case 'tsel-day': setDay(_upadown);break;
					}
				}

				// 获取start文本框属性
				function setStartTime(){
					_submit.attr('data-c','start')
				}
				// 获取end文本框属性
				function setEndTime(){
					_submit.attr('data-c','end')
				}

				// 设置时间到输入框
				function submitTime(){
					var _target = $(this).attr('data-c');
					if(_target == 'start'){
						_start.html(_timerObj.year+'-'+_timerObj.month+'-'+_timerObj.day)
					}else if(_target == 'end'){
						_end.html(_timerObj.year+'-'+_timerObj.month+'-'+_timerObj.day)
					}
				}

				// 获取当前日期
				function getTimer(){
					var _date = new Date(),
						_month = _date.getMonth()+1,
						_day = _date.getDate();
					_timerObj = {
						year:_date.getFullYear(),
						month:_month > 9 ? _month : '0'+_month,
						day:_day > 9 ? _day : '0'+_day
					}
					return _timerObj
				}

				// 设置年份
				function setYear(pTag){
					var _ti = _timerObj.year;
					_timerObj.year = pTag == 'up' ? +_ti+1 : +_ti-1;
					setTime(_timerObj);
				}

				// 设置月份
				function setMonth(pTag){
					var _ti = _timerObj.month,_t = pTag;
					if(_ti >= 12 && _t == 'up'){
						_timerObj.month = '01';
						setYear('up')
					}else if(_ti <= 1 && _t == 'down'){
						_timerObj.month = '12';
						setYear('down')
					}else{
						_ti = pTag == 'up' ? +_ti+1 : +_ti-1;
						_timerObj.month = _ti > 9 ? _ti : '0'+_ti;
					}
					setTime(_timerObj);
				}
				// 判断闰年
				function isLoopYear(pYear){
					if((pYear%4==0 && pYear%100!=0)||(pYear%100==0 && pYear%400==0)){
						return true
					}else{
						return false;
					}
				}
				

				// 设置天
				function setDay(pTag){
					var _ti = +_timerObj.day,_t = pTag;
					var _x = 31;
					
					 //判断是否是闰年
  					
					if(_t == 'up'){
						 if(checkDate(_ti+1)){
						 	_ti += 1;
						 }else{
						 	_ti = 1;
						 	setMonth('up')
						 }
					 }else if(_t == 'down'){
					 	if((_ti-1) > 0){
					 		_ti -= 1;
					 	}else{
					 		_ti = DA[(_timerObj.month-1) <= 0 ? 12 :(_timerObj.month-1) ]
					 		setMonth('down')
					 	}
					 }
					_timerObj.day = _ti > 9 ? _ti : '0'+_ti;
					setTime(_timerObj);
				}
				// 判断日期有效性
				function checkDate(pDay){
					// 根据是否是闰年设置日期
					isLoopYear(_timerObj.year) ? DA[2] = 29 : DA[2] = 28;
					// 检查日期是否是有效值
					if(pDay > DA[+_timerObj.month]) {
						return false
					}else{
						return true;
					}

				}

				// 给日期控件赋值
				function setTime(pTimer){
					_year.find('p').html(+pTimer.year);
					_month.find('p').html(pTimer.month);
					_day.find('p').html(pTimer.day)
				}
			}
			w.selecttimer = selecttimer;
		})(this)
(function(w){
	// 组件解析
	// 组件解析
	var webComponent = function(){
		var _domObj = {
			// 公用幻灯
			'SLIDE':function(){
				return {
					'html':'template/slide.html',
					'contoller':function(sDom){
							var _windowWid,_padding,_defaultIndex,_index,_imgWid,_nextfinish = true,_prevfinish = true,_thisindex,_postag; 
							var _lefttag = true,_rigtag = true;
							//$(window).bind('resize',init)
							function init(){
								_windowWid = $(document).width()*0.9 // 窗口宽度
								_padding = 10 // 计算出图片相隔的距离
								_defaultIndex = sDom.attr('data-imgnum') // 设置每屏显示几个图片
								_index = $('.slide-lmove li').size() // 总共有的图片数量
								_imgWid = (_windowWid - _padding*(_defaultIndex-1) - 60)/_defaultIndex
								var _wid = _index*_imgWid+_padding*(_defaultIndex-1) + 60+2*_padding+_index*30
								
								$('.leftbtn').addClass('leftbtn-no');
								_prevfinish = false;
								$('.slide-box').css('width',_windowWid-4)
								$('.slide-lmove').css('width',_wid);
								$('.slide-con').css('width',_windowWid-60)
								$('.slide-lmove li').css({'padding-right':_padding,'width':_imgWid})
								$('.slide-lmove img').css({'width':(_imgWid-4),'height':(_imgWid-4)/1.87});
								$('.rightbtn').tap(next)
								$('.leftbtn').tap(prev)
								setTransform($('.slide-lmove')[0],0)
								$('.slide-lmove')[0].addEventListener('webkitTransitionEnd',transend)
								$('.slide-lmove')[0].addEventListener('transitionend',transend)
								//$('.slide-lmove')[0].style['-webkit-transition'] = 'transform 1s'
								
							}
							
								function transend(){
									//alert(_thisindex)
									if(_postag == 'left'){
										_lefttag = true;
										if((_index - _thisindex - _defaultIndex-1) < 0){
											$('.rightbtn').addClass('rightbtn-no');
											_nextfinish = false;
										}
										$('.leftbtn').removeClass('leftbtn-no');
										_prevfinish = true;
									}else if(_postag == 'right'){
										_rigtag = true;
										if(Math.abs(_thisindex)-1 <= 0){
											$('.leftbtn').addClass('leftbtn-no');
											_prevfinish = false;
										}
										$('.rightbtn').removeClass('rightbtn-no');
										_nextfinish = true;
									}
									
								}
							// 获取transform属性
							function getTransform(pDom){
								var _stdom = pDom.style['webkitTransform'] || 
								pDom.style['msTransform'] ||
								pDom.style['Transform'] ||
								pDom.style['webkitTransform'],
								    _nArr = _stdom.split(','),
								    _objArr = {};
								if(_nArr.length == 3){
									_objArr.x = _nArr[0].match(/([^\(]+)+px/)[1];
								}else{
									_objArr.x = _nArr[13].match(/([^\(]+)+px/)[1];
								}
								return _objArr
							}

							// 设置transform属性
							function setTransform(pDom,pNum){
								pDom.style['-webkit-transform'] = 'translate3d('+pNum+'px,0,0)';
							}
							
							// 下一项
							function next(){
								var _ndom = $('.slide-lmove')[0]
								var _nstr = getTransform(_ndom).x
								_postag = 'left'
								
								if(_nextfinish && _lefttag){
									_thisindex = Math.abs(parseInt(_nstr)/(_imgWid+_padding))
									_thisindex++
									_lefttag = false;
									setTransform($('.slide-lmove')[0],-Math.round(_thisindex)*(_imgWid+_padding))
								}

								
							}
							// 上一项
							function prev(){
								var _ndom = $('.slide-lmove')[0]
								var _nstr = getTransform(_ndom).x
								
								_postag = 'right'
								if(_prevfinish && _rigtag){
									_thisindex = Math.abs(parseInt(_nstr)/(_imgWid+_padding))
									_rigtag = false;
									setTransform($('.slide-lmove')[0],-(_thisindex-1)*(_imgWid+_padding))
								}					
								
							}
							init()
						}
					}
				}
			}
			
			//
			function loadHtml(sDom,sObj){
			    $.ajax({
			        url: sObj.html,
			        global: false,
			        type: "POST",
			        dataType: "html",
			        async: false,
			        success: function(msg) {
			            sDom.html(msg);
			            sObj.contoller(sDom);
			            //bindData(msg,sObj.data)
			        }
			    })
			}

			// 绑定数据
			function bindData(pStr,pData){
				//var _matStr = pStr.match(/data-bind="([^"]+)"/g);
				//for(var i=0;i<pData.length;i++){
					//console.log(_matStr)
					//pStr = pStr.replace(/data-bind="[^]+"/,"data-bind=\""+pData[i]+"\"")
				//}
				//console.log(pStr)
			}

			//$('*').each(function(index,dom){
				//if(typeof _domObj[dom.nodeName] == 'function'){
					//var _component = _domObj[dom.nodeName]();
					// 获取html文件
					var x = _domObj['SLIDE']()
					x.contoller($('.slide-box'))
					//loadHtml($(dom),_component)
				//}
			//})
		}
		
		$(function(){webComponent();})

		// 上传图片类
		var UploadFile = function(pObj){
			pObj = pObj || {};
			var _param,_default = {
				file:document.getElementById('upload_file'),
				imgbox:document.getElementById('upload_img')
			}

			// 判断浏览器环境
			if(document.all) document.write('<!--[if lte IE 6]><script type="text/javascript">window.ie6 = true<\/script><![endif]-->'); // IE6
			// 扩展默认对象
			function extend(pDefault,pInput){
				for(var a in pDefault){
					pDefault[a] = pInput[a] || pDefault[a]
				}
				return pDefault;
			}

			// 默认参数
			_param = extend(_default,pObj)

			// 展示预览图
			if(document.all){
				_param.file.attachEvent('onchange',function(e){
					showPrevew(_param.imgbox,_param.file);
				});
			}else{
				_param.file.addEventListener('change',function(e){
					showPrevew(_param.imgbox,_param.file);
				});
			}
			
			//

			// 实现兼容所有浏览器的上传图片预览图效果
			function showPrevew(pPic,pFile){
				var _pic = pPic,
					_file = pFile;
				if(window.FileReader){ // chrome, firefox7+, opera, IE9, IE10 IE9也支持滤镜模式
					oFReader = new FileReader(); // 创建文件对象
					oFReader.readAsDataURL(_file.files[0]); // 将文件控件加载到文件读取器里
					oFReader.onload = function(oFREvent){ // 注册文件对象的载入事件
						_pic.src = oFREvent.target.result; 
						// 将free varibute属性内的路径读取出来付给图片src属性，实现显示图片目的
					}
				}else if(document.all){ // IE8-
					var reallocalpath; 
					_file.select();
					_file.blur();
					reallocalpath = document.selection.createRange().text;
					// IE下获取实际本地文件路径，IE8以下由于安全策略的问题不能通过value的形式获取真实file文件地址
					if(window.ie6){ // ie6浏览器
						_pic.src = reallocalpath; // ie8以下浏览器中，ie6浏览器是可以通过src设置图片路径
					}else{	// ie8以下浏览器而又不是ie6浏览器的情况是不能通过src设置显示本地图片，只能通过滤镜来实现。IE10以上浏览器不支持路径就得用FileReader来处理
						_pic.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod='image',src=\"" + reallocalpath + "\"sizingMethod='scale'";
						_pic.src = 'data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==' // 设置img的src为base64编码的透明图片，这样就不会显示红叉子图片
					}
				}else if(_file.files){ // Firefox6-以下使用files对象获取地址
					if(_file.files.item(0)){
						var _url = _file.files.item(0).getAsDataURL();
						pic.src = _url;
					}
				}
			}
		}
		w.h_tool ? void 0 : w.h_tool = {};
		w.h_tool.UploadFile = UploadFile;
	})(this)
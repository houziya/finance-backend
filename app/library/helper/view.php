<?php
/**
 * 模版视图处理类
 * @author liufei
 */
class helper_view {
	
	/**
	 * 向头部添加 css 文件
	 *
	 * Example:
	 * <code>
	 * helper_view::addCss('v2/css/main.css');
	 * helper_view::addCss(array('v2/css/main.css', 'v2/css/style.css'));
	 * </code>
	 *
	 * @param string|array $files 文件路径
	 * @param int $sort	 加载顺序
	 * @param bool $echo 是否合并输出
	 * @param string $path 路径
	 * @return void
	 */
	static public function addCss($files = array(), $sort = '', $echo = false, $path = 's'){
		static $_csses = array();
		if (!is_array($files))  $files = array($files);
		if(empty($sort) || !is_numeric($sort)) $sort = 99;
		foreach ($files as $v) {
			if(empty($v)) continue;
			$v = trim($v,'/');
			if(empty($_csses[$sort])) $_csses[$sort] = array();
			if (!in_array($v, $_csses[$sort])) {
				$_csses[$sort][] = trim($path,'/').'/'.$v;
			}
		}
			
		//合并输出
		$_arr = array();
		$str = '';
		if(!empty($_csses) && $echo){			
			ksort($_csses);			
			foreach ($_csses as $v) {
				if(empty($v)) continue;
				foreach($v as $v2){
					$_arr[] = $v2;
				}
			}
			if($_arr){
				$_arr = array_flip(array_flip($_arr));
				$url = C('url');
				$web_version = C('web_version');
				$static_compress = C('static_compress');				
				foreach($_arr as $k => $v){
					if($static_compress){
						//压缩输出
						$str .= $v . ',';
					}else{
						//普通输出
						$str .= '<link type="text/css" rel="stylesheet" href="'. $url['img4'].'/'.$v . '?_v=' . $web_version . '" />'."\r\n";
					}						
				}
				if($static_compress){
					$str = '<link type="text/css" rel="stylesheet" href="'. $url['img4'].'/min/?f='.trim($str,','). '&_v=' . $web_version . '" />'."\r\n";
				}
			}
		}
		return $echo ? $str : $_csses;
	}
	
	
	/**
	 * 向头部添加 js 文件
	 *
	 * Example:
	 * <code>
	 * helper_view::addJs('v2/js/prototype.js');
	 * helper_view::addJs(array('v2/js/jQuery.js', 'v2/js/jQuery.calc.js'));
	 * </code>
	 *
	 * @param string|array $files 文件路径
	 * @param int $sort	 加载顺序
	 * @param bool $echo 是否合并输出
	 * @param string $path 路径
	 * @return void
	 */
	static public function addJs($files = array(), $sort = '', $echo = false, $path = 's'){
		static $_jses = array();
		if (!is_array($files))  $files = array($files);
		if(empty($sort) || !is_numeric($sort)) $sort = 99;
		foreach ($files as $v) {
			if(empty($v)) continue;
			$v = trim($v,'/');
			if(empty($_jses[$sort])) $_jses[$sort] = array();
			if (!in_array($v, $_jses[$sort])) {
				$_jses[$sort][] = trim($path,'/').'/'.$v;
			}
		}

		//合并输出
		$_arr = array();
		$str = '';
		if(!empty($_jses) && $echo){			
			ksort($_jses);
			foreach ($_jses as $v) {
				if(empty($v)) continue;
				foreach($v as $v2){
					$_arr[] = $v2;
				}
			}
			if($_arr){
				$_arr = array_flip(array_flip($_arr));
				$url = C('url');
				$web_version = C('web_version');
				$static_compress = C('static_compress');				
				foreach($_arr as $k => $v){
					if($static_compress){
						//压缩输出
						$str .= $v . ',';
					}else{
						//普通输出
						$str .= '<script type="text/javascript" src="'. $url['img4'].'/'.$v . '?_v=' . $web_version . '"></script>'."\r\n";
					}						
				}
				if($static_compress){
					$str = '<script type="text/javascript" src="'. $url['img4'].'/min/?f='.trim($str,','). '&_v=' . $web_version . '"></script>'."\r\n";
				}
			}
		}
		return $echo ? $str : $_jses;
	}
	
	/**
	 * 向头部添加 JS 片断
	 *
	 * Example:
	 * <code>
	 * helper_view::addJsCode('var a = 111;');
	 * helper_view::addJsCode(array('var data = {"json": 1};', 'alert('Hello World!')'));
	 * </code>
	 *
	 * @param string|array $code
	 * @param int $sort	 加载顺序
	 * @param bool $echo 是否合并输出
	 * @return void
	 */
	static public function addJsCode($files, $sort = '', $echo = false) {	
		static $_jscodes = array();
		if (!is_array($files))  $files = array($files);
		if(empty($sort) || !is_numeric($sort)) $sort = 99;
		foreach ($files as $v) {
			if(empty($v)) continue;
			$_jscodes[$sort][] = $v;
		}

		//合并输出
		$_arr = array();
		$str = '';
		if(!empty($_jscodes) && $echo){			
			ksort($_jscodes);			
			foreach ($_jscodes as $v) {
				if(empty($v)) continue;
				foreach($v as $v2){
					$_arr[] = $v2;
					$str .= '<script type="text/javascript">' . "\r\n";
					$str .= implode("\r\n", $v);
					$str .= "\r\n</script>\r\n";
				}
			}
		}
		return $echo ? $str : $_jscodes;
	}
	
}

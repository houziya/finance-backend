<?php
// +----------------------------------------------------------------------
// |  项目公共函数
// +----------------------------------------------------------------------

/*
  U('项目://路由@模块-控制器/操作?参数1=值1&参数N=值N');
  URL组装 支持不同模式和路由
  字符串传参 U（'[项目://][路由@][分组名-模块/]操作? 参数1=值1[&参数N=值N]'）
  数组传参 U（'[项目://][路由@][分组名-模块/]操作',array('参数1'=>'值1' [,'参数N'=>'值N'])）
  如果不定义项目和模块的话 就表示当前项目和模块名称 例如：
  U（'Myapp://User/add'） // 生成Myapp项目的User模块的add操作的URL地址
  U（'Blog/read?id=1'） // 生成Blog模块的read操作 并且id为1的URL地址
  U（'Admin-User/select'） // 生成Admin分组的User模块的select操作的URL地址
  U（'Test@Admin?id=1'）
 */
function url($url, $params = array(), $redirect = false) {
	$u = dispatcher::formatUrlStr($url); //格式化url字符串
	$def = array();
	$get = '';
	$def['m'] = C('sys_default_module');
	$def['c'] = C('sys_default_controller');
	$def['a'] = C('sys_default_action');
	$s = C('sys_url_depr');
	if(empty($u['get'])){
	   $u['get'] = array();	
    }
    $u['get'] = array_merge($u['get'],$params);
    foreach ($u['get'] as $k => $v) {
        if($v === '') continue;
        $get .= $s.$k.$s.$v;
    }
	if($u['m']==$def['m'] && $u['c']==$def['c'] && $u['a']==$def['a'] && empty($u['get'])){
		$url = '';
	}else{
        $url = helper_rewrite::getReWriteUrl($u,$params); //URL重写部分
	}

	//url路径模式处理
	$phpfile = dirname(_PHP_FILE_);
	if ($phpfile == '\\' || $phpfile == '/')  $phpfile = '';
	if(empty($phpfile)&&empty($url))  $url = '';
	$domain = C('url.'.$u['m']);
	if(empty($domain)){
		$domain = HTTP_PROTOCAL . $u['m'].'.'.C('sysconfig.web_domain');
	}
	$url = $domain.$phpfile . $url;
	if ($redirect) {
		redirect($url);
	} else {
		return $url;
	}
}



// 得到勾选的选项框
function select($a, $b) {
	$a = (string) $a;
	$b = (string) $b;
	if ($a === $b) {
		return ' selected="selected"';
	} else {
		return '';
	}
}

//得到勾选的按钮
function radio($a, $b) {
	$a = (string) $a;
	$b = (string) $b;
	if ($a === $b) {
		return ' checked="checked"';
	} else {
		return '';
	}
}

//得到格式化提示
function getStatusTips($id, $type=0) {
	$tpl[0] = array(
		-1 => "<span class='gray4'>⊙</span>",
		0 => "<span class='red'>×</span>",
		1 => "<span class='green'>√</span>",		
	);
	$tpl[1] = array(
		-1 => "<span class='gray4' title='审核失败'>失败</span>",
		0 => "<span class='red' title='待审核'>待审</span>",
		1 => "<span class='green' title='正常'>正常</span>",		
	);
	return isset($tpl[$type][$id]) ? $tpl[$type][$id] : '';
}

function dumps($str,$value=true){
	if($value==true){
		echo '<pre>';print_r($str);exit;
	}else{
		var_dump($str);exit;
	}
}

/**
 * 获取访问的方式  1 get  2 post
 */
if(!function_exists('getRequestMethod'))
{
    function getRequestMethod()
    {
        if($_SERVER['REQUEST_METHOD'] == "POST" )
        {
            return 2;
        }
        else if($_SERVER['REQUEST_METHOD'] == "GET" )
        {
            return 1;
        }
        return 1;
    }
}

// 取得操作成功后要返回的URL地址 默认返回当前模块的默认操作
function getReUrl($name='default') {
	$urls = cookie('web_current');
	$urls = $urls ? @unserialize(urldecode($urls)) : array();
	return isset($urls[$name]) ? $urls[$name] : '';
}
// 设置当前浏览页面
function setReUrl($name='default', $id=''){
	$urls = cookie('web_current');
	$urls = $urls ? @unserialize(urldecode($urls)) : array();
	$urls[$name] = $id ? $id : $_SERVER['REQUEST_URI'];
	cookie('web_current', urlencode(serialize($urls)));
}

// 编码中文（处理中文json_encode编码问题）
function urlEncodeJson($value) {
	$value = is_array($value) ? array_map('urlEncodeJson', $value) : urlencode($value);
	return $value;
}

/**
 * xss过滤函数  所有的post和get全部默认利用此函数处理
 * @param $string
 * @return string
 */
function removeXss($string) {
	$string = helper_string::removeXss($string);
	return $string;
}

/**
 * 输出安全的html，用于过滤危险代码
 * @param string $text 要处理的字符串
 * @param mixed $tags 允许的标签列表，如 table|td|th|td
 * @return string
 */
function safeHtml($text, $tags = null) {
	$string = helper_string::safeHtml($text,$tags);
	return $string;
}

/*
 * 前台sql查询防注入字段过滤
 * @param string $text 要处理的字符串
 * @param bool $isreplace 替换关键字
 * @param bool $islike 替换like关键字
 * @return string
 */
function sqlReplace($string, $isreplace = true, $islike = false){
	$string = strip_tags($string);
	$string = helper_string::sqlReplace($string, $isreplace, $islike);
	return $string;
}

/**
* 计数器 设置、获取、清除（过期时间默认1周）
* 1 获取指定名称计数器: G('name')
* 2 设置指定名称计数器递增或递减: G('name',1) | G('name',-1)
* 2 删除计数器: G('name',null)
* @param string $name 键值
* @param string $value 递增值 默认为1
* @param string $exptime 过期时间
* @return string
*/
function G($name = '', $value = '',$exptime = 604800) {
   $name = 'count_' . $name;
   $cache = cache::getInstance('redis');
   if ($value === null) {
	   $cache->rm($name);
	   return 0;
   } else {
	   if ($value === '') {
		   return (int) $cache->get($name);
	   } else {
		   $n = $cache->incr($name, $value);
			if ($exptime > 0) {
				$cache->setTimeout($name, $exptime);
			}
			return $n;
		}
   }
}

<?php

// +----------------------------------------------------------------------
// | 公共函数库
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
function U($url, $params = array(), $url_mode = '', $redirect = false) {
	$url = dispatcher::U($url, $params, $url_mode);
	if ($redirect) {
		redirect($url);
	} else {
		return $url;
	}
}

// 得到当前网站域名信息
function getUrl() {
	$http = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
	$host = $_SERVER['HTTP_HOST'] ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'] ? $_SERVER['SERVER_NAME'] : getenv('SERVER_NAME'));
	$port = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '80' ? '' : $_SERVER['SERVER_PORT'];
	return $http . $host . $port;
}

//获取客户端IP地（获取真实IP）
function getClientIp(){
	$ip = '';
	if (!empty($_SERVER['REMOTE_ADDR'])) {
		$ip = $_SERVER['REMOTE_ADDR'];
	}elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
		$pos = array_search('unknown', $ips);
		if (false !== $pos) unset($ips[$pos]);
		for ($i = 0; $i < count($ips); $i++) {
			if (!preg_match("#^(10|172\.16|192\.168|127\.0)\.#i", $ips[$i])) {
				$ip = $ips[$i];
				break;
			}
		}
	}
	if (empty($ip) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	}
	return $ip;
}

// 获取客户端IP地址(优先获取代理IP)
function getIp() {
	$ip = '';
	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
		$pos = array_search('unknown', $ips);
		if (false !== $pos) unset($ips[$pos]);
		for ($i = 0; $i < count($ips); $i++) {
			if (!preg_match("#^(10|172\.16|192\.168|127\.0)\.#i", $ips[$i])) {
				$ip = $ips[$i];
				break;
			}
		}
	}
	if (empty($ip)) {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) $ip = $_SERVER['HTTP_CLIENT_IP'];
		elseif (!empty($_SERVER['REMOTE_ADDR'])) $ip = $_SERVER['REMOTE_ADDR'];
	}
	return $ip;
}

/**
 * 自定义异常处理
 * @param string $msg 异常消息
 * @param string $type 异常类型 默认为fee_exception
 * @param integer $code 异常代码 默认为0
 * @return void
 */
function throw_exception($msg, $type = 'fee_exception', $code = 0) {
	if (class_exists($type, false)) {
		throw new $type($msg, $code, false);
	} else {
		halt($msg);  //异常类型不存在则输出错误信息字串
	}
}

// 错误输出
function halt($error) {
	$e = array();
	if (C('sys_debug')) {
		//调试模式下输出错误信息
		if (!is_array($error)) {
			$trace = debug_backtrace();
			//array_shift($trace);
			$e['message'] = $error;
			$e['file'] = $trace[0]['file'];
			$e['class'] = isset($trace[0]['class']) ? $trace[0]['class'] : '';
			$e['function'] = isset($trace[0]['function']) ? $trace[0]['function'] : '';
			$e['line'] = $trace[0]['line'];
			$traceInfo = '';
			$time = date('y-m-d H:i:m');
			foreach ($trace as $t) {
				$class = empty($t['class']) ? '' : $t['class'];
				$type = empty($t['type']) ? '' : $t['type'];
				$traceInfo .= '[' . $time . '] ' . $t['file'] . ' (' . $t['line'] . ') ';
				$traceInfo .= $class . $type . $t['function'] . '(';
				$traceInfo .= @implode(', ', $t['args']);
				$traceInfo .=')<br/>';
			}
			$e['trace'] = $traceInfo;
		} else {
			$e = $error;
		}
	} else {
		//否则定向到错误页面
		$error_page = C('sys_error_page');
		if (!empty($error_page)) {
			redirect($error_page);
		} else {
			if (C('sys_show_error_msg')) $e['message'] = is_array($error) ? $error['message'] : $error;
			else $e['message'] = C('sys_error_message');
		}
	}
	// 包含异常页面模板
	header("http/1.1 404 Not Found");
	include C('sys_tpl_exception_file');
	exit;
}

// URL重定向
function redirect($url, $time = 0, $msg = '') {
	//多行URL地址支持
	$url = str_replace(array("\n", "\r"), '', $url);
	if (empty($msg)) $msg = "系统将在{$time}秒之后自动跳转到{$url}！";
	if (!headers_sent()) {
		// redirect
		if (0 === $time) {
			header("Location: " . $url);
		} else {
			header("refresh:{$time};url={$url}");
			echo($msg);
		}
		exit();
	} else {
		$str = "<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
		if ($time != 0) $str .= $msg;
		exit($str);
	}
}

// 浏览器变量调试输出
function dump($var, $echo = true, $label = null, $strict = true) {
	$label = ($label === null) ? '' : rtrim($label) . ' ';
	if (!$strict) {
		if (ini_get('html_errors')) {
			$output = print_r($var, true);
			$output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
		} else {
			$output = $label . " : " . print_r($var, true);
		}
	} else {
		ob_start();
		var_dump($var);
		$output = ob_get_clean();
		if (!extension_loaded('xdebug')) {
			$output = preg_replace("/\]\=\>\n(\s+)/m", '] => ', $output);
			$output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
		}
	}
	if ($echo) {
		echo($output);
		return null;
	}else return $output;
}

// 取得对象实例 支持调用类的静态方法
function getInstanceOf($name, $method = '', $args = array()) {
	static $_instance = array();
	$identify = empty($args) ? $name . $method : $name . $method . md5(serialize($args));
	if (!isset($_instance[$identify])) {
		if (class_exists($name)) {
			$o = new $name();
			if (method_exists($o, $method)) {
				if (!empty($args)) {
					$_instance[$identify] = call_user_func_array(array(&$o, $method), $args);
				} else {
					$_instance[$identify] = $o->$method();
				}
			}
			else $_instance[$identify] = $o;
		}
		else halt(L('_CLASS_NOT_EXIST_') . ':' . $name);
	}
	return $_instance[$identify];
}

// 优化的require_once
function requireCache($filename) {
	static $_importFiles = array();
	$filename = realpath($filename);
	if (!isset($_importFiles[$filename])) {
		if (file_exists($filename)) {
			require $filename;
			$_importFiles[$filename] = true;
		} else {
			$_importFiles[$filename] = false;
		}
	}
	return $_importFiles[$filename];
}

/**
 * 加载应用函数
 * @param string $name 函数库文件名 不带后缀
 * @param string $baseUrl 路径
 * @param string $ext 导入的文件扩展名
 * @example loadFun('aa/bb') 加载应用级函数
 * @example loadFun('@/aa/bb') 加载框架级函数
 */
function loadFun($name, $baseUrl = '', $ext = '.php') {
	if (empty($baseUrl)) {
		if (0 === strpos($name, '@/')) {
			$baseUrl = FEE_PATH . '/function';
			$name = substr($name, 2);
		} else {
			$baseUrl = FUNC_PATH;			
		}
	}
	$name = trim($name, '/');
	return requireCache($baseUrl . '/' . $name . $ext);
}

/**
 * 加载应用类库
 * @param string $name 类库文件名 不带后缀
 * @param string $baseUrl 路径
 * @param string $ext 导入的文件扩展名
 * @example loadClass('aa/bb') 加载应用级类库
 * @example loadClass('@/aa/bb') 加载框架级类库
 */
function loadClass($name, $baseUrl = '', $ext = '.php') {
	if (empty($baseUrl)) {
		if (0 === strpos($name, '@/')) {
			$baseUrl = FEE_PATH . '/library';
			$name = substr($name, 2);
		} else {
			$baseUrl = LIB_PATH;
		}
	}
	$name = trim($name, '/');
	return requireCache($baseUrl . '/' . $name . $ext);
}

// 快速定义和导入别名
function aliasImport($alias, $classfile = '') {
	static $_alias = array();
	if ('' !== $classfile) {
		// 定义别名导入
		$_alias[$alias] = $classfile;
		return;
	}
	if (is_string($alias)) {
		if (isset($_alias[$alias])) return requireCache($_alias[$alias]);
	}elseif (is_array($alias)) {
		foreach ($alias as $key => $val)
			$_alias[$key] = $val;
		return;
	}
	return false;
}

/**
 * 加载控制器
 * @param string name 控制器名称
 * @param string app 模块
 * @param bool obj 是否实例化
 * @return action object
 */
function A($name, $obj = true) {
	static $_controller = array();
	$key = md5(strtolower($name));
	if (isset($_controller[$key])) return $_controller[$key];
	
	//加载控制器
	$name = trim($name, '/');
	if (strpos($name, '/') === false) {
		$file = APP_PATH . '/controller/'.strtolower($name).'.php';
		$className = 'controller_' . strtolower($name);
	} else {
		$file = APP_PATH . '/controller/' . strtolower($name).'.php';
		$className = 'controller_' . str_replace('/', '_', $name);
	}

	$result = requireCache($file);
	if ($obj) {
		if (class_exists($className, false)) {
			$_controller[$key] = new $className();
			return $_controller[$key];
		} else {
			return false;
		}
	} else {
		return $result;
	}
}

/**
 * 实例化model模型
 * @param string name 模型名称（表名）
 * @return object
 */
function M($name = '') {
	if (empty($name)) return getInstanceOf('model');
	static $_model = array();
	$key = md5(strtolower($name));
	if (isset($_model[$key])) return $_model[$key];
	$_model[$key] = new model($name);
	return $_model[$key];
}

/**
 * 加载模型
 * @param string name Model名称，格式：目录/模型
 * @return model object
 */
function D($name = '') {
	if (empty($name)) return getInstanceOf('model');
	static $_model = array();
	$key = md5(strtolower($name));
	if (isset($_model[$key])) return $_model[$key];

	$name = trim($name, '/');
	if (strpos($name, '/') === false) {		
		$file = APP_PATH . '/model/'.strtolower($name).'.php';
		$className = 'model_' . strtolower($name);
	} else {		
		$file = APP_PATH . '/model/' . strtolower($name).'.php';
		$className = 'model_' . str_replace('/', '_', $name);
		$name = substr(strrchr($name, '/'), 1);
	}
	requireCache($file);
	if (class_exists($className)) {
		$model = new $className($name);
	} else {
		throw_exception(L('_MODEL_NOT_EXIST_') . $className . '.php');
	}
	$_model[$key] = $model;
	return $model;
}

// 获取和设置语言定义(不区分大小写)
function L($name = null, $value = null) {
	static $_lang = array();
	// 空参数返回所有定义
	if (empty($name)) return $_lang;
	// 判断语言获取(或设置)
	// 若不存在,直接返回全大写$name
	if (is_string($name)) {
		$name = strtoupper($name);
		if (is_null($value)) return isset($_lang[$name]) ? $_lang[$name] : $name;
		$_lang[$name] = $value; // 语言定义
		return;
	}
	// 批量定义
	if (is_array($name)) $_lang = array_merge($_lang, array_change_key_case($name, CASE_UPPER));
	return;
}

// 执行行为
function B($name) {
	$class = 'behavior_' . $name;
	loadClass('behavior/' . $name);	
	if (class_exists($class, false)) {
		$behavior = new $class();
		return $behavior->run();
	} else {
		return false;
	}	
}

/**
 * 缓存管理
 * @param mixed $name 缓存名称，如果为数组表示进行缓存设置
 * @param mixed $value 缓存值
 * @param mixed $options 缓存参数
 * @return mixed
 */
function S($name, $value = '', $options = null) {	
	static $cache = array();
	aliasImport('cache');
	if (is_array($options) && empty($cache)) {
		// 缓存操作的同时初始化
		$type = isset($options['type']) ? $options['type'] : '';
		$cache = cache::getInstance($type, $options);
	} elseif (is_array($name)) { // 缓存初始化
		$type = isset($name['type']) ? $name['type'] : '';
		$cache = cache::getInstance($type, $name);
		return $cache;
	} elseif (empty($cache)) { // 自动初始化
		$cache = cache::getInstance();
	}
	if ('' === $value) { // 获取缓存
		if(C('sys_cache_open') == false) return false;
		return $cache->get($name);
	} elseif (is_null($value)) { // 删除缓存
		return $cache->rm($name);
	} else { // 缓存数据
		if (is_array($options)) {
			$expire = isset($options['expire']) ? $options['expire'] : NULL;
		} else {
			$expire = is_numeric($options) ? $options : NULL;
		}
		return $cache->set($name, $value, $expire);
	}
}

// 快速文件数据读取和保存 针对简单类型数据 字符串、数组
function F($name, $value = '', $path = CACHE_PATH) {
	static $_cache = array();
	$name = ltrim($name, '/');
	$filename = $path . '/' . $name . '.php';
	if ('' !== $value) {
		if (is_null($value)) {
			// 删除缓存
			return unlink($filename);
		} else {
			// 缓存数据
			$dir = dirname($filename);
			// 目录不存在则创建
			if (!is_dir($dir)) mkdir($dir);
			$_cache[$name] = $value;
			return file_put_contents($filename, json_encode($value));
		}
	}
	if (isset($_cache[$name])) return $_cache[$name];
	// 获取缓存数据
	if (is_file($filename)) {
		$value = json_decode(file_get_contents($filename), true);
		$_cache[$name] = $value;
	} else {
		$value = false;
	}
	return $value;
}

// 循环创建目录
function mk_dir($dir, $mode = 0777) {
	return mkdir($dir, $mode, true);
//	if (is_dir($dir) || @mkdir($dir, $mode)) return true;
//	if (!@mk_dir(dirname($dir), $mode)) return false;
//	return @mkdir($dir, $mode);
}

/**
 * Cookie 设置、获取、清除
 * 1 获取指定名称cookie: cookie('name')
 * 2 删除默认前缀的所有cookie: cookie(null) | 注：没有默认前缀，不进行删除
 * 3 删除指定前缀所有cookie: cookie(null,'fee_') | 注：前缀不区分大小写
 * 4 清空全部cookie: cookie(null,null)
 * 5 设置指定名称cookie: cookie('name','value') | 指定保存时间: cookie('name','value',3600)
 * 6 删除指定名称cookie: cookie('name',null)
 * $option 可用设置prefix,expire,path,domain
 * 支持数组形式对参数设置:cookie('name','value',array('expire'=>1,'prefix'=>'fee_'))
 * 支持query形式字符串对参数设置:cookie('name','value','prefix=fee_&expire=10000')
 */
function cookie($name, $value = '', $option = null) {
	// 默认设置
	$config = array(
		'prefix' => C('sys_cookie_prefix'), // cookie 名称前缀
		'expire' => C('sys_cookie_expire'), // cookie 保存时间
		'path' => C('sys_cookie_path'), // cookie 保存路径
		'domain' => C('sys_cookie_domain'), // cookie 有效域名
	);
	
	// 参数设置(会覆盖黙认设置)
	if (!empty($option)) {
		if (is_numeric($option)) $option = array('expire' => $option);
		elseif (is_string($option)) parse_str($option, $option);
		$config = array_merge($config, array_change_key_case($option));
	}
	// 清除指定前缀的所有cookie
	if (is_null($name)) {
		if (empty($_COOKIE)) return;

		if (is_null($value)) {
			// 清空全部cookie
			foreach ($_COOKIE as $key => $val) {
				setcookie($key, '', time() - 3600, $config['path'], $config['domain']);
				unset($_COOKIE[$key]);
			}
		} else {
			// 要删除的cookie前缀
			$prefix = empty($value) ? $config['prefix'] : $value;
			if (!empty($prefix)) {
				// 只处理有前缀的cookie
				foreach ($_COOKIE as $key => $val) {
					if (0 === stripos($key, $prefix)) {
						setcookie($key, '', time() - 3600, $config['path'], $config['domain']);
						unset($_COOKIE[$key]);
					}
				}
			}
		}
	} else {
		$name = $config['prefix'] . $name;
		if ('' === $value) {
			return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null; // 获取指定Cookie
		} else {
			if (is_null($value)) {
				setcookie($name, '', time() - 3600, $config['path'], $config['domain']);
				unset($_COOKIE[$name]); // 删除指定cookie
			} else {
				// 设置cookie
				$expire = !empty($config['expire']) ? time() + intval($config['expire']) : 0;
				setcookie($name, $value, $expire, $config['path'], $config['domain']);
				$_COOKIE[$name] = $value;
			}
		}
	}
}

/**
 * Session 设置、获取、清除
 * 1 获取指定名称session: session('name')
 * 2 删除默认前缀的所有session: session(null) | 注：没有默认前缀，不进行删除
 * 3 删除指定前缀的所有session: session(null,'fee') | 注：前缀将不区分大小写
 * 4 清空全部session: session(null,null)
 * 5 设置指定名称session: session('name','value')
 * 6 删除指定名称session: session('name',null)
 * $option 可用设置prefix
 * 支持数组形式对参数设置:session('name','value',array('prefix'=>'fee'))
 * 支持query形式字符串对参数设置:session('name','value','prefix=fee')
 */
function session($name, $value = '', $option = null) {
	if (!isset($_SESSION)) session_start();
	// 默认设置
	$config = array(
		'prefix' => C('sys_session_prefix'), // Session 默认名称前缀
	);
	$prefix = $config['prefix'];
	// 参数设置(会覆盖黙认设置)
	if (!empty($option)) {
		if (is_string($option)) parse_str($option, $option);
		$config = array_merge($config, array_change_key_case($option));
	}

	if (is_null($name)) {
		if (empty($_SESSION)) return;
		if (is_null($value)) {
			// 清空session
			$_SESSION = array();
		} else {
			// 要删除的session前缀
			$prefix = empty($value) ? $config['prefix'] : $value;
			// 只处理有前缀的session
			if (!empty($prefix)) {
				unset($_SESSION[$prefix]);
			}
		}
	} else {
		if ('' === $value) {
			// 获取指定session
			if ($prefix) {
				return isset($_SESSION[$prefix][$name]) ? $_SESSION[$prefix][$name] : null;
			} else {
				return isset($_SESSION[$name]) ? $_SESSION[$name] : null;
			}
		} elseif (is_null($value)) {
			// 删除指定Session
			if ($prefix) {
				unset($_SESSION[$prefix][$name]);
			} else {
				unset($_SESSION[$name]);
			}
		} else {
			// 设置session
			if ($prefix) {
				if (!is_array($_SESSION[$prefix])) $_SESSION[$prefix] = array();
				$_SESSION[$prefix][$name] = $value;
			}else {
				$_SESSION[$name] = $value;
			}
		}
	}
}

/**
 * 可逆加密
 * @param string $string 待加密或解密的字符串
 * @param string $operation DECODE：加密  ENCODE：解密
 * @param string $key 密钥  需要对应的密钥才能解密
 * @param int $expiry 时间  加密字符过期时间
 * @param int $ckey_length 加密强度*16次方 0为1个原文对应1个加密串 大于0为多个加密串 增加破解难度
 * @return string
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0, $ckey_length = 4) {

	if ($operation == 'DECODE') {
		//特殊字符替换
		$string = str_ireplace(array('A4vQo', 'BpUYe', 'NbM8i'), array('+', '/', '='), $string);
	}

	$key = md5($key ? $key : C('sys_default_key'));
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya . md5($keya . $keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for ($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for ($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for ($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if ($operation == 'DECODE') {
		if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		$tmp = $keyc . str_replace('=', '', base64_encode($result));
		//特殊字符替换
		$tmp = str_ireplace(array('+', '/', '='), array('A4vQo', 'BpUYe', 'NbM8i'), $tmp);
		return $tmp;
	}
}

// 清除转义字符
function stripslashesDeep($value) {
	$value = is_array($value) ? array_map('stripslashesDeep', $value) : stripslashes($value);
	return $value;
}

// 添加转义字符
function addslashesDeep($str) {
	$str = is_array($str) ? array_map('addslashesDeep', $str) : addslashes($str);
	return $str;
}

//取得文件扩展后缀
function fileext($filename) {
	return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
}

/**
 * 字符串命名风格转换
 * type
 * =0 将Java风格转换为C的风格
 * =1 将C风格转换为Java的风格
 * @param string $name 字符串
 * @param integer $type 转换类型
 */
function parseName($name, $type = 0) {
	if ($type) {
		return ucfirst(preg_replace("/_([a-zA-Z])/e", "strtoupper('\\1')", $name));
	} else {
		$name = preg_replace("/[A-Z]/", "_\\0", $name);
		return strtolower(trim($name, "_"));
	}
}

// 区间调试开始
function debugStart($label='')
{
	$GLOBALS[$label]['_beginTime'] = microtime(TRUE);
	if ( IS_MEMORY )  $GLOBALS[$label]['_beginMem'] = memory_get_usage();
}

// 区间调试结束，显示指定标记到当前位置的调试
function debugEnd($label='')
{
	$GLOBALS[$label]['_endTime'] = microtime(TRUE);
	echo '<div style="text-align:center;width:100%">Process '.$label.': Times '.number_format($GLOBALS[$label]['_endTime']-$GLOBALS[$label]['_beginTime'],6).'s ';
	if ( IS_MEMORY )  {
		$GLOBALS[$label]['_endMem'] = memory_get_usage();
		echo ' Memories '.number_format(($GLOBALS[$label]['_endMem']-$GLOBALS[$label]['_beginMem'])/1024).' k';
	}
	echo '</div>';
}

// 编译文件
function compile($filename,$runtime=false) {
	if(defined('RUN_TMP') && RUN_TMP==true) return '';
	$content = file_get_contents($filename);
	if(true === $runtime)
	// 替换预编译指令
	$content = preg_replace('/\/\/\[RUNTIME\](.*?)\/\/\[\/RUNTIME\]/s','',$content);
	$content = substr(trim($content),5);
	if('?>' == substr($content,-2))
	$content = substr($content,0,-2);
	return $content;
}

// 去除代码中的空白和注释
function stripWhitespace($content) {
	$stripStr = '';
	//分析php源码
	$tokens =   token_get_all ($content);
	$last_space = false;
	for ($i = 0, $j = count ($tokens); $i < $j; $i++)
	{
		if (is_string ($tokens[$i]))
		{
			$last_space = false;
			$stripStr .= $tokens[$i];
		}
		else
		{
			switch ($tokens[$i][0])
			{
				//过滤各种PHP注释
				case T_COMMENT:
				case T_DOC_COMMENT:
					break;
					//过滤空格
				case T_WHITESPACE:
					if (!$last_space)
					{
						$stripStr .= ' ';
						$last_space = true;
					}
					break;
				default:
					$last_space = false;
					$stripStr .= $tokens[$i][1];
			}
		}
	}
	return $stripStr;
}
<?php

/**
 * 应用程序类 执行应用过程管理
 */
class app {

	/**
	 * 运行应用实例 入口文件使用的快捷方法
	 * @static
	 * @access public
	 * @return void
	 */
	static public function run() {
		// 页面压缩输出支持
		if (C('sys_output_encode')) {
			$zlib = ini_get('zlib.output_compression');
			if (empty($zlib)) ob_start('ob_gzhandler');
		}
		self::init();
		// 记录应用初始化时间
		if (C('sys_show_run_time')) $GLOBALS['sys_time_init'] = microtime(TRUE);
		self::exec();
		return;
	}

	/**
	 * 应用程序初始化
	 * @static
	 * @access public
	 * @return void
	 */
	static public function init() {

		//设定错误和异常的自定义处理
		register_shutdown_function(array('app', 'fatalError'));
		set_error_handler(array('app', 'appError'));
		set_exception_handler(array('app', 'appException'));

		//注册自动加载函数
		if (!function_exists('spl_autoload_register')) {
			throw_exception("spl_autoload does not exist in this PHP installation");
		} else {
			spl_autoload_register(array('app', 'autoload'));
		}

		if (C('sys_session_start') && !isset($_SESSION)) session_start();

		self::checkLanguage();  //语言检查
		self::checkTemplate();  //模板检查
		// 开启静态缓存
		if (C('sys_html_cache')) helper_htmlcache::readHTMLCache();
		return;
	}

	//语言检查 检查浏览器支持语言，并自动加载语言包
	static private function checkLanguage() {
		$langSet = C('sys_default_lang');

		// 不开启语言包功能，仅仅加载框架语言文件直接返回
		if (!C('sys_lang_switch')) {			
			L(include FEE_PATH . '/lang/' . $langSet . '.php');
			return;
		}

		// 通过url变更语言
		if (isset($_GET[C('var_lang')])) {
			$langSet = $_GET[C('var_lang')];
			cookie('language', $langSet, 3600);
		} elseif (cookie('language')) {
			// 获取上次用户的选择
			$langSet = cookie('language');
		}

		// 自动侦测浏览器语言
		if (C('sys_lang_auto') && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			preg_match('/^([a-z\-]+)/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $matches);
			$langSet = strtolower($matches[1]);
			cookie('language', $langSet, 3600);
		}

		// 定义当前语言
		define('LANG_SET', strtolower($langSet));

		// 加载框架语言包
		L(include FEE_PATH . '/lang/' . LANG_SET . '.php');

		//加载项目语言包
		if (is_file(LANG_PATH . '/' . LANG_SET . '.php')) L(include LANG_PATH . '/' . LANG_SET . '.php');

		//加载模块语言包
		if (is_file(LANG_PATH . '/' . MODULE_NAME . '/' . LANG_SET . '.php')) L(include LANG_PATH . '/' . MODULE_NAME . '/' . LANG_SET . '.php');
	}

	//模板检查，如果不存在使用默认
	static private function checkTemplate() {
		//模板路径
		if (!defined('TPL_PATH'))  define('TPL_PATH', APP_PATH . '/view/' . MODULE_NAME);

		//模板前台目录
		define('_TPL_', rtrim(_ROOT_,'/') . '/tpl/' . MODULE_NAME);

		if (C('sys_tpl_theme')) { //开启多套模版主题
			if (C('sys_tpl_detect')) { // 自动侦测模板主题
				$t = C('var_tpl');
				if (isset($_GET[$t])) {
					$tplSet = $_GET[$t];
					cookie('template', $tplSet, 3600);
				} else {
					if (cookie('template')) {
						$tplSet = cookie('template');
					} else {
						$tplSet = C('sys_default_theme');
						cookie('template', $tplSet, 3600);
					}
				}
				//模版主题不存在的话，使用默认模版主题
				if (!is_dir(TPL_PATH . '/' . $tplSet)) $tplSet = C('sys_default_theme');
			}else {
				$tplSet = C('sys_default_theme');
			}
			//模版主题名称
			define('THEME_NAME', $tplSet);
			
			//当前模版主题路径
			define('THEME_PATH', TPL_PATH . '/' . $tplSet);
			//模板主题前台目录
			define('_THEME_', _TPL_ . '/' . $tplSet);
		}else{
			//当前模版主题路径
			define('THEME_PATH', TPL_PATH);
			//模板主题前台目录
			define('_THEME_', _TPL_);
		}		

		//模版缓存路径
		C('app_tpl_cachepath', CACHE_PATH . '/tpl/' . MODULE_NAME);
		//当前操作默认模版
		C('app_tpl_filename', THEME_PATH . '/' . CONTROLLER_NAME . '/' . ACTION_NAME . C('sys_tpl_suffix'));

		//网站前台静态数据目录
		if (!defined('_STATIC_')) define('_STATIC_', rtrim(_ROOT_,'/') . '/s');

		//网站前台静态数据目录
		if (!defined('STATIC_PATH')) {
			if (substr(_STATIC_, 0, 7) == 'http://' || substr(_STATIC_, 0, 8) == 'https://') {
				define('STATIC_PATH', rtrim($_SERVER['DOCUMENT_ROOT'],'/') . preg_replace('|http[s]?://[^/]+|is', '', _STATIC_));
			} else {
				define('STATIC_PATH', rtrim($_SERVER['DOCUMENT_ROOT'],'/') . _STATIC_);
			}
		}
		
		//附件上传目录
		if (!defined('_UPLOAD_')) define('_UPLOAD_', _STATIC_ . '/upload');
		//附件上传绝对路径
		if (!defined('UPLOAD_PATH')) define('UPLOAD_PATH', STATIC_PATH . '/upload');
		
		//模版前台静态数据目录
		define('_TPL_STATIC_', _STATIC_ . '/' . MODULE_NAME);
		return;
	}

	//执行应用程序
	static public function exec() {
		//模块名称安全检测
		if (!preg_match('/^[A-Za-z_0-9]+$/', MODULE_NAME)) {
			throw_exception(L('_CONTROLLER_NOT_EXIST_'));
		}
		//控制器名称安全检测
		if (!preg_match('/^[A-Za-z_0-9]+$/', CONTROLLER_NAME)) {
			throw_exception(L('_CONTROLLER_NOT_EXIST_'));
		}

		//创建控制器实例
		$module = A(MODULE_NAME.'/'.CONTROLLER_NAME);
		if (!$module) {
			// 是否定义模块Empty控制器
			$module = A(MODULE_NAME.'/empty');
			if (!$module) throw_exception(L('_CONTROLLER_NOT_EXIST_') . ' controller_' . MODULE_NAME . '_' . CONTROLLER_NAME . '.php');
		}
		call_user_func(array(&$module, 'action'.ucfirst(ACTION_NAME))); //执行操作
		return;
	}

	/**
	 * 系统自动加载基类库和当前项目的model和action对象
	 * @param string $name 自动加载类名
	 */
	static public function autoload($name) {
		// 检查是否存在别名定义
		if (aliasImport($name)) return;
		// 自动加载当前项目的控制器类和模型类
		if (substr($name, 0, 6) == "model_") {
			$file = APP_PATH . '/' .strtolower(str_replace('_', '/', $name)).'.php';			
		} elseif (substr($name, 0, 11) == "controller_") {
			$file = APP_PATH . '/' .strtolower(str_replace('_', '/', $name)).'.php';
		} else {
			$file = APP_PATH . '/library/' .strtolower(str_replace('_', '/', $name)).'.php';
		}
		requireCache($file);
		return;
	}

	/**
	 * 自定义异常处理
	 * @param mixed $e 异常对象
	 */
	static public function appException($e) {
		halt($e->__toString());
	}

	/**
	 * 自定义错误处理
	 * @param int $errno 错误类型
	 * @param string $errstr 错误信息
	 * @param string $errfile 错误文件
	 * @param int $errline 错误行数
	 * @return void
	 */
	static public function appError($errno, $errstr, $errfile, $errline) {
		switch ($errno) {
			case E_ERROR:
			case E_PARSE:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
			case E_USER_ERROR:
				ob_end_clean();
				// 页面压缩输出支持
				if (C('sys_output_encode')) {
					$zlib = ini_get('zlib.output_compression');
					if (empty($zlib)) ob_start('ob_gzhandler');
				}
				$errorStr = "[$errno] $errstr " . $errfile . " 第 $errline 行.";
				if (C('sys_log_record')) log::record($errorStr, log::ERR);
				function_exists('halt') ? halt($errorStr) : exit('ERROR:' . $errorStr);
				break;
			case E_STRICT:
			case E_USER_WARNING:
			case E_USER_NOTICE:
			case E_WARNING:
				$errorStr = "[$errno] $errstr " . $errfile . " 第 $errline 行.";
				log::record($errorStr, log::WARN);
				break;
			default:
				$errorStr = "[$errno] $errstr " . $errfile . " 第 $errline 行.";
				log::record($errorStr, log::NOTICE);
				break;
		}
	}

	// 致命错误捕获
	static public function fatalError() {
		// 保存日志记录
		if (C('sys_log_record')) log::save();
		if ($e = error_get_last()) {
			switch ($e['type']) {
				case E_ERROR:
				case E_PARSE:
				case E_CORE_ERROR:
				case E_COMPILE_ERROR:
				case E_USER_ERROR:
					ob_end_clean();
					function_exists('halt') ? halt($e) : exit('ERROR:' . $e['message'] . ' in <b>' . $e['file'] . '</b> on line <b>' . $e['line'] . '</b>');
					break;
			}
		}
	}

}

?>
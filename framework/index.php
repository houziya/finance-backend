<?php
// +----------------------------------------------------------------------
// | 框架公共入口文件
// +----------------------------------------------------------------------

//框架需要运行于PHP5.0版本之上
if (version_compare(PHP_VERSION, '5.0.0', '<'))  die('require PHP > 5.0');
if (version_compare(PHP_VERSION, '6.0.0', '<')) {
	@set_magic_quotes_runtime(0);
	define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc() ? true : false);
}
//记录开始运行时间
$GLOBALS['sys_time_start'] = microtime(true);

// 记录内存初始使用
define('IS_MEMORY', function_exists('memory_get_usage'));
if (IS_MEMORY)  $GLOBALS['sys_usemem_start'] = memory_get_usage();

//获取配置值
function C($name=null, $value=null) {
	static $_config = array();
	if (empty($name))  return $_config;

	// 优先执行设置获取或赋值
	if (is_string($name)) {
		if (!strpos($name, '.')) {
			$name = strtolower($name);
			if (is_null($value))  return isset($_config[$name]) ? $_config[$name] : null;
			$_config[$name] = $value;  return;
		}
		// 二维数组设置和获取支持		
		$pos =& $_config;
		$name = explode('.', $name);
        foreach ($name as $part) {
            if (!isset($pos[$part])) return null;
            $pos =& $pos[$part];
        }
		if($value !== null) $pos = $value;
		return $pos;
	}
	// 批量设置
	if (is_array($name))  return $_config = array_merge($_config, array_change_key_case($name));
	return null;
}

// 系统目录定义
define('FEE_PATH', dirname(__FILE__));
if (!defined('APP_PATH'))  define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']));
if (!defined('APP_NAME')) define('APP_NAME', trim(str_replace(dirname(APP_PATH), '', APP_PATH),"/"));
if (!defined('DATA_PATH'))  define('DATA_PATH', APP_PATH . '/data');

//系统路径定义
define('CACHE_PATH', DATA_PATH . '/cache'); //公共缓存路径
define('LOG_PATH', DATA_PATH . '/log'); //项目日志路径
define('CONF_PATH', APP_PATH . '/config'); //项目配置路径
define('FUNC_PATH', APP_PATH . '/function'); //项目函数路径
define('LIB_PATH', APP_PATH . '/library'); //项目类库路径
define('LANG_PATH', APP_PATH . '/lang'); //项目语言包路径

if (empty($_SERVER["HTTP_X_FORWARDED_PROTO"])) {
	define('HTTP_PROTOCAL', 'http://');
} else {
	define('HTTP_PROTOCAL', 'https://');
}

//系统运行环境检测
define('IS_CGI', substr(PHP_SAPI, 0, 3) == 'cgi' ? 1 : 0);
define('IS_CLI',PHP_SAPI=='cli'? 1   :   0);
define('IS_WIN', strstr(PHP_OS, 'WIN') ? 1 : 0);

// 当前文件名
if (IS_CGI) {
	//CGI/FASTCGI模式下
	$_temp = explode('.php', $_SERVER["PHP_SELF"]);
	define('_PHP_FILE_', rtrim(str_replace($_SERVER["HTTP_HOST"], '', $_temp[0] . '.php'), '/'));
} else {
//	define('_PHP_FILE_', '/'.trim($_SERVER["SCRIPT_NAME"], '/'));
	define('_PHP_FILE_', '/index.php');
}

if (!defined('_ROOT_')) {
	// 网站URL根目录
	$_root = dirname(_PHP_FILE_);
	define('_ROOT_', (($_root == '/' || $_root == '\\') ? '/' : $_root));
}
if (!defined('ROOT_PATH'))  define('ROOT_PATH', dirname($_SERVER['SCRIPT_FILENAME']));

//加载系统核心函数
require FEE_PATH . '/function/function.php';

// 加载系统配置文件
C(include FEE_PATH . '/config/config.php');

//加载项目公共配置文件
if(is_file(CONF_PATH . '/config.php')){
	C(include CONF_PATH . '/config.php');
}

//加载线上配置文件
if(is_file(CONF_PATH . '/config.deploy.php')){
	C(include CONF_PATH . '/config.deploy.php');
}

if (C('sys_debug')) {
	//加载框架调试配置
	C(include FEE_PATH . '/config/debug.php');
	//加载项目调试配置
	if (is_file(CONF_PATH . '/debug.php')) {
		C(include (CONF_PATH . '/debug.php'));
	}
}

//加载项目公共函数库
if(is_file(FUNC_PATH . '/function.php')){
	include FUNC_PATH . '/function.php';
}

//加载扩展配置文件
$config_list = C('sys_config_list');
foreach ($config_list as $val){
	if(is_file(CONF_PATH . '/'.$val.'.php')){
		C('_'.$val.'_',array_change_key_case(include CONF_PATH . '/'.$val.'.php'));
	}
}

//加载框架核心类库
require FEE_PATH . '/core/fee_exception.php'; //加载异常处理类
require FEE_PATH . '/core/log.php'; //加载日志类
require FEE_PATH . '/core/app.php'; //应用程序类
require FEE_PATH . '/core/controller.php'; //控制器类
require FEE_PATH . '/core/model.php'; //模型类
require FEE_PATH . '/core/view.php'; //视图类
require FEE_PATH . '/core/dispatcher.php'; //加载url路由解析类

//加载别名
aliasImport(array(	
	'behavior' => FEE_PATH . '/library/behavior/behavior.php',
	'cache' => FEE_PATH . '/library/cache/cache.php',
	'db' => FEE_PATH . '/library/db/db.php',
	'widget' => FEE_PATH . '/library/widget/widget.php',	
	'template' => FEE_PATH . '/library/template/template.php',	
	'taglib' => FEE_PATH . '/library/template/taglib.php',
	'taglib_cx' => FEE_PATH . '/library/template/taglib/taglib_cx.php',
	'taglib_html' => FEE_PATH . '/library/template/taglib/taglib_html.php',
	'advmodel' => FEE_PATH . '/library/model/adv.php',
	'mongomodel' => FEE_PATH . '/library/model/mongo.php',
	'relationmodel' => FEE_PATH . '/library/model/relation.php',
	'viewmodel' => FEE_PATH . '/library/model/view.php',
	
	'helper_captcha' => APP_PATH . '/library/helper/captcha.php',
	'helper_codeswitch' => APP_PATH . '/library/helper/codeswitch.php',
	'helper_cookie' => APP_PATH . '/library/helper/cookie.php',
	'helper_date' => APP_PATH . '/library/helper/date.php',
	'helper_debug' => APP_PATH . '/library/helper/debug.php',
	'helper_dir' => APP_PATH . '/library/helper/dir.php',
	'helper_files' => APP_PATH . '/library/helper/files.php',
	'helper_form' => APP_PATH . '/library/helper/form.php',
	'helper_htmlcache' => APP_PATH . '/library/helper/htmlcache.php',
	'helper_http' => APP_PATH . '/library/helper/http.php',
	'helper_httpdown' => APP_PATH . '/library/helper/httpdown.php',
	'helper_image' => APP_PATH . '/library/helper/image.php',
	'helper_input' => APP_PATH . '/library/helper/input.php',
	'helper_iplocation' => APP_PATH . '/library/helper/iplocation.php',
	'helper_mail' => APP_PATH . '/library/helper/mail.php',
	'helper_networkmac' => APP_PATH . '/library/helper/networkmac.php',
	'helper_page' => APP_PATH . '/library/helper/page.php',
	'helper_pinyin' => APP_PATH . '/library/helper/pinyin.php',
	'helper_polygon' => APP_PATH . '/library/helper/polygon.php',
	'helper_queue' => APP_PATH . '/library/helper/queue.php',
	'helper_rbac' => APP_PATH . '/library/helper/rbac.php',
	'helper_session' => APP_PATH . '/library/helper/session.php',
	'helper_socket' => APP_PATH . '/library/helper/socket.php',
	'helper_splitword' => APP_PATH . '/library/helper/splitword.php',
	'helper_string' => APP_PATH . '/library/helper/string.php',
	'helper_tree' => APP_PATH . '/library/helper/tree.php',
	'helper_uploadfile' => APP_PATH . '/library/helper/uploadfile.php',
	'helper_xml' => APP_PATH . '/library/helper/xml.php',
));

//还原命令行模式参数
if (IS_CLI) {
	$_argv = $argv;
	array_shift($_argv); //去掉第一个无用参数
	if(isset($_argv[0])){
		$_act = explode('.',array_shift($_argv));
		switch (count($_act)) {
			case 1:
				array_unshift($_act, C('sys_default_module'), C('sys_default_controller'));
				break;
			case 2:
				array_unshift($_act, C('sys_default_module'));
				break;
			default:
				break;
		}
		$_get = array(C('var_module')=>$_act[0], C('var_controller')=>$_act[1], C('var_action')=>$_act[2]);
	}else{
		$_get = array(C('var_module')=>C('sys_default_module') ,C('var_controller')=>C('sys_default_controller'), C('var_action')=>C('sys_default_action'));
	}
	$_GET = array_merge($_GET,$_get);
	preg_replace('@(\w+)\/([^,\/]+)@e', '$_GET[\'\\1\']=\'\\2\';', implode('/',$_argv)); //还原$_GET参数
}

//执行url控制器
dispatcher::dispatch();

//设置系统时区 PHP5支持
if (function_exists('date_default_timezone_set'))  date_default_timezone_set(C('sys_default_timezone'));

define('IP', getIp());
define('TIME', time());

// 记录加载文件时间
$GLOBALS['sys_time_load'] = microtime(TRUE);
define('FEE_VERSION', '1.2');
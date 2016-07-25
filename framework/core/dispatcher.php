<?php

// +----------------------------------------------------------------------
// | URL解析、路由和调度
// +----------------------------------------------------------------------
//支持的URL模式
define('URL_COMMON', 0);   //普通模式
define('URL_PATHINFO', 1);   //PATHINFO模式
define('URL_REWRITE', 2);   //REWRITE模式
define('SYS_DOMAIN_MODULE', C('sys_domain_module')); //是否启用二级域名当作模块名

class dispatcher {

	//URL映射到控制器
	static public function dispatch() {
		$urlMode = C('sys_url_mode');
		//入口文件
		if ($urlMode == URL_REWRITE) {
			$url = dirname(_PHP_FILE_);
			if ($url == '/' || $url == '\\') $url = '/';
			define('PHP_FILE', $url);
		}else {
			define('PHP_FILE', _PHP_FILE_);
		}

		//获取二级域名
		if(IS_CLI){
			define('SUB_DOMAIN', $_GET[C('var_module')]);
		}else{		
			if(!defined('SUB_DOMAIN') && !empty($_SERVER['HTTP_HOST'])){
				$subDomain = strtolower(substr($_SERVER['HTTP_HOST'], 0, strpos($_SERVER['HTTP_HOST'], '.')));
				define('SUB_DOMAIN', $subDomain); 
			}
		}

		self::getPathInfo();
		$params = array();
		$pathinfo = trim($_SERVER['PATH_INFO'], '/');
		$depr = C('sys_url_depr');
		
		//有phpinfo路径 则进行路由参数配置		
		if (!empty($pathinfo)) {
			$params = explode($depr, $pathinfo); //url参数
		}
		//处理路由规则
		self::setRouter($params);

		// 获取模块 控制器 方法
		define('MODULE_NAME', self::getModule());
		define('CONTROLLER_NAME', self::getController());
		define('ACTION_NAME', self::getAction());

		// 当前页面地址
		define('_SELF_', empty($_SERVER['REQUEST_URI']) ? $_SERVER['PATH_INFO'] : $_SERVER['REQUEST_URI']);
		//path_info地址
		define('_INFO_', $_SERVER['PATH_INFO']);

		// 当前网站地址
		define('_APP_', PHP_FILE);

		//模块、控制、方法的URL
		if($urlMode){
			if(SYS_DOMAIN_MODULE){
				define('_M_', _APP_);
				define('_C_', rtrim(_APP_,'/') . '/' . CONTROLLER_NAME);
			}else{
				define('_M_', MODULE_NAME == C('sys_default_module') ? _APP_ : rtrim(_APP_,'/') . '/' . MODULE_NAME);
				define('_C_', rtrim(_APP_,'/') . '/' . MODULE_NAME . $depr . CONTROLLER_NAME);
			}			
			define('_A_', _C_ . $depr . ACTION_NAME);
		}else{
			if(SYS_DOMAIN_MODULE){
				define('_M_', _APP_);
			}else{
				define('_M_', _APP_.'?m=' . MODULE_NAME);
			}
			define('_C_', _M_ . '&c=' . CONTROLLER_NAME);
			define('_A_', _C_ . '&a=' . ACTION_NAME);
		}
		
		//保证$_REQUEST正常取值
		$_REQUEST = array_merge($_POST, $_GET);
	}

	//设置路由
	static public function setRouter($params) {
		$var = array();
		if(C('sys_route')){
			$params2 = $params;
			$routers = C('sys_route_rules');
			$router_name = array_shift($params2);
			if($router_name && !empty($routers[$router_name])) {
				$rule = $routers[$router_name];
				$var = self::getRouterFormatData($rule, $params2);
			}elseif($router_name && !empty($routers[$router_name . '@'])) {
				$rule = $routers[$router_name. '@'];
				$var = self::getRouterFormatData($rule, $params2, '@');
			}elseif($router_name && !empty($routers['*'])){
				$rule = $routers['*'];
				$var = self::getRouterFormatData($rule, $params2);
			}elseif($router_name && !empty($routers['*@'])){
				$rule = $routers['*@'];
				$var = self::getRouterFormatData($rule, $params2);
			}
		}

		//没有找到路由 按url默认规则分配参数
		if(empty($var)){
			if(SYS_DOMAIN_MODULE){
				$var[C('var_module')] = SUB_DOMAIN;
			}else{
				if (isset($params[0])) {
					$var[C('var_module')] = array_shift($params);
				} else {
					$var[C('var_module')] = C('sys_default_module');
				}
			}
			if (isset($params[0])) {
				$var[C('var_controller')] = array_shift($params);
			} else {
				$var[C('var_controller')] = C('sys_default_controller');
			}
			if (isset($params[0])) {
				$var[C('var_action')] = array_shift($params);
			} else {
				$var[C('var_action')] = C('sys_default_action');
			}
		}
		
		//检查当前模块是否在系统指定的模块列表中
		$sys_module_list = C('sys_module_list');
		if(!in_array($var[C('var_module')], $sys_module_list)){
			$var[C('var_module')] = array_shift($sys_module_list);
		}

		// 解析剩余的URL参数
		$res = @preg_replace('@(\w+)\/([^,\/]+)@e', '$var[\'\\1\']=\'\\2\';', implode('/', $params));
		$_GET = array_merge($var, $_GET);
	}
	
	/*
	 * 得到路由解析后的数据
	 * @param array $rule 路由规则
	 * @param array $params GET参数
	 * @param int $type 类型 0简单路由  1正则路由
	 * @return array()
	 */
	static public function getRouterFormatData($rule, $params, $type = ''){
		$var = array();
		if($type == ''){
			// 简单路由定义：array('模块','控制器','方法','var1,var2'),
			if(SYS_DOMAIN_MODULE){
				$var[C('var_module')] = SUB_DOMAIN;
			}else{
				$var[C('var_module')] = !empty($rule[0]) ? $rule[0] : (isset($params[0]) ? array_shift($params) : C('sys_default_module'));
			}			
			$var[C('var_controller')] = !empty($rule[1]) ? $rule[1] : (isset($params[0]) ? array_shift($params) : C('sys_default_controller'));
			$var[C('var_action')] = !empty($rule[2]) ? $rule[2] : (isset($params[0]) ? array_shift($params) : C('sys_default_action'));

			// 处理路由规则的路由参数			
			if (!empty($rule[3])) {
				$vars = explode(',', $rule[3]);
				for ($i = 0; $i < count($vars); $i++) {
					$tmp = array_shift($params);
					if ($tmp !== null) $var[$vars[$i]] = $tmp;
				}
			}
		}elseif($type == '@'){
			// 正则路由定义：array('模块','控制器','方法','var1,var2','/^(\d+)\/(\d+)/'),
			$depr = C('sys_url_depr');
			foreach ($rule as $_rule) {
				if (preg_match($_rule[4], implode($depr, $params))) {
					if(SYS_DOMAIN_MODULE){
						$var[C('var_module')] = SUB_DOMAIN;
					}else{
						$var[C('var_module')] = !empty($_rule[0]) ? $_rule[0] : (isset($params[0]) ? array_shift($params) : C('sys_default_module'));
					}					
					$var[C('var_controller')] = !empty($_rule[1]) ? $_rule[1] : (isset($params[0]) ? array_shift($params) : C('sys_default_controller'));
					$var[C('var_action')] = !empty($_rule[2]) ? $_rule[2] : (isset($params[0]) ? array_shift($params) : C('sys_default_action'));
					
					// 处理路由规则的路由参数
					if (!empty($_rule[3])) {
						$vars = explode(',', $_rule[3]);
						for ($i = 0; $i < count($vars); $i++){
							$tmp = array_shift($params);
							if ($tmp !== null) $var[$vars[$i]] = $tmp;
						}
					}
				}
			}
		}
		return $var;
	}

	//获得服务器的PATH_INFO信息
	public static function getPathInfo() {
		if (!empty($_SERVER['PATH_INFO'])) {
			$pathInfo = $_SERVER['PATH_INFO'];
			if (0 === strpos($pathInfo, $_SERVER['SCRIPT_NAME']))
				$path = substr($pathInfo, strlen($_SERVER['SCRIPT_NAME']));
			else
				$path = $pathInfo;
		}elseif (!empty($_SERVER['ORIG_PATH_INFO'])) {
			$pathInfo = $_SERVER['ORIG_PATH_INFO'];
			if (0 === strpos($pathInfo, $_SERVER['SCRIPT_NAME']))
				$path = substr($pathInfo, strlen($_SERVER['SCRIPT_NAME']));
			else
				$path = $pathInfo;
		}elseif (!empty($_SERVER['REDIRECT_PATH_INFO'])) {
			$path = $_SERVER['REDIRECT_PATH_INFO'];
		} elseif (!empty($_SERVER["REDIRECT_Url"])) {
			$path = $_SERVER["REDIRECT_Url"];
			if (empty($_SERVER['QUERY_STRING']) || $_SERVER['QUERY_STRING'] == $_SERVER["REDIRECT_QUERY_STRING"]) {
				$parsedUrl = parse_url($_SERVER["REQUEST_URI"]);
				if (!empty($parsedUrl['query'])) {
					$_SERVER['QUERY_STRING'] = $parsedUrl['query'];
					parse_str($parsedUrl['query'], $GET);
					$_GET = array_merge($_GET, $GET);
					reset($_GET);
				} else {
					unset($_SERVER['QUERY_STRING']);
				}
				reset($_SERVER);
			}
		}
		$suffix = C('sys_url_suffix');
		if ($suffix && !empty($path)) {
			$suffix = substr($suffix, 1);
			$path = preg_replace('/\.' . $suffix . '$/', '', $path);
		}
		$_SERVER['PATH_INFO'] = empty($path) ? '/' : $path;
	}

	//获得模块名称
	static public function getModule($var='') {
		$var = $var ? $var : C('var_module');
		$name = !empty($_GET[$var]) ? $_GET[$var] : C('sys_default_module');
		unset($_GET[$var]);
		return strtolower($name);
	}

	//获得控制器名称
	static public function getController($var='') {
		$var = $var ? $var : C('var_controller');
		$name = !empty($_GET[$var]) ? $_GET[$var] : C('sys_default_controller');
		unset($_GET[$var]);
		return strtolower($name);
	}

	//获得操作名称
	static public function getAction($var='') {
		$var = $var ? $var : C('var_action');
		$name = !empty($_GET[$var]) ? $_GET[$var] : C('sys_default_action');
		unset($_GET[$var]);
		return strtolower($name);
	}

	//url构造函数
	static public function U($url, $params=array(), $url_mode='') {
		if (!is_numeric($url_mode))  $url_mode = C('sys_url_mode');
		$u = self::formatUrlStr($url); //格式化url字符串
		$def = array();
		$get = '';
		$def['m'] = C('sys_default_module');
		$def['c'] = C('sys_default_controller');
		$def['a'] = C('sys_default_action');
		$s = $url_mode==0 ? '&' : C('sys_url_depr');
		
		//开始处理url组合
		if ($url_mode > 0) {
			if($u['get']){
				foreach ($u['get'] as $k => $v) {
					$get .= $s.$k.$s.$v;
				}
			}

			if($u['m']==$def['m'] && $u['c']==$def['c'] && $u['a']==$def['a'] && empty($u['get'])){
				$url = '';
			}else{
				if(C('sys_domain_module')){
					$url = '/' . $u['c'] . $s . $u['a'] . $get;
				}else{
					$url = '/' . $u['m'] . $s . $u['c'] . $s . $u['a'] . $get;
				}
				if(C('sys_url_suffix')){
					$url = rtrim($url, $s);
					$url .= C('sys_url_suffix');
				}
			}

			//url路径模式处理
			if ($url_mode == URL_REWRITE) {
				$phpfile = dirname(_PHP_FILE_);
				if ($phpfile == '\\' || $phpfile == '/')  $phpfile = '';
				if(empty($phpfile)&&empty($url))  $url = '/';
			}else {
				$phpfile = _PHP_FILE_;
			}
			$url = $phpfile . $url;
		} else {
			if($u['get'])  $get = http_build_query($u['get']);
			if($u['m']==$def['m'] && $u['c']==$def['c'] && $u['a']==$def['a'] && empty($u['get'])){
				$url = _PHP_FILE_;
			}else{
				$url = '?'.C('var_module').'='.$u['m'].$s.C('var_controller').'='.$u['c'].$s.C('var_action').'='.$u['a'].$s.$get;
				$url = _PHP_FILE_.rtrim($url, $s);
			}
		}
		return $url;
	}

	//url字符串分析 route@admin-user/info?v1=1&v2=2
	static public function formatUrlStr($url) {
		if (0 === strpos($url, '/'))
			$url = substr($url, 1);
		if (!strpos($url, '://')) // 没有指定项目名 使用当前项目名
			$url = 'fee://' . $url;

		// 分析URL地址
		$array = parse_url($url);
		$route = C('_route_');
		$app = ('fee' == $array['scheme']) ? '' : $array['scheme'];
		$route = isset($array['user']) && isset($route[$array['user']]) ? $array['user'] : '';
		$module = MODULE_NAME;
		if (isset($array['path'])) {
			//指定了模块和操作
			$action = substr($array['path'], 1);
			if (strpos($array['host'], '-')) {
				list($module, $controller) = explode('-', $array['host']);
			} else {
				$controller = $array['host'];
			}
		} else {
			//只指定操作
			$controller = CONTROLLER_NAME;
			$action = $array['host'];
		}
		//处理GET参数
		$query = array();
		if(isset($array['query'])){
			parse_str($array['query'], $query);
		}
		
		$var = array();
		$var['r'] = $route;
		$var['m'] = $module;
		$var['c'] = $controller;
		$var['a'] = $action;
		$var['get'] = $query;
		return $var;
	}

}

?>
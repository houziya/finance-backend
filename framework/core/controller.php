<?php

/**
 * controller控制器基类 抽象类
 */
abstract class controller {

	// 视图实例对象
	protected $_view = null;

	//架构函数 取得模板对象实例
	public function __construct() {
		//实例化视图类
		$this->_view = new view();
		//控制器初始化
		if (method_exists($this, '_initialize')) $this->_initialize();
	}

	//是否AJAX请求
	protected function isAjax() {
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
			if ('xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])) return true;
		}
		if (!empty($_POST[C('var_ajax_submit')]) || !empty($_GET[C('var_ajax_submit')])) return true;
		return false;
	}

	/**
	 * 模板显示 调用内置的模板引擎显示方法，
	 * @access protected
	 * @param string $templateFile 指定要调用的模板文件,默认为空 由系统自动定位模板文件
	 * @param string $charset 输出编码
	 * @param string $contentType 输出类型
	 * @return void
	 */
	protected function display($templateFile = '', $charset = '', $contentType = '') {
		$this->_view->display($templateFile, $charset, $contentType);
	}

	/**
	 *  获取输出页面内容
	 * 调用内置的模板引擎fetch方法，
	 * @access protected
	 * @param string $templateFile 指定要调用的模板文件,默认为空 由系统自动定位模板文件
	 * @return string
	 */
	protected function fetch($templateFile = '') {
		return $this->_view->fetch($templateFile);
	}

	/**
	 * 创建静态页面
	 * @access protected
	 * @htmlfile 生成的静态文件名称
	 * @htmlpath 生成的静态文件路径
	 * @param string $templateFile 指定要调用的模板文件,默认为空 由系统自动定位模板文件
	 * @return string
	 */
	protected function buildHtml($htmlfile = '', $htmlpath = '', $templateFile = '') {
		$content = $this->fetch($templateFile);
		if (empty($htmlpath)) $htmlpath = APP_PATH . '/html';
		if (substr($htmlpath, -1) != '/') $htmlpath .= '/';
		$htmlfile = $htmlpath . $htmlfile . C('sys_html_suffix');
		if (!is_dir(dirname($htmlfile))) {
			// 如果静态目录不存在 则创建
			mkdir(dirname($htmlfile), 0755, true);
		}
		if (false === file_put_contents($htmlfile, $content)) throw_exception(L('_CACHE_WRITE_ERROR_') . ':' . $htmlfile);
		return $content;
	}

	/**
	 * 模板变量赋值
	 * @access protected
	 * @param mixed $name 要显示的模板变量
	 * @param mixed $value 变量的值
	 * @return Action
	 */
	protected function assign($name, $value = '') {
		$this->_view->assign($name, $value);
		return $this;
	}
	
	/**
	 * 设置模板显示变量的值
	 * @param string $name 模板显示名称
	 * @param string $name 模板显示变量
	 * @return mixed
	 */
	public function __set($name,$value) {
        $this->_view->assign($name,$value);
    }

	/**
	 * 取得模板显示变量的值
	 * @param string $name 模板显示变量
	 * @return mixed
	 */
	public function get($name = '') {
		return $this->_view->get($name);
	}

	//Trace变量赋值
	protected function trace($name, $value = '') {
		$this->_view->trace($name, $value);
	}

	/**
	 * 魔术方法 有不存在的操作的时候执行
	 * @access public
	 * @param string $method 方法名
	 * @param array $args 参数
	 * @return mixed
	 */
	public function __call($method, $args) {
		if (0 === strcasecmp($method, 'action'.ucfirst(ACTION_NAME))) {
			if (method_exists($this, '_empty')) {
				// 如果定义了_empty操作 则调用
				$this->_empty($method, $args);
			} elseif (file_exists(C('app_tpl_filename'))) {
				// 检查是否存在默认模版 如果有直接输出模版
				$this->display();
			} else {
				// 抛出异常
				throw_exception(L('_ERROR_ACTION_') . ACTION_NAME);
			}
		} else {
			switch (strtolower($method)) {
				// 判断提交方式
				case 'ispost' :
				case 'isget' :
				case 'ishead' :
				case 'isdelete' :
				case 'isput' :
					return strtolower($_SERVER['REQUEST_METHOD']) == strtolower(substr($method, 2));
				// 获取变量 支持过滤和默认值 调用方式 $this->_post($key,$default,$filter);
				case '_get' : $input = $_GET;
					break;
				case '_post' : $input = $_POST;
					break;
				case '_put' : parse_str(file_get_contents('php://input'), $input);
					break;
				case '_param' :
					switch ($_SERVER['REQUEST_METHOD']) {
						case 'POST':
							$input = $_POST;
							break;
						case 'PUT':
							parse_str(file_get_contents('php://input'), $input);
							break;
						default:
							$input = $_GET;
					}
					break;
				case '_request' : $input = $_REQUEST;
					break;
				case '_session' : $input = $_SESSION;
					break;
				case '_cookie' : $input = $_COOKIE;
					break;
				case '_server' : $input = $_SERVER;
					break;
				case '_globals' : $input = $GLOBALS;
					break;
				default:
					throw_exception(__CLASS__ . ':' . $method . L('_METHOD_NOT_EXIST_'));
			}
			if (!isset($args[0])) {
				$data = $input; // 获取全局变量
				$filters = C('sys_default_key_filter');
				$filters2 = C('sys_default_key_filter'.strtolower($method));
				$filters = $filters2 ? $filters2.','.$filters : $filters;
				if ($filters) {
					$filters = explode(',', trim($filters,','));
					foreach ($filters as $filter) {
						if (function_exists($filter)) {
							$data = is_array($data) ? array_map($filter, $data) : $filter($data); // 参数过滤
						}
					}
				}
			} elseif (isset($input[$args[0]])) { // 取值操作
				$data = $input[$args[0]];
				$filters = isset($args[2]) ? $args[2] : C('sys_default_key_filter');
				$filters2 = C('sys_default_key_filter'.strtolower($method));
				$filters = $filters2 ? $filters2.','.$filters : $filters;
				if ($filters) {
					$filters = explode(',', trim($filters,','));
					foreach ($filters as $filter) {
						if (function_exists($filter)) {
							$data = is_array($data) ? array_map($filter, $data) : $filter($data); // 参数过滤
						}
					}
				}
			} else { // 变量默认值
				$data = isset($args[1]) ? $args[1] : NULL;
			}
			return $data;
		}
	}

	/**
	 * 操作错误跳转的快捷方法
	 * @param string $message 错误信息
	 * @param string $jumpUrl 页面跳转地址
	 * @param Boolean|array $ajax 是否为Ajax方式
	 */
	protected function error($message, $jumpUrl = '', $ajax = false) {
		$this->dispatchJump($message, 0, $jumpUrl, $ajax);
	}

	/**
	 * 操作成功跳转的快捷方法
	 * @param string $message 提示信息
	 * @param Boolean $ajax 是否为Ajax方式
	 */
	protected function success($message, $jumpUrl = '', $ajax = false) {
		$this->dispatchJump($message, 1, $jumpUrl, $ajax);
	}

	/**
	 * Ajax方式返回数据到客户端
	 * @param mixed $data 要返回的数据
	 * @param String $status ajax返回类型 JSON XML
	 */
	protected function ajaxReturn($data, $type = '') {
		if (empty($type)) $type = C('sys_default_ajax_return');
		switch (strtoupper($type)) {
			case 'JSON' :
				// 返回JSON数据格式到客户端 包含状态信息
				header('Content-Type:application/json; charset=utf-8');
				exit(json_encode($data));
			case 'XML' :
				// 返回xml格式数据
				header('Content-Type:text/xml; charset=utf-8');
				exit(xml_encode($data));
			case 'JSONP':
				// 返回JSON数据格式到客户端 包含状态信息
				header('Content-Type:application/json; charset=utf-8');
				$handler = isset($_GET[C('var_jsonp_handler')]) ? $_GET[C('var_jsonp_handler')] : 'callback';
				exit($handler . '(' . json_encode($data) . ');');
			case 'EVAL' :
				// 返回可执行的js脚本
				header('Content-Type:text/html; charset=utf-8');
				exit($data);
			default :
			// 用于扩展其他返回格式数据
		}
	}

	/**
	 * 默认跳转操作 支持错误导向和正确跳转
	 * 调用模板显示 默认为public目录下面的success页面
	 * 提示页面为可配置 支持模板标签
	 * @param string $message 提示信息
	 * @param Boolean $status 状态
	 * @param string $jumpUrl 页面跳转地址
	 * @param Boolean $ajax 是否为Ajax方式
	 */
	private function dispatchJump($message, $status = 1, $jumpUrl = '', $ajax = false) {
		if ($ajax || $this->isAjax()) {// AJAX提交
			$data = is_array($ajax) ? $ajax : array();
			$data['info'] = $message;
			$data['status'] = $status;
			$data['url'] = $jumpUrl;
			$this->ajaxReturn($data);
		}
		if (is_numeric($ajax)) $this->assign('waitSecond', $ajax);
		if (!empty($jumpUrl)) $this->assign('jumpUrl', $jumpUrl);
		// 提示标题
		$this->assign('msgTitle', $status ? L('_OPERATION_SUCCESS_') : L('_OPERATION_FAIL_'));
		//如果设置了关闭窗口，则提示完毕后自动关闭窗口
		if ($this->get('closeWin')) $this->assign('jumpUrl', 'javascript:window.close();');
		$this->assign('status', $status);   // 状态
		$this->assign('message', $message); // 提示信息
		//保证输出不受静态缓存影响
		C('sys_html_cache', false);
		if ($status) { //发送成功信息
			// 成功操作后默认停留1秒
			if (!is_numeric($this->get('waitSecond'))) $this->assign('waitSecond', "1");
			// 默认操作成功自动返回操作前页面
			if (!$this->get('jumpUrl')) $this->assign("jumpUrl", $_SERVER["HTTP_REFERER"]);
			$this->display(C('sys_tpl_ctl_success'));
			exit;
		}else {
			//发生错误时候默认停留3秒
			if (!is_numeric($this->get('waitSecond'))) $this->assign('waitSecond', "3");
			// 默认发生错误的话自动返回上页
			if (!$this->get('jumpUrl')) $this->assign('jumpUrl', "javascript:history.back(-1);");
			$this->display(C('sys_tpl_ctl_error'));
			exit;
		}
	}

}
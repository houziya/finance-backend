<?php

/**
 * 视图输出 支持缓存和页面压缩
 */
class view {

	protected $tVar = array(); // 模板输出变量
	protected $trace = array();  // 页面trace变量
	protected $templateFile = '';   // 模板文件名

	/**
	 * 模板变量赋值
	 * @access public
	 * @param mixed $name
	 * @param mixed $value
	 */

	public function assign($name, $value = '') {
		if (is_array($name)) {
			$this->tVar = array_merge($this->tVar, $name);
		} else {
			$this->tVar[$name] = $value;
		}
	}

	/**
	 * Trace变量赋值
	 * @param mixed $name
	 * @param mixed $value
	 */
	public function trace($title, $value = '') {
		if (is_array($title)) $this->trace = array_merge($this->trace, $title);
		else $this->trace[$title] = $value;
	}

	/**
	 * 取得模板变量的值
	 * @access public
	 * @param string $name
	 * @return mixed
	 */
	public function get($name = '') {
		if ('' === $name) return $this->tVar;
		return isset($this->tVar[$name]) ? $this->tVar[$name] : false;
	}

	// 调试页面所有的模板变量
	public function traceVar() {
		foreach ($this->tVar as $name => $val) {
			dump($val, 1, '[' . $name . ']<br/>');
		}
	}

	/**
	 * 加载模板和页面输出 可以返回输出内容
	 * @access public
	 * @param string $templateFile 模板文件名
	 * @param string $charset 模板输出字符集
	 * @param string $contentType 输出类型
	 * @return mixed
	 */
	public function display($templateFile = '', $charset = '', $contentType = '') {
		// 解析并获取模板内容
		$content = $this->fetch($templateFile);
		// 输出模板内容
		$this->render($content, $charset, $contentType);
	}

	/**
	 * 解析和获取模板内容 用于输出
	 * @access public
	 * @param string $templateFile 模板文件名
	 * @return string
	 */
	public function fetch($templateFile = '') {
		$GLOBALS['sys_time_view_start'] = microtime(TRUE);
		// 自动定位模板文件
		if (!file_exists($templateFile)) $templateFile = $this->parseTemplateFile($templateFile);

		//页面缓存
		ob_start();
		ob_implicit_flush(0);
		if ('php' == strtolower(C('sys_tpl_engine'))) {
			// 模板阵列变量分解成为独立变量
			extract($this->tVar, EXTR_OVERWRITE);
			// 直接载入PHP模板
			include $templateFile;
		} elseif ($this->checkCache($templateFile)) {
			// 缓存有效有效 调用模板引擎 分解变量并载入模板缓存
			extract($this->tVar, EXTR_OVERWRITE);
			//载入模版缓存文件
			include C('app_tpl_cachepath') . '/' . md5($templateFile) . C('sys_tpl_cache_suffix');
		} else {
			// 缓存无效 重新编译
			$tpl = getInstanceOf('template');
			// 编译并加载模板文件
			$tpl->load($templateFile, $this->tVar);
		}
		$this->templateFile = $templateFile;
		// 获取并清空缓存
		$content = ob_get_clean();
		// 模板内容替换
		$content = $this->templateContentReplace($content);
		// 输出模板文件
		return $content;
	}

	/**
	 * 检查缓存文件是否有效  如果无效则需要重新编译
	 * @param string $tplTemplateFile  模板文件名
	 * @return boolen
	 */
	protected function checkCache($tplTemplateFile) {
		if (!C('sys_tpl_cache')) return false; // 优先对配置设定检测 
		$tplCacheFile = C('app_tpl_cachepath') . '/' . md5($tplTemplateFile) . C('sys_tpl_cache_suffix');
		if (!is_file($tplCacheFile)) {
			return false;
		} elseif (filemtime($tplTemplateFile) > filemtime($tplCacheFile)) {
			// 模板文件如果有更新则缓存需要更新
			return false;
		} elseif (C('sys_tpl_cache_time') != -1 && time() > filemtime($tplCacheFile) + C('sys_tpl_cache_time')) {
			// 缓存是否在有效期
			return false;
		}
		//缓存有效
		return true;
	}

	/**
	 * 输出模板
	 * @param string $content 模板内容
	 * @param boolean $display 是否直接显示
	 * @return mixed
	 */
	protected function render($content, $charset = '', $contentType = '') {
		// 网页字符编码
		if (empty($charset)) $charset = C('sys_default_charset');
		if (empty($contentType)) $contentType = C('sys_tpl_content_type');
		if (!IS_CLI) {
			header("Content-Type:" . $contentType . "; charset=" . $charset);
			//header("Cache-control: private");  //支持页面回跳
		}
		if (C('sys_html_cache')) helper_htmlcache::writeHTMLCache($content);
		if (C('sys_show_run_time')) {
			$runtime = '<div id="fee_run_time" class="fee_run_time">' . $this->showTime() . '</div>';
			if (strpos($content, '{__RUNTIME__}')) $content = str_replace('{__RUNTIME__}', $runtime, $content);
			else $content .= $runtime;
		}
		echo $content;
		if (C('sys_show_trace')) $this->showTrace();
	}

	//模板内容替换
	protected function templateContentReplace($content) {
		// 系统默认的特殊变量替换
		$replace = array(
			'_TPL_STATIC_' => _TPL_STATIC_, // 项目公共目录
			'_STATIC_' => _STATIC_, // 站点静态数据目录
			'_TPL_' => _TPL_, // 项目模板目录
			'_THEME_' => _THEME_, // 项目模板主题目录
			'_ROOT_' => _ROOT_, // 当前网站地址
			'_APP_' => _APP_, // 当前项目地址
			'_M_' => _M_, // 当前模块地址
			'_C_' => _C_, // 当前模块地址
			'_A_' => _A_, // 当前操作地址
			'_SELF_' => _SELF_, // 当前页面地址
			'_INFO_' => _INFO_, // PATH_INFO地址
		);

		if (C('sys_token')) {
			if (strpos($content, '{__TOKEN__}')) {
				// 指定表单令牌隐藏域位置
				$replace['{__TOKEN__}'] = $this->buildFormToken();
			} elseif (strpos($content, '{__NOTOKEN__}')) {
				// 标记为不需要令牌验证
				$replace['{__NOTOKEN__}'] = '';
			} elseif (preg_match('/<\/form(\s*)>/is', $content, $match)) {
				// 智能生成表单令牌隐藏域
				$replace[$match[0]] = $this->buildFormToken() . $match[0];
			}
		}
		// 允许用户自定义模板的字符串替换
		if (is_array(C('sys_tpl_replace_string'))) $replace = array_merge($replace, C('sys_tpl_replace_string'));
		$content = str_replace(array_keys($replace), array_values($replace), $content);
		return $content;
	}

	//创建表单令牌隐藏域
	private function buildFormToken() {
		// 开启表单验证自动生成表单令牌
		$tokenName = C('sys_token_name');
		$tokenValue = md5(microtime(TRUE));
		$token = '<input type="hidden" name="' . $tokenName . '" value="' . $tokenValue . '" />';
		$_SESSION[$tokenName] = $tokenValue;
		return $token;
	}

	//自动定位模板文件
	public function parseTemplateFile($templateFile) {
		if ('' == $templateFile) {
			// 如果模板文件名为空 按照默认规则定位
			$templateFile = C('app_tpl_filename');
		} elseif (strpos($templateFile, '@')) {
			// 引入其它主题的操作模板 必须带上模块名称 例如 blue@user:add
			$templateFile = dirname(THEME_PATH) . '/' . str_replace(array('@', ':'), array('/', '/'), $templateFile) . C('sys_tpl_suffix');
		} elseif (strpos($templateFile, ':') || strpos($templateFile, '/')) {
			// 引入其它模块的操作模板
			$templateFile = THEME_PATH . '/' . str_replace(':', '/', $templateFile) . C('sys_tpl_suffix');
		} elseif (!is_file($templateFile)) {
			// 引入当前模块的其它操作模板
			$templateFile = THEME_PATH . '/' . CONTROLLER_NAME . '/' . $templateFile . C('sys_tpl_suffix');
		}
		if (!file_exists($templateFile)) throw_exception(L('_TEMPLATE_NOT_EXIST_') . '[' . $templateFile . ']');
		return $templateFile;
	}

	//显示运行时间、数据库操作、缓存次数、内存使用信息
	private function showTime() {
		// 显示运行时间
		$startTime = $GLOBALS['sys_time_view_start'];
		$endTime = microtime(TRUE);
		$total_run_time = number_format(($endTime - $GLOBALS['sys_time_start']), 3);
		$showTime = 'Process: ' . $total_run_time . 's ';

		if (C('sys_show_adv_time')) {
			// 显示详细运行时间
			$_load_time = number_format(($GLOBALS['sys_time_load'] - $GLOBALS['sys_time_start']), 3);
			$_init_time = number_format(($GLOBALS['sys_time_init'] - $GLOBALS['sys_time_load']), 3);
			$_exec_time = number_format(($startTime - $GLOBALS['sys_time_init']), 3);
			$_parse_time = number_format(($endTime - $startTime), 3);
			$showTime .= '( Load:' . $_load_time . 's Init:' . $_init_time . 's Exec:' . $_exec_time . 's Template:' . $_parse_time . 's )';
		}
		if (C('sys_show_db_time') && class_exists('db', false)) {
			// 显示数据库操作次数
			$db = db::getInstance();
			$showTime .= ' | DB :' . $db->Q() . ' queries ' . $db->W() . ' writes ';
		}
		if (C('sys_show_cache_time') && class_exists('cache', false)) {
			// 显示缓存读写次数
			$cache = cache::getInstance();
			$showTime .= ' | Cache :' . $cache->Q() . ' gets ' . $cache->W() . ' writes ';
		}
		if (IS_MEMORY && C('sys_show_usemem')) {
			// 显示内存开销
			$startMem = array_sum(explode(' ', $GLOBALS['sys_usemem_start']));
			$endMem = array_sum(explode(' ', memory_get_usage()));
			$showTime .= ' | UseMem:' . number_format(($endMem - $startMem) / 1024) . ' kb';
		}
		return $showTime;
	}

	/**
	 * 显示页面Trace信息
	 * @access private
	 */
	private function showTrace() {
		// 显示页面Trace信息 读取Trace定义文件
		// 定义格式 return array('当前页面'=>$_SERVER['PHP_SELF'],'通信协议'=>$_SERVER['SERVER_PROTOCOL'],...);
		$traceFile = APP_PATH . '/' . MODULE_NAME . '/config/trace.inc.php';
		$_trace = is_file($traceFile) ? include $traceFile : array();
		// 系统默认显示信息
		$this->trace('当前页面', $_SERVER['REQUEST_URI']);
		$this->trace('模板缓存', C('app_tpl_cachepath') . '/' . md5($this->templateFile) . C('sys_tpl_cache_suffix'));
		$this->trace('请求方法', $_SERVER['REQUEST_METHOD']);
		$this->trace('通信协议', $_SERVER['SERVER_PROTOCOL']);
		$this->trace('请求时间', date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']));
		$this->trace('用户代理', $_SERVER['HTTP_USER_AGENT']);
		$this->trace('会话ID', session_id());
		$log = log::$log;
		$this->trace('日志记录', count($log) ? count($log) . '条日志<br/>' . implode('<br/>', $log) : '无日志记录');
		$files = get_included_files();
		$this->trace('加载文件', count($files) . str_replace("\n", '<br/>', substr(substr(print_r($files, true), 7), 0, -2)));
		$_trace = array_merge($_trace, $this->trace);
		// 调用Trace页面模板
		include C('sys_tpl_trace_file');
	}

}
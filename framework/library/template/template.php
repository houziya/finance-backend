<?php

/**
  +------------------------------------------------------------------------------
 * 编译型模板引擎 支持动态缓存
  +------------------------------------------------------------------------------
 */
class template {

	// 模板页面中引入的标签库列表
	protected $tagLib = array();
	// 当前模板文件
	protected $templateFile = '';
	// 模板变量
	public $tVar = array();
	public $config = array();
	private $literal = array();

	//取得模板实例对象
	static public function getInstance() {
		return getInstanceOf(__CLASS__);
	}

	/**
	 * 架构函数
	 * @param array $config 模板引擎配置数组
	 */
	public function __construct() {
		$this->config['cache_path'] = C('app_tpl_cachepath');
		$this->config['template_suffix'] = C('sys_tpl_suffix');
		$this->config['cache_suffix'] = C('sys_tpl_cache_suffix');
		$this->config['tpl_cache'] = C('sys_tpl_cache');
		$this->config['cache_time'] = C('sys_tpl_cache_time');
		$this->config['taglib_begin'] = $this->stripPreg(C('sys_taglib_begin'));
		$this->config['taglib_end'] = $this->stripPreg(C('sys_taglib_end'));
		$this->config['tpl_begin'] = $this->stripPreg(C('sys_tpl_l_delim'));
		$this->config['tpl_end'] = $this->stripPreg(C('sys_tpl_r_delim'));
		$this->config['default_tpl'] = C('app_tpl_filename');
		$this->config['tag_level'] = C('sys_taglib_level');
	}

	private function stripPreg($str) {
		$str = str_replace(array('{', '}', '(', ')', '|', '[', ']'), array('\{', '\}', '\(', '\)', '\|', '\[', '\]'), $str);
		return $str;
	}

	// 模板变量获取和设置
	public function get($name) {
		if (isset($this->tVar[$name]))
			return $this->tVar[$name];
		else
			return false;
	}

	public function set($name, $value) {
		$this->tVar[$name] = $value;
	}

	// 加载模板
	public function load($templateFile, $templateVar) {
		$this->tVar = $templateVar;
		$templateCacheFile = $this->loadTemplate($templateFile);
		// 模板阵列变量分解成为独立变量
		extract($templateVar, EXTR_OVERWRITE);
		//载入模版缓存文件
		include $templateCacheFile;
	}

	/**
	 * 加载主模板并缓存
	 * @param string $tplTemplateFile 模板文件
	 * @param string $varPrefix  模板变量前缀
	 * @return string
	 */
	public function loadTemplate($tplTemplateFile='') {
		if (empty($tplTemplateFile))
			$tplTemplateFile = $this->config['default_tpl'];
		if (!is_file($tplTemplateFile)) {
			$tplTemplateFile = dirname($this->config['default_tpl']) . '/' . $tplTemplateFile . $this->config['template_suffix'];
			if (!is_file($tplTemplateFile))
				throw_exception(L('_TEMPLATE_NOT_EXIST_'));
		}
		$this->templateFile = $tplTemplateFile;

		//根据模版文件名定位缓存文件
		$tplCacheFile = $this->config['cache_path'] . '/' . md5($tplTemplateFile) . $this->config['cache_suffix'];
		$tplContent = '';
		// 检查Cache文件是否需要更新
		if (!$this->checkCache($tplTemplateFile)) {
			// 需要更新模版 读出原模板内容
			$tplContent = file_get_contents($tplTemplateFile);
			//编译模板内容
			$tplContent = $this->compiler($tplContent);
			// 检测分组目录
			if (!is_dir($this->config['cache_path']))
				mk_dir($this->config['cache_path']);
			//重写Cache文件
			if (false === file_put_contents($tplCacheFile, trim($tplContent)))
				throw_exception(L('_CACHE_WRITE_ERROR_'));
		}
		return $tplCacheFile;
	}

	/**
	 * 编译模板文件内容
	 * @param mixed $tplContent 模板内容
	 * @return string
	 */
	protected function compiler($tplContent) {
		//模板解析
		$tplContent = $this->parse($tplContent);
		if (ini_get('short_open_tag'))
		//开启短标签的情况要将<?标签用echo方式输出 否则无法正常输出xml标识
			$tplContent = preg_replace('/(<\?(?!php|=|$))/i', '<?php echo \'\\1\'; ?>' . "\n", $tplContent);
		//还原被替换的Literal标签
		$tplContent = preg_replace('/<!--###literal(\d)###-->/eis', "\$this->restoreLiteral('\\1')", $tplContent);
		//添加安全代码
		$tplContent = '<?php if (!defined(\'FEE_PATH\')) exit();?>' . $tplContent;
		if (C('sys_tpl_strip_space')) {
			/* 去除html空格与换行 */
			$find = array("~>\s+<~", "~>(\s+\n|\r)~");
			$replace = array("><", ">");
			$tplContent = preg_replace($find, $replace, $tplContent);
		}
		return trim($tplContent);
	}

	/**
	 * 检查缓存文件是否有效  如果无效则需要重新更新
	 * @param string $tplTemplateFile  模板文件名
	 * @return boolen
	 */
	protected function checkCache($tplTemplateFile) {
		if (!$this->config['tpl_cache']) // 优先对配置检测
			return false;
		$tplCacheFile = $this->config['cache_path'] . '/' . md5($tplTemplateFile) . $this->config['cache_suffix'];
		if (!is_file($tplCacheFile)) {
			return false;
		} elseif (filemtime($tplTemplateFile) > filemtime($tplCacheFile)) {
			// 模板文件如果有更新则缓存需要更新
			return false;
		} elseif ($this->config['cache_time'] != -1 && time() > filemtime($tplCacheFile) + $this->config['cache_time']) {
			// 缓存是否在有效期
			return false;
		}
		//缓存有效
		return true;
	}

	/**
	 * 模板解析入口 支持普通标签和TagLib解析 支持自定义标签库
	 * @param string $content 要解析的模板内容
	 * @return string
	 */
	public function parse($content) {
		$begin = $this->config['taglib_begin'];
		$end = $this->config['taglib_end'];
		//首先替换literal标签内容
		$content = preg_replace('/' . $begin . 'literal' . $end . '(.*?)' . $begin . '\/literal' . $end . '/eis', "\$this->parseLiteral('\\1')", $content);

		//获取需要引入的标签库列表  标签库只需要定义一次，允许引入多个一次
		//一般放在文件的最前面  格式：<taglib name="html,mytag..." />
		//当sys_taglib_load配置为true时才会进行检测
		if (C('sys_taglib_load')) {
			$this->getIncludeTagLib($content);
			if (!empty($this->tagLib)) {
				foreach ($this->tagLib as $tagLibName) {
					// 内置标签库
					$tagLibName = strtolower($tagLibName);
					$this->parseTagLib($tagLibName, $content);
				}
			}
		}

		// 预先加载的标签库 无需在每个模板中使用taglib标签加载
		if (C('sys_taglib_pre_load')) {
			$tagLibs = explode(',', C('sys_taglib_pre_load'));
			foreach ($tagLibs as $tag) {
				$this->parseTagLib($tag, $content);
			}
		}
		// 内置标签库 无需使用taglib标签导入就可以使用
		$tagLibs = explode(',', C('sys_taglib_build_in'));
		foreach ($tagLibs as $tag) {
			$this->parseTagLib($tag, $content, true);
		}
		//解析普通模板标签 {tagName:}		
		$content = preg_replace('/(' . $this->config['tpl_begin'] . ')(\S.+?)(' . $this->config['tpl_end'] . ')/eis', "\$this->parseTag('\\2')", $content);
		return $content;
	}

	/**
	 * 替换页面中的literal标签
	 * @param string $content  模板内容
	 * @return string|false
	 */
	public function parseLiteral($content) {
		if (trim($content) == '')
			return '';
		$content = stripslashes($content);
		$i = count($this->literal);
		$parseStr = "<!--###literal{$i}###-->";
		$this->literal[$i] = $content;
		return $parseStr;
	}

	/**
	 * 还原被替换的literal标签
	 * @param string $tag  literal标签序号
	 * @return string|false
	 */
	public function restoreLiteral($tag) {
		// 还原literal标签
		$parseStr = $this->literal[$tag];
		// 销毁literal记录
		unset($this->literal[$tag]);
		return $parseStr;
	}

	/**
	 * 搜索模板页面中包含的TagLib库 并返回列表
	 * @param string $content  模板内容
	 * @return string|false
	 */
	public function getIncludeTagLib(& $content) {
		//搜索是否有TagLib标签
		$find = preg_match('/' . $this->config['taglib_begin'] . 'taglib\s(.+?)(\s*?)\/' . $this->config['taglib_end'] . '\W/is', $content, $matches);
		if ($find) {
			//替换TagLib标签
			$content = str_replace($matches[0], '', $content);
			//解析TagLib标签
			$tagLibs = $matches[1];
			$xml = '<tpl><tag ' . $tagLibs . ' /></tpl>';
			$xml = simplexml_load_string($xml);
			if (!$xml)
				throw_exception(L('_XML_TAG_ERROR_'));
			$xml = (array) ($xml->tag->attributes());
			$array = array_change_key_case($xml['@attributes']);
			$this->tagLib = explode(',', $array['name']);
		}
		return;
	}

	/**
	 * TagLib库解析
	 * @param string $tagLib 要解析的标签库
	 * @param string $content 要解析的模板内容
	 * @param boolen $hide 是否隐藏标签库前缀
	 * @return string
	 */
	public function parseTagLib($tagLib, &$content, $hide=false) {
		$begin = $this->config['taglib_begin'];
		$end = $this->config['taglib_end'];
		$tLib = getInstanceOf('taglib_' . strtolower($tagLib));
		foreach ($tLib->tags as $name => $val) {
			$tags = array();
			if (isset($val['alias'])) {// 别名设置
				$tags = explode(',', $val['alias']);
				$tags[] = $name;
			} else {
				$tags[] = $name;
			}
			$level = isset($val['level']) ? $val['level'] : 1;
			$closeTag = isset($val['close']) ? $val['close'] : true;
			foreach ($tags as $tag) {
				//实际要解析的标签名称
				$parseTag = !$hide ? $tagLib . ':' . $tag : $tag;
				if (empty($val['attr'])) {
					//无属性标签
					if (!$closeTag) {
						$content = preg_replace('/' . $begin . $parseTag . '(\s.*?)\/(\s*?)' . $end . '/eis', "\$this->parseXmlTag('$tagLib','$tag','\\1','')", $content);
					} else {
						for ($i = 0; $i < $level; $i++)
							$content = preg_replace('/' . $begin . $parseTag . '(\s*?)' . $end . '(.*?)' . $begin . '\/' . $parseTag . '(\s*?)' . $end . '/eis', "\$this->parseXmlTag('$tagLib','$tag','\\1','\\2')", $content);
					}
				} else {
					if (!$closeTag) {						
						$content = preg_replace('/' . $begin . $parseTag . '\s(.*?)\/(\s*?)' . $end . '/eis', "\$this->parseXmlTag('$tagLib','$tag','\\1','')", $content);
					} else {
						for ($i = 0; $i < $level; $i++)
							$content = preg_replace('/' . $begin . $parseTag . '\s(.*?)' . $end . '(.*?)' . $begin . '\/' . $parseTag . '(\s*?)' . $end . '/eis', "\$this->parseXmlTag('$tagLib','$tag','\\1','\\2')", $content);
					}
				}
			}
		}
	}

	/**
	 * 解析标签库的标签  需要调用对应的标签库文件解析类
	 * @param string $tagLib  标签库名称
	 * @param string $tag  标签名
	 * @param string $attr  标签属性
	 * @param string $content  标签内容
	 * @return string|false
	 */
	public function parseXmlTag($tagLib, $tag, $attr, $content) {
		//if (MAGIC_QUOTES_GPC) {
		$attr = stripslashes($attr);
		$content = stripslashes($content);
		//}
		if (ini_get('magic_quotes_sybase'))
			$attr = str_replace('\"', '\'', $attr);
		$tLib = getInstanceOf('taglib_' . strtolower($tagLib));
		$parse = '_' . $tag;
		$content = trim($content);
		return $tLib->$parse($attr, $content);
	}

	/**
	 * 模板标签解析
	 * 格式： {TagName:args [|content] }
	 * @param string $tagStr 标签内容
	 * @return string
	 */
	public function parseTag($tagStr) {
		//if (MAGIC_QUOTES_GPC) {
		$tagStr = stripslashes($tagStr);
		//}
		//还原非模板标签
		//过滤空格和数字打头的标签
		if (preg_match('/^[\s|\d]/is', $tagStr))		
			return C('sys_tpl_l_delim') . $tagStr . C('sys_tpl_r_delim');
		$flag = substr($tagStr, 0, 1);
		$name = substr($tagStr, 1);
		if ('$' == $flag) {
			//解析模板变量 格式 {$varName}
			return $this->parseVar($name);
		} elseif (':' == $flag) {
			// 输出某个函数的结果
			return '<?php echo ' . $name . ';?>';
		} elseif ('~' == $flag) {
			// 执行某个函数
			return '<?php ' . $name . ';?>';
		} elseif ('&' == $flag) {
			// 输出配置参数
			return '<?php echo C("' . $name . '");?>';
		} elseif ('%' == $flag) {
			// 输出语言变量
			return '<?php echo L("' . $name . '");?>';
		} elseif ('@' == $flag) {
			// 输出SESSION变量
			if (strpos($name, '.')) {
				$array = explode('.', $name);
				return '<?php echo $_SESSION["' . $array[0] . '"]["' . $array[1] . '"];?>';
			} else {
				return '<?php echo $_SESSION["' . $name . '"];?>';
			}
		} elseif ('#' == $flag) {
			// 输出COOKIE变量
			if (strpos($name, '.')) {
				$array = explode('.', $name);
				return '<?php echo $_COOKIE["' . $array[0] . '"]["' . $array[1] . '"];?>';
			} else {
				return '<?php echo $_COOKIE["' . $name . '"];?>';
			}
		} elseif ('.' == $flag) {
			// 输出GET变量
			return '<?php echo $_GET["' . $name . '"];?>';
		} elseif ('^' == $flag) {
			// 输出POST变量
			return '<?php echo $_POST["' . $name . '"];?>';
		} elseif ('*' == $flag) {
			// 输出常量
			return '<?php echo constant("' . $name . '");?>';
		}

		$tagStr = trim($tagStr);
		//注释标签
		if (substr($tagStr, 0, 2) == '//' || (substr($tagStr, 0, 2) == '/*' && substr($tagStr, -2) == '*/'))		
			return '';

		//解析其它标签
		//统一标签格式 {TagName:args [|content]}
		$pos = strpos($tagStr, ':');
		$tag = substr($tagStr, 0, $pos);
		$args = trim(substr($tagStr, $pos + 1));

		//解析标签内容
		if (!empty($args)) {
			$tag = strtolower($tag);
			switch ($tag) {
				case 'include':
					return $this->parseInclude($args);
					break;
				case 'load':
					return $this->parseLoad($args);
					break;
				//TODO 这里扩展其它标签……
				default:
					if (C('sys_tag_extend_parse')) {
						$method = C('sys_tag_extend_parse');
						if (array_key_exists($tag, $method))
							return $method[$tag]($args);
					}
			}
		}
		return C('sys_tpl_l_delim') . $tagStr . C('sys_tpl_r_delim');
	}

	/**
	 * 加载js或者css文件
	 * {load:_STATIC_/js/jquery.js} 加载js文件
	 * {load:_STATIC_/css/style.css} 加载css文件
	 * @param string $params  参数
	 * @return string
	 */
	public function parseLoad($str) {
		$type = strtolower(substr(strrchr($str, '.'), 1));
		$parseStr = '';
		if ($type == 'js') {
			$parseStr .= '<script type="text/javascript" src="' . $str . '"></script>';
		} elseif ($type == 'css') {
			$parseStr .= '<link rel="stylesheet" type="text/css" href="' . $str . '" />';
		}
		return $parseStr;
	}

	/**
	 * 模板变量解析,支持使用函数
	 * 格式： {$varname|function1|function2=arg1,arg2}
	 * @param string $varStr 变量数据
	 * @return string
	 */
	public function parseVar($varStr) {
		$varStr = trim($varStr);
		static $_varParseList = array();
		//如果已经解析过该变量字串，则直接返回变量值
		if (isset($_varParseList[$varStr]))
			return $_varParseList[$varStr];
		$parseStr = '';
		$varExists = true;
		if (!empty($varStr)) {
			$varArray = explode('|', $varStr);
			//取得变量名称
			$var = array_shift($varArray);
			//非法变量过滤 不允许在变量里面使用 ->
			//TODO：还需要继续完善
			if (preg_match('/->/is', $var))
				return '';
			if ('FEE.' == substr($var, 0, 4)) {
				// 所有以FEE.打头的以特殊变量对待 无需模板赋值就可以输出
				$name = $this->parseFeeVar($var);
			} elseif (false !== strpos($var, '.')) {
				//支持 {$var.property}
				$vars = explode('.', $var);
				$var = array_shift($vars);
				switch (strtolower(C('sys_tpl_var_identify'))) {
					case 'array': // 识别为数组
						$name = '$' . $var;
						foreach ($vars as $key => $val)
							$name .= '["' . $val . '"]';
						break;
					case 'obj':  // 识别为对象
						$name = '$' . $var;
						foreach ($vars as $key => $val)
							$name .= '->' . $val;
						break;
					default:  // 自动判断数组或对象 只支持二维
						$name = 'is_array($' . $var . ')?$' . $var . '["' . $vars[0] . '"]:$' . $var . '->' . $vars[0];
				}
			} elseif (false !== strpos($var, '::')) {
				//支持 {$var:property} 方式输出对象的属性
				$vars = explode('::', $var);
				$var = str_replace('::', '->', $var);
				$name = "$" . $var;
				$var = $vars[0];
			} elseif (false !== strpos($var, '[')) {
				//支持 {$var['key']} 方式输出数组
				$name = "$" . $var;
				preg_match('/(.+?)\[(.+?)\]/is', $var, $match);
				$var = $match[1];
			} else {
				$name = "$$var";
			}
			//对变量使用函数
			if (count($varArray) > 0)
				$name = $this->parseVarFunction($name, $varArray);
			$parseStr = '<?php echo (' . $name . '); ?>';
		}
		$_varParseList[$varStr] = $parseStr;
		return $parseStr;
	}

	/**
	 * 对模板变量使用函数
	 * 格式 {$varname|function1|function2=arg1,arg2}
	 * @param string $name 变量名
	 * @param array $varArray  函数列表
	 * @return string
	 */
	public function parseVarFunction($name, $varArray) {
		//对变量使用函数
		$length = count($varArray);
		//取得模板禁止使用函数列表
		$template_deny_funs = explode(',', C('sys_tpl_deny_func'));
		for ($i = 0; $i < $length; $i++) {
			if (0 === stripos($varArray[$i], 'default='))
				$args = explode('=', $varArray[$i], 2);
			else
				$args = explode('=', $varArray[$i]);
			//模板函数过滤
			$args[0] = trim($args[0]);
			switch (strtolower($args[0])) {
				case 'default':  //特殊模板函数
					$name = 'isset(' . $name . ')?(' . $name . '):' . $args[1];
					break;
				default:  //通用模板函数
					if (!in_array($args[0], $template_deny_funs)) {
						if (isset($args[1])) {
							if (strstr($args[1], '###')) {
								$args[1] = str_replace('###', $name, $args[1]);
								$name = "$args[0]($args[1])";
							} else {
								$name = "$args[0]($name,$args[1])";
							}
						} else if (!empty($args[0])) {
							$name = "$args[0]($name)";
						}
					}
			}
		}
		return $name;
	}

	/**
	 * 特殊模板变量解析
	 * 格式 以 $Fee. 打头的变量属于特殊模板变量
	 * @param string $varStr  变量字符串
	 * @return string
	 */
	public function parseFeeVar($varStr) {
		$vars = explode('.', $varStr);
		$vars[1] = strtoupper(trim($vars[1]));
		$parseStr = '';
		if (count($vars) >= 3) {
			$vars[2] = trim($vars[2]);
			switch ($vars[1]) {
				case 'SERVER':
					$parseStr = '$_SERVER[\'' . strtoupper($vars[2]) . '\']';
					break;
				case 'GET':
					$parseStr = '$_GET[\'' . $vars[2] . '\']';
					break;
				case 'POST':
					$parseStr = '$_POST[\'' . $vars[2] . '\']';
					break;
				case 'COOKIE':
					if (isset($vars[3])) {
						$parseStr = '$_COOKIE[\'' . $vars[2] . '\'][\'' . $vars[3] . '\']';
					} else {
						$parseStr = '$_COOKIE[\'' . $vars[2] . '\']';
					}break;
				case 'SESSION':
					if (isset($vars[3])) {
						$parseStr = '$_SESSION[\'' . $vars[2] . '\'][\'' . $vars[3] . '\']';
					} else {
						$parseStr = '$_SESSION[\'' . $vars[2] . '\']';
					}
					break;
				case 'ENV':
					$parseStr = '$_ENV[\'' . $vars[2] . '\']';
					break;
				case 'REQUEST':
					$parseStr = '$_REQUEST[\'' . $vars[2] . '\']';
					break;
				case 'CONST':
					$parseStr = strtoupper($vars[2]);
					break;
				case 'LANG':
					$parseStr = 'L("' . $vars[2] . '")';
					break;
				case 'CONFIG':
					if (isset($vars[3])) {
						$vars[2] .= '.' . $vars[3];
					}
					$parseStr = 'C("' . $vars[2] . '")';
					break;
				default:break;
			}
		} else if (count($vars) == 2) {
			switch ($vars[1]) {
				case 'NOW':
					$parseStr = "date('Y-m-d H:i:s',time())";
					break;
				case 'VERSION':
					$parseStr = 'FEE_VERSION';
					break;
				case 'TEMPLATE':
					$parseStr = 'C("app_tpl_filename")';
					break;
				case 'LDELIM':
					$parseStr = 'C("sys_tpl_l_delim")';
					break;
				case 'RDELIM':
					$parseStr = 'C("sys_tpl_r_delim")';
					break;
				default:
					if (defined($vars[1]))
						$parseStr = $vars[1];
			}
		}
		return $parseStr;
	}

	/**
	 * 加载公共模板并缓存 和当前模板在同一路径，否则使用相对路径
	 * @param string $tplPublicName  公共模板文件名
	 * @return string
	 */
	public function parseInclude($tplPublicName) {
		if (substr($tplPublicName, 0, 1) == '$')
			//支持加载变量文件名
			$tplPublicName = $this->get(substr($tplPublicName, 1));

		if (is_file($tplPublicName)) {
			//直接包含文件
			$parseStr = file_get_contents($tplPublicName);
		} else {
			$tplPublicName = trim($tplPublicName);
			if (strpos($tplPublicName, '@')) {
				// 引入其它模块的操作模板
				$tplTemplateFile = dirname(dirname(dirname($this->templateFile))) . '/' . str_replace(array('@', ':'), array('/', '/'), $tplPublicName);
			} elseif (strpos($tplPublicName, ':') || strpos($tplPublicName, '/')) {
				// 引入其它模块的操作模板
				$tplTemplateFile = dirname(dirname($this->templateFile)) . '/' . str_replace(':', '/', $tplPublicName);
			} else {
				// 默认导入当前模块下面的模板
				$tplTemplateFile = dirname($this->templateFile) . '/' . $tplPublicName;
			}
			$tplTemplateFile .= $this->config['template_suffix'];
			$parseStr = file_get_contents($tplTemplateFile);
		}
		//再次对包含文件进行模板分析
		return $this->parse($parseStr);
	}

}

?>
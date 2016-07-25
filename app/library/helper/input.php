<?php

/** 输入数据管理类
 * 使用方法
 *  $Input = helper_input::getInstance();
 *  $Input->get('name','md5','0');
 *  $Input->session('memberId','','0');
 *
 * 下面总结了一些常用的数据处理方法。以下方法无需考虑magic_quotes_gpc的设置。
 *
 * 获取数据：
 *    如果从$_POST或者$_GET中获取，使用helper_input::getVar($_POST['field']);，从数据库或者文件就不需要了。
 *    或者直接使用 helper_input::magicQuotes来消除所有的magic_quotes_gpc转义。
 *
 * 存储过程：
 *    经过helper_input::getVar($_POST['field'])获得的数据，就是干净的数据，可以直接保存。
 *    如果要过滤危险的html，可以使用 $html = helper_input::safeHtml($data);
 *
 * 页面显示：
 *    纯文本显示在网页中，如文章标题<title>$data</title>： $data = helper_input::forShow($field);
 *    HTML 在网页中显示，如文章内容：无需处理。
 *    在网页中以源代码方式显示html：$vo = helper_input::forShow($html);
 *    纯文本或者HTML在textarea中进行编辑: $vo = helper_input::forTarea($value);
 *    html在标签中使用，如<input value="数据" /> ，使用 $vo = helper_input::forTag($value); 或者 $vo = helper_input::hsc($value);
 *
 * 特殊使用情况：
 *    字符串要在数据库进行搜索： $data = helper_input::forSearch($field);
 */
class helper_input {

	private $filter = null;   // 输入过滤
	private static $_input = array('get', 'post', 'request', 'env', 'server', 'cookie', 'session', 'globals', 'config', 'lang', 'call');
	//html标签设置
	public static $htmlTags = array(
		'allow' => 'table|td|th|tr|i|b|u|strong|img|p|br|div|strong|em|ul|ol|li|dl|dd|dt|a',
		'ban' => 'html|head|meta|link|base|basefont|body|bgsound|title|style|script|form|iframe|frame|frameset|applet|id|ilayer|layer|name|script|style|xml',
	);

	static public function getInstance() {
		return getInstanceOf(__CLASS__);
	}

	/**
	 * 魔术方法 有不存在的操作的时候执行
	 * @param string $type 输入数据类型
	 * @param array $args 参数 array(key,filter,default)
	 * @return mixed
	 */
	public function __call($type, $args = array()) {
		$type = strtolower(trim($type));
		if (in_array($type, self::$_input, true)) {
			switch ($type) {
				case 'get': $input = & $_GET;
					break;
				case 'post': $input = & $_POST;
					break;
				case 'request': $input = & $_REQUEST;
					break;
				case 'env': $input = & $_ENV;
					break;
				case 'server': $input = & $_SERVER;
					break;
				case 'cookie': $input = & $_COOKIE;
					break;
				case 'session': $input = & $_SESSION;
					break;
				case 'globals': $input = & $GLOBALS;
					break;
				case 'files': $input = & $_FILES;
					break;
				case 'call': $input = 'call';
					break;
				case 'config': $input = C();
					break;
				case 'lang': $input = L();
					break;
				default:return NULL;
			}
			if ('call' === $input) {
				// 呼叫其他方式的输入数据
				$callback = array_shift($args);
				$params = array_shift($args);
				$data = call_user_func_array($callback, $params);
				if (count($args) === 0) {
					return $data;
				}
				$filter = isset($args[0]) ? $args[0] : $this->filter;
				if (!empty($filter)) {
					$data = call_user_func_array($filter, $data);
				}
			} else {
				if (0 == count($args) || empty($args[0])) {
					return $input;
				} elseif (array_key_exists($args[0], $input)) {
					// 系统变量
					$data = $input[$args[0]];
					$filter = isset($args[1]) ? $args[1] : $this->filter;
					if (!empty($filter)) {
						$data = call_user_func_array($filter, $data);
					}
				} else {
					// 不存在指定输入
					$data = isset($args[2]) ? $args[2] : NULL;
				}
			}
			return $data;
		}
	}

	/**
	 * 设置数据过滤方法
	 * @param mixed $filter 过滤方法
	 * @return void
	 */
	public function filter($filter) {
		$this->filter = $filter;
		return $this;
	}

	/**
	 * 字符MagicQuote转义过滤
	 * @return void
	 */
	static public function noGPC() {
		if (get_magic_quotes_gpc()) {
			$_POST = stripslashesDeep($_POST);
			$_GET = stripslashesDeep($_GET);
			$_COOKIE = stripslashesDeep($_COOKIE);
			$_REQUEST = stripslashesDeep($_REQUEST);
		}
	}

	/**
	 * 处理字符串，以便可以正常进行搜索
	 * @param string $string 要处理的字符串
	 * @return string
	 */
	static public function forSearch($string) {
		return str_replace(array('%', '_'), array('\%', '\_'), $string);
	}

	/**
	 * @param string $string 要处理的字符串
	 * @return string
	 */
	static public function forShow($string) {
		return self::nl2Br(self::hsc($string));
	}

	/**
	  +----------------------------------------------------------
	 * 处理纯文本数据，以便在textarea标签中显示
	  +----------------------------------------------------------
	 * @access public
	  +----------------------------------------------------------
	 * @param string $text 要处理的字符串
	  +----------------------------------------------------------
	 * @return string
	  +----------------------------------------------------------
	 */
	static public function forTarea($string) {
		return str_ireplace(array('<textarea>', '</textarea>'), array('&lt;textarea>', '&lt;/textarea>'), $string);
	}

	/**
	  +----------------------------------------------------------
	 * 将数据中的单引号和双引号进行转义
	  +----------------------------------------------------------
	 * @access public
	  +----------------------------------------------------------
	 * @param string $text 要处理的字符串
	  +----------------------------------------------------------
	 * @return string
	  +----------------------------------------------------------
	 */
	static public function forTag($string) {
		return str_replace(array('"', "'"), array('&quot;', '&#039;'), $string);
	}

	

	/**
	  +----------------------------------------------------------
	 * 如果 magic_quotes_gpc 为关闭状态，这个函数可以转义字符串
	  +----------------------------------------------------------
	 * @access public
	  +----------------------------------------------------------
	 * @param string $text 要处理的字符串
	  +----------------------------------------------------------
	 * @return string
	  +----------------------------------------------------------
	 */
	static public function addSlashes($string) {
		if (!get_magic_quotes_gpc()) {
			$string = addslashes($string);
		}
		return $string;
	}

	/**
	  +----------------------------------------------------------
	 * 从$_POST，$_GET，$_COOKIE，$_REQUEST等数组中获得数据
	  +----------------------------------------------------------
	 * @access public
	  +----------------------------------------------------------
	 * @param string $text 要处理的字符串
	  +----------------------------------------------------------
	 * @return string
	  +----------------------------------------------------------
	 */
	static public function getVar($string) {
		return self::stripSlashes($string);
	}

	/**
	  +----------------------------------------------------------
	 * 如果 magic_quotes_gpc 为开启状态，这个函数可以反转义字符串
	  +----------------------------------------------------------
	 * @access public
	  +----------------------------------------------------------
	 * @param string $text 要处理的字符串
	  +----------------------------------------------------------
	 * @return string
	  +----------------------------------------------------------
	 */
	static public function stripSlashes($string) {
		if (get_magic_quotes_gpc()) {
			$string = stripslashes($string);
		}
		return $string;
	}

	/**
	 * 用于在textbox表单中显示html代码
	 * @param string $text 要处理的字符串
	 * @return string
	 */
	static function hsc($string) {
		return preg_replace(array("/&amp;/i", "/&nbsp;/i"), array('&', '&amp;nbsp;'), htmlspecialchars($string, ENT_QUOTES));
	}

}

?>
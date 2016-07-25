<?php

// +----------------------------------------------------------------------
// | Http 工具类 提供一系列的Http方法
// +----------------------------------------------------------------------

class helper_http {

	//请求的URL地址
	public $request_url;
	//请求的header头
	public $request_headers;
	//请求的body
	public $request_body;
	//响应的请求内容。
	public $response;
	//cURL对象的句柄
	public $curl_handle;
	//curl请求方法 POST GET
	public $method;
	//代理参数
	public $proxy = null;
	//登录采集的用户名
	public $username = null;
	//登录采集的密码
	public $password = null;
	//自定义的CURLOPT参数
	public $curlopts = null;
	//是否开始debug调试
	public $debug_mode = true;
	//UserAgent
	public $useragent;
	//UserAgent列表
	public $useragent_list = array(
		'ie6' => 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1;)',
		'ie9' => 'Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0)',
		'firefox' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.1.6) Gecko/20091201 Firefox/3.5.6',
		'chrome' => 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.1 (KHTML, like Gecko) Chrome/21.0.1180.89 Safari/537.1',
		'ipad' => 'Mozilla/5.0 (iPad; CPU OS 6_0_1 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A523 Safari/8536.25',
		'itunes' => 'iTunes/4.2 (Macintosh; U; PPC Mac OS X 10.2" -H "X-Apple-Store-Front: 143465-1',
		'itunes_pc' => 'iTunes/10.1.1 (Windows; Microsoft Windows XP Professional Service Pack 3 (Build 2600)) AppleWebKit/533.19.4',
		'mac_store' => 'MacAppStore/1.0 (Macintosh; U; Intel Mac OS X 10.6.6; zh-Hans) AppleWebKit/533.19.4',
	);
	//超时时间
	public $timeout = 30;

	/**
	 * 构造函数
	 * @param string $url 待采集网址
	 * @param string $proxy 代理地址
	 */
	public function __construct($url = null, $proxy = null) {
		$this->request_url = $url;
		$this->method = 'GET';
		$this->request_headers = array();
		$this->request_body = '';
		$this->useragent = $this->useragent_list['ie6'];
		if ($proxy)
			$this->setProxy($proxy);
		return $this;
	}

	//设置待请求URL地址
	public function setUrl($url) {
		$this->request_url = $url;
		return $this;
	}

	/**
	 * 设置HTTP Basic/Digest 验证用户名和密码
	 * @param string $user 用户名
	 * @param string $pass 密码
	 */
	public function setBasicAuth($user, $pass) {
		$this->username = $user;
		$this->password = $pass;
		return $this;
	}

	/**
	 * 设置自定义header参数
	 * @param string $key 
	 * @param string $value 
	 */
	public function setHeader($key, $value) {
		$this->request_headers[$key] = $value;
		return $this;
	}

	/**
	 * 移除自定义header参数
	 * @param string $key 
	 * @param string $value 
	 */
	public function removeHeader($key) {
		if (isset($this->request_headers[$key])) {
			unset($this->request_headers[$key]);
		}
		return $this;
	}

	//设置请求方法，默认为GET请求
	public function setMethod($method) {
		$this->method = strtoupper($method);
		return $this;
	}

	//设置User Agent浏览器标志
	public function setUserAgent($ua) {
		$this->useragent = isset($this->useragent_list[$ua]) ? $this->useragent_list[$ua] : $ua;
		return $this;
	}

	//设置请求的body内容
	public function setBody($body){
		$this->request_body = $body;
        return $this;
	}


	//设置User Agent浏览器标志
	public function setCurlOpts($curlopts) {
        $this->curlopts = $curlopts;
        return $this;
    }
	
	//设置请求的body内容
	public function setTimeOut($time){
		$this->timeout = $time;
        return $this;
	}

	//获取URL内容
	public function request($url='',$method=''){
		$this->response = array();
		if($method) $this->setMethod($method);
		if($url) $this->setUrl($url);
		$this->curl_handle = $this->curl();
		$content = curl_exec($this->curl_handle);
		if ($content === false)  return false;
        if (is_resource($this->curl_handle)) {
			$this->response['headers_info'] = curl_getinfo($this->curl_handle);
            $header_size = $this->response['headers_info']['header_size'];
            $this->response['headers'] = substr($content, 0, $header_size);
            $this->response['body'] = substr($content, $header_size);
            $this->response['code'] = $this->response['headers_info']['http_code'];      
            //解析headers头
            $this->response['headers'] = explode("\r\n\r\n", trim($this->response['headers']));
            $this->response['headers'] = array_pop($this->response['headers']);
            $this->response['headers'] = explode("\r\n", $this->response['headers']);
            array_shift($this->response['headers']);
            $header_assoc = array();
            foreach ($this->response['headers'] as $header) {
                $kv = explode(': ', $header);
                $header_assoc[$kv[0]] = $kv [1];
            }
            $this->response['headers'] = $header_assoc;
            $this->response['headers_info']['method'] = $this->method;
        }
		curl_close($this->curl_handle);
		unset($content,$header_size,$header_assoc);
		return $this->response;
    }

	public function curl($url='') {
		$url = $url ? $url : $this->request_url;
		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt($curl_handle, CURLOPT_FILETIME, true);
		curl_setopt($curl_handle, CURLOPT_FRESH_CONNECT, false);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, true);
		curl_setopt($curl_handle, CURLOPT_CLOSEPOLICY, CURLCLOSEPOLICY_LEAST_RECENTLY_USED);
		curl_setopt($curl_handle, CURLOPT_MAXREDIRS, 5);
		curl_setopt($curl_handle, CURLOPT_HEADER, true);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl_handle, CURLOPT_TIMEOUT, 360);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 120);
		curl_setopt($curl_handle, CURLOPT_NOSIGNAL, true);
		curl_setopt($curl_handle, CURLOPT_REFERER, $url);
		curl_setopt($curl_handle, CURLOPT_USERAGENT, $this->useragent);
		if ($this->debug_mode) {
			curl_setopt($curl_handle, CURLOPT_VERBOSE, true);
		}

		// 代理设置
		if ($this->proxy) {
			curl_setopt($curl_handle, CURLOPT_HTTPPROXYTUNNEL, true);
			$host = $this->proxy['host'];
			$host .= ($this->proxy['port']) ? ':' . $this->proxy['port'] : '';
			curl_setopt($curl_handle, CURLOPT_PROXY, $host);
			if (isset($this->proxy['user']) && isset($this->proxy['pass'])) {
				curl_setopt($curl_handle, CURLOPT_PROXYUSERPWD, $this->proxy['user'] . ':' . $this->proxy['pass']);
			}
		}
		// 设置HTTP Basic/Digest 验证
		if ($this->username && $this->password) {
			curl_setopt($curl_handle, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
			curl_setopt($curl_handle, CURLOPT_USERPWD, $this->username . ':' . $this->password);
		}

		//解码数据
		if (extension_loaded('zlib')) {
			curl_setopt($curl_handle, CURLOPT_ENCODING, '');
		}
		//设置自定义header头部
		if (isset($this->request_headers) && count($this->request_headers)) {
			$temp_headers = array();
			foreach ($this->request_headers as $k => $v) {
				$temp_headers[] = $k . ': ' . $v;
			}
			curl_setopt($curl_handle, CURLOPT_HTTPHEADER, $temp_headers);
		}
		switch ($this->method) {
			case 'PUT' :
				curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $this->request_body);
				break;
			case 'POST' :
				curl_setopt($curl_handle, CURLOPT_POST, true);
				curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $this->request_body);
				break;
			case 'HEAD' :
				curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, 'HEAD');
				curl_setopt($curl_handle, CURLOPT_NOBODY, 1);
				break;
			default :
				curl_setopt($curl_handle, CURLOPT_CUSTOMREQUEST, $this->method);
				curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $this->request_body);
				break;
		}
		//设置cURL其他参数
		if (isset($this->curlopts) && sizeof($this->curlopts) > 0) {
			foreach ($this->curlopts as $k => $v) {
				curl_setopt($curl_handle, $k, $v);
			}
		}
		return $curl_handle;
	}

	/**
	  +----------------------------------------------------------
	 * 采集远程文件
	  +----------------------------------------------------------
	 * @param string $remote 远程文件名
	 * @param string $local 本地保存文件名
	  +----------------------------------------------------------
	 * @return mixed
	  +----------------------------------------------------------
	 */
	static public function curl_download($remote, $local) {
		if (!is_dir(dirname($local)))
			mk_dir(dirname($local));
		$cp = curl_init($remote);		
		$fp = fopen($local, "w");
		curl_setopt($cp, CURLOPT_FILE, $fp);
		curl_setopt($cp, CURLOPT_HEADER, 0);
		curl_exec($cp);
		$code = curl_getinfo($cp, CURLINFO_HTTP_CODE);
		curl_close($cp);
		fclose($fp);
		if($code>'400') unlink($local);
	}

	/**
	  +----------------------------------------------------------
	 * 下载文件
	 * 可以指定下载显示的文件名，并自动发送相应的Header信息
	 * 如果指定了content参数，则下载该参数的内容
	  +----------------------------------------------------------
	 * @param string $filename 下载文件名
	 * @param string $showname 下载显示的文件名
	 * @param string $content  下载的内容
	 * @param integer $expire  下载内容浏览器缓存时间
	  +----------------------------------------------------------
	 */
	static public function download($filename, $showname = '', $content = '', $expire = 180) {
		if (is_file($filename)) {
			$length = filesize($filename);
		} elseif (is_file(UPLOAD_PATH . $filename)) {
			$filename = UPLOAD_PATH . $filename;
			$length = filesize($filename);
		} elseif ($content != '') {
			$length = strlen($content);
		} else {
			exit("下载文件不存在！");
		}
		if (empty($showname)) {
			$showname = $filename;
		}
		$showname = basename($showname);
		if (!empty($filename)) {
			$type = mime_content_type($filename);
		} else {
			$type = "application/octet-stream";
		}
		//发送Http Header信息 开始下载
		header("Pragma: public");
		header('Last-Modified: '.gmdate('D, d M Y H:i:s') . ' GMT');
		header('Cache-Control: no-store, no-cache, must-revalidate');
		header('Cache-Control: pre-check=0, post-check=0, max-age=0');
		//header("Expires: " . gmdate("D, d M Y H:i:s", time() + $expire) . "GMT");
		header("Content-Transfer-Encoding: binary");
		header('Content-Encoding: none');
		header("Content-type: " . $type);
		header("Content-Disposition: attachment; filename=" . $showname);
		header("Content-Length: " . $length);
		if ($content == '') {
			readfile($filename);
		} else {
			echo($content);
		}
		exit();
	}

	/**
	  +----------------------------------------------------------
	 * 显示HTTP Header 信息
	  +----------------------------------------------------------
	 * @return string
	  +----------------------------------------------------------
	 */
	static function get_header_info($header = '', $echo = true) {
		ob_start();
		$headers = getallheaders();
		if (!empty($header)) {
			$info = $headers[$header];
			echo($header . ':' . $info . "\n");
			;
		} else {
			foreach ($headers as $key => $val) {
				echo("$key:$val\n");
			}
		}
		$output = ob_get_clean();
		if ($echo) {
			echo (nl2br($output));
		} else {
			return $output;
		}
	}

	/**
	 * 发送http状态码
	 * @param int $num
	 */
	static function send_http_status($code) {
		static $_status = array(
	// Informational 1xx
	100 => 'Continue',
	101 => 'Switching Protocols',
	// Success 2xx
	200 => 'OK',
	201 => 'Created',
	202 => 'Accepted',
	203 => 'Non-Authoritative Information',
	204 => 'No Content',
	205 => 'Reset Content',
	206 => 'Partial Content',
	// Redirection 3xx
	300 => 'Multiple Choices',
	301 => 'Moved Permanently',
	302 => 'Found', // 1.1
	303 => 'See Other',
	304 => 'Not Modified',
	305 => 'Use Proxy',
	// 306 is deprecated but reserved
	307 => 'Temporary Redirect',
	// Client Error 4xx
	400 => 'Bad Request',
	401 => 'Unauthorized',
	402 => 'Payment Required',
	403 => 'Forbidden',
	404 => 'Not Found',
	405 => 'Method Not Allowed',
	406 => 'Not Acceptable',
	407 => 'Proxy Authentication Required',
	408 => 'Request Timeout',
	409 => 'Conflict',
	410 => 'Gone',
	411 => 'Length Required',
	412 => 'Precondition Failed',
	413 => 'Request Entity Too Large',
	414 => 'Request-URI Too Long',
	415 => 'Unsupported Media Type',
	416 => 'Requested Range Not Satisfiable',
	417 => 'Expectation Failed',
	// Server Error 5xx
	500 => 'Internal Server Error',
	501 => 'Not Implemented',
	502 => 'Bad Gateway',
	503 => 'Service Unavailable',
	504 => 'Gateway Timeout',
	505 => 'HTTP Version Not Supported',
	509 => 'Bandwidth Limit Exceeded'
		);
		if (array_key_exists($code, $_status)) {
			header('HTTP/1.1 ' . $code . ' ' . $_status[$code]);
		}
	}

}

if (!function_exists('mime_content_type')) {

	/**
	  +----------------------------------------------------------
	 * 获取文件的mime_content类型
	  +----------------------------------------------------------
	 * @return string
	  +----------------------------------------------------------
	 */
	function mime_content_type($filename) {
		static $contentType = array(
	'ai' => 'application/postscript',
	'aif' => 'audio/x-aiff',
	'aifc' => 'audio/x-aiff',
	'aiff' => 'audio/x-aiff',
	'asc' => 'application/pgp', //changed by skwashd - was text/plain
	'asf' => 'video/x-ms-asf',
	'asx' => 'video/x-ms-asf',
	'au' => 'audio/basic',
	'avi' => 'video/x-msvideo',
	'bcpio' => 'application/x-bcpio',
	'bin' => 'application/octet-stream',
	'bmp' => 'image/bmp',
	'c' => 'text/plain', // or 'text/x-csrc', //added by skwashd
	'cc' => 'text/plain', // or 'text/x-c++src', //added by skwashd
	'cs' => 'text/plain', //added by skwashd - for C# src
	'cpp' => 'text/x-c++src', //added by skwashd
	'cxx' => 'text/x-c++src', //added by skwashd
	'cdf' => 'application/x-netcdf',
	'class' => 'application/octet-stream', //secure but application/java-class is correct
	'com' => 'application/octet-stream', //added by skwashd
	'cpio' => 'application/x-cpio',
	'cpt' => 'application/mac-compactpro',
	'csh' => 'application/x-csh',
	'css' => 'text/css',
	'csv' => 'text/comma-separated-values', //added by skwashd
	'dcr' => 'application/x-director',
	'diff' => 'text/diff',
	'dir' => 'application/x-director',
	'dll' => 'application/octet-stream',
	'dms' => 'application/octet-stream',
	'doc' => 'application/msword',
	'dot' => 'application/msword', //added by skwashd
	'dvi' => 'application/x-dvi',
	'dxr' => 'application/x-director',
	'eps' => 'application/postscript',
	'etx' => 'text/x-setext',
	'exe' => 'application/octet-stream',
	'ez' => 'application/andrew-inset',
	'gif' => 'image/gif',
	'gtar' => 'application/x-gtar',
	'gz' => 'application/x-gzip',
	'h' => 'text/plain', // or 'text/x-chdr',//added by skwashd
	'h++' => 'text/plain', // or 'text/x-c++hdr', //added by skwashd
	'hh' => 'text/plain', // or 'text/x-c++hdr', //added by skwashd
	'hpp' => 'text/plain', // or 'text/x-c++hdr', //added by skwashd
	'hxx' => 'text/plain', // or 'text/x-c++hdr', //added by skwashd
	'hdf' => 'application/x-hdf',
	'hqx' => 'application/mac-binhex40',
	'htm' => 'text/html',
	'html' => 'text/html',
	'ice' => 'x-conference/x-cooltalk',
	'ics' => 'text/calendar',
	'ief' => 'image/ief',
	'ifb' => 'text/calendar',
	'iges' => 'model/iges',
	'igs' => 'model/iges',
	'jar' => 'application/x-jar', //added by skwashd - alternative mime type
	'java' => 'text/x-java-source', //added by skwashd
	'jpe' => 'image/jpeg',
	'jpeg' => 'image/jpeg',
	'jpg' => 'image/jpeg',
	'js' => 'application/x-javascript',
	'kar' => 'audio/midi',
	'latex' => 'application/x-latex',
	'lha' => 'application/octet-stream',
	'log' => 'text/plain',
	'lzh' => 'application/octet-stream',
	'm3u' => 'audio/x-mpegurl',
	'man' => 'application/x-troff-man',
	'me' => 'application/x-troff-me',
	'mesh' => 'model/mesh',
	'mid' => 'audio/midi',
	'midi' => 'audio/midi',
	'mif' => 'application/vnd.mif',
	'mov' => 'video/quicktime',
	'movie' => 'video/x-sgi-movie',
	'mp2' => 'audio/mpeg',
	'mp3' => 'audio/mpeg',
	'mpe' => 'video/mpeg',
	'mpeg' => 'video/mpeg',
	'mpg' => 'video/mpeg',
	'mpga' => 'audio/mpeg',
	'ms' => 'application/x-troff-ms',
	'msh' => 'model/mesh',
	'mxu' => 'video/vnd.mpegurl',
	'nc' => 'application/x-netcdf',
	'oda' => 'application/oda',
	'patch' => 'text/diff',
	'pbm' => 'image/x-portable-bitmap',
	'pdb' => 'chemical/x-pdb',
	'pdf' => 'application/pdf',
	'pgm' => 'image/x-portable-graymap',
	'pgn' => 'application/x-chess-pgn',
	'pgp' => 'application/pgp', //added by skwashd
	'php' => 'application/x-httpd-php',
	'php3' => 'application/x-httpd-php3',
	'pl' => 'application/x-perl',
	'pm' => 'application/x-perl',
	'png' => 'image/png',
	'pnm' => 'image/x-portable-anymap',
	'po' => 'text/plain',
	'ppm' => 'image/x-portable-pixmap',
	'ppt' => 'application/vnd.ms-powerpoint',
	'ps' => 'application/postscript',
	'qt' => 'video/quicktime',
	'ra' => 'audio/x-realaudio',
	'rar' => 'application/octet-stream',
	'ram' => 'audio/x-pn-realaudio',
	'ras' => 'image/x-cmu-raster',
	'rgb' => 'image/x-rgb',
	'rm' => 'audio/x-pn-realaudio',
	'roff' => 'application/x-troff',
	'rpm' => 'audio/x-pn-realaudio-plugin',
	'rtf' => 'text/rtf',
	'rtx' => 'text/richtext',
	'sgm' => 'text/sgml',
	'sgml' => 'text/sgml',
	'sh' => 'application/x-sh',
	'shar' => 'application/x-shar',
	'shtml' => 'text/html',
	'silo' => 'model/mesh',
	'sit' => 'application/x-stuffit',
	'skd' => 'application/x-koan',
	'skm' => 'application/x-koan',
	'skp' => 'application/x-koan',
	'skt' => 'application/x-koan',
	'smi' => 'application/smil',
	'smil' => 'application/smil',
	'snd' => 'audio/basic',
	'so' => 'application/octet-stream',
	'spl' => 'application/x-futuresplash',
	'src' => 'application/x-wais-source',
	'stc' => 'application/vnd.sun.xml.calc.template',
	'std' => 'application/vnd.sun.xml.draw.template',
	'sti' => 'application/vnd.sun.xml.impress.template',
	'stw' => 'application/vnd.sun.xml.writer.template',
	'sv4cpio' => 'application/x-sv4cpio',
	'sv4crc' => 'application/x-sv4crc',
	'swf' => 'application/x-shockwave-flash',
	'sxc' => 'application/vnd.sun.xml.calc',
	'sxd' => 'application/vnd.sun.xml.draw',
	'sxg' => 'application/vnd.sun.xml.writer.global',
	'sxi' => 'application/vnd.sun.xml.impress',
	'sxm' => 'application/vnd.sun.xml.math',
	'sxw' => 'application/vnd.sun.xml.writer',
	't' => 'application/x-troff',
	'tar' => 'application/x-tar',
	'tcl' => 'application/x-tcl',
	'tex' => 'application/x-tex',
	'texi' => 'application/x-texinfo',
	'texinfo' => 'application/x-texinfo',
	'tgz' => 'application/x-gtar',
	'tif' => 'image/tiff',
	'tiff' => 'image/tiff',
	'tr' => 'application/x-troff',
	'tsv' => 'text/tab-separated-values',
	'txt' => 'text/plain',
	'ustar' => 'application/x-ustar',
	'vbs' => 'text/plain', //added by skwashd - for obvious reasons
	'vcd' => 'application/x-cdlink',
	'vcf' => 'text/x-vcard',
	'vcs' => 'text/calendar',
	'vfb' => 'text/calendar',
	'vrml' => 'model/vrml',
	'vsd' => 'application/vnd.visio',
	'wav' => 'audio/x-wav',
	'wax' => 'audio/x-ms-wax',
	'wbmp' => 'image/vnd.wap.wbmp',
	'wbxml' => 'application/vnd.wap.wbxml',
	'wm' => 'video/x-ms-wm',
	'wma' => 'audio/x-ms-wma',
	'wmd' => 'application/x-ms-wmd',
	'wml' => 'text/vnd.wap.wml',
	'wmlc' => 'application/vnd.wap.wmlc',
	'wmls' => 'text/vnd.wap.wmlscript',
	'wmlsc' => 'application/vnd.wap.wmlscriptc',
	'wmv' => 'video/x-ms-wmv',
	'wmx' => 'video/x-ms-wmx',
	'wmz' => 'application/x-ms-wmz',
	'wrl' => 'model/vrml',
	'wvx' => 'video/x-ms-wvx',
	'xbm' => 'image/x-xbitmap',
	'xht' => 'application/xhtml+xml',
	'xhtml' => 'application/xhtml+xml',
	'xls' => 'application/vnd.ms-excel',
	'xlt' => 'application/vnd.ms-excel',
	'xml' => 'application/xml',
	'xpm' => 'image/x-xpixmap',
	'xsl' => 'text/xml',
	'xwd' => 'image/x-xwindowdump',
	'xyz' => 'chemical/x-xyz',
	'z' => 'application/x-compress',
	'zip' => 'application/zip',
		);
		$type = strtolower(substr(strrchr($filename, '.'), 1));
		if (isset($contentType[$type])) {
			$mime = $contentType[$type];
		} else {
			$mime = 'application/octet-stream';
		}
		return $mime;
	}

}

if (!function_exists('image_type_to_extension')) {

	function image_type_to_extension($imagetype) {
		if (empty($imagetype))
			return false;
		switch ($imagetype) {
			case IMAGETYPE_GIF : return '.gif';
			case IMAGETYPE_JPEG : return '.jpg';
			case IMAGETYPE_PNG : return '.png';
			case IMAGETYPE_SWF : return '.swf';
			case IMAGETYPE_PSD : return '.psd';
			case IMAGETYPE_BMP : return '.bmp';
			case IMAGETYPE_TIFF_II : return '.tiff';
			case IMAGETYPE_TIFF_MM : return '.tiff';
			case IMAGETYPE_JPC : return '.jpc';
			case IMAGETYPE_JP2 : return '.jp2';
			case IMAGETYPE_JPX : return '.jpf';
			case IMAGETYPE_JB2 : return '.jb2';
			case IMAGETYPE_SWC : return '.swc';
			case IMAGETYPE_IFF : return '.aiff';
			case IMAGETYPE_WBMP : return '.wbmp';
			case IMAGETYPE_XBM : return '.xbm';
			default : return false;
		}
	}

}
?>
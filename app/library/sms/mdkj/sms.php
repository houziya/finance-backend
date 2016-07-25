<?PHP
//============ 漫道科技 单发群发类 ============
//改demo的功能是群发短信和发单条短信。（传一个手机号就是发单条，多个手机号既是群发）
//您把序列号和密码还有手机号，填上，直接运行就可以了
//如果您的系统是utf-8,请转成GB2312 后，再提交、
//请参考 'content'=>iconv( "UTF-8", "gb2312//IGNORE" ,'您好测试短信[XXX公司]'),//短信内容

class sms_mdkj_sms {

	public function sendSms($config, $type = '2') {
		$flag = 0;
		//构造要post的字符串 
		foreach ($config as $key => $value) {
			if ($flag != 0) {
				$params .= "&";
				$flag = 1;
			}
			$params.= $key . "=";
			$params.= urlencode($value);
			$flag = 1;
		}
		$length = strlen($params);
		//创建socket连接 
		$fp = fsockopen("sdk.entinfo.cn", 8061, $errno, $errstr, 10) or exit($errstr . "--->" . $errno);
		//构造post请求的头 
		if ($type == '1') {
			$header = "POST /webservice.asmx/mdsmssend HTTP/1.1\r\n";
		} elseif ($type == '2') {
			$header = "POST /webservice.asmx/mdgxsend HTTP/1.1\r\n";
		}
		$header .= "Host:sdk.entinfo.cn\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . $length . "\r\n";
		$header .= "Connection: Close\r\n\r\n";
		//添加post的字符串 
		$header .= $params . "\r\n";
		//发送post的数据 
		fputs($fp, $header);
		$inheader = 1;
		while (!feof($fp)) {
			$line = fgets($fp, 1024); //去除请求包的头只显示页面的返回数据 
			if ($inheader && ($line == "\n" || $line == "\r\n")) {
				$inheader = 0;
			}
			if ($inheader == 0) {
				// echo $line; 
			}
		}
		//<string xmlns="http://tempuri.org/">-5</string>
		$line = str_replace("<string xmlns=\"http://tempuri.org/\">", "", $line);
		$line = str_replace("</string>", "", $line);
		$result = explode("-", $line);
		// echo $line."-------------";
		if (count($result) > 1) {
			// echo '发送失败返回值为:'.$line.'。请查看webservice返回值对照表';
			return 0;
		} else {
			// echo '发送成功 返回值为:'.$line;
			return 1;
		}
	}

}

?>
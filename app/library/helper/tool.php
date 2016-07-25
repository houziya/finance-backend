<?php
// +----------------------------------------------------------------------
// | 工具类
// +----------------------------------------------------------------------
class helper_tool {
	
	/**
	 * 是否开启相应场景的验证码
	 *
	 * @param  string  $type
	 *
	 * @return bool
	 */
	static public function isOpenCaptcha($type = "admin") {
		$switch = C('captcha.' . $type);
		if (0 === $switch) {
			return false;
		}
		return true;
	}
	
	/*
	 * 生成图片验证码
	 * @param string $length  位数
	 * @param string $mode  字串类型 0字母数字 1数字 2小写字母 3大写字母 4字母 5中文
	 * @param string $type 图像格式
	 * @param string $width  宽度
	 * @param string $height  高度
	 * @param string $verifyName 验证码名称
	 * @return string
	 */
	static public function imgValidate($length = '', $mode = '', $type = '', $width = '', $height = '', $verifyName = '') {
		$img = new helper_image();
		if (!is_numeric($length)) $length = 4;
		if (!is_numeric($mode)) $mode = 1;
		if (empty($type)) $type = 'png';
		if (empty($width)) $width = 50;
		if (empty($height)) $height = 24;
		if (empty($verifyName)) $verifyName = 'default';
		$img->buildImageVerify($length, $mode, $type, $width, $height, $verifyName);
	}
	
	/*
	 * 生成验证码
	 * @param string $verifyName 验证码名称
	 * @param string $length  位数
	 * @param string $mode  字串类型 0字母数字 1数字 2小写字母 3大写字母 4字母 5中文
	 * @param string $session_id  不填默认获取session_id()
	 * @return string
	 */
	static public function validate($verifyName = '', $length = '', $mode = '', $session_id = '') {
		if (empty($verifyName)) $verifyName = 'default';
		if (!is_numeric($length)) $length = 4;
		if (!is_numeric($mode)) $mode = 0;
		$randval = helper_string::randString($length, $mode);
		$randval = (string)$randval;
		$captcha = array('code' => strtoupper($randval), 'time' => time());
		if(empty($session_id)){
			session('validate_'.$verifyName, $captcha);
		}else{
			helper_tool::redisCookie('validate_'.$verifyName, $captcha, '',$session_id);
		}
		return $captcha;
	}
	
	/*
	 * 获取指定时间内的验证码
	 * @param string $verifyName 验证码名称
	 * @param int $expiry_time  多少时间以内
	 * @param string $session_id  不填默认获取session_id()
	 * @return string
	 */
	static public function getValidate($verifyName = '',$expiry_time = 60, $session_id = '') {
		if (empty($verifyName)) $verifyName = 'default';
		if(empty($session_id)){
			$sess = session('validate_'.$verifyName);
		}else{
			$sess = helper_tool::redisCookie('validate_'.$verifyName, '', '',$session_id);
		}
		if(empty($sess) || empty($sess['code'])) return false;
		if($sess['time'] + $expiry_time > time()){
			return $sess;
		}else{
			return false;
		}
	}
	
	/**
	 * 验证验证码是否有效
	 *
	 * @param string $code  验证码串
	 * @param bool $del  验证完后是否删除验证码
	 * @param int $expiry_time  验证码过期时间
	 * @param string $verifyName  验证码名称
	 * @param string $session_id  不填默认获取session_id()
	 * @return boolean
	 */
	static public function checkValidate($code, $del = true, $expiry_time = '', $verifyName = '', $session_id = '') {
		if(empty($expiry_time)) $expiry_time = 600;
		if (empty($verifyName)) $verifyName = 'default';
		$code = (string)$code;
		if(empty($session_id)){
			$sess = session('validate_'.$verifyName);
		}else{
			$sess = helper_tool::redisCookie('validate_'.$verifyName, '', '',$session_id);
		}		
		if(empty($sess) || empty($sess['code'])) return false;
		if($del){
			if(empty($session_id)){
				session('validate_'.$verifyName, null);
			}else{
				helper_tool::redisCookie('validate_'.$verifyName, null, '',$session_id);
			}
		}
		if($sess['time'] + $expiry_time > time() && strtoupper($sess['code']) === strtoupper($code)){
			return true;
		}else{
			$sess['error_num'] = empty($sess['error_num']) ? 1 : $sess['error_num'] + 1;
			if($sess['error_num'] > 30){
				//防止穷举验证码，超过30次验证错误直接注销掉验证码				
				if(empty($session_id)){
					session('validate_'.$verifyName, null);
				}else{
					helper_tool::redisCookie('validate_'.$verifyName, null, '',$session_id);
				}
			}else{
				if(empty($session_id)){
					session('validate_'.$verifyName, $sess);
				}else{
					helper_tool::redisCookie('validate_'.$verifyName, $sess, '',$session_id);
				}				
			}
			return false;
		}
	}

	/**
	 * 发送邮件
	 * @param string $email 接收人email
	 * @param string $title 标题
	 * @param string $content 内容
	 * @param string $server 邮件服务商
	 * @param ting $isqueue 是否启用队列发送 默认启用
	 * @return return
	 */
	static function sendEmail($email='',$title='',$content='',$server = '',$isqueue = true){
		if(empty($email) || empty($title) || empty($content)) return array('status' => 0);
		if(empty($server)) $server = 'qq';
		if($isqueue){
			//入队列发送
			$data = array('type' => 'email', 'email' => $email, 'title' => $title, 'content' => $content, 'server' => $server);
			$cache = cache::getInstance('redis');
			$rs = $cache->lpush('list_remind', $data);
			$res = array('status'=> $rs!==false ? 1: 0);
			return $res;
		}
		
		if($server == 'qq' && mt_rand(1, 3) == 1){
			$server = 'sohu';
		}
				
		//即时发送
		$result = 0;
		if($server == 'qq'){			
			$users = array(
				array('service1@renrentou.com.cn', 'rrt123'),
				array('service2@renrentou.com.cn', 'rrt123'),
				array('service3@renrentou.com.cn', 'rrt123'),
				array('service4@renrentou.com.cn', 'rrt123'),
				array('service5@renrentou.com.cn', 'rrt123'),
				array('service6@renrentou.com.cn', 'rrt123'),
				array('service7@renrentou.com.cn', 'rrt123'),
				array('service8@renrentou.com.cn', 'rrt123'),
				array('service9@renrentou.com.cn', 'rrt123'),
				array('service10@renrentou.com.cn', 'rrt123'),	
				array('service1@renrentou.com', 'rrtjsb1'),
				array('service2@renrentou.com', 'rrtjsb2'),
				array('service3@renrentou.com', 'rrtjsb3'),
				array('service4@renrentou.com', 'rrtjsb4'),
				array('service5@renrentou.com', 'rrtjsb5'),
				array('service6@renrentou.com', 'rrtjsb6'),
				array('service7@renrentou.com', 'rrtjsb7'),
				array('service8@renrentou.com', 'rrtjsb8'),
				array('service9@renrentou.com', 'rrtjsb9'),
				array('service10@renrentou.com', 'rrtjsb10'),
				array('service11@renrentou.com', 'rrtjsb11'),
				array('service12@renrentou.com', 'rrtjsb12'),
				array('service13@renrentou.com', 'rrtjsb13'),
				array('service14@renrentou.com', 'rrtjsb14'),
				array('service15@renrentou.com', 'rrtjsb15'),
				array('service16@renrentou.com', 'rrtjsb16'),
				array('service17@renrentou.com', 'rrtjsb17'),
				array('service18@renrentou.com', 'rrtjsb18'),
				array('service19@renrentou.com', 'rrtjsb19'),
				array('service20@renrentou.com', 'rrtjsb20'),
				array('service21@renrentou.com', 'rrtjsb21'),
				array('service22@renrentou.com', 'rrtjsb22'),
				array('service23@renrentou.com', 'rrtjsb23'),
				array('service24@renrentou.com', 'rrtjsb24'),
				array('service25@renrentou.com', 'rrtjsb25'),
				array('service26@renrentou.com', 'rrtjsb26'),
				array('service27@renrentou.com', 'rrtjsb27'),
				array('service28@renrentou.com', 'rrtjsb28'),
				array('service29@renrentou.com', 'rrtjsb29'),
				array('service30@renrentou.com', 'rrtjsb30'),
				array('service31@renrentou.com', 'rrtjsb31'),
				array('service32@renrentou.com', 'rrtjsb32'),
				array('service33@renrentou.com', 'rrtjsb33'),
				array('service34@renrentou.com', 'rrtjsb34'),
				array('service35@renrentou.com', 'rrtjsb35'),
				array('service36@renrentou.com', 'rrtjsb36'),
				array('service37@renrentou.com', 'rrtjsb37'),
				array('service38@renrentou.com', 'rrtjsb38'),
				array('service39@renrentou.com', 'rrtjsb39'),
				array('service40@renrentou.com', 'rrtjsb40'),
				array('service41@renrentou.com', 'rrtjsb41'),
				array('service42@renrentou.com', 'rrtjsb42'),
				array('service43@renrentou.com', 'rrtjsb43'),
				array('service44@renrentou.com', 'rrtjsb44'),
				array('service45@renrentou.com', 'rrtjsb45'),
				array('service46@renrentou.com', 'rrtjsb46'),
				array('service47@renrentou.com', 'rrtjsb47'),
				array('service48@renrentou.com', 'rrtjsb48'),
				array('service49@renrentou.com', 'rrtjsb49'),
				array('service50@renrentou.com', 'rrtjsb50'),
				array('service51@renrentou.com', 'rrtjsb51'),
				array('service52@renrentou.com', 'rrtjsb52'),
				array('service53@renrentou.com', 'rrtjsb53'),
				array('service54@renrentou.com', 'rrtjsb54'),
				array('service55@renrentou.com', 'rrtjsb55'),
				array('service56@renrentou.com', 'rrtjsb56'),
				array('service57@renrentou.com', 'rrtjsb57'),
				array('service58@renrentou.com', 'rrtjsb58'),
				array('service59@renrentou.com', 'rrtjsb59'),
				array('service60@renrentou.com', 'rrtjsb60'),
				array('service61@renrentou.com', 'rrtjsb61'),
				array('service62@renrentou.com', 'rrtjsb62'),
				array('service63@renrentou.com', 'rrtjsb63'),
				array('service64@renrentou.com', 'rrtjsb64'),
				array('service65@renrentou.com', 'rrtjsb65'),
				array('service66@renrentou.com', 'rrtjsb66'),
				array('service67@renrentou.com', 'rrtjsb67'),
				array('service68@renrentou.com', 'rrtjsb68'),
				array('service69@renrentou.com', 'rrtjsb69'),
				array('service70@renrentou.com', 'rrtjsb70'),
				array('service71@renrentou.com', 'rrtjsb71'),
				array('service72@renrentou.com', 'rrtjsb72'),
				array('service73@renrentou.com', 'rrtjsb73'),
				array('service74@renrentou.com', 'rrtjsb74'),
				array('service75@renrentou.com', 'rrtjsb75'),
				array('service76@renrentou.com', 'rrtjsb76'),
				array('service77@renrentou.com', 'rrtjsb77'),
				array('service78@renrentou.com', 'rrtjsb78'),
				array('service79@renrentou.com', 'rrtjsb79'),
				array('service80@renrentou.com', 'rrtjsb80'),
				array('service81@renrentou.com', 'rrtjsb81'),
				array('service82@renrentou.com', 'rrtjsb82'),
				array('service83@renrentou.com', 'rrtjsb83'),
				array('service84@renrentou.com', 'rrtjsb84'),
				array('service85@renrentou.com', 'rrtjsb85'),
				array('service86@renrentou.com', 'rrtjsb86'),
				array('service87@renrentou.com', 'rrtjsb87'),
				array('service88@renrentou.com', 'rrtjsb88'),
				array('service89@renrentou.com', 'rrtjsb89'),
				array('service90@renrentou.com', 'rrtjsb90'),
				array('service91@renrentou.com', 'rrtjsb91'),
				array('service92@renrentou.com', 'rrtjsb92'),
				array('service93@renrentou.com', 'rrtjsb93'),
				array('service94@renrentou.com', 'rrtjsb94'),
				array('service95@renrentou.com', 'rrtjsb95'),
				array('service96@renrentou.com', 'rrtjsb96'),
				array('service97@renrentou.com', 'rrtjsb97'),
				array('service98@renrentou.com', 'rrtjsb98'),
				array('service99@renrentou.com', 'rrtjsb99'),
				array('service100@renrentou.com', 'rrtjsb100'),
			);
			$_arr = $users[array_rand($users)]; //随机查找一个邮箱

			//QQ邮件服务发送
			$config = C('email_config.'.$server);
			$send_email = $_arr[0]; //发信人
			$smtpserver = $config['host']; //SMTP服务器
			$smtpserverport = $config['port']; //SMTP服务器端口
			$smtpusermail = $send_email; //SMTP服务器的用户邮箱
			$smtpemailto = $email; //发送给谁
			$smtpuser = $send_email; //SMTP服务器的用户帐号
			$smtppass = $_arr[1]; //SMTP服务器的用户密码
			$smtpusername = $config['fromname'];//接收者显示的姓名
			$mailtype = "HTML"; //邮件格式（HTML/TXT）,TXT为文本邮件

			//邮件发送入库
			if(RUN_MODE == 'deploy'){
				//正式模式直接发送
				$smtp = new helper_mail($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);
				$result = $smtp->sendmail($smtpemailto, $smtpusermail, $smtpusername, $title, $content, $mailtype);
				$result = empty($result) ? 0 : 1;
			}else{
				$result = 1;
			}
		}elseif($server == 'sohu'){
			//sohu邮件服务发送
			$config = C('email_config.'.$server);
			if(RUN_MODE == 'deploy'){
				$param = array(
					'api_user' => $config['api_user'],
					'api_key' => $config['api_key'],
					'from' => $config['from'],
					'fromname' => $config['fromname'],
					'to' => $email,
					'subject' => $title,
					'html' => $content
				);
				$options = array('http' => array('method'  => 'POST','content' => http_build_query($param)));
				$context  = stream_context_create($options);
				$result = file_get_contents($config['host'], false, $context);
				if(!empty($result)) $result = json_decode($result,true);
				$result = is_array($result) && $result['message'] == 'success' ? 1 : 0;
			}else{
				$result = 1;
			}
		}
		return array('status'=> $result);
	}

	/**
	 * 发送短信
	 * @param int $mobile 接收人手机号
	 * @param string $content 内容
	 * @param string $isvoice 是否语音
	 * @param string $server 短信服务商
	 * @param bool $isqueue 是否启用队列发送 默认启用
	 * @param bool $iswork 是否白天工作时间发送
	 * @return return
	 */
	static function sendMobile($mobile='',$content='',$server = '',$isvoice=0,$isqueue = true,$iswork = 0){
		if(empty($mobile) || empty($content)) return array('status' => 0,'msg' => '手机或短信内容不能为空');
		if(empty($server)) $server = 'yimei'; //默认为亿美短信
		if($isvoice==1) $server = 'yimei';
		if($isqueue){			
			//入队
			$data = array('type' =>'mobile', 'mobile'=>$mobile,'content'=>$content, 'server'=>$server, 'isvoice'=>$isvoice,'iswork'=>$iswork);
			$cache = cache::getInstance('redis');
			if($iswork == 1){				
				$rs = $cache->lpush('list_remind_work', $data);
				$res = array('status'=> $rs!==false ? 1: 0);				
			}else{
				$rs = $cache->lpush('list_remind', $data);
				$res = array('status'=> $rs!==false ? 1: 0);
			}
			return $res;
		}
		
		//即时发送
		$result = 0;
		if($server == 'yimei' || $server == 'yimei2'){
			$config = C('sms_config.'.$server);
			$client = getInstanceOf('sms_'.$server.'_client');
			$client->client($config['url'],$config['username'], $config['password'], $config['sessionkey']);
			$client->setOutgoingEncoding("UTF-8");
			if(!is_array($mobile))  $mobile = explode(',', $mobile);
			if($isvoice){
				$info = $client->sendVoice($mobile, $content);
				if(substr($info, 0, 1) == '0')  $info = 0;
			}else{
				$info = $client->sendSMS($mobile, $content);
			}
			$result = $info==0 ? 1: 0;
		}elseif($server == 'yuntongxun'){
			$config = C('sms_config.'.$server);
			$rest = getInstanceOf('sms_yuntongxun_rest');
			$rest->init($config['host'],$config['port'],$config['version']);
			$rest->setAccount($config['main_account'],$config['main_token']);
			$rest->setAppId($config['app_id']);

			//调用语音验证码接口
			$result = $rest->voiceVerify($content,3,$mobile,'','');
			if($result == NULL ) {
				$result = 0;
			}elseif($result->statusCode != 0) {
				$result = 0;
			} else{
				$result = 1;
			}			
		}elseif($server == 'mdkj'){
			$config = C('sms_config.'.$server);
			$config['mobile'] = $mobile;
			$config['content'] = $content.'【人人投】';
			// 1 内容相同     2 内容不同
			$mdsms = getInstanceOf('sms_mdkj_sms');
			$result = $mdsms->sendSms($config,1);
		}		
		return array('status'=> $result);
	}
	
	/**
	 * 群发邮件
	 * @param string $email 接收人email
	 * @param string $title 标题
	 * @param string $content 内容
	 * @param string $server 邮件服务商
	 * @return return
	 */
	static function sendEmailMass($email='',$title='',$content='',$server=''){
		if(empty($email) || empty($title) || empty($content)) return array('status' => 0);
		if(empty($server)) $server = 'qq';
		//入队列发送
		$data = array('type' => 'email', 'email' => $email, 'title' => $title, 'content' => $content, 'server' => $server);
		$cache = cache::getInstance('redis');
		$rs = $cache->lpush('list_remind_mass', $data);
		$res = array('status'=> $rs!==false ? 1: 0);
		return $res;
	}
	
	/**
	 * 群发短信
	 * @param string $mobile 接收人手机号   以,分隔
	 * @param string $content 内容
	 * @param string $server 短信服务商   默认漫道科技
	 * @return return
	 */
	static function sendMobileMass($mobile='',$content='',$server = 'yimei2'){
		if(empty($mobile) || empty($content)) 
		{
			return array('status' => 0,'msg' => '手机或短信内容不能为空');
		}
		//入队
		$data = array('type' =>'mobile', 'mobile'=>$mobile,'content'=>$content, 'server'=>$server, 'isvoice'=>0,'iswork'=>0);
		$cache = cache::getInstance('redis');
		$rs = $cache->lpush('list_remind_mass', $data);
		$res = array('status'=> $rs!==false ? 1: 0);
		return $res;
	}
	
	/**
	 * 检测电子邮件真实性和唯一性
	 * @param  string  $email
	 * @param  bool  $isdata  检查数据库是否存在
	 * @return boolean
	 */
	static public function checkEmail($email, $isdata = false) {
		if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)) {
			if($isdata){
				$count = M('user')->where(array('email' => $email))->count();
				if($count > 0) return false;
			}
			if(!IS_WIN){
				list($username, $domain) = explode('@', $email);
				if (!checkdnsrr($domain, 'MX')) {
					return false;
				}
			}			
			return true;
		}
		return false;
	}
	
	/**
	 * 检测手机号码真实性和唯一性
	 * @param  string  $mobile
	 * @param  bool  $isdata  检查数据库是否存在
	 * @return boolean
	 */
	static public function checkMobile($mobile, $isdata = false) {
		if(preg_match("/^(1[3-9])\d{9}$/", $mobile)) {
			if($isdata){
				$count = M('user')->where(array('mobile' => $mobile))->count();
				if($count > 0) return false;
			}
			return true;
		}
		return false;
	}
	
	/**
	 * 密码加密
	 * @param  string  $str 密码
	 * @param  string  $key 密钥
	 * @return boolean
	 */
	static public function pwdEncode($str, $key = ''){
		$k = $key ? $key : C('key_password');
		$str = md5(trim($str) . $k);
		return $str;
	}
	
	/**
	 * 检测固话
	 * @param  string  $mobile
	 * @return boolean
	 */
	static public function checkTelphone($mobile) {
		if (!preg_match("/^(0[0-9]{2,3}-)?([2-9][0-9]{6,7})+(-[0-9]{1,4})?$/", $mobile)) {
			return false;
		}
		return true;
	}
	
	/**
	 * 检测QQ
	 * @param string $str 检测的字符串
	 * @return boolean
	 */
	static public function checkQQ($str)
    {
		return preg_match('/^\d{5,11}$/', $str);
	}

	/**
	 * 检测用户名真实性和唯一性
	 * @author liufei
	 * @param  string  $username
	 * @param  bool  $isdata  检查数据库是否存在
	 * @return boolean
	 */
	static public function checkUserName($username, $isdata = false) {
		if (preg_match("/^([a-zA-Z]|[\x{4E00}-\x{9FA5}]){1}([a-zA-Z]|[\x{4E00}-\x{9FA5}]|[0-9_])*$/u", $username)) {
			if(strlen($username) >= 6 && strlen($username) <= 30){
				//黑名单过滤
				$rs = model_user::checkBadKeyWords($username);
				if($rs > 0)   return false;
				if($isdata){
					$count = M('user')->where(array('username' => $username))->count();
					if($count > 0) return false;
				}
				return true;
			}else{
				return false;
			}
		}
		return false;
	}
	
	/**
	 * 检测身份证号真实性和唯一性
	 * @author liufei
	 * @param  string  $card_id 身份证号
	 * @param  bool  $isdata  检查数据库是否存在
	 * @return true 正确 false 错误
	 */
	static public function checkCard($card_id, $isdata = false) {
		$rs = helper_idcard::validateIDCard($card_id);
		if($rs == false) return false;
		if($isdata){
			$count = M('user_body')->where(array('u_body_num' => $card_id))->count();
			if($count > 0) return false;
		}
		return true;
	}
	
	/**
	 * 检测用户名密码
	 * @param  string  $email
	 * @return boolean
	 * huangnan 2015.04.07
	 */
	static public function checkPassword($password) {
		if (!preg_match("/^\S{6,30}$/", $password)) {
			return false;
		}else{
			return true;
		}
	}
	
    /**
     * 使用二维数组子节点中的某个字段来当作key（注意：这个值多数情况是唯一的，否则会被覆盖）
     * @param array $array 需要设置的数组
     * @param string $key key
     */
    public static function setKeyArray(array &$array, $key)
    {
        $arr = array();
        foreach ($array as &$val) {
            $arr[$val[$key]] = $val;
        }
        $array = $arr;

        return $array;
    }

    /**
     * 获取二维数组子节点中的某个字段
     * @param array $array 数组
     * @param string $field 获取的字段
     * @param boolean $unique 是否去重
     */
    public static function getFieldValue(array $array, $field, $unique = true)
    {
        $data = array_map(create_function('$v', 'return $v["' . $field . '"];'), $array);
        if ($unique) {
            $data = array_unique($data);
        }

        return array_values($data);
    }
    
	/**
	 * 检测字符串中是否包含电子邮件
	 * @param string $str
	 * @return boolean
	 */
	public static function existEmail($str)
    {
		return preg_match('/([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+/', $str);
	}
	
	/**
	 * 检测字符中是否包含手机号码
	 * @param string $str
	 * @param boolean $allButt 匹配全部
	 * @return boolean
	 */
	public static function existMobile($str, $allButt = false)
    {
        if ($allButt) {
            return preg_match('/^(1[3-9])\d{9}$/', $str) ? true : false;
        }
        else {
            return preg_match('/(1[3-9])\d{9}/', $str) ? true : false;
        }
	}
	
	/**
	 * 检测字符串中是否包含固话
	 * @param string $str
	 * @param boolean $allButt 匹配全部
	 * @return boolean
	 */
	public static function existTel($str, $allButt = false)
    {
        if ($allButt) {
		    return preg_match('/^(0[0-9]{2,3}-)?([2-9][0-9]{6,7})+(-[0-9]{1,4})?$/', $str) ? true : false;
        }
        else {
		    return preg_match('/(0[0-9]{2,3}-)?([2-9][0-9]{6,7})+(-[0-9]{1,4})?/', $str) ? true : false;
        }
	}
	
	/**
	 * 检测字符串中是否包含QQ
	 * @param string $str 检测的字符串
	 * @param boolean $allButt 匹配全部
	 * @return boolean
	 */
	public static function existQQ($str, $allButt = false)
    {
        if ($allButt) {
		    return preg_match('/^\d{5,11}$/', $str) ? true : false;
        }
        else {
		    return preg_match('/\d{5,11}/', $str) ? true : false;
        }
	}

    /**
     * 检测是否是移动端
     */
    public static function isWap()
    {
        // 移动端类型
        $types  = 'iPhone|Android|WAP|NetFront|JAVA|OperasMini|UCWEB|WindowssCE|Symbian|';
        $types .= 'Series|webOS|SonyEricsson|Sony|BlackBerry|Cellphone|dopod|Nokia|samsung|PalmSource|';
        $types .= 'Xphone|Xda|Smartphone|PIEPlus|MEIZU|MIDP|CLDC';

        // wap域名访问
        if (SUB_DOMAIN == 'wap') {
            return true;
        }
        // 判断客户端是否是移动类型
        elseif (preg_match("/{$types}/i", $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        }
        else {
            if (preg_match('/(mozilla|chrome|safari|opera|m3gate|winwap|openwave|ipad)/i', $_SERVER['HTTP_USER_AGENT'])) {
                return false;
            }
            else {
                // 如果参数中强制声明是wap
                if (isset($_GET['mobile']) && $_GET['mobile'] === 'yes') {
                    return true;
                }
                else {
                    return false;
                }
            }
        }
    }
    
    /**
     * 获取二级域名
     * @param string $domain 域名前缀
     * return string
     */
    public static function domain($domain)
    {
        $domain = $domain ? $domain : (self::isWap() ? 'wap' : 'www');
        $sysconfig = C('sysconfig');
        return HTTP_PROTOCAL . "{$domain}.{$sysconfig['web_domain']}";
    }
    
    /**
     * 获取当前域下的连接地址
     * @param string $str 连接的一部分
     * @param string $domain 强制指定域
     * return string
     */
    public static function currentDomainUrl($str, $domain = 'www')
    {
        return url(self::currentSubDomain() . '-' . $str);
    }
    
    /**
     * 获取当前站的域(包括www、wap但不包括user、img等)
     * return string
     */
    public static function currentSubDomain()
    {
//        return self::isWap() ? 'wap' : (cookie('sub_domain') ? cookie('sub_domain') : 'www');
        return cookie('sub_domain') ? cookie('sub_domain') : 'www';
    }
    
    /**
     * 获取缩略图片URL地址
     * @param string $img 原始图片地址 /s/upload/xxx.jpg
     * @param string $w 宽
     * @param string $h 高
     * @return string
     * @author: liufei
     */
    static public function getThumbImg($img, $w = 0, $h = 0){
        if ($img) {
            $url = C('url');
            if (C('is_thumb_open') && $w && $h) {
                $img_url = $url['img3'] . $img . "@1e_{$w}w_{$h}h_1c_0i_1o_90Q_1x.jpg";
            } else {
                $img_url = $url['img2'] . $img;
            }
        } else {
            $img_url = "";
        }
        return $img_url;
    }


    
    /**
     * 获取指定图片尺寸后缀
     * @param int $type 类型
     * return string
     */
    public static function getSizeImgExt($type)
    {
        $thumbConfig = C('thumb_config');
        return isset($thumbConfig[$type]) ? $thumbConfig[$type] : '';
    }
    
    /*
     * 生成图片地址 用于前台直接输出显示
    *	$img 图片数据库地址
    *	$t   生成缩略图类型
    *	    face  =>   宽度100px 高度100px
    *		project_w300_h200  =>   宽度300px 高度200px
    *		project_w253_h168  =>   宽度253px 高度168px
    *   20150806  huangnan
    */
    public static function img_url_show($img,$t=0)
    {
    	if($img)
    	{
	    	$type = self::getSizeImgExt($t);
	    	$url = C('url');
	    	$is_thumb_open = C('is_thumb_open');
	    	if($type && $is_thumb_open)
	    	{
	    		$img_url = $url['img3'].$img.$type;
	    	}else
	    	{
	    		$img_url = $url['img2'].$img;
	    	}	
    	}else
    	{
    		$img_url = "";
    	}
    	return $img_url;
    }
    

    /**
     * 安全格式化一个半角逗号分隔的ID字符串
     * @param string $str 半角逗号分隔的ID字符串
     * @param boolean $returnArray 是否返回数组[true-以数组的方式返回格式化的字符串，false-返回字符串]
     * @return array
     */
    public static function safeFormatIDStr($str, $returnArray = false)
    {
        if (!$str) {
            return array();
        }
        $IDList = explode(',', $str);
        foreach ($IDList as $key => &$val) {
            $val = trim($val);
            if (!$val) {
                unset($IDList[$key]);
            }
        }
        return $returnArray ? $IDList : implode(',', $IDList);
    }
	
	/**
	 * 浏览器行为检查
	 * @param int $time 间隔时间
	 * @param string $name 行为名称
	 * @param string $key 用户登录标识 为空默认为session_id
	 * @return bool 真为通过成功  假为通过失败
	 */
    static public function refreshBrowser($time = 1, $name = '', $key = '')
    {
		if(empty($name)) $name = 'default';
		if(empty($time) || !is_numeric($time)) $time = 1;
		if (empty($key)){
			if(!isset($_SESSION)) session_start();
			$key = session_id();
		}
		$key = '_browser_refresh_'.md5("{$name}_{$key}");
		$redis = cache::getInstance('redis');
		$last_time = $redis->get($key);
		
		$res = false;
		if(empty($last_time)){
            $redis->set($key, time(), $time);
			$res = true;
		}
        return $res;
	}
	
	/**
	 * redis模拟cookie
	 * @param int $time 间隔时间
	 * @param string $key cookie键
	 * @param string $value cookie值
	 * @param string $expiretime 过期时间， 默认为cookie配置里面的过期时间
	 * @param string $session_id 唯一用户标识符
	 * @return bool
	 */
    static public function redisCookie($name = '', $value = '', $expiretime = '', $session_id = '') {
		if(empty($session_id)) {
			if(!isset($_SESSION)) session_start();
			$session_id = session_id();
		}
		$key = 'cookie_'.md5($name.$session_id);
		$redis = cache::getInstance('redis');
		if ('' === $value) {
			$rs = $redis->get($key);
		}else{
			if (is_null($value)) {
				$rs = $redis->rm($key);
			} else {
				$expire = $expiretime ? (int)$expiretime : C('sys_cookie_expire');
				$rs = $redis->set($key, $value, $expire);
			}
		}
		return $rs;
	}

    /**
     * ID和有效时间戳至的authcode加密字符串，例如：authcode('3412-1491542224', 'ENCODE')的结果就是一个$sign有效参数
     * @return array
     */
    public static function signdecode($sign)
    {
        $sign = authcode($sign);
        if ($sign) {
            $sign = explode('-', $sign);
            if (count($sign) >= 2) {
                if ($sign[1] > time()) {
                    return $sign;
                }
            }
        }
        return false;
    }

    /**
     * 是否是搜素引擎
     * @return bool
     */
    public static function isRobot()
    {
        $spiders = 'Bot|Crawl|Spider|slurp|sohu-search|lycos|robozilla';
        $browsers = 'MSIE|Netscape|Opera|Konqueror|Mozilla';
        $agent = $_SERVER['HTTP_USER_AGENT'];
        if (strpos($agent, 'http://') !== false && preg_match("/($browsers)/i", $agent)) {
            return false;
        } elseif (preg_match("/($spiders)/i", $agent)) {  
            return true;
        } else {  
            return false;
        }
    }  
    
    /**
     * 过滤一个字符串
     * @param string $string 需要过滤的字符串
     * @return string
     */
    public static function safeString($string)
    {
        return $string ? addslashes(htmlspecialchars(trim(strip_tags($string)))) : '';
    }
    
    /**
     * 生成短链接，短网址
     * huangnan 
     * 2015.10.19
     */
    public static function shortUrl($url)
    {
    	$ch=curl_init();
    	curl_setopt($ch,CURLOPT_URL,"http://dwz.cn/create.php");
    	curl_setopt($ch,CURLOPT_POST,true);
    	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    	$data=array('url'=>$url);
    	curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
    	$strRes=curl_exec($ch);
    	curl_close($ch);
    	$arrResponse=json_decode($strRes,true);
    	if($arrResponse['tinyurl'])
    	{
    		return $arrResponse['tinyurl'];
    	}else{
    		return false;
    	}
    }
    

    /**
     * 获取分页样式
     * @param int $totalCount 记录总数
     * @param int $pageSize 分页大小
     * @param int $currentPage 当前页
     * @param boolean $isAjax 是否是ajax分页
     * @return string
     */
    public static function pager($totalCount, $pageSize = 10, $currentPage = 1, $isAjax = false)
    {
        $totalPage = ceil($totalCount / $pageSize);
        $currentPage = self::currentPage($totalCount, $pageSize, $currentPage);
        if ($isAjax) {
            $pager = new helper_page($totalCount, $currentPage, $pageSize, array(), true);
        } else {
            $pager = new helper_page($totalCount, $currentPage, $pageSize);
        }
	    // 分页条主题
        $pager->setConfig('theme', ' %firstPage%  %prePage%  %linkPage%  %nextPage%  %lastPage%');
        $pager->setTheme('theme');

        return $pager->show();
    }

    /**
     * 获取正确的当前分页
     * @param int $totalCount 记录总数
     * @param int $pageSize 分页大小
     * @param int $currentPage 当前页
     * @return int
     */
    public static function currentPage($totalCount, $pageSize = 10, $currentPage = 1)
    {
        $totalPage = ceil($totalCount / $pageSize);
        $currentPage = $currentPage > $totalPage ? $totalPage : $currentPage;
        $currentPage = $currentPage > 0 ? $currentPage : 1;
        return $currentPage;
    }
    
    /**
     * 获取字符串参数
     * @param string $key 提交的参数key
     * @param string $default 默认值
     * @param string $method 提交的方式
     * @return string
     */
    public static function strParam($key, $default = '', $method = 'REQUEST')
    {
        switch (strtoupper($method)) {
            case 'POST':
                $value = empty($_POST[$key]) ? $default : $_POST[$key];
                break;
            case 'GET':
                $value = empty($_GET[$key]) ? $default : $_GET[$key];
                break;
            default:
                $value = empty($_REQUEST[$key]) ? $default : $_REQUEST[$key];
        }
        $value = trim($value);
        $value = addslashes($value);
        $value = htmlspecialchars($value);
        return $value;
    }
	
	/**
	* 网址转字符
	* @param string $url url地址
	* @param bool $js 是否js跳转
	* @param bool $redirect 是否直接跳转
	* @return string
	*/	
   static public function redirect($url = '', $js = true, $redirect = false){
	   if($redirect) redirect($url);
	   $url = urlencode($url);
	   if($js){
		   return C('url.www').'/redirect/js/?url='.$url;
	   }else{
		   return C('url.www').'/redirect/?url='.$url;
	   }
   }
   
   /**
	 * 黑名单IP检查
	 * @param string $name IP验证类型名称  view:访问黑名单  sms:短信发送黑名单
	 * @param string $ip IP
	 * @param string $value 黑名单次数  null为清除黑名单  空为读取黑名单次数
	 * @param string $expiry_time 黑名单过期时间 默认1年
	 * @return string 黑名单加入统计的次数
	 * @author: liufei
	 */
	public function chkBlackIp($name = '', $ip = '', $value = '', $expiry_time = 31536000) {
		if(empty($name) || empty($ip)) return 0;
		$num = G($name.$ip, $value, $expiry_time);
		return $num;
	}
    
    /**
     * 格式化人民币(单位：万)
     * @param float $money 钱币数
     * @param string
     */
    public static function moneyFormat($money)
    {
        if (!is_numeric($money)) {
            return 0;
        }
        $base = 10000;
        if ($money >= $base) {
            $end = '万';
            $money = intval($money * 100 / $base) / 100;
        } else {
            $end = '元';
            $money = intval($money * 100) / 100;
        }
        return "¥{$money}{$end}";
    }

    /**
     * 判断当前浏览器是否为微信内置浏览器
     * @return bool
     */
    public static function isWechat() {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }

    /**
     * 判断当前浏览器是否为指定浏览器(pc|iphone|ipad|android)
     * @param $browser
     * @return bool
     */
    public static function isBrowserOf($browser) {
        if ($browser == 'pc') $browser = 'windows nt';
        if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']), strtolower($browser)) !== false) {
            return true;
        }
        return false;
    }

    /**
     * 判断当前浏览器是否为PC
     * @return bool
     */
    public static function isPc() {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'windows nt') !== false) {
            return true;
        }
        return false;
    }

       /**
     * 人民币金额数字转中文大写(单位：万)
     * @param float $money 钱币数
     * @param string
     */
       public function moneycny($num) {
                $c1 = "零壹贰叁肆伍陆柒捌玖";
        $c2 = "分角元拾佰仟万拾佰仟亿";
        //精确到分后面就不要了，所以只留两个小数位
        $num = round($num, 2); 
        //将数字转化为整数
        $num = $num * 100;
        if (strlen($num) > 10) {
                return "金额太大，请检查";
        } 
        $i = 0;
        $c = "";
        while (1) {
                if ($i == 0) {
                        //获取最后一位数字
                        $n = substr($num, strlen($num)-1, 1);
                } else {
                        $n = $num % 10;
                }
                //每次将最后一位数字转化为中文
                $p1 = substr($c1, 3 * $n, 3);
                $p2 = substr($c2, 3 * $i, 3);
                if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
                        $c = $p1 . $p2 . $c;
                } else {
                        $c = $p1 . $c;
                }
                $i = $i + 1;
                //去掉数字最后一位了
                $num = $num / 10;
                $num = (int)$num;
                //结束循环
                if ($num == 0) {
                        break;
                } 
        }
        $j = 0;
        $slen = strlen($c);
        while ($j < $slen) {
                //utf8一个汉字相当3个字符
                $m = substr($c, $j, 6);
                //处理数字中很多0的情况,每次循环去掉一个汉字“零”
                if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
                        $left = substr($c, 0, $j);
                        $right = substr($c, $j + 3);
                        $c = $left . $right;
                        $j = $j-3;
                        $slen = $slen-3;
                } 
                $j = $j + 3;
        } 
        //这个是为了去掉类似23.0中最后一个“零”字
        if (substr($c, strlen($c)-3, 3) == '零') {
                $c = substr($c, 0, strlen($c)-3);
        }
        //将处理的汉字加上“整”
        if (empty($c)) {
                return "零元整";
        }else{
                return $c . "整";
        }
    }
 function array_old_column($input, $columnKey, $indexKey = NULL)
  {
    $columnKeyIsNumber = (is_numeric($columnKey)) ? TRUE : FALSE;
    $indexKeyIsNull = (is_null($indexKey)) ? TRUE : FALSE;
    $indexKeyIsNumber = (is_numeric($indexKey)) ? TRUE : FALSE;
    $result = array();
 
    foreach ((array)$input AS $key => $row)
    { 
      if ($columnKeyIsNumber)
      {
        $tmp = array_slice($row, $columnKey, 1);
        $tmp = (is_array($tmp) && !empty($tmp)) ? current($tmp) : NULL;
      }
      else
      {
        $tmp = isset($row[$columnKey]) ? $row[$columnKey] : NULL;
      }
      if ( ! $indexKeyIsNull)
      {
        if ($indexKeyIsNumber)
        {
          $key = array_slice($row, $indexKey, 1);
          $key = (is_array($key) && ! empty($key)) ? current($key) : NULL;
          $key = is_null($key) ? 0 : $key;
        }
        else
        {
          $key = isset($row[$indexKey]) ? $row[$indexKey] : 0;
        }
      }
 
      $result[$key] = $tmp;
    }
 
    return $result;
  }
  /**
	 * 合同
	 * @param string $email 接收人email
	 * @param int $pid 项目ID
	 * @param int $uid 用户ID
	 * @param ting $isqueue 是否启用队列发送 默认启用
	 * @return return
	 */
	static function sendSign($pid='',$uid='',$isqueue = true){
		if(empty($pid) || empty($uid) ) return array('status' => 0);
		if(empty($server)) $server = 'qq';
		if($isqueue){
			//入队列发送
			$data = array('type' => 'contract', 'pid' => $pid, 'uid' => $uid );
			$cache = cache::getInstance('redis');
			$rs = $cache->lpush('list_contract', $data);
			$res = array('status'=> $rs!==false ? 1: 0);
			return $res;
		}
        }
}
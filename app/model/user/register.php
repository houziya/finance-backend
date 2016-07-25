<?php
/**
 * 注册相关公用信息
 * User: wangbingang
 * Date: 2015/6/4
 */

class model_user_register
{
	/**
	 * 注册推荐,推广联盟配置信息
	 * @var unknown
	 */
	public static $conf = array(
			'REGISTER_TM_NAME'=>'reg_tm', //推荐注册cookie的KEY名称
//			'REGISTER_TM_NAME_PARAM'=>'reg_tm_param', //推荐注册额外参数
			'REGISTER_TM_CODE'=>'k', //推荐注册URL标识符
			'REGISTER_TM_TIME'=>86400, //推荐注册cook有效时间,单位秒
			'REGISTER_TM_NAME_activity'=>'reg_tm_activity', //推荐注册session的KEY名称
			'REGISTER_TM_CODE_activity'=>'activ', //推荐注册活动URL标识符
//			'REGISTER_TM_LM_NAME'=>'REGISTER_TM_LM_NAME', //推广联盟注册cookie的KEY名称
//			'REGISTER_TM_LM_CODE'=>'k', //推广联盟注册url标识符 url?$1=xx
//			'REGISTER_TM_LM_TIME'=>86400, //推广联盟注册cook有效时间,单位秒
			'REGISTER_MOBILE_CODE'=>'REGISTER_MOBILE_CODE', //手机验证码session的KEY名称
			'MOBILE_CODE_TIME'=>60, //发送注册手机验证码间隔时间,单位:秒
	);
	
	/*
	 * 种植推广来源信息
	 * @author liufei
	 * @param string $session_id 当前用户唯一标识符
	 * @return bool
	 */
	public function setTmSource($session_id = '') {
		//设置推荐注册
		$key = isset($_GET[self::$conf['REGISTER_TM_CODE']]) ? helper_safe::new_html_special_chars($_GET[self::$conf['REGISTER_TM_CODE']]) : '';
		if(empty($key)) return false;
		//检查加密地址合法性
		$arr = model_lianmengUser::checkCookdata($key);
		if($arr['status'] != 1) return false;
		//等待保存的数据		
		$param = array();
		$param['string'] = $key;
		$param['type'] = $arr['type'];
		$param['data'] = $arr['data'];
		$param['active'] = $arr['active'];
		$param['time'] = time();
		$param['source'] = C('sys_global_source');
		$param['url'] = isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI']) ? 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] : '';
		$param['referer'] = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
		$param['get'] = array();
		$param['session_id'] = $session_id;
		//提取url参数
		$query = parse_url($param['url']);
		if($query){
			parse_str($query['query'],$get);			
			if(empty($get)) $get = array();
			if($_GET) $get = array_merge($get, $_GET);
			if($session_id) $get['session_id'] = $session_id;
			unset($get[self::$conf['REGISTER_TM_CODE']]);
			$param['get'] = $get;
		}
		//合法推广地址 写入redis
		helper_tool::redisCookie(self::$conf['REGISTER_TM_NAME'],$param,self::$conf['REGISTER_TM_TIME'],$session_id);
		return true;
	}
	
	/*
	 * 添加推广用户
	 * @author liufei
	 * @param int $uid 新注册用户
	 * @param string $session_id 唯一标识符
	 * @return return
	 */
	public function addTgUser($uid, $session_id = ''){
		//获取推广来源种植的cookie信息
		$cookie = helper_tool::redisCookie(self::$conf['REGISTER_TM_NAME'],'','',$session_id);
		if(empty($cookie)) return array('status' => 0, 'msg' => '失败');
		
		$callback_function_html = '';
		if($cookie['type'] == 'uid'){
			//用户推荐注册
			$this->addRecommend($uid, $cookie);
		}elseif($cookie['type'] == 'tg'){
			//推广联盟注册
			$callback_function_html = $this->addLianmeng($uid, $cookie);
		}
		return array('status' => 1, 'msg' => '成功', 'uid' => $uid, 'data' => $callback_function_html);
	}
	
	/**
	 * 添加推荐注册关系
	 * @param int $uid 新注册用户ID
	 * @param array $data 推广来源信息
	 */
	public function addRecommend($uid, $data)
	{
        if (!is_numeric($uid) || empty($data['type']) || $data['type'] != 'uid') {
			return false;
		}
		$top_uid = $data['data'];
		$recommend_data = array();
		$recommend_data['top_uid'] = $top_uid;
		$recommend_data['uid'] = $uid;
		$recommend_data['source'] = $data['source'];
		if( isset($data['active'])  && $data['active'] )
			$recommend_data['active'] = $data['active'];
		$active = session(self::$conf['REGISTER_TM_NAME_activity']);
		$addRecommend_result = model_userrecommend::addRecommend($recommend_data, $active);
		if ($addRecommend_result['status'] != 1) {
			$error_msg = '添加推荐注册关系失败 -1,来源:' . $data['source'] . ' 类:' . __CLASS__ . ' 方法:' . __FUNCTION__ . ' 错误状态:' . $addRecommend_result['status'] . ' 错误信息:' . $addRecommend_result['msg'] . ' 添加SQL:【' . M()->sql() . '】  错误信息:' . mysql_error();
			log::write($error_msg, 'INFO');
		} else {
            //写入推荐成功触发动作到队列
            /*$_arr = array('type' => 'urecommend', 'top_uid' => $top_uid, 'uid' => $uid, 'activity_logo' => $active);
            $redis = cache::getInstance('redis');
            $redis->lpush('list_actions', $_arr);*/

            // 红包雨，更新推荐用户金币
            $r = model_packets_packets::upUserCoin($top_uid,$active);
            cache::getInstance('redis')->lpush('reg_packets_up_coin_list'.date('Ymd'),$r);
        }
		
		helper_tool::redisCookie(self::$conf['REGISTER_TM_NAME'],null,'',$data['session_id']); //删除推广信息
		session(self::$conf['REGISTER_TM_NAME_activity'],null);
		return true;
	}
	
	/**
	 * 添加联盟注册关系
	 * @param int $uid 新注册用户ID
	 * @param array $data 推广来源信息
	 */
	public function addLianmeng($uid, $data) {
		if (!is_numeric($uid) || empty($data['type']) || $data['type'] != 'tg') {
			return '';
		}
		$pid = $data['data'];
		
		$userData = model_user::getInfo($uid);
		if(empty($userData)) return '';
		
		$lianmengplatform = model_lianmengPlatform::getInfo($pid);
		
		$lianmeng_data = array();
		$lianmeng_data['uid'] = $userData['uid'];
		$lianmeng_data['username'] = $userData['username'];
		$lianmeng_data['lianmeng_id'] = $pid;
		$lianmeng_data['lianmeng_typeid'] = $lianmengplatform['lianmeng_typeid'];
		$lianmeng_data['setting'] = serialize($data);
		$lianmeng_data['referer'] = $data['referer'];

		$callback = ''; //回调信息
		$addUserLianmeng_result = model_lianmengUser::addUserLianmeng($lianmeng_data); //添加到联盟表
		if ($addUserLianmeng_result['status'] != 1) {
			$error_msg = '添加推广联盟关系失败 -1,来源:' . $data['source'] . ' 类:' . __CLASS__ . ' 方法:' . __FUNCTION__ . ' 错误状态:' . $addUserLianmeng_result['status'] . ' 错误信息:' . $addUserLianmeng_result['msg'] . ' 添加SQL:【' . M()->sql() . '】  错误信息:' . mysql_error();
			log::write($error_msg, 'INFO');
		}else{
			//获取回调信息
			if($lianmengplatform['callback_function_type'] == 0){
				//成功后即时回调
				$callback = D('lianmengPlatform')->callbackLianMeng($pid, $lianmeng_data);
			}
		}

		helper_tool::redisCookie(self::$conf['REGISTER_TM_NAME'],null,'',$data['session_id']); //删除推广信息
		return empty($callback) ? '' : $callback;
	}
	
	/**
	 * 检测用户表单输入
	 * @params array $data 验证数据
	 * @params $is_open_captcha 是否验证验证码
	 * @author wangbingang
	 */
	public function checkForm($data, $is_open_captcha)
	{
		if (!helper_tool::checkMobile($data['mobile'])) {
			return array('status'=>-4001, 'msg'=>'手机号码不正确');
		}
		//##debug##
		$getValidate = helper_tool::getValidate('mobile_reg', self::$conf['MOBILE_CODE_TIME']*10);
		if($getValidate['code'] != $data['mobilecode']) {
			return array('status'=>-4002, 'msg'=>'手机验证码不正确');
		}
		if (!helper_tool::checkPassword($data['password'])) {
			return array('status'=>-4003, 'msg'=>'密码必须在6-30位之间');
		}
		if ($is_open_captcha) {
			if (!helper_tool::checkValidate($data['code'])) {
				return array('status'=>-4004, 'msg'=>'验证码不正确');
			}
		}
		if (!$data['xieyi']) {
			return array('status'=>-4005, 'msg'=>'请您先同意注册协议');
		}
		return array('status'=>1, 'msg'=>'success');
	}
	
	/**
	 * 发送注册手机验证码
	 * @author wangbingang
	 */
	public function sendMobileCode($mobile, $code, $msg_type)
	{
		if (!helper_tool::checkValidate($code, true, '', 'send_mobile_code')) {
			return array('status'=>-4001, 'msg'=>'验证码不正确');
		}
		//if (session(self::$conf['REGISTER_MOBILE_CODE']. 'time') && time() - session(self::$conf['REGISTER_MOBILE_CODE']. 'time') < self::$conf['MOBILE_CODE_TIME']) {
		//	$s_time = (self::$conf['MOBILE_CODE_TIME'] - (time() - session(self::$conf['REGISTER_MOBILE_CODE'] . 'time')));
		//	return array('status'=>-4002, 'msg'=>'验证码发送过于频繁,请'.$s_time.'秒后再发送');
		//}
		if(helper_tool::getValidate('mobile_reg', self::$conf['MOBILE_CODE_TIME'])) {
			return array('status'=>-4002, 'msg'=>'验证码发送过于频繁,请稍后再发送');
		}
		if (!helper_tool::checkMobile($mobile)) {
			return array('status'=>-4003, 'msg'=>'手机号码不正确');
		}
		if (D('user')->checkMobileExist($mobile)) {
			return array('status'=>-4004, 'msg'=>'该手机号码已经注册过,请更换手机号');
		}
		$number = helper_tool::validate('mobile_reg', 6, 1); //上线使用
        $number = $number['code'];
		/*//测试验证码
		$number = '0000';
		$captcha = array('code' => strtoupper($number), 'time' => time());
		session('validate_mobile_reg', $captcha);*/
		
		$voice  = $msg_type=='voice' ? 1 : 0;
		$msg_content = $number;
		
		//防作弊处理
		if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 'xmlhttprequest' != strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])) {
			return array('status'=>1, 'msg'=>'验证码发送成功'); //返回虚假成功
		}		
		//黑名单检查
		if(helper_tool::chkBlackIp('sms',getIp())){
			return array('status'=>1, 'msg'=>'验证码发送成功'); //返回虚假成功
		}
		
		//获取短信模版内容
		if(!$voice) {
			$msg_content = model_remind::getTplContent('mobile_reg', array('valicode' => $number));
			$msg_content = $msg_content['mobile']['content'];
		}
		if (model_remind::sendMobile($mobile, $msg_content, 'mobile_reg','',$voice)) {
			//session(self::$conf['REGISTER_MOBILE_CODE'], $number);
			//session(self::$conf['REGISTER_MOBILE_CODE'] . 'time', time());
			return array('status'=>1, 'msg'=>'验证码发送成功');
		}
		return array('status'=>-4005, 'msg'=>'验证码发送失败');
	}
	
	
}
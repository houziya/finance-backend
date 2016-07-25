<?php
class controller_app_login extends controller_app_abstract {

	public function __construct() {
		parent::__construct();
	}
	/*
	 * App 登录
	 * @author bjs
	 * @return int
	 */
	public function actionSetLogin(){
        //if ($_SERVER['REQUEST_METHOD'] != 'POST')  $this->ajax(-3, '请求方式错误');

        $username = trim($this->_post('username'),'');
        $password = trim($this->_post('password'),'');
        $devices_id = (string)trim($this->_post('devices_id',''));
        //$device_info['devices_model'] = (string)trim($this->_post('devices_model'))?trim($this->_post('devices_model')):'xiaomi';
        //$device_info['system_version'] = (string)trim($this->_post('system_version'))?trim($this->_post('system_version')):'android5.0';

        //登录判断
        if(!empty($username) && !empty($password)){
            $this->checkForm($username,$password);
            $key = $this->getRandNumberKey();
            $access_token = self::APP_LOGIN_USER_INFO.md5($key);
            $is_login = $this->userLogin(model_user::username2userid($username),$access_token,controller_app_abstract::TOKEN_LIFE_TIME,C('sys_global_source'));
            if(!$is_login) {
                $this->ajax(-107,'登录失败,请重试');
            }else{
                //$user_data = S($access_token);
                $redis = cache::getInstance('redis');
                $user_data = $redis->get($access_token);
                $data = array(
                    'access_token' => $key,
                    'userinfo' => $user_data,
                );

                //todo 修改登录用户设备信息
                if(!empty($devices_id)){
                    $device_info['uid'] = $user_data['uid'];
                    $device_info['is_bind'] = 1;
                    $device_info['login_ip'] = (string)getIP();
                    $device_info['login_time'] = time();
                    model_MobileDevices::updateMobileDevices($device_info,array('devices_id'=>$devices_id));
                }

                $this->ajax('-101', '登录成功', $data);
            }
        }else{
            $this->ajax('-102', '请填写用户名或密码');
        }
        return true;
	}

    /**
     * 检测用户表单输入
     */
    private function checkForm($username,$password)
    {
        if(!helper_tool::checkEmail($username) && !helper_tool::checkMobile($username)) {
            $this->ajax('-103', '用户名不合法');
        }
        //$user_data = model_user::getInfo($username);
		$user_data = model_user::getInfo($username, null, true);
        if(!$user_data) {
            $this->ajax('-104', '用户名不存在');
        }
        if($user_data['password'] != helper_tool::pwdEncode($password)) {
            $this->ajax('-105', '登录密码不正确');
        }
        if($user_data['status'] != 1) {
            $this->ajax('-106', '账户正在审核中');
        }
        if($user_data['is_enterprise']  >= 1) {
            $this->ajax('-107', '企业用户请在PC端进行登录');
        }
    }


    /**
     * 验证码
     * @author bjs
     */
    private function getRandNumberKey()
    {
        return helper_string::randString(32);
    }

    /*
     * 获取版本信息
     * @return array
     * @author Baijiansheng
     */
    public function actionGetVersion() {
        $data['ios_version'] = sprintf('%.1f', 5.4);
        $data['android_version'] = 31;//sprintf('%.1f', 23.0);
        $data['ios_download'] =  'https://itunes.apple.com/cn/app/ren-ren-tou/id978259052?mt=8';//itms-apps://itunes.apple.com/WebObjects/MZStore.woa/wa/viewSoftware?id=490355553
        $data['android_download'] =   'http://www.renrentou.com/s/download/app/cn.mobile.renrentou.apk';
        $data['ios_explain'] =  '';
        $data['android_explain'] =  "1、视频播放更新；\n2、版本性能优化";
        $this->ajax('-101','获取成功',$data);
    }
    /*
     * 退出登录
     * @return array
     * @author Baijiansheng
     */
    public function actionSetLoginOut() {
        $map['uid'] = $this->_userinfo['uid'];
        if(!$map['uid']) $this->ajax('-6','您还未登录');
        $access_token = !empty($this->post['access_token']) ? $this->post['access_token'] : '';

        $MobileDevices = D('MobileDevices');
        $devices_info = $MobileDevices->getMobileDevices($map);
        $cache = cache::getInstance('redis');
        if(!empty($devices_info)){
            if(model_MobileDevices::updateMobileDevices(array('is_bind'=>0),array('uid'=>$map['uid']))){
                $key = self::APP_LOGIN_USER_INFO.md5($access_token);
                $cache->rm($key);
                $this->ajax('-101','退出成功');
            }else{
                $this->ajax('-102','退出失败');
            }
        }else{
            $key = self::APP_LOGIN_USER_INFO.md5($access_token);
            $cache->rm($key);
            $this->ajax('-101','退出成功');
        }
    }

}

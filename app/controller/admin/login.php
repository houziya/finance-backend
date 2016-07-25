<?php
// +----------------------------------------------------------------------
// | 登录
// +----------------------------------------------------------------------

class controller_admin_login extends controller_abstract {

	//后台登录首页
	public function actionIndex() {
		$redis_pwd_error_key = 'admin_error_pwd_count' . trim($this->_post('username'));
		$pwd_error_max = 5;

		//是否开启验证码
		$var = array();
		$var['error_msg'] = '';
		$var['is_open_captcha'] = $is_open_captcha = helper_tool::isOpenCaptcha("admin");

		if (isset($_POST['do']) && $_POST['do'] == 'login') {
			$cur_num = S($redis_pwd_error_key);
			$cur_num = $cur_num ? $cur_num : 0;
			if ($cur_num > $pwd_error_max - 1) {
				$var['error_msg'] = '您密码输入错误太多，账户已冻结，请联系管理员';
			} else {
				if ($is_open_captcha) {
					$verify = $this->_post('verify');
					if (empty($verify) || !helper_tool::checkValidate($verify,false)) {
						$var['error_msg'] = '验证码错误';
						$this->assign($var);
						$this->display();exit;
					}
				}

				//动态验证码
				$info = M('AdminUser')->field("uid,is_token,token")->where(array('username' => $this->_post('username')))->find();
				if(!empty($info['is_token']) && !empty($info['token'])){
					$tokenpwd = substr($this->_post('password'), -6);
					$_POST['password'] = substr($this->_post('password'), 0, -6);
					$rs = helper_google2fa::verify_key($info['token'], $tokenpwd);
					if(empty($rs)){
						$var['error_msg'] = '动态密码错误';
						$this->assign($var);
						$this->display();exit;
					}
				}

				//登录处理
				$rs = D('AdminUser')->chkUser($this->_post('username'), $this->_post('password'));
				if ($rs['status'] == 1) {
					S($redis_pwd_error_key, null);
					D('AdminUser')->setLogin($rs['user']['uid'], 1);
					header("Location: " . url('index/index'));
					exit;
				} elseif ($rs['status'] == -3) {
					$cur_num++;
					S($redis_pwd_error_key,$cur_num,86400);
					if ($cur_num == $pwd_error_max) {
						$var['error_msg'] = '您密码输入错误太多，账户已冻结，请联系管理员';
					} else {
						$var['error_msg'] = $rs['msg'] . '【今日再输错' . ($pwd_error_max - $cur_num ) . '次，账户将冻结】';
					}
				} else {
					$var['error_msg'] = $rs['msg'];
				}
			}
		}
		$this->assign($var);
		$this->display();
	}

	// 注销
	public function actionLogout(){
		D('AdminUser')->setLogout();
		header("Location:".url('login/index'));
	}

	//验证码
	public function actionImgcode(){
		helper_tool::imgValidate();
	}

}

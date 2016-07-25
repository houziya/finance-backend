<?php
/**
 * 用户后台管理
 */

class controller_admin_adminuser extends controller_admin_abstract{

	//查看管理员列表
	public function actionIndex(){
		$var = $this->_get();
		//搜索参数处理
		$map = $this->_search(array('AdminUser'));
		if(!empty($map['username'])) $map['username'] = array('like',"%{$map['username']}%");
		if(!empty($var['role_id'])){
			$ids = M('AdminRoleUser')->where(array('role_id' => $this->_get('role_id')))->getField("GROUP_CONCAT(uid) AS ids");
			if(empty($ids)) $ids=0;
			$map['uid'] = array('in',$ids);
		}

		$res = M('AdminUser')->where($map)->order('uid desc')->page();
		if($res['lists']){
			$t1 = 'admin_role_user';
			$t2 = 'admin_role';
			foreach($res['lists'] as $k=>$v){
				$map = array(
					'a.uid' => $v['uid'],
				);
				$row = M()->table("$t1 AS a")->field("b.name")->join("$t2 AS b ON a.role_id = b.id")->where($map)->find();
				$v['rolename'] = isset($row['name']) ? $row['name'] : '';
				$res['lists'][$k] = $v;
			}
		}

		$var = array_merge($var,$res);
		$this->assign($var);
		$this->setReUrl();
		$this->display();
	}

	//添加管理员
	public function actionAdd(){
		if (isset($_POST['do']) && $_POST['do'] == 'dosubmit'){
			$data = $this->_post('data');
			$data['realname'] = $data['realname'] ? $data['realname'] : $data['username'];
			$data['add_time'] = time();
			$data['ip'] = getIp();

			//处理新增系统管理员
			$res = D('AdminUser')->userAdd($data['username'], $data['password'], $data);
			if($res['status'] != 1) $this->error($res['msg']);

			//处理用户角色
			$data['uid'] = $res['data']['uid'];
			D('AdminRole')->setUserRole($data['uid'], $this->_post('role_id'));
			
			$this->setReUrl('admin_user_pid',$this->_post('role_id'));

			$this->savelog('添加系统用户【'.$data['username'].'】');
			$this->assign('jumpUrl',$this->getReUrl());
			$this->success('系统用户添加成功');
		};

		$role_id = $this->getReUrl('admin_user_pid');
		$var['roleSelect'] = D('AdminRole')->getSelect($role_id);
		$this->assign($var);
		$this->display();
	}

	//修改系统用户
	public function actionEdit(){
		//修改系统用户
		if (isset($_POST['do']) && $_POST['do'] == 'dosubmit'){			
			$data = $this->_post('data');
			if($this->auth['uid'] <> 1 && $data['uid'] == 1){
				$this->error('禁止修改最高管理员密码');
			}
			if(!empty($_POST['password'])){
				//更新用户密码
				$password = $this->_post("password");
				D('AdminUser')->setPwd($data['uid'],$password);
			}

			D('AdminUser')->where("uid='{$data['uid']}'")->save($data);

			//处理用户角色
			if(!empty($_POST['role_id'])){
				D('AdminRole')->setUserRole($data['uid'],$this->_post("role_id"));
			}

			$this->savelog('修改系统用户【uid:'.$data['uid'].'】');
			$this->success('系统用户修改成功', $this->getReUrl());
		}

		//批量锁定处理
		if (isset($_POST['do']) && $_POST['do'] == 'lock'){
			if(empty($_POST['ids'])) $this->error('请选择待处理的用户');
			$ids = $this->_post("ids");
			foreach($ids as $uid){
				D('AdminUser')->userDelete($uid);
			}
			$ids = implode(',',$ids);
			$this->savelog("锁定系统用户【uid:{$ids}】");
			$this->success("系统用户锁定成功");
		}

		if (empty($_GET['uid'])) $this->error('参数错误');
		$uid = $_GET['uid'];

		//系统用户详情
		$var['info'] = D('AdminUser')->getInfo($uid);

		//获取用户权限角色
		$userinfo = D('AdminUser')->getAccess($uid);
		$role_id = $userinfo['role_id'] ? array_shift($userinfo['role_id']) : 0;
		$var['roleSelect'] = D('AdminRole')->getSelect($role_id);

		$this->assign($var);
		$this->display();
	}

	//删除系统用户
	public function actionDelete(){
		$ids = $this->_post("ids");
		if(empty($ids)) $this->error('请选择待删除的用户');
		
		foreach($ids as $uid){
			D('AdminUser')->userDelete($uid, true);
		}
		$ids = implode(',',$_POST['ids']);
		$this->savelog("删除系统用户【uid:{$ids}】");
		$this->success('系统用户删除成功');
	}

	//修改我的信息
	public function actionPublicEdit(){
		//修改系统用户
		if (isset($_POST['do']) && $_POST['do'] == 'dosubmit'){
			$data = $_POST['data'];
			$password = trim($_POST['password']);
			$uid = $this->auth['uid'];
			
			if(!empty($data['token']) && !empty($_POST['tokenpwd'])){
				$rs = helper_google2fa::verify_key($data['token'], $this->_post('tokenpwd'));
				if(empty($rs)) $this->error('动态密码错误');
				$data['is_token'] = 1;
			}else{
				unset($data['token']);
			}

			//更新用户信息
			D('AdminUser')->where("uid={$uid}")->save($data);
			
			if($password){
				//修改密码
				D('AdminUser')->setPwd($uid,$password);				
			}
			D('AdminUser')->setLogin($uid);
			$this->savelog('修改个人资料【uid:'.$uid.'】');
			$this->success('个人资料修改成功');
		}

		$var = $this->_get();
		$uid = $this->auth['uid'];

		//系统用户详情
		$var['info'] = D('AdminUser')->getInfo($uid);
//		$var['token'] = session('google_token');
//		if(empty($var['token'])){
			$var['token'] = helper_string::randString(16);
			$var['token'] = strtoupper($var['token']);
			session('google_token', strtoupper($var['token']));
//		}
		$var['key'] = authcode("otpauth://totp/{$var['info']['username']}?secret={$var['token']}&issuer=RenRenTou",'ENCODE');
		$this->assign($var);
		$this->display();
	}

	//ajax验证系统用户名是否可用
	public function actionAjaxchkusername(){
		$username = isset($_GET['username']) ? $_GET['username'] : '';
		if(empty($username)){
			exit('0');
		}

		$info = M('AdminUser')->where(array('username' => $username))->count();
		$info2 = M('user')->where(array('username' => $username))->count();
		if($info > 0 || $info2 > 0){
			exit('1');
		}else{
			exit('0');
		}
	}

	//ajax验证邮箱是否可用
	public function actionAjaxchkemail(){
		$email = isset($_GET['email']) ? $_GET['email'] : '';
		if(empty($email)){
			exit('0');
		}

		$info = M('AdminUser')->where(array('email' => $email))->count();
		if($info){
			exit('1');
		}else{
			exit('0');
		}
	}

	/**
	 * 取消用户登录限制（密码错误次数超过5次）
	 *
	 */
	public function actionCleanloginlimit()
	{
        $username = $this->_get('username');
	    $redis_pwd_error_key = 'admin_error_pwd_count' . trim($username);
		S($redis_pwd_error_key, null);
		$this->success('操作成功',$this->getReUrl());
	}
	
	/*
	 * google 
	 * @author liufei
	 */
	public function actionPublicGoogleAuthenticator(){
		$var = array();
		$var['key1'] = authcode(url('wap-app/googleauthenticatorios'),'ENCODE'); //ios
		$var['key2'] = authcode(url('wap-app/googleauthenticatorandroid'),'ENCODE'); //android
		$var['key3'] = authcode(url('wap-app/barcodescanner4android'),'ENCODE'); //android
		$this->assign($var);
		$this->display();
	}
	
	/*
	 * 二维码
	 * @author liufei
	 */
	public function actionPublicQrcode(){
		$key = $this->_get('key');
		$key = authcode($key, 'DECODE');
		if(empty($key))exit();
		$ecc = 'M'; // L-smallest, M, Q, H-best
		$size = 20; // 1-50 
		helper_qrcode::png($key, false, $ecc, $size, 2);
	}
	
}

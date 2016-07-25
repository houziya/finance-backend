<?php
// +----------------------------------------------------------------------
// | 后台用户模型
// +----------------------------------------------------------------------

class model_AdminUser extends model_abstruct {
	
	protected $tableName = 'admin_user';

	//获取登录管理员信息
	public function user($field = '') {
		$user = session('auth_user');
		return $field ? (isset($user[$field]) ? $user[$field] : '') : $user;
	}

	/*
	 * 设置管理员用户密码
	 * @param string $uid 用户ID
	 * @param string $newpwd 新密码
	 * @param string $oldpwd 旧密码（为空不验证旧密码）
	 * @return return
	 */
	public function setPwd($uid, $newpwd, $oldpwd = '') {
		if (!is_numeric($uid)) return false;

		//验证原密码是否正确
		if ($oldpwd) {
			$pwd = helper_tool::pwdEncode($oldpwd);
			$result = $this->find("uid='$uid' AND password='$pwd'")->find();
			if (!$result)  return false;
		}
		$pwd = helper_tool::pwdEncode($newpwd);
		//修改系统用户密码
		$data = array();
		$data['password'] = $pwd;
		$data['reset_pwd'] = 0;
		$this->where("uid='$uid'")->save($data);
		return true;
	}
	
	/*
	 * 添加管理员
	 * @param string $username 用户名
	 * @param string $password 密码
	 * @param array $params 其他参数
	 * @return return
	 */
	public function userAdd($username = '', $password = '', $params = array()){
		if(empty($username) || empty($password)){
			return array('status' => -1, 'msg' => '用户名和密码不能为空');
		}
		$num = $this->where(array('username' => $username))->count();
		$num2 = M('user')->where(array('username' => $username))->count();
		if($num > 0 || $num2 > 0) return array('status' => -2, 'msg' => '用户名已存在');
		
		$data = $data2 = array();
		$data['username'] = $data2['username'] = $username;
		$data['password'] = $data2['password'] = strlen($password)==32 ? $password : helper_tool::pwdEncode($password);
		$data['regist_time'] = time();
		$data['regist_ip'] = getIp();
		$data['mobile'] = null;
		$data['email'] = null;
		$data['is_admin'] = 1;
		$data['uid'] = $data2['uid'] = M('user')->add($data);
		if(empty($data['uid'])) return array('status' => -3, 'msg' => '用户添加失败');
		
		$data2 = array_merge($params, $data2);		
		$this->add($data2);
		return array('status' => 1, 'msg' => '用户添加成功', 'data' => $data2);
	}

	//获取管理员详细信息  $pri是否同时获取用户角色和权限
	public function getInfo($uid, $pri = false) {
		$map = $access = array();
		if (is_numeric($uid)) {
			$map['uid'] = $uid;
		} elseif (is_array($uid) && $uid) {
			$map = $uid;
		} else {
			return array();
		}
		$info = $this->where($map)->find();
		if ($info && $pri) {
			$access = $this->getAccess($map['uid']);
			$info = array_merge($info, $access);
		}
		return $info;
	}

	//获取用户角色和权限
	//$type:null获取角色和菜单，$type:role获取角色，$type:menu获取菜单
	public function getAccess($uid, $type = '') {
		if (empty($uid)) return array();
		//获取所属角色
		$map = $info = array();
		$map['uid'] = $uid;
		$rows = D('AdminRoleUser')->where($map)->field('role_id')->findAll();
		if ($uid == 1) $info['isadmin'] = true;
		if ($rows) {
			foreach ($rows as $v) {
				//if ($v['role_id'] == 1) $info['isadmin'] = true;
				$info['role_id'][$v['role_id']] = $v['role_id'];
			}
		}

		//获取所在角色的权限
		if (!empty($info['role_id'])) {
			$map = array();
			$rows = D('AdminRoleMenu')->where("role_id IN(".implode(',',$info['role_id']).")")->field('menu_id')->findAll();
			if($rows){
				foreach ($rows as $v) {
					$info['menu_id'][$v['menu_id']] = $v['menu_id'];
				}
			}
		}
		if ($type == 'role') {
			$info = isset($info['role_id']) ? $info['role_id'] : array();
		} elseif ($type == 'menu') {
			$info = isset($info['menu_id']) ? $info['menu_id'] : array();
		}
		if(empty($info['isadmin'])) $info['isadmin'] = false;
		return $info;
	}

	//获取用户菜单列表
	public function getUserMenu($uid) {
		if (empty($uid)) return array();
		$priv = $this->getAccess($uid);
		$rows = D('AdminMenu')->getNodes();
		foreach ($rows as $v) {
			if ($priv['isadmin'] || in_array($v['id'], $priv['menu_id'])) {
				if ($v['is_show'] == 1) $menus[] = $v; //隐藏的菜单不显示
			}
		}
		$tree = new helper_tree($menus);
		$menus = $tree->getChildTree(0);
		unset($priv, $rows);
		return $menus;
	}

	//删除系统用户
	public function userDelete($uid, $clear = false) {
		if (!is_numeric($uid) || $uid == 1) return false;
		$map = "uid = '{$uid}'";
		if ($clear) {
			//彻底物理删除会员表信息
			$this->where($map)->delete();

			//用户权限表
			M('AdminRoleUser')->where($map)->delete();
			
			//用户收藏表
			M('AdminUserMenu')->where($map)->delete();

		} else {
			$this->where($map)->data(array('status' => 2))->save();
		}
		return true;
	}

	//系统用户检测
	public function chkUser($uname, $pwd) {
		$map = $res = array();
		$res['user'] = array();
		if (empty($uname) || empty($pwd)) {
			$res['status'] = -1;
			$res['msg'] = '帐号和密码不能为空';
		} else {
			$pwd = trim($pwd);
			$map['username'] = trim($uname);
			if(strlen($pwd) <> 32)  $pwd = helper_tool::pwdEncode($pwd);
			//$map['password'] = $pwd;
			$info = $this->where($map)->find();
			$res['user'] = $info;
			if (empty($info)) {
			    //用户不存在
				$res['status'] = 0;
				$res['msg'] = '用户不存在';
			} elseif($info['password'] != $pwd){
			    //密码错误
                $res['status'] = -3;
				$res['msg'] = '密码错误';
			}else {
				if ($info['status'] == 1) {
					$res['status'] = 1;
					$res['msg'] = '登录成功';
				} elseif ($info['status'] == 2) {
					$res['status'] = -2;
					$res['msg'] = '帐号已被删除';
				}
			}
		}
		return $res;
	}

	/**
	 * 后台用户登录
	 * @param int $uid 用户uid
	 * @param int $log 是否加入登录日志 0否 1后台 2web 3wap 4ios 5android
	 */
	public function setLogin($uid, $log=0) {
		$user = $this->getInfo($uid, true);
		if (empty($user)) return 0;
		// 登陆信息正确  开始访问授权
		session('auth_user', $user);

		// 如果是最高管理员则开启最高权限
		if ($user['uid'] == 1) {
			session('auth_super',1);
		}

		//缓存访问权限
		helper_rbac::saveAccessList($user['uid']);
		if (!empty($log) && is_numeric($log)) {
			//添加到登陆记录表
			model_login::loginAdd($uid, $log, 1);
		}
		return 1;
	}

	/**
	 * 检查用户是否是超级管理员
	 * @param int $uid 用户ID
	 * @return bool
	 */
    public function checkUserSuper($uid){
		$user = $this->getInfo($uid, true);
		if (empty($user)) return 0;
		// 如果是最高管理员则开启最高权限
		if (in_array(1, $user['role_id']) || $user['uid'] == 1) {
			return 1;
		}else{
			return 0;
		}
	}

	//系统用户注销
	public function setLogout() {
		session('auth_user',null);
		session('auth_super',null);
	}

}

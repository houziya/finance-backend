<?php

/**
 * 菜单模型
 */
class model_login extends model_abstruct {

	protected $tableName = 'login';
	
	//登录类型
	static public $type_arr = array(
		1 => array('id' => 1, 'name' => '后台', 'style' => ''),
		2 => array('id' => 2, 'name' => 'web', 'style' => ''),
		3 => array('id' => 3, 'name' => 'wap', 'style' => ''),
		4 => array('id' => 4, 'name' => 'ios', 'style' => ''),
		5 => array('id' => 5, 'name' => 'android', 'style' => ''),
	);
	
	//管理员类型
	static public $is_admin_arr = array(
		0 => array('id' => 0, 'name' => '否', 'style' => ''),
		1 => array('id' => 1, 'name' => '是', 'style' => ''),
	);
	
	/*
	 * 添加登录日志
	 * @param string $uid 用户UID
	 * @param string $type 登录类型
	 * @param string $is_admin 是否管理员 0否 1是
	 * @return bool
	 */
	static public function loginAdd($uid, $type = 0, $is_admin = 0){
            
		if($is_admin == 1){
			$username = M('AdminUser')->where(array('uid' => $uid))->getField('username');
		}else{
			$username = model_user::getInfo($uid,'username');
		}		
		if(empty($uid) || empty($username) || empty($type)) return false;
		$data = array();
		$data['type'] = $type;
		$data['uid'] = $uid;
		$data['username'] = $username;
		$data['add_time'] = time();
		$data['is_admin'] = $is_admin;
		$data['ip'] = getIp();
		$areainfo = D('area')->ip2area($data['ip']);
		$data['address'] = $areainfo ? $areainfo['address'] : '';
		M('login')->data($data)->add();
		//更新最后一次登录时间
		M('user')->where(array('uid' => $uid))->save(array('last_login_ip' => $data['ip'],'last_login_time' => $data['add_time']));
               
		D('user')->getInfo($uid, '', true);
	}
	
}

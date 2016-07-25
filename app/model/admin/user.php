<?php
// +----------------------------------------------------------------------
// | 用户模型
// +----------------------------------------------------------------------

class model_admin_user extends model_abstruct {
	
	protected $tableName = 'user';
	
	/**
	 * 检查管理员权限并输出手机号 
	 * @param int $uid 用户UID
	 * @param int $admin_uid 管理员UID
	 * @param bool $is_arr 是否输出数组
	 * @return string
	 * @author: liufei
	 */
	static public function lookMobile($uid, $admin_uid, $is_arr = false){
		return self::_lookInfo($uid, $admin_uid, $is_arr, $type = 'mobile');
	}
	
	/**
	 * 检查管理员权限并输出邮箱 
	 * @param int $uid 用户UID
	 * @param int $admin_uid 管理员UID
	 * @param bool $is_arr 是否输出数组
	 * @return string
	 * @author: liufei
	 */
	static public function lookEmail($uid, $admin_uid, $is_arr = false){
		return self::_lookInfo($uid, $admin_uid, $is_arr, $type = 'email');
	}
	
	/**
	 * 检查管理员权限并输出身份证号
	 * @param int $uid 用户UID
	 * @param int $admin_uid 管理员UID
	 * @param bool $is_arr 是否输出数组
	 * @return string
	 * @author: liufei
	 */
	static public function lookIdcard($uid, $admin_uid, $is_arr = false){
		return self::_lookInfo($uid, $admin_uid, $is_arr, $type = 'idcard');
	}
	
	/**
	 * 检查管理员权限并输出手机号 
	 * @param int $uid 用户UID
	 * @param int $admin_uid 管理员UID
	 * @param bool $is_arr 是否输出数组
	 * @param string $type 查看类型
	 * @return string
	 * @author: liufei
	 */
	static public function _lookInfo($uid, $admin_uid, $is_arr = false, $type = ''){
		if(empty($admin_uid)){
			$auth = session('auth_user');
			$admin_uid = $auth['uid'];
		}
		if(empty($type)) $type = 'mobile';
		$key = "model_admin_user_look{$type}_{$admin_uid}";
		$access_id = S($key);
		if(empty($access_id) || true){
			$access_id = helper_rbac::access2id($admin_uid,'admin','user',"look{$type}");
			S($key, $access_id, 3600);
		}
		
		//这里为根据字段获取相关内容
		$userinfo = model_user::getInfo($uid);
		if($type == 'idcard'){
			$info = M('user_body')->where("uid = '{$uid}'")->getField('u_body_num');
			$userinfo['u_body_num'] = $info;
		}else{
			$info = $userinfo[$type];
		}
		if(empty($info)){
			return $is_arr ? array() : '';
		}

		$str = '';
		$arr = array();
		if($access_id){
			//有权限显示的内容
			//$str = "<span id='look{$type}_{$uid}'>{$info}</span>";
			if(empty($info)){
				$str = '';
			}else{
				$str = $info;
			}
			$arr = $userinfo;
		}else{
			if(empty($info)){
				$str = '';
			}else{
				//无权限显示的内容
				if($type == 'mobile'){
					$info2 = substr_replace($info,'****',3,-4);
				}elseif($type == 'idcard'){
					$info2 = substr_replace($info,'********',6,-4);
				}else{
					$info2 = substr_replace($info,'*****',0,5);
				}
				//$str = "<span id='look{$type}_{$uid}' style='cursor:pointer' onclick='accessUserInfo({$uid},'{$type}')'>{$info2}</span>";
				$str = $info2;
			}
		}
		return $is_arr ? $arr : $str;
	}
	
}

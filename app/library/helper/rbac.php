<?php
/**
  +------------------------------------------------------------------------------
 * 基于角色和职位的数据库方式验证类
  +------------------------------------------------------------------------------
 */

/*
  配置文件增加设置
  'auth_open' => true,				// 开启项目权限检查
  'auth_type' => 1,					// 认证类型 1 登录认证 2 实时认证
  'auth_user' => 'auth_user',			// 用户认证标记
  'auth_super' => 'auth_super',	// 最高管理员认证标记
  'auth_gateway' => 'login/index',	// 认证网关
  'auth_not_m' => '',					// 无需认证模块
  'auth_not_c' => 'public',			// 无需认证控制器
  'auth_not_a' => 'public',			// 无需认证的操作
 */

class helper_rbac {

	protected $tables; // 待操作的用户表
	protected $node; //节点模型

	// 检查当前操作是否需要认证
	// return true:需要认证  false:无需认证
	static public function checkAccess() {
		$res = true; //默认需要认证
		if (C('auth_open')) {
			if(MODULE_NAME == 'admin' && CONTROLLER_NAME == 'index' && ACTION_NAME == 'index'){
				$res = false; //无需认证
			}			
			$_not = self::getNotAccess(C('auth_not_m'),C('auth_not_c'),C('auth_not_a'));
			//检查是否需要认证
			if (in_array(1, $_not)) {
				$res = false; //无需认证
			}
			unset($_not);
		}
		return $res;
	}

	//得到无需认证的模块、控制器和操作的状态
	static protected function getNotAccess($m='',$c='',$a=''){
		$urlnames = array('m'=>MODULE_NAME, 'c'=>CONTROLLER_NAME, 'a'=>ACTION_NAME);
		$nots = array('m'=>$m,'c'=>$c,'a'=>$a);

		//处理mvc无需认证的模块
		foreach($nots as $k=>$v){
			$urlname = $urlnames[$k];
			if($v){
				$arr = explode(',', $v);
				foreach($arr as $v2){
					$res = '';
					$len1 = strlen($v2);
					$len2 = strlen($urlname);
					if($len1>$len2){
						$res = 0; //需要认证
					}else{
						if($v2==substr($urlname, 0, $len1)){
							$nots[$k] = 1; //无需认证
							break;
						}else{
							$res = 0; //需要认证
						}
					}
					$nots[$k] = $res;
				}
			}else{
				$nots[$k] = 0; //需要认证
			}
		}
		unset($urlnames,$urlname,$arr,$len1,$len2,$v,$v2);
		return $nots;
	}

	//用于检测用户权限的方法,并保存到Session中
	static public function saveAccessList($uid=null) {
		$auth = session('auth_user');
		if (null === $uid) $uid = $auth['uid'];
		session('auth_rabc_access_list',self::getAccessList($uid));
		return;
	}

	// 取得当前认证号的所有权限列表
	static public function getAccessList($uid=null) {
		if (!is_numeric($uid)) return array();
		$access = D('AdminUser')->getAccess($uid);
		$access = (isset($access['menu_id'])&&$access['menu_id']) ? $access['menu_id'] : array();
		return $access;
	}

	// 权限认证的过滤器方法
	static public function AccessDecision() {

		//当前操作不需要认证 直接通过
		if (!self::checkAccess()) return true;

		//管理员不需要认证 直接通过
		if (session('auth_super')) return true;

		//存在认证识别号，则进行进一步的访问决策
		$code = MODULE_NAME . '_' . CONTROLLER_NAME . '_' . ACTION_NAME; //权限标识符
		$code = strtolower($code);
		$codeGuid = md5($code);

		$pass = session('auth_rbac_pass_list');
		if (C('auth_type') == 2) {
			//加强验证和即时验证模式 后台权限修改即时生效
			$auth = session('auth_user');
			$accessList = self::getAccessList($auth['uid']);
		} else {
			//如果当前操作已经认证过，无需再次认证			
			if ($pass[$codeGuid] == true) {
				return true;
			}
			//登录验证模式，比较登录后保存的权限访问列表
			$accessList = session('auth_rabc_access_list');
		}

		// 开始检查权限
		$menu = D('AdminMenu');
		$id = (int)$menu->code2id($code);
		if (in_array($id, $accessList)) {
			// 有权限操作 直接返回
			$pass[$codeGuid] = true;
			session('auth_rbac_pass_list',$pass);
			return true;
		}
		return false;
	}
	
	/**
	 * 根据mvc获取某个管理员的权限ID
	 * @param int $uid 管理员ID，不填默认为当前登录管理员
	 * @param int $m 
	 * @param int $c 
	 * @param int $a 
	 * @return int
	 * @author: liufei
	 */
	static public function access2id($uid='', $m = '', $c = '', $a = ''){		
		if(empty($uid)){
			$auth = session('auth_user');
			$uid = $auth['uid'];
		}
		if(empty($m)) $m = MODULE_NAME;
		if(empty($c)) $c = CONTROLLER_NAME;
		if(empty($a)) $a = ACTION_NAME;
                

		//标识符转权限ID
		$code = strtolower($m . '_' . $c . '_' . $a);
		if (session('auth_super')){
			//超管
			$id = (int)D('AdminMenu')->code2id($code);
			return $id;
		}else{
			//普通管理员
			//获取管理员组全部权限ID
			$accessList = self::getAccessList($uid);
			$id = (int)D('AdminMenu')->code2id($code);
			return isset($accessList[$id]) ? $id : 0;
		}
	}
	

	// 清除权限缓存
	static public function delAccessCache() {
		
	}

	

}

?>
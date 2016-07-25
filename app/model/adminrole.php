<?php
// 角色模型
class model_AdminRole extends model_abstruct{
	
	protected $tableName = 'admin_role';

	protected $_tmp = array(); //临时数据
	protected $_lists = array(); //全部角色
	protected $cachename = 'model_adminrole_list'; //角色缓存名称

	//删除角色
	public function roleDelete($id){
		$count = M('AdminRoleUser')->where("role_id='$id'")->count();
		if ($count > 0) return array('status' => 0, 'msg' => '请先修改角色下面管理员为其他角色');
		$res = M('AdminRole')->where("id='$id'")->delete();
		if ($res) {
			//删除角色菜单
			M('AdminRoleMenu')->where("role_id='{$id}'")->delete();
		}
		return array('status' => 1);
	}

	//设置用户所属角色
	public function setUserRole($uid,$role_id){
		if(!is_numeric($uid) || !is_numeric($role_id)) return false;
		$data = array();
		$data['uid'] = $uid;
		$data['role_id'] = $role_id;
		M('AdminRoleUser')->where("uid='$uid'")->delete();
		M('AdminRoleUser')->data($data)->add();
		return true;
	}

	//设置角色菜单
	public function setRoleMenu($role_id,$menu_ids){
		if(empty($role_id)) return false;
		M('AdminRoleMenu')->where("role_id='$role_id'")->delete();
		if($menu_ids){
			if(!is_array($menu_ids)) $menu_ids = explode(',', $menu_ids) ;
			foreach($menu_ids as $v){
				$data = array();
				$data['role_id'] = $role_id;
				$data['menu_id'] = $v;
				M('AdminRoleMenu')->data($data)->add();
			}
		}
		return true;
	}

	//得到角色菜单
	public function getRoleMenu($role_id,$type='string'){
		if(empty($role_id)) return array();
		$rows = M('AdminRoleMenu')->where("role_id='$role_id'")->findAll();
		$ids = array();
		if($rows){
			foreach($rows as $row){
				$ids[] = $row['menu_id'];
			}
		}
		return $type=='string' ? implode(',',$ids) : $ids;
	}

	//得到下拉框
	public function getSelect($ids='', $rule=''){
		$lists = $this->getList();
		$str = helper_form::select($ids, $lists, 'name="role_id" id="role_id"');
		return $str;
	}

	//得到角色详情
	public function getInfo($role_id,$field=''){
		if(!is_numeric($role_id)) return ;
		$row = $this->where("id='$role_id'")->find();
		$row['menu_ids'] = $this->getRoleMenu($row['id'],'array');
		return $field ? $row[$field] : $row;
	}

	/**
	 * 得到角色下的所有管理员
	 * @param int $role_id 角色id
	 */
	public function getUserList($role_id){
		$ids = M('AdminRoleUser')->where("role_id='$role_id'")->getField('GROUP_CONCAT(uid)');
		$lists = array();
		if($ids)  $lists = M('AdminUser')->where("uid in($ids)")->findAll();
		return $lists;
	}

	/**
	 * 得到全部角色缓存列表
	 * @param bool $delcache 是否删除缓存
	 * @return return
	 */
	public function getList($delcache=false) {
		$this->_lists = S($this->cachename);
		if (empty($this->_lists) || $delcache) {
			$rows = $this->order("sort asc,id asc")->findAll();
			foreach ($rows as $k=>$v) {
				$this->_lists[$v['id']] = $v;
			}
			S($this->cachename, $this->_lists, 3600 * 24);
			unset($rows);
		}
		return $this->_lists;
	}

}
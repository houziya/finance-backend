<?php

/**
 * admin_user_menu模型
 */
class model_AdminUserMenu extends model_abstruct {
	
	protected $tableName = 'admin_user_menu';
	
	/**
	 * 得到我收藏的菜单
	 * @param int $user_id 用户id
	 * @return array
	 */
	public function getMenuList($uid){
		if(empty($uid)) return array();
		$rows = $this->where("uid = '$uid'")->limit(10)->order("id asc")->findAll();
		return $rows ? $rows : array();
	}
	
}

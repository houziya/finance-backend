<?php
// +----------------------------------------------------------------------
// | 后台首页
// +----------------------------------------------------------------------

class controller_admin_index extends controller_admin_abstract {
	
	//后台框架首页
	public function actionIndex() {
		//获取我收藏的菜单
		$var['my_menus'] = $my_menus = D('AdminUserMenu')->getMenuList($this->auth['uid']);
		$M = D('AdminUser');
		$var['menus'] = $M->getUserMenu($this->auth['uid']);
		$menu_id = 0;
		if(!empty($var['menus'])){
			foreach ($var['menus'] as $k => $v) {
				$menu_id = $k;
				break;
			}
		}
		$var['menu_id'] = isset($_GET['menu_id']) && $var['menus'][$_GET['menu_id']] ? $_GET['menu_id'] : $menu_id;
		$var['user'] = $this->auth;
		$var['server_name'] = $_SERVER['SERVER_NAME'];
		$var['body_style'] = ' class="indexmain"';
		$this->assign($var);
		$this->display();
	}

	//后台详情页
	public function actionPublicMain() {
		
		$var                 =  array();
		$role_id 		     =  $this->auth['role_id'];
		$role_id             =  array_shift($role_id);
		$var['role_name']    =  M('AdminRole')->where("id = {$role_id}")->getField('name');
		$var['lastlogin']    =  M('login')->where("uid = ".$this->auth['uid'])->order("id desc")->find();
		$var['lastlogin']['addtime_tips'] = date('Y-m-d H:i:s',$var['lastlogin']['add_time']);
		
		$var['websiteInfo']  =  D('admin/adminuser')->websiteInformationProcessed();
	
		$this->assign($var);
		$this->display();
	}

	//获取默认菜单
	protected function getDefultMenu() {
		return array();
	}

	//得到当前位置
	public function actionAjaxCurrentPos(){
		$menu_id = $this->_get('menu_id');
		$menu = D('AdminMenu');
		$node = $menu->getNode($menu_id);
		$rows = $menu->getParentList($menu_id);
		$rows[$node['id']] = $node;
		$current = '';
		foreach($rows as $v){
			$current .= $v['name'].' &gt ';
		}
		echo $current;exit;
	}
	
	//收藏当前页面
	public function actionAjaxaddpanel(){
		$menu_id = $this->_post('menu_id');
		$url = $this->_post('url');
		if(empty($menu_id) || empty($url)) exit();
		$name = '';
		if(preg_match_all('|http://[^/]+/(.*)$|is', $url, $match)){
			$arr = explode('/',$match[1][0]);
			$menu_id = D('AdminMenu')->code2id("admin_{$arr[0]}_{$arr[1]}");
			$menu_info = D('AdminMenu')->getNode($menu_id);
			$name = !empty($menu_info['name']) ? $menu_info['name'] : '菜单'.$menu_id;
		}else{
			$name = D('AdminMenu')->id2name($menu_id);
		}
		
		$data = array(
			'uid' => $this->auth['uid'],
			'name' => $name,
			'menu_id' => $menu_id,
			'url' => $url,
			'add_time' => time(),
		);
		$id = D('AdminUserMenu')->data($data)->add();		
		echo '<span id="panel_'.$id.'"><a href="'.$url.'" target="right" onclick="paneladdclass(this);">'.$data['name'].'</a>  <a href="javascript:delete_panel('.$id.');" class="panel-delete"></a></span>';
		exit;
	}
	
	//删除收藏页面
	public function actionAjaxdeletepanel(){
		$id = $this->_post('id');
		if(empty($id)) exit("0");
		$uid = $this->auth['uid'];
		$map = array(
			'id' => $id,
			'uid' => $uid,
		);
		D('AdminUserMenu')->where($map)->delete();
		exit("1");		
	}
	
	//保持session
	public function actionPublicsessionlife(){
		if(!isset($_SESSION)) session_start();
		exit("1");
	}
	
	//删除全站缓存
	public function actionCacheClean(){
		$arr = array();
		
		//角色缓存
		$arr['model_adminrole_list']     = '角色缓存';		
		//菜单缓存
		$arr['model_adminmenu_list']     = '菜单缓存';
		
		
		$type = $this->_get('type');
		if($type == 'all'){
			$keys = $arr;
		}else{
			$keys = array();
			if(isset($arr[$type])){
				$keys[$type] = $arr[$type];
			}
		}
		
		if($keys){		
			//清除缓存
			$redis = cache::getInstance('redis');
			$pre = C('sys_cache_prefix');
			foreach($keys as $k => $v){
				$key = $k.'*';
				$rows = $redis->keys($key);
				if($rows){
					foreach($rows as $v2){
						$v2 = substr($v2, strlen($pre));
						$redis->rm($v2);
					}				
				}
			}
			$this->success('缓存删除成功');
		}

		$this->assign('lists',$arr);
		$this->display();
	}
	
}

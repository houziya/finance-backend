<?php

// +----------------------------------------------------------------------
// | 菜单权限管理
// +----------------------------------------------------------------------

class controller_admin_menu extends controller_admin_abstract {

	// 查看菜单列表
	public function actionIndex() {
		$lists = D('AdminMenu')->getChildList(0);
		foreach ($lists as $k => &$v) {
			$v['isshow_tips'] = $v['is_show'] ? '' : '<span class="gray4">[隐]</span>';
			$v['code'] = '';
			$v['code'] .= !empty($v['module']) ? $v['module'] . '-' : '';
			$v['code'] .= !empty($v['controller']) ? $v['controller'] . '-' : '';
			$v['code'] .= !empty($v['action']) ? $v['action'] : '';
			$v['code'] .= !empty($v['param']) ? '?' . $v['param'] : '';
			if ($v['code']) $v['code'] = '<span class="gray4">[' . $v['code'] . ']</span>';
		}
		$this->assign('lists',$lists);
		$this->display();
	}

	//添加菜单
	public function actionAdd() {
		$menu = D('AdminMenu');
		if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
			$data = $this->_post('data');			
			if ($data['pid'] != 0) {
				// 第4级菜单下面禁止添加子菜单
				$res = $menu->getParentList($data['pid']);
				if ($res && count($res) >= 3) {
					$this->error('错误！此菜单下面禁止添加子菜单！');
				}
			}
			$menu->data($data)->add();
			$menu->cacheDelete();
			$this->setReUrl('menu_add_pid', $data['pid']);
			$this->savelog('添加菜单【' . $data['name'] . '】');
			$this->success('菜单添加成功！');
		}

		$pid = $this->_get('pid') ? $this->_get('pid') : (int) $this->getReurl('menu_add_pid');
		$menuSelect = $menu->getSelect($pid);
		$this->assign('menuSelect',$menuSelect);
		$this->display();
	}

	//修改菜单
	public function actionEdit() {
		$menu = D('AdminMenu');
		if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
			$data = $this->_post('data');
			if($data['pid'] == $data['id']) $this->error('上级菜单不能为自己');
			if ($data['pid'] != 0) {
				// 第4级菜单下面禁止添加子菜单
				$res = $menu->getParentList($data['pid']);
				if ($res && count($res) >= 3) {
					$this->error('错误！此菜单下面禁止添加子菜单！');
				}
			}
			$menu->where(array('id' => $data['id']))->save($data);
			$menu->cacheDelete();
			$this->savelog('修改菜单【' . $data['name'] . '】');
			$this->success('菜单修改成功！', url('index'));
		}

		//排序处理
		if (isset($_POST['do']) && $_POST['do'] == 'sort') {
			$rows = $this->_post('sort');
			foreach ($rows as $k => $v) {
				$data = array();
				$data['sort'] = $v;
				$menu->where(array('id' => $k))->save($data);
			}
			$menu->cacheDelete();
			$this->savelog('批量更新菜单排序');
			$this->success('排序更新成功');
		}

		$var = $this->_get();
		$id = $this->_get('id');
		if (empty($id)) $this->error('参数错误');
		$var['info'] = $menu->getInfo($id);
		$var['menuSelect'] = $menu->getSelect($var['info']['pid']);
		$this->assign($var);
		$this->display();
	}

	//删除菜单
	public function actionDelete() {
		if (empty($_GET['id'])) $this->error('参数错误');
		$id = $this->_get('id');
		D('AdminMenu')->menuDelete($id);
		$this->savelog('删除菜单【id:' . $id . '】');
		$this->success('菜单删除成功！');
	}
	
	//检查菜单
	public function actionCheckMenu(){
		if(isset($_POST['do']) && $_POST['do'] == 'dosubmit'){
			$menus = $this->_post('menus');
			if(empty($menus)) $this->error("没有要加入的菜单");

			$menu = D('AdminMenu');
			//过滤掉四级菜单
			foreach($menus as $k => $v){
				$pid = (int)$v['pid'];
				if($pid == 0) continue;
				$name = $v['name'] ? $v['name'] : $k;
				$is_show = empty($v['is_show']) ? 0 : 1;
				if ($pid != 0) {
					// 第4级菜单下面禁止添加子菜单
					$res = $menu->getParentList($pid);
					if ($res && count($res) >= 3) {
						continue;
					}
				}
				$arr = explode('_', $k);
				$data = array();
				$data['pid'] = $pid;
				$data['name'] = $name;
				$data['module'] = $arr[0];
				$data['controller'] = $arr[1];
				$data['action'] = $arr[2];
				$data['is_show'] = $is_show;
				$menu->add($data);
			}
			$menu->cacheDelete();
			$this->success('菜单添加成功！');
		}
		
		$var = $methods = array();
		
		$path = dirname(__FILE__);
		$files = glob("{$path}/*.php");
		$not_c = explode(',',C('auth_not_c'));
		$not_c[] = 'cron';
		$not_a = explode(',',C('auth_not_a'));
		
		$not_file = array('abstract.php','login.php'); //不加入权限菜单的文件
		foreach($files as $file){
			$name = basename($file);
			if(in_array($name, $not_file)) continue;
			$name = substr($name, 0, -4);
			$class_name = "controller_admin_". strtolower($name);
			foreach($not_c as $v){
				if(strtolower(substr($name,0,strlen($v))) == $v) continue 2;
			}
			
			$rows = get_class_methods($class_name);
			if ($rows) {
				foreach ($rows as $v2) {
					if (substr($v2, 0, 6) <> 'action') continue;
					foreach ($not_a as $v3) {
						if (strtolower(substr($v2, 6, strlen($v3))) == $v3) continue 2;
					}
					$name2 = strtolower(substr($v2, 6));
					if($name2=="usersign") continue;
					$code_name = 'admin_' . strtolower($name).'_'.$name2;
					if($code_name == 'admin_index_index' || $code_name == 'admin_index_main') continue;
					$methods[$code_name] = array('class'=>$class_name,'method'=>$v2);
				}
			}
		}

		$lists = array();
		$menu = D('AdminMenu');
		$menu_lists = $menu->getChildList(0);
		foreach($methods as $k => $v){			
			$id = $menu->code2id($k);
			if(!empty($id)) continue;
			$v['menu_select'] = helper_form::select('',$menu_lists,'name="menus['.$k.'][pid]"','顶级菜单',"<option value='\$id'\$selected>\$spacer \$name</option>");
			$lists[$k] = $v;
		}
		
		$this->assign('lists',$lists);
		$this->display();
	}

}
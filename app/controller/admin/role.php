<?php
// +----------------------------------------------------------------------
// | 角色管理
// +----------------------------------------------------------------------

class controller_admin_role extends controller_admin_abstract{

	//查看角色列表
	public function actionIndex(){
		$res = D('AdminRole')->order('sort ASC,id ASC')->page();
		foreach($res['lists'] as $k=>$v){			
			$v['users'] = D('AdminRole')->getUserList($v['id']);
			$res['lists'][$k] = $v;
		}
		$this->assign($res);
		$this->setReUrl();
		$this->display();
	}

	//添加角色
	public function actionAdd(){
		if (isset($_POST['do']) && $_POST['do'] == 'dosubmit'){
			$data = $this->_post("data");
			D("AdminRole")->add($data);
			D("AdminRole")->getList(true);
			$this->savelog('添加角色【'.$data['name'].'】');
			$this->success('角色添加成功',$this->getReUrl());
		}
		$this->display();
	}

	//修改角色
	public function actionEdit(){
		if (isset($_POST['do']) && $_POST['do'] == 'dosubmit'){
			$data = $this->_post('data');
			D('AdminRole')->where("id='{$data['id']}'")->save($data);
			D('AdminRole')->getList(true);
			$this->savelog('修改角色【'.$data['name'].'】');
			$this->success('角色修改成功',$this->getReUrl());
		}

		//排序处理
		if (isset($_POST['do']) && $_POST['do'] == 'order'){
			$rows = $this->_post('order');
			foreach($rows as $k=>$v){
				M('AdminRole')->where("id='$k'")->save(array('sort' => $v));
			}
			$this->savelog("批量更新角色排序");
			$this->success('排序更新成功');
		}

		if(!is_numeric($_GET['id'])) $this->error('参数错误');
		$info = D('AdminRole')->getInfo($this->_get("id"));
		$this->assign('info',$info);
		$this->display();
	}

	//删除角色
	public function actionDelete(){		
		$id = $this->_get('id');
		if(!is_numeric($id)) $this->error('参数错误');
		$res = D('AdminRole')->roleDelete($id);
		if($res['status']==1){
			$this->savelog('删除角色【id:'.$id.'】');
			$this->success('角色删除成功');
		}else{
			$this->error($res['msg']);
		}
	}

	//角色权限设置
	public function actionRolepriv(){
		if (isset($_POST['do']) && $_POST['do'] == 'submit'){
			$menu_ids = $this->_post('menu_ids');
			$id = $this->_get('id');
			D('AdminRole')->setRoleMenu($id,$menu_ids);
			$this->savelog("设置角色操作权限【id:{$id}】");
			$this->assign('dialog','edit');
			$this->success('权限设置成功');
		}

		if(!is_numeric($_GET['id'])) $this->error('参数错误');
		$info = D('AdminRole')->getInfo($this->_get('id'));
		$menuSelect = D('AdminMenu')->getCheckbox($info['menu_ids']);
		$this->assign('info',$info);
		$this->assign('menuSelect',$menuSelect);
		$this->display();
	}

}
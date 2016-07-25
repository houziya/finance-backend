<?php
// +----------------------------------------------------------------------
// | 后台基类
// +----------------------------------------------------------------------

class controller_admin_abstract extends controller_abstract {
	
	protected $auth = array(); //后台登录用户信息

	/**
     * 构造函数
     */
    public function __construct() {
		parent::__construct();
		
		C('sys_tpl_ctl_error','admin@error');
		C('sys_tpl_ctl_success','admin@success');
		C('sys_global_source', 1);//后台来源
		$this->checkAccess(); //登录和权限检查
		$this->getTopNav(); //获取页面导航
        $this->assign('effectiveTime', time() + EFFECTIVE_TIME);
	}

    /**
	 * 后台登录验证
	 */
	protected function _auth() {
		$admin_info = $this->auth;
		if (empty($admin_info)) {
			$this->error('非法操作');
		}
		return $admin_info;
	}

	// 检查管理员是否登录 如没有登录直接跳转到登陆界面
	protected function checkAccess(){
		if(PHP_SAPI == 'cli'){
			//命令行执行 默认为admin权限
			$user = D('AdminUser')->getInfo(1);
			$this->auth = $user;
		}else{
			//登录检查
			if (!session('auth_user')) {
				echo "<script>window.parent.location.href='".url('admin-login/index')."'</script>";exit;
			}

			//检查是否需要强制修改管理员登录密码
			$this->auth = session('auth_user');
			$this->assign('auth', $this->auth);
			if(!empty($this->auth['reset_pwd']) && $this->auth['reset_pwd'] == 1 && CONTROLLER_NAME <> 'index' && !in_array(ACTION_NAME,array('publicedit','publicqrcode','publicgoogleauthenticator'))){
				header('location:'.url('adminuser/publicedit?type=resetpwd'));exit;
			}

			//系统用户权限检查
			if (!session('auth_super')) {
				$chk = helper_rbac::AccessDecision();
				$this->assign('auth_super', 0);
				if(!$chk) $this->error('您没有权限执行此操作！请与管理员联系！');
			}else{				
				$this->assign('auth_super', 1);
			}
		
		}

	}

	//根据menu_id参数获取页面顶部导航
	protected function getTopNav(){
		$Mmenu = D('AdminMenu');
		if(isset($_GET['menu_id']) && $_GET['menu_id'] > 0){
			$menu_id = $_GET['menu_id'];
		}else{
			$menu_id = $Mmenu->code2id(MODULE_NAME.'_'.CONTROLLER_NAME.'_'.ACTION_NAME);
		}

		//页面公共导航
		$node = $Mmenu->getNode($menu_id);
		$rows = array();
		if(isset($node['level']) && $node['level']==3){
			$rows[] = $node;
			$rows2 = $Mmenu->getChildTree($node['id']);
			if($rows2) $rows = array_merge($rows,$rows2);
		}elseif(isset($node['level']) && $node['level']==4){
			$node2 = $Mmenu->getNode($node['pid']);
			$rows[] = $node2;
			$rows2 = $Mmenu->getChildTree($node['pid']);
			if($rows2) $rows = array_merge($rows,$rows2);
		}

		$tpl = '';
		if($rows){
			$i=1;
			foreach($rows as $v){
				if($v['is_show']){
					$span = $i==1 ? "" : "<span>|</span>";
					$on = $v['id']!=$menu_id ? "" : " class='on'";
					$tpl .= "$span<a href='{$v['url']}'$on><em>{$v['name']}</em></a>";
					$i++;
				}
			}
		}
		$this->assign('topnav',$tpl);
		unset($tpl,$node2,$rows2,$rows);
	}

	// 取得操作成功后要返回的URL地址 默认返回当前模块的默认操作 可以在action控制器中重载
	protected function getReUrl($name='default') {
		return getReUrl($name);
	}

	// 设置当前浏览页面
	protected function setReUrl($name='default', $id=''){
		return setReUrl($name,$id);
	}

	// 记录管理员操作日志
	protected function savelog($remark=''){
		$data = array();
		$data['uid'] = $this->auth['uid'];
		$data['username'] = $this->auth['username'];
		$data['module'] = MODULE_NAME;
		$data['controller'] = CONTROLLER_NAME;
		$data['action'] = ACTION_NAME;
		$data['post'] = !empty($_POST) ? serialize($_POST) : '';
		$data['url'] = empty($_SERVER['REQUEST_URI']) ? '' : $_SERVER['REQUEST_URI'];
		$data['remark'] = $remark;
		$data['add_time'] = date('Y-m-d H:i:s');
		$data['ip'] = getIp();
		M('AdminUserLog')->data($data)->add();
		return ;
	}
	
	/*
	 * 删除记录的回收操作
	 * @author liurengang
	 * @date   2015.06.11
	 * @param string $table      所删除数据对应的表名
	 * @param string $table_id   所删除数据对应的自增ID
	 * @param string $info       所删除数据的数据内容
	 * @return array
	 */
	protected function saveRecycle( $table, $table_id, $info = array() ){
		
		if(empty($table) || empty($table_id) || empty($info)) return false;
		$data =  array(
			'table'     =>  $table,
			'table_id'  =>  $table_id,
			'data'      =>  serialize($info),
			'add_time'  =>  date('Y-m-d H:i:s'),
			'ip'        =>  getIp(),
		);
		if(M('recycle')->data($data)->add()) {
			return true;
		}
		
		return false;
	}

	
}

?>
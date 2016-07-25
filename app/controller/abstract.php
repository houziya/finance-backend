<?php
// +----------------------------------------------------------------------
// | 控制器基类
// +----------------------------------------------------------------------

class controller_abstract extends controller {

	//meta信息
	protected $_metaes = array();
	//静态资源版本号
	protected $_web_version = 1;
	//ssl跳转
	private $_ssl_config = array(
//		'www' => array(
//			'user' => '*',
//			'pay' => array('index', 'recharge'),
//		),
	);	
	protected $_url;
	
	public function __construct() {
		parent::__construct();

		header("Content-Type:text/html; charset=utf-8");
		header("Cache-control: no-cache");
        C('sys_global_source', 2);//web来源
		
		//判断ssl跳转
		$need_ssl = false;
	    if (isset($this->_ssl_config[MODULE_NAME][CONTROLLER_NAME])) {
            $c_action = $this->_ssl_config[MODULE_NAME][CONTROLLER_NAME];
	        if ($c_action == '*' || in_array(ACTION_NAME, $c_action)) {
                $need_ssl = true;
            }
	    }

	    if (empty($_SERVER["HTTP_X_FORWARDED_PROTO"]) && $need_ssl) {
	    	header('Location:https://'. $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
	    	exit;
	    }

	    if (!empty($_SERVER["HTTP_X_FORWARDED_PROTO"]) && !$need_ssl) {
	    	header('Location:http://'. $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
	    	exit;
	    }
		
		if ( get_magic_quotes_gpc() ) {
			if (!empty($_POST)) $_POST = stripslashesDeep($_POST);
			if (!empty($_GET)) $_GET = stripslashesDeep($_GET);
			if (!empty($_COOKIE)) $_COOKIE = stripslashesDeep($_COOKIE);
			if (!empty($_REQUEST)) $_REQUEST = stripslashesDeep($_REQUEST);
		}
        // 登录的用户信息
        $this->assign('user', model_user::getLoginUser() );
		$this->assign('sing_url', url("abstract/UserSign"));
		$this->_web_version = C('web_version');
		$this->assign(array('title'=>'','csses'=>'','jses'=>'','jscodes'=>'','morestrings'=>''));
		
		//初始化公共模版变量
		$this->_url = C('url');
		$this->_url['admin_tpl'] = $this->_url['img'] . '/s/admin'; //后台模版静态资源目录
		$this->_url['web_tpl'] = $this->_url['img2'] . '/s/v2'; //前台模版静态资源目录
		$this->_url['web_tpl_v3'] = $this->_url['img2'] . '/s/v3'; //前台模版静态资源目录
		$this->assign('url', $this->_url);
		$this->assign('appini',C());
        
        //是否是测试（代码逻辑自行根据该参数判断，用户在上线时候测试使用）
        if ($this->_request('_dotest')) {
            $this->assign('_dotest', 1);
        }
	}

	/**
	 * 向头部添加 meta 标签
	 *
	 * Example:
	 * <code>
	 * $meta = array(
	 *     array('name' => "publishid",'content' => "30,59,1"),
	 *     array('name' => 'stencil', 'content'=> 'PGLS000022')
	 * );
	 * $this->addMeta(array('name' => 'stencil', 'content'=> 'PGLS000022'));
	 * </code>
	 *
	 * @param array $metas			键值对
	 * @param boolean $multi		是否是二维数组
	 * @return void
	 */
	public function addMeta($metas, $multi = false) {
		if($multi) {
			$this->_metas = array_merge($this->_metas, $metas);
		}else{			
			$this->_metas[] = $metas;
		}		
		//加载meta
		$str = '';
		if ($this->_metas) {
			foreach ($this->_metas as $v) {
				$str .= '<meta ';
				foreach ($v as $k2 => $v2) {
					$str .= $k2 . '="' . addslashes($v2) . '"';
				}
				$str .= " />\r\n";
			}
		}
		$this->assign('morestrings', $str);
	}

	/**
	 * 设置页面 <title></title>
	 *
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title){
		$this->assign("title", $title);
	}
	
	/**
	 * 浏览器行为检查
	 * @param int $time 间隔时间
	 * @param string $name 行为名称
	 * @return bool 真为通过成功  假为通过失败
	 */
	public function refreshBrowser($time = 1,$name = ''){
		if(empty($name)) $name = 'default';
		if(empty($time) || !is_numeric($time)) $time = 1;
        $res = helper_tool::refreshBrowser($time,$name);
		if($res == false){
		    $this->error('您的速度太快了，请稍后再试！', '', $this->isAjax());
		}
	}
	
	/**
	 * 根据表单生成查询条件 进行列表过滤
	 * @param array $tabs 模型
	 * @param array $filter 额外合并的参数
	 */
	protected function _search($tabs = array(), $filter = array()) {
		if (isset($_POST['search']) && $_POST['search']) {
			header( 'Location:'.url(CONTROLLER_NAME.'/'.ACTION_NAME.'?'.http_build_query($_POST['search'])) );exit;
		}

		//生成查询条件
		if (empty($tabs)) {
			$tabs = array();
			$tabs[] = CONTROLLER_NAME;
		}else{
			if(is_string($tabs)){
				$tabs2 = array();
				$tabs2[] = $tabs;
				$tabs = $tabs2;
			}
		}
		$map = array();
		foreach($tabs as $name){
			$model = M($name);
			$fields = $model->getDbFields();
			foreach ( $fields as $val ) {
				if (isset( $_GET[$val] ) && $_GET[$val] != '') {
					$map[$val] = trim($_GET[$val]);
				}
			}
		}
		if (is_array($filter) && !empty( $filter )) {
			foreach ($filter as $k => $v){
				if ( isset( $map[$k] ) && $map[$k] != '' ) {
					$map[$k] = trim($_GET[$v]);
				}
			}
		}
		unset($map['module'],$map['controller'],$map['action']);
		return $map;
	}

    /**
     * 功能与 _search 一致，添加范围查询，字段名需以__min_或__max_开头
     * @param array $mods 模型
     * @param array $filter 额外合并的参数
     * @return array
     */
    protected function _search_more($mods = array(),$filter = array()) {
        if (isset($_POST['search']) && $_POST['search']) {
            header( 'Location:'.url(CONTROLLER_NAME.'/'.ACTION_NAME.'?'.http_build_query($_POST['search'])) );exit;
        }

        //生成查询条件
        if (empty($mods)) {
            $mods = array();
            $mods[] = CONTROLLER_NAME;
        }else{
            if(is_string($mods)){
                $mods2 = array();
                $mods2[] = $mods;
                $mods = $mods2;
            }
        }
        $fields = array();
        foreach($mods as $name){
            $model = M($name);
            $tmp_fields = $model->getDbFields();
            $fields = array_merge($fields,$tmp_fields);
        }
        $map = $_GET;
        $map_keys = array_keys($map);
        foreach($map_keys as $map_key) {
            if (strpos($map_key,'__min_') === 0 || strpos($map_key,'__max_') === 0) {
                $new_key = substr($map_key,6);
                if (strpos($map_key,'__min_') === 0) {
                    $map[$new_key][] = array('egt',$map[$map_key]);
                } elseif (strpos($map_key,'__max_') === 0) {
                    $map[$new_key][] = array('elt',$map[$map_key]);
                }
                unset($map[$map_key]);
                $map_key = $new_key;
            }
            if (!in_array($map_key,$fields)) {
                unset($map[$map_key]);
            }
        }
        if (is_array($filter) && !empty( $filter )) {
            foreach ($filter as $k => $v){
                if ( isset( $map[$k] ) && $map[$k] != '' ) {
                    $map[$k] = trim($_GET[$v]);
                }
            }
        }
        unset($map['module'],$map['controller'],$map['action']);
        return $map;
    }
    
      /**
     *  制定模糊搜索的key
     *   Jimmy Fu 2015-11-24
     *  @param  array   $map  查询条件
     *  @param  array   $keys 关键数组
     * 
     */ 
    protected function likeSearchKey($map,$key){
        
        foreach($map as $k => &$v){
            if(in_array($k,$key)){
                $v = array('like','%'.$v.'%'); 
            }    
        }
        return $map;
        
    }

	public function actionUserSign() //用户签到
	{
       $user = model_user::getLoginUser();
	   $data = array();
	   if (!$user) 
	   {
		   $data['status'] = 0;
		   $data['info'] = "还未登陆，签到失败";
		   $this->ajaxReturn($data);
		   exit();

	   }
	   $uid = $user['uid']+0;
	   $fg = D('Usersignlog')->sign($uid);
	  // var_dump($fg);die;
	   if($fg===false) 
	   {
           $data['status'] = 0;
		   $data['info'] = "签到失败";
		   $this->ajaxReturn($data);
		   exit();
	   } 
	   if($fg==1)
	   {
               $score = D('usersignlog')->getSignScore($uid);
               if(!in_array($score['sign_day'], array(7,14,30))) $score['sign_day'] = '';
               $data['score'] = $score['score'];
               $data['sign_day'] = $score['sign_day'];
              $data['status'] = 1;
		      $data['info'] = "签到成功";
			   $this->ajaxReturn($data);
		   exit();
	   }

	   if($fg==-1)
	   {
            $data['status'] = 0;
		     $data['info'] = "已签到";
			  $this->ajaxReturn($data);
		   exit();
	   }
	   if($fg==-2)
	   { 
            $data['status'] = 0;
		     $data['info'] = "签到失败";
			  $this->ajaxReturn($data);
		   exit();
	   }
	   
	  

	}

	
	
}

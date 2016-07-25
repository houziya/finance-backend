<?php
// +----------------------------------------------------------------------
// | 回访
// +----------------------------------------------------------------------
class controller_admin_admincallback extends controller_admin_abstract {
	
	//会员回访    update 2015.05.26  liurengang
	public function actionUserCallback(){
		
		$var  =  $this->_get();
		// 搜索参数处理
		$map  =  $this->_search ( array ('adminCallback') );
		
		//搜索时间的处理
		if (! empty ( $_GET ['start_time'] ) && ! empty ( $_GET ['end_time'] )) {
			$map ['add_time']  =  array (
				array ('egt',strtotime ( $this->_get ( 'start_time' ) )),
				array ('elt',strtotime ( $this->_get ( 'end_time' )) + 86400)
			);
		}
		//项目回访 类型
		$map['type_id']        =  array('eq',1);
		
		$lists                 =  M ( 'adminCallback' )->where ( $map )->order ( 'add_time desc' )->page ();
		
		if ( !empty($lists['lists']) ) {
			foreach($lists['lists'] as $key=>&$val) {
				if ( empty($val['username']) ) {
					$username          =  model_user::getInfo($val['uid'],'username');
					if(empty($username))  $username['username']  =  '';
					$val['username']   =  $username['username'];
				}
				
				$uname                 =  model_user::getInfo($val['table_id'],'username');
				
				if(empty($uname))      $uname  =  '';
				$val['uname']          =  $uname;
				
				$arr                   =  model_admincallback::$callback_type_arr[$val['type_id']];
				$val['type_tips']      =  $arr['name'];
				
				$arr                   =  model_admincallback::$callback_status_arr[$val['status']];
				$val['status_tips']    =  '<span>'.$arr['name'].'</span>';
				
				unset($val);				
			}
		}
		$var                      =  array_merge ( $var, $lists );
		// 处理时间区间段input表单 
		$var ['input_starttime']  =  helper_form::date ( 'search[start_time]', $this->_get ( 'start_time' ) );
		$var ['input_endtime']    =  helper_form::date ( 'search[end_time]', $this->_get ( 'end_time' ) );
		
		//回访状态
		$var['status_select']    =  helper_form::select($this->_get('status'),model_admincallback::$callback_status_arr,'name="search[status]"','全部');
		
		$this->assign($var);
		$this->display();
	}
	
	//招商回访      update 2015.05.26  liurengang
	public function actionMerchantsCallback($id){
		
		$var  =  $this->_get();
		// 搜索参数处理
		$map  =  $this->_search ( array ('adminCallback') );
		
		//搜索时间的处理
		if (! empty ( $_GET ['start_time'] ) && ! empty ( $_GET ['end_time'] )) {
			$map ['add_time']  =  array (
				array ('egt',strtotime ( $this->_get ( 'start_time' ) )),
				array ('elt',strtotime ( $this->_get ( 'end_time' )) + 86400 )
			);
		}
		//项目回访 类型
		$map['type_id']        =  array('eq',2);
		
		$lists                 =  M ( 'adminCallback' )->where ( $map )->order ( 'add_time desc' )->page ();
		
		if ( !empty($lists['lists']) ) {
			foreach($lists['lists'] as $key=>&$val) {
				if ( empty($val['username']) ) {
					$username          =  model_user::getInfo($val['uid'],'username');
					if(empty($username))  $username['username']  =  '';
					$val['username']   =  $username['username'];
				}
				
				$arr                   =  model_admincallback::$callback_type_arr[$val['type_id']];
				$val['type_tips']      =  $arr['name'];
				
				$arr                   =  model_admincallback::$callback_status_arr[$val['status']];
				$val['status_tips']    =  '<span>'.$arr['name'].'</span>';
				
				unset($val);				
			}
		}
	
		$var                      =  array_merge ( $var, $lists );
		// 处理时间区间段input表单 
		$var ['input_starttime']  =  helper_form::date ( 'search[start_time]', $this->_get ( 'start_time' ) );
		$var ['input_endtime']    =  helper_form::date ( 'search[end_time]', $this->_get ( 'end_time' ) );
		//回访状态
		$var['status_select']    =  helper_form::select($this->_get('status'),model_admincallback::$callback_status_arr,'name="search[status]"','全部');
		
		$this->assign($var);
		$this->display();
	}
	
	//项目回访列表    update 2015.05.26  liurengang
	public function actionProjectCallback(){
		
		$var  =  $this->_get();
		// 搜索参数处理
		$map  =  $this->_search ( array ('adminCallback') );

		//搜索时间的处理
		if (! empty ( $_GET ['start_time'] ) && ! empty ( $_GET ['end_time'] )) {
			$map ['add_time']  =  array (
				array ('egt',strtotime ( $this->_get ( 'start_time' ) )),
				array ('elt',strtotime ( $this->_get ( 'end_time' )) + 86400 )
			);
		}
		//项目回访 类型
		$map['type_id']        =  array('eq',3);
		if(isset($_GET['pid']) && !empty($_GET['pid'])) {
			$map['table_id']  =  $_GET['pid'];
			$var['table_id']   =  $_GET['pid'];
		}
		
		$lists                 =  M ( 'adminCallback' )->where ( $map )->order ( 'add_time desc' )->page ();
		
		if ( !empty($lists['lists']) ) {
			foreach($lists['lists'] as $key=>&$val) {
				if ( empty($val['username']) ) {
					$username          =  model_user::getInfo($val['uid'],'username');
					if(empty($username))  $username['username']  =  '';
					$val['username']   =  $username['username'];
				}
				
				$proname               =  model_project::getInfo($val['table_id'],'name');
				
				if(empty($proname))    $proname  =  '';
				$val['proname']        =  $proname;
				
				$arr                   =  model_admincallback::$callback_type_arr[$val['type_id']];
				$val['type_tips']      =  $arr['name'];
				
				$arr                   =  model_admincallback::$callback_status_arr[$val['status']];
				$val['status_tips']    =  '<span>'.$arr['name'].'</span>';
				
				unset($val);				
			}
		}
		
		$var                           =  array_merge ( $var, $lists );
		// 处理时间区间段input表单 
		$var ['input_starttime']       =  helper_form::date ( 'search[start_time]', $this->_get ( 'start_time' ) );
		$var ['input_endtime']         =  helper_form::date ( 'search[end_time]', $this->_get ( 'end_time' ) );
		//回访状态
		$var['status_select']    =  helper_form::select($this->_get('status'),model_admincallback::$callback_status_arr,'name="search[status]"','全部');
		
		$this->assign($var);
		$this->display();
	}
	

	//会员回访记录详情
	public function actionUserCallbackDetails(){
		
		$id    =  $this->_get('id')  ?  $this->_get('id')  :  0;
		if ( empty($id) ) $this->error('参数错误',url('usercallback')); 
		
		$var['callback_info']  =  M('adminCallback')->where(array('id'=>$id))->find();
		$arr  =  model_admincallback::$callback_type_arr[$var['callback_info']['status']];
		$var['callback_info']['type_tips']  =  $arr['name'];
		$arr  =  model_admincallback::$callback_status_arr[$var['callback_info']['type_id']];
		$var['callback_info']['status_tips']    =  $arr['name'];
		
		$this->assign($var);
		$this->display();
		
	}
	
	//招商回访记录详情
	public function actionMerchantsCallbackDetails(){
		$id    =  $this->_get('id')  ?  $this->_get('id')  :  0;
		if ( empty($id) ) $this->error('参数错误',url('merchantscallback')); 
		
		$var['callback_info']  =  M('adminCallback')->where(array('id'=>$id))->find();
		$arr  =  model_admincallback::$callback_type_arr[$var['callback_info']['status']];
		$var['callback_info']['type_tips']  =  $arr['name'];
		$arr  =  model_admincallback::$callback_status_arr[$var['callback_info']['type_id']];
		$var['callback_info']['status_tips']    =  $arr['name'];
		
		$this->assign($var);
		$this->display();
		
	}
	
	//项目回访记录详情
	public function actionProjectCallbackDetails(){
		$id    =  $this->_get('id')  ?  $this->_get('id')  :  0;
		if ( empty($id) ) $this->error('参数错误',url('projectcallback')); 
		
		$var['callback_info']  =  M('adminCallback')->where(array('id'=>$id))->find();
		$arr  =  model_admincallback::$callback_type_arr[$var['callback_info']['status']];
		$var['callback_info']['type_tips']  =  $arr['name'];
		$arr  =  model_admincallback::$callback_status_arr[$var['callback_info']['type_id']];
		$var['callback_info']['status_tips']    =  $arr['name'];
		
		$this->assign($var);
		$this->display();
		
	}
	
	
	/**
	 * 
	 * 添加回访信息（我要回访）
	 * @author liurengang
	 * @date   2015.05.15
	 * 
	 */
	public function actionCallbackAdd(){
		
		if(isset($_GET['table_id']) && !empty($_GET['table_id'])) $table_id = $_GET['table_id'];
		else $this->error('参数错误！');
		
		$adminCallback = M('adminCallback');
		if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
			
			$data              =  $this->_post('data');
			$data['type_id']   =  $_POST['type']; 
			$data['table_id']  =  $table_id;
			$data['content']   =  strip_tags($data['content']);
			$data['uid']       =  $this->auth['uid'];
			$data['username']  =  $this->auth['username'];
			$data['add_time']  =  time();
			$data['ip']        =  getIP();
			$returnid          =  $adminCallback->add($data);
			
			switch($_POST['type']){
				case 1:
					$data2['callback_status']   =  $data['status'];
					M('user')->where(array('uid'=>$table_id))->save($data2);
					$url  = url('admincallback/usercallback?table_id='.$table_id);
					break;
				case 2:
					//修改招商列表状态
					$data2['callback_status']  =  $data['status'];
					M('merchants')->where(array('id' => $table_id))->save($data2);
					$url  =  url('admincallback/merchantscallback?table_id='.$table_id);
					break;
				case 3:
					$data2['callback_status']   =  $data['status'];
					M('project')->where(array('id'=>$table_id))->save($data2);
					$url  =  url('admincallback/projectcallback?table_id='.$table_id);
					break;
				default:
					$data2['callback_status']   =  $data['status'];
					M('user')->where(array('uid'=>$table_id))->save($data2);
					$url  = url('admincallback/usercallback?table_id='.$table_id);
					break;
			}
			
			$this->savelog('添加回访记录【id:' . $returnid . '】');
			$this->success('操作成功！', $url);
		}
		
		//回访类型标识
		$var['type'] = $this->_get('type');
		
		$var['callback_info']                 =  $adminCallback->where(array('table_id'=>$table_id))->order('add_time desc')->find();
		if( empty($var['callback_info']) )    $var['bs']  =  '-1';
		
		$arr                                  =  model_admincallback::$callback_type_arr[$var['callback_info']['type_id']];
		$var['callback_info']['type_tips']    =  $arr['name'];
		$arr                                  =  model_admincallback::$callback_status_arr[$var['callback_info']['status']];
		$var['callback_info']['status_tips']  =  $arr['name'];
		
		$var['id'] = $id;
		
		$this->assign($var);
		$this->display();
	}
	
	/**
	 * 
	 * 回访信息编辑
	 * @author liurengang
	 * @date   2015.05.15
	 * 
	 */
	public function actionCallbackEdit(){
		
		$adminCallback = M('adminCallback');
		if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
			$data  =  $this->_post('data');
			
			$adminCallback->where(array('id'=>$data['id']))->save($data);
			
			switch($data['type_id']){
				case 1:
					$data2['callback_status']   =  $data['status'];
					M('user')->where(array('uid'=>$table_id))->save($data2);
					$url  = url('admincallback/usercallback?table_id='.$data['table_id']);
					break;
				case 2:
					//修改招商列表状态
					$data2['callback_status']  =  $data['status'];
					M('merchants')->where(array('id' => $table_id))->save($data2);
					$url  =  url('admincallback/merchantscallback?table_id='.$data['table_id']);
					break;
				case 3:
					$data2['callback_status']   =  $data['status'];
					M('project')->where(array('id'=>$table_id))->save($data2);
					$url  =  url('admincallback/projectcallback?table_id='.$data['table_id']);
					break;
				default:
					$data2['callback_status']   =  $data['status'];
					M('user')->where(array('uid'=>$table_id))->save($data2);
					$url  = url('admincallback/usercallback?table_id='.$data['table_id']);
					break;
			}
			$this->savelog('修改回访记录【id:' . $data['id'] . '】');
			$this->success('操作成功！', $url);
		}
		
		if(isset($_GET['id']) && !empty($_GET['id'])) $id = $_GET['id'];

		$var['callback_info'] = $adminCallback->where(array('id'=>$id))->find();
		
		$this->assign($var);
		$this->display();
	}
	
	
}
<?php
// +----------------------------------------------------------------------
// | 招商
// +----------------------------------------------------------------------

class controller_admin_merchants extends controller_admin_abstract {

	//招商列表
	public function actionIndex(){
		
		$var = $this->_get ();
		// 搜索参数处理
		$map = $this->_search ( 'merchants' );
		//搜索时间的处理
		if (! empty ( $_GET ['start_time'] ) && ! empty ( $_GET ['end_time'] )) {
			$map ['add_time'] = array (
					array ('egt',strtotime ( $this->_get ( 'start_time' ) )),
					array ('elt',strtotime ( $this->_get ( 'end_time' ) ) + 86400)
			);
		}
		$lists       =  M ( 'merchants' )->where ( $map )->order ( 'id desc' )->page ();
		if(!empty($lists['lists'])) {
			foreach($lists['lists'] as $key => &$val){
				$arr                          =  model_merchants::$callback_status_arr[$val['callback_status']];
				$val['callback_status_tips']  =  '<span '.$arr['style'].'>'.$arr['name'].'</span>';
				
				$arr                          =  model_merchants::$status_arr[$val['status']];
				$val['status_tips']           =  '<span '.$arr['style'].'>'.$arr['name'].'</span>';
				
				$arr                          =  model_merchants::$type_arr[$val['type']];
				$val['type_tips']             =  '<span '.$arr['style'].'>'.$arr['name'].'</span>';
				
				if(empty($val['username'])){
					$username          =  model_user::getInfo($val['uid'],'username');
					if(empty($username)) $username['username'] = '';
					$val['username']   =  $username['username'];
				}
				if(empty($val['real_name'])){
					$realname          =  model_user::getInfo($val['uid'],'realname');
					if(empty($username)) $realname['real_name'] = '';
					$val['real_name']  =  $realname['real_name'];
				}
				
				unset($val);
			}
		}

		$var = array_merge ( $var, $lists );
		
		// 处理时间区间段input表单
		$var ['input_starttime']        =  helper_form::date ( 'search[start_time]', $this->_get ( 'start_time' ) );
		$var ['input_endtime']          =  helper_form::date ( 'search[end_time]', $this->_get ( 'end_time' ) );
        //回访状态
        $var['callback_status_select']  =  helper_form::select($this->_get('callback_status', ''), model_merchants::$callback_status_arr, 'name="search[callback_status]"', '全部');
        //审核状态
        $var['status_select']           =  helper_form::select($this->_get('status', ''), model_merchants::$status_arr, 'name="search[status]"', '全部');
 
		$this->assign($var);
		$this->display('index');
	}
	
	/**
	 * 
	 * 编辑招商信息
	 * @author liurengang
	 * @date   2015.05.14
	 * 
	 */
	/*public function actionEdit(){
		
		$merchants = D('merchants');
		if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
			$data = $this->_post('data');
			$merchants->where(array('id' => $data['id']))->save($data);
			$this->savelog('修改招商【' . $data['id'] . '】');
			$this->success('招商修改成功！', url('index'));
		}
		$id = !empty($_GET['id'])?$_GET['id']:$this->error('请选择修改的招商信息！');
		$var['info'] = D('merchants')->getinfoOne($id);
		$this->assign($var);
		$this->assign('id',$id);
		$this->display();
	}*/
	
	/**
	 * 
	 * 批量审核招商信息
	 * @author liurengang
	 * @date   2015.05.14
	 * 
	 */
	/*public function actionAudite(){
		
		if ( $_POST ['do'] == 'toaudite' ) {
			$ids      =  $this->_post ( "ids" );
			if (empty ( $ids ))
				$this->error ( '请选择待审核的招商记录' );
			foreach ( $ids as $id ) {
				M ( 'merchants' )->where(array('id'=>$id))->save(array('status'=>1));
			}
			$ids = implode ( ',', $_POST ['ids'] );
			$this->savelog ( "批量审核招商记录【id:{$ids}】" );
			$this->success ( '审核招商记录审核成功' );
		} 
	}*/
	
	/**
	 * 
	 * 招商信息回访 我要回访
	 * @author liurengang
	 * @date   2015.05.15
	 * 
	 */
	/*public function actionCallback(){
		
		if(isset($_GET['id']) && !empty($_GET['id'])) $id = $_GET['id'];
		else $this->error('参数错误！', url('index'));
		
		$adminCallback = M('adminCallback');
		if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
			
			$data              =  $this->_post('data');
			$data['type_id']   =  2; //目前仅限于招商回访
			$data['table_id']  =  $id;
			$data['content']   =  strip_tags($data['content']);
			$data['uid']       =  $this->auth['uid'];
			$data['username']  =  $this->auth['username'];
			$data['add_time']  =  time();
			$data['ip']        =  getIP();
			$adminCallback->add($data);
			
			//修改招商列表状态
			$data2['callback_status']  =  $data['status'];
			M('merchants')->where(array('id' => $id))->save($data2);
			
			$this->savelog('添加招商信息回访记录【id:' . $id . '】');
			$this->success('操作成功！', url('callbacklist?table_id='.$id));
		}
		
		$var['callback_info'] = $adminCallback->where(array('table_id'=>$id))->order('add_time desc')->find();
		
		if(!empty($var['callback_info'])){
			if( $var['callback_info']['type'] == 1 )
				$var['callback_info']['type_tips']  =  '会员回访';
			elseif( $var['callback_info']['type']== 2 )
				$var['callback_info']['type_tips']  =  '招商回访';
			elseif( $var['callback_info']['type']== 3 )
				$var['callback_info']['type_tips']  =  '项目回访';
				
			if( $var['callback_info']['status'] == 0 )
				$var['callback_info']['status_tips']  =  '未回访';
			elseif( $var['callback_info']['status'] == 1 )
				$var['callback_info']['status_tips']  =  '已回访';
			elseif( $var['callback_info']['status'] == 2 )
				$var['callback_info']['status_tips']  =  '已回访并特殊标记';
			elseif( $var['callback_info']['status'] == 3 )
				$var['callback_info']['status_tips']  =  '需要再次回访';
		}
		
		$var['id'] = $id;
		
		$this->assign($var);
		$this->display();
	}*/
	
	//招商列表
	/*public function actionCallbackList(){
		
		$var = $this->_get ();
		// 搜索参数处理
		$map = $this->_search ( 'adminCallback' );
		//搜索时间的处理
		if (! empty ( $_GET ['start_time'] ) && ! empty ( $_GET ['end_time'] )) {
			$map ['add_time'] = array (
					array ('egt',strtotime ( $this->_get ( 'start_time' ) )),
					array ('elt',strtotime ( $this->_get ( 'end_time' ) ))
			);
		}
		//招商回访 类型
		$map['type_id']  =  array('eq',2);
		$lists       =  M ( 'adminCallback' )->where ( $map )->order ( 'add_time desc' )->page ();
		if(!empty($lists['lists'])) {
			foreach($lists['lists'] as $key => &$val){
				$arr                 =  model_merchants::$callback_type_arr[$val['type_id']];
				$val['type_tips']    =  '<span '.$arr['style'].'>'.$arr['name'].'</span>';
				$arr                 =  model_merchants::$callback_status_arr[$val['status']];
				$val['status_tips']  =  '<span '.$arr['style'].'>'.$arr['name'].'</span>';
			
				unset($val);
			}
		}
	
		$var = array_merge ( $var, $lists );
		
		// 处理时间区间段input表单
		$var ['input_starttime']  =  helper_form::date ( 'search[start_time]', $this->_get ( 'start_time' ) );
		$var ['input_endtime']    =  helper_form::date ( 'search[end_time]', $this->_get ( 'end_time' ) );
		//回访类型
        $var['type_select']       =  helper_form::select($this->_get('type_id', ''), model_merchants::$callback_type_arr, 'name="search[type_id]"', '全部');
        //回访状态
        $var['status_select']     =  helper_form::select($this->_get('status', ''), model_merchants::$callback_status_arr, 'name="search[status]"', '全部');
       
		$this->assign($var);
		$this->assign('lists',$lists['lists']);
		$this->assign('pages',$lists['pages']);
		$this->display();
	}*/
	
	/**
	 * 
	 * 招商回访信息编辑
	 * @author liurengang
	 * @date   2015.05.15
	 * 
	 */
	/*public function actionCallbackEdit(){
		
		$adminCallback = M('adminCallback');
		if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
			
			$data  =  $this->_post('data');
			
			$adminCallback->where(array('id'=>$data['id']))->save($data);
			//修改招商列表状态
			$data2['callback_status']  =  $data['status'];
			M('merchants')->where(array('id' => $data['table_id']))->save($data2);
			
			$this->savelog('修改招商信息回访记录【id:' . $data['id'] . '】');
			$this->success('操作成功！', url('index?id='.$data['table_id']));
		}
		
		if(isset($_GET['id']) && !empty($_GET['id'])) $id = $_GET['id'];
		else $this->error('参数错误！', url('callbacklist'));
		
		$var['callback_info'] = $adminCallback->where(array('id'=>$id))->find();
		
		$this->assign($var);
		$this->display();
	}*/
	
	
}
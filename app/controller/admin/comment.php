<?php
/**
 * 后台操作评论
 */
class controller_admin_comment extends controller_admin_abstract {
	// 公用的评论列表
	// 查看评论列表
	public function actionIndex($typeid = '') {
		$var = $this->_get ();
		$map = $this->_search ( array ('comment' ) );
		// 搜索参数处理
		if (! empty ( $_GET ['start_time'] ) && ! empty ( $_GET ['end_time'] )) {
			$map ['add_time'] = array (
				array ('egt',strtotime ( $this->_get ( 'start_time' ) ) ),
				array ('elt',strtotime ( $this->_get ( 'end_time' )) + 86400  ) 
			);
		}
		
		if (! empty ( $typeid )) {
			$map ['types'] = array ('eq',$typeid);
		}else{
			//从项目详情页或是用户详情页传来 为获取对应项目id（或uid）对应的评论记录
			if($this->_get('value_id')){
				$map ['value_id'] = array ('eq',$this->_get('value_id'));
			}
			
		}
		
		$res = M ( 'Comment' )->where ( $map )->order ( 'id desc' )->page ();
		if(!empty($res['lists'])){
			foreach($res['lists'] as $key => &$val){
				$val['add_time'] = date('Y-m-d H:i:s',$val['add_time']);
				$arr = model_comment::$status_array[$val['status']];
				$val['status_tips'] = '<span '.$arr['style'].'>'.$arr['name'].'</span>';
				$arr = model_comment::$type_array[$val['types']];
				$val['types_tips'] = '<span '.$arr['style'].'>'.$arr['name'].'</span>';
				if(!empty($val['value_id'])){
					
					if( $typeid == 1 ) {
						$id    =  model_user::getInfo($val['value_id'],'uid');
						$name  =  model_user::getInfo($val['value_id'],'username');
					}
					
					if( $typeid == 2 ){
						$id    =  model_project::getInfo($val['value_id'],'id');
						$name  =  model_project::getInfo($val['value_id'],'name');
					}
					
					if(empty($name['id'])) $val['value_id']      =  '';
					if(empty($name['name'])) $val['value_name']  =  '';
					$val['value_id']    =  $id;
					$val['value_name']  =  $name;
				}
				unset($val);
				
			}
			
		}
		
		$var = array_merge ( $var, $res );
		// 处理时间区间段input表单
		$var ['input_starttime'] = helper_form::date ( 'search[start_time]', $this->_get ( 'start_time' ) );
		$var ['input_endtime']   = helper_form::date ( 'search[end_time]', $this->_get ( 'end_time' ) );
		// 评论类型下拉框
		//$var ['types_select']    = helper_form::select ( $this->_get ( 'types', '' ), model_comment::$type_array, 'name="search[types]"', '全部' );
		// 评论状态下拉框
		$var ['status_select']   = helper_form::select ( $this->_get ( 'status', '' ), model_comment::$status_array, 'name="search[status]"', '全部' );
		
		$this->assign ( $var );
		$this->display ( 'index' );
	}
	
	// 删除评论
	public function actionDelete() {
		if ($_GET ['do'] == 'all') {
			$ids = $this->_post ( "ids" );
			if (empty ( $ids ))
				$this->error ( '请选择待删除的评论' );
			foreach ( $ids as $id ) {
				D ( 'Comment' )->commentDelete ( $id, false );
			}
			$ids = implode ( ',', $_POST ['ids'] );
			$this->savelog ( "批量删除评论【id:{$ids}】" );
		} else {
			$id = $this->_get ( "id" );
			if (empty ( $id ))
				$this->error ( '请选择待删除的评论' );
			D ( 'Comment' )->commentDelete ( $id, false );
			$id = implode ( ',', $_GET ['id'] );
			$this->savelog ( "删除评论【id:{$id}】" );
		}
		$this->success ( '评论删除成功' );
	}
	
	// 修改评论
	public function actionEdit() {
		// 评论审核start
		if ($_GET ['check'] == 'audite') {
			if ($_GET ['do'] == 'all') {
				
				$ids = $this->_post ( "ids" );
				if (empty ( $ids ))
					$this->error ( '请选择待审核的评论' );
				foreach ( $ids as $id ) {
					if ($_GET ['isshow'] == 'no') {
						D ( 'Comment' )->commentAudite ( $id, true );
					} else {
						D ( 'Comment' )->commentAudite ( $id, false );
                        //对项目进行评论获得10积分，每日限一次
                        $comment = D('comment')->getInfo($id);
                        if($comment){
                            model_credit::setCredit(array('uid'=>$comment['uid'],'code'=>'project_comment','评论项目:'.$comment['value_id']), TRUE);
                        }
					}
				}
				$ids = implode ( ',', $_POST ['ids'] );
				$this->savelog ( "批量审核评论【uid:{$ids}】" );
				$this->success ( '评论审核成功' );
			} else {
				$id = $this->_get ( "id" );
				if (empty ( $id ))
					$this->error ( '请选择待审核的评论' );
				if ($_GET ['isshow'] == 'no') {
					// 修改评论状态为审核未通过
					D ( 'Comment' )->commentAudite ( $id, true );
				} else {
					// 修改评论状态为审核通过
					D ( 'Comment' )->commentAudite ( $id, false );
                    //对项目进行评论获得10积分，每日限一次
                    $comment = D('comment')->getInfo($id);
                    if($comment){
                        model_credit::setCredit(array('uid'=>$comment['uid'],'code'=>'project_comment','评论项目:'.$comment['value_id']), TRUE);
                    }
				}
				$id = implode ( ',', $_GET ['id'] );
				$this->savelog ( "审核评论【id:{$id}】" );
				$this->success ( '评论审核成功' );
			}
		}
		// 评论审核end
		// 修改添加评论
		if (isset ( $_POST ['do'] ) && $_POST ['do'] == 'dosubmit') {
			$id = $this->_post ( 'id' );
			if ($id) {
				// 修改
				$data = $this->_post ( 'data' );
				$data ['update_time'] = time ();
				D ( 'comment' )->where ( array (
						'id' => $id 
				) )->save ( $data );
				$this->savelog ( '修改评论【' . $id . '】' );
				$this->success ( '修改评论成功！', url ( 'index' ) );
			} else {
				// 添加
				$data = $this->_post ( 'data' );
				$data ['update_time'] = time ();
				$data ['add_time'] = time ();
				$data ['uid'] = $_SESSION ['auth_user'] ['uid'];
				$data ['username'] = $_SESSION ['auth_user'] ['username'];
				$data ['ip'] = getIp ();
				$id_add = D ( 'comment' )->add ( $data );
				$this->savelog ( '添加评论【' . $id_add . '】' );
				$this->success ( '添加评论成功！', url ( 'index' ) );
			}
		}

		// 评论状态数组
		$status_array = array (
				'-1' =>  '审核未通过',
				'0'  =>  '待审核',
				'1'  =>  '审核通过' 
		);
		if (! empty ( $_GET ['id'] )) {
			$var = $_GET;
			$id = $_GET ['id'];
			$var ['res'] = D ( 'comment' )->getInfo ( $id );
			if(empty($var['res']['status'])) $var['res']['status'] == -1;
		}else{
			$var['res']['status']  = -1;
		}
		
		$this->assign ( 'type_array', $type_array );
		$this->assign ( 'status_array', $status_array );
		$this->assign ( $var );
		$this->display ();
	}
	
	/*
	 * 项目评论
	 */
	public function actionProjectComment() {
		$this->actionIndex ( 2 );
	}
	/*
	 * 用户评论
	 */
	public function actionUserComment() {
		$this->actionIndex ( 1 );
	}
	/*
	 * 网站评论列表
	 */
	public function actionNetworkComment() {
		$this->actionIndex ( 3 );
	}
	// 评论回复列表
	public function actionAnswerComment() {
		$var  =  $this->_get ();
		// 搜索参数处理
		$map  =  $this->_search ( 'comment_reply' );
		if (! empty ( $_GET ['start_time'] ) && ! empty ( $_GET ['end_time'] )) {
			$map ['add_time'] = array (
					array ('egt',strtotime ( $this->_get ( 'start_time' ) ) ),
					array ('elt',strtotime ( $this->_get ( 'end_time' ) ) + 86400 ) 
			);
		}
		$res  =  M ( 'comment_reply' )->where ( $map )->order ( 'id desc' )->page ();
		if(!empty($res['lists'])) {
			//回复对应 名称
			foreach ($res['lists'] as $key=>&$val){
				if($val['types']==1){//用户
					$val['value_name'] = D("user")->userid2username($val['value_id']);
					
				}elseif ($val['types']==2){//项目
					$val['value_name'] = D("project")->getInfo($val['value_id'],'name');
					
				}
				
				$arr                 =  model_comment::$status_array[$val['status']];
				$val['status_tips']  =  "<span {$arr['style']}>".$arr['name']."</span>";
				$arr                 =  model_comment::$type_array[$val['types']];
				$val['type_tips']    =  $arr['name'];
			}
		}
		$var = array_merge ( $var, $res );
		// 处理时间区间段input表单
		$var ['input_starttime']     =  helper_form::date ( 'search[start_time]', $this->_get ( 'start_time' ) );
		$var ['input_endtime']       =  helper_form::date ( 'search[end_time]', $this->_get ( 'end_time' ) );
		$this->assign ( $var );
		
		$this->display ();
	}
	// 修改评论回复
	public function actionEditAnswer() {
		// 评论回复审核start
		if ($_GET ['check'] == 'audite') {
			if ($_GET ['do'] == 'all') {
				$ids = $this->_post ( "ids" );
				if (empty ( $ids ))
					$this->error ( '请选择待审核的评论' );
				foreach ( $ids as $id ) {
					if ($_GET ['isshow'] == 'no') {
						D ( 'commentreply' )->answerAudite ( $id, true );
					} else {
						D ( 'commentreply' )->answerAudite ( $id, false );
					}
				}
				$ids = implode ( ',', $_POST ['ids'] );
				$this->savelog ( "批量审核评论回复【uid:{$ids}】" );
				$this->success ( '评论回复审核成功' );
			} else {
				$id = $this->_get ( "id" );
				if (empty ( $id ))
					$this->error ( '请选择待审核的评论回复' );
				if ($_GET ['isshow'] == 'no') {
					// 修改评论回复状态为审核未通过
					D ( 'Commentreply' )->answerAudite ( $id, true );
				} else {
					// 修改评论回复状态为审核通过
					D ( 'Commentreply' )->answerAudite ( $id, false );
				}
				$id = implode ( ',', $_GET ['id'] );
				$this->savelog ( "审核评论回复【id:{$id}】" );
				$this->success ( '评论回复审核成功' );
			}
		}
		// 评论回复审核end
		// 修改添加评论回复
		if (isset ( $_POST ['do'] ) && $_POST ['do'] == 'dosubmit') {
			$id = $this->_post ( 'id' );
				// 修改
				$data = $this->_post ( 'data' );
				$data ['update_time'] = time ();
				D ( 'commentreply' )->where ( array (
						'id' => $id 
				) )->save ( $data );
				$this->savelog ( '修改评论回复【' . $id . '】' );
				$this->success ( '修改评论回复成功！', url ( 'answerComment' ) );
			
		}
		// 评论类型数组
		$type_array = array (
				1 => '对用户的评论',
				2 => '对项目的评论',
				3 => '对网站的评论',
				4 => '对财务报表的评论',
				5 => '对分红的评论' 
		);
		// 评论状态数组
		$status_array = array (
				'-1' => '审核未通过',
				'0' => '待审核',
				'1' => '审核通过' 
		);
		if (! empty ( $_GET ['id'] )) {
			$var = $_GET;
			$id = $_GET ['id'];
			$var ['res'] = D ( 'commentreply' )->getInfo ( $id );
		}
		$this->assign ( 'type_array', $type_array );
		$this->assign ( 'status_array', $status_array );
		$this->assign ( $var );
		$this->display ();
	}
	// 删除评论回复
	public function actionDeleteAnswer() {
		if ($_GET ['do'] == 'all') {
			$ids = $this->_post ( "ids" );
			if (empty ( $ids ))
				$this->error ( '请选择待删除的评论回复' );
			foreach ( $ids as $id ) {
				D ( 'Commentreply' )->delete_answer ( $id, false );
			}
			$ids = implode ( ',', $_POST ['ids'] );
			$this->savelog ( "批量删除评论回复【id:{$ids}】" );
		} else {
			$id = $this->_get ( "id" );
			if (empty ( $id ))
				$this->error ( '请选择待删除的评论回复' );
			D ( 'Commentreply' )->delete_answer ( $id, false );
			$id = implode ( ',', $_GET ['id'] );
			$this->savelog ( "删除评论回复【id:{$id}】" );
		}
		$this->success ( '评论回复删除成功' );
	}
	//添加评论回复
	public function actionAddAnswe(){
		// 评论状态数组
		$this->status_array = array (
				'-1' => '审核未通过',
				'0' => '待审核',
				'1' => '审核通过'
		);
		$var = $this->_get ();
		$id = $_GET['id'];
		if(!empty($id)){
			$res = D ( 'comment' )->getInfo ( $id );
		}
		if($_POST){
			$data = $this->_post('data');
			$data['uid'] = $_SESSION['auth_user']['uid'];
			$data['username'] = $_SESSION['auth_user']['username'];
			$data['comment_id'] = $res['id'];
			$data['comment_uid'] = $res['uid'];
			$data['comment_username'] = $res['username'];
			$data['value_id'] = $res['value_id'];
			$data['types'] = $res['types'];
			$data['is_admin'] = 1;
			$data['add_time'] = time ();
			$data['ip'] = getIp();
			$id_add = D ( 'commentreply' )->add ( $data );
			$this->savelog ( '添加评论回复【' . $id_add . '】' );
			$this->success ( '添加评论回复成功！', url ( 'index' ) );
		}
		$this->display();
	}
}

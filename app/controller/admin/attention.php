<?php
// +----------------------------------------------------------------------
// | 项目关注
// +----------------------------------------------------------------------

class controller_admin_attention extends controller_admin_abstract {

	//关注项目列表
	public function actionIndex(){
		$var = $this->_get ();

		// 搜索参数处理
		$map = $this->_search ('projectAttention');
		//搜索时间的处理
		if (! empty ( $_GET ['start_time'] ) && ! empty ( $_GET ['end_time'] )) {
			$map ['add_time'] = array (
					array (
							'egt',
							strtotime ( $this->_get ( 'start_time' ) )
					),
					array (
							'elt',
							strtotime ( $this->_get ( 'end_time' ) ) + 86400
					)
			);
		}
		
		if(!empty($_GET['username'])){
			$map['uid'] = D('user')->getInfo($_GET['username'],'uid');
		}
		
		$lists  =  M ( 'projectAttention' )->where ( $map )->order ( 'id desc' )->page ();
		
		if(!empty($lists['lists'])){
			foreach ($lists['lists'] as $key => &$val){
				$pro_name   =  M ( 'project' )->where(array('id'=>$val['pid']))->getField ( 'name' );		
				$username   =  M ( 'user' )->where(array('uid'=>$val['uid']))->getField ( 'username' );
				if(empty($pro_name)) $pro_name  =  '-';
				if(empty($username)) $pro_name  =  '-';
				$val['pro_name']  =  $pro_name;
				$val['username']  =  $username;
				
				unset($val);
			}
		}
		
		$var = array_merge ( $var, $lists );
		
		// 处理时间区间段input表单
		$var ['input_starttime'] = helper_form::date ( 'search[start_time]', $this->_get ( 'start_time' ) );
		$var ['input_endtime']   = helper_form::date ( 'search[end_time]', $this->_get ( 'end_time' ) );
	
		$this->assign($var);
		$this->display();
	}

}
<?php
/**
 * 短消息管理
 */

class controller_admin_message extends controller_admin_abstract{

	//短消息管理
	public function actionIndex(){		
		$var = $this->_get();

		//搜索参数处理
		$map = $this->_search(array('message'));
		if(!empty($_GET['start_time']) && !empty($_GET['end_time'])){
			$map['add_time'] = array( array('egt', strtotime($this->_get('start_time'))), array('lt', strtotime($this->_get('end_time')) + 86400) );
		}
		$res = M('message')->where($map)->order("id desc")->page();
		if($res['lists']){
			foreach($res['lists'] as &$v){
				$v['delete_tips']  =  model_message::$isdelete_arr[$v['is_delete']];
				$arr               =  model_message::$status_arr[$v['status']];
				$v['add_time']     =  date('Y-m-d H:i:s',$v['add_time']);
				$v['status_tips']  =  '<span "'.$arr['style'].'">['.$arr['name'].']</span>';
				$v['content']      =  htmlspecialchars($v['content']);
				$v['type_name']    =  D('remind')->getInfo($v['remind'],'name');
				
				unset($v);
			}
		}
	
		$var = array_merge($var,$res);

		//处理时间区间段input表单
		$var['input_starttime'] = helper_form::date('search[start_time]',$this->_get('start_time'));
		$var['input_endtime'] = helper_form::date('search[end_time]',$this->_get('end_time'));
		
		//状态下拉框
		$var['status_select'] = helper_form::select($this->_get('status',''),model_message::$status_arr,'name="search[status]"','全部');
		
		//状态下拉框
		$var['type_select'] = helper_form::select($this->_get('type',''),model_message::$type_arr,'name="search[type]"','全部');
		
		//提醒下拉框
		$var['remind_select'] = D('remind')->getSelect($this->_get('remind',''), 'name="search[remind]"', '全部');
		
		$this->assign($var);
		$this->setReUrl();
		$this->display();
	}

	//删除短消息
	public function actionDelete(){
		$ids = $this->_post("ids");
		if(empty($ids)) $this->error('请选择待删除的信息');		
		foreach($ids as $id){
			M('message')->where(array('id' => $id))->delete();
		}
		$ids = implode(',',$ids);
		$this->savelog("删除站内短信【id:{$ids}】");
		$this->success('站内短信删除成功');
	}

}

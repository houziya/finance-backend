<?php
/**
 * 移动设备相关
 */

class controller_admin_mobile extends controller_admin_abstract{

	//查看手机归属地
	public function actionVest(){
		$var = $this->_get();

		//搜索参数处理
		$map = $this->_search(array('MobileVest'));
		if(!empty($map['mobile'])){
			$map['mobile'] = substr($map['mobile'], 0, 7);
		}
		$res = M('MobileVest')->where($map)->order('id desc')->page();
		$var = array_merge($var,$res);

		$this->assign($var);
		$this->display();
	}
	
	//手机设备列表
	public function actionDevices(){
		$var = $this->_get();

		//搜索参数处理
		$map = $this->_search(array('MobileDevices'));
		if(!empty($_GET['start_time']) && !empty($_GET['end_time'])){
			$map['login_time'] = array( array('egt', strtotime($this->_get('start_time'))), array('lt', strtotime($this->_get('end_time')) + 86400) );
		}
		$res = M('MobileDevices')->where($map)->order('id desc')->page();
		$var = array_merge($var,$res);
		
		//处理时间区间段input表单
		$var['input_starttime'] = helper_form::date('search[start_time]',$this->_get('start_time'));
		$var['input_endtime'] = helper_form::date('search[end_time]',$this->_get('end_time'));
		
		//绑定状态
		$var['is_bind_select'] = helper_form::select($this->_get('is_bind',''),model_MobileDevices::$is_bind_arr,'name="search[is_bind]"','全部');
		
		$this->assign($var);
		$this->display();
	}
	

}

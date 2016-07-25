<?php

/**
 * admin_callback  回访
 */
class model_admincallback extends model_abstruct {

	protected $tableName = 'admin_callback';
	
	//招商信息回访类型
	static public $callback_type_arr = array(
		1   =>  array('id'  =>  1,   'name'  =>  '会员回访',  'style'  =>  'class="green"'),
		2   =>  array('id'  =>  2,   'name'  =>  '招商回访',  'style'  =>  'class="green"'),
		3   =>  array('id'  =>  3,   'name'  =>  '项目回访',  'style'  =>  'class="green"'),
	);
	
	//回访状态
	static public $callback_status_arr = array(
		0   =>  array('id'  =>  0,   'name'  =>  '未回访',        'style'  =>  'class="red"'),
		1   =>  array('id'  =>  1,   'name'  =>  '已回访',        'style'  =>  'class="green"'),
		2   =>  array('id'  =>  2,   'name'  =>  '已回访并特殊标记', 'style'  =>  'class="red"'),
		3   =>  array('id'  =>  3,   'name'  =>  '需要再次回访',    'style'  =>  'class="red"'),
	);
	/*
	 * 获取回访信息
	 */
	public function getInfo(){
		$info = $this->findAll();
		return $info;
	}
}

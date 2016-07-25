<?php
/**
 * 后台操作日志
 */

class controller_admin_log extends controller_admin_abstract{

	//查看后台操作日志列表
	public function actionIndex(){
		$var = $this->_get();

		//搜索参数处理
		$map = $this->_search(array('AdminUserLog'));
		if(!empty($_GET['start_time']) && !empty($_GET['end_time'])){
			$map['add_time'] = array( array('egt', $this->_get('start_time')), array('elt', $this->_get('end_time')) + 86400 );
		}
		if(!empty($_GET['remark'])){
			$map['remark'] = array('like', '%'.$this->_get('remark').'%');
		}

		$res = M('AdminUserLog')->where($map)->order('id desc')->page();
		$var = array_merge($var,$res);

		//处理时间区间段input表单
		$var['input_starttime'] = helper_form::date('search[start_time]',$this->_get('start_time'));
		$var['input_endtime'] = helper_form::date('search[end_time]',$this->_get('end_time'));

		$this->assign($var);
		$this->display();
	}
	
	//查看后台日志详情
	public function actionInfo(){
		$var = $this->_get();
		if(empty($var['id'])) $this->error('参数错误');
		
		$info = M('admin_user_log')->where(array('id' => $var['id']))->find();
		$info = $info['post'] ? unserialize($info['post']) : '';
		$info = "<pre>".var_export($info,true)."</pre>";
		
		$this->assign('info', $info);
		$this->display();
	}
	
	//查看用户操作日志列表
	public function actionOper(){
		$var = $this->_get();

		//搜索参数处理
		$map = $this->_search(array('UserLog'));
		if(!empty($_GET['start_time']) && !empty($_GET['end_time'])){
			$map['add_time'] = array( array('egt', $this->_get('start_time')), array('elt', $this->_get('end_time')) + 86400 );
		}
		if(!empty($_GET['remark'])){
			$map['remark'] = array('like', '%'.$this->_get('remark').'%');
		}

		$res = M('UserLog')->where($map)->order('id desc')->page();
		$var = array_merge($var,$res);

		//处理时间区间段input表单
		$var['input_starttime'] = helper_form::date('search[start_time]',$this->_get('start_time'));
		$var['input_endtime'] = helper_form::date('search[end_time]',$this->_get('end_time'));

		$this->assign($var);
		$this->display();
	}
	
	//查看登录日志列表
	public function actionLogin(){
		$var = $this->_get();

		//搜索参数处理
		$map = $this->_search(array('login'));		
		if(!empty($_GET['start_time']) && !empty($_GET['end_time'])){
			$map['add_time'] = array( array('egt', $this->_get('start_time')), array('elt', $this->_get('end_time')) + 86400 );
		}

		$res = M('login')->where($map)->order('id desc')->page();
		if(!empty($res['lists'])){
			foreach($res['lists'] as &$v){
				$v['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
				$v['admin_tips'] = $v['is_admin'] ? '<img src="'.C('url.img').'/s/admin/images/icon/icon_user.png" title="管理员" />' : '';
				$arr = model_user::$source_arr[$v['type']];
				$v['type_tips']      =  '<span '.$arr['style'].'>'.$arr['name'].'</span>';
			}
			unset($v);
		}
		$var = array_merge($var,$res);

		//处理时间区间段input表单
		$var['input_starttime'] = helper_form::date('search[start_time]',$this->_get('start_time'));
		$var['input_endtime'] = helper_form::date('search[end_time]',$this->_get('end_time'));
		
		//状态下拉框
		$var['type_select'] = helper_form::select($this->_get('type', ''), model_login::$type_arr, 'name="search[type]"', '全部');

		$this->assign($var);
		$this->display();
	}

	//手机短信发送日志
	public function actionSms(){		
		$var = $this->_get();

		//搜索参数处理
		$map = $this->_search(array('UserSendsmsLog'));		
		if(!empty($_GET['start_time']) && !empty($_GET['end_time'])){
			$map['add_time'] = array( array('egt', $this->_get('start_time')), array('elt', $this->_get('end_time')) );
		}

		$res = M('UserSendsmsLog')->where($map)->order('id desc')->page();

		if(!empty($res['lists'])){
			foreach($res['lists'] as &$v){
				$v['remind_name'] = D('remind')->getInfo($v['remind'],'name');
				$v['username'] = $v['user_id'] ? $v['username'] : '<span class="blue">系统</span>';
				$arr = model_UserSendsmsLog::$status_arr[$v['status']];
				$v['status_tips'] = '<span "'.$arr['style'].'">'.$arr['name'].'</span>';
			}
			unset($v);
		}
		$var = array_merge($var,$res);
		
		//处理时间区间段input表单
		$var['input_starttime'] = helper_form::date('search[start_time]',$this->_get('start_time'));
		$var['input_endtime'] = helper_form::date('search[end_time]',$this->_get('end_time'));
		
		//状态下拉框
		$var['status_select'] = helper_form::select($this->_get('status'),model_UserSendsmsLog::$status_arr,'name="search[status]"','全部');
		
		//提醒类型下拉框
		$var['remind_select'] = D('remind')->getSelect($this->_get('remind'),'name="search[remind]"','全部');

		$this->assign($var);
		$this->display();
	}
	
	
	//邮箱发送日志
	public function actionEmail(){
		$var = $this->_get();

		//搜索参数处理
		$map = $this->_search(array('UserSendemailLog'));		
		if(!empty($_GET['start_time']) && !empty($_GET['end_time'])){
			$map['add_time'] = array( array('egt', $this->_get('start_time')), array('elt', $this->_get('end_time')) + 86400 );
		}

		$res = M('UserSendemailLog')->where($map)->order('id desc')->page();
		if(!empty($res['lists'])){
			foreach($res['lists'] as &$v){
				$v['remind_name'] = D('remind')->getInfo($v['remind'],'name');
				$v['username'] = $v['user_id'] ? $v['username'] : '<span class="blue">系统</span>';
				$arr = model_Usersendemaillog::$status_arr[$v['status']];
				$v['status_tips'] = '<span "'.$arr['style'].'">'.$arr['name'].'</span>';
				$v['content'] = helper_string::html2text($v['content']);
			}
			unset($v);
		}
		$var = array_merge($var,$res);
		
		//处理时间区间段input表单
		$var['input_starttime'] = helper_form::date('search[start_time]',$this->_get('start_time'));
		$var['input_endtime'] = helper_form::date('search[end_time]',$this->_get('end_time'));
		
		//状态下拉框
		$var['status_select'] = helper_form::select($this->_get('status'),model_UserSendemailLog::$status_arr,'name="search[status]"','全部');
		
		//提醒类型下拉框
		$var['remind_select'] = D('remind')->getSelect($this->_get('remind'),'name="search[remind]"','全部');
		
		$this->assign($var);
		$this->display();
	}

}

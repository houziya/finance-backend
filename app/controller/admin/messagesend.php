<?php
/**
 * 自定义 短信 邮件 发送
 * huangnan 2015.06.07
 */

class controller_admin_messageSend extends controller_admin_abstract
{
	public $names;
	
	/**
	 * 构造函数
	 */
	public function __construct() {
		parent::__construct();
		$this->names = array(
				array('id'=>'1', 'name'=>'关注'),
				array('id'=>'2', 'name'=>'约谈'),
				array('id'=>'3', 'name'=>'预约认购'),
				array('id'=>'4', 'name'=>'认购'),
//				array('id'=>'5', 'name'=>'本省'),
//				array('id'=>'6', 'name'=>'本市'),
		);
	}
	
	/**
	 * 自定义短信群发
	 * @author: liufei
	 */
	public function actionSmsSendMassList(){
		$var = $this->_get();

		//搜索参数处理
		$map = $this->_search(array('user_sendsmsmass_log'));
		if(!empty($_GET['start_time']) && !empty($_GET['end_time'])){
			$map['add_time'] = array( array('egt', strtotime($this->_get('start_time'))), array('lt', strtotime($this->_get('end_time')) + 86400) );
		}
		if(!empty($_GET['username'])){
			$uid = M('admin_user')->where("username = '{$this->_get('username')}'")->getField('uid');
			if($uid) $map['send_uid'] = $uid;
		}

		$res = M('user_sendsmsmass_log')->where($map)->order('id desc')->page();
		foreach($res['lists'] as $k => $v){
			$v['username'] = $v['send_uid'] ? M('admin_user')->where("uid = '{$v['send_uid']}'")->getField('username') : '<span class="red">系统</span>';
			$v['count'] = count(explode(',',$v['mobile']));
			$res['lists'][$k] = $v;
		}
		$var = array_merge($var,$res);

		//处理时间区间段input表单
		$var['input_starttime'] = helper_form::date('search[start_time]',$this->_get('start_time'));
		$var['input_endtime'] = helper_form::date('search[end_time]',$this->_get('end_time'));

		$this->assign($var);
		$this->display();		
	}

	
	/**
	 * 自定义短信  群发
	 * huangnan 2015.06.07
	 */
	public function actionSmsSendMass()
	{
		//发送短信
		if($this->_post())
		{
			$mobiles = $this->_post('mobiles');
			$message = $this->_post('message');
			$type = $this->_post('type',1);
			if(empty($mobiles) || empty($message))
			{
				$this->error("手机,内容不能为空！",url('/messagesend/smssendmass'));
			}
			$code = model_remind::getInfo($type,'en_name');
			$result = model_remind::sendMobileMass($mobiles,$message,$this->auth['uid'],$code);
			if($result)
			{
				$this->success("群发短信操作已执行",url('/messagesend/smssendmass'));
			}else
			{
				$this->error("群发短信操作未执行",url('/messagesend/smssendmass'));
			}
		} 
		
		//获取号码筛选条件
		$condition_checkbox = helper_form::checkbox('-1',$this->names,"name='ids[]'");
		//获取模版
		$rs = M('remind')->where(array("status"=>"1","enable_mobile"=>"1"))->findAll();
		//暂时显示自定义群发
		foreach($rs as $k=>$v)
		{
			if($v['id'] != 1)
			{
				unset($rs[$k]);
			}
		}
		$condition_select = helper_form::select('-1',$rs,"id='sms_tpl_num'");
		
		//城市
		$area_select = helper_form::ajaxarea('','',''," name='province_search' id='province_search'"," name='city_search' id='city_search'"," name='area_search' id='area_search'");
		$investment_checkbox = helper_form::checkbox('',array(1),"id='is_investment'");
		
		$this->assign('condition_checkbox',$condition_checkbox);
		$this->assign('condition_select',$condition_select);
		$this->assign('area_select',$area_select);
		$this->assign('investment_checkbox',$investment_checkbox);
		$this->setTitle("短信自定义发送");
		$this->display();
	}
	
	
	/**
	 * 自定义邮件群发
	 * @author: liufei
	 */
	public function actionEmailSendMassList(){
		$var = $this->_get();

		//搜索参数处理
		$map = $this->_search(array('user_sendemailmass_log'));
		if(!empty($_GET['start_time']) && !empty($_GET['end_time'])){
			$map['add_time'] = array( array('egt', strtotime($this->_get('start_time'))), array('lt', strtotime($this->_get('end_time')) + 86400) );
		}
		if(!empty($_GET['username'])){
			$uid = M('admin_user')->where("username = '{$this->_get('username')}'")->getField('uid');
			if($uid) $map['send_uid'] = $uid;
		}

		$res = M('user_sendemailmass_log')->where($map)->order('id desc')->page();
		foreach($res['lists'] as $k => $v){
			$v['username'] = $v['send_uid'] ? M('admin_user')->where("uid = '{$v['send_uid']}'")->getField('username') : '<span class="red">系统</span>';
			$v['count'] = count(explode(',',$v['email']));
			$res['lists'][$k] = $v;
		}
		$var = array_merge($var,$res);

		//处理时间区间段input表单
		$var['input_starttime'] = helper_form::date('search[start_time]',$this->_get('start_time'));
		$var['input_endtime'] = helper_form::date('search[end_time]',$this->_get('end_time'));

		$this->assign($var);
		$this->display();		
	}
	
	/**
	 * 自定义邮件 群发
	 * huangnan 2015.06.07
	 */
	public function actionEmailSendMass()
	{
		//发送邮件
		if($this->_post())
		{
			$emails = $this->_post('emails');
			$title = $this->_post('title');
			$message = $_POST['message'];
			$type = $this->_post('type',2);
			if(empty($emails) || empty($title) || empty($message))
			{
				$this->error("邮箱,标题,内容不能为空！",url('/messagesend/emailsendmass'));
			}	
			$result = model_remind::sendEmailMass($emails,$title,$message,$this->auth['uid'],$type);
			if($result)
			{
				$this->success("群发短信操作已执行",url('/messagesend/emailsendmass'));
			}else
			{
				$this->error("群发短信操作未执行",url('/messagesend/emailsendmass'));
			}
		}
		//获取邮箱筛选条件
		$condition_checkbox = helper_form::checkbox('-1',$this->names,"name='ids[]'");
		//获取模版
		$rs = M('remind')->where(array("status"=>"1","enable_email"=>"1"))->findAll();
		//暂时显示 自定义发送和      项目状态变更所需群发模版 6种  id 28--33
		foreach($rs as $k=>$v)
		{
			if(!in_array($v['id'],array('2','28','29','30','31','32','33')))
			{
				unset($rs[$k]);
			}
		}
		$condition_select = helper_form::select('-1',$rs,"id='email_tpl_num'");
		//获取编辑器
		$content_edit = helper_form::editor('message','message','','admin','90%');
		
		//城市
		$area_select = helper_form::ajaxarea('','',''," name='province_search' id='province_search'"," name='city_search' id='city_search'"," name='area_search' id='area_search'");
		$investment_checkbox = helper_form::checkbox('',array(1),"id='is_investment'");
	
		$this->assign('condition_checkbox',$condition_checkbox);
		$this->assign('condition_select',$condition_select);
		$this->assign('content_edit',$content_edit);
		$this->assign('area_select',$area_select);
		$this->assign('investment_checkbox',$investment_checkbox);
		$this->setTitle("邮件自定义发送");
		$this->display();
	}
	
	/**
	 * 获取模版内容
	 * huangnan 2015.06.07
	 */
	public function  actionAjaxGetTpl()
	{
		$tpl_id = $this->_post('tpl_id',1);
		$pid = $this->_post('pid',1);
		//获取模版替换所需数据
		$replace_data = $this->getReplaceData($pid);
		//生成模版内容
		$result = model_remind::getTplContent($tpl_id,$replace_data);
		
		if($result)
		{
			$data['status'] = '1';
			$data['msg'] = '查询成功';
			$data['info'] = $result;
			$this->ajaxReturn($data);
		}else 
		{
			$data['status'] = '0';
			$data['msg'] = '无数据';
			$data['info'] = '';
			$this->ajaxReturn($data);
		}
		
	}
	
	
	/**
	 * 手机邮箱 查询
	 * huangnan 2015.06.07
	 */
	public function  actionAjaxSearch()
	{
		// pid>0为按项目搜索  pid=-100 为按地区搜索  pid=-200 为按地区搜索且有投资记录
		$pid = $this->_post('pid');
		$ids = $this->_post('ids');
		$field = $this->_post('field');
		
		$result = $this->getUserInfo($pid,$ids,$field);
		
		
		$data['status'] = '1';
		$data['msg'] = '查询成功';
		$data['info'] = $result;
		$this->ajaxReturn($data);
	}

	/**
	 * 用户手机邮箱 查询
	 * huangnan 2015.06.08
	 */
	private function getUserInfo($pid,$ids,$field)
	{
		//筛选条件id    
		$ids_array = explode("|",$ids);
		$result = '';
		// 获取配置文件内指定添加的手机和邮箱   目前是 韩福娟
		$em_designated = C('em_designated');
		if($field == 'mobile')
		{
			$result = $em_designated['mobiles'].',';
		}elseif($field == 'email')
		{
			$result = $em_designated['emails'].',';
		}
		// 目前只查询已同步易宝的相关用户
		$condition = " and t2.is_idcard = 3";
		if($pid > 0)
		{
			foreach ($ids_array as $value)
			{
				if($value == 1)//关注
				{
					$userinfo  = M()->table('project_attention as t1')->field('t2.'.$field)
					->join('join user as t2 on t1.uid=t2.uid')
					->where('t1.pid='.$pid.$condition)
					->findAll();
				}elseif($value == 2)//约谈
				{
					$userinfo  = M()->table('project_relation_speak as t1')->field('t2.'.$field)
					->join('join user as t2 on t1.uid=t2.uid')
					->where('t1.pid='.$pid.$condition)
					->findAll();
				}elseif($value == 3)//预约认购
				{
					$userinfo  = M()->table('project_investment_pre as t1')->field('t2.'.$field)
					->join('join user as t2 on t1.uid=t2.uid')
					->where('t1.pid='.$pid.$condition)
					->findAll();
				}elseif($value == 4)//认购
				{
					$userinfo  = M()->table('project_investment as t1')->field('t2.'.$field)
					->join('join user as t2 on t1.uid=t2.uid')
					->where('t1.pid='.$pid.' and t1.status in (1,3)'.$condition)
					->findAll();
				}elseif($value == 5)//项目本省			
				{
					$userinfo  = M()->table('project as t1')->field('t2.'.$field)
					->join('join user as t2 on t1.province=t2.province')
					->where('t1.id='.$pid.$condition)
					->findAll();
				}elseif($value == 6)//项目本市
				{
					$userinfo  = M()->table('project as t1')->field('t2.'.$field)
					->join('join user as t2 on t1.city=t2.city')
					->where('t1.id='.$pid.$condition)
					->findAll();
				}
				//将查询的手机或邮箱  转成字符串格式
				if($userinfo)
				{
					foreach($userinfo as $val)
					{
						if($val[$field])
						{
							$result .= $val[$field].',';
						}
					}
				}
			}
		}elseif($pid == -100) 
		{
			krsort($ids_array);
			$f = array('province','city','area');
			foreach($ids_array as $key=>$val)
			{
				if($val > 0)
				{
					$where = array('is_idcard'=>3,$f[$key]=>$val);
					$userinfo = M('user')->field($field)->where($where)->field()->select();
					//将查询的手机或邮箱  转成字符串格式
					if($userinfo)
					{
						foreach($userinfo as $val)
						{
							if($val[$field])
							{
								$result .= $val[$field].',';
							}
						}
					}
					break;
				}
			}
		}elseif($pid == -200) 
		{
			krsort($ids_array);
			$f = array('province','city','area');
			foreach($ids_array as $key=>$val)
			{
				if($val > 0)
				{
					$sql = "select b.uid,b.{$field} 
							from 
								( select uid from project_investment group by uid ) a 
							join `user` b 
							on a.uid=b.uid and b.is_idcard=3 and b.{$field} is not null and b.{$f[$key]}={$val}";
					$userinfo = M()->query($sql);
					//将查询的手机或邮箱  转成字符串格式
					if($userinfo)
					{
						foreach($userinfo as $val)
						{
							if($val[$field])
							{
								$result .= $val[$field].',';
							}
						}
					}
					break;
				}
			}
		}
	
		if($result)
		{
			$result = substr($result,0,-1);
		}else
		{
			$result = "没有符合条件的数据";
		}		
		return $result;
	}
	
	/**
	 * 获取相关模版 返回内容
	 * @param int $type
	 * @param int $pid
	 */
	private function getReplaceData($pid)
	{
		$project = M('project')->where("id = {$pid}")->find();
		if(empty($project))
		{
			return '-1';
		}
		// 获取项目所在地区
		$province = D('area')->getAreas(array('id' => $project['province']),'shortname');
		$city = D('area')->getAreas(array('id' => $project['city']),'shortname');
		//获取预热项目
		$projects = D('project/project')->getProjects(3, 'ASC', 'plan');

		//添加推送项目所需相关数据
		$replace_data['www_url'] = $this->_url['www'];
		$replace_data['web_tpl'] = $this->_url['web_tpl'];
		$replace_data['area_text'] = $province[0]['shortname'].$city[0]['shortname'];
		$replace_data['project_name'] = $project['name'];
		$replace_data['img_cover'] = $this->_url['img2'].$project['img_cover'];
		$replace_data['funding_cycle'] = $project['funding_cycle'];
		$replace_data['finance_total'] = $project['finance_total']/10000;
		$replace_data['lest_finance'] = $project['lest_finance']/10000; 
		//已融资金额  项目方+投资方   后台可修改
		$replace_data['finance_amount'] = $project['finance_amount']; 
		//融资完成 日期格式  
		$replace_data['amount_success_date'] = date('Y年m月d日H:i点',$project['finsh_time']);
		//融资失败 日期格式  
		$replace_data['amount_fail_date'] = date('Y年m月d日H:i点',$project['fail_time']);
		//预热开始 日期格式  
		$replace_data['preheat_begin_date'] = date('Y年m月d日H:i点',$project['preheat_begin_time']);
		//融资开始 日期格式
		$replace_data['amount_begin_date'] = date('Y年m月d日H:i点',$project['amount_begin_time']);
		//预热失败 日期格式
		$replace_data['preheat_fail_date'] = date('Y年m月d日H:i点',time());
		//融资进度  
		$replace_data['rzjd'] = intval($project['finance_amount']/$project['finance_total']*100).'%';
		//已经融资的天数
		$replace_data['yjrzts'] = ceil((time()-$project['amount_begin_time'])/86400);
		
		$replace_data['preheat_begin_y'] = date('Y',$project['preheat_begin_time']);
		$replace_data['preheat_begin_m'] = date('m',$project['preheat_begin_time']);
		$replace_data['preheat_begin_d'] = date('d',$project['preheat_begin_time']);
		$replace_data['preheat_begin_t'] = date('H:i',$project['preheat_begin_time']);
		$replace_data['amount_begin_y'] = date('Y',$project['amount_begin_time']);
		$replace_data['amount_begin_m'] = date('m',$project['amount_begin_time']);
		$replace_data['amount_begin_d'] = date('d',$project['amount_begin_time']);
		$replace_data['amount_begin_t'] = date('H:i',$project['amount_begin_time']);
		//邮件下方4个小图调取预热项目
		$replace_data['project_url_0'] = $this->_url['www']."project/detail/project_id/".$projects['0']['id'];
		$replace_data['project_img_0'] = helper_tool::getThumbImg($projects['0']['img_cover'],162,108);
		$replace_data['project_name_0'] = $projects['0']['name'];
		$replace_data['project_url_1'] = $this->_url['www']."project/detail/project_id/".$projects['1']['id'];
		$replace_data['project_img_1'] = helper_tool::getThumbImg($projects['1']['img_cover'],162,108);
		$replace_data['project_name_1'] = $projects['1']['name'];
		$replace_data['project_url_2'] = $this->_url['www']."project/detail/project_id/".$projects['2']['id'];
		$replace_data['project_img_2'] = helper_tool::getThumbImg($projects['2']['img_cover'],162,108);
		$replace_data['project_name_2'] = $projects['2']['name'];
		$replace_data['project_url_3'] = $this->_url['www']."project/detail/project_id/".$projects['3']['id'];
		$replace_data['project_img_3'] = helper_tool::getThumbImg($projects['3']['img_cover'],162,108);
		$replace_data['project_name_3'] = $projects['3']['name'];

		return $replace_data;
	}
	
}
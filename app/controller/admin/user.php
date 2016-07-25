<?php
/**
 * controller模型 会员管理
 */
class controller_admin_user extends controller_admin_abstract{

	//查看用户列表
	public function actionIndex(){
            $search = $this->_post("search");
            //用户ID
            $id = $search["id"];
            if (!empty($id)) {
                $map['id'] = $id;
            }
            //用户姓名
            $name = $search["name"];
            if (!empty($name)) {
                $map['name'] = $name;
            }
            
            $res = M("user")->where($map)->order('id desc')->page();
            $this->assign($res);
            $this->setReUrl();
            $this->display();
	    $this->display();	
	}
        /*
        * 添加会员
        * @author tianxiang
        * @return 无
        */
	public function actionAdd() {
		
		$user = D('user');
		if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
			$data = $this->_post('data');
			$data['regist_time'] = $data['update_time'] = time();
			$data['regist_ip']   = getIp();
			$data['password']    = helper_tool::pwdEncode($data['password']);
			$data['source']    = C('sys_global_source');
			$id = $user->data($data)->add();
			$this->savelog('添加会员【uid:'.$id.' | ' . $data['username'] . '】');
			$this->success('会员添加成功！',url('index'));
		}
		
		$this->assign($var);  
		$this->display();
	}
	/**
	 * 
	 * 修改会员信息
	 * @author liurengang
	 * @date   2015/3/11 星期三
	 * 
	 */
	public function actionEdit() {
		
		$user = D('user');
		if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
			$data = $this->_post('data');
			$password = $this->_post('password');
			$userinfo_old = $user->getInfo($data['uid']);
			if(empty($data['email'])) $data['email'] = null;
			if(empty($data['mobile'])) $data['mobile'] = null;
			$map = array('uid' => $data['uid']);
			$user->where($map)->save($data);
			if($data['realname'] && M('userBody')->where($map)->count()){
				M('userBody')->where($map)->save(array('realname' => $data['realname']));
			}
			$user->getInfo($data['uid'],'',true);
			
			//删除原来手机和邮箱缓存
			if(!empty($data['mobile'])) S('model_user_info_'.$userinfo_old['mobile'], null);
			if(!empty($data['email'])) S('model_user_info_'.$userinfo_old['email'], null);
			
			if(!empty($password)){
				model_user::setLoginPwd($data['uid'], trim($password));
			}
			$this->savelog('修改会员id:【' . $data['uid'] . '】');
			$this->success('会员修改成功！', url('index'));
		}
		
		$id  = $this->_get('id');
		if (empty($id)) $this->error('参数错误');
		$var['info'] = $user->getInfo($id);
		
		$this->assign($var);
		$this->display();
	}
	/**
	 * 删除会员
	 * 
	 * @author liurengang
	 * @date   2015/3/11 星期三
	 * 
	 */
	public function actionDelete() {
	
		if(isset($_GET['do']) && $_GET['do']=='all'){
			$ids  =  $this->_post('ids');
			if(!empty($ids)){
				foreach($ids as $uid){
					$userinfo  =  model_user::getInfo($uid);
					$this->saveRecycle('user', $uid, $userinfo);
					D('user')->userDelete($uid);
					model_user::getInfo($uid,'',true);
				}
			}
			$ids  =  implode(',',$ids);
			$this->savelog('批量删除会员【uid:' . $ids . '】');
			$this->success('批量删除成功！');
		}else{
			if (empty($_GET['uid'])) $this->error('参数错误');
			$uid       =  $this->_get('uid');
			$userinfo  =  model_user::getInfo($uid);
			$this->saveRecycle('user', $uid, $userinfo);
			if(D('user')->userDelete($uid)){
				model_user::getInfo($uid,'',true);
				
				$this->savelog('删除会员【uid:' . $uid . '】');
				$this->success('删除成功！');
			}
		}		
	}
	
	/**
	 * 
	 * 会员详情
	 * @author liurengang
	 * @date   2015/3/11 星期三
	 */
	public function actionInfo(){
		if (empty($_GET['uid'])) $this->error('参数错误');
		$uid  = $this->_get('uid');
		$info = D('user')->getInfo($uid);
		
		$info['add_time'] = date('Y-m-d H:i:s',$info['add_time']);
		
		/**
		 * 获取用户资产统计
		 * 
		 * */
		$property_info = D('account')->getInfo($uid);
		
		/**
		 * 获取用户投资的所有项目数
		 * 
		 * */
		$investmentPronums = D('user')->getInvestmentProNums($uid);
		
		//获取易宝余额
		$yeepay_account = array();
		$is_account_tips = false;
		if($info['is_idcard'] == 3){
			$yeepay = new helper_yeepay();
			$yeepay_info = $yeepay->yu_e_cha_xun(array('platformUserNo' => $uid));
			$arr = array();
			if(isset($yeepay_info['balance'])){
				$arr['total'] = $yeepay_info['balance'];
				$arr['amount'] = $yeepay_info['availableAmount'];
				$arr['freeze'] = $yeepay_info['freezeAmount'];
			}
			$yeepay_account = $arr;
			if($property_info['total'] != $yeepay_account['total'] || $property_info['amount'] != $yeepay_account['amount']){
				$is_account_tips = true;
			}
		}
		
		$this->assign('is_account_tips',$is_account_tips);
		$this->assign('property_info',$property_info);
		$this->assign('investmentPronums',$investmentPronums);
		$this->assign('info',$info);
		$this->assign('yeepay_account',$yeepay_account);
		$this->display();
	}
	
	/**
	 * 
	 * 会员组列表
	 * @author liurengang
	 * @date   2015/3/12 星期四
	 */
	public function actionUserGroup(){
		$var = $this->_get();
		$res = M('UserGroup')->where($map)->order('sort asc,id asc')->page();
		if(!empty($res['lists'])){
			foreach($res['lists'] as $key => &$val){
				$val['add_time'] = date('Y-m-d H:i:s',$val['add_time']);
			}
			unset($val);
		}

		$var = array_merge($var,$res);
		
		//处理时间区间段input表单
		$var['input_starttime'] = helper_form::date('search[start_time]',$this->_get('start_time'));
		$var['input_endtime']   = helper_form::date('search[end_time]',$this->_get('end_time'));
		$var['group_list']      = M('UserGroup')->order('id asc')->select();
		$this->assign($var);
		$this->setReUrl();
		$this->display();
	}
	
	/**
	 * 添加会员组信息
	 * 
	 * @author liurengang
	 * @date   2015/3/12 星期四
	 * 
	 */
	public function actionUserGroupAdd() {
		if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
			$data = $this->_post('data');
			$data['ip']   = getIp();			
			$data['add_time'] = time();
			M('userGroup')->data($data)->add();
			$this->savelog('添加会员组【' . $data['name'] . '】');
			$this->success('会员组添加成功！',$this->getReUrl());
		}
		$this->display();
	}
	
	
	/**
	 * 
	 * 修改会员组信息
	 * @author liurengang
	 * @date   2015/3/12 星期四
	 * 
	 */
	public function actionUserGroupEdit() {
		$userGroup = M('userGroup');
		if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
			$data = $this->_post('data');
			$userGroup->where(array('id' => $data['id']))->save($data);
			$this->savelog('修改会员组【' . $data['name'] . '】');
			$this->success('会员组修改成功！', url('usergroup'));
		}
		
		//排序处理
		if (isset($_POST['do']) && $_POST['do'] == 'sort'){
			$rows = $this->_post('sort');
			foreach($rows as $k=>$v){
				M('UserGroup')->where(array('id' => $k))->save(array('sort' => $v));
			}
			$this->savelog("批量更新会员组排序");
			$this->success('排序更新成功');
		}
		
		$var = $this->_get();
		$id  = $this->_get('id');
		if (empty($id)) $this->error('参数错误');
		$var['group_info'] = $userGroup->where(array('id' => $id))->find();
		$this->assign($var);
		$this->display();
	}
	
	/**
	 * 删除会员组
	 * 
	 * @author liurengang
	 * @date   2015/3/11 星期三
	 * 
	 */
	public function actionUserGroupDelete() {	
		if (empty($_GET['id'])) $this->error('参数错误');
		$id = $this->_get('id');
		$num = M('user')->where(array('group_id' => $id))->count();
		if (empty($num)) {
			$this->savelog('删除会员组【id:' . $id . '】');
			$this->success('删除会员组成功！');
		} else {
			$this->error('请先清空该会员组下的会员，不能删除！');
		}
	}
	
	/**
	 * 
	 * 投资人身份验证（会员）
	 * @author liurengang
	 * @date   2015/3/13 星期五
	 */
	public function actionUseridentity(){
		$var = $this->_get();
		//搜索参数处理
		$map = $this->_search(array('UserBody'));
		if(!empty($_GET['start_time']) && !empty($_GET['end_time'])){
			$map['add_time'] = array( array('egt', strtotime($this->_get('start_time'))), array('elt', strtotime($this->_get('end_time'))+86400) );
		}
		if(!empty($_GET['u_body_num']) && !empty($_GET['u_body_num'])) $map['u_body_num']  =  trim($_GET['u_body_num']);
		$res = M('UserBody')->where($map)->order('add_time desc,check_time desc')->page();
		if(!empty($res['lists'])){
			foreach($res['lists'] as $key => &$val){
				if($val['add_time'] && !empty($val['add_time'])){
					$val['add_time']     = date('Y-m-d H:i:s',$val['add_time']);
				}else{
					$val['add_time']     = '';
				}
				if($val['check_time'] && !empty($val['check_time'])){
					$val['check_time']   = date('Y-m-d H:i:s',$val['check_time']);
				}else{
					$val['check_time']   = '';
				}
				if($var['u_body_photo']){
					$var['u_body_photo'] = model_attachment::getSignUrl($var['u_body_photo']);
				}
				if($var['u_body_photof']){
					$var['u_body_photof'] = model_attachment::getSignUrl($var['u_body_photof']);
				}
				if(!empty($val['uid'])){
					$userinfo = M('user')->field('is_idcard')->where(array('uid'=>$val['uid']))->find();
					$arr = model_user::$isidcard_arr[$userinfo['is_idcard']];
					$val['identity_tips'] = '<span '.$arr['style'].'>'.$arr['name'].'</span>';
					$val['is_idcard']     = $userinfo['is_idcard'];
				}
				unset($val);
			}
			
		}
		
		$var = array_merge($var,$res);
		
		//处理时间区间段input表单
		$var['input_starttime'] = helper_form::date('search[start_time]',$this->_get('start_time'));
		$var['input_endtime']   = helper_form::date('search[end_time]',$this->_get('end_time'));
		//审核状态下拉框
		//$var['status_select']   = helper_form::select($this->_get('status'),model_user::$identity_arr,'name="search[status]"','全部');
		
		$this->assign($var);
		$this->display();
	}
	
	/**
	 * 
	 * 会员身份认证审核
	 * @author liurengang
	 * @date   2015.04.03
	 */
	public function actionSetStatus(){
		
		if (empty($_GET['id'])) $this->error('参数错误');
		
		$id  = $this->_get('id') ? $this->_get('id'): 0;
		
		$indentity_info = M('userBody')->where(array('id'=>$id))->find();
		if($indentity_info['u_body_photo']){
			$indentity_info['u_body_photo'] = model_attachment::getSignUrl($indentity_info['u_body_photo']);
		}
		if($indentity_info['u_body_photof']){
			$indentity_info['u_body_photof'] = model_attachment::getSignUrl($indentity_info['u_body_photof']);
		}

		$userinfo       = D('user')->field('is_idcard')->where(array('uid'=>$indentity_info['uid']))->find();
		
		$indentity_info['is_idcard'] = $userinfo['is_idcard'];
		
		if(!empty($_POST['do']) && $_POST['do'] == 'dosubmit'){
			
			$data['is_idcard']   = $_POST['is_idcard'];
			$data2['check_time'] = time();
			M('user')->where(array('uid' => $indentity_info['uid']))->save($data);
			M('userBody')->where(array('id'=>$id))->save($data2);
			$remark   = $_POST['remark'];
			//该身份认证状态是根据该会员对应的实名认证状态为基准
			$res      = D('CheckLog')->addLog('user_body',$indentity_info['id'],'is_idcard',$remark,$indentity_info['is_idcard'],$data['is_idcard']);
			if($res){
				$this->success('操作成功',url('useridentity'));
				/*
				//实名认证的
				if ( $data['is_idcard'] == 2 ) {
					$param = array('time'=>$data2['check_time']);
					//实名认证成功审核后发送站内信息
					D('remind')->send($indentity_info['uid'],'realname_verify',$param);
				}*/
			}
		}
		$checkLogInfo = M('checkLog')->where(array('table_id'=>$id))->find();
		
		//获取审核日志
		if(empty($checkLogInfo)){
			$checkLogInfo['remark'] = '';
		}
		
		$this->assign('indentity_info',$indentity_info);
		$this->assign('checkLogInfo',$checkLogInfo);
		$this->display();
	}

    /**
     *
     * 会员企业身份认证验证
     */
    public function actionUserEnterprise(){
        $var = $this->_get();
        //搜索参数处理
        $map = $this->_search(array('userEnterpriseInfo'));
        if(!empty($_GET['start_time']) && !empty($_GET['end_time'])){
            $map['add_time'] = array( array('egt', strtotime($this->_get('start_time'))), array('elt', strtotime($this->_get('end_time'))+86400) );
        }
        $res = M('userEnterpriseInfo')->where($map)->order('add_time desc,check_time desc')->page();
        if(!empty($res['lists'])){
            foreach($res['lists'] as $key=>$val){
                if($val['add_time'] && !empty($val['add_time'])){
                    $res['lists'][$key]['add_time']     = date('Y-m-d H:i:s',$val['add_time']);
                }else{
                    $res['lists'][$key]['add_time']     = '';
                }
                if($val['check_time'] && !empty($val['check_time'])){
                    $res['lists'][$key]['check_time']   = date('Y-m-d H:i:s',$val['check_time']);
                }else{
                    $res['lists'][$key]['check_time']   = '';
                }
                if($var['id_photo']){
                    $var[$key]['id_photo'] = model_attachment::getSignUrl($var['id_photo']);
                }
                if($var['atta_yyzz']){
                    $res['lists'][$key]['atta_yyzz'] = model_attachment::getSignUrl($var['atta_yyzz']);
                }
                if($var['atta_swdj']){
                    $res['lists'][$key]['atta_swdj'] = model_attachment::getSignUrl($var['atta_swdj']);
                }
                if($var['atta_zzjg']){
                    $res['lists'][$key]['atta_zzjg'] = model_attachment::getSignUrl($var['atta_zzjg']);
                }
                if($var['atta_yhkh']){
                    $res['lists'][$key]['atta_yhkh'] = model_attachment::getSignUrl($var['atta_yhkh']);
                }
                $res['lists'][$key]['type_tips'] = model_UserEnterpriseInfo::$type_arr[$val['type']]['name'];

                if(!empty($val['uid'])){
                    $userinfo = M('user')->field('is_enterprise')->where(array('uid'=>$val['uid']))->find();
                    $arr = model_UserEnterpriseInfo::$isenterprise_arr[$userinfo['is_enterprise']];
                    $res['lists'][$key]['enterprise_tips'] = '<span '.$arr['style'].'>'.$arr['name'].'</span>';
                    $res['lists'][$key]['is_enterprise'] = $userinfo['is_enterprise'];
                }
                //unset($val);
            }
        }

        //处理时间区间段input表单
        $res['input_starttime'] = helper_form::date('search[start_time]',$this->_get('start_time'));
        $res['input_endtime']   = helper_form::date('search[end_time]',$this->_get('end_time'));
        //审核状态下拉框
        //$res['status_select']   = helper_form::select($this->_get('status'),model_UserEnterpriseInfo::$isenterprise_arr,'name="search[is_enterprise]"','全部');

        $var = array_merge($var,$res);

        $this->assign($var);
        $this->display();
    }

    /**
     *
     * 会员企业身份认证审核
     */
    public function actionSetEnterpriseStatus(){

        if (empty($_GET['id'])) $this->error('参数错误');

        $id  = $this->_get('id') ? $this->_get('id'): 0;

        $indentity_info = M('userEnterpriseInfo')->where(array('id'=>$id))->find();
        if($indentity_info['id_photo']){
            $indentity_info['id_photo'] = model_attachment::getSignUrl($indentity_info['id_photo']);
        }
        if($indentity_info['atta_yyzz']){
            $indentity_info['atta_yyzz'] = model_attachment::getSignUrl($indentity_info['atta_yyzz']);
        }
        if($indentity_info['atta_swdj']){
            $indentity_info['atta_swdj'] = model_attachment::getSignUrl($indentity_info['atta_swdj']);
        }
        if($indentity_info['atta_zzjg']){
            $indentity_info['atta_zzjg'] = model_attachment::getSignUrl($indentity_info['atta_zzjg']);
        }
        if($indentity_info['atta_yhkh']){
            $indentity_info['atta_yhkh'] = model_attachment::getSignUrl($indentity_info['atta_yhkh']);
        }
        $indentity_info['type'] = model_UserEnterpriseInfo::$type_arr[$indentity_info['type']]['name'];

        $userinfo = D('user')->field('is_enterprise')->where(array('uid'=>$indentity_info['uid']))->find();

        $indentity_info['is_enterprise'] = $userinfo['is_enterprise'];

        if(!empty($_POST['do']) && $_POST['do'] == 'dosubmit'){

            $data['is_enterprise']   = $_POST['is_enterprise'];
            $data2['check_time'] = time();
            M('user')->where(array('uid' => $indentity_info['uid']))->save($data);
            M('userEnterpriseInfo')->where(array('id'=>$id))->save($data2);
            $remark   = $_POST['remark'];
            //该身份认证状态是根据该会员对应的实名认证状态为基准
            $res      = D('CheckLog')->addLog('user_enterprise_info',$indentity_info['id'],'is_enterprise',$remark,$indentity_info['is_enterprise'],$data['is_enterprise']);
            if($res){
                model_user::getInfo($indentity_info['uid'], '', true);
                $this->success('操作成功',url('userenterprise'));
                /*
                //实名认证的
                if ( $data['is_enterprise'] == 2 ) {
                    $param = array('time'=>$data2['check_time']);
                    //实名认证成功审核后发送站内信息
                    D('remind')->send($indentity_info['uid'],'realname_verify',$param);
                }*/
            }
        }
        $checkLogInfo = M('checkLog')->where(array('table_id'=>$id,'table'=>'user_enterprise_info'))->order('add_time desc')->find();

        //获取审核日志
        if(empty($checkLogInfo)){
            $checkLogInfo['remark'] = '';
        }

        $this->assign('indentity_info',$indentity_info);
        $this->assign('checkLogInfo',$checkLogInfo);
        $this->display();
    }

    /*
     * 企业认证附件下载预览
     */
    public function actionDownFile(){
        //文件下载
        $id = trim($this->_get('id'));
        $filetype = trim($this->_get('filetype'));
        $info = D('UserEnterpriseInfo')->getUserEnterpriseInfo(array('id'=>$id));
        $file = model_attachment::getSignUrl($info[$filetype]);
        if(!fopen( $file,'r')){
            $this->error('该附件不存在');
        }

        header('Content-Type:text/html; charset=utf-8');
        header('Location: '.$file);
        exit;
    }
	
	/**
	 * 
	 * 会员收货地址列表
	 * @author liurengang
	 * @date   2015/3/13 星期五
	 */
	public function actionDeliveryAddress(){
		
		$var = $this->_get();
		//搜索参数处理
		$map = $this->_search(array('UserContact'));
		if(!empty($_GET['start_time']) && !empty($_GET['end_time'])){
			$map['add_time'] = array( array('egt', strtotime($this->_get('start_time'))), array('elt', strtotime($this->_get('end_time'))+86400) );
		}
		$map['is_delete'] = " is_delete = 0 ";
		$res = M('UserContact')->where($map)->order('add_time DESC')->page();
		if(!empty($res['lists'])){
			foreach($res['lists'] as $key => &$val){
				if($val['add_time'] && !empty($val['add_time'])){
					$val['add_time']   = date('Y-m-d H:i:s',$val['add_time']);
				}else{
					$val['add_time']   = '';
				}
				if($val['address'] && !empty($val['address'])){
					$val['address'] = htmlspecialchars($val['address']);
				}
				if($val['zip'] && !empty($val['zip'])){
					$val['zip'] = htmlspecialchars($val['zip']);
				}else{
					$val['zip'] = '';
				}
				
			}
			unset($val);
		}

		$var = array_merge($var,$res);
		
		//处理时间区间段input表单
		$var['input_starttime'] = helper_form::date('search[start_time]',$this->_get('start_time'));
		$var['input_endtime']   = helper_form::date('search[end_time]',$this->_get('end_time'));
		//审核状态下拉框
		$var['status_select']   = helper_form::select($this->_get('status'),model_user::$identity_arr,'name="search[status]"','全部');
		
		$this->assign($var);
		$this->display();
	}
	
	
	/**
	 * 添加会员地址
	 * 
	 * @author liurengang
	 * @date   2015/3/13  星期五
	 * 
	 */
	public function actionDeliveryAddressadd() {
		
		if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
			$data = $this->_post('data');
			$data['ip']   = getIp();
			
			$data['add_time'] = time();
			M('UserContact')->data($data)->add();
			$this->savelog('添加会员收货地址【' . $data['username'] . '】');
			if(isset($_POST['uid']) && !empty($_POST['uid'])){
				$this->success('添加成功！',url('deliveryaddress?uid='.$_POST['uid']));
			}else{
				$this->success('添加成功！',url('deliveryaddress'));
			}
			
		}
		$id  = $this->_get('uid');
		$var['info'] = D('User')->getInfo($id);

		$this->assign($var);	
		$this->display();
	}
	
	
	/**
	 * 修改会员地址
	 * 
	 * @author liurengang
	 * @date   2015/3/13  星期五
	 * 
	 */
	public function actionDeliveryAddressEdit() {
		
		$UserContact = M('UserContact');
		if (isset($_POST['do']) && $_POST['do'] == 'dosubmit') {
			$data = $this->_post('data');
			$UserContact->where(array('id' => $data['id']))->save($data);
			$this->savelog('修改会员收货地址【' . $data['username'] . '】');
			if(isset($_POST['uid']) && !empty($_POST['uid'])){
				$this->success('添加成功！',url('deliveryaddress?uid='.$_POST['uid']));
			}else{
				$this->success('修改成功！', url('deliveryaddress'));
			}
		}
		
		$id  = $this->_get('id');
		if (empty($id)) $this->error('参数错误');
		$var['contact_info']   = $UserContact->where(array('id'=>$id))->find();
		
		$this->assign($var);
		$this->display();
	}
	
	/**
	 * 删除、批量删除会员收货地址
	 * 
	 * @author liurengang
	 * @date   2015/3/13 星期五
	 * 
	 */
	public function actionDeliveryaddressDelete() {
	
		if(isset($_GET['do']) && $_GET['do']=='all'){
			$ids = $this->_post('ids');
			if(!empty($ids)){
				foreach($ids as $id){
					D('user')->deliveryaddressDelete($id);
				}
			}
			$ids = implode(',',$ids);
			$this->savelog('批量删除会员收货地址【id:' . $ids . '】');
			$this->success('批量删除成功！');
		}else{
			if (empty($_GET['id'])) $this->error('参数错误');
			$id = $this->_get('id');
			if(D('user')->deliveryaddressDelete($id)){
				$this->savelog('删除会员收货地址【id:' . $id . '】');
				$this->success('删除成功！');
			}		
		}		
	}
	
	/*
	 * 模拟用户登录
	 * @author liufei
	 * @return string
	 */
	public function actionUserLogin(){
		$var = $this->_get();
		if(empty($var['uid'])) $this->error('参数错误');
		$info = model_user::getInfo($var['uid']);
		if(empty($info)) $this->error('用户名不存在');
		
		D('user')->setLogin($var['uid'],86400,0);
		$this->success('登录成功',url('user-index/index'));
	}
	
	
	/**
	 * ajax验证系统用户名是否可用
	 * 
	 * @author liurengang
	 * @date   2015/3/13 星期五
	 * 
	 */
	public function actionAjaxchusername(){
	
		$username = isset($_GET['username']) ? $_GET['username'] : '';
		if(empty($username)){
			exit('0');
		}
		
		$info  = M('user')->where(array('username' => $username))->count();
		$info2 = M('user')->where(array('username' => $username))->count();
		if($info > 0 || $info2 > 0){
			exit('1');
		}else{
			exit('0');
		}
	}
	/**
	 * ajax验证邮箱是否可用
	 * 
	 * @author liurengang
	 * @date   2015/3/13 星期五
	 * 
	 */
	public function actionAjaxchkemail(){
		
		$email = isset($_GET['email']) ? $_GET['email'] : '';
		if(empty($email)){
			exit('0');
		}

		$info = M('user')->where(array('email' => $email))->count();
		if($info){
			exit('1');
		}else{
			exit('0');
		}
	}
	
	/**
	 * ajax验证手机是否可用
	 * 
	 * @author liurengang
	 * @date   2015/3/13 星期五
	 * 
	 */
	public function actionAjaxchMobile(){
		
		$mobile = isset($_GET['mobile']) ? $_GET['mobile'] : '';
		if(empty($mobile)){
			exit('0');
		}

		$info = M('user')->where(array('mobile' => $mobile))->count();
		if($info){
			exit('1');
		}else{
			exit('0');
		}
	}
	
	/**
	 * 用户高级认证
	 *
	 * @author lujiaming
	 * @date   2015/11/20
	 */
	public function actionAuthentication(){
		$m = M('user_authentication');
		$uid = (int)$this->_get('uid');
		if ($uid === 0) {
			$this->error('参数错误');
		}
		$res = $m->where('uid='.$uid)->find();
		if (empty($res)) {
			$this->error('无相关信息');
		}
		// 检测是否post过来数据
		if ($this->isPost()) {
			$post = $this->_post();
			$post['check_time'] = time();
			$flag = false;
			if (!empty($post))
				$flag = $m->save($post);

			if ($flag > 0) {
				if ($res['company_status'] != $post['company_status']){
                    $this->savelog("审核机构领投人【uid:{$uid}】为：".model_userauthentication::$status_arr[$post['company_status']]['name'].'，状态：'.$post['company_status']);
                }
                if ($res['person_status'] != $post['person_status']){
                    $this->savelog("审核明星领投人【uid:{$uid}】为：".model_userauthentication::$status_arr[$post['person_status']]['name'].'，状态：'.$post['person_status']);
               }
            }
			if ($flag === false) {
				$this->error('更新失败'.$m->getLastSql());
			} else {
				$this->success('更新成功');
			}
		}
		$this->assign($res);
		$this->display();
	}

    /**
     *
     * 领投合投列表
     * @date   2015/3/13 星期五
     */
    public function actionAuthenticationList(){
        $var = $this->_get();
        //搜索参数处理
        $map = $this->_search(array('UserAuthentication'));
        if(!empty($_GET['start_time']) && !empty($_GET['end_time'])){
            $map['add_time'] = array( array('egt', strtotime($this->_get('start_time'))), array('elt', strtotime($this->_get('end_time'))+86400) );
        }

        $res = M('UserAuthentication ')->where($map)->order('add_time desc')->page();
        if(!empty($res['lists'])){
            foreach($res['lists'] as $key => &$val){
                if($val['add_time'] && !empty($val['add_time'])){
                    $val['add_time']     = date('Y-m-d H:i:s',$val['add_time']);
                }else{
                    $val['add_time']     = '';
                }
                if(!empty($val['uid'])){
                    $userinfo = M('user')->field('username')->where(array('uid'=>$val['uid']))->find();
                    $val['username']     = $userinfo['username'];
                    $arr = model_userauthentication::$status_arr[$val['company_status']];
                    $arr_person = model_userauthentication::$status_arr[$val['person_status']];
                    $val['company_status'] = '<span '.$arr['style'].'>'.$arr['name'].'</span>';
                    $val['person_status'] = '<span '.$arr_person['style'].'>'.$arr_person['name'].'</span>';
                }
                unset($val);
            }

        }

        $var = array_merge($var,$res);

        //处理时间区间段input表单
        $var['input_starttime'] = helper_form::date('search[start_time]',$this->_get('start_time'));
        $var['input_endtime'] = helper_form::date('search[end_time]',$this->_get('end_time'));
        //机构领投
        $var['company_select'] =  helper_form::select($this->_get('company_status'),model_userauthentication::$status_arr,'name="search[company_status]"','全部');
        //明星领投
        $var['person_select'] =  helper_form::select($this->_get('person_status'),model_userauthentication::$status_arr,'name="search[person_status]"','全部');

        $this->assign($var);
        $this->display();
    }
	
	/**
	 * 查看用户手机
	 * @author: liufei
	 */
	public function actionLookMobile(){
		$uid = $this->_get("uid",'');		
		$info = model_admin_user::lookMobile($uid, $this->auth['uid'], true);
		if(empty($info)) $this->error('您没权限查看或用户不存在');
		$this->assign('info',$info);
		$this->display();
	}
	
	/**
	 * 查看用户邮箱
	 * @author: liufei
	 */
	public function actionLookEmail(){
		$uid = $this->_get("uid",'');		
		$info = model_admin_user::lookEmail($uid, $this->auth['uid'], true);
		if(empty($info)) $this->error('您没权限查看或用户不存在');
		$this->assign('info',$info);
		$this->display();
	}
	
	/**
	 * 查看用户身份证号
	 * @author: liufei
	 */
	public function actionLookIdcard(){		
		$uid = $this->_get("uid",'');		
		$info = model_admin_user::lookIdcard($uid, $this->auth['uid'], true);
		if(empty($info)) $this->error('您没权限查看或用户不存在');
		$this->assign('info',$info);
		$this->display();
	}
	
}

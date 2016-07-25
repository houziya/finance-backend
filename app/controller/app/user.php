<?php
/**
 * 用户控制器
 * @author Baijiansheng
 */
class controller_app_user extends controller_app_abstract {
    private $page_num;

    const LOAN_MOBILE_CODE = 'LOAN_MOBILE_CODE'; //手机验证码cache的KEY名称

    const REGISTER_EMAIL_CODE = 'REGISTER_EMAIL_CODE'; //邮箱验证码cache的KEY名称

    const EMAIL_CODE_TIME = 60; //发送注册手机验证码间隔时间,单位:秒

    const CHECK_OUT_TIME = 1800; //验证码过期时间，单位：秒

    const RETRIEVE_PWD_MOBILE_CODE = 'RETRIEVE_PWD_MOBILE_CODE'; //手机验证码redis的KEY名称

	public function __construct() {
		parent::__construct();
        //默认每页显示数量
        $this->page_num = 10;
	}
	/*
	 * 获取用户信息
	 * @return array
	 * @author Baijiansheng
	 */
	public function actionGetUserInfo(){
        $this->checkLogin();

        $arr_img = C('url');
        $uid = $this->_userinfo['uid'];

        $user = D('user');
        $userBody = D('UserBody');
        $field = 'realname,u_body_photo,u_body_photof,u_body_num';

        //基本信息
        $info = model_user::getInfo($uid);
        $body_info = $userBody->getUserBody(array('uid'=>$uid),$field);
        $data['uid'] = $info['uid'] ? $info['uid'] :"";
        $data['realname'] = $body_info['realname'] ? $body_info['realname'] : $info['realname'];
        $data['usernick'] = $info['usernick'] ? $info['usernick'] :"";
        $data['email'] = $info['email'] ? $info['email'] : "";
        $data['username'] = $info['username'] ? $info['username'] : "";
        $data['is_username'] = $info['is_username'];
        $data['mobile'] = $info['mobile'] ? $info['mobile'] : "";
        $data['password'] = $info['password'] ? $info['password'] : "";
        $data['sex'] = model_user::$sex_arr[$info['sex']]['name'];
        $data['face'] = $info['face'] ? $arr_img['img2'].$info['face']:'';
        $data['is_idcard'] = $info['is_idcard'] ? $info['is_idcard'] : "";
        //$data['is_bindbank'] = $info['is_bindbank'] ? $info['is_bindbank'] : "";
		$data['is_bindbank'] = $info['is_bindbank'] +0;
        $data['is_auto_yeepay'] = $info['is_auto_yeepay'] ? $info['is_auto_yeepay'] :"";

        $cachename = 'app_user_GetUserInfo_' . $uid;
        $cache_data = S($cachename);
        if(!empty($data2)) $this->ajax(-101, '获取成功',$cache_data);
        //认证信息
        $body_info = $userBody->getUserBody(array('uid'=>$uid),$field);
        $body_info['u_body_photo'] = $body_info['u_body_photo'] ? $arr_img['img2'].$body_info['u_body_photo'] : '';
        $body_info['u_body_photof'] = $body_info['u_body_photof'] ? $arr_img['img2'].$body_info['u_body_photof'] : '';
        $body_info['u_body_num'] = $body_info['u_body_num'] ? substr_replace($body_info['u_body_num'],'**************','1','14') : '';
        if(!empty($body_info)) $data = array_merge($data,$body_info);

        //获取当前用户投资项目总数
        $nums = model_project_project::getProjectNumsByUidCache($uid);
        //未读私信条数,投资数量,
        $data['newpm'] = intval($info['newpm']);
        $data['investor_num'] = D('project/project')->getUserProjects($uid, 2, -1, true);
        //获取当前用户发布项目总数
        $data['publish_num'] = D('project/project')->getUserProjects($uid, 'publish', -1, true);
        //获取当前用户关注项目总数
        $data['focus_num'] = $nums['attentionNum'];//D('project/project')->getUserProjects($uid, 3, -1, true);
        $data['usertype'] = model_userauthentication::getUserType($uid);
        //获取当前用户预约项目总数
        $data['make_num'] = D('project/project')->getUserProjects($uid, 6, -1, true);
        //获取当前用户约谈项目总数
        $data['turn_around_num'] =  $nums['speakNum'];//D('project/project')->getUserProjects($uid, 4, -1, true);

        //内容
        $user_contact = M('user_contact')->where(array('uid'=>$uid))->find();
        $province = D('area')->id2name($user_contact['province']);
        $city = D('area')->id2name($user_contact['city']);
        $area = D('area')->id2name($user_contact['area']);
        $data['area'] = $province.$city.$area ? $province.$city.$area : "";
        $data['address'] = $user_contact['address'] ? $user_contact['address'] :"";
        $data['tel'] = $user_contact['tel'] ? $user_contact['tel'] : "";
        //用户等级
        $user_level = D('ScoreUser')->getUserLevel($this->_userinfo['uid']);
        $data['tzf_level_img'] = $user_level['tzf_level_img'] ? $this->_url['web_tpl'].$user_level['tzf_level_img'] : '';
        $data['xmf_level_img'] = $user_level['xmf_level_img'] ? $this->_url['web_tpl'].$user_level['xmf_level_img'] : '';
        $data['tzf_level'] = $user_level['tzf_dj'];
        $data['xmf_level'] = $user_level['xmf_dj'];
        //自动授权
        $data['auto_yeepay_url'] = url('app-pay/AuthorizeAutoTransfer').'?';
        $data['my_credit_url'] = url('wap-record/credit').'?';
        //预计应收分红
        $collect = D('stock/return')->getCollectAmount($uid);
        $data['ys_share_amount'] = $collect['sum_share_amount'] ? $collect['sum_share_amount'] : 0;
        $data['sum_share_received'] = $collect['sum_share_received'] ? $collect['sum_share_received'] : 0;
        //预计应发分红
        $repayment = D('stock/return')->getShareAmount($uid);
        $data['yf_share_amount'] = $repayment['sum_share_amount'] ? $repayment['sum_share_amount'] : 0;
        $data['sum_share_sent'] = $repayment['sum_share_sent'] ? $repayment['sum_share_sent'] : 0;
        //账户金额
        $info = D('account')->getInfo($uid);
        $data['user_amount'] = $info['user_amount'] ? $info['user_amount'] : '0';//可用余额
        $data['freeze_amount'] = $info['freeze_amount'] ? (string)$info['freeze_amount'] : '0';//冻结金额
        $data['investment_total'] = $info['investment_amount'] ? $info['investment_amount'] : '0';//已投资总金额

        if(!empty($data)){
            S($cachename, $data, 5);
            $this->ajax(-101, '获取成功',$data);
        }
        $this->ajax(-102, '暂无相关内容');
	}

    /*
     * 修改用户信息
     * @return array
     * @author Baijiansheng
     */
    public function actionSetUserInfo(){
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->ajax('-3','请求方式错误');
        }

        $uid = $this->_userinfo['uid'];

        $type = intval($this->_post('type',0));
        $user_data = $this->getUserCondition();
        $user_params = $user_data['user'];
        $body_params = $user_data['user_body'];
        $user_contact_params = $user_data['user_contact'];

        $redis = cache::getInstance('redis');
        $user = D('user');
        $invest_info = M('user')->field('is_idcard')->where(array('uid'=>$uid))->find();
        if((!empty($user_params['realname']) || !empty($user_params['mobile']) || !empty($body_params['u_body_num'])) && ($invest_info['is_idcard'] >= 2)){
            $this->ajax('-106','您已经是认证投资人，不可更改个人信息');
        }
        $rs = '';
        if(!empty($user_params)){
            if(M('user')->data($user_params)->where(array('uid'=>$uid))->save()){
                if($user_params['mobile']){
                    $redis->set(self::LOAN_MOBILE_CODE.$user_params['mobile'], null);
                    $redis->set(self::LOAN_MOBILE_CODE.$user_params['mobile'].'time', null);
                }
                if($user_params['email']){
                    $redis->set(self::REGISTER_EMAIL_CODE.$user_params['email'], null);
                    $redis->set(self::REGISTER_EMAIL_CODE.$user_params['email'].'time', null);
                }
                $status = -101;
                model_user::getInfo($uid,'',true);
            }
            else $status = -102;
        }
        if(!empty($body_params)){
            $UserBody = D('UserBody');
            $body_info = $UserBody->getUserBody(array('uid'=>$uid));
            if(empty($body_info)){
                $body_params['uid'] = $uid;
                $body_params['add_time'] = time();
                $body_params['ip'] = getIp();
                $body_params['source'] = C('sys_global_source');
                $rs = M('UserBody')->data($body_params)->add();
            }else{
                $rs = M('UserBody')->data($body_params)->where(array('uid'=>$uid))->save();
            }
            //if($rs){
                $status = -101;
                model_user::getInfo($uid,'',true);
            //}
            //else $status = -102;
        }
        if(!empty($user_contact_params)){
            $contant_info = D('UserContact')->getUserContact(array('uid'=>$uid));
            if(!empty($contant_info)){
                $rs = M('user_contact')->data($user_contact_params)->where(array('uid'=>$uid))->save();
            }else{
                $user_contact_params['uid'] = $uid;
                $rs = M('user_contact')->data($user_contact_params)->add();
            }
            if($rs) $status = -101;
            else $status = -102;
        }
        if($type){
            //$email = model_user::getInfo($uid,'email');
            $body_body     =   D('userBody')->getUserBody(array('uid'=>$uid));
            if($body_body['realname'] && $body_body['u_body_num']){
                $res['url'] = url('app-pay/Register').'?';
                $res['auto_yeepay_url'] = url('app-pay/AuthorizeAutoTransfer').'?';
            }
        }
        if($status == -101) $this->ajax($status,'修改成功',$res);
        else $this->ajax($status,'修改失败');
    }


    /*
	 * 认证领投，合投人身份
     * @return array
     * @author Baijiansheng
	 */
    public function actionSetUserAuthentication(){
        $usertype = $this->_post('usertype') ? intval($this->_post('usertype')) : 1;
        if($usertype == 1){
            $this->leadInvestment();
        }else if($usertype == 2){
            $this->togetherInvestment();
        }
        $this->ajax(-103,'参数获取失败');
    }

    /*
	 * 获取认证领投，合投人身份信息
     * @return array
     * @author Baijiansheng
	 */
    public function actionGetUserAuthentication(){
        $this->checkLogin();
        $info = D('userauthentication')->get_user_authentication($this->_userinfo['uid']);
        if(!empty($info)){
            $arr_url = C('url');
            $info['company_logo'] = $info['company_logo'] ? $arr_url['img2'].$info['company_logo'] : '';
            $info['company_business_licence_img'] = $info['company_business_licence_img'] ? model_attachment::getImgUrl($info['company_business_licence_img']) : '';
            $info['person_asset'] = $info['person_asset'] ? model_attachment::getImgUrl($info['person_asset']) : '';
            $info['person_credit'] = $info['person_credit'] ? model_attachment::getImgUrl($info['person_credit']) : '';
            $info['person_photo'] = $info['person_photo'] ? $arr_url['img2'].$info['person_photo'] : '';
            unset($info['company_is_show']);
            unset($info['person_is_show']);
            unset($info['add_time']);
            unset($info['ip']);
            $this->ajax(-101,'获取成功',$info);
        }
        $this->ajax(-102,'暂无相关信息');
    }

    /*
	 * 认证领投人身份
     * @return array
     * @author Baijiansheng
	 */
    private function leadInvestment()
    {
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->ajax(-3,'请求方式错误');
        }
        $uid = $this->_userinfo['uid'];
        //验证是否允许提交领投
        $info = D('userauthentication')->get_user_authentication($uid);
        if($info['person_status'] > 0){
            $this->ajax(-103,'您已提交领投明星信息，不能进行此操作');
        }
        //验证是否允许修改信息
        $check_rs = D('userauthentication')->check_user_authentication($uid,'company_status');
        if(!$check_rs['status']) $this->ajax(-104,$check_rs['msg']);
        if($this->_post()){
            if((strlen($this->_post('company_info')) < 100) || (strlen($this->_post('company_info')) > 1000)) $this->ajax('-106',"公司简介长度应在100-1000个之间！");
            $data['company_name'] = sqlReplace($this->_post('company_name'));
            $data['company_info'] = sqlReplace($this->_post('company_info'));
            $data['company_business_licence'] = sqlReplace($this->_post('company_business_licence'));
            $data['company_logo'] = sqlReplace($_POST['company_logo']);
            $data['company_business_licence_img'] = sqlReplace($_POST['company_business_licence_img']);
            //$data['company_status'] = 0;
            //$data['company_is_show'] = 1;
            $data = array_filter($data);
            if($check_rs['status'] == 1)
            {
                $data['uid'] = $uid;
                $data['add_time'] = time();
                $data['ip'] = getIp();
                M('user_authentication')->add($data);
            }elseif(($check_rs['status'] == 2) || ($check_rs['status'] == 3)){
                M('user_authentication')->where(array('uid'=>$uid))->save($data);
            }else{
                $this->ajax(-105,'暂时无法提交领投身份认证信息');
            }
            $autoinfo = D('userauthentication')->get_user_authentication($uid);
            if($autoinfo['company_name'] && $autoinfo['company_info'] && $autoinfo['company_business_licence'] && $autoinfo['company_logo'] && $autoinfo['company_business_licence_img'] && (in_array($autoinfo['company_status'],array(0,-1)))){
                $compdata['company_status'] = 1;
                $compdata['company_is_show'] = 1;
                M('user_authentication')->where(array('uid'=>$uid))->save($compdata);
            }
            $this->ajax(-101,'提交成功');
        }
        //$var['data'] = D('userauthentication')->get_user_authentication($uid);
        $this->ajax(-102,'提交失败');
    }

    /*
	 * 认证合投人身份
	*/
    private function togetherInvestment(){
        $uid = $this->_userinfo['uid'];
        //验证是否允许提交领投
        $info = D('userauthentication')->get_user_authentication($uid);
        if($info['company_status'] > 0){
            $this->ajax(-103,'您已提交领投机构信息，不能进行此操作');
        }
        //验证是否允许修改信息
        $check_rs = D('userauthentication')->check_user_authentication($uid,'person_status');
        if(!$check_rs['status']) $this->ajax(-104,$check_rs['msg']);
        if($this->_post())
        {
            $data['person_name'] = sqlReplace($this->_post('person_name'));
            $data['person_cardid'] = sqlReplace($this->_post('person_cardid'));
            $data['person_mobile'] = sqlReplace($this->_post('person_mobile'));
            $data['person_asset'] = sqlReplace($_POST['person_asset']);
            $data['person_credit'] = sqlReplace($_POST['person_credit']);
            $data['person_photo'] = sqlReplace($_POST['person_photo']);
            //$data['person_status'] = 1;
            //$data['person_is_show'] = 1;
            $data = array_filter($data);
            if($check_rs['status'] == 1)
            {
                $data['uid'] = $uid;
                $data['add_time'] = time();
                $data['ip'] = getIp();
                M('user_authentication')->add($data);
            }elseif(($check_rs['status'] == 2) || ($check_rs['status'] == 3)){
                M('user_authentication')->where(array('uid'=>$uid))->save($data);
            }else{
                $this->ajax(-105,"暂时无法提交合投身份认证信息！");
            }
            $autoinfo = D('userauthentication')->get_user_authentication($uid);
            if($autoinfo['person_name'] && $autoinfo['person_cardid'] && $autoinfo['person_mobile'] && $autoinfo['person_asset'] && $autoinfo['person_credit'] && $autoinfo['person_photo'] && (in_array($autoinfo['person_status'],array(0,-1)))){
                $perdata['person_status'] = 1;
                $perdata['person_is_show'] = 1;
                M('user_authentication')->where(array('uid'=>$uid))->save($perdata);
            }
            $this->ajax(-101,"提交成功！");
        }
        //$var['data'] = D('userauthentication')->get_user_authentication($uid);
        $this->ajax(-102,'提交失败');
    }

    /*
     * 修改用户登录密码
     * @return int
     * @author Baijiansheng
     */
    public function actionSetUserPassword(){
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] != 'POST')  $this->ajax(-3,'请求方式错误');

        $uid = $this->_userinfo['uid'];
        $login_password = model_user::getInfo($uid,'password');

        //旧密码
        $oldpwd = $this->_post('oldpwd') ? trim($this->_post('oldpwd')) : '';
        //新密码
        $password_new = $this->_post('password_new') ? trim($this->_post('password_new')) : '';
        $password_new_re = $this->_post('password_new_re') ? trim($this->_post('password_new_re')) : '';
        if($login_password != helper_tool::pwdEncode($oldpwd)) {
            $this->ajax(-102, '原密码不正确');
        }

        $this->passwordBasicCheck($oldpwd,$password_new,$password_new_re);

        $rs = model_user::setLoginPwd($uid, $password_new, $oldpwd);
        if($rs){
            //修改环信用户密码
            D('user')->updateHxPassword($uid);
            model_user::getInfo($uid,'',true);
            $this->ajax(-101,'修改成功');
        }else{
            $this->ajax(-103,'修改失败');
        }
    }

    /*
     * 获取认购人联系方式
     * @return array
     * @author Baijiansheng
     */
    public function actionGetProjectBuyNews(){
        $this->checkLogin();

        $UserContact = D('UserContact');
        $info = $UserContact->getUserContact(array('uid'=>$this->_userinfo['uid'],'is_delete'=>0),'id,mobile,name,province,city,area,address,is_default,zip');
        if(!empty($info)){
            $this->ajax(-101,'认购人联系方式',$info);
        }else{
            $this->ajax(-102,'暂无认购人联系方式');
        }
    }

    /*
     * 添加或修改认购人联系方式
     * @return int
     * @author Baijiansheng
     */
    public function actionSetProjectBuyNews(){
        $this->checkLogin();
        $uid = $this->_userinfo['uid'];
        $UserContact = D('UserContact');

        $id = intval($this->_post('id',0));
        $data_arr = $this->_post();
        if($data_arr['name']) $data['name'] = trim($data_arr['name']);
        if($data_arr['mobile']) $data['mobile'] = trim($data_arr['mobile']);
        if($data_arr['province']) $data['province'] = intval($data_arr['province']);
        if($data_arr['city']) $data['city'] = intval($data_arr['city']);
        if($data_arr['area']) $data['area'] = intval($data_arr['area']);
        if($data_arr['zip']) $data['zip'] = trim($data_arr['zip']);
        if($data_arr['address']) $data['address'] = trim($data_arr['address']);
        if($data_arr['is_default']) $data['is_default'] = trim($data_arr['is_default']);
        if(empty($data)) $this->ajax('-104','所修改参数不能全为空');

        $info = $UserContact->getUserContact(array('uid'=>$uid));
        //修改收货地址
        if(!empty($info) && $id){
            $rs = $UserContact->saveUserContact($id, $uid, $data);
            $msg = '修改成功!';
        }else{
            $data['uid'] = $uid;
            $data['username'] = model_user::userid2username($uid);
            $data['source'] = C('sys_global_source');
            $rs = $UserContact->addUserContact($data);
            $msg = '添加成功!';
        }
        
        if($rs){
            $this->ajax(-101,$msg);
        }
        $this->ajax(-102,'保存失败');
    }
    
    /*
     * 删除认购人联系方式
     * @return int
     * @author GaoJianQiang
     */
    public function actionDelProjectBuyNews(){
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->ajax(-3,'请求方式错误');
        }
        $uid = $this->_userinfo['uid'];

        $id = intval($this->_post('id',0));
        if(empty($id)) $this->ajax('-103','参数获取失败，请稍后再试');

        $UserContact = D('UserContact');
        $rs = $UserContact->delUserContact($id, $uid);
        if($rs !== false){
            $this->ajax(-101,'删除成功！');
        }else{
            $this->ajax(-102,'删除失败！');
        }
    }
    
    /*
     * 找回用户密码
     * @param string $username 用户名称
     * @param string $imgcode 验证码
     * @param string $mobilecode 手机验证码
     * @param string $password_new 新密码
     * @param string $password_new_re 重复新密码
     * @return int
     * @author Baijiansheng
     */
    public function actionSetFindPassword(){
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->ajax(-3,'请求方式错误');
        }
        $redis = cache::getInstance('redis');
        $step = $this->_post('step') ? $this->_post('step'):'';
        //第一步参数
        $username = $this->_post('username') ? $this->_post('username'):'';

        //第二步参数
        $password_new = $this->_post('password_new') ? trim($this->_post('password_new')) : '';

        $uid = model_user::username2userid($username);
        $mobile =  model_user::getInfo($uid,'mobile');
        if($step == '1'){
            /*if($this->is_open_captcha) {
                if(!helper_tool::checkValidate($this->_post('imgcode'))) {
                    $this->ajax('-103', '验证码不正确');
                }
            }*/
            if (!helper_tool::checkEmail($username) && !helper_tool::checkMobile($username) ) {
                $this->ajax(-104, '邮箱/手机号码不正确');
            }
            //发送手机短信验证码
            $number = helper_string::randString(6, 1);
            $msg_content = model_remind::getTplContent('mobile_reg', array('valicode' => $number));
            $msg_content = $msg_content['mobile']['content'];
            if (model_remind::sendMobile($mobile, $msg_content, 'mobile_reg')) {
                $redis->set(self::RETRIEVE_PWD_MOBILE_CODE , $number, self::CHECK_OUT_TIME);
                $this->ajax(-107, '验证码发送成功');
            }
            $this->ajax(-108, '验证码发送失败,请稍后重试');

        }elseif($step == '2'){
            if ($redis->get(self::RETRIEVE_PWD_MOBILE_CODE ) != $this->_post('mobilecode')) {
                $this->ajax(-105, '手机验证码不正确');
            }
            if ($this->_post('password_new_re') && ($password_new != $this->_post('password_new_re'))) {
                $this->ajax(-106, '重复密码不正确');
            }
        }

        if($uid && model_user::setLoginPwd($uid, $password_new)){
            //修改环信用户密码
            D('user')->updateHxPassword($uid);
            model_user::getInfo($uid,'',true);
            $redis->set(self::RETRIEVE_PWD_MOBILE_CODE , null);
            $this->ajax(-101,'找回成功');
        }else{
            $this->ajax(-102,'找回失败，请稍后再试');
        }

    }
    /*
     * 获取用户统计信息
     * @param login param
     * @return int
     * @author Baijiansheng
     */
    public function actionGetUserCount(){
        $this->checkLogin();
        $uid = $this->_userinfo['uid'];
        $cachename = 'app_user_GetUserCount_' . $uid;
        $data = S($cachename);
        if(!empty($data))  $this->ajax('-101','获取成功',$data);

        //未读私信条数,投资数量,
        $user_info = model_user::getInfo($uid,'',1);
        //获取当前用户投资项目总数
        $data['newpm'] = intval($user_info[0]['newpm']);
        $data['investor_num'] = D('project/project')->getUserProjects($uid, 2, -1, true);

        //获取当前用户发布项目总数
        $data['publish_num'] = D('project/project')->getUserProjects($uid, 'publish', -1, true);

        //获取当前用户关注项目总数
        $data['focus_num'] = D('project/project')->getUserProjects($uid, 3, -1, true);
        //获取当前用户预约项目总数
        $data['make_num'] = D('project/project')->getUserProjects($uid, 6, -1, true);
        //获取当前用户约谈项目总数
        $data['turn_around_num'] = D('project/project')->getUserProjects($uid, 4, -1, true);

        S($cachename, $data, 3);
        $this->ajax('-101','获取成功',$data);
    }

    /*
     * 获取未读消息
     * @param login param
     * @return int
     * @author Baijiansheng
     */
    public function actionGetUnreadMsgCount(){
        $this->checkLogin();

        $type = isset($this->post['type'])?$this->post['type']:'';
        $uid = $this->_userinfo['uid'];

        $page = $this->getPageCondition();

        $cachename = 'app_user_GetUnreadMsgCount_' .$type. helper_cache::makeKey($page);
        $list = S($cachename);
        if(!empty($list)) $this->ajax(-101,'获取成功',$list);

        $field = 'sent_uid,id,title,content,type,pid,add_time';
        $message = D('message');
        //未读消息
        if($type){
            $list = $message->getMessages(array('receive_uid'=>$uid,'status'=>0),$page['start_num'],$page['page_num'],$field);
        }else{
            $list = $message->getMessages(array('receive_uid'=>$uid),$page['start_num'],$page['page_num'],$field);
        }
        if(!empty($list)){
            foreach($list as $key=>$val){
                if(!$val['sent_uid']){
                    $list[$key]['type'] = 3;
                }
                if($val['pid']){
                    $list[$key]['type'] = 4;
                }
            }
            S($cachename, $list, 3);
            $this->ajax(-101,'获取成功',$list);
        }
        $this->ajax(-102,'暂无相关信息');
    }

    /*
     * 上传头像、身份证图片附件
     * @param login param
     * @return int
     * @author Baijiansheng
     */
    public function actionSetUploadFile(){
        $this->checkLogin();
        $arr_img = C('url');
        $uid = $this->_userinfo['uid'];
        $type = $this->_post('type') ? intval($this->_post('type')):'1';
        $field = $this->_post('file_name') ? trim($this->_post('file_name')) : 'pic';

        //上传参数设置
        $upload_path = array(1=>'user/'.date('Y/md'),2=>'images/user/cardback',3=>'images/user/cardback',4=>'images/project/bill/'.date('Ym').'/'.date('d'),5=>'images/user/authentication/'.date('Y/md'),6=>'images/user/authentication/'.date('Y/md'),7=>'images/user/authentication/'.date('Y/md'),8=>'images/user/authentication/'.date('Y/md'),9=>'images/user/authentication/'.date('Y/md'),10=>'images/project/income/'.date('Y/md'));
        $tables = array(1=>'user',2=>'user_body',3=>'user_body',4=>'financial_expenditure',5=>'user_authentication',6=>'user_authentication',7=>'user_authentication',8=>'user_authentication',9=>'user_authentication',10=>'financial_income');
        $fields = array(1=>'face',2=>'u_body_photo',3=>'u_body_photof',4=>'attach',5=>'company_logo',6=>'company_business_licence_img',7=>'person_asset',8=> 'person_credit',9=>'person_photo',10=>'attach');
        //$field = $fields[$type];
        if(in_array($type,array(7,8))) $atta_fix = 'doc|docx|ppt|pptx|pdf|jpg|jpeg|png|gif|bmp';
        else $atta_fix = 'jpg|jpeg|png|gif';
        //上传附件 图片
        $conf = array(
            'table' => $tables[$type], //附件对应的表 （必填）
            'table_field' => $fields[$type], //附件对应表的字段（必填）
            'exts' => $atta_fix, //附件后缀
            //'saverule' => $imgArr['name_app'], //文件名
            'savepath' => $upload_path[$type], //文件路径
        );
        if(in_array($type,array(2,3,4,6,7,8,10))) $conf['private_file'] = true;

        $rs = D('attachment')->upload($field, $conf);
        if($rs['status'] == 1){
            $UserBody = D('UserBody');
            $body_info = $UserBody->getUserBody(array('uid'=>$uid));
            $body_params['uid'] = $uid;
            $body_params['add_time'] = time();

            if($type == '1'){
                M('user')->where(array('uid'=>$uid))->data(array('face'=>$rs['data']['url']))->save();
            }else if($type == 2){
                if(empty($body_info)){
                    $body_params['u_body_photo'] = $rs['data'];
                    $res = M('UserBody')->data($body_params)->add();
                }else{
                    M('UserBody')->where(array('uid'=>$uid))->data(array('u_body_photo'=>$rs['data']['url']))->save();
                }
            }else if($type == 3){
                if(empty($body_info)){
                    $body_params['u_body_photof'] = $rs['data'];
                    $res = M('UserBody')->data($body_params)->add();
                }else{
                    M('UserBody')->where(array('uid'=>$uid))->data(array('u_body_photof'=>$rs['data']['url']))->save();
                }
            }
            $file_data['save_img'] = $rs['data']['url'];
            $file_data['img_url'] = $rs['data']['url2'];//$arr_img['img2'].$rs['data'];
            unset($rs['data']['basepath'],$rs['data']['file']);
            model_user::getInfo($uid,'',true);
            $this->ajax('-101','上传成功',$file_data);
        }else{
            $this->ajax('-102','上传失败');
        }
    }

    /**
     * 用户基本信息
     * @author Baijianshneg
     */
    private function getUserCondition()
    {
        $uid = $this->_userinfo['uid'];
        $redis = cache::getInstance('redis');
        //性别
        if((string)$this->_post('sex') == '0' || (string)$this->_post('sex') == '1' || (string)$this->_post('sex') == '2') {
            $user_params['sex'] = $this->_post('sex');
        }
        //邮箱
        $code = trim($this->_post('code'));
        $email = trim($this->_post('email'));
        $mobile = trim($this->_post('mobile'));
        $realname = trim($this->_post('realname'));
        $username = trim($this->_post('username'));
        if(!empty($email)){
            $user_params['email'] = $email;
            if (D('user')->checkEmailExist($email)) {
                $this->ajax('-108', '该邮箱已存在');
            }
            if (!$code || ($redis->get(self::REGISTER_EMAIL_CODE.$email) != $code)) {
                $this->ajax('-104', '邮箱验证码不正确');
            }
        }
        //手机号
        if(!empty($mobile)){
            $user_params['mobile'] = $mobile;
            if (D('user')->checkMobileExist($mobile)) {
                $this->ajax('-105', '该手机号已存在');
            }
            if (!$code || ($redis->get(self::LOAN_MOBILE_CODE.$user_params['mobile']) != $code)) {
                $this->ajax('-104', '手机验证码不正确');
            }
        }
        //真实姓名
        if(!empty($realname)){
            $user_params['realname'] = $body_params['realname'] = $realname;
        }
        //真实姓名
        if(!empty($username)){
            $user_params['username'] = $username;
            $user_params['is_username'] = 1;
            if(model_user::checkBadKeyWords($username)) $this->ajax('-111', '用户名不能包含禁用关键字');
            if(D('user')->checkUserNameExist($username)) $this->ajax('-110', '此用户名已存在');
        }
        //头像
        /*if(!empty($this->post['uface'])){
            $user_params['uface'] = trim($this->post['uface']);
        }*/

        $id_number = trim($this->_post('id_number'));
        $id_card_positive = trim($this->_post('id_card_positive'));
        $id_card_opposite = trim($this->_post('id_card_opposite'));
        //身份证号码
        if(!empty($id_number) && ($this->_userinfo['is_idcard'] != 3)){
            if(!helper_idcard::validateIDCard($id_number)) $this->ajax('-107','身份证号码不正确');
            $num = M('UserBody')->where(array('u_body_num' => $id_number,'uid'=>array('neq', $uid)))->count();
            if ($num > 0) $this->ajax('-109','此身份证号码已存在，请重新输入');
            $body_params['u_body_num'] = $id_number;
        }
        //身份证正面
        if(!empty($id_card_positive) ){
            $body_params['u_body_photo'] = $id_card_positive;
        }
        //身份证反面
        if(!empty($id_card_opposite)){
            $body_params['u_body_photof'] = $id_card_opposite;
        }

        //地区信息
        $data_user_contact['province'] = $province = $this->_post('province') ? intval($this->_post('province')) : '';
        $data_user_contact['city'] = $city = $this->_post('city') ? intval($this->_post('city')) : '';
        $data_user_contact['area'] = $area = $this->_post('area') ? intval($this->_post('area')) : '';
        $data_user_contact['address'] = strip_tags($this->_post('address'));

        if(empty($user_params) && empty($body_params) && empty($data_user_contact)){
            $this->ajax('-103','参数获取失败');
        }
        if(!empty($user_params)) $data['user'] = $user_params;
        if(!empty($body_params)) $data['user_body'] = array_filter($body_params);
        if(!empty($data_user_contact)) $data['user_contact'] = array_filter($data_user_contact);

        return $data;
    }

    /*
	 * 密码基本验证
     * @param string $password 旧密码
     * @param string $password_new 新密码
     * @param string $password_new_re 重复新密码
	 * @author Baijianshneg
	 */
    private function passwordBasicCheck($password,$password_new,$password_new_re)
    {
        if(!empty($password) && $password == $password_new)
        {
            $result['status'] = '-104';
            $result['msg'] = '新旧密码不能相同';
        }elseif($password_new != $password_new_re)
        {
            $result['status'] = '-105';
            $result['msg'] = '两次输入密码不一致';
        }elseif(!helper_tool::checkPassword($password_new))
        {
            $result['status'] = '-106';
            $result['msg'] = '密码要求6-16位数字或字母';
        }
        if(!empty($result)) $this->ajax($result['status'],$result['msg']);
    }

    /**
     * 发送邮箱验证码
     * @author Baijiansheng
     */
     public function actionGetEmailCode(){
        $email = $this->_post('email') ? trim($this->_post('email')) : '';
        /*$code   = $this->_post('code');
        if (!helper_tool::checkValidate($code, true, '', 'send_mobile_code')) {
            $this->ajax('-5', '验证码不正确');
        }*/
        $redis = cache::getInstance('redis');
        if ($redis->get(self::REGISTER_EMAIL_CODE  . $email . 'time') && time() - $redis->get(self::REGISTER_EMAIL_CODE . $email .'time') < self::EMAIL_CODE_TIME ) {
            $s_time = (self::EMAIL_CODE_TIME - (time() - $redis->get(self::REGISTER_EMAIL_CODE  . $email . 'time')));
            $this->ajax('-102', '验证码发送过于频繁,请'.$s_time.'秒后再发送');
        }
        if (!helper_tool::checkEmail($email)) {
            $this->ajax('-103', '该邮箱不正确或已被注册');
        }
        /*if (D('user')->checkEmailExist($email)) {
            $this->ajax('-104', '该邮箱号码已经注册过,请更换邮箱');
        }*/
        $valicode = helper_tool::validate('email',6,1);
        $msg_content = model_remind::getTplContent('email_reg',array('valicode' => $valicode['code']));
        if (model_remind::sendEmail($email,$msg_content['email']['title'],$msg_content['email']['content'],'email_reg','sohu')) {
            $redis->set(self::REGISTER_EMAIL_CODE . $email, $valicode['code']);
            $redis->set(self::REGISTER_EMAIL_CODE . $email .'time', time());
            $this->ajax('-101', '验证码发送成功');
        }
        $this->ajax('-105', '验证码发送失败,请稍后重试');
    }

    /*
	 * 验证登录密码
     * @param string $password 密码
	 * @author Baijianshneg
	 */
    public function actionCheckLoginPassword(){
        $this->checkLogin();
        if ($_SERVER['REQUEST_METHOD'] != 'POST') {
            $this->ajax('-3','请求方式错误');
        }
        $password = trim($this->_post('password'));
        if(empty($password)) $this->ajax('-102', '请输入密码');

        $uid = $this->_userinfo['uid'];
        $login_password = model_user::getInfo($uid,'password');

        if($login_password != helper_tool::pwdEncode($password)) {
            $this->ajax('-103', '原密码不正确');
        }
        $this->ajax('-101', '验证成功');
    }

    /**
     * 黑名单列表
     * @param string $type 类型（1 高风险，2 企业，3投资人）
     */
    public function actionGetBlackList()
    {
        $type = intval($this->_request('type', 2));

        $page = $this->getPageCondition();

        $arr_url = C('url');
        $condition = array('is_show' => 1, 'type' => $type);

        $cachename = 'app_user_GetBlackList_' .helper_cache::makeKey($condition).'_'. helper_cache::makeKey($page);
        $list = S($cachename);
        if(!empty($list)) $this->ajax('-101', '获取成功', $list);

        $fields = 'type,pid,project_name,username,relation,amount,cover,province,city,gender,publicity_time,reason,content,project_desc,project_status,card_id,problem,progress,legal';
        $list = M('black')->field($fields)->where($condition)->limit("{$page['start_num']}, {$page['page_num']}")->select();
        if(!empty($list)) {
            foreach($list as $key=>$val){
                $list[$key]['address'] = D('project/project')->getProjectArea($val['pid']);
                $list[$key]['cover'] = $val['cover'] ? $arr_url['img2'].$val['cover'] : '';
                $val['reason'] = $val['content'] ? $val['content'] : $val['reason'];
                $list[$key]['reason'] = str_replace(array(' ',"\t","\r\n","\r","\n"),'',str_replace('&nbsp','',helper_string::unHsc(helper_string::safeReplace(helper_string::html2text(strip_tags($val['reason']))))));
                $list[$key]['progress'] = str_replace(array(' ',"\t","\r\n","\r","\n"),'',str_replace('&nbsp','',helper_string::unHsc(helper_string::safeReplace(helper_string::html2text(strip_tags($val['progress']))))));
                $list[$key]['project_desc'] = str_replace(array(' ',"\t","\r\n","\r","\n"),'',str_replace('&nbsp','',helper_string::unHsc(helper_string::safeReplace(helper_string::html2text(strip_tags($val['project_desc']))))));
                $list[$key]['problem'] = str_replace(array(' ',"\t","\r\n","\r","\n"),'',str_replace('&nbsp','',helper_string::unHsc(helper_string::safeReplace(helper_string::html2text(strip_tags($val['problem']))))));
                //地区
                $list[$key]['province'] = D('area')->id2name($val['province']);
                $list[$key]['city'] = D('area')->id2name($val['city']);
                if($type == '1'){
                    $project = D('project')->getInfo($val['pid']);
                    $list[$key]['img_cover'] = helper_tool::getThumbImg($val['img_cover'], 300, 200);
                    $list[$key]['opening_time'] = $project['opening_time'];
                    //项目所属行业
                    $list[$key]['trade_one'] = $this->getTradeName($project['trade_one']);
                    $list[$key]['trade_two'] = $this->getTradeName($project['trade_two']);

                    $list[$key]['finance_total'] = $project['finance_total'];
                    $list[$key]['amount_begin_time'] = $project['amount_begin_time'];
                    $list[$key]['finsh_time'] = $project['finsh_time'];
                }
            }
            S($cachename, $list, 15);
            $this->ajax('-101', '获取成功', $list);

        }
        $this->ajax('-102', '暂无相关内容');
    }

    //获取行业名称
    private function getTradeName($id){
        $trade = D('trade')->getInfo($id);
        return $trade['name'] ? $trade['name'] :'';
    }


    /*
	 * 获取投资人信息（ios导出）
	 * @return array
	 * @author Baijiansheng
	 */
    public function actionGetUserInvests(){

        $page['page'] = $this->_post('page') ? intval($this->_post('page')) : 1;
        $page['page_num'] = $this->_post('page_num') ? intval($this->_post('page_num')) : 10;
        $page['start_num'] = ($page['page'] - 1) * $page['page_num'];

        $time = $this->_post('datatime') ? date('Y-m-d H:i:s',trim($this->_post('datatime'))) : '';

        //$condition = array('is_idcard'=>array('egt',2), 'mobile'=>array('neq',''));
        $where = " and u.is_idcard >= 2 AND u.mobile != '' GROUP BY r.uid ";
        if($time){
            $where = "and r.add_time>'".$time."'".$where;
        }

        $cachename = 'app_user_GetUserInvests_' .helper_cache::makeKey($page).'_'.$time;
        $cache_data = S($cachename);
        if(!empty($cache_data)) $this->ajax('-101', '获取成功', $cache_data);

        //$users = D('user')->getUsers($condition, 'realname,mobile', $page['start_num'], $page['page_num']);
        $sql = 'SELECT u.uid,u.realname,u.mobile,u.email,r.add_time FROM account_recharge_log r LEFT JOIN user u on r.uid=u.uid WHERE 1 '.$where.' limit '.$page['start_num'].','. $page['page_num'];
        $users['info'] = M()->query($sql);
        if(!empty($users['info'])){
            foreach($users['info'] as $key=>$val){
                $date[] = $val['add_time'];
                $data['info'][$key]['realname'] = $val['realname'];
                if(empty($val['realname'])){
                    $userbody = M('user_body')->field('realname')->where(array('uid'=>$val['uid']))->find();
                    $data['info'][$key]['realname'] = $userbody['realname'];
                }
                $data['info'][$key]['mobile'] = $val['mobile'];
                $data['info'][$key]['email'] = $val['email'] ? $val['email'] : '';
            }
            $users_count = M()->query('SELECT count(count) as count FROM (SELECT count(*) as count FROM account_recharge_log r LEFT JOIN user u on r.uid=u.uid WHERE 1 '.$where.') as invest');
            $data['count'] = $users_count[0]['count'] ? $users_count[0]['count'] : 0;
            //返回最后一次时间
            $data['datatime'] = strtotime(max($date));
            unset($date);
            unset($users);
            S($cachename, $data, 5);
            $this->ajax(-101, '获取成功',$data);
        }
        $this->ajax(-102, '暂无相关内容');
    }

    /**
     * 获取搜索条件
     * @author Baijianshneg
     */
    private function getPageCondition(){
        $data['page'] = $this->_post('page') ? intval($this->_post('page')) : 1;
        $data['page_num'] = $this->_post('page_num') ? intval($this->_post('page_num')) : $this->page_num;
        $data['start_num'] = ($data['page'] - 1) * $data['page_num'];

        return $data;
    }

    /**
     * 获取找回密码手机验证码
     * @author Baijianshneg
     */
    private static function getMobileCode()
    {
        $redis = cache::getInstance('redis');
        return $redis->get(self::RETRIEVE_PWD_MOBILE_CODE);
    }

    /**
     * 老站联盟301
     * 老站地址 wap wapuserControl.php	register()
     * @author wangbingang
     */
    public function actionRegister()
    {
        $web_url = C('url');
        //老站 www.renrentou.com/appapi/setLianmengDev?{idfa}=1&{udid}=1&{source}=1&callback=1
        //新站 app.dev2.renrentou.com/user/SetExtensionDev?idfa={$idfa}&udid={$udid}&callback=1&k=25f27qF3NG4k9yevRKf2BpUYeBpUYe71FHX4EIAn2R0HmsiNXaNrqcCuaIA4vQoszNXnmKlZ
        $lianmeng_data = D('lianmengPlatform')->getByAll(array());
        //导出新站推广联盟链接
        /*foreach($lianmeng_data as $val) {
            $name = $val['name'];
            if($val['remark']) {
                $name = $val['remark'];
            }
            echo 'WAP推广链接: http://wap.renrentou.com/register/promotionreg?k='.$val['key'].' 推广名称: '.$name.'<br>';
            echo 'PC推广链接: http://www.renrentou.com/register/tm?k='.$val['key'].' 推广名称: '.$name.'<br>';
        }
        exit;*/
        foreach($lianmeng_data as $val)
        {

            if(isset($_GET[$val['name']]) && !empty($_GET[$val['name']]))
            {
                $url = $web_url['wap'].'/register/promotionreg?k='.$val['key'];
                header("HTTP/1.1 301 Moved Permanently");
                header("Location:".$url);
                exit;
            }
        }
        header("HTTP/1.1 301 Moved Permanently");
        header("Location:".$web_url['wap']."/register/promotionreg");
    }

    //用户资金明细筛选的种类
    public function actionGetFundsDetailSpecies(){
        $this->checkLogin();
        $arr = array(
            'recharge_online'=>'充值成功', 
            'project_full_success'=>'融资成功',
            'projet_income'=>'投资放款', 
            'projet_apply'=>'资金申请',
            'projet_apply_success'=>'终审通过',
            'projet_apply_fail'=>'终审失败',
            'withdraw_success'=>'提现成功',
            'stock_buy_success'=>'项目转让成功',
            'stock_sell_success'=>'项目卖出成功',
            'paltform_transfer'=>'平台转账成功',
            'recharge_admin'=>'后台充值',
            'admin_minus'=>'后台扣款'
        );
        $this->ajax(-101, '获取成功',$arr);
    }
    
    //获取用户资金明细
    public function actionGetUserFundsDetails(){
        //判断是否登录
        $this->checkLogin();
        $uid = $this->_userinfo['uid'];

        $page = $this->getPageCondition();
        $type = $this->_post('type')?trim($this->_post('type')):'';

        $arr = D('user')->GetUserFundsDetails($type, $uid, $page['start_num'], $page['page_num']);
        if($arr){
            // 临时应ios端要求(因为掉错字段了，暂时这样处理，等待下次app更新改正code)
            foreach ($arr as &$val) {
                $val['amount'] = $val['money'];
            }
            unset($val);
            $this->ajax(-101,'获取成功', $arr);
        }else{
            $this->ajax(-102,'暂时没有更多数据！');
        }
    }

	public function actionUserSign() //用户签到
	{
        $this->checkLogin(); //判断登陆
        $uid = $this->_userinfo['uid'];
		$tag = $this->_post('tag');
		if($tag==1) //检测签到状态
		{
               $sfg = D('Usersignlog')->chkSign($uid);
			   if($sfg===false)
			   {
                    $this->ajax(-104,'参数错误');
			        exit();
			   }
			   if($sfg==1)
			   {
                   $this->ajax(-102,'今日已签到');
			       exit();
			   }
			   if($sfg==-1)
			   {
                   $this->ajax(-105,'未签到');
			       exit();
			   }
		}
		$fg = D('Usersignlog')->sign($uid);
		if($fg==false)
		{
           $this->ajax(-104,'参数错误');
			exit();
		}
		if($fg==1)
	   {
           $score = D('usersignlog')->getSignScore($uid);
           $msg = '签到成功，获得'.$score['score'].'积分';
           if(in_array($score['sign_day'], array(7,14,30))) $msg = '您已连续签到'.$score['sign_day'].'天，获得'.$score['score'].'积分';

           if($score['score'] == 999) $msg = '恭喜您获得'.$score['score'].'积分，祝您端午快乐！';
		   $this->ajax(-101,$msg);
		   exit();
	   }

	   if($fg==-1)
	   {
		   $this->ajax(-102,'今日已签到');
		   exit();
            
	   }
	   if($fg==-2)
	   { 
            $this->ajax(-103,'签到失败');
		   exit();
	   }
	}
}

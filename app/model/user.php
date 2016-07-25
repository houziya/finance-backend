<?php

/**
 * user模型 会员管理
 * 
 */
class model_user extends model_abstruct {

	protected $tableName = 'user';
	protected $_lists = array(); //全部会员
	protected $cachename = 'model_user_list'; //地区缓存名称 缓存一周

    const APP_LOGIN_USER_INFO = 'APP_LOGIN_USER_INFO'; //注册成功后登陆信息的KEY名称
	const USER_TYPE_PERSON=1;//投资方
	const USER_TYPE_COMPANY=2;//项目方
	//会员状态
    static public $status_arr = array(
            0  => array('id' => 0, 'name' => '待审', 'style' => 'class="red"'),
            1  => array('id' => 1, 'name' => '正常', 'style' => 'class="green"'),
            -1 => array('id' => -1, 'name' => '已删除', 'style' => 'class="gray4"'),
    );
	//用户类型
	static public $type_arr = array(
		1 => array('id' => 1, 'name' => '投资方', 'style' => 'class="green"'),
		2 => array('id' => 2, 'name' => '项目方', 'style' => 'class="green"'),
	);
	//用户项目发布状态（由于数据表字段的修改  暂无用）
	static public $project_arr = array(
		0 => array('id' => 0, 'name' => '未发布', 'style' => 'class="green"'),
		1 => array('id' => 1, 'name' => '已发布', 'style' => 'class="red"'),
	);
	//实名认证状态
	static public $isidcard_arr = array(
		0 => array('id' => 0, 'name' => '待添加', 'style' => 'class="gray4"'),
		1 => array('id' => 1, 'name' => '待审核', 'style' => 'class="red"'),
		2 => array('id' => 2, 'name' => '实名审核通过', 'style' => 'class="green"'),
		3 => array('id' => 3, 'name' => '易宝审核通过', 'style' => 'class="blue"'),
		-1 => array('id' => -1, 'name' => '实名审核失败', 'style' => 'class="red"'),
		-2 => array('id' => -2, 'name' => '易宝审核失败 ', 'style' => 'class="red"'),
	);
	//注册来源
	//1后台 2web 3wap 4ios 5android
	static public $source_arr = array(
		1 => array('id' => 1, 'name' => '后台'),
		2 => array('id' => 2, 'name' => 'web'),
		3 => array('id' => 3, 'name' => 'wap'),
		4 => array('id' => 4, 'name' => 'ios'),
		5 => array('id' => 5, 'name' => 'android'),
	);
    
     
    //投资人认证审核状态
    static public $identity_arr = array(
		-1 => array('id' => -1, 'name' => '驳回重审', 'style' => 'class="red"'),
		1  => array('id' => 1, 'name' => '待审核', 'style' => 'class="red"'),
		2  => array('id' => 2, 'name' => '审核通过', 'style' => 'class="green"'),
    );
	
    //性别
    static public $sex_arr = array(
		0  => array('id' => 0, 'name' => '保密'),
		1  => array('id' => 1, 'name' => '男'),		
		2  => array('id' => 2, 'name' => '女'),	
    );
    
    //回访状态
	static public $callback_status_arr = array(
		0   =>  array('id'  =>  0,   'name'  =>  '未回访',        'style'  =>  'class="red"'),
		1   =>  array('id'  =>  1,   'name'  =>  '已回访',        'style'  =>  'class="green"'),
		2   =>  array('id'  =>  2,   'name'  =>  '已回访并特殊标记', 'style'  =>  'class="red"'),
		3   =>  array('id'  =>  3,   'name'  =>  '需要再次回访',    'style'  =>  'class="red"'),
	);
	/**
	 * 获取用户详情
	 * @param int $uid 用户ID|用户名|email
	 * @param string $field 返回指定字段
	 * @param bool $delcache 是否删除缓存,默认不删除
	 * @return array
	 */
	static public function getInfo($uid, $field = '', $delcache = false) {
		if (empty($uid)) return $field ? '' : array();
		$cachename = 'model_user_info_' . $uid;
		$info = S($cachename);
		if (empty($info) || $delcache)
		{
			if (is_numeric($uid)) {
				$where = strlen($uid) == 11 ? array('mobile' => $uid) : array('uid' => $uid);
			} elseif (strpos($uid, '@') === false) {
				$where = array('username'=> $uid);
			} else {
				$where = array('email'=>$uid);
			}
			$info = M('user')->where($where)->find();
			if (empty($info)) return $field ? '' : array();
			$info['usernick'] = empty($info['usernick']) ? $info['username'] : $info['usernick'];	
			$info['user_data'] = array();
			//todo  用户扩展表信息
			$cachename = 'model_user_info_' . $info['uid'];
			S($cachename, $info);
			$cachename = 'model_user_info_' . $info['username'];
			S($cachename, $info);
			if (!empty($info['email'])) {
				$cachename = 'model_user_info_' . $info['email'];
				S($cachename, $info);
			}
			if (!empty($info['mobile'])) {
				$cachename = 'model_user_info_' . $info['mobile'];
				S($cachename, $info);
			}
            // 用户身份类型
            //$info['usertype'] = model_userauthentication::getUserType($uid);
		}
		//生成头像缩略图地址
		//$info['face_img'] = helper_tool::img_url_show($info['face'],'face');
		return $field ? $info[$field] : $info;
	}

	/**
	 * 用户名转用户ID
	 * @param int $username 用户名
	 * @return array
	 */
	static public function username2userid($username) {
		return self::getInfo($username, 'uid');
	}

	/**
	 * 用户ID转用户名
	 * @param int $uid 用户ID
	 * @return array
	 */
	 static public function userid2username($uid) {
		return self::getInfo($uid, 'username');
	}

	/**
	 * 设置用户登录密码
	 * @param int $uid 用户ID
	 * @param string $newpwd 新密码
	 * @param string $oldpwd 旧密码
	 * @return return
	 */
	static public function setLoginPwd($uid, $newpwd, $oldpwd = '') {
		if (!is_numeric($uid)) return false;
		$result = M('user')->where("uid='$uid'")->field("uid,password")->find();
		if (!$result) return false;

		//验证原密码是否正确
		if ($oldpwd) {
			$pwd = helper_tool::pwdEncode($oldpwd);
			if ($pwd != $result['password']) {
				return false;
			}
		}
		//修改系统用户密码
		$data = array();
		$data['password'] = helper_tool::pwdEncode(trim($newpwd));
		M('user')->where(array('uid' => $uid))->save($data);
		self::getInfo($uid, '', true);
		return true;
	}

	/**
	 * 得到推广链接
	 * @param int $uid 用户ID
	 * @return string
	 */
	static public function getPromoteUrl($uid) {
		$href = url('www-register/index') . '?k=' . authcode($uid, 'ENCODE');
		return $href;
	}

	/**
	 * 检查用户邮箱是否存在
	 * @param string $email 邮箱
	 * @return boolean  true存在 false不存在
	 */
	public function checkEmailExist($email) {
		$num = $this->where(array('email' => $email))->count();
		if ($num > 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 检查用户名是否存在
	 *
	 * @param string $username 用户名
	 * @return boolean  true存在 false不存在
	 */
	public function checkUserNameExist($username) {
		$num = $this->where(array('username' => $username))->count();
		if ($num > 0) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 检查用户手机号是否存在
	 *
	 * @param string $username 用户名
	 * @return boolean  true存在 false不存在
	 */
	public function checkMobileExist($mobile) {
		$num = $this->where(array('mobile' => $mobile))->count();
		if ($num > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * 检测手机号码真实性
	 * @param string $mobile
	 * @param int $uid 用户ID
	 * @return boolean
	 */
	public function checkMobileOwner($mobile, $uid)
    {
		if (!preg_match("/^(1[3-9])\d{9}$/", $mobile)) {
            return false;;
        }

        // 验证手机号拥有者
        $condition = array('uid' => $uid, 'mobile' => $mobile);
        $count = M($this->tableName)->where($condition)->count();
        if ($count < 1) {
            return false;
        }

        return true;
	}

	/**
	 * 得到登陆用户基本信息
	 * @param string $field 返回指定用户信息字段
	 */
	static public function getLoginUser($field = '') {
		static $myuser = array();
		if (empty($myuser)) {
			$auth = cookie('auth');
			if (empty($auth)) return array();
			$auth = authcode($auth, 'DECODE');
			if (empty($auth)) return array();
			$auth = explode("\t", $auth);
			if (!is_array($auth)) return array();
			
			//检查登录用户密码有无修改
			$myuser = self::getInfo($auth[0]);
			if ($myuser['password'] != $auth[1]) return array();
		}
		return $field ? (isset($myuser[$field]) ? $myuser[$field] : '') : $myuser;
	}

	/**
	 * 前台用户登录
	 * @param int $uid 用户uid
	 * @param int $expire 过期时间
	 * @param int $log 是否加入登录日志 0否 1后台 2web 3wap 4ios 5android
	 */
	static public function setLogin($uid, $expire = 86400, $log = 2) {
		$user = self::getInfo($uid);
		if (empty($user)) return 0;
		// 登陆信息正确  开始访问授权
		$expire = !empty($expire) ? (int)$expire : 0;
		$auth = $user['uid'] . "\t" . $user['password'];
		cookie('auth', authcode($auth, 'ENCODE'), $expire);

		if ($log && is_numeric($log)) {
			//添加到登陆记录表
			model_login::loginAdd($uid, $log);
		}
		return 1;
	}

	/**
	 * 用户注销登录
	 *
	 * @return
	 */
	static public function setLogout() {
		cookie('auth', null);
	}

	/**
	 * 检查用户名是否包含禁用关键字
	 * @param string $username 用户名
	 * @return int 0 不包含 >0 包含
	 */
	static public function checkBadKeyWords($username) {
		$web_keywords = include(CONF_PATH . '/blackwords.php');
		if (!empty($web_keywords) && is_array($web_keywords)) {
			$web_keywords = implode('|', $web_keywords);
			$reg = '/^.*(' . $web_keywords . ').*$/';
			return preg_match($reg, $username);
		}
		return '0';
	}

	/**
	 * 删除会员
	 * @param int $id 会员ID
	 * @param bool $clear 是否物理删除 0否 1是
	 * @return int
	 */
	public function userDelete($uid, $clear = false) {
		if ($clear == 1) {
			$res = $this->where("uid = '{$uid}'")->delete();
			if ($res) {
				//todo 删除用户相关的所有关联扩展表信息
			}
		} else {
			$data['mobile']  =  NULL;
			$data['email']   =  NULL;
			$data['status']  =  '-1';
			
			$res = $this->where("uid = '{$uid}'")->save($data);
		}
		return $res;
	}

	/**
	 * 冻结
	 * @param int $id 会员ID
	 * @return int
	 */
	public function userLock($uid, $type = 'lock') {
		if ($type == 'lock') {
			$data['status'] = 1;
		} else {
			$data['status'] = 0;
		}
		$res = $this->where(array('uid' => $uid))->save($data);
		return $res;
	}
	
	/**
	 * 删除、批量删除会员收货地址
	 * @param int $id 投资人身份认证ID
	 * @param boole $clear 是否物理删除 0否 1是
	 * @return int
	 */
	public function deliveryaddressDelete($id,$clear = false) {
		
		if($clear == 1){
			$res = M('UserContact')->where("id ={$id}")->delete();
			if($res){
				//todo 删除会员组对应的拓展表
			}
		}else{
			$res = M('UserContact')->where("id ={$id}")->save(array('is_delete'=>1));
		}
		return $res;
	}
	
	//注册是检验身份证是否可以使用
	//huangnan 2015.11.06
	public static  function registerCheckIdCard($idcard)
	{
		$rs = helper_idcard::validateIDCard($idcard);
		if($rs == false)
		{
			return false;
		}
		$count = M('user_body')->where(array('u_body_num'=>$idcard))->count();
		if($count > 0)
		{
			return false;
		}
		return true;
	}
	
	//根据身份证号获取身份证详情
	//一个18位身份证的组成部分=省（3位）市（3位）年（4位）月（2位）日（2位）校验位（4位）
	//前六位是地区编号，倒数第二位为性别编号(奇为男，偶为女)
	public function getIdCardInfo($idcard){
		$rs = helper_idcard::validateIDCard($idcard);
		if(empty($rs)) return array();
		
		if(strlen($idcard) == 15){
			$idcard = helper_idcard::convertIDCard15to18($idcard);
		}
		
		$num = substr($idcard, 0, 6);
		$row = M("idcard")->where(array('code' => $num))->find();
		if(empty($row)) return array();
		
		//性别
		$sex = substr($idcard, -2,1);
		if(in_array($sex, array(0,2,4,6,8))){
			$sex = 0; //女
		}else{
			$sex = 1; //男
		}
		
		//生日
		$birthday = substr($idcard, 6, 4).'-'.substr($idcard, 10, 2).'-'.substr($idcard, 12, 2);
		
		$res = array(
			'idcard' => $idcard,
			'address' => $row['address'],
			'borthday' => $birthday,
			'sex' => $sex,
			'sex_name' => $sex ? '男' : '女',
		);
		return $res;
	}
	

	/**
	 * 获取用户资产（总金额、总余额、投资中金额、提现中金额）
	 * @param int $uid 投资人ID
	 * @param
	 * @return array
	 */
     public function getProperty($uid){
		
		$property_info    = M('account')->field('total,amount,user_amount,project_amount,freeze_amount')->where(array('uid'=>$uid))->find();
		
		
		$M  =  M();
		//投资总金额
		$investment_info  = $M->query("select sum(`amount`) as investment_amount from project_investment where uid='{$uid}' and status in(1,3) group by uid ");
		//投资中金额
		$investment_total = $M->query("select sum(`amount`) as investment_total from project_investment where uid='{$uid}' and status in(0) group by uid ");
		//项目提现中金额
		$drawmoney_info   = $M->query("select sum(`amount`) as drawmoney_amount from project_drawmoney where uid='{$uid}' and status = 0 group by uid ");
        //充值总金额
        $recharge_info    = $M->query("select sum(`amount`) as recharge_amount from account_recharge_log where uid='{$uid}' and status = 1 group by uid ");
		
		return $arr = array(
			'info'    =>$property_info,
			'investment_total'  => isset($investment_total[0]['investment_total']) ? $investment_total[0]['investment_total'] : 0,
			'investment_amount' => isset($investment_info[0]['investment_amount']) ? $investment_total[0]['investment_amount'] : 0,
			'drawmoney_amount'  => isset($drawmoney_info[0]['drawmoney_amount']) ? $drawmoney_info[0]['drawmoney_amount'] : 0,
            'recharge_amount'  => isset($recharge_info[0]['recharge_amount']) ? $recharge_info[0]['recharge_amount'] : 0,
		);
	}
	
	/**
	 *获取用户投资各个状态的项目总数（全部、草稿、未审核、未通过、待审核、预热中、融资中、融资失败、融资成功）
	 * @param int $uid 投资人ID
	 * @param
	 * @return array
	 */
	public function getInvestmentProNums($uid){
		
		$investProNums['zs_nums'] = M('project')->where(array('uid'=>$uid))->count();
		$investProNums['ws_nums'] = M('project')->where(array('uid'=>$uid,'status'=>0))->count();
		$investProNums['ds_nums'] = M('project')->where(array('uid'=>$uid,'status'=>1))->count();
		$investProNums['yr_nums'] = M('project')->where(array('uid'=>$uid,'status'=>2))->count();
		$investProNums['rz_mums'] = M('project')->where(array('uid'=>$uid,'status'=>4))->count();
		$investProNums['sb_nums'] = M('project')->where(array('uid'=>$uid,'status'=>5))->count();
		$investProNums['wc_nums'] = M('project')->where(array('uid'=>$uid,'status'=>6))->count();
		$investProNums['wg_nums'] = M('project')->where(array('uid'=>$uid,'status'=>-1))->count();
		
		return $investProNums;
	
	}
	

            /**
         * 新增用户数据
         * @param $data
         * @return array
         * @author wangbingang<67063492@qq.com>
         */
        public static function addUser(array $data, $data_user_body = "") {
            if (!is_array($data)) {
                return array('status' => -1, 'msg' => '请传入正确的数组值');
            }
            if (!isset($data['username']) || !helper_tool::checkUserName($data['username'])) {
                return array('status' => -2, 'msg' => '用户名格式不正确');
            }
            if (!isset($data['mobile']) || !helper_tool::checkMobile($data['mobile'])) {
                return array('status' => -3, 'msg' => '手机号码不正确');
            }
            if (!isset($data['password']) || empty($data['password'])) {
                return array('status' => -4, 'msg' => '密码不能为空');
            }
            if (!isset($data['source']) || empty($data['source'])) {
                return array('status' => -5, 'msg' => '注册来源不能为空');
            }
            $data['regist_time'] = time();
            $data['regist_ip'] = getIp();
            $ip_data = model_area::ip2area($data['regist_ip']);

            $result = M('user')->add($data);
            if ($result) {

                return array('status' => 1, 'msg' => '添加成功', 'data' => $result);
            } else {
                return array('status' => -7, 'msg' => '系统错误，注册失败，请稍后再试', 'data' => $result);
            }
        }
        
       
	
	/*
	 * 检查用户的登录状态
	*/
	public static function checkLogin()
	{
		//return self::$_login_user_id?true:false;
		return isset($_SESSION[C('sys_cookie_prefix').'login_user_id']) ? TRUE : FALSE;
	}
	
	/*
	 * 修改用户名
	 * @author liufei
	 * @param string $var info
	 * @return return
	 */
	public function setNewUsername($uid = 0, $username = ''){
		if(empty($uid) || empty($username)) return false;
		$rs = M()->execute("UPDATE user SET username = '{$username}',is_username = 1 WHERE uid = '{$uid}' AND is_username = 0");
		if($rs){
			M()->execute("UPDATE account_draw_log SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE active_sendphoto_score SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE attachment SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE comment SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE comment_reply SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE comment_reply SET comment_username = '{$username}' WHERE comment_uid = '{$uid}'");
			M()->execute("UPDATE conscribe SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE credit SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE credit_log SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE feedback SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE gifts_order SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE happiness_to SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE lianmeng_platform SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE lianmeng_user SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE login SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE luck_chance_log SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE luck_log SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE luck_user SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE merchants SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE message SET sent_username = '{$username}' WHERE sent_uid = '{$uid}'");
			M()->execute("UPDATE message SET receive_username = '{$username}' WHERE receive_uid = '{$uid}'");
			M()->execute("UPDATE project_announce SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE project_drawmoney SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE project_drawmoney_log SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE project_drawmoney_tickets SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE project_graded_log SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE project_investment SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE project_investment_pre SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE project_new SET username = '{$username}' WHERE uid = '{$uid}'");		
			M()->execute("UPDATE user_contact SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE user_log SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE user_sendemail_log SET username = '{$username}' WHERE uid = '{$uid}'");
			M()->execute("UPDATE user_sendsms_log SET username = '{$username}' WHERE uid = '{$uid}'");
		}
		return $rs;
	}
	
	/*
	 * 密码基本验证
	* huangnan 2015.04.07
	*/
	public function passwordBasicCheck($password,$password_new,$password_new_re)
	{
		if(!empty($password) && $password == $password_new)
		{
			$result['status'] = '-1';
			$result['msg'] = '新旧密码不能相同';
		}elseif($password_new != $password_new_re)
		{
			$result['status'] = '-2';
			$result['msg'] = '两次输入密码不一致';
		}elseif(!helper_tool::checkPassword($password_new))
		{
			$result['status'] = '-3';
			$result['msg'] = '密码要求6-16位数字或字母';
		}else
		{
			$result['status'] = '1';
		}
		return $result;
	}

    /**
     * 检测用户密码是否合法
     * @param $uid
     * @param $pass
     * @return bool
     */
    public function checkPass($uid,$pass) {
        $oepass = $this->where('uid='.$uid)->getField('password');
        if ($oepass == helper_tool::pwdEncode($pass)) {
            return true;
        }
        return false;
    }
	
	
    /**
     * 根据条件获取用户信息列表
     * @param string/array $condition 条件集合
     * @param string/array $fields 获取的字段
     * @param int $start 分页起始位置
     * @param int $length 分页长度
     * @return array
     * @author quanzhijie
     */
    public function getUsers($condition, $fields = '*', $start = 0, $length = 0)
    {
		$length = $length > 0 ? $length : 10000;
        $users = M('user')->field($fields)->where($condition)->limit("{$start}, {$length}")->select();
        return $users ? $users : array();
    }
	
    /**
     * 根据用户ID获取用户信息
     * @param int $uid 用户ID
     * @param string/array $fields 获取的字段
     * @return array
     * @author quanzhijie
     */
    public function getUserByUid($uid, $fields = '*')
    {
        $user = M($this->tableName)->field($fields)->where("uid = '{$uid}'")->find();
        if ($user) {
            self::fetchUserOther($user);
        }
        return $user ? $user : array();
    }

    /**
    * 渲染用户其他信息
    * @param array $user 用户信息
    * @return array
    */
    public static function fetchUserOther(&$user)
    {
        if ($user) {
            // 用户名
            $user['name'] = $user['realname'] ? $user['realname'] : ($user['usernick'] ? $user['usernick'] : $user['username']);
            // 头像
            $user['face_img'] = helper_tool::img_url_show($user['face'], 'face');
            $user['face_img_url'] = helper_tool::img_url_show('/s/v2/images/user_index/user_index-img.png');
            $user['face'] = $user['face_img'] ? $user['face_img'] : $user['face_img_url'];
        }
        return $user;
    }

    /**
     * 统计会员总数
     * @param array $condition 条件集合
     * @return int
     * @author bjs
     */
    public function getUserCount(array $condition)
    {
        $count = M($this->tableName)->where($condition)->count();
        return $count;
    }
    
	/**
	 * 获取用户分组
	 * @param $group_id int    用户组id 默认为空
	 * @return $str     string 用户组构成的组字符串
	 * @author liurengang
	 * @date   2015/4/22 星期五
	 * 
	 */
	public function getUserGroup($group_id=''){
		
		$group_info = M('userGroup')->field('id,name,sort,add_time')->select();
		$str = "<select name='data[group_id]' id='group_id'>
		<option value=''>请选择用户组</option>";
		foreach($group_info as $key => $val){
			if(!empty($group_id)){
				if($val['id'] == $group_id){
					$str .= "<option value='".$val['id']."' selected>{$val['name']}</option>";
				}else{
					$str .= "<option value='".$val['id']."' >{$val['name']}</option>";
				}
			}else{
				$str .= "<option value='".$val['id']."'>{$val['name']}</option>";
			}
		}
		
		$str .= "</select>";
		return $str;
	}

    /**
     * 根据用户ID更新用户未读私信数
     * @param int $uid 用户ID
     * @param string $value 变更值步长
     * @return boolean
     * @author quanzhijie
     */
    public function updateMewpmByUid($uid, $value = '+ 1')
    {
        $sql = "UPDATE user SET newpm = newpm {$value} WHERE uid = '{$uid}' LIMIT 1";
        return M($this->tableName)->execute($sql);
    }

    /**
     * 生成唯一的随机用户名
     * @author Baijiansheng
     */
    static public function getRandUsername()
    {
        $username = 'RRT'.helper_string::randString(8,0);
        if(!M('user')->where(array('username'=>$username))->count()){
            return $username;
        }
        self::getRandUsername();
    }

    /**
     * 计算投资等级 心2  这个规则是五颗星等于一个红心
     * @param unknown_type $score
     * @return multitype:string number
     */
    public static function MindScroeLevel($score,$level=array(),$i=0)
    {
        //星级转换规则
        define('STAR',1);//星星
        define('HEART',5);//红心
        define('DIAMOND',25);//钻石
        define('CROWN',125);//皇冠
        define('MAX_CROWN',10);//最大皇冠
        $rule = array(SCROWN=>CROWN,SDIAMOND=>DIAMOND,SRED=>HEART,SSTAR=>STAR);
        $key = array_keys($rule);
        if($score > 0)
        {
            $count = count($key);
            if($key[$i])
            {
                $$key[$i] = floor($score/$rule[$key[$i]]);
                if($key[$i]==SCROWN)
                {
                    if($$key[$i] >=MAX_CROWN )
                    {
                        $level[$key[$i]] = MAX_CROWN;
                        return $level;
                    }
                    else
                        $level[$key[$i]] = $$key[$i];
                }
                else
                    $level[$key[$i]] = $$key[$i];
                $surplus = $score%$rule[$key[$i]];
                $i++;
                return self::MindScroeLevel($surplus,$level,$i);
            }
        }
        return $level;
    }
    
    
    /**
     *  提现前 查询易宝借口 确定是否绑卡 
     *  huangnan 2015.06.09
     *  $uid 用户ID
     *  $status  数据库is_bindbank状态
     */
    public function CheckBankBind($uid,$status=0)
    {
    	$result = array();
    	$uid = intval($uid);
    	if($uid < 1)
    	{
    		return $result;
    	}
    	//查询易宝数据
    	$info['platformUserNo'] = $uid;
    	$yeePay = new helper_yeepay();
    	$data_yeepay = $yeePay->yu_e_cha_xun($info);
    	//判断易宝数据
    	if(!empty($data_yeepay['bank']) && !empty($data_yeepay['cardNo']))
    	{
    		$result['card_bind'] = model_account::$bank_info[$data_yeepay['bank'].'_DEBITNOCARD_DEBIT'];
    		$result['card_num'] = $data_yeepay['cardNo'];
    		// 如果   易宝数据已绑卡 和  数据库状态不一致  则更新数据库
    		if($status == 0)
    		{
    			$condition_bindbank = array('uid'=>$uid);
    			$condition_save = array('is_bindbank'=>'1');
    			$this->where($condition_bindbank)->save($condition_save);
    			model_user::getInfo($uid,'',true);
    		}
    	}else
    	{
    		// 如果   易宝数据未绑卡 和  数据库状态不一致  则更新数据库
    		if($status == 1)
    		{
    			$condition_bindbank = array('uid'=>$uid);
    			$condition_save = array('is_bindbank'=>'0');
    			$this->where($condition_bindbank)->save($condition_save);
    			model_user::getInfo($uid,'',true);
    		}
    	}
    	return $result;   	
    }

    /**
     * 根据规则获取用户环信登录密码
     * @param int $uid 用户ID
     * @return string
     */
    public static function getHxPassword($uid)
    {
        $fix = 'rrt_chat';
        $pwd = D('user')->getInfo($uid, 'password');
        return md5($pwd . $fix);
    }
	
	/**
     * 根据规则获取用户环形昵称
     * @param int $uid 用户ID
     * @return string
     */
    public static function getHxNickname($uid)
    {
        $info = D('user')->getInfo($uid);
        return $info['realname'] ? $info['realname'] : $info['username'];
    }

    /**
     *  修改环信用户登录密码
     */
    public function updateHxPassword($uid)
    {
        $chat_user = D('chat/user')->getChatUser(array('uid'=>$uid));
        if(!empty($chat_user)){
            $result = D('chat/chat')->updatePassword($uid, self::getHxPassword($uid));
        }
        return $result ? $result : array();
    }

    /**
     * 获取用户状态
     * @param int $uid 用户ID
     * @return int
     */
    public static function getUserStatusByUid($uid)
    {
        $user = self::getInfo($uid);
        $userStatus = 0;

        if ($user) {
        	$userBody = M('user_body')->where("uid = '{$uid}'")->find();
			$user_enterprise = D('UserEnterpriseInfo')->getUserEnterpriseInfo(array('uid' =>$uid));
        	if (($user['is_idcard'] != 3)  && ($user['is_enterprise'] < 1)) {
        		if (!$user['mobile']) {
        			$userStatus = '-1';
        		} elseif(!$user['email']) {
        			$userStatus = '-2';
        		} else {
        			if ($userBody['realname'] && $userBody['u_body_num']) {
        				$userStatus = '-4';
        			} else {
        				//$userStatus = '-3';
						//企业信息
                        if($user_enterprise['legal'] && $user_enterprise['legal_id_no'] && $user_enterprise['enterprise_name'] && $user_enterprise['bank_license'] && $user_enterprise['org_no'] && $user_enterprise['business_license'] && $user_enterprise['tax_no'] && $user_enterprise['type'] && $user_enterprise['contact'])
                        {
                            $userStatus = '1';
                        }else{
        				    $userStatus = '-3';
                        }
        			}
        		}
        	} elseif(($user['is_auto_yeepay'] != 1) && ($user['is_idcard'] == 3) && ($user['is_enterprise'] == 3)) {
        		$userStatus = '-5';
        	} else {
        		$userStatus = '1';
        	}
        }

        return $userStatus;
    }

    /**
     * 获取用户头像列表
     * @param array $uids 用户ID集合
     * @param int $cacheTime 缓存时间秒
     * @return array
     */
    public static function getUsersByUids($uids, $cacheTime = 30)
    {
        $uids[] = '0';
        $condition = array('uid' => array('in', $uids));
        $cacheKey = "model_user_getUsers" . helper_cache::makeKey($condition);
        $callback = array(D('user'), 'getUsers');
        $users = helper_cache::getSmartCache($cacheKey, $callback, $cacheTime, array($condition));
        helper_tool::setKeyArray($users, 'uid');
        return $users;
    }

	/*
     * 验证token是否成功
     *
     * @param $access_token 加密秘钥串
     * @param $signtime 加密时间戳
     * @param 加密后的值
     * @return 返回成功或者失败  boolean
     */
    public function verifyToken($access_token, $signtime = '', $sign = ''){
        if((RUN_MODE != 'deploy')){
            if(empty($access_token)) return array('status'=>-6, 'msg'=>'未授权');
        }else{
            if (empty($access_token) || empty($signtime) || empty($sign)) return array('status'=>-6, 'msg'=>'未授权');
        }

        //检查请求时间是否过期
        $_t = strtotime($signtime);
        $_t = abs(time() - $_t);

        //验证加密是否正确
        $sign2 = md5($signtime.'&'.$access_token);
        if(RUN_MODE == 'deploy') {
            if($sign != $sign2)  return false;//加密未通过
        }
        $key = self::APP_LOGIN_USER_INFO . md5($access_token);
        $cache = cache::getInstance('redis');
        $userinfo = $cache->get($key);
        //$userinfo = S($access_token);
        if (!empty($userinfo)) {
            return array('status'=>-1, 'msg'=>'验证成功','data'=>$userinfo);
        }else{
            return array('status'=>-4, 'msg'=>'登录信息已过期，请重新登录');
        }
    }

    /**
    * 近一年的融资统计、分红统计
    * @param int $uid 用户ID
    * @return array
    */
    public function getUserYearStatistics($uid)
    {
        $investment = D('ProjectInvestment')->getUserYearInvestment($uid);
        $share = D('ProjectShareCollect')->getUserYearShare($uid);
        return array('investment' => $investment, 'share' => $share);
    }

    /*
     * 获取用户资金明细
     * @param string/array $condition 条件
     * @param string/array $fields 获取的字段
     * @param int $start 分页起始位置
     * @param int $length 分页长度
     * @return array
     */
    public function GetUserFundsDetails($type, $uid, $start = 0, $length = 10, $fields = '*', $order = 'add_time desc')
    {
        if(empty($type)){
            $condition = array('uid'=>$uid,'is_show'=>1);
        }else{
            $condition = array('uid'=>$uid,'type'=>$type,'is_show'=>1);
        }
        $arr = M('account_log')->field($fields)->where($condition)->order($order)->limit("{$start}, {$length}")->select();
        if(!empty($arr)){
            foreach($arr as $key=>$val){
                $arr[$key]['add_time'] = date('Y-m-d H:i:s', $val['add_time']);
                if($arr[$key]['sign']==0) {
                    $arr[$key]['sign'] = '-';
                } elseif($arr[$key]['sign']==1) {
                    $arr[$key]['sign'] = '+';
                } else {
                    $arr[$key]['sign'] = '';
                }
            }
        }else{
            return array();
        }
        return $arr;
    }
    //获取用户信息
    public function getUserAuthentication($uid){
        $model = new model;
        $res = $model->table("user as u ")
               ->field(" u.uid,u.realname,province,city,area,u.type,u_body_num as person_cardid,mobile,address ")
               ->join(" left join user_body as ua on u.uid=ua.uid ")
                 ->join(" left join user_info as ui on u.uid=ui.uid ")
                 ->where("u.uid = '{$uid}'")->find();
           // echo  $model->getLastSql();
               return $res;
    }
}

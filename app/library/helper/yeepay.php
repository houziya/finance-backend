<?php
/*
 * 易宝支付接口类
 */
class helper_yeepay
{
    private $wg_path='https://member.yeepay.com/member/bha/';//网关模式
    private $zl_path='https://member.yeepay.com/member/bhaexter/bhaController';//直连模式
    //private $wg_path='http://qa.yeepay.com/member/bha/';//网关模式
    //private $zl_path='http://qa.yeepay.com/member/bhaexter/bhaController';//直连模式
    private $platformNo;
    private $pay;
    private $feemode;// PLATFORM  商户支付  USER为用户支付手续费  此处为扣费模式
	private $setopt = array(
//        'port'=>443, //访问的端口,http默认是 80
        'userAgent'=>'', //客户端 USERAGENT,如:"Mozilla/4.0",为空则使用用户的浏览器
        'timeOut'=>30, //连接超时时间
        'useCookie'=>false, //是否使用 COOKIE 建议打开，因为一般网站都会用到
        'ssl'=>false, //是否支持SSL
        'gzip'=>false, //客户端是否支持 gzip压缩
    );
    private $setopt2 = array(
//        'port'=>8088, //访问的端口,http默认是 80
        'userAgent'=>'', //客户端 USERAGENT,如:"Mozilla/4.0",为空则使用用户的浏览器
        'timeOut'=>30, //连接超时时间
        'useCookie'=>false, //是否使用 COOKIE 建议打开，因为一般网站都会用到
        'ssl'=>false, //是否支持SSL
        'gzip'=>false, //客户端是否支持 gzip压缩
    );
    /*
     * 初始化
     */
    public function __construct(){
        $this->pay = C('pay');
        $this->platformNo = $this->pay['platformno'];
        if(RUN_MODE != 'deploy'){
                $this->wg_path = 'http://220.181.25.233:8081/member/bha/';
                $this->zl_path = 'http://220.181.25.233:8081/member/bhaexter/bhaController';
                $this->platformNo = '12345678910';
        }
        $this->feemode = $this->pay['feemode'];
    }
    /**
     * 自动投标授权
     * @param type $params
     * @return type
     */
    public function authorize_autotransfer($params)
    {
        $xml_array['platformNo'] = $this->platformNo;
        $xml_array['callbackUrl'] = $params['callbackUrl'];
        $xml_array['notifyUrl'] = $params['notifyUrl'];
        $xml_array['platformUserNo'] = $params['platformUserNo'];
        $request_no = $this->_request_no(array(__FUNCTION__,$xml_array));//记录流水并返回流水号
        $xml_array['requestNo'] = $request_no; //注册流水号
        if($request_no){
            $params_xml = $this->_replace_xml($xml_array);
            $sign = $this->signcontent($params_xml);
            return array('req'=>$params_xml,'sign'=>$sign,'path'=>$this->wg_path.'toAuthorizeAutoTransfer');
        }
        return array('req'=>'','sign'=>'','path'=>'');
    }
    
	/**
     * 解绑卡
     * @param type $params
     * @return type
     */
    public function unbind_card($params)
    {
        $xml_array['platformNo'] = $this->platformNo;
        $xml_array['platformUserNo'] = $params['platformUserNo'];
        $request_no = $this->_request_no(array(__FUNCTION__,$xml_array));//记录流水并返回流水号
        $xml_array['requestNo'] = $request_no; //注册流水号
        if($request_no){
            $params_xml = $this->_replace_xml($xml_array);
            $sign = $this->signcontent($params_xml);
            $post = array('req'=>$params_xml,'sign'=>$sign,'service' => 'UNBIND_CARD');
			$rs = $this->_post($post,$this->setopt);
            return $rs;
        }
    }
    public function cancel_authorize_autotransfer($params)
    {
        $xml_array['platformNo'] = $this->platformNo;
        $xml_array['platformUserNo'] = $params['platformUserNo'];
        $request_no = $this->_request_no(array(__FUNCTION__,$xml_array));//记录流水并返回流水号
        $xml_array['requestNo'] = $request_no; //注册流水号
        if($request_no){
            $params_xml = $this->_replace_xml($xml_array);
            $sign = $this->signcontent($params_xml);
            $post = array('req'=>$params_xml,'sign'=>$sign,'service' => 'CANCEL_AUTHORIZE_AUTO_TRANSFER');
            $rs = $this->_post($post,$this->setopt);
            //数据提交后进行回调
            $callbackModeel = new model_pay_callback();
            $callbackModeel->init($this->feemode,'CANCEL_AUTHORIZE_AUTO_TRANSFER',$this->callback);
            $returnInfo  = $callbackModeel->callback(array('uid'=>$params['platformUserNo'],'requestNo'=>$request_no,'code'=>$rs['code'],'prvRequestNo'=>$params['requestNo']), $rs ,$request_no );
            return $returnInfo;
        }
    }
    /*
     * 同步用户
     * $requestNo  全局唯一流水号
     * $params  接口需要的其他参数
     */
    public function tong_bu_user($params){
        $xml_array = array(
            'platformNo' => $this->platformNo,
            'platformUserNo' => $params['platformUserNo'],
            'nickName' => $params['nickName'],
            'realName' => $params['realName'],
            'idCardType' => $params['idCardType'],
            'idCardNo' => $params['idCardNo'],
            'mobile' => $params['mobile'],
            'email' => $params['email'],
            'callbackUrl' => $params['callbackUrl'],
            'notifyUrl' => $params['notifyUrl']
        );

        $request_no = $this->_request_no(array(__FUNCTION__,$xml_array));//记录流水并返回流水号
        $xml_array['requestNo'] = $request_no; //注册流水号		
        $params_xml = $this->_replace_xml($xml_array);
        $sign = $this->signcontent($params_xml);
        return array('req'=>$params_xml,'sign'=>$sign,'path'=>$this->wg_path.'toRegister');
    }

    /*
     * 同步注册企业用户
     * $requestNo  全局唯一流水号
     * $params  接口需要的其他参数
     */
    public function tong_bu_qiye_user($params){
        $xml_array = array(
            'platformNo' => $this->platformNo,
            'platformUserNo' => $params['platformUserNo'],
            'enterpriseName' => $params['enterpriseName'],
            'bankLicense' => $params['bankLicense'],
            'orgNo' => $params['orgNo'],
            'businessLicense' => $params['businessLicense'],
            'taxNo' => $params['taxNo'],
            'legal' => $params['legal'],
            'legalIdNo' => $params['legalIdNo'],
            'contact' => $params['contact'],
            'contactPhone' => $params['contactPhone'],
            'email' => $params['email'],
            'memberClassType' => $params['memberClassType'],
            'callbackUrl' => $params['callbackUrl'],
            'notifyUrl' => $params['notifyUrl']
        );

        $request_no = $this->_request_no(array(__FUNCTION__,$xml_array));//记录流水并返回流水号
        $xml_array['requestNo'] = $request_no; //注册流水号
        $params_xml = $this->_replace_xml($xml_array);
        $sign = $this->signcontent($params_xml);
        return array('req'=>$params_xml,'sign'=>$sign,'path'=>$this->wg_path.'toEnterpriseRegister');
    }

    /*
     * 充值
     */
    public function chong_zhi($params){
        $xml_array = array(
            'platformNo' => $this->platformNo,
            'platformUserNo' => $params['platformUserNo'],
            //'payWay' => $params['payWay'],//支付方式
            'amount' => $params['amount'],//支付金额
            'feeMode' => $params['feeMode'],//费率模式
            'callbackUrl' => $params['callbackUrl'],//回调通知地址
            'notifyUrl' => $params['notifyUrl']//服务器通知地址
        );
        $request_no = $this->_request_no(array(__FUNCTION__,$xml_array));//记录流水并返回流水号
        if($request_no)
        {
            $xml_array['requestNo'] = $request_no; //充值流水号
            $recordData = $xml_array;
            if(!model_pay_record::recordInsert($recordData, 'recharge'))
            {
                return array('req'=>'','sign'=>'','path'=>'');
            }
            $params_xml = $this->_replace_xml($xml_array);
            $sign = $this->signcontent($params_xml);
            return array('req'=>$params_xml,'sign'=>$sign,'path'=>$this->wg_path.'toRecharge');
        }
        else
        {
            return null;
        }
    }
    
    /*
     * 提现
     */
    public function ti_xian($params){
        $xml_array = array(
            'platformNo'        => $this->platformNo,
            'platformUserNo'    => $params['platformUserNo'],
            'feeMode'           => $params['feeMode'],//费率模式
            'callbackUrl'       => $params['callbackUrl'],//回调通知地址
            'notifyUrl'         => $params['notifyUrl'],//服务器通知地址
            'amount'            => $params['amount']
        );
        $request_no = $this->_request_no(array(__FUNCTION__,$xml_array));//记录流水并返回流水号
        $xml_array['requestNo'] = $request_no; //提现流水号
        $recordData = $xml_array;
        if(!model_pay_record::recordInsert($recordData, 'withdraw'))
        {
            return array('req'=>'','sign'=>'','path'=>'');
        }
        $params_xml = $this->_replace_xml($xml_array);
        $sign = $this->signcontent($params_xml);
        return array('req'=>$params_xml,'sign'=>$sign,'path'=>$this->wg_path.'toWithdraw');
    }
    
    
   /**
     * 冻结（投标）
     * 网关
     */
    public function dong_jie_tou_biao($params,$islog=TRUE){
        $xml_array = array(
            'platformNo' => $this->platformNo,
            'platformUserNo' => $params['platformUserNo'],
            'orderNo' => $params['orderNo'],//标的号，自动换款的标的号
            'transferAmount' => $params['transferAmount'],//标的金额
            'targetPlatformUserNo' => $params['targetPlatformUserNo'],//目标会员编号
            'paymentAmount' => $params['paymentAmount'],//冻结金额
            'expired' => '{expired}',//支付时间 超过此时间无法支付
            'callbackUrl' => $params['callbackUrl'],
            'notifyUrl' => $params['notifyUrl']
        );
        if($islog){
            $request_no = $this->_request_no(array(__FUNCTION__,$xml_array));//记录流水并返回流水号
            $xml_array['requestNo'] = $request_no; //流水号
            $recordData = $xml_array;
            $recordData['expired'] = $params['expired'];
            if(!model_pay_record::recordInsert($recordData, 'freeze'))
            {
                return array('req'=>'','sign'=>'','path'=>'');
            }
        }
        else
            $xml_array['requestNo'] = $params['requestNo']; //流水号
        $params_xml = $this->_replace_xml($xml_array);
        $params_xml = str_replace('{expired}', $params['expired'], $params_xml); //替换支付时间
        $sign = $this->signcontent($params_xml);
        return array('req'=>$params_xml,'sign'=>$sign,'path'=>$this->wg_path.'toTransfer');
    }
    
    /**
     * 自动投标
     * 直连接口
     */
    public function auto_transfer($params, $islog = TRUE) {
		$expired = time() + 60;
		$xml_array = array(
			'platformNo' => $this->platformNo,
			'platformUserNo' => $params['platformUserNo'],
			'orderNo' => $params['orderNo'], //标的号，自动换款的标的号
			'transferAmount' => $params['transferAmount'], //标的金额
			'targetPlatformUserNo' => $params['targetPlatformUserNo'], //目标会员编号
			'paymentAmount' => $params['paymentAmount'], //冻结金额
			'notifyUrl' => $params['notifyUrl']
		);
		
		if ($islog) {
			$request_no = $this->_request_no(array(__FUNCTION__, $xml_array)); //记录流水并返回流水号
			$xml_array['requestNo'] = $request_no; //流水号
			$recordData = $xml_array;
			$recordData['expired'] = $expired;
            //$recordData['usertype'] = $params['usertype'];
			if (!model_pay_record::recordInsert($recordData, 'freeze')) {
				return array('req' => '', 'sign' => '', 'path' => '');
			}/*else{
                $project_investor_relation = model_ProjectInvestment::getinvestorRelation($params['platformUserNo'], $params['orderNo']);
                if(empty($project_investor_relation)){
                    D('ProjectInvestorRelation')->add($params['platformUserNo'], $params['orderNo'], $params['usertype']);
                }
            }*/
		} else {
			$xml_array['requestNo'] = $params['requestNo']; //流水号
		} 
		$params_xml = $this->_replace_xml($xml_array);
		//获取易宝签名
		$sign = $this->signcontent($params_xml);
		$post = array('req' => $params_xml, 'sign' => $sign, 'service' => 'AUTO_TRANSFER');
		$rs = $this->_post($post, $this->setopt);
		//数据提交后进行回调
		$callbackModeel = new model_pay_callback();
		$callbackModeel->init($this->feemode, 'TRANSFER', $this->callback);
		$n = 3; //执行n次回调来确保回调正确
		for($i=1;$i <= $n; $i++){
			$returnInfo = $callbackModeel->callback(array('uid' => $params['platformUserNo'], 'requestNo' => $request_no, 'code' => $rs['code']), $rs, $request_no);
			if($returnInfo['code'] == 1) break;
			if($i == $n){				
				//第n次回调不成功，执行易宝订单解冻操作
				$params = array();
				$params['requestNo'] = $request_no;//订单流水号
				$params['platformUserNo'] = $params['platformUserNo'];//uid
				$this->qu_xiao_dong_jie($params, FALSE);
			}
		}
        return $returnInfo;
	}
    /**
     * 取消冻结 (投标)
     * 直连
     */
    public function qu_xiao_dong_jie($params,$isCallback=TRUE){
        $xml_array = array(
            'platformNo' => $this->platformNo,
            'requestNo' => $params['requestNo'],//之前投标的请求流水号
            'platformUserNo'=>$params['platformUserNo']
        );
        $request_no = $this->_request_no(array(__FUNCTION__,$xml_array));
        $params_xml = $this->_replace_xml($xml_array);
        //获取易宝签名
        $sign = $this->signcontent($params_xml);
        $post = array('req'=>$params_xml,'sign'=>$sign,'service' => 'REVOCATION_TRANSFER');
        $rs = $this->_post($post,$this->setopt);
        if($isCallback==FALSE)
        {
            return $rs;
        }
        //数据提交后进行回调
        $callbackModeel = new model_pay_callback();
        $callbackModeel->init($this->feemode,'REVOCATION_TRANSFER',$this->callback);
        $returnInfo  = $callbackModeel->callback(array('uid'=>$params['platformUserNo'],'requestNo'=>$request_no,'code'=>$rs['code']), $rs ,$request_no );
        return $returnInfo;
    }
	
	/**
     * 临时取消冻结 (投标)，不做任何回调操作
     * 直连
     */
    public function qu_xiao_dong_jie2($params){
        $xml_array = array(
            'platformNo' => $this->platformNo,
            'requestNo' => $params['requestNo'],//之前投标的请求流水号
            'platformUserNo'=>$params['platformUserNo']
        );
        $params_xml = $this->_replace_xml($xml_array);
        //获取易宝签名
        $sign = $this->signcontent($params_xml);
        $post = array('req'=>$params_xml,'sign'=>$sign,'service' => 'REVOCATION_TRANSFER');
        $rs = $this->_post($post,$this->setopt);
        return $rs;
    }
	
   /**
     * 放款
     * 直连
     */
    public function fang_kuan($params){
        $xml_array = array(
            'platformNo'        => $this->platformNo,
            'orderNo'           => $params['orderNo'], //标示一笔要自动换款的标的号
            'fee'               => $params['fee'], //平台扣除的金额
            'transfers'         => array(
                                'transfer' => array(
                                        'requestNo'                 => $params['requestNo'],//之前投标的请求流水号
                                        'transferAmount'            => $params['transferAmount'],//转帐请求转帐金额
                                        'sourceUserType'            => $params['sourceUserType'],//出款人会员类型
                                        'sourcePlatformUserNo'      => $params['sourcePlatformUserNo'],//出款人会员编号
                                        'targetUserType'            => $params['targetUserType'],//借款人会员类型
                                        'targetPlatformUserNo'      => $params['targetPlatformUserNo'],//借款人会员编号
                                        )
                                ),
            'notifyUrl'         => $params['notifyUrl'],//服务器通知url
        );
        $recorddata = $xml_array;
        $recorddata['platformUserNo'] = $params['sourcePlatformUserNo'];
        $request_no = $this->_request_no(array(__FUNCTION__,$recorddata));//记录流水并返回流水号
        if($request_no){ 
            $params_xml = $this->_replace_xml($xml_array);
            //获取易宝签名
            $sign = $this->signcontent($params_xml);
            $post = array('req'=>$params_xml,'sign'=>$sign,'service' => 'LOAN');
            $rs = $this->_post($post,$this->setopt);
            //提交数据后进行回调
            $callbackModeel = new model_pay_callback();
            $callbackModeel->init($this->feemode,'LOAN',$this->callback);
            $returnInfo  = $callbackModeel->callback(array('uid'=>$params['sourcePlatformUserNo'],'requestNo'=>$params['requestNo'],'code'=>$rs['code']),$rs,$request_no);
            return $returnInfo;
        }
        else
            return null;
    }
    
    //准备金还款
    public function reserve_repayment($requestNo,$params){
    	$xml_array = array(
    			'platformNo' => $this->platformNo,
    			'orderNo' => $params['orderNo'], //标示一笔要自动换款的标的号
    			'paymentRequestNo' => $params['paymentRequestNo'],//转帐请求流水号
    			'targetUserNo' => $params['targetUserNo'],//投资人会员编号
    			'amount' => $params['amount'],//还款金额
    			'fee' => $params['fee'],//借款人会员类型
    			'notifyUrl' => $params['notifyUrl'],//服务器通知url
    	);
    	$request_no = $this->_request_no(array(__FUNCTION__,$xml_array));//记录流水并返回流水号
    	$xml_array['requestNo'] = $request_no; //充值流水号
    	
    	$params_xml = Helper_xml::array_to_xml($xml_array,'request');
        $sign = $this->signcontent($params_xml);
    	$post = array('req'=>$params_xml,'sign'=>$sign,'service' => 'RESERVE_REPAYMENT');
    	return $this->_post($post);
    }
    /**
	 * 单笔业务查询
	 * @param unknown_type $requestNo
	 * @param unknown_type $params
	 * PAYMENT_RECORD 投标记录
	 * REPAYMENT_RECORD 还款记录
	 * WITHDRAW_RECORD 提现记录
	 * RECHARGE_RECORD 充值记录
	 * CP_TRANSACTION 通用转账记录
	 * @return multitype:unknown
	 */
    public function query($requestNo,$params,$isRecord=false){
    	$xml_array = array(
    			'platformNo' => $this->platformNo,//商户编号
                        'requestNo' => $requestNo,//流水号
    			'mode' => $params['mode'],//查询模式
    	);
        if($isRecord==true)
        {
            $request_no = $this->_request_no(array(__FUNCTION__,$xml_array));//记录流水并返回流水号
        }
        $params_xml = $this->_replace_xml($xml_array);
        $sign = $this->signcontent($params_xml);
    	$post = array('req'=>$params_xml,'sign'=>$sign,'service' => 'QUERY');
    	$rs = $this->_post($post,$this->setopt);
    	return $rs;
    }
    
    /**
     * 债权转让
     * 网关
     */
    public function transfar_claims(){
    	
    }
   
    /**
     * 还款 
     * 网关
     */
    public function auto_repayment($params){
    	$xml_array = array(
    			'platformNo' => $this->platformNo,
    			'platformUserNo' => $params['platformUserNo'],
    			'orderNo' => $params['orderNo'],//支付方式
                        'repayments'=>array(
                            'repayment' => array(
                                'paymentRequestNo' => $params['paymentRequestNo'],
                                'targetUserNo'=>$params['targetUserNo'],
                                'amount'=>$params['amount'],
                                'fee'=>0
                            )
                        ),
    			'notifyUrl' => $params['notifyUrl']//服务器通知地址
    	);
    	$request_no = $this->_request_no(array(__FUNCTION__,$xml_array));//记录流水并返回流水号
        if($request_no){
            $xml_array['requestNo'] = $request_no; //充值流水号
            $recordData = $xml_array;
            if (!model_pay_record::recordInsert($recordData, 'repayment')) {
                    return array('req' => '', 'sign' => '', 'path' => '');
            }
            $params_xml = $this->_replace_xml($xml_array);
            $sign = $this->signcontent($params_xml);
            $post = array('req'=>$params_xml,'sign'=>$sign,'service' => 'AUTO_REPAYMENT');
            $rs = $this->_post($post,$this->setopt);
            $callbackModeel = new model_pay_callback();
            $callbackModeel->init($this->feemode,'AUTO_REPAYMENT',$this->callback);
            $returnInfo  = $callbackModeel->callback(array('uid'=>$params['sourcePlatformUserNo'],'requestNo'=>$request_no,'code'=>$rs['code']),$rs,$request_no);
            return $returnInfo;
        }
        return null;
    }
    
    /**
     * 绑卡
     * 网关
     * @param unknown_type $params
     */
    public function bind_bankcard($params){
    	$xml_array = array(
    			'platformNo' => $this->platformNo,//商户编号
                        'platformUserNo' => $params['platformUserNo'],//会员编号
    			'platformUserNo' => $params['platformUserNo'],//会员编号
                        'callbackUrl' => $params['callbackUrl'],//回调通知地址
    	);
    	$request_no = $this->_request_no(array(__FUNCTION__,$xml_array));//记录流水并返回流水号
        if($request_no)
        {
            $xml_array['requestNo'] = $request_no; //流水号
            $params_xml = $this->_replace_xml($xml_array);
            $sign = $this->signcontent($params_xml);
            return array('req'=>$params_xml,'sign'=>$sign,'path'=>$this->wg_path.'toBindBankCard');
        }
        return array('req'=>'','path'=>'','sign'=>'');
    }
    public function transfer($params)
    {
        $xml_array = array(
                'platformNo' => $this->platformNo,//商户编号
                'platformUserNo' => $params['platformUserNo'],//会员编号
                'userType'  => 'MEMBER',//平台用户
                'bizType'   =>  'TRANSFER',//通用转账代码
                'expired'   =>  '{expired}',//过期时间
                'details'    =>  array(
                    'detail'    => array(
                            'amount'            =>   $params['amount'],//金额   
                            'targetUserType'    =>   $params['targetUserType'],//用户类型  MEMBER 平台用户  MERCHANT 商户
                            'targetPlatformUserNo'=> $params['targetPlatformUserNo'],//MEMBER时为平台用户uid    MERCHANT时为商户编号
                            'bizType'           => 'TRANSFER'
                    )
                ),
                'notifyUrl' =>  $params['notifyUrl'],
                'callbackUrl'=> $params['callbackUrl']
    	);
        $insertRecord  = $xml_array;
        $insertRecord['dealwith_data']['apply_id'] = $params['apply_id'];
    	$request_no = $this->_request_no(array(__FUNCTION__,$insertRecord));//记录流水并返回流水号
        if(empty($request_no))
            return null;
        if(D('stockApplyPay')->where(array('id'=>$params['apply_id']))->save(array('request_no'=>$request_no)))
        {
            $xml_array['requestNo'] = $request_no; //流水号
            $params_xml = $this->_replace_xml($xml_array);
            $params_xml = str_replace('{expired}', $params['expired'], $params_xml); //替换支付时间
            $sign = $this->signcontent($params_xml);
            return array('req'=>$params_xml,'sign'=>$sign,'path'=>$this->wg_path.'toCpTransaction');
        }
        return null;
    }
    /**
     *  通用转账接口
     *  Jimmmy Fu 2016-4-6
     * 
     */ 
    public function toTransfer($params)
    {
        $xml_array = array(
                'platformNo' => $this->platformNo,//商户编号
                'platformUserNo' => $params['platformUserNo'],//会员编号
                'userType'  => 'MEMBER',//平台用户
                'bizType'   =>  'TRANSFER',//通用转账代码
                'expired'   =>  '{expired}',//过期时间
                'details'    =>  array(
                    'detail'    => array(
                            'amount'            =>   $params['amount'],//金额   
                            'targetUserType'    =>   $params['targetUserType'],//用户类型  MEMBER 平台用户  MERCHANT 商户
                            'targetPlatformUserNo'=> $params['targetPlatformUserNo'],//MEMBER时为平台用户uid    MERCHANT时为商户编号
                            'bizType'           => 'TRANSFER'
                    )
                ),
                'notifyUrl' =>  $params['notifyUrl'],
                'callbackUrl'=> $params['callbackUrl']
    	);
        $insertRecord  = $xml_array;
        $insertRecord['dealwith_data']['apply_id'] = $params['apply_id'];
    	$request_no = $this->_request_no(array(__FUNCTION__,$insertRecord));//记录流水并返回流水号
        if(empty($request_no))
            return null;
       
        $xml_array['requestNo'] = $request_no; //流水号
        $params_xml = $this->_replace_xml($xml_array);
        $params_xml = str_replace('{expired}', $params['expired'], $params_xml); //替换支付时间
        $sign = $this->signcontent($params_xml);
        return array('req'=>$params_xml,'sign'=>$sign,'path'=>$this->wg_path.'toCpTransaction','requestNo'=>$request_no,'param'=>$params);
        
        return null;
    }
    
    
    /**
     * 取消绑卡
     * 网关
     * @param unknown_type $params
     */
    public function unbind_bankcard($params){
    	$xml_array = array(
                'platformNo' => $this->platformNo,//商户编号
                'platformUserNo' => $params['platformUserNo'],//会员编号
                'callbackUrl' => $params['callbackUrl'],//回调通知地址
    	);
    	$request_no = $this->_request_no(array(__FUNCTION__,$xml_array));//记录流水并返回流水号
        $xml_array['requestNo'] = $request_no; //流水号
    	$params_xml = $this->_replace_xml($xml_array);
        $sign = $this->signcontent($params_xml);
    	return array('req'=>$params_xml,'sign'=>$sign,'path'=>$this->wg_path.'toUnbindBankCard','requestNo'=>$request_no);
    }
    /**
     * 余额查询 直连接口
     * @param type $params 
     * @param type $isRecord 是否开启流水号开关 当为true的时候为开启 将会记录用户查询的流水记录
     * @return type array 
     */
    public function yu_e_cha_xun($params,$isRecord=false){
        $xml_array = array(
            'platformNo' => $this->platformNo,//商户编号
            'platformUserNo' => $params['platformUserNo'],//会员编号
        );
        if($isRecord)
            $request_no = $this->_request_no(array(__FUNCTION__,$xml_array));//记录流水并返回流水号
        $params_xml = $this->_replace_xml($xml_array);
        $sign = $this->signcontent($params_xml);
        $post = array('req'=>$params_xml,'sign'=>$sign,'service' => 'ACCOUNT_INFO');
        $rs = $this->_post($post,$this->setopt);
        if($rs['code'] == 1){
            return $rs;
        }
        return $rs;
    }
    /**
     * 平台划账
     * @param type $uid
     * @param type $amounts
     * @return null
     */
    public function platform_transfer($uid,$amounts)
    {
            $newData = $xml_array = array(
            'platformNo' => $this->platformNo,
            'sourceUserType'=>'MERCHANT',
            'sourcePlatformUserNo'=> $this->platformNo,
            'amount'=>round($amounts,2),
            'targetUserType'=>'MEMBER',
            'targetPlatformUserNo'=>$uid
        );
//        $newData['platformUserNo'] = 140;
        $request_no = $this->_request_no(array(__FUNCTION__,$newData));
        if($request_no){
            $xml_array['requestNo'] = $request_no;
            $newData['requestNo'] = $request_no;
            if (!model_pay_record::recordInsert($newData, 'plattransfer')) {
                return array('req' => '', 'sign' => '', 'path' => '');
            }
            $params_xml = $this->_replace_xml($xml_array);
            $sign = $this->signcontent($params_xml);
            $post = array('req'=>$params_xml,'sign'=>$sign,'service' => 'PLATFORM_TRANSFER');
            $rs = $this->_post($post,$this->setopt);
            $callbackModeel = new model_pay_callback();
            $callbackModeel->init($this->feemode,'PLATFORM_TRANSFER',$this->callback);
            $returnInfo  = $callbackModeel->callback(array('uid'=>$uid,'requestNo'=>$request_no,'code'=>$rs['code']),$rs,$request_no);
            Log::write('platform_transfer_'.json_encode($returnInfo).'\r\n','INFO',3,LOG_PATH.'/complete_transaction'.  date('Y-m-d',  time()).'.txt');
            if (is_array($returnInfo)) {
                $returnInfo['uid'] = $uid;
                $returnInfo['requestNo'] = $request_no;
            }
            return $returnInfo;
        }
        return null;
    }
    public function complete_transaction($params)
    {
        $insertData = $xml_array = array(
                'platformNo' => $this->platformNo,//商户编号
                'requestNo' => $params['requestNo'],//流水号
                'mode'  => $params['mode'],//模式
                'notifyUrl' =>  $params['notifyUrl']
    	);
        $insertData['platformUserNo'] = model_user::getLoginUser('uid');
    	$request_no = $this->_request_no(array(__FUNCTION__,$insertData));//记录流水并返回流水号
        if(empty($request_no))
            return null;
        $params_xml = $this->_replace_xml($xml_array);
        $params_xml = str_replace('{expired}', $params['expired'], $params_xml); //替换支付时间
        $sign = $this->signcontent($params_xml);
        $post = array('req'=>$params_xml,'sign'=>$sign,'service' => 'COMPLETE_TRANSACTION');
        $results = $this->_post($post,$this->setopt);
        Log::write('complete_transaction_'.json_encode($results).'\r\n','INFO',3,LOG_PATH.'/complete_transaction'.  date('Y-m-d',  time()).'.txt');
        if($results['code']==1)
            return array('message'=>'转账确认成功','status'=>1);
        else
            return array('message'=>'转账确认失败','status'=>-1);
    }
    /**
     * 自动还款授权
     * @param type $params
     * @return null
     */
    public function repayment_authorize($params)
    {
        $xml_array = array(
            'platformNo' => $this->platformNo,
            'platformUserNo'=>$params['platformUserNo'],
            'orderNo'=> $params['orderNo'],
            'callbackUrl'=>$params['callbackUrl'],
            'notifyUrl'=>$params['notifyUrl']
        );
        $request_no = $this->_request_no(array(__FUNCTION__,$xml_array));
        if($request_no){
            $xml_array['requestNo'] = $request_no;
            $params_xml = $this->_replace_xml($xml_array);
            $sign = $this->signcontent($params_xml);
            return array('req'=>$params_xml,'sign'=>$sign,'path'=>$this->wg_path.'toAuthorizeAutoRepayment');
        }
        return null;
    }
	
	/**
     * 修改手机号
     * @param type $params
     * @return null
     */
    public function reset_mobile($params)
    {
        $xml_array = array(
            'platformNo' => $this->platformNo,
            'platformUserNo'=>$params['platformUserNo'],
            'mobile'=> $params['mobile'],
            'callbackUrl'=>$params['callbackUrl'],
            'notifyUrl'=>$params['notifyUrl']
        );
        $request_no = $this->_request_no(array(__FUNCTION__,$xml_array));
        if($request_no){
            $xml_array['requestNo'] = $request_no;
            $params_xml = $this->_replace_xml($xml_array);
            $sign = $this->signcontent($params_xml);
            return array('req'=>$params_xml,'sign'=>$sign,'path'=>$this->wg_path.'toResetMobile');
        }
        return null;
    }
    /**
     * 对账
     * @param unknown_type $params
     */    
    public function reconciliation($params){
    	$xml_array = array(
            'platformNo' => $this->platformNo,//商户编号
            'date' => $params['date']//yyyy-MM-dd
    	);
    	$params_xml = Helper_xml::array_to_xml2($xml_array,'request');
    	$request_no = $this->_request_no(array(__FUNCTION__,$xml_array));//记录流水并返回流水号
        $sign = $this->signcontent($params_xml);
    	$post = array('req'=>$params_xml,'sign'=>$sign,'service' => 'RECONCILIATION');
    	$results = $this->_post($post,$this->setopt);

    	return $results;
    }
    
    /**
     * 对账
     * @param unknown_type $params
     */    
    public function reset_passwd($params){
    	$xml_array = array(
            'platformNo'        => $this->platformNo,//商户编号
            'platformUserNo'    => $params['platformUserNo'],//用户编号
            'callbackUrl'       => $params['callbackUrl']//回调地址
    	);
    	$request_no = $this->_request_no(array(__FUNCTION__,$xml_array));//记录流水并返回流水号
        if($request_no)$xml_array['requestNo'] = $request_no;
        else return null;
    	$params_xml = $this->_replace_xml($xml_array);
        $sign = $this->signcontent($params_xml);
    	return array('req'=>$params_xml,'sign'=>$sign,'path'=>$this->wg_path.'toResetPassword');
    }
   /**
     * 直连提交
     */
    private function _post($post,$arr=array()){
        if(count($arr)>1){
            $cu = new helper_curl($arr);
        }else{
            $cu = new helper_curl();
        }
        $post_result = $cu->post($this->zl_path,$post);
        $post_result = helper_xml::xml_to_array($post_result);
        return $post_result;
    }
    
    private function _replace_xml($xml)
    {
        if(!is_array($xml) || empty($xml))
        {
            return null;
        }
        else
        {
            $params_xml = null;
            $params_xml = helper_xml::array_to_xml($xml,'request');
            $params_xml = str_replace("<request>", "<request platformNo='".$this->platformNo."'>", $params_xml);
            $params_xml = preg_replace("/\s/","",$params_xml);
            $params_xml = str_replace("'", "\"", $params_xml);
            $params_xml = str_replace("requestplatformNo", "request platformNo", $params_xml);
            $params_xml = str_replace('?xmlversion="1.0"encoding="UTF-8"?', '?xml version="1.0" encoding="UTF-8"?', $params_xml);
            return $params_xml;
        }
    }


    /**
	 * 生成流水号
	 * @param type $params  参数
	 * @param type $source  访问来源  1后台 2web 3wap 4ios 5android
	 * @return type bool
	 */
	private function _request_no($params) {
		$log_yeepay = D('logYeepay');
		$source = C('sys_global_source') ? C('sys_global_source') : 2;
		$serializeStr = serialize($params); //序列化请求易宝的数据
		$request_type = getRequestMethod(); //获取请求方式
		$InsertData = array('actions' => $params[0], 'params' => $serializeStr, 'uid' => (int)$params[1]['platformUserNo'], 'source' => $source, 'request_type' => $request_type, 'add_time' => time());
                if (isset($params['dealwith_data']) && !empty($params['dealwith_data'])) $InsertData['dealwith_data'] = $params['dealwith_data'];
		if (isset($params['puid']) && !empty($params['puid'])) $InsertData['puid'] = (int)$params['puid'];
		if (isset($params['pid']) && !empty($params['pid'])) $InsertData['pid'] = (int)$params['pid'];
		return $log_yeepay->add($InsertData);
	}

    /**
	 * 对内容进行签名加密
	 */
	private function signcontent($content) {
		$reqarr['req'] = $content;
		$cu = new helper_curl($this->setopt2);
		if (RUN_MODE == 'deploy') {
			$signurl = 'http://server1:8088/sign';
		} else {
			$signurl = 'http://172.16.0.252:8088/sign';
		}
		$post_result = $cu->post($signurl, $reqarr);
		return $post_result;
	}

	public function signcontentCheck($xml, $result) {
		$reqarr['req'] = $xml;
		$reqarr['sign'] = $result;
		$cu = new Helper_curl($this->setopt2);
		if (RUN_MODE == 'deploy') {
			$signurl = 'http://server1:8088/verify';
		} else {
			$signurl = 'http://172.16.0.252:8088/verify';
		}
		$post_result = $cu->post($signurl, $reqarr);
		if ($post_result == 'SUCCESS') {
			return 'SUCCESS';
		}
		else return null;
	}
    
    /**
     *  本方法为直接去易宝,免去确认
     *   Jimmy Fu 2016-4-6
     * 
     */  
	public final function gotoYeepay($req , $path , $sign){
		
		Log::write($req, 'INFO', 3, LOG_PATH . '/yeepay_xml' . date("Y-m-d") . '.log');
		echo "<body onload='yeepay_form.submit()'><form name='yeepay_form' action='$path' method='post'><input type='hidden' name='req' value='" .$req. "'/><input type='hidden' name='sign' value='" . $sign . "'/>" . $server . "</form></body>";
        exit;
	}
    
}
?>
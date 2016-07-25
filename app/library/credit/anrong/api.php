<?php

/**
 *  安融征信API接口
 * @author Jimmy Fu 2015-12-3 
 * @copyright 2015
 */

class  credit_anrong_api{
	
    static public $domain = 'https://www.allwincredit.cn';  //接口地址
    
    static public $key = 'bdc2vPytVodjk';  
    
    static public $member = '1247'; //安融征信的账号
    
    static public $debug = 0; //调试模式是否开启
   
   
   /**
    *  安融征信初始化方法
    *  Jimmy Fu 2015-12-14
    */ 
   public static function init(){
    
        $anrong_config =  C('anrong_config'); //读取安融征信的配置信息
        self::$domain = $anrong_config['domain']?$anrong_config['domain']:self::$domain; //如果config不存在，那么取本地配置
        self::$member = $anrong_config['member']?$anrong_config['member']:self::$member; //如果config不存在，那么取本地配置
        self::$key    = $anrong_config['key']?$anrong_config['key']:self::$key; //如果config不存在，那么取本地配置
   } 
    
    /**
     *  个人关联工商查询 接口
     *  Jimmy Fu 2015-12-3
     *  @param  string   $idcard    身份证号码
     *  @return array    $contentArr
     */ 
    public static function  personRelCompany($idcard){
        
        //配置信息初始化 
        self::init();
        
        //工商查询接口
        $url = self::$domain.'/reportserver/rest/personGs/gongshang';
        $key = self::$key;
        
        $data = array();
        $data['cid'] = $idcard;         //身份证号码
        $data['member'] = self::$member; //会员机构号
        $sign = md5(implode('',$data).$key);  
        $data['sign'] = $sign; 
       
        $datas =  http_build_query ( $data, '&' );
        $contents =  self::curlGet($url.'?'.$datas);
        if(empty($contents)){
            return array(); //查询结果为空
        }
        $contentArr = ($contents)? json_decode($contents,true):array();//强制把json格式转化为数组
        $contentArr['queryDesc'] = self::personRelCompanyCodeMessage($contentArr['message']);
        return $contentArr;
        
    }
    
      /**
     *  个人关联工商查询 提示信息描述
     *  Jimmy Fu 2015-12-7
     *  @param  string   $code    提示代码
     *  @return mix     提示信息
     */ 
    public static function personRelCompanyCodeMessage($code){
        $codeMessage = array('0000' => '提示成功',
                            '0001' => '必填参数不能为空！',
                            '0001_wrong' => '必填参数格式错误',
                            '0002' => '授权失败',
                            '0003_sfrz' => '身份认证查询接口欠费',
                            '0003_grgs' => '个人工商查询接口欠费',
                            '0003_ylkyz' => '银联卡验证接口欠费',
                            '0003_grzdduoka' => '个人账单多卡查询接口欠费',
                            '0003_grzddanka' => '个人账单单卡查询接口欠费',
                            '0003_shzdduoka' => '商户账单多卡查询接口欠费',
                            '0003_shzddanka' => '商户账单单卡查询接口欠费',
                            '0003_grbg' => '个人报告查询接口欠费',
                            '0003_more_grbg' => '个人报告多卡查询接口欠费',
                            '0003_shbg' => '商户报告查询接口欠费',
                            '0003_more_shbg' => '商户报告多mid(商户编号)查询接口欠费',
                            '0004' => '系统错误',
                            '0005_card' => '个人报告和个人账单多卡查询接口传入的card的银行卡号数量不能大于五个',
                            '0005_onlyonecard' => '个人报告和个人账单单卡查询接口传入的card的银行卡号数量只能为一个',
                            '0005_mid' => '商户报告和商户账单多mid查询接口传入的mid的商户编号数量不能大于五个',
                            '0005_onlyonemid' => '商户报告和商户账单单mid查询接口传入的mid的商户编号数量只能为一个',
                            '0005_less2' => '银联卡账单多卡查询接口传入的银联卡数不能小于2张',
                            '0005_cardfail' => '银联卡验证失败，不出账单',
                            '0006' => '未查到相关数据，此笔查询不扣费',
                            '0008' => '身份证号格式错误',
        );
       if(empty($code)){
            return $codeMessage;
       } else{
            if(array_key_exists($code,$codeMessage)){
                $retMsg = $codeMessage[$code];
            }else{
                $retMsg = '未定义错误，错误code为：'.$code;
            }

       }
       return $retMsg;
         
    }
    
    
    /**
     *  个人司法信息 接口  
     *   Jimmy Fu  2015-12-3
     *  @param  string   $name      姓名
     *  @param  string   $idcard    身份证号码
     */ 
    public static function personJudicial($name,$idcard){
        //配置信息初始化 
        self::init();
        
        $url = self::$domain.'/reportserver/rest/lawInfo/search/p2p'; 
        
        $data = array();
        $data['n'] = $name;                 //姓名
        $data['id'] = $idcard;               //身份证号码
        $data['member'] = self::$member;       //会员机构号
        $data['sign'] = self::$key;        //不用加密
        
        $datas =  http_build_query ( $data, '&' );
        $requestUrl = $url.'?'.$datas;
        $contents =  self::curlGet($requestUrl);
        
        if(empty($contents)){
            return array(); //查询结果为空
        }
        $contentArr = ($contents)? json_decode($contents,true):array();//强制把json格式转化为数组
        $contentArr['queryDesc'] = self::personJudicialCodeMessage($contentArr['error']);
        return $contentArr;
    }
    
      /**
     *  个人司法查询 提示信息描述
     *  Jimmy Fu 2015-12-7
     *  @param  string   $code    提示代码
     *  @return mix     提示信息
     */ 
    public static function personJudicialCodeMessage($code){
        $codeMessage = array('0' => '无错误',
                            '1' => '姓名n不能为空',
                            '2' => '身份证号id不能为空',
                            '3' => '身份证号格式不正确',
                            '4' => '机构号member不能为空',
                            '5' => '机构验证码sign不能为空',
                            '6' => '机构号member错误',
                            '7' => '机构验证码sign错误',
                            '8' => '该机构还未绑定IP，暂无法访问',
                            '9' => '该IP没有访问权限',
                            '10' => '该会员合同中没有司法信息P2P查询权限',
                            '11' => '费用不足，请充费，谢谢！',
                          
        );
       if(empty($code) && $code != 0){
            return $codeMessage;
       } else{
            if(array_key_exists($code,$codeMessage)){
                $retMsg = $codeMessage[$code];
            }else{
                $retMsg = '未定义错误，错误code为：'.$code;
            }

       }
       return $retMsg;
         
    }
    
    
      /**
     *   企业司法信息查询 接口  
     *   Jimmy Fu  2015-12-3
     *  @param  string   $companyName    企业名称
     *  @param  string   $companyID    组织机构代码
     */ 
    public static  function companyJudicial($companyName,$companyID){
        //配置信息初始化 
        self::init();
        
        //工商查询接口
        $url = self::$domain.'/reportserver/rest/lawInfo/company/search';
      
        $data = array();
        $data['n'] = $companyName;               //企业名称
        $data['id'] = $companyID;               //组织机构代码
        $data['member'] = self::$member;        //会员机构号
        $data['sign'] = self::$key; 
      
        $datas =  http_build_query ( $data, '&' );
        $requestUrl = $url.'?'.$datas;
        $contents =  self::curlGet($requestUrl);
       
        if(empty($contents)){
            return array(); //查询结果为空,接口连接错误
        }
        $contentArr = ($contents)? json_decode($contents,true):array();//强制把json格式转化为数组
        $contentArr['queryDesc'] = self::companyJudicialCodeMessage($contentArr['error']);
        return $contentArr;
    }
    
      /**
     *  企业司法查询 提示信息描述
     *  Jimmy Fu 2015-12-7
     *  @param  string   $code    提示代码
     *  @return mix     提示信息
     */ 
    public static function companyJudicialCodeMessage($code){
        $codeMessage = array('' => '无错误',
                            '1' => '企业名称不能为空',
                            '2' => '保留',
                            '3' => '保留',
                            '4' => '机构号member不能为空',
                            '5' => '机构验证码sign不能为空',
                            '6' => '机构号member错误',
                            '7' => '机构验证码sign错误',
                            '8' => '该机构还未绑定IP，暂无法访问',
                            '9' => '该IP没有访问权限',
                            '10' => '该会员合同中没有司法信息P2P查询权限',
                            '11' => '费用不足，请充费，谢谢！',
                          
        );
       if(empty($code) && $code != 0){
            return $codeMessage;
       } else{
            if(array_key_exists($code,$codeMessage)){
                $retMsg = $codeMessage[$code];
            }else{
                $retMsg = '未定义错误，错误code为：'.$code;
            }

       }
       return $retMsg;
         
    }
    
    /**
     *  反欺诈 接口  
     *   Jimmy Fu  2015-12-3
     *  @param  string   $name      姓名
     *  @param  string   $idcard    身份证号码
     *  @param  array    $param     扩展参数
     */ 
    public static function validFqz($name,$idcard,$param=array()){
        //配置信息初始化 
        self::init();
        
        $url = self::$domain.'/p2pfqz/rest/p2p/validFqz'; 
         
        $data = array();
        $data['customerName'] = $name;       //姓名
        $data['paperNumber'] = $idcard;     //身份证号码
        $data['member'] = self::$member;    //会员机构号
        $data['sign'] = self::$key; 
        
        //非必填参数
        if($param['phones']){
             $data['phones'] = $param['phones']; 
        }
        if($param['emails']){
             $data['emails'] = $param['emails']; 
        }
        if($param['homeAddress']){
             $data['homeAddress'] = $param['homeAddress']; 
        }
        if($param['homeTel']){
             $data['homeTel'] = $param['homeTel']; 
        }
        if($param['qq']){
             $data['qq'] = $param['qq']; 
        }
        if($param['qq']){
             $data['workUnit'] = $param['workUnit']; 
        }
        
        $datas =  http_build_query ( $data, '&' );
        $requestUrl = $url.'?'.$datas;
        $contents =  self::curlGet($requestUrl);
        if(empty($contents)){
            return array(); //查询结果为空,接口连接错误
        }
        $contentArr = ($contents)? json_decode($contents,true):array();//强制把json格式转化为数组
        $contentArr['queryDesc'] = self::validFqzCodeMessage($contentArr['errorCode']);
        return $contentArr;
    }
    
    /**
     *  企业司法查询 提示信息描述
     *  Jimmy Fu 2015-12-7
     *  @param  string   $code    提示代码
     *  @return mix     提示信息
     */ 
    public static function validFqzCodeMessage($code){
        $codeMessage = array('' => '查询成功，无错误！',
                            '0006' => '余额不足，请充值！',
                            '0007' => '姓名与身份证必须填写！',
                            '0008' => '系统错误，请稍后再试！',
                            '0009' => '非法身份！',
  
        );
       if(empty($code) && $code != 0){
            return $codeMessage;
       } else{
            if(empty($code)){
                $code = '';
            }
            if(array_key_exists($code,$codeMessage)){
                $retMsg = $codeMessage[$code];
            }else{
                $retMsg = '未定义错误，错误code为：'.$code;
            }

       }
       return $retMsg;
         
    }
  
    
     /**
     *  风险预警 接口  
     *   Jimmy Fu  2015-12-3
     *  @param  string   $name      姓名
     *  @param  string   $idcard    身份证号码
     *  @param  array    $param     扩展参数
     */ 
    public static function validFxyj($name,$idcard,$param=array()){
        //配置信息初始化 
        self::init();
        
        $callbackUrl = '';//改接口预警回调信息将以URL回调信息的方式反馈，目前是邮件告诉安融的人
        $url = self::$domain.'/p2pfxyj/remote/addFocus.shtml'; 
         
        $data = array();
        $data['customerName'] = $name;   //姓名
        $data['paperNumber'] = $idcard;               //身份证号码
        $data['member'] = self::$member;                //会员机构号
        $data['sign'] = self::$key; 
        $data['lat'] = 'all';              //监控维度
        
        //扩展参数
        if(is_array($param) && !empty($param)){
        
             //非必填参数
            if($param['mobile']){
                 $data['mobile'] = $param['mobile']; 
            }
            if($param['email']){
                 $data['email'] = $param['email']; 
            }
            if($param['homeAddressCode']){
                 $data['homeAddressCode'] = $param['homeAddressCode']; 
            }
            if($param['homeAddressDetail']){
                 $data['homeAddressDetail'] = $param['homeAddressDetail']; //家庭地址行政区划编码
            }
            if($param['homePhone']){
                 $data['homePhone'] = $param['homePhone']; 
            }
            if($param['qq']){
                 $data['qq'] = $param['qq']; 
            }
            if($param['workUnit']){
                 $data['workUnit'] = $param['workUnit'];  //工作单位
            }
            if($param['position']){
                 $data['position'] = $param['position']; 
            }
            if($param['workAddressCode']){    
                 $data['workAddressCode'] = $param['workAddressCode'];  //工作地址行政区划编码
            }
            if($param['workAddreesDetail']){   
                 $data['workAddreesDetail'] = $param['workAddreesDetail']; //工作地址详细地址
            }
            if($param['workPhone']){   
                  $data['workPhone'] = $param['workPhone'];      //工作电话
            }
        
        }
       
        $datas =  http_build_query ( $data, '&' );
        $requestUrl = $url.'?'.$datas;
        $contents =  self::curlGet($requestUrl);
        $contentArr = ($contents)? json_decode($contents,true):array();//强制把json格式转化为数组
        return $contentArr;
    }
    
    
    /**
     *  封装Curl Get方法
     */ 
    public static function curlGet($url){
        $cookie = tempnam ("/tmp", "CURLCOOKIE");
        $ch = curl_init();
        
        curl_setopt( $ch, CURLOPT_USERAGENT,      "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:24.0) Gecko/20100101 Firefox/24.0" );
        curl_setopt( $ch, CURLOPT_URL,            $url );
        curl_setopt( $ch, CURLOPT_COOKIEJAR,      $cookie );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_ENCODING,       "" );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_AUTOREFERER,    true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 10 );
        curl_setopt( $ch, CURLOPT_TIMEOUT,        10 );
        curl_setopt( $ch, CURLOPT_MAXREDIRS,      10);
        
        $content  = curl_exec ( $ch );
        $response = curl_getinfo ( $ch );
        if(self::$debug){
             dump($response);
             dump($content);
        } 
        curl_close ( $ch );
        return $content;
     }
 
    
    
}



?>
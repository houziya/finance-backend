<?php

/**
 *  91征信API接口
 * @author Jimmy Fu 2016-1-18 
 * @copyright 2016
 */

class  credit_91zhengxin_api{
	
    public static  $url = 'http://114.113.101.219:8801/xcif/zxservice.do';  //接口地址
    
    public static  $sign = 'CB5CF256BDE3728E3DBEFE468E201E48';   //签名
    
    public static  $custNo = 'P21T2CF1103364241';  //请求或报送方，信息源 17位
    
    public static  $encode = '01';  //编码，01.UTF8 02.GBK
    
    public static  $encryptType = '01';  //加密类型 01.不加密 02.RSA
    
    public static  $msgType = '01';  //消息类型 01.JSON 02.XML 03.Protobuf
    
    public static  $keyArr = array('version','custNo','encode','trxCode','encryptType','msgType','msgBody','retCode','retMsg','sign');
    
    public static  $debug = 0; //调试模式是否开启
   
   
   public function __construct(){
        //初始化
        $this->init();
   }
   
   
   /**
    *  91征信初始化方法
    *  Jimmy Fu 2016-1-18
    */ 
   public function init(){
    
        $init_config =  C('91zhengxin_config'); //读取安融征信的配置信息
        self::$url = $init_config['url']?$init_config['url']:self::$url; //如果config不存在，那么取本地配置
        self::$custNo = $init_config['custNo']?$init_config['custNo']:self::$custNo; //如果config不存在，那么取本地配置
        self::$sign    = $init_config['sign']?$init_config['sign']:self::$sign; //如果config不存在，那么取本地配置
   } 
   
   
   
   /**
    *  报文模板
    */ 
   public  function getMessageTpl($trxCode,$msgBody,$retCode=''){
    
        
        $keyArr = self::$keyArr;
        //请求报文
        $param = array(
            $keyArr[0]      => '01',  //版本，默认01
            $keyArr[1]      => self::$custNo,  //请求或报送方，信息源 17位
            $keyArr[2]      => self::$encode,  //编码，01.UTF8 02.GBK
            $keyArr[3]      => $trxCode,  //报文编号， 默认四位 例:0001
            $keyArr[4]      => self::$encryptType,  //加密类型 01.不加密 02.RSA
            $keyArr[5]      => self::$msgType,   //消息类型,01.JSON 02.XML 03.Protobuf
            $keyArr[6]      => $msgBody,  //报文主体为Base64编码的字节数组
            $keyArr[7]      => $retCode,  //返回代码
            $keyArr[8]      => '',  //返回消息
            $keyArr[9]      => self::$sign,
 
        );
        
        return $param;
    
   }
   
   
   
   /**
    *  91征信返回的内容转化为数组形式
    *  
    */ 
   public function getReturnMessageArr($mesage){
    
        if(empty($mesage)){
            return false;
        }
        $mesage =  trim($mesage,'"');
        $mes_arr = explode('|',$mesage);
        if(empty($mes_arr)){
            return false;
        }
        $paramRes = array();
        $keyArr = self::$keyArr;
        foreach($mes_arr as $key => $val){
            $paramRes[$keyArr[$key]] = $val;
            if(in_array($keyArr[$key],array('msgBody','retMsg'))){
                //解码
                $paramRes[$keyArr[$key]] = base64_decode($val);
                if($keyArr[$key] == 'msgBody'){
                    $paramRes[$keyArr[$key]] = json_decode($paramRes[$keyArr[$key]],true);
                }
            }
        }
        
        return $paramRes;
   }
   
   
   /**
    *  91征信1001查询
    *  @param string $name   姓名
    *  @param string $idcard 身份证号码
    */ 
   public  function request1001($name,$idcard){
        $array = array(
        	"realName"=>$name, 
        	"idCard"=>$idcard
        );
    
        $msgBody = base64_encode(json_encode($array));
        
        $param = $this->getMessageTpl(1001,$msgBody);
        $data = implode('|',$param);
        $post_ret = $this->post(self::$url,$data);
        
        //如果开启调试，则写入日志
        if(self::$debug){
            $logMeg = '91征信发送报文,request1001='.$data.',$msgBody='.json_encode($array).',http_post_ret='.json_encode($post_ret);
            log::write($logMeg,'INFO',3,LOG_PATH.'/91zxCallback_debug_log');  //记录日志，方便排错 
        }
        //pr($post_ret);
        
        $content_arr = $this->getReturnMessageArr($post_ret);
        
        return $content_arr;
    
   }
   
   /**
    *  91征信1002查询
    *  @param string $trxNo  交易代码 32位GUID查询的唯一标示用于匹配查询
    */ 
   public  function request1002($trxNo){
    
       $array = array(
        	"trxNo"=>$trxNo, 
        );
    
        $msgBody = base64_encode(json_encode($array));
        
        $param = $this->getMessageTpl(1002,$msgBody);
        $data = implode('|',$param);
        $post_ret = $this->post(self::$url,$data);
        
        
        $content_arr = $this->getReturnMessageArr($post_ret);
        
          //pr($content_arr);
        return $content_arr;
    
   } 
   
    /**
    *  91征信发送4002报文
    *  Jimmy Fu 2016-1-20
    */ 
   public  function  request4002(){
    
        $param = $this->getMessageTpl(4002,'','0000');
        $data = implode('|',$param);
        $post_ret = $this->post(self::$url,$data);
        
        return $post_ret;
    
   }
   
   
   
    
    
    
    
    public  function post($url, $data) {
    	$header[] = "Content-Type: application/octet-stream";
    	$ch = curl_init ($url);
    	curl_setopt($ch, CURLOPT_URL, $url);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    	curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
    	curl_setopt($ch, CURLOPT_POST, 1);
    	curl_setopt($ch,CURLOPT_POSTFIELDS, $data);
    	$response = curl_exec($ch);
    	
        if(self::$debug){
            $curl_info = curl_getinfo ( $ch );
            var_dump($curl_info); 
        }
    
    	curl_close($ch);
    	return $response;
    }
 
    
    
}



?>
<?php

/**
 *  华道征信接口API
 * @author Jimmy Fu 2016-2-17
 * @copyright 2016
 */

class  credit_huadao_api{
	
    public static  $url = 'https://api.sinowaycredit.com:8093';  //接口地址,默认地址
    
    public static  $account = 'renrentou';                    //合作方账号
    
    public static  $privateKey = 'cfe0ca8c7c8d1f72458e0a0782809c83';    //合作方密钥
    
    public static  $debug = 0; //调试模式是否开启
   
   
   public function __construct(){
        //初始化
        $this->init();
   }
   
   
   /**
    *  华道征信接口初始化方法,加载自定义配置
    *  Jimmy Fu 2016-2-17
    */ 
   public function init(){
    
        $init_config =  C('huadao_config'); //读取同盾征信的配置信息
        self::$url = $init_config['url']?$init_config['url']:self::$url; //如果config不存在，那么取本地配置
        self::$account = $init_config['account']?$init_config['account']:self::$account; //如果config不存在，那么取本地默认配置
        self::$privateKey  = $init_config['privateKey']?$init_config['privateKey']:self::$privateKey; //如果config不存在，那么取本地配置
        
   } 
   
   
 
   
    /**
     *  华道征信航旅查询接口
     *   Jimmy Fu  2016-2-19
     *  @param  string   $name      姓名
     *  @param  string   $idcard    身份证号码
     *  @return  array        passengResult 不为空则有查询内容,如果status -1 调用失败，返回false
     */ 
    public function hanglvReport($name,$idcard){
      
        //获取POST提交URL
        $data['name'] = $name;
        $data['pid'] = $idcard;
        $data['account'] = self::$account;
        $data['sign'] = md5($name.$idcard.self::$privateKey);
        $requestUrl = self::$url.'/SinowayApi/jzdservice/report?'.http_build_query ($data, '&' ); 
         
        $contents =  self::curlGet($requestUrl);
        $contentObj = simplexml_load_string($contents);
        if($contentObj->responseHeader->status == -1){
            //调用失败，sign验签失败
            return false;
        }
        $info = $contentObj->result->info;
       	$data = json_decode(json_encode($info), true); //转化为数组格式
        return $data;
    }
   
  
     /**
     *  华道征信黑名单查询接口
     *   Jimmy Fu  2016-2-18
     *  @param  string   $name      姓名
     *  @param  string   $idcard    身份证号码
     */ 
    public function blankReport($idcard){
      
        $data['idcode'] = $idcard;
        $data['account'] = self::$account;
        $data['sign'] = md5($idcard.self::$privateKey);
        $requestUrl = self::$url.'/SinowayApi/huifaService/MoHuSearch?'.http_build_query ($data, '&' ); 
       
        $contents =  self::curlGet($requestUrl);
        $contentObj = simplexml_load_string($contents);
        $info = $contentObj->result->info;
       	$data = json_decode(json_encode($info), true); //转化为数组格式
        return $data;
    }
   
  
    
    /**
     *  POST提交
     */ 
    public static function  curlPost($url,$data){
        
        $cookie = tempnam ("/tmp", "CURLCOOKIE"); 
        $datas =  http_build_query ( $data, '&' );
        $ch = curl_init(); //var_dump($ch);
        curl_setopt($ch, CURLOPT_URL,        $url );
        curl_setopt($ch, CURLOPT_COOKIEJAR,  $cookie);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
        curl_setopt($ch, CURLOPT_USERAGENT,  'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:24.0) Gecko/20100101 Firefox/24.0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        //curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  //跳过SSL证书检查
        
        $result = curl_exec($ch);
        if(self::$debug){
            $response = curl_getinfo ( $ch );
            dump($response); 
            var_dump(curl_error($ch)); 
        }
        
        curl_close($ch);
        
        return $result;
     }
     
     /**
     *  GET提交,直接查询
     *   Jimmy Fu 2016-1-25
     */ 
    function curlGet($url){
        
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
        if(self::$debug){
              $response = curl_getinfo ( $ch );
               dump($response); 
               var_dump(curl_error($ch)); 
        }
        curl_close ( $ch );
        return $content;
     }
    
    
}



?>
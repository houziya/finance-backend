<?php

/**
 *  企查查业务封装
 * @author Jimmy Fu 2015-12-22
 * @copyright 2015
 */

class  credit_qichacha_qichacha{
	
    static public $domain = 'http://open.qichacha.com';  //接口地址
    
    static public $token = '68b938756c1fe50eeab4a26bee57d079';  
    
    static public $debug = 0; //调试模式是否开启
   
   
   
   public function __construct(){
        $this->init();
     
   }
   
   
   /**
    *  安融征信初始化方法
    *  Jimmy Fu 2015-12-14
    */ 
   public  function init(){
    
        $qichacha_config =  C('qichacha_config'); //读取安融征信的配置信息
        self::$domain = $qichacha_config['domain']?$qichacha_config['domain']:self::$domain; //如果config不存在，那么取本地配置
        self::$token = $qichacha_config['token']?$qichacha_config['token']:self::$token; //如果config不存在，那么取本地配置
       
   } 
   
   
   /**
    *  判读公司是否存在  Jimmy Fu 2015-12-22
    *  @param   string  $company 公司全称
    *  @return  bool    true 存在  false不存在   
    * 
    */ 
   public  function companyExist($company){
        
        $request_url = self::$domain.'/open/IsExist';
        $data['key'] = $company;
        $data['token'] = self::$token;
        $datas =  http_build_query ( $data, '&' );
        $contents =  self::curlGet($request_url.'?'.$datas);
        if(empty($contents)){
            return array(); //查询结果为空
        }
        $contentArr = ($contents)? json_decode($contents,true):array();//强制把json格式转化为数组
        return empty($contentArr['success'])?false:true;
   }
    
    
     
   /**
    *  查看企业的基本信息  Jimmy Fu 2015-12-22
    *  @param   string  $company 公司全称
    *  @return  string   
    */ 
   public  function companyInfo($company){
       
        $request_url = self::$domain.'/html/#/rrtou';
        $data['key'] = $company;
        $data['token'] = self::$token;
        $datas =  http_build_query ( $data, '&' );
        $contents =  self::curlGet($request_url.'?'.$datas);
        return $contents;
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
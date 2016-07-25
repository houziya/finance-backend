<?php

/**
 *  同盾API接口
 * @author Jimmy Fu 2016-1-25
 * @copyright 2016
 */

class  credit_tongdun_api{
	
    public static  $url = 'https://credittest.api.tongdun.cn';  //接口地址,默认地址
    
    public static  $partner_code = 'renrentou';   //合作方标识
    
    public static  $partner_key = 'cc1ab0d55e5d497abb5345ab7dc69d78';    //合作方密钥
    
    public static  $app_name = 'renrentou_web';        //应用名称
  
    public static  $debug = 0; //调试模式是否开启
   
   
   public function __construct(){
        //初始化
        $this->init();
   }
   
   
   /**
    *  同盾信贷云初始化方法
    *  Jimmy Fu 2016-1-25
    */ 
   public function init(){
    
        $init_config =  C('tongdun_config'); //读取同盾征信的配置信息
        self::$url = $init_config['url']?$init_config['url']:self::$url; //如果config不存在，那么取本地配置
        self::$partner_code = $init_config['partner_code']?$init_config['partner_code']:self::$partner_code; //如果config不存在，那么取本地默认配置
        self::$partner_key    = $init_config['partner_key']?$init_config['partner_key']:self::$partner_key; //如果config不存在，那么取本地配置
        
   } 
   
   
   
   /** 
    * 得到接口的请求URL 
    *  Jimmy Fu 2016-1-25
    *
    */  
   public function getRequestUrl($param = array()){
        $data['partner_code'] = self::$partner_code;
        $data['partner_key'] = self::$partner_key;
        
        if(is_array($param) && !empty($param)){
           
            $data = array_merge($data,$param);
        }
        return http_build_query ($data, '&' );;
   }
   
   
    /**
     *  准入submit接口 
     *   Jimmy Fu  2016-1-25
     *  @param  string   $name      姓名
     *  @param  string   $idcard    身份证号码
     *  @param  string   $mobile    手机号码
     *  @param  array    $param     扩展参数
     */ 
    public function preloanApply($name,$idcard,$mobile,$param=array()){
      
        //获取POST提交URL
        $requestUrl = self::$url.'/preloan/apply?'.$this->getRequestUrl(array('app_name'=>self::$app_name)); 
         
        $data = array();
        $data['name'] = $name;               //姓名，必填
        $data['id_number'] = $idcard;       //身份证号码 必填
        $data['mobile'] = $mobile;          //手机号码 必填
        $data['email'] = $param['email'];    //推荐
        $data['qq'] = $param['qq'];       //推荐
        $data['card_number'] = $param['card_number'];  //推荐,银行卡
      
        //扩展参数
        if(is_array($param) && !empty($param)){
        
             //非必填参数
            if($param['loan_amount']){
                 $data['loan_amount'] = $param['loan_amount']; 
            }
        
            if($param['loan_term']){
                 $data['loan_term'] = $param['loan_term']; 
            }
            if($param['loan_term_unit']){
                 $data['loan_term_unit'] = $param['loan_term_unit']; 
            }
            if($param['loan_date']){
                 $data['loan_date'] = $param['loan_date']; 
            }
            if($param['diploma']){
                 $data['diploma'] = $param['diploma']; 
            }
            if($param['marriage']){
                 $data['marriage'] = $param['marriage']; 
            }
            if($param['registered_address']){
                 $data['registered_address'] = $param['registered_address'];  
            }
            if($param['home_address']){
                 $data['home_address'] = $param['home_address']; 
            }
            if($param['company_name']){    
                 $data['company_name'] = $param['company_name'];  
            }    
            if($param['company_address']){   
                 $data['company_address'] = $param['company_address']; 
            }
            if($param['contact_address']){   
                  $data['contact_address'] = $param['contact_address'];      
            }
        
        }
       
        $contents =  self::curlPost($requestUrl,$data);
        //var_dump($contents);
        
        $contentArr = ($contents)? json_decode($contents,true):array();//强制把json格式转化为数组
        return $contentArr;
        
    }
    
   
   
    /**
     *  准入query查询接口 
     *   Jimmy Fu  2016-1-25
     *  @param  string   $report_id      报告编号
     * 
     */ 
    public function preloanQuery($report_id){
      
        //获取POST提交URL
        $data['app_name'] = self::$app_name;
        $data['report_id'] = $report_id;
        $requestUrl = self::$url.'/preloan/report?'.$this->getRequestUrl($data); 
         
        $contents =  self::curlGet($requestUrl);
       
        $contentArr = ($contents)? json_decode($contents,true):array();//强制把json格式转化为数组
        return $contentArr;
    }
   
  
   /**
    *  添加监控
    *  Jimmy Fu 2016-1-25
    *  $dataID =  $data['report_id'] = $report_id 或 $data['sequence_id'] = $sequence_id ; 
    */ 
   public function monitorAdd($dataID,$loan_date,$loan_term,$loan_amount,$loan_term_unit='MONTH'){
    
        
        //获取POST提交URL
        $requestUrl = self::$url.'/postloan/monitor/add?'.$this->getRequestUrl(); 
        
        $data = array();
        if(isset($dataID['report_id'])){
            $data['report_id'] = $dataID['report_id'] ;      //准入报告编号  
        }
        if(isset($dataID['sequence_id'])){
             $data['sequence_id'] = $dataID['sequence_id'] ;  // sequence_id
        }
        $data['loan_term'] = $loan_term;          //放款期限 必填
        $data['loan_date'] = $loan_date;          //放款日期 必填
        $data['loan_amount'] = $loan_amount;      //放款金额 必填
        $data['loan_term_unit'] = $loan_term_unit;  //默认是月，填写时可以为天或月:DAY,MONTH
       
    
        $contents =  self::curlPost($requestUrl,$data);
       
        $contentArr = ($contents)? json_decode($contents,true):array();//强制把json格式转化为数组
        return $contentArr;
    
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
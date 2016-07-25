<?php

/**
 *  安融征信业务层
 * @author Jimmy Fu 2015-12-3 
 * @copyright 2015
 */

class  credit_anrong_anrong{
    
    protected $onCache = true; //是否开启缓存结果
    
    protected $cacheTime = '300'; //默认设置缓存五分钟
   
    
    /** 
     * 安融征信入口，构造函数
     * 
     */
    public function __construct(){
        
        
    }
    
    /**
     *  设置是否开启缓存
     * 
     */ 
    public function  setCache($on){
        if($on){
            $this->onCache = true;
        }else{
            $this->onCache = false;
        }
        
    }
    
    
     /**
     *  设置缓存生效时间
     *  @param  int $time 缓存有效期，单位秒
     * 
     */ 
    public function  setCachetime($time){
        $this->cacheTime = $time;
    }
    
    /**
     *  个人关联工商查询 ,缓存查询结果
     *  Jimmy Fu 2015-12-4
     *  @param  string   $idcard    身份证号码
     */ 
    public function personRelCompany($idcard){
        
        $cache_keyname = 'credit_anrong_anrong_personRelCompany_'.$idcard;
        
        $dataInfo = S($cache_keyname);
        //如果缓存不存在或者关闭缓存
        if(!$this->onCache || empty($dataInfo)){
            $retData = credit_anrong_api::personRelCompany($idcard);  //通过接口调用工商信息
            if(!empty($retData)){
                S($cache_keyname,$retData,$this->cacheTime); 
               
            }
            $dataInfo = $retData;
        }
  
        return $dataInfo;
       
        
    }
    
    
    /**
     *  个人司法信息 接口  
     *   Jimmy Fu  2015-12-7
     *  @param  string   $name      姓名
     *  @param  string   $idcard    身份证号码
     */ 
    public function personJudicial($name,$idcard){
        $cache_keyname = 'credit_anrong_anrong_personJudicial_'.$idcard;
        
        $dataInfo = S($cache_keyname);
        //如果缓存不存在或者关闭缓存
        if(!$this->onCache || empty($dataInfo)){
            $retData = credit_anrong_api::personJudicial($name,$idcard);  //通过接口调用工商信息
            if(!empty($retData)){
                S($cache_keyname,$retData,$this->cacheTime); 
            }
            $dataInfo = $retData;
        }
  
        return $dataInfo;
    }
    
    
      /**
     *   企业司法信息查询 接口  
     *   Jimmy Fu  2015-12-3
     *  @param  string   $companyName    企业名称
     *  @param  string   $companyID    组织机构代码
     */ 
    public function companyJudicial($companyName,$companyID){
        $cache_keyname = 'credit_anrong_anrong_companyJudicial_'.helper_cache::makeKey($companyID,$companyName);
        
        $dataInfo = S($cache_keyname);
        //如果缓存不存在或者关闭缓存
        if(!$this->onCache || empty($dataInfo)){
            $retData = credit_anrong_api::companyJudicial($companyName,$companyID);  //通过接口调用工商信息
            if(!empty($retData)){
                S($cache_keyname,$retData,$this->cacheTime); 
            }
            $dataInfo = $retData;
        }
        return $dataInfo;
    }
    
    /**
     *  反欺诈 接口  
     *   Jimmy Fu  2015-12-10
     *  @param  string   $name      姓名
     *  @param  string   $idcard    身份证号码
     *  @param  array    $param     扩展参数
     */ 
    public function validFqz($name,$idcard,$param=array()){
        $cache_keyname = 'credit_anrong_anrong_validFqz_'.helper_cache::makeKey($name,$idcard,$param);
        
        $dataInfo = S($cache_keyname);
        //如果缓存不存在或者关闭缓存
        if(!$this->onCache || empty($dataInfo)){
            $retData = credit_anrong_api::validFqz($name,$idcard,$param=array());  //通过接口调用工商信息
            if(!empty($retData)){
                S($cache_keyname,$retData,$this->cacheTime); 
            }
            $dataInfo = $retData;
        }
        return $dataInfo;
    }
    
     /**
     *  风险预警 接口  
     *   Jimmy Fu  2015-12-3
     *  @param  string   $name      姓名
     *  @param  string   $idcard    身份证号码
     *  @param  array    $param     扩展参数
     */ 
    public function validFxyj($name,$idcard,$param=array()){
       
    }
    
    
    /**
     *  封装Curl Get方法
     */ 
    private function curlGet($url){
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
        if(debugS){
             pr($response);
        }
        curl_close ( $ch );
        return $content;
     }
 
    
    
}



?>
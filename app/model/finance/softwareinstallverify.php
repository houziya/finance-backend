<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/6/29
 * Time: 16:20
 */
class model_finance_softwareInstallVerify extends model
{
    public static $typeVerifyMobile = 0;//验证手机号操作
    public static $typeVerifyCode = 1;//验证激活码操作
    protected $tableName = 'sortware_install_verify';

    /*根据标识码获取临时信息*/
    public function getInfoByIdentificationType($identification, $type = 0, $field = null, $delcache = false) {
        $cachename = 'model_finance_sortware_install_verify__type_' . $type . '_identification_' . $identification;
        $info = S($cachename);
        if(empty($info) || $delcache) {
            $info = M('sortware_install_verify') -> where(array('identification' => $identification, 'type' => $type)) -> find();
            if (empty($info)) return $field ? '' : array();
            S($cachename, $info);
        }
        return $field ? $info[$field] : $info;
    }
    /*查询激活码是否存在
     *wangmengmeng
     *$code激活码值
     *2016-7-4
     */
    public function isExpireCode($code, $delcache = false) {
        $cachename = 'model_finance_sortware_install_verify_is_expire_code_' . $code;
        $count = S($cachename);
        if((empty($count) && $count != 0) || $delcache) {
            $count = M('sortware_install_verify') -> where(array('code' => $code, 'type' => self::$typeVerifyCode)) -> count();
            S($cachename, $count);
        }
        return $count > 0;
    }
    /*查询手机号是否存在
     *wangmengmeng
     *$code激活码值
     *2016-7-4
     */
    public function isExpireMobile($mobile, $delcache = false) {
        $cachename = 'model_finance_sortware_install_verify_is_expire_mobile_' . $mobile;
        $count = S($cachename);
        if((empty($count) && $count != 0) || $delcache) {
            $count = M('sortware_install_verify') -> where(array('code' => $mobile, 'type' => self::$typeVerifyMobile)) -> count();
            S($cachename, $count);
        }
        return $count > 0;
    }
    public function verifyMobile($mobile, $identification) {
        if($mobile && $identification) {
            return true;//直接通过
            /*扫描有没有注册过*/
            $clientMobile = D('finance/softwareClient') -> getInfoByIdentification($identification);
            if($clientMobile) {
                return $clientMobile['mobile'] === $mobile;
            }else {
                if(D('finance/softwareClient') -> isExpireMobile($mobile)) {
                    return false;
                }
            }
            /*接着去临时表扫描*/
            $verifyInfo = $this -> getInfoByIdentificationType($identification, self::$typeVerifyMobile);
            if($verifyInfo) {
                return $verifyInfo['mobile'] === $mobile;
            }else {
                return $this -> isExpireMobile($mobile) ? false : true;
            }
        }else {
            return false;
        }
    }

    /* 验证激活码是否存在,并且是否在有效期内*/
    public function vefifyCodeIsExpire($code) {
        $codeInfo = D('finance/softwareCodeBatchInfo') -> getInfoByCode($code);
        if(($codeInfo['code'] == $code) && (!in_array($codeInfo['status'], array(-1, 2))) && (time() >= $codeInfo['start_time'] && time() <= $codeInfo['end_time'])){
            return true;
        }else {
            return false;
        }
        //return intval(M('client_code_batchinfo') -> where(array('code' => $code, 'code' => 0)) ->count()) > 0;
    }
    /*验证激活码是否使用过*/
    public function verifyCode($code, $identification) {
        if($code && $identification) {
            /*扫描有没有注册过*/
            $clientCode = D('finance/softwareClient') -> getInfoByIdentification($identification);
            if($clientCode) {
                return $clientCode['code'] === $code;
            }else {
                if(D('finance/softwareClient') -> isExpireCode($code)) {
                    return false;
                }
            }
            /*扫描临时表*/
            $verifyCode = $this -> getInfoByIdentificationType($identification, self::$typeVerifyCode);
            if($verifyCode) {
                return $verifyCode['code'] === $code;
            }else {
                return $this -> isExpireCode($code) ? false : true;
            }
        }else {
            return false;
        }
    }
    /*
    * @param array $data 数据库相关信息
    * @return bool
    */
    public function save($data = array()){
        return $this->add($data);
    }
}
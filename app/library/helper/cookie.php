<?php
// +----------------------------------------------------------------------
// | Cookie管理类
// +----------------------------------------------------------------------

class helper_cookie {
    // 判断Cookie是否存在
    static function is_set($name) {
        return isset($_COOKIE[C('sys_cookie_prefix').$name]);
    }

    // 获取某个Cookie值
    static function get($name) {
        $value = self::is_set($name) ? $_COOKIE[C('sys_cookie_prefix').$name] : null;
        $value = $value ? unserialize(base64_decode($value)) : null;
        return $value;
    }

    // 设置某个Cookie值
    static function set($name,$value,$expire='',$path='',$domain='') {
        if($expire=='') {
            $expire =   C('sys_cookie_expire');
        }
        if(empty($path)) {
            $path = C('sys_cookie_path');
        }
        if(empty($domain)) {
            $domain =   C('sys_cookie_domain');
        }
        $expire =   !empty($expire)?    time()+$expire   :  0;
        $value   =  base64_encode(serialize($value));
        setcookie(C('sys_cookie_prefix').$name, $value,$expire,$path,$domain);
        $_COOKIE[C('sys_cookie_prefix').$name]  =   $value;
    }

    // 删除某个Cookie值
    static function delete($name) {
        self::set($name,'',time()-3600);
        unset($_COOKIE[C('sys_cookie_prefix').$name]);
    }

    // 清空Cookie值
    static function clear() {
        unset($_COOKIE);
    }
}
?>
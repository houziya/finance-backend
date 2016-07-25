<?php
// +----------------------------------------------------------------------
// | 禁止浏览器刷新
// +----------------------------------------------------------------------

class behavior_browsercheck extends behavior {
    public function run() {
        if($_SERVER['REQUEST_METHOD'] == 'GET') {
            $time = C('LIMIT_REFLESH_TIMES');
            if(empty($time)) $time = 5;
            //	启用页面防刷新机制
            $guid	=	md5($_SERVER['PHP_SELF']);
            // 检查页面刷新间隔
            if(cookie::is_set('_last_visit_time_'.$guid) && cookie::get('_last_visit_time_'.$guid)>time()-$time) {
                // 页面刷新读取浏览器缓存
                header('HTTP/1.1 304 Not Modified');
                exit();
            }else{
                // 缓存当前地址访问时间
                cookie::set('_last_visit_time_'.$guid, $_SERVER['REQUEST_TIME'],$_SERVER['REQUEST_TIME']+3600);
                header('Last-Modified:'.(date('D,d M Y H:i:s',$_SERVER['REQUEST_TIME']-$time)).' GMT');
            }
        }
    }
}
?>
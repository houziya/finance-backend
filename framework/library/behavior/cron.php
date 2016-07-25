<?php
// +----------------------------------------------------------------------
// | 计划任务执行
// +----------------------------------------------------------------------

class behavior_cron extends behavior {
    public function run() {
        // 锁定自动执行
        $lockfile	 =	 DATA_PATH.'/cron.lock';
        if(is_writable($lockfile) && filemtime($lockfile) > $_SERVER['REQUEST_TIME'] - C('CRON_MAX_TIME')) {
            return ;
        } else {
            touch($lockfile);
        }
        set_time_limit(1000);
        ignore_user_abort(true);

        // 载入cron配置文件
        // 格式 return array(
        // 'cronname'=>array('filename',intervals,nextruntime),...
        // );
        if(is_file(DATA_PATH.'/runtime/crons.php')) {
            $crons	=	include DATA_PATH.'/runtime/crons.php';
        }elseif(is_file(CONF_PATH.'/crons.php')){
            $crons	=	include CONF_PATH.'/crons.php';
        }
        if(isset($crons) && is_array($crons)) {
            $update	 =	 false;
            $log	=	array();
            foreach ($crons as $key=>$cron){
                if(empty($cron[2]) || $_SERVER['REQUEST_TIME']>=$cron[2]) {
                    // 到达时间 执行cron文件
                    $_beginTime	=	microtime(TRUE);
                    include CRON_PATH.$cron[0];
                    $_endTime	=	microtime(TRUE);
                    $_useTime	 =	 number_format(($_endTime - $_beginTime), 6);
                    // 更新cron记录
                    $cron[2]	=	$_SERVER['REQUEST_TIME']+$cron[1];
                    $crons[$key]	=	$cron;
                    $log[] = "Cron:$key Runat ".date('Y-m-d H:i:s')." Use $_useTime s\n";
                    $update	 =	 true;
                }
            }
            if($update) {
                // 记录Cron执行日志
                log::record(implode(' ',$log));
                // 更新cron文件
                $content  = "<?php\nreturn ".var_export($crons,true).";\n?>";
                file_put_contents(DATA_PATH.'/runtime/crons.php',$content);
            }
        }
        // 解除锁定
        unlink($lockfile);
        return ;
    }
}
?>
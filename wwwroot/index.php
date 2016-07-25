<?php
define('APP_NAME', 'renrentou');
define('APP_PATH', realpath(dirname(__FILE__).'/../app'));
define('ROOT_PATH', realpath(dirname(__FILE__)));
define('DATA_PATH', APP_PATH . '/data');
require(realpath(dirname(__FILE__)."/../framework/index.php"));

if(!IS_CLI){
	$_t = time();
	$_t1 = strtotime('2015-06-05 23:59:59');
	$_t2 = strtotime('2019-06-06 10:00:00');
	if($_t1 <= $_t && $_t <= $_t2){
		 $ip = getIp();
		if(!in_array($ip, array('127.0.0.1','124.193.163.170','124.202.178.110','122.71.148.197','122.71.183.231','122.71.149.59','122.71.141.160','122.71.180.150','122.71.154.58'))){
			//redirect('/notice.html');exit;

		}
	}
}
app::run();
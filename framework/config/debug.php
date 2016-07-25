<?php
// +----------------------------------------------------------------------
// | 默认的调试配置文件
// +----------------------------------------------------------------------

return  array(
    /* 日志设置 */
    'sys_log_record'            => true,   // 开启记录日志
    'sys_log_record_level'      => array('EMERG','ALERT','CRIT','ERR','WARN','NOTIC','INFO','DEBUG','SQL'),  // 允许记录的日志级别

    /* 数据库设置 */
    'sys_db_fields_cache'       => false,  // 是否启用字段缓存

    /* 运行时间设置 */
	'sys_show_run_time'			=> true,   // 运行时间显示
    'sys_show_adv_time'			=> true,   // 显示详细的运行时间
    'sys_show_db_time'			=> true,   // 显示数据库查询和写入次数
    'sys_show_cache_time'		=> true,   // 显示缓存操作次数
    'sys_show_usemem'			=> true,   // 显示内存开销
    'sys_show_trace'			=> true,   // 显示页面Trace信息 由Trace文件定义和Action操作赋值
    'sys_show_error_msg'        => true,   // 显示错误信息
    'sys_tpl_cache'				=> false,  // 是否开启模板编译缓存,设为false则每次都会重新编译
);
?>
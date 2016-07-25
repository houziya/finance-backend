<?php

// +----------------------------------------------------------------------
// | 惯例配置文件
// | 该文件请不要修改，如果要覆盖惯例配置的值，可在项目配置文件中设定和惯例不符的配置项
// | 配置名称大小写任意，系统会统一转换成小写
// | 所有配置参数都可以在生效前动态改变
// +----------------------------------------------------------------------

return array(
	/* 项目设定 */
	'sys_debug' => false, // 是否开启调试模式
	
	'sys_module_list' => array('www','admin'), //开通的模块列表

	/* Cookie设置 */
	'sys_cookie_expire' => 3600, // Coodie有效期
	'sys_cookie_domain' => '', // Cookie有效域名
	'sys_cookie_path' => '/', // Cookie路径
	'sys_cookie_prefix' => '', // Cookie前缀 避免冲突

	/* SESSION设置 */
	'sys_session_start' => false, // 是否自动开启Session
	'sys_session_prefix' => 'fee', // Session前缀

	/* 默认设定 */
	'sys_default_module' => 'www', // 默认模块名称
	'sys_default_controller' => 'index', // 默认控制器名称
	'sys_default_action' => 'index', // 默认操作名称
	'sys_default_charset' => 'utf-8', // 默认输出编码
	'sys_default_timezone' => 'PRC', // 默认时区
	'sys_default_ajax_return' => 'JSON', // 默认AJAX 数据返回格式,可选JSON XML ...
	'sys_default_theme' => 'default', // 默认模板主题名称
	'sys_default_lang' => 'zh-cn', // 默认语言
	'sys_default_key' => 'iloveyouchina', // 默认可逆加密钥
	'sys_default_key_filter' => '', // htmlspecialchars 默认参数过滤方法 用于 $this->_get('变量名');$this->_post('变量名')...

	/* 数据库设置 */
	'sys_db_type' => 'mysql', // 数据库类型
	'sys_db_host' => '192.168.1.252', // 服务器地址
	'sys_db_name' => 'renrentou_dev', // 数据库名
	'sys_db_user' => 'root', // 用户名
	'sys_db_pwd' => '123456', // 密码
	'sys_db_port' => 3306, // 端口
	'sys_db_prefix' => '', // 数据库表前缀
	'sys_db_charset' => 'utf8', // 数据库编码默认采用utf8
	'sys_db_fields_check' => false, // 是否进行字段类型检查
	'sys_db_fields_cache' => true, // 是否启动数据库字段缓存	
	'sys_db_deploy_type' => 0, // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
	'sys_db_rw_separate' => false, // 数据库读写是否分离 主从式有效
	'sys_db_master_num' => 1, // 读写分离后 主服务器数量
	'sys_db_sql_build_cache' => false, // 数据库查询的SQL创建缓存
	'sys_db_sqllog' => false, // SQL执行日志记录
	'sys_db_field_version' => 1, // 数据库字段版本号，修改数据库字段后，需要修改此值以刷新全部字段缓存

	/* 数据缓存设置 */
	'sys_cache_open' => true, //是否开启全局缓存 false否 true是
	'sys_cache_time' => 86400, // 数据缓存有效期
	'sys_cache_compress' => false, // 数据缓存是否压缩缓存
	'sys_cache_check' => false, // 数据缓存是否校验缓存
	'sys_cache_prefix' => '', // 缓存前缀
	'sys_cache_type' => 'file', // 数据缓存类型,支持:File|Db|Apc|Memcache|Shmop|Sqlite| Xcache|Apachenote|Eaccelerator
	'sys_cache_path' => CACHE_PATH, // 缓存路径设置 (仅对File方式缓存有效)
	'sys_cache_subdir' => true, // 使用子目录缓存 (自动根据缓存标识的哈希创建子目录)
	'sys_cache_path_level' => 2, // 子目录缓存级别

	/* 错误设置 */
	'sys_error_message' => '您浏览的页面暂时发生了错误！请稍后再试～', //错误显示信息,非调试模式有效
	'sys_error_page' => '', // 错误定向页面

	/* 静态缓存设置 */
	'sys_html_cache' => false, // 是否开启静态缓存
	'sys_html_cachetime' => 600, // 静态缓存有效期
	'sys_html_read' => 0, // 静态缓存读取方式 0 readfile 1 redirect
	'sys_html_suffix' => '.html', // 默认静态文件后缀
	'sys_html_path' => CACHE_PATH.'/html', //HTML缓存路径

	/* 语言设置 */
	'sys_lang_switch' => false, // 是否开启多语言包功能
	'sys_lang_auto' => false, // 自动侦测语言 开启多语言功能后有效

	/* 日志设置 */
	'sys_log_exception_record' => false, // 是否记录异常信息日志(默认为开启状态)
	'sys_log_record' => true, // 默认记录日志
	'sys_log_file_size' => 10485760, // 日志文件大小限制10M
	'sys_log_record_level' => array('EMERG','ALERT','CRIT','ERR'), // 允许记录的日志级别

	/* 分页设置 */
	'sys_page_rollpage' => 5, // 分页显示页数
	'sys_page_listrows' => 20, // 分页每页显示记录数

	/* 运行时间设置 */
	'sys_show_run_time' => false, // 运行时间显示
	'sys_show_adv_time' => false, // 显示详细的运行时间
	'sys_show_db_time' => false, // 显示数据库查询和写入次数
	'sys_show_cache_time' => false, // 显示缓存操作次数
	'sys_show_usemem' => false, // 显示内存开销
	'sys_show_trace' => false, // 显示页面Trace信息 由Trace文件定义和Action操作赋值
	'sys_show_error_msg' => false, // 显示错误信息

	/* 模板引擎设置 */
	'sys_tpl_engine' => 'fee', // 默认模板引擎
	'sys_tpl_theme' => false, // 开启多套模版主题，开启后模版目录下会增加下一级主题目录，默认为default目录
	'sys_tpl_detect' => false, // 自动侦测模板主题
	'sys_tpl_suffix' => '.php', // 模板文件后缀
	'sys_tpl_content_type' => 'text/html', // 默认模板输出类型
	'sys_tpl_deny_func' => 'echo,exit', // 模板引擎禁用函数
	'sys_tpl_replace_string' => array(), // 模板引擎要自动替换的字符串，必须是数组形式。
	'sys_tpl_l_delim' => '<{', // 模板引擎普通标签开始标记
	'sys_tpl_r_delim' => '}>', // 模板引擎普通标签结束标记
	'sys_tpl_var_identify' => 'array', // 模板变量识别。留空自动判断,参数为'obj'则表示对象
	'sys_tpl_strip_space' => false, // 是否去除模板文件里面的html空格与换行

	'sys_tpl_cache_suffix' => '.php', // 模板缓存后缀
	'sys_tpl_cache' => true, // 是否开启模板编译缓存,设为false则每次都会重新编译
	'sys_tpl_cache_time' => -1, // 模板缓存有效期 -1 为永久，(以数字为值，单位:秒)
	'sys_tpl_ctl_error' => 'public_error', // 默认错误跳转对应的模板文件
	'sys_tpl_ctl_success' => 'public_success', // 默认成功跳转对应的模板文件
	'sys_tpl_trace_file' => FEE_PATH . '/data/tpl/page_trace.tpl.php', // 页面Trace的模板文件
	'sys_tpl_exception_file' => FEE_PATH . '/data/tpl/fee_exception.tpl.php', // 异常页面的模板文件
	'sys_taglib_begin' => '<', // 标签库标签开始标记
	'sys_taglib_end' => '>', // 标签库标签结束标记
	'sys_taglib_load' => false, // 是否使用内置标签库之外的其它标签库，默认自动检测
	'sys_taglib_build_in' => 'cx', // 内置标签库名称(标签使用不必指定标签库名称),以逗号分隔
	'sys_taglib_pre_load' => '', // 需要额外加载的标签库(须指定标签库名称)，多个以逗号分隔
	'sys_taglib_level' => 3, // 标签嵌套级别
	'sys_tag_extend_parse' => '', // 指定对普通标签进行扩展定义和解析的函数名称。

	/* 表单令牌验证 */
	'sys_token' => false, // 开启令牌验证
	'sys_token_name' => '_hash_', // 令牌验证的表单隐藏字段名称

	/* URL设置 */
	'sys_domain_module' => true, // 是否启用二级域名当作模块名
	'sys_route' => false, // 是否开启URL路由	
	'sys_route_rules' => array(), //路由参数
	/* sys_route_rules路由参数
	  'rulename' => array('module','controller','action','var1,var2'), //普通路由
	  'rulename@' => array(array('module','controller','action','var1,var2','/^(\d+)(\/p\/\d+)?$/'), ), //正则路由
	  '*' => array('module','controller','action','var1,var2'), //泛普通路由
	  '*@' => array(array('module','controller','action','var1,var2','/^(\d+)(\/p\/\d+)?$/'), ), //泛正则路由
	 */
	 
	'sys_url_mode' => 0, // URL生成模式,可选参数0、1、2,代表以下模式 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE 模式);
	'sys_url_depr' => '/', // PATHINFO模式下，各参数之间的分割符号
	'sys_url_suffix' => '', // URL伪静态后缀设置

	'sys_output_encode' => false, // 页面压缩输出

	/* 系统变量名称设置 */
	'var_module' => 'm', // 默认模块获取变量
	'var_controller' => 'c', // 默认控制器获取变量
	'var_action' => 'a', // 默认操作获取变量
	'var_router' => 'r', // 默认路由获取变量
	'var_page' => 'p', // 默认分页跳转变量
	'var_tpl' => 't', // 默认模板切换变量
	'var_lang' => 'l', // 默认语言切换变量
	'var_ajax_submit' => 'ajax', // 默认的AJAX提交变量
	'var_jsonp_handler' => 'callback',
	
	/* 其他变量 */
	'sys_config_list' => array('router', 'html'), // 扩展配置列表
	'sys_font_type' => FEE_PATH.'/data/elephant.ttf', //验证码字体文件
);
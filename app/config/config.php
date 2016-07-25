<?php
// +----------------------------------------------------------------------
// | 惯例配置文件
// | 该文件请不要修改，如果要覆盖惯例配置的值，可在项目配置文件中设定和惯例不符的配置项
// | 配置名称大小写任意，系统会统一转换成小写
// | 所有配置参数都可以在生效前动态改变
// +----------------------------------------------------------------------

//运行模式有 devel（开发）、deploy（生产） 和 test（测试） 三种
define("RUN_MODE", 'devel');

return array(
	'sys_debug' => true, // 是否开启调试模式
	
	'url' => array(
		'www' => HTTP_PROTOCAL . 'www.wmm.com',
		'admin' => HTTP_PROTOCAL . 'admin.wmm.com',
		'app' => HTTP_PROTOCAL.'app.wmm.com',
		'api' => HTTP_PROTOCAL.'api.wmm.com',
        'img' => HTTP_PROTOCAL.'admin.wmm.com',
        'img2' => HTTP_PROTOCAL.'img2.wmm.com'
	),
    
    
	
	//网站配置变量
	'sysconfig' => array(
		'web_name' => '人人投财务监管', //网站名称
		'web_domain' => 'wmm.com', //网站域名
		'company_name' => '北京人人投网络科技有限公司', //公司名称
		'company_address' => '北京市西城区莲花池东路106号汇融大厦A座1204—1206', //公司地址
		'contact_email' => 'service@renrentou.com', //联系我们EMAIL
		'contact_email2' => 'business@renrentou.com', //商业合作EMAIL
		'contact_mobile' => '400-070-5286', //客服电话
		'contact_tel' => '400-070-5286', //商务电话
		'qq' => '2355451278', //官方QQ
		'beian_icp' => '京ICP备12015672号-7', //ICP备案号
		'beian_icp2' => '京ICP证140078号', //ICP经营许可证
		'weixin' => 'rrtou51', // 公众微信
		'ceo_weixin' => '18910812788', // CEO微信
		'complaint_mobile' => '13811393285', // 投诉电话
		'kf_online' => 'http://cs.ecqun.com/cs/talkrand?id=1413882&scheme=0&version=4.0.0.0', //web端客服链接
		'kf_online2' => 'http://cs.ecqun.com/mobile/rand?id=1413882', //移动端客服链接
	),
    
    //是否显示分站的地址
    'address_is_show'=>true,
    //是否显示分站的联系电话
    'tel_is_show'=>true,
    //是否生成缩略图地址
	'is_thumb_open'=>false,
    // 网站图片缩略尺寸
    'thumb_config' => array(
        'face'              => '@1e_100w_100h_1c_0i_1o_90Q_1x.jpg', // 头像
        'project_w300_h200' => '@1e_300w_200h_1c_0i_1o_90Q_1x.jpg', // 项目图片 300 x 200
        'project_w253_h168' => '@1e_253w_168h_1c_0i_1o_90Q_1x.jpg', // 项目图片 253 x 168
    ),
	
	//邮件发送帐号，可以有多条。随机选择发送
	'email_config' => array(
		'qq' => array('host' => 'smtp.exmail.qq.com', 'port' => '25', 'username' => 'no-replay@renrentou.com', 'password' => '', 'from' => 'no-replay@renrentou.com', 'fromname' => '人人投'),
		'sohu' => array('host' => 'http://sendcloud.sohu.com/webapi/mail.send.json', 'api_user' => 'renrentou', 'api_key' => '', 'from' => 'service@mail.renrentou.com', 'fromname' => '人人投'),
	),
	//群发 短信和邮件 加上指定的手机号码邮件   目前添加客服部 韩福娟 手机和邮箱
	'em_designated'=>array('mobiles'=>'18513617736','emails'=>'2355534829@qq.com'),
	//短信发送帐号配置
	'sms_config' => array(
		//亿美短信
		'yimei' => array('url' => 'http://sdk999ws.eucp.b2m.cn:8080/sdk/SDKService', 'username' => '9SDK-EMY-0999-JDWML', 'password' => '','sessionkey' => ''),
		'yimei2' => array('url' => 'http://hprpt2.eucp.b2m.cn:8080/sdk/SDKService?wsdl', 'username' => '8SDK-EMY-6699-RERUP', 'password' => '','sessionkey' => ''),
		//容联云通讯
		'yuntongxun' => array('host'=>'app.cloopen.com','port'=>'8883','version'=>'2013-12-26','main_account'=>'8a48b5514a61a814014a79d945a60e43','main_token'=>'','app_id'=>''),
		//漫道科技
		'mdkj' => array(
			'sn' => 'SDK-BBX-010-22614', ////替换成您自己的序列号
			'pwd' => '', //此处密码需要加密 加密方式为 md5(sn+password) 32位大写
			'mobile' => '', //手机号 多个用英文的逗号隔开 post理论没有长度限制.推荐群发一次小于等于10000个手机号
			'content' => '', //iconv( "gb2312", "UTF-8//IGNORE" ,'您好测试短信[XXX公司]'),//短信内容
			'ext' => '',
			'stime' => '', //定时时间 格式为2011-6-29 11:09:21
			'msgfmt' => '',
			'rrid' => ''
		),
	),

	'upload_filetype' => 'jpg|jpeg|gif|png|bmp|doc|docx|xls|xlsx|ppt|pptx|pdf|txt|rar|zip|swf', //允许上传的全部附件后缀
	
	'key_password' => 'rrt^&*', //密码加密密钥	
	'auth_open' => true, // 开启后台项目权限检查
	'auth_type' => 2, // 认证类型 1 登录认证 2 实时查询认证
	'auth_not_m' => '', // 无需认证模块前缀
	'auth_not_c' => 'public,ajax', // 无需认证控制器前缀
	'auth_not_a' => 'public,ajax', // 无需认证的操作前缀
	
	//验证码开关配置:  注意为了安全，开关值必须配置为0才可关闭验证码
    'captcha' => array(
        //注册场景
        "register" => 0,
        //登陆场景
        "login" => 1,
        //投标场景
        "tender" => 1,
        //管理后台场景
        "admin" => 0,
        //手机验证码
        "mobile" => 0,
    	//我的账户  user@user/index
    	"userinfo" => 1,
    	//找回密码  www@user/passwordfind
    	"passwordfind" => 1,
    ),
    
	'sys_module_list' => array('www','admin','app','api'), //开通的模块列表

	'static_compress' => false, //是否开启静态资源合并压缩
	
	/* SESSION设置 */
	'sys_session_start' => true, // 是否自动开启Session
	'sys_session_prefix' => '', // Session前缀
	
	/* Cookie设置 */
	'sys_cookie_expire' => 3600, // Coodie有效期
	'sys_cookie_domain' => '.wmm.com', // Cookie有效域名
	'sys_cookie_path' => '/', // Cookie路径
	'sys_cookie_prefix' => 'cw_', // Cookie前缀 避免冲突
	
	/* 数据库设置 */
	'sys_db_type' => 'mysql', // 数据库类型
	'sys_db_host' => '172.16.0.252', // 服务器地址
	'sys_db_name' => 'finance', // 数据库名
	'sys_db_user' => 'root', // 用户名
	'sys_db_pwd' => '123456', // 密码
	'sys_db_port' => 3306, // 端口
	
	/* 数据缓存设置 */
	'sys_cache_open' => true, //是否开启全局缓存 false否  true是
	'sys_cache_time' => 86400, // 数据缓存有效期
	'sys_cache_prefix' => 'devcaiwu_', // 缓存前缀
	'sys_cache_type' => 'redis',
	//'sys_redis_host' => '172.16.0.252',
    'sys_redis_host' => '127.0.0.1',
	'sys_redis_port' => 6379,

	'sys_url_mode' => 2, // URL生成模式,可选参数0、1、2,代表以下模式 0 (普通模式); 1 (PATHINFO 模式); 2 (REWRITE 模式);
	'sys_url_suffix' => '', // URL伪静态后缀设置
	
	'sys_default_key'  => 'DUjjg9d7DHFY7h', // 默认可逆加密钥
	'sys_platform_key' => 'DUjjg9d7DHFY7h', // 推广密钥
  
	//版本号
	'web_version' => '1',

);
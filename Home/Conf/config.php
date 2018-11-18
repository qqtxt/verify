<?php
defined('JETEE_PATH') or exit();
//大小写一样
return array(
	'VER'           	=>'1.0.0',
	'DB_TYPE'           =>'mysqli',

	'DB_NAME'           =>'verify_net',
	'DB_HOST'           =>'127.0.0.1',
	'DB_USER'           =>'qqtxt',
	'DB_PWD'            =>'zhh1981zhh',
	'DB_PREFIX'         =>'je_',
    'DB_PORT'           =>'3306',
	
	'DB_SQL_BUILD_CACHE'    => true, // 数据库查询的SQL创建缓存    用pdo后无效
	
	'TOKEN_ON'      		=> true, // 是否开启令牌验证
	'TOKEN_NAME'     		=> '___t_token___',    // 令牌验证字段名称
	'TOKEN_TYPE'     		=> 'md5',   // 令牌验证哈希规则
	'TOKEN_RESET'    		=> true, // 检查令牌错误后是否重置

	'OUTPUT_ENCODE'			=>true,
	/*'DATA_CACHE_PREFIX'     => 'ma_', // 缓存前缀
	'SESSION_PREFIX'        => 'ma_', 
	'COOKIE_PREFIX'			=> 'ma_',*/
	
    /* Cookie设置 */
    'COOKIE_EXPIRE'         => 0,       // 有效期 0是会话
    //'COOKIE_DOMAIN'         => defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN :'', // Cookie有效域名
    'COOKIE_PATH'           => '/',     // Cookie路径
    'COOKIE_SECURE'         => defined('IS_SSL') ?  (IS_SSL ? true : false) :'',     // Cookie路径
    'COOKIE_HTTPONLY'       => true,     // Cookie路径
	
    /* SESSION设置 */
    'SESSION_OPTIONS'       => array('expire'=>864000,'name'=>'___s_token___'), // session 配置数组 支持type name id path expire domain 等参数  ,'cache_expire'=>120,'cache_limiter'=>'public' ,'domain'=>defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN :''
    'SESSION_TYPE'          => '', // session hander驱动 Memcache Db Redis  或空为系统自带
    /* 数据缓存设置 */
    'DATA_CACHE_TYPE'       => 'Redis',  // Redis|Memcache 数据缓存类型,支持:File|Db|Apc|Memcache|Shmop|Sqlite|Xcache|Apachenote|Eaccelerator|Redis	

	#'MEMCACHE_HOST'		=>'127.0.0.1',	'MEMCACHE_PORT'		=>'11211',


	'APP_GROUP_LIST' 		=> 'Home,Admin',
    'URL_CASE_INSENSITIVE'  => true,   // 默认false 表示URL区分大小写 true则表示不区分大小写
	'URL_MODEL'				=>2,
	'TMPL_FILE_DEPR'		=>'_',
	//自动加载	'APP_AUTOLOAD_PATH'		=>'@.Vendor',// 自动加载机制的自动搜索路径,注意搜索顺序
    'DEFAULT_FILTER'        => 'trim,jeHtmlspecialchars',
	
	/*项目配置*/
	'JS_MOD'				=>defined('APP_DEBUG') && APP_DEBUG ? 'min' :'min',//css与js是用cdn 还是压缩min  还是原生no_min  还可以增加 baidu_cdn   xx_cdn
	'EMAIL_GAP_TIME'		=>60, //用户发送邮箱间隙	
	'sn_captcha_email'		=>'captcha_email',	//接收验证码邮箱 session命名
	'sn_email_send_time'	=>'email_send_time',//邮件发送时间 session命名
	'sn_email_captcha'		=>'email_captcha',//邮件发送的验证码 session命名
	
	'sms_send_gap'		=>60, //用户发送短信间隙
	
	

);
?>
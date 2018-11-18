<?php
// 入口文件
// 记录开始运行时间
defined('MODE_NAME') or define('MODE_NAME','Jetee');
defined('RUNTIME_PATH') or define('RUNTIME_PATH',APP_PATH.'Runtime/');
defined('RUNTIME_FILE') or define('RUNTIME_FILE',RUNTIME_PATH.'~'.MODE_NAME.'_runtime.php');
if((!defined('APP_DEBUG') || !APP_DEBUG )&& is_file(RUNTIME_FILE)){//如果存在编译文件
	include RUNTIME_FILE;
	exit;
}
$GLOBALS['_beginTime'] = microtime(TRUE);
if(PHP_SAPI!='cli')header_remove('X-Powered-By');
define('MEMORY_LIMIT_ON',function_exists('memory_get_usage'));// 记录内存初始使用
if(MEMORY_LIMIT_ON) $GLOBALS['_startUseMems'] = memory_get_usage();

const EXT               =   '.class.php';// 类文件后缀
defined('JETEE_PATH')   or define('JETEE_PATH',     __DIR__.'/');// 系统目录定义
defined('APP_PATH') 	or define('APP_PATH', dirname($_SERVER['SCRIPT_FILENAME']).'/');
defined('APP_DEBUG') 	or define('APP_DEBUG',false); // 是否调试模式
defined('BUILD_DIR_SECURE') 	or define('BUILD_DIR_SECURE',true); // 生成安全文件 空index.html

//系统检测
define('OPCACHE_ENABLE',ini_get('opcache.enable'));
if(version_compare(PHP_VERSION,'5.4.0','<'))  die('require PHP > 5.4.0 !');
define('JETEE_VERSION', '1.0.1');//  版本信息3.1.3
define('MAGIC_QUOTES_GPC',false);//5.4起false

define('IS_CGI',substr(PHP_SAPI, 0,3)=='cgi' ? 1 : 0 );
define('IS_WIN',strstr(PHP_OS, 'WIN') ? 1 : 0 );
define('IS_CLI',PHP_SAPI=='cli'? 1   :   0);

// 项目名称
defined('APP_NAME') or define('APP_NAME', basename(dirname($_SERVER['SCRIPT_FILENAME'])));

if(!IS_CLI) {
	// 当前入口文件名  /cms/index.php
	if(!defined('_PHP_FILE_')) {
		if(IS_CGI) {//CGI/FASTCGI模式下			
			$_temp  = explode('.php',$_SERVER['PHP_SELF']);
			define('_PHP_FILE_',    rtrim(str_replace($_SERVER['HTTP_HOST'],'',$_temp[0].'.php'),'/'));
		}else {
			define('_PHP_FILE_',   strpos($_SERVER['SCRIPT_NAME'],$_SERVER["DOCUMENT_ROOT"])===0 ? str_replace($_SERVER["DOCUMENT_ROOT"],'',rtrim($_SERVER['SCRIPT_NAME'],'/')) :rtrim($_SERVER['SCRIPT_NAME'],'/'));
	   }
	}
	if(!defined('__ROOT__')) {
		// 网站URL根目录  /cms
		if( strtoupper(APP_NAME) == strtoupper(basename(dirname(_PHP_FILE_))) ) {
			$_root = dirname(dirname(_PHP_FILE_));
		}else {
			$_root = dirname(_PHP_FILE_);
		}
		define('__ROOT__',   (($_root=='/' || $_root=='\\')?'':$_root));

	}
	
	//支持的URL模式
	define('URL_COMMON',      0);   //普通模式
	define('URL_PATHINFO',    1);   //PATHINFO模式
	define('URL_REWRITE',     2);   //REWRITE模式
	define('URL_COMPAT',      3);   // 兼容模式
}
// 路径设置 可在入口文件中重新定义 所有路径常量都必须以/ 结尾
defined('CORE_PATH')    or define('CORE_PATH',      JETEE_PATH.'Lib/'); // 系统核心类库目录
defined('EXTEND_PATH')  or define('EXTEND_PATH',    JETEE_PATH.'Extend/'); // 系统扩展目录
defined('MODE_PATH')    or define('MODE_PATH',      JETEE_PATH.'Mode/'); // 模式扩展目录
defined('ENGINE_PATH')  or define('ENGINE_PATH',    APP_PATH.'Extend/Engine/Jetee/'); // 引擎扩展目录
defined('VENDOR_PATH')  or define('VENDOR_PATH',    EXTEND_PATH.'Vendor/'); // 第三方类库目录
defined('LIBRARY_PATH') or define('LIBRARY_PATH',   EXTEND_PATH.'Library/'); // 扩展类库目录
defined('COMMON_PATH')  or define('COMMON_PATH',    APP_PATH.'Common/'); // 项目公共目录
defined('LIB_PATH')     or define('LIB_PATH',       APP_PATH.'Lib/'); // 项目类库目录
defined('CONF_PATH')    or define('CONF_PATH',      APP_PATH.'Conf/'); // 项目配置目录
defined('LANG_PATH')    or define('LANG_PATH',      APP_PATH.'Lang/'); // 项目语言包目录
defined('TMPL_PATH')    or define('TMPL_PATH',      APP_PATH.'Tpl/'); // 项目模板目录
defined('HTML_PATH')    or define('HTML_PATH',      APP_PATH.'Html/'); // 项目静态目录
defined('LOG_PATH')     or define('LOG_PATH',       RUNTIME_PATH.'Logs/'); // 项目日志目录
defined('TEMP_PATH')    or define('TEMP_PATH',      RUNTIME_PATH.'Temp/'); // 项目缓存目录
defined('DATA_PATH')    or define('DATA_PATH',      RUNTIME_PATH.'Data/'); // 项目数据目录
defined('CACHE_PATH')   or define('CACHE_PATH',     RUNTIME_PATH.'Cache/'); // 项目模板缓存目录
#$defs  = get_defined_constants(TRUE);print_r($defs['user']);exit;
// 为了方便导入第三方类库 设置Vendor目录到include_path
set_include_path(get_include_path() . PATH_SEPARATOR . VENDOR_PATH);
// 加载引擎必须文件 并负责自动目录生成
require JETEE_PATH.'Common/common.php';	
if(!IS_CLI) {
	define('IS_SSL',		is_ssl());
	define('DOMAIN_URL',	get_domain());
	define('COOKIE_DOMAIN',	get_cookie_domain());//cookie域名  如.music.qq.com   .ma863.com  去除当前子域名
	define('ROOT_URL',trim(__ROOT__,'/').'/');//cms安装目录 /shop或空
}
// 读取核心文件列表
$list = array(
	CORE_PATH.'Core/Jetee.class.php',
	CORE_PATH.'Core/JeteeException.class.php',  // 异常处理类
	CORE_PATH.'Core/Behavior.class.php',
);
// 加载模式文件列表
foreach ($list as $key=>$file){
	if(is_file($file)) require_cache($file);
}
// 检查项目目录结构 如果不存在则自动创建
if(!is_dir(LIB_PATH)) {
	// 创建项目目录结构
	build_app_dir();
}elseif(!is_dir(CACHE_PATH)){
	// 检查缓存目录
	check_runtime();
}elseif(APP_DEBUG){
	// 调试模式切换删除编译缓存
	if(is_file(RUNTIME_FILE))   unlink(RUNTIME_FILE);
}

// 记录加载文件时间
G('loadTime');
// 执行入口
Jetee::Start();

// 检查缓存目录(Runtime) 如果不存在则自动创建
function check_runtime() {
	if(!is_dir(RUNTIME_PATH)) {
		mkdir(RUNTIME_PATH);
	}elseif(!is_writeable(RUNTIME_PATH)) {
		header('Content-Type:text/html; charset=utf-8');
		exit('目录 [ '.RUNTIME_PATH.' ] 不可写！');
	}
	mkdir(CACHE_PATH);  // 模板缓存目录
	if(!is_dir(LOG_PATH))   mkdir(LOG_PATH);    // 日志目录
	if(!is_dir(TEMP_PATH))  mkdir(TEMP_PATH);   // 数据缓存目录
	if(!is_dir(DATA_PATH))  mkdir(DATA_PATH);   // 数据文件目录
	return true;
}

// 创建编译缓存
function build_runtime_cache($append='') {
	// 生成编译文件
	$defs   = get_defined_constants(TRUE);
	$content=  'if(PHP_SAPI!=\'cli\')header_remove(\'X-Powered-By\');$GLOBALS[\'_beginTime\'] = microtime(TRUE);';
	$content.= array_define($defs['user']);
	$content.= 'if(MEMORY_LIMIT_ON) $GLOBALS[\'_startUseMems\'] = memory_get_usage();set_include_path(get_include_path() . PATH_SEPARATOR . VENDOR_PATH);';
	// 读取核心编译文件列表
	$list = array(
		JETEE_PATH.'Common/common.php',
		CORE_PATH.'Core/Jetee.class.php',
		CORE_PATH.'Core/JeteeException.class.php',
		CORE_PATH.'Core/Behavior.class.php',
	);
	foreach ($list as $file){
		$content .= compile($file);
	}
	// 系统行为扩展文件统一编译
	$content .= build_tags_cache();	
	// 编译框架默认语言包和配置参数
	$content   .= $append."\nL(".var_export(L(),true).");C(".var_export(C(),true).');G(\'loadTime\');Jetee::Start();';
	file_put_contents(RUNTIME_FILE,strip_whitespace('<?php '.str_replace("defined('JETEE_PATH') or exit();",' ',$content)));
}

// 编译系统行为扩展类库
function build_tags_cache() {
	$tags = C('extends');
	$content = '';
	foreach ($tags as $tag=>$item){
		foreach ($item as $key=>$name) {
			$content .= is_int($key)?compile(CORE_PATH.'Behavior/'.$name.'Behavior.class.php'):compile($name);
		}
	}
	return $content;
}

// 创建项目目录结构
function build_app_dir() {
	// 没有创建项目目录的话自动创建
	if(!is_dir(APP_PATH)) mkdir(APP_PATH,0755,true);
	if(is_writeable(APP_PATH)) {
		$dirs  = array(
			LIB_PATH,
			RUNTIME_PATH,
			CONF_PATH,
			COMMON_PATH,
			LANG_PATH,
			CACHE_PATH,
			TMPL_PATH,
			TMPL_PATH.C('DEFAULT_THEME').'/',
			LOG_PATH,
			TEMP_PATH,
			DATA_PATH,
			LIB_PATH.'Model/',
			LIB_PATH.'Action/',
			LIB_PATH.'Behavior/',
			LIB_PATH.'Widget/',
			);
		foreach ($dirs as $dir){
			if(!is_dir($dir))  mkdir($dir,0755,true);
		}
		// 如果定义有BUILD_DIR_SECURE 写入目录安全文件  index.html
		build_dir_secure($dirs);
		// 写入初始配置文件
		if(!is_file(CONF_PATH.'config.php'))
			file_put_contents(CONF_PATH.'config.php',"<?php\nreturn array(\n\t//'配置项'=>'配置值'\n);\n?>");
		// 写入测试Action
		if(!is_file(LIB_PATH.'Action/IndexAction.class.php'))
			build_first_action();
	}else{
		header('Content-Type:text/html; charset=utf-8');
		exit('项目目录不可写，目录无法自动生成！<BR>请使用项目生成器或者手动生成项目目录~');
	}
}

// 创建测试Action
function build_first_action() {
	$content = file_get_contents(JETEE_PATH.'Tpl/default_index.tpl');
	file_put_contents(LIB_PATH.'Action/IndexAction.class.php',$content);
}

// 生成目录安全文件
function build_dir_secure($dirs=array()) {
	// 目录安全写入
	if(defined('BUILD_DIR_SECURE') && BUILD_DIR_SECURE) {
		defined('DIR_SECURE_FILENAME')  or define('DIR_SECURE_FILENAME',    'index.html');
		defined('DIR_SECURE_CONTENT')   or define('DIR_SECURE_CONTENT',     ' ');
		// 自动写入目录安全文件
		$content = DIR_SECURE_CONTENT;
		$files = explode(',', DIR_SECURE_FILENAME);
		foreach ($files as $filename){
			foreach ($dirs as $dir)
				file_put_contents($dir.$filename,$content);
		}
	}
}



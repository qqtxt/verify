<?php
defined('JETEE_PATH') or exit();
/**
 * 框架 Portal类  核心类
 */
class Jetee {
	// 类映射
	private static $_map      = array();
	// 实例化对象
	private static $_instance = array();
	/**
	 * 应用程序初始化
	 * @access public
	 * @return void
	 */
	static public function start() {
		if(!IS_CLI){
			header('Referrer-Policy: no-referrer');
			if(IS_SSL) header('Strict-Transport-Security: max-age=31536000');//输出安全头部
			//header('X-Frame-Options: SAMEORIGIN');//页面只能被本站页面嵌入到iframe或者frame中。
			header('X-XSS-Protection: 1; mode=block');
			header('X-Content-Type-Options: nosniff');
			//header("Content-Security-Policy:default-src 'self'; script-src 'self' https://apps.bdimg.com; connect-src 'self'; img-src 'self'; style-src 'self' https://apps.bdimg.com");
		}
		/*/ 设定错误和异常处理*/
		register_shutdown_function(array('Jetee','fatalError'));//定义PHP程序执行完成或意外死掉后执行的函数
		set_error_handler(array('Jetee','appError'));// 设置一个用户定义的错误处理函数
		set_exception_handler(array('Jetee','appException'));//自定义异常处理。
		// 注册AUTOLOAD方法
		spl_autoload_register(array('Jetee', 'autoload'));
		//[RUNTIME]
		Jetee::buildApp();         // 预编译项目
		//[/RUNTIME]
		// 运行应用
		App::run();
		return;
	}

	//[RUNTIME]
	/**
	 * 读取配置信息 编译项目
	 * @access private
	 * @return string
	 */
	static private function buildApp() {
		$mode   = include MODE_PATH.strtolower(MODE_NAME).'.php';
		// 加载核心惯例配置文件
		C(include JETEE_PATH.'Conf/convention.php');
		if(isset($mode['config'])) {// 加载模式配置文件
			C( is_array($mode['config'])?$mode['config']:include $mode['config'] );
		}
		// 加载项目配置文件
		if(is_file(CONF_PATH.'config.php'))
			C(include CONF_PATH.'config.php');
		
		
		// 加载框架底层语言包
		L(include APP_PATH.'Lang/'.strtolower(C('DEFAULT_LANG')).'.php');
		// 加载模式系统行为定义
		if(C('APP_TAGS_ON') && isset($mode['extends'])) {
			C('extends',$mode['extends']);
		}
		// 加载应用行为定义
		if(isset($mode['tags'])) {
			C('tags', $mode['tags']);
		}

		$compile   = '';
		// 读取核心编译文件列表
		$list  =  $mode['core'];
		foreach ($list as $file){
			if(is_file($file))  {
				require_cache($file);
				if(!APP_DEBUG)   $compile .= compile($file);
			}
		}
		//取模式或项目公共文件
		if(is_file(MODE_PATH.MODE_NAME.'/common.php')){
			$common=MODE_PATH.MODE_NAME.'/common.php';
		}
		elseif(is_file(COMMON_PATH.'common.php')) {
			$common=COMMON_PATH.'common.php';
		}
		if($common){
			include $common;
			if(!APP_DEBUG)  $compile   .= compile($common);
		}
		// 加载模式别名定义
		if(isset($mode['alias'])) {
			$alias = is_array($mode['alias'])?$mode['alias']:include $mode['alias'];
			Jetee::addMap($alias);
			if(!APP_DEBUG) $compile .= 'Jetee::addMap('.var_export($alias,true).');';               
		}
		// 加载项目别名定义
		if(is_file(CONF_PATH.'alias.php')){ 
			$alias = include CONF_PATH.'alias.php';
			Jetee::addMap($alias);
			if(!APP_DEBUG) $compile .= 'Jetee::addMap('.var_export($alias,true).');';
		}

		if(APP_DEBUG) {
			// 调试模式加载系统默认的配置文件
			C(include JETEE_PATH.'Conf/debug.php');
			// 加载对应的项目配置文件
			if(is_file(CONF_PATH.'debug.php'))
				// 允许项目增加开发模式配置定义
				C(include CONF_PATH.'debug.php');
		}else{
			// 部署模式下面生成编译文件
			build_runtime_cache($compile);
		}
		return ;
	}
	//[/RUNTIME]

	// 注册classmap
	static public function addMap($class, $map=''){
		if(is_array($class)){
			self::$_map = array_merge(self::$_map, $class);
		}else{
			self::$_map[$class] = $map;
		}        
	}

	// 获取classmap
	static public function getMap($class=''){
		if(''===$class){
			return self::$_map;
		}elseif(isset(self::$_map[$class])){
			return self::$_map[$class];
		}else{
			return null;
		}
	}
	/**
	 * 系统自动加载框架类库
	 * 并且支持配置自动加载路径
	 * @param string $class 对象类名
	 * @return void
	 */
	public static function autoload($class) {
		// 检查是否存在映射
		if(isset(self::$_map[$class])) {
			include self::$_map[$class];
			return;
		}
		$group      =   defined('GROUP_NAME') ?GROUP_NAME.'/':'';
		$file       =   $class.'.class.php';
		if(substr($class,-8)=='Behavior') { // 加载行为
			if(require_array(array(
				CORE_PATH.'Behavior/'.$file,
				LIB_PATH.'Behavior/'.$file),true) || require_cache(MODE_PATH.MODE_NAME.'/Behavior/'.$file)) {
				return ;
			}
		}elseif(substr($class,-5)=='Model'){ // 加载模型
			if(require_array(array(
				LIB_PATH.'Model/'.$file,
				LIB_PATH.'Model/'.$group.$file),true)) {
				return ;
			}
		}elseif(substr($class,-6)=='Action'){ // 加载控制器
			if(require_array(array(
				LIB_PATH.'Action/'.$group.$file,
				LIB_PATH.'Action/'.$file),true)) {
				return ;
			}
		}elseif(substr($class,0,5)=='Cache'){ // 加载缓存驱动
			if(require_cache(CORE_PATH.'Driver/Cache/'.$file)){
				return ;
			}
		}elseif(substr($class,0,7)=='Session'){ // 加载Session驱动
			if(require_cache(CORE_PATH.'Driver/Session/'.$file)){
				return ;
			}
		}elseif(substr($class,0,2)=='Db'){ // 加载数据库驱动
			if(require_cache(CORE_PATH.'Driver/Db/'.$file)){
				return ;
			}
		}elseif(substr($class,0,8)=='Template'){ // 加载模板引擎驱动
			if(require_cache(CORE_PATH.'Template/'.$file)){
				return ;
			}
		}elseif(substr($class,0,6)=='TagLib'){ // 加载标签库驱动
			if(require_cache(CORE_PATH.'Driver/TagLib/'.$file)) {
				return ;
			}
		}
		if(import('@.Vendor.'.$class))
			return ;
	}

	/**
	 * 取得对象实例 支持调用类的静态方法
	 * @param string $class 对象类名
	 * @param string $method 类的静态方法名
	 * @return object
	 */
	static public function instance($class,$method='') {
		$identify   =   $class.$method;
		if(!isset(self::$_instance[$identify])) {
			if(class_exists($class)){
				$o = new $class();
				if(!empty($method) && method_exists($o,$method))
					self::$_instance[$identify] = call_user_func_array(array(&$o, $method));
				else
					self::$_instance[$identify] = $o;
			}
			else
				halt(L('_CLASS_NOT_EXIST_').':'.$class);
		}
		return self::$_instance[$identify];
	}

	/**
	 * 自定义异常处理
	 * @access public
	 * @param mixed $e 异常对象
	 */
	static public function appException($e) {
		$error = array();
		$error['message']   = $e->getMessage();
		$trace  =   $e->getTrace();
		if('throw_exception'==$trace[0]['function']) {
			$error['file']  =   $trace[0]['file'];
			$error['line']  =   $trace[0]['line'];
		}else{
			$error['file']      = $e->getFile();
			$error['line']      = $e->getLine();
		}
		Log::record($error['message'],Log::ERR);
		halt($error);
	}

	/**
	 * 自定义错误处理
	 * @access public
	 * @param int $errno 错误类型
	 * @param string $errstr 错误信息
	 * @param string $errfile 错误文件
	 * @param int $errline 错误行数
	 * @return void
	 */
	static public function appError($errno, $errstr, $errfile, $errline) {
	  switch ($errno) {
		  case E_ERROR:
		  case E_PARSE:
		  case E_CORE_ERROR:
		  case E_COMPILE_ERROR:
		  case E_USER_ERROR:
			ob_end_clean();
			// 页面压缩输出支持
			if(C('OUTPUT_ENCODE')){
				$zlib = ini_get('zlib.output_compression');
				if(empty($zlib)) ob_start('ob_gzhandler');
			}
			$errorStr = "$errstr ".$errfile." 第 $errline 行.";
			if(C('LOG_RECORD')) Log::write("[$errno] ".$errorStr,Log::ERR);
			function_exists('halt')?halt($errorStr):exit('ERROR:'.$errorStr);
			break;
		  case E_STRICT:
		  case E_USER_WARNING:
		  case E_USER_NOTICE:
		  default:
			$errorStr = "[$errno] $errstr ".$errfile." 第 $errline 行.";
			trace($errorStr,'','NOTIC');
			break;
	  }
	}
	
	// 致命错误捕获  PHP执行完成或意外死掉后
	static public function fatalError() {
		// 保存日志记录
		if(C('LOG_RECORD')) Log::save();
		if ($e = error_get_last()) {
			switch($e['type']){
			  case E_ERROR:
			  case E_PARSE:
			  case E_CORE_ERROR:
			  case E_COMPILE_ERROR:
			  case E_USER_ERROR:  
				ob_end_clean();
				function_exists('halt')?halt($e):exit('ERROR:'.$e['message']. ' in <b>'.$e['file'].'</b> on line <b>'.$e['line'].'</b>');
				break;
			}
		}
	}

}

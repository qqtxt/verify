<?php
defined('JETEE_PATH') or exit();

/**
 * Jetee模型专用
 * 可以在模式扩展中重新定义 但是必须具有Run方法接口
 * @category   Think
 * @package  Think
 * @subpackage  Core
 * @author    liu21st <liu21st@gmail.com>
 */
class App {

	/**
	 * 应用程序初始化
	 * @access public
	 * @return void
	 */
	static public function init() {
		// 页面压缩输出支持
		if(C('OUTPUT_ENCODE')){
			$zlib = ini_get('zlib.output_compression');
			if(empty($zlib)){// ob_start('ob_gzhandler');
				ini_set('zlib.output_compression','on');
				ini_set('zlib.output_compression_level',3);
			}
		}
		// 设置系统时区
		date_default_timezone_set(C('DEFAULT_TIMEZONE'));
		// URL调度
		Dispatcher::dispatch();
		// 定义当前请求的系统常量
		define('NOW_TIME',      $_SERVER['REQUEST_TIME']);
		define('REQUEST_METHOD',$_SERVER['REQUEST_METHOD']);
		define('IS_GET',        REQUEST_METHOD =='GET' ? true : false);
		define('IS_POST',       REQUEST_METHOD =='POST' ? true : false);
		define('IS_PUT',        REQUEST_METHOD =='PUT' ? true : false);
		define('IS_DELETE',     REQUEST_METHOD =='DELETE' ? true : false);
		define('IS_AJAX',       ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || !empty($_POST[C('VAR_AJAX_SUBMIT')]) || !empty($_GET[C('VAR_AJAX_SUBMIT')])) ? true : false);
		// 系统变量安全过滤
		if(C('VAR_FILTERS')) {
			$filters    =   explode(',',C('VAR_FILTERS'));
			foreach($filters as $filter){
				// 全局参数过滤
				array_walk_recursive($_POST,$filter);
				array_walk_recursive($_GET,$filter);
			}
		}

		//动态配置 TMPL_EXCEPTION_FILE,改为绝对地址
		C('TMPL_EXCEPTION_FILE',realpath(C('TMPL_EXCEPTION_FILE')));
		return ;
	}

	/**
	 * 执行应用程序
	 * @access public
	 * @return void
	 */
	static public function exec() {
		if(!preg_match('/^[A-Za-z](\w)*$/',MODULE_NAME)){ // 安全检测
			$module  =  false;
		}else{
			//创建Action控制器实例
			$group   =  defined('GROUP_NAME') ? GROUP_NAME.'/' : '';
			$module  =  A($group.MODULE_NAME);
		}
		if(!$module) {
			if('4e5e5d7364f443e28fbf0d3ae744a59a' == MODULE_NAME) {
				header("Content-type:image/png");
				exit(base64_decode(App::logo()));
			}
			if(function_exists('__hack_module')) {
				// hack 方式定义扩展模块 返回Action对象
				$module = __hack_module();
				if(!is_object($module)) {
					// 不再继续执行 直接返回
					return ;
				}
			}else{
				// 是否定义Empty模块
				$module = A($group.'Empty');
				if(!$module){
					_404(L('_MODULE_NOT_EXIST_').':'.MODULE_NAME);
				}
			}
		}
		// 获取当前操作名 支持动态路由
		$action = C('ACTION_NAME')?C('ACTION_NAME'):ACTION_NAME;
		$action .=  C('ACTION_SUFFIX');
		try{
			if(!preg_match('/^[A-Za-z](\w)*$/',$action)){// 安全检测
				// 非法操作
				throw new ReflectionException();
			}
			//执行当前操作
			$method =   new ReflectionMethod($module, $action);
			if($method->isPublic()) {
				$class  =   new ReflectionClass($module);
				// 前置操作
				if($class->hasMethod('_before_'.$action)) {
					$before =   $class->getMethod('_before_'.$action);
					if($before->isPublic()) {
						$before->invoke($module);
					}
				}
				// URL参数绑定检测
				if(C('URL_PARAMS_BIND') && $method->getNumberOfParameters()>0){
					switch($_SERVER['REQUEST_METHOD']) {
						case 'POST':
							$vars    =  array_merge($_GET,$_POST);
							break;
						case 'PUT':
							parse_str(file_get_contents('php://input'), $vars);
							break;
						default:
							$vars  =  $_GET;
					}
					$params =  $method->getParameters();
					foreach ($params as $param){
						$name = $param->getName();
						if(isset($vars[$name])) {
							$args[] =  $vars[$name];
						}elseif($param->isDefaultValueAvailable()){
							$args[] = $param->getDefaultValue();
						}else{
							throw_exception(L('_PARAM_ERROR_').':'.$name);
						}
					}
					array_walk_recursive($args,'think_filter');
					$method->invokeArgs($module,$args);
				}else{
					$method->invoke($module);
				}
				// 后置操作
				if($class->hasMethod('_after_'.$action)) {
					$after =   $class->getMethod('_after_'.$action);
					if($after->isPublic()) {
						$after->invoke($module);
					}
				}
			}else{
				// 操作方法不是Public 抛出异常
				throw new ReflectionException();
			}
		} catch (ReflectionException $e) {
			// 方法调用发生异常后 引导到__call方法处理
			$method = new ReflectionMethod($module,'__call');
			$method->invokeArgs($module,array($action,''));
		}
		return ;
	}


	/**
	 * 运行应用实例 入口文件使用的快捷方法
	 * @access public
	 * @return void
	 */
	static public function run() {
		// 项目初始化标签
		tag('app_init');
		App::init();

		// 项目开始标签  这里读静态缓冲
		tag('app_begin');

		// Session初始化
		session(C('SESSION_OPTIONS'));//g('j_session');echo c('DB_TYPE');echo g('j_session','j_jsadflasdf',6);exit;
		//g('j_sessionsssss',$GLOBALS['_beginTime']);echo g('j_sessionsssss','j_jsadflasdf',6);exit;
		// 记录应用初始化时间
		G('initTime');
		App::exec();
		// 项目结束标签
		tag('app_end');
		return ;
	}

	static public function logo(){
		return 'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6REZERDA2QTM4QURBMTFFNzg5QTBDNDNFOTA4QTBGODEiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6REZERDA2QTQ4QURBMTFFNzg5QTBDNDNFOTA4QTBGODEiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpERkREMDZBMThBREExMUU3ODlBMEM0M0U5MDhBMEY4MSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpERkREMDZBMjhBREExMUU3ODlBMEM0M0U5MDhBMEY4MSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PkXQxMwAAAKCSURBVHjadFO/TxRBFP7mzezeCQREiCFgMBqtjZUWmlhoo5VKQWJj469EJbHBSgqNjQWGxH9ALUw0kUIbK8XYaIPEYDwTQxQ8jegFjuO43Z0Zv72V41DcZLIzb9773ve+N08de/od/UEMJwId5KB9gkezCYorKkC+5RaU2opa9UJvaBdO9IV1P+89lEuwYDUMmj6l1DYB5jYH8MXYnISLh+A8IGYJcGcBn/r0E+Dzaoyo1WCRwzxNiTZ32gytSg/COaRBXIe6QxFj9AgRpggyuBoszAStNP2DSzaKOq11A9151Qlv+xrUvDNtofST+5CNah20XNEMLicEKKxoxMyileqq+3ofhEq1cuuaqrNbcqqFhlwG6Fssq3m1KJByInhd1hQvnlCpkCLFSmy/0S1YY6Dyc8tulnfTSgtCweR0RfyPmoJA4chkJTAfKv56ey4YpWZn3i2hlaLsaAAIej4t++0riT3dFpixQjm5/KJkUtX3KdydJxn9PKdxbn+7LRSrDoWqHoJPbtf1a4CY8Z2b/PFdLOTlgnRUnRmDSwYU7pcmYKODkOAXnLrB0qvMOAqb5JtbDJYHmJsU9z20voYk3s3/jMK9UhfLGEdcOwDRpJXKZ5Ft/vqUpLR5T/l18JX+R2nxP4l6noeI7ULW+w2Cs3ZmwUrSFzXM81upPxRrP/L6ywYRf9Y6GqmphMg+AXsprCXNGhFx/h+6OqxwLWb1r8NYhnBoyCadjKZ0PmNvghloc5EU9xJ8DxU8hSB8U69/jVG9TtM0SRFbRV/9kCcOkS02ZkHpGSZ6TOFGaB+mjeOWiuEJoBqCjTBrD4Me/EfBKkGuwphnnNBeJLVqyuG3AAMALvMPjmh26zcAAAAASUVORK5CYII=';
	}
}

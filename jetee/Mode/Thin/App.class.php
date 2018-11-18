<?php
defined('JETEE_PATH') or exit();
/**
 * ThinkPHP 精简模式应用程序类
 */
class App {

	/**
	 * 应用程序初始化
	 * @access public
	 * @return void
	 */
	static public function run() {
		// 页面压缩输出支持
		if(C('OUTPUT_ENCODE')){
			$zlib = ini_get('zlib.output_compression');
			if(empty($zlib)){// ob_start('ob_gzhandler');
				ini_set('zlib.output_compression','on');
				ini_set('zlib.output_compression_level',3);
			}
		}
		define('NOW_TIME',      $_SERVER['REQUEST_TIME']);
		define('IS_AJAX',       ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || !empty($_POST[C('VAR_AJAX_SUBMIT')]) || !empty($_GET[C('VAR_AJAX_SUBMIT')])) ? true : false);
		define('__SELF__',strip_tags($_SERVER['REQUEST_URI']));
		define('__APP__',strip_tags(dirname(_PHP_FILE_)));
		// 设置系统时区
		date_default_timezone_set(C('DEFAULT_TIMEZONE'));
		// 取得模块和操作名称
		define('MODULE_NAME',   App::getModule());       // Module名称
		define('ACTION_NAME',   App::getAction());        // Action操作
		if(!preg_match('/^[A-Za-z](\w)*$/',MODULE_NAME) || !preg_match('/^[A-Za-z](\w)*$/',ACTION_NAME)){ // 安全检测
			die('非法访问！');
		}		
		$allow=array('Light','Wap','Pc','Cron');
		//允许的模型
		if(!in_array(MODULE_NAME, $allow))
			die('非法访问！');
		/*
        ob_start();
        ob_implicit_flush(0);
		ob_end_flush();//echo ob_get_clean();  */
		
		// 记录应用初始化时间
		G('initTime');
		// 执行操作
		R(MODULE_NAME.'/'.ACTION_NAME);		
		// 保存日志记录
		if(C('LOG_RECORD'))
			Log::save();
		
		if(C('SHOW_PAGE_TRACE'))
			tag('app_end');
		
		return ;
	}

	/**
	 * 获得实际的模块名称
	 * @access private
	 * @return string
	 */
	static private function getModule() {
		$var  =  C('VAR_MODULE');
		$module = !empty($_POST[$var]) ?
			$_POST[$var] :
			(!empty($_GET[$var])? $_GET[$var]:C('DEFAULT_MODULE'));
		if(C('URL_CASE_INSENSITIVE')) {
			// URL地址不区分大小写
			define('P_MODULE_NAME',strtolower($module));
			// 智能识别方式 index.php/user_type/index/ 识别到 UserTypeAction 模块
			//$module = ucfirst(parse_name(strtolower($module),1));
			$module = ucfirst(strtolower($module));
		}
		unset($_POST[$var],$_GET[$var]);
		return $module;
	}

	/**
	 * 获得实际的操作名称
	 * @access private
	 * @return string
	 */
	static private function getAction() {
		$var  =  C('VAR_ACTION');
		$action   = !empty($_POST[$var]) ?
			$_POST[$var] :
			(!empty($_GET[$var])?$_GET[$var]:C('DEFAULT_ACTION'));
		unset($_POST[$var],$_GET[$var]);
		return $action;
	}
	static public function logo(){
		return 'iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6REZERDA2QTM4QURBMTFFNzg5QTBDNDNFOTA4QTBGODEiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6REZERDA2QTQ4QURBMTFFNzg5QTBDNDNFOTA4QTBGODEiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpERkREMDZBMThBREExMUU3ODlBMEM0M0U5MDhBMEY4MSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpERkREMDZBMjhBREExMUU3ODlBMEM0M0U5MDhBMEY4MSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PkXQxMwAAAKCSURBVHjadFO/TxRBFP7mzezeCQREiCFgMBqtjZUWmlhoo5VKQWJj469EJbHBSgqNjQWGxH9ALUw0kUIbK8XYaIPEYDwTQxQ8jegFjuO43Z0Zv72V41DcZLIzb9773ve+N08de/od/UEMJwId5KB9gkezCYorKkC+5RaU2opa9UJvaBdO9IV1P+89lEuwYDUMmj6l1DYB5jYH8MXYnISLh+A8IGYJcGcBn/r0E+Dzaoyo1WCRwzxNiTZ32gytSg/COaRBXIe6QxFj9AgRpggyuBoszAStNP2DSzaKOq11A9151Qlv+xrUvDNtofST+5CNah20XNEMLicEKKxoxMyileqq+3ofhEq1cuuaqrNbcqqFhlwG6Fssq3m1KJByInhd1hQvnlCpkCLFSmy/0S1YY6Dyc8tulnfTSgtCweR0RfyPmoJA4chkJTAfKv56ey4YpWZn3i2hlaLsaAAIej4t++0riT3dFpixQjm5/KJkUtX3KdydJxn9PKdxbn+7LRSrDoWqHoJPbtf1a4CY8Z2b/PFdLOTlgnRUnRmDSwYU7pcmYKODkOAXnLrB0qvMOAqb5JtbDJYHmJsU9z20voYk3s3/jMK9UhfLGEdcOwDRpJXKZ5Ft/vqUpLR5T/l18JX+R2nxP4l6noeI7ULW+w2Cs3ZmwUrSFzXM81upPxRrP/L6ywYRf9Y6GqmphMg+AXsprCXNGhFx/h+6OqxwLWb1r8NYhnBoyCadjKZ0PmNvghloc5EU9xJ8DxU8hSB8U69/jVG9TtM0SRFbRV/9kCcOkS02ZkHpGSZ6TOFGaB+mjeOWiuEJoBqCjTBrD4Me/EfBKkGuwphnnNBeJLVqyuG3AAMALvMPjmh26zcAAAAASUVORK5CYII=';
	}
};
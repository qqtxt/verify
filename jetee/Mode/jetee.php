<?php
defined('JETEE_PATH') or exit();
// 定制核心定义文件列表
return array(

    'core'         =>   array(
		JETEE_PATH.'Common/functions.php', // 标准模式函数库
		CORE_PATH.'Core/Dispatcher.class.php', // URL调度类
		CORE_PATH.'Core/App.class.php',   // 应用程序类
		CORE_PATH.'Core/Action.class.php', // 控制器类
		CORE_PATH.'Core/View.class.php',  // 视图类
		CORE_PATH.'Core/Log.class.php',
    ),
    // 项目别名定义文件 [支持数组直接定义或者文件名定义]
    'alias'         =>  array(
		'Model'         => CORE_PATH.'Core/Model.class.php',
		'Db'            => CORE_PATH.'Core/Db.class.php',
		'Log'          	=> CORE_PATH.'Core/Log.class.php',
		'Template'	    => CORE_PATH.'Template/Template.class.php',
		'TagLib'        => CORE_PATH.'Template/TagLib.class.php',
		'Cache'         => CORE_PATH.'Core/Cache.class.php',
		'Widget'        => CORE_PATH.'Core/Widget.class.php',
		'TagLibCx'      => CORE_PATH.'Driver/TagLib/TagLibCx.class.php',
	), 

    // 系统行为定义文件 [必须 支持数组直接定义或者文件名定义 ]
    'extends'       =>  array(
		'app_init'      =>  array(),
		'app_begin'     =>  array(
			'ReadHtmlCache', // 读取静态缓存
		),
		// 路由检测 'route_check'   =>  array('CheckRoute'), 
		'app_end'       =>  array(),
		'path_info'     =>  array(),
		'action_begin'  =>  array(),
		'action_end'    =>  array(),
		'view_begin'    =>  array(),
		'view_parse'    =>  array(
			'ParseTemplate', // 模板解析 支持PHP、内置模板引擎和第三方模板引擎
		),
		'view_filter'   =>  array(
			'ContentReplace', // 模板输出替换
			'TokenBuild',   // 表单令牌
			'WriteHtmlCache', // 写入静态缓存
			'ShowRuntime', // 运行时间显示
		),
		'view_end'      =>  array(
			'ShowPageTrace', // 页面Trace显示
		),
	), 

    // 项目应用行为定义文件 [支持数组直接定义或者文件名定义]
    'tags'          =>array(),

);
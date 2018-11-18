<?php
defined('JETEE_PATH') or exit();
// 简洁模式核心定义文件列表
return array(

	'core'         =>   array(
		JETEE_PATH.'Common/functions.php', // 标准模式函数库
		CORE_PATH.'Core/Log.class.php',    // 日志处理类
		MODE_PATH.'Thin/App.class.php', // 应用程序类
		MODE_PATH.'Thin/Action.class.php',// 控制器类
	),

	// 项目别名定义文件 [支持数组直接定义或者文件名定义]
	'alias'         =>  array(
		'Model'         => CORE_PATH.'Core/Model.class.php',
		'Db'            => CORE_PATH.'Core/Db.class.php',
		'Log'          	=> CORE_PATH.'Core/Log.class.php',
		'Cache'         => CORE_PATH.'Core/Cache.class.php',
	), 

	// 系统行为定义文件 [必须 支持数组直接定义或者文件名定义 ]
    'extends'       =>  array(
		'app_end'      =>  array(
			'ShowPageTrace', // 页面Trace显示
		),
	), 

	// 项目应用行为定义文件 [支持数组直接定义或者文件名定义]
	'tags'          =>  array(), 

);
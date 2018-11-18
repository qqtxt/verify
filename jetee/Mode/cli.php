<?php
// 命令行模式定义文件
return array(
    'core'          =>   array(
        MODE_PATH.'Cli/functions.php',   // 命令行系统函数库
        CORE_PATH.'Core/Log.class.php',
        MODE_PATH.'Cli/App.class.php',
        MODE_PATH.'Cli/Action.class.php',
    ),

    // 项目别名定义文件 [支持数组直接定义或者文件名定义]
    'alias'         =>   array(
        'Model'     =>   CORE_PATH.'Core/Model.class.php',
        'Db'        =>   CORE_PATH.'Core/Db.class.php',
        'Cache'     =>   CORE_PATH.'Core/Cache.class.php',
        'Debug'     =>   CORE_PATH.'Util/Debug.class.php',
    ), 
    // 系统行为定义文件 [必须 支持数组直接定义或者文件名定义 ]
    'extends'       =>  array(), 
);
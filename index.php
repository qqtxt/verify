<?php
define('APP_DEBUG', true);//开启调试模式
define('HIDE_DIR','.');//可以把项目放入站根目录之外   根目录只要Public与Uploads
define('APP_NAME', 'Home');//定义项目
define('APP_PATH', HIDE_DIR.'/Home/');//加载框架入文件
define('UPLOADS_PATH', 	'./Uploads/');//上传目录
define('PUBLIC_PATH',	'./Public/'); 
define('UPLOADS_URL',	'Uploads/');
define('PUBLIC_URL',	'Public/');
//检查计划任务是否已执行
if(isset($_REQUEST['trigger'])&& $_REQUEST['trigger']=='1981' || PHP_SAPI=='cli' && $_SERVER['argv'][1]=='trigger/1981'){//cd E:\w\work18\shua.net\ php index.php trigger/1981      ?trigger=1981
	 trigger_cron();
}
if(isset($_REQUEST['thin'])&&$_REQUEST['thin']==200){//精简模式 求速度 验证码
	define('MODE_NAME', 'Thin');
}elseif(PHP_SAPI=='cli'){
	define('MODE_NAME', 'Cli');//命令行界面模式  php index.php Im start -d进入的是daemon守护进程模式，终端关闭不会影响Workerman。
}

require './jetee/Jetee_runtime.php';
#require APP_PATH.'Runtime/~'.MODE_NAME.'_runtime.php';

































function trigger_cron(){
	if(defined('APP_DEBUG')&& APP_DEBUG) echo "1\n";
	$path=APP_PATH.'Runtime/Data/';
	$file='cron_check.txt';
	if(!file_exists($path.$file)){
		if (!is_dir($path)){
			mkdir($path,0755,true);
		}
		file_put_contents($path.$file,'0');
	}
	$sleep=10+50;//程序睡眠时间+超时时间
	//检查
	$check=intval(file_get_contents($path.$file));
	$now=time();
	if(defined('APP_DEBUG')&& APP_DEBUG) echo "2\n";

	//现在时间 减记录时间 超过指定分钟以上 说明任务中断了   开启
	if($now-$check > $sleep){
		if(PHP_SAPI=='cli'){
			$_SERVER['argv'][1]='Cron/index';
		}else{//浏览器访问thin
			$_POST['m']='Cron';
			$_POST['a']='index';
			$_REQUEST['thin']=200;
			ignore_user_abort(true);
			set_time_limit(0);
		}
		if(defined('APP_DEBUG')&& APP_DEBUG) echo "3\n";

	}else{
		exit;
	}
}
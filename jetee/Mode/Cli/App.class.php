<?php
defined('JETEE_PATH') or exit();
/**
 * 命令模式应用程序类
 */
class App {
    /**
     * 执行应用程序
     * @access public
     * @return void
     */
    static public function run() {
		define('NOW_TIME',      $_SERVER['REQUEST_TIME']);
        date_default_timezone_set(C('DEFAULT_TIMEZONE'));
        if(C('URL_MODEL')==2) {//重写模式URL下面 采用 index.php module/action/id/4
            $depr = C('URL_PATHINFO_DEPR');
            $path   = isset($_SERVER['argv'][1])?$_SERVER['argv'][1]:'';
            if(!empty($path)) {
                $params = explode($depr,trim($path,$depr));
            }
            // 取得模块和操作名称 index.php module/action/id/4
            define('MODULE_NAME',   !empty($params)?array_shift($params):C('DEFAULT_MODULE'));
            define('ACTION_NAME',  !empty($params)?array_shift($params):C('DEFAULT_ACTION'));
            if(count($params)>1) {
                // 解析剩余参数 并采用GET方式获取
				for($i=0;$i<count($params);$i=$i+2)	$_GET[$params[$i]]=$params[$i+1];
            }
        }else{// 默认URL模式 采用 index.php module action id 4
            // 取得模块和操作名称
            define('MODULE_NAME',   isset($_SERVER['argv'][1])?$_SERVER['argv'][1]:C('DEFAULT_MODULE'));
            define('ACTION_NAME',    isset($_SERVER['argv'][2])?$_SERVER['argv'][2]:C('DEFAULT_ACTION'));
            if($_SERVER['argc']>3) {
				$params=array_slice($_SERVER['argv'],3);
				for($i=0;$i<count($params);$i=$i+2)	$_GET[$params[$i]]=$params[$i+1];
            }
        }
        // 执行操作
        $module  =  A(MODULE_NAME);
        if(!$module) {
            // 是否定义Empty模块
            $module = A("Empty");
            if(!$module){
                // 模块不存在 抛出异常
                throw_exception(L('_MODULE_NOT_EXIST_').MODULE_NAME);
            }
        }
        call_user_func(array(&$module,ACTION_NAME));
        // 保存日志记录
        // if(C('LOG_RECORD')) Log::save();
		// if(C('SHOW_PAGE_TRACE'))	tag('app_end');

        return ;
    }

};
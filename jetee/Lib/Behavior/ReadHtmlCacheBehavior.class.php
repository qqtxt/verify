<?php
defined('JETEE_PATH') or exit();
/**
 * 系统行为扩展：静态缓存读取
 * @category   Think
 * @package  Think
 * @subpackage  Behavior
 * @author   liu21st <liu21st@gmail.com>
 */
class ReadHtmlCacheBehavior extends Behavior {
    protected $options   =  array(
            'HTML_CACHE_ON'     =>  false,
            'HTML_CACHE_TIME'   =>  60,
            'HTML_CACHE_RULES'  =>  array(),
            'HTML_FILE_SUFFIX'  =>  '.html',
        );

    // 行为扩展的执行入口必须是run
    public function run(&$params){
        // 开启静态缓存
        if(C('HTML_CACHE_ON'))  {
            $cacheTime = $this->requireHtmlCache();
			//header('Last-Modified: '.gmdate('r') . " GMT");
            if( false !== $cacheTime && $this->checkHTMLCache(HTML_FILE_NAME,$cacheTime)) { //静态页面有效
				if(is_numeric($cacheTime)){//是数字 不是函数
					header('Last-Modified: '.gmdate('r',CACHEFILE_FILEMTIME) . " GMT");
					header('ETag:'.$cacheTime);//缓冲时间  还没考虑cacheTime是函数的情况
				}
				#if(C('TOKEN_ON')){session(C('SESSION_OPTIONS'));buildToken();		}
                // 读取静态页面输出
               readfile(HTML_FILE_NAME);//$a='';b('ShowRuntime',$a);echo $a;g('j_sessionsssss',$GLOBALS['_beginTime']);echo g('j_sessionsssss','j_jsadflasdf',6);exit;
               exit();
            }
        }
    }

    // 判断是否需要静态缓存
    static private function requireHtmlCache() {
        // 分析当前的静态规则
         $htmls = C('HTML_CACHE_RULES'); // 读取静态规则
         if(!empty($htmls)) {
            $htmls = array_change_key_case($htmls);
            // 静态规则文件定义格式 actionName=>array('静态规则','缓存时间','附加规则')
            // 'read'=>array('{id},{name}',60,'md5') 必须保证静态规则的唯一性 和 可判断性
            // 检测静态规则
            $moduleName = strtolower(MODULE_NAME);
            $actionName = strtolower(ACTION_NAME);
            if(isset($htmls[$moduleName.':'.$actionName])) {
                $html   =   $htmls[$moduleName.':'.$actionName];   // 某个模块的操作的静态规则
            }elseif(isset($htmls[$moduleName.':'])){// 某个模块的静态规则
                $html   =   $htmls[$moduleName.':'];
            }elseif(isset($htmls[$actionName])){
                $html   =   $htmls[$actionName]; // 所有操作的静态规则
            }elseif(isset($htmls['*'])){
                $html   =   $htmls['*']; // 全局静态规则
            }elseif(isset($htmls['empty:index']) && !class_exists(MODULE_NAME.'Action')){
                $html   =    $htmls['empty:index']; // 空模块静态规则
            }elseif(isset($htmls[$moduleName.':_empty']) && self::isEmptyAction(MODULE_NAME,ACTION_NAME)){
                $html   =    $htmls[$moduleName.':_empty']; // 空操作静态规则
            }
            if(!empty($html)) {
                // 解读静态规则
                $rule   = $html[0];
                // 以$_开头的系统变量 
				$rule   = preg_replace_callback('/{\$(_\w+)\.(\w+)\|(\w+)}/',  function($m){return $m[3](${$m[1]}[$m[2]]);}, $rule);//{$_GET.id|md5}=md5($_GET['id'])
				$rule   = preg_replace_callback('/{\$(_\w+)\.(\w+)}/',  function($m){return ${$m[1]}[$m[2]];}, $rule);//{$_GET.id}=$_GET['id']
                // {ID|FUN} GET变量的简写
				$rule   = preg_replace_callback('/{(\w+)\|(\w+)}/',  function($m){return $m[2]($_GET[$m[1]]);}, $rule);//{id|md5}=md5($_GET['id'])
 				$rule   = preg_replace_callback('/{(\w+)}/',  function($m){return $_GET[$m[1]];}, $rule);//{id}=$_GET['id']
               // 特殊系统变量
                $rule   = str_ireplace(
                    array('{:app}','{:module}','{:action}','{:group}'),
                    array(APP_NAME,MODULE_NAME,ACTION_NAME,defined('GROUP_NAME')?GROUP_NAME:''),
                    $rule);
                // {|FUN} 单独使用函数 preg_replace
 				$rule   = preg_replace_callback('/{|(\w+)}/',  function($m){return $m[1]();}, $rule);//{|function}=function()
                if(!empty($html[2])) $rule    =   $html[2]($rule); // 应用附加函数
                $cacheTime = isset($html[1])?$html[1]:C('HTML_CACHE_TIME'); // 缓存有效期
                // 当前缓存文件
                define('HTML_FILE_NAME',HTML_PATH . $rule.C('HTML_FILE_SUFFIX'));
                return $cacheTime;
            }
        }
        // 无需缓存
        return false;
    }

    /**
     * 检查静态HTML文件是否有效
     * 如果无效需要重新更新
     * @access public
     * @param string $cacheFile  静态文件名
     * @param integer $cacheTime  缓存有效期
     * @return boolean
     */
    static public function checkHTMLCache($cacheFile='',$cacheTime='') {
        if(!is_file($cacheFile)){
            return false;
        }elseif (define('CACHEFILE_FILEMTIME',filemtime($cacheFile)) && filemtime(C('TEMPLATE_NAME')) > CACHEFILE_FILEMTIME) {//默认模板路径
            // 模板文件如果更新静态文件需要更新
            return false;
        }elseif(!is_numeric($cacheTime) && function_exists($cacheTime)){
            return $cacheTime($cacheFile);
        }elseif ($cacheTime != 0 && NOW_TIME > CACHEFILE_FILEMTIME+$cacheTime) {
            // 文件是否在有效期
            return false;
        }
        //静态文件有效
        return true;
    }

    //检测是否是空操作
    static private function isEmptyAction($module,$action) {
        $className  =   $module.'Action';
        $class      =   new $className;
        return !method_exists($class,$action);
    }

}
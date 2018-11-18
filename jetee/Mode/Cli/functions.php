<?php
defined('JETEE_PATH') or exit();
/**
 * Think 命令行模式公共函数库
 * @category   Think
 * @package  Common
 * @author   liu21st <liu21st@gmail.com>
 */

// 错误输出
function halt($error) {
	Log::write(print_r($error,true));
    //exit(mb_convert_encoding($error, 'GBK', 'UTF-8')); 
}

// 自定义异常处理
function throw_exception($msg, $type='ThinkException', $code=0) {
    halt($msg);
}

function initCache($name='',$options=null){
	static $cache   =   '';
	if(is_array($name)) { // 缓存初始化
		$type       =   isset($name['type'])?$name['type']:'';
		$cache      =   Cache::getInstance($type,$name);
	}elseif(empty($cache)) { // 自动初始化
		if(is_array($options)){
			$type       =   isset($options['type'])?$options['type']:'';
			$cache      =   Cache::getInstance($type,$options);
		}else $cache      =   Cache::getInstance();
	}
	return $cache;
}
/**
 * 缓存管理
 * @param mixed $name 缓存名称，如果为数组表示进行缓存设置
 * @param mixed $value 缓存值
 * @param mixed $options 缓存参数  如果是数字为过期时间秒
 * @return mixed  取值不存在 返回false
 */
function S($name,$value='',$options=null){
	$cache=initCache($name,$options);
	if(''=== $value){//获取缓存
		return $cache->g($name);
	}elseif(is_null($value)) {//删除缓存
		return $cache->rm($name);
	}else {//缓存数据
		if(is_array($options)) {
			$expire     =   isset($options['expire'])?$options['expire']:NULL;
		}else{
			$expire     =   is_int($options)?$options:NULL;
		}
		return $cache->s($name, $value, $expire);
	}
}
/**
 * 缓存数组管理
 * @param mixed $name 缓存名称，如果为数组表示进行缓存设置
 * @param mixed $arr 键值对
 * @param mixed $options 缓存参数  如果是数字为过期时间秒
 * @return mixed
 */
function hm($name,$arr='',$options=null) {
	$cache=initCache($name,$options);
	if(''=== $arr){ // 获取缓存
		return $cache->hGetAll($name);
	}elseif(is_null($arr)) { // 删除缓存
		return $cache->rm($name);
	}else{ //缓存数据
		if(is_array($options)) {
			$expire     =   isset($options['expire'])?$options['expire']:NULL;
		}else{
			$expire     =   is_int($options)?$options:NULL;
		}
		return $cache->hmSet($name, $arr, $expire);
	}
}
/**
 * 缓存数组键值管理
 * @param mixed $name 缓存名称，如果为数组表示进行缓存设置
 * @param mixed $key 键
 * @param mixed $val 值
 * @param mixed $options 缓存参数  如果是数字为过期时间秒
 * @return mixed
 */
function h($name,$key,$val='',$options=null) {
	$cache=initCache($name,$options);
	if(''=== $val){// 获取缓存
		return $cache->hGet($name,$key);
	}elseif(is_null($val)) { // 删除缓存
		return $cache->rm($name);
	}else{//缓存数据
		if(is_array($options)) {
			$expire     =   isset($options['expire'])?$options['expire']:NULL;
		}else{
			$expire     =   is_int($options)?$options:NULL;
		}
		return $cache->hSet($name, $key, $val, $expire);
	}
}
/**
 * 快速文件数据读取和保存 针对简单类型数据 字符串、数组
 * @param string $name 缓存名称
 * @param mixed $value 缓存值
 * @param string $path 缓存路径
 * @return mixed
 */
function F($name, $value='', $path=DATA_PATH) {
	static $_cache  = array();
	$filename       = $path . $name . '.php';
	if ('' !== $value) {
		if (is_null($value)) {
			// 删除缓存
			return false !== strpos($name,'*')?array_map("unlink", glob($filename)):unlink($filename);
		} else {
			// 缓存数据
			$dir            =   dirname($filename);
			// 目录不存在则创建
			if (!is_dir($dir))
				mkdir($dir,0755,true);
			$_cache[$name]  =   $value;
			$return=file_put_contents($filename, strip_whitespace("<?php\treturn " . var_export($value, true) . ";?>"));
			if(OPCACHE_ENABLE)
				opcache_invalidate($filename);
			return $return;
		}
	}
	if (isset($_cache[$name]))
		return $_cache[$name];
	// 获取缓存数据
	if (is_file($filename)) {
		$value          =   include $filename;
		$_cache[$name]  =   $value;
	} else {
		$value          =   false;
	}
	return $value;
}

/**
 * 取得对象实例 支持调用类的静态方法
 */
function get_instance_of($name, $method='', $args=array()) {
	static $_instance = array();
	$identify = empty($args) ? $name . $method : $name . $method . to_guid_string($args);
	if (!isset($_instance[$identify])) {
		if (class_exists($name)) {
			$o = new $name();
			if (method_exists($o, $method)) {
				if (!empty($args)) {
					$_instance[$identify] = call_user_func_array(array(&$o, $method), $args);
				} else {
					$_instance[$identify] = $o->$method();
				}
			}
			else
				$_instance[$identify] = $o;
		}
		else
			halt(L('_CLASS_NOT_EXIST_') . ':' . $name);
	}
	return $_instance[$identify];
}

/**
 * 根据PHP各种类型变量生成唯一标识号
 * @param mixed $mix 变量
 * @return string
 */
function to_guid_string($mix) {
	if (is_object($mix) && function_exists('spl_object_hash')) {
		return spl_object_hash($mix);
	} elseif (is_resource($mix)) {
		$mix = get_resource_type($mix) . strval($mix);
	} else {
		$mix = serialize($mix);
	}
	return md5($mix);
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @return mixed
 */
function get_client_ip($type = 0) {
	$type       =  $type ? 1 : 0;
	static $ip  =   NULL;
	if ($ip !== NULL) return $ip[$type];
	if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
		$pos    =   array_search('unknown',$arr);
		if(false !== $pos) unset($arr[$pos]);
		$ip     =   trim($arr[0]);
	}elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
		$ip     =   $_SERVER['HTTP_CLIENT_IP'];
	}elseif (isset($_SERVER['REMOTE_ADDR'])) {
		$ip     =   $_SERVER['REMOTE_ADDR'];
	}
	// IP地址合法验证
	$long = sprintf("%u",ip2long($ip));
	$ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
	return $ip[$type];
}
<?php
defined('JETEE_PATH') or exit();
	/*使用方法
	$handler  = new Redis;
	$handler->pconnect('127.0.0.1',6379);
	$handler->select(1);
	var_dump($handler->hMSet('my1',array('a'=>1)));	
	exit;


	$cache      =  new CacheRedis();
	var_dump($cache->hMSet('my1',array('ok'=>'qqtxt1','pass'=>19811,'qq'=>false,'aa'=>NULL)));
	var_dump($cache->hGetall('my1'));exit;*/

/**
 * Redis缓存驱动 
 * 要求安装phpredis扩展：https://github.com/nicolasff/phpredis
 * @category   Extend
 * @package  Extend
 * @subpackage  Driver.Cache
 * @author    尘缘 <130775@qq.com>
 */
class CacheRedis extends Cache {
	 /**
	 * 架构函数
     * @param array $options 缓存参数 
     * @access public
     */
    public function __construct($options=array()) {
        if ( !extension_loaded('redis') ) {
            throw_exception(L('_NOT_SUPPERT_').':redis');
        }
        if(empty($options)) {
            $options = array (
                'host'          => C('REDIS_HOST') ? C('REDIS_HOST') : '127.0.0.1',
                'port'          => C('REDIS_PORT') ? C('REDIS_PORT') : 6379,
                'timeout'       => C('DATA_CACHE_TIMEOUT') ? C('DATA_CACHE_TIMEOUT') : false,
                'persistent'    => true,
            );
        }
        $this->options =  $options;
        $this->options['expire'] =  isset($options['expire'])?  $options['expire']  :   C('DATA_CACHE_TIME');
        $this->options['prefix'] =  isset($options['prefix'])?  $options['prefix']  :   C('DATA_CACHE_PREFIX');        
        $this->options['length'] =  isset($options['length'])?  $options['length']  :   0;        
        $func = $options['persistent'] ? 'pconnect' : 'connect';
        $this->handler  = new Redis;
        $options['timeout'] === false ?
            $this->handler->$func($options['host'], $options['port']) :
            $this->handler->$func($options['host'], $options['port'], $options['timeout']);
		$this->handler->select(2);
    }

    /**
     * 通用读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @return mixed  无数据返回 false
     */
    public function get($name) {
        N('cache_read',1);
        $value = $this->handler->get($this->options['prefix'].$name);
        $jsonData  = json_decode( $value, true );
        return ($jsonData === NULL) ? $value : $jsonData;	//检测是否为JSON数据 true 返回JSON解析数组, false返回源数据
    }
    /**
     * 通用写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     * @param integer $expire  有效时间（秒） 必须为整形  <=0  直接过期
     * @return boolen
     */
    public function set($name, $value, $expire = null) {
        N('cache_write',1);
        $name   =   $this->options['prefix'].$name;
        //对数组/对象数据进行缓存处理，保证数据完整性
        $value  =  (is_object($value) || is_array($value)) ? json_encode($value) : $value;
        $result = $this->handler->set($name, $value);
		
        if(is_null($expire)) {
            $expire  =  $this->options['expire'];
        }
        if($result && is_int($expire)) {
            $this->handler->setTimeout($name, $expire);
        }		
        if($result && $this->options['length']>0) {
            // 记录缓存队列
            $this->queue($name);
        }
        return $result;
    }

    /**
     * 字符串(String)读取缓存
     * @access public
     * @param string $name 缓存变量名
     * @return mixed  无数据返回 false
     */
    public function g($name) {
        N('cache_read',1);
        return $this->handler->get($this->options['prefix'].$name);
    }
    /**
     * 字符串(String)写入缓存
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     * @param integer $expire  有效时间（秒） 必须为整形  <=0  直接过期
     * @return boolen
     */
    public function s($name, $value, $expire = null) {
        N('cache_write',1);
        $name   =   $this->options['prefix'].$name;
        $result = $this->handler->set($name, $value);
		
        if(is_null($expire)) {
            $expire  =  $this->options['expire'];
        }
        if($result && is_int($expire)) {
            $this->handler->setTimeout($name, $expire);
        }		
        if($result && $this->options['length']>0) {
            // 记录缓存队列
            $this->queue($name);
        }
        return $result;
    }
	/*
     * 数组 读取 单key
     * @access public
     * @param string $name 缓存变量名
     * @return mixed  无数据返回 false
     */
    public function hGet($name,$key) {
        N('cache_read',1);
        return $this->handler->hGet($this->options['prefix'].$name,$key);
    }
    /**
     * 数组 读取 全部 
     * @access public
     * @param string $name 缓存变量名
     * @return mixed  无数据返回 空数组
     */
    public function hGetAll($name) {
        N('cache_read',1);
        return $this->handler->hGetAll($this->options['prefix'].$name);
    }
    /**
     * 数组 写 键=值      
     * @access public
     * @access public
     * @param string $name 	缓存变量名
     * @param string $key 	键
     * @param mixed $value  值       
     * @param integer $expire  有效时间（秒） 必须为整形  <=0  直接过期
     * @return boolen 更新成功时返回0   新增成功返回1
     */
    public function hSet($name, $key, $value, $expire = null) {
        N('cache_write',1);
        $name   =   $this->options['prefix'].$name;
        $result = $this->handler->hSet($name, $key, $value);
		if(is_null($expire)) {
            $expire  =  $this->options['expire'];
        }
        if($result && is_int($expire)) {
            $this->handler->setTimeout($name, $expire);
        }		
        if($result && $this->options['length']>0) {
            // 记录缓存队列
            $this->queue($name);
        }
        return $result;
    }
    /**
     * 数组 读取键名在key数组中的值    做出兼容  cacheFile再放开
     * @access public
     * @param string $name 	缓存变量名
     * @param array $key_arr 	键数组
     * @return array|bool    键值对 array('field1' => 'value1', 'field2' => 'value2')
     */
    public function hMGet($name, $key_arr) {
        N('cache_read',1);
        return $this->handler->hMGet($this->options['prefix'].$name,$key_arr);
    }
    /**
     * 数组 写入(更新)键值对   
     * @access public
     * @param string $name 	缓存变量名
     * @param array $arr 	键值对   array('pass'=>19811,'qq'=>false,'aa'=>NULL) 19811='19811'   false=''   null='' true='1'
     * @param integer $expire  有效时间（秒） 必须为整形  <=0  直接过期
	 * @return boolen
     */
    public function hMSet($name, $arr, $expire = null) {
        N('cache_write',1);
        $name   =   $this->options['prefix'].$name;
        $result = $this->handler->hMSet($name, $arr);
		if(is_null($expire)) {
            $expire  =  $this->options['expire'];
        }
        if($result && is_int($expire)) {
            $this->handler->setTimeout($name, $expire);
        }		
        if($result && $this->options['length']>0) {
            // 记录缓存队列
            $this->queue($name);
        }
        return $result;
    }

    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolen
     */
    public function rm($name) {
        return $this->handler->delete($this->options['prefix'].$name);
    }

    /**
     * 清除缓存
     * @access public
     * @return boolen
     */
    public function clear() {
        return $this->handler->flushDB();
    }

}

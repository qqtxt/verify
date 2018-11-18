<?php
defined('JETEE_PATH') or exit();
/**
	$cache      =  new CacheFile();
	var_dump($cache->hMSet('my1',array('ok'=>'qqtxt1','pass'=>19811,'qq'=>false,'aa'=>NULL)));
	var_dump($cache->hGetall('my1'));exit;

 * 文件类型缓存类
 */
class CacheFile extends Cache {

    /**
     * 架构函数
     * @access public
     */
    public function __construct($options=array()) {
        if(!empty($options)) {
            $this->options =  $options;
        }
        $this->options['temp']      =   !empty($options['temp'])?   $options['temp']    :   C('DATA_CACHE_PATH');
        $this->options['prefix']    =   isset($options['prefix'])?  $options['prefix']  :   C('DATA_CACHE_PREFIX');
        $this->options['expire']    =   isset($options['expire'])?  $options['expire']  :   C('DATA_CACHE_TIME');
        $this->options['length']    =   isset($options['length'])?  $options['length']  :   0;
        if(substr($this->options['temp'], -1) != '/')    $this->options['temp'] .= '/';
        $this->init();
    }

    /**
     * 初始化检查
     * @access private
     * @return boolen
     */
    private function init() {
        // 创建项目缓存目录
        if (!is_dir($this->options['temp'])) {
            mkdir($this->options['temp']);
        }
    }

    /**
     * 取得变量的存储文件名
     * @access private
     * @param string $name 缓存变量名
     * @return string
     */
    private function filename($name) {
        $name	=	md5($name);
        if(C('DATA_CACHE_SUBDIR')) {
            // 使用子目录
            $dir   ='';
            for($i=0;$i<C('DATA_PATH_LEVEL');$i++) {
                $dir	.=	$name{$i}.'/';
            }
            if(!is_dir($this->options['temp'].$dir)) {
                mkdir($this->options['temp'].$dir,0755,true);
            }
            $filename	=	$dir.$this->options['prefix'].$name.'.php';
        }else{
            $filename	=	$this->options['prefix'].$name.'.php';
        }
        return $this->options['temp'].$filename;
    }

    /**
     * 读取缓存  可以是数组
     * @access public
     * @param string $name 缓存变量名
     * @return mixed  无数据返回false
     */
    public function get($name) {
		return ($content=$this->g($name)) ? unserialize($content) : false;
    }
    /**
     * 字符串(String)读取缓存   
     * @access public
     * @param string $name 缓存变量名
     * @return mixed  无数据返回 false
     */
    public function g($name) {
        $filename   =   $this->filename($name);
        if (!is_file($filename)) {
           return false;
        }
        N('cache_read',1);
        $content    =   file_get_contents($filename);
        if( false !== $content) {
            $expire  =  (int)substr($content,8, 12);
            if($expire != 0 && time() > filemtime($filename) + $expire) {
                //缓存过期删除缓存文件
                unlink($filename);
                return false;
            }
            if(C('DATA_CACHE_CHECK')) {//开启数据校验
                $check  =  substr($content,20, 32);
                $content   =  substr($content,52, -3);
                if($check != md5($content)) {//校验错误
                    return false;
                }
            }else {
            	$content   =  substr($content,20, -3);
            }
            if(C('DATA_CACHE_COMPRESS') && function_exists('gzcompress')) {
                //启用数据压缩
                $content   =   gzuncompress($content);
            }
            return $content;
        }
        else {
            return false;
        }
    }
    /**
     * 写入缓存  可以是数组
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     * @param int $expire  有效时间 0为永久  小于0直接过期
     * @return boolen
     */
    public function set($name,$value,$expire=null){
		return $this->s($name,serialize($value),$expire);
    }
    /**
     * 写入缓存   不能存数组
     * @access public
     * @param string $name 缓存变量名
     * @param mixed $value  存储数据
     * @param int $expire  有效时间 0为永久  小于0直接过期
     * @return boolen
     */
    public function s($name,$data,$expire=null) {
        N('cache_write',1);
        if(is_null($expire)) {
            $expire =  $this->options['expire'];
        }
        $filename   =   $this->filename($name);
		$data = str_replace(PHP_EOL, '', $data);
        if( C('DATA_CACHE_COMPRESS') && function_exists('gzcompress')) {
            //数据压缩
            $data   =   gzcompress($data,3);
        }
        if(C('DATA_CACHE_CHECK')) {//开启数据校验
            $check  =  md5($data);
        }else {
            $check  =  '';
        }
        $data    = "<?php\n//".sprintf('%012d',$expire).$check.$data."\n?>";
        $result  =   file_put_contents($filename,$data);
        if($result) {
            if($this->options['length']>0) {
                // 记录缓存队列
                $this->queue($name);
            }
            clearstatcache();
            return true;
        }else {
            return false;
        }
    }
	/*
     * 数组 读取 单key
     * @access public
     * @param string $name 缓存变量名
     * @return mixed  无数据返回 false
     */
    public function hGet($name,$key) {
        $return=$this->get($name);
		if(isset($return[$key])){
			return $return[$key];
		}else return false;
    }
    /**
     * 数组 读取 全部 
     * @access public
     * @param string $name 缓存变量名
     * @return mixed  无数据返回 false
     */
    public function hGetAll($name) {
        return $this->get($name);
    }
    /**
     * 数组 写入(更新)键值对   
     * @access public
     * @param string $name 	缓存变量名
     * @param array $arr 	键值对   array('pass'=>19811,'qq'=>false,'aa'=>NULL) 19811='19811'   false=''   null='' true='1'
     * @param integer $expire  有效时间（秒） 必须为整形  <=0  直接过期
	 * @return boolen
     */
    public function hMSet($name, $arr, $expire = null){
		$row=$this->get($name);
		if($row){
			$arr=array_merge($row,$arr);
		}
		return $this->set($name,$arr,$expire);
    }
	
	
    /**
     * 数组 写 键=值      注意只有这个更新成功时返回0
     * @access public
     * @access public
     * @param string $name 	缓存变量名
     * @param string $key 	键
     * @param mixed $value  值       
     * @param integer $expire  有效时间（秒） 必须为整形  <=0  直接过期
     * @return boolen 
     */
    public function hSet($name, $key, $value, $expire = null) {
		$arr=array($key=>$value);		
		$row=$this->get($name);
		$update=0;
		if($row){
			$update=1;
			$arr=array_merge($row,$arr);
		}
		if($this->set($name,$arr,$expire)){
			return $update ? 0 : 1;
		}
		return false;
    }
    /**
     * 数组 读取键名在key数组中的值    
     * @access public
     * @param string $name 	缓存变量名
     * @param array $key_arr 	键数组
     * @return array|bool    键值对 array('field1' => 'value1', 'field2' => 'value2')
     */
    public function hMGet($name, $key_arr) {
       if($row=$this->get($name)){
		   $return=array();
			foreach($key_arr as $k=>$v){
				$return[$v]=isset($row[$v])?$row[$v] : false;
			}
			return $return;
	   }else return false;
    }
    /**
     * 删除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolen
     */
    public function rm($name) {
        return unlink($this->filename($name));
    }

    /**
     * 清除缓存
     * @access public
     * @param string $name 缓存变量名
     * @return boolen
     */
    public function clear() {
        $path   =  $this->options['temp'];
        $files  =   scandir($path);
        if($files){
            foreach($files as $file){
                if ($file != '.' && $file != '..' && is_dir($path.$file) ){
                    array_map( 'unlink', glob( $path.$file.'/*.*' ) );
                }elseif(is_file($path.$file)){
                    unlink( $path . $file );
                }
            }
            return true;
        }
        return false;
    }
}
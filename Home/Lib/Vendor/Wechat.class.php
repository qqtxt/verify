<?php
defined('JETEE_PATH') or exit();

class Wechat extends WechatAbstract {

    public function __construct($options){
        parent::__construct($options);
    }

    /**
     * 日志记录
     * @param mixed $log 输入日志
     * @return mixed
     */
    public function log($log){
		Log::write($log,'ALERT',3,'wechat');
    }
	/*
	public function clearCache(){
		return $this->cache->clear();
	}*/

	/**
	 * 设置缓存
	 * @param string $cachename
	 * @param mixed $value
	 * @param int $expired
	 * @return boolean
	 */
	protected function setCache($cachename,$value,$expired){
		return S($cachename,$value,array('expire'=>$expired));
	}

	/**
	 * 获取缓存
	 * @param string $cachename
	 * @return mixed
	 */
	protected function getCache($cachename){
		return S($cachename);
	}

	/**
	 * 清除缓存
	 * @param string $cachename
	 * @return boolean
	 */
	protected function removeCache($cachename){
		return S($cachename,NULL);
	}

}

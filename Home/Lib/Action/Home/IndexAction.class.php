<?php
defined('JETEE_PATH') or exit();
#require_once(APP_PATH.'Lib/Vendor/ipip.class.php');

class IndexAction extends HomeAction{
	public function __construct(){
		parent::__construct();
		$this->redirect('Admin/Index/login');
	}
	public function index(){
/* 	$cache      =  new CacheRedis();
	var_dump($cache->hmset('my61',array('66666666666','6666666'),1));
	var_dump($cache->hmget('my61',array(0)));
	var_dump($cache->hmget('my61',array(3)));
	sleep(2);
	var_dump($cache->hgetall('my61'));
	exit;		
 */		
		if(is_login(0)){
			$this->redirect('User/center');
		}
		$this->display();
	}
}




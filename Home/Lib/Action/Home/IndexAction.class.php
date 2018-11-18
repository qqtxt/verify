<?php
defined('JETEE_PATH') or exit();
#require_once(APP_PATH.'Lib/Vendor/ipip.class.php');

class IndexAction extends HomeAction{
	public function __construct(){
		parent::__construct();
		#redirect('Admin/Index/login');
	}
	public function index(){
  /*	$cache      =  new CacheRedis();
	var_dump($cache->set('my61','6666666',1));
	var_dump($cache->get('my61'));
	sleep(2);
	var_dump($cache->get('my61'));
	exit;		
*/		
		if(is_login(0)){
			$this->redirect('User/center');
		}
		$this->display();
	}
}




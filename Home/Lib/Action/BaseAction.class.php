<?php
defined('JETEE_PATH') or exit();
//exc d m
/**
* 本站前后台最基础控制器
* @version 0.0.1 15:06 2014/11/20
*/
class BaseAction extends Action {
	public function __construct() {
		parent::__construct();
		//去斜线		
		if(MAGIC_QUOTES_GPC) {
			$_GET = dstripslashes($_GET);
			$_POST = dstripslashes($_POST);
			$_COOKIE = dstripslashes($_COOKIE);
			//保证$_REQUEST正常取值
			$_REQUEST = array_merge($_POST,$_GET);	
		}
		
		//配置参数添加修改	
		//读入数据库中网站配置缓冲
		common_config();
	}
	/**
	 * Action跳转(URL重定向） 延时跳转
	 * @access protected
	 * @param string $url 字符串
	 * @param array $params 其它URL参数
	 * @param integer $delay 延时跳转的时间 单位为秒
	 * @param string $msg 跳转提示信息
	 * @return void
	 */
	protected function location($url,$delay=2,$msg='',$url_info='') {
		$message['content'] = $msg;	
		$message['delay'] = $delay;	
		if (empty($url)){
			$message['url_info'] =$url_info ? $url_info :'返回上一页' ;
			$message['back_url'] = 'javascript:window.history.go(-1);';
		}else{
				$message['url_info'] =$url_info ? $url_info : '自动跳转';
				$message['back_url'] = (stripos($url,'http://')===0 || strpos($url,'/')===0 ? '':__ROOT__.'/').$url;
		}
		$this->assign('message', $message);	
		$this->display('Home@Public:location');
		exit;
	}
	public function now() {
		echo date('Y-m-d H:i:s');
	}
	public function php_max() {
		echo PHP_INT_MAX;
	}
	public function _empty(){
		header('location:'.DOMAIN_URL.ROOT_URL);
	}	
	/**/
	public function server() {
		echo '$_SERVER=>';print_r($_SERVER);echo "\r\n";
		echo '$_REQUEST=>';print_r($_REQUEST);echo "\r\n";
		echo '$GLOBALS =>';print_r($GLOBALS );echo "\r\n";
	}
	public function phpinfo() {phpinfo();}
	public function c(){print_r(C());var_dump(C());}	
	public function cfg(){$cfg=db('config')->getField('name,value');echo count($cfg);}	
	public function d(){$defs  = get_defined_constants(TRUE);print_r($defs['user']);var_dump($defs['user']);}	
	
	
}
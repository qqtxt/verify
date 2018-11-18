<?php
defined('JETEE_PATH') or exit();
/**
* 前台公共控制器  直接放入action目录  前端各分组好继承直接用
* @link http://www.jetee.cn/
* @version 1.0.1 15:33  11:31 2016/05/26 
*/
class HomeAction extends BaseAction {
    /**
     * 登陆用户 row
     */      
	public $login=array();
    public function __construct() {
        parent::__construct();
		#header("Content-type: text/html; charset=utf-8");
		//前台配置参数添加修改
		$TMPL_PARSE_STRING=array(
			'__HOME__' => __ROOT__.'/Public/Home',
			'__ROOT__' => DOMAIN_URL.__ROOT__
		);
		C('TMPL_PARSE_STRING',$TMPL_PARSE_STRING);
		//定义公用 title等
		$this->assign('title',C('site_title'));
		$this->assign('description',C('description'));
		$this->assign('keywords',C('keywords'));
	}
	
}
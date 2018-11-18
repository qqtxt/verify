<?php
defined('JETEE_PATH') or exit();
class ArticleAction extends HomeAction{
	public function __construct(){
		parent::__construct();
		$this->d=d(MODULE_NAME);
	}
    public function show(){
		$id=intval($_REQUEST['id']);
		if(!$id ||  !($item=db()->row('select * from '.C('DB_PREFIX').'article where article_id='.$id))){ 
			header('location:'.DOMAIN_URL.ROOT_URL);
		}
		$this->assign('item',$item);
		$this->display();
	}
}


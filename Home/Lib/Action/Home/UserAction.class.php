<?php
defined('JETEE_PATH') or exit();
class UserAction extends HomeAction{
	public function __construct(){
		parent::__construct();
		//检查有没有登陆
		if(!is_login(0) && !in_array(ACTION_NAME, array('login','forget','reg'))){
			$this->redirect('User/login');
		}
	}
	public function login(){
		if(is_login(0) ){
			$this->redirect('User/center');
		}
		$this->display();
	}
	public function reg(){
		$this->display();
	}
	public function forget(){
		$this->display();
	}	
    public function center(){
		$this->assign('feedback',D('Feedback')->getFeedback());
		$this->assign('feedback_new_count',D('Feedback')->getNewCount());
		$this->assign('notification',M('notification')->where(array('uid'=>session('uid')))->order('nid desc')->limit(10)->select());
		$this->assign('notice_new_num',M('notification')->where(array('uid'=>session('uid'),'new'=>1))->count());
		$this->display();
	}
	public function main(){
		$this->display();
	}
	public function head(){
		$this->display();
	}
	public function info(){
		$this->assign('recommend', user('recommend') ? M('user')->where(array('uid'=>user('recommend')))->getField('email') : '');
		$this->display();
	}
	public function notice(){
		$url=MODULE_NAME.'/'.ACTION_NAME;
		$where='where (cat_id=1 or cat_id=0) and status=1';
		$count = db()->single('select count(*) from '.C('DB_PREFIX').'notice '.$where);
		$page=new BootstrapPage($count,C('page_rows'),'',$url);	
		$rows=db()->query('select * from '.C('DB_PREFIX').'notice '.$where." order by sort_order desc,notice_id desc limit {$page->firstRow},{$page->listRows}");
		$this->assign('rows',$rows);
		$this->assign('page', $page->show());
		$this->display();
	}
	public function show_notice(){
		$id=I('get.notice_id',1,'intval');
		$row=db()->row('select * from '.C('DB_PREFIX').'notice '." where notice_id=$id".' limit 1');
		$this->assign('row',$row);
		$this->display();
	}
	public function help(){
		$row=db()->row('select * from '.C('DB_PREFIX').'article where article_id='.intval($_REQUEST['id']).' limit 1');
		$this->assign('row',$row);
		$this->display();
	}
}




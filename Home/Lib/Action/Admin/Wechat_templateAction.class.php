<?php
defined('JETEE_PATH') or exit();

 /**
* 功能描述：微信公众平台管理
* @version 0.0.1 16:42 2017/10/3
*/
class Wechat_templateAction extends Wechat_baseAction{
	public function __construct(){
		parent::__construct();	
		$this->d=d(MODULE_NAME);	
	}
	/**
	 * 模板消息 1
	 */
	public function message()
	{
		$page=new BootstrapPage($this->d->count(),20,'','Admin/'.MODULE_NAME.'/'.ACTION_NAME);
		$this->assign('page', $page->show());

		// 查找status值为1的用户数据 以创建时间排序 返回10条数据
		$list =$this->d->order('id desc')->page($page->nowPage,$page->listRows)->select();
		if($list){
			foreach($list as $key=>$val){
				$list[$key]['add_time'] = date('Y-m-d H:i:s',$val['add_time']);
			}
		}
		$lang = array('template_title'=>'模板标题','serial'=>'编号','add_time'=>'创建时间','wechat_close'=>'关闭','wechat_open'=>'开启');
		$this->assign('lang',$lang);
		$this->assign('list',$list);
		$this->display();
	}

	/**
	 * 开关按钮1
	 */
	public function toggle(){
		if(IS_GET){			
			$open_id = I('open_id');
			$data['switch'] = I('value',0,'intval');
			$data['add_time'] =NOW_TIME;
			$this->d->where('open_id ="'.$open_id.'"')->data($data)->save();
			echo $data['switch'];
		}
	}
}

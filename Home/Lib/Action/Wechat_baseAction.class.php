<?php
defined('JETEE_PATH') or exit();
 /**
* 功能描述：微信公众平台管理
* @version 0.0.1 14:45 2016/11/21
*/
class Wechat_baseAction extends AdministrationAction{
	protected $weObj = '';
	protected $wechat_id = 0;//当前管理的微信公众号id  get转入session
	public function __construct(){
		parent::__construct();
		$this->d=d('Wechat');
		/*/ 没有公众号 新建一个
		$mpInfo = $this->d->find($this->wechat_id);
		if(empty($mpInfo)){
			$data = array(
				'id' => $this->wechat_id,
				'add_time' => time(),
				'type' => 2,
				'status' => 1,
				'default_wx' => 1
			);
			$this->d->data($data)->add();
			$this->redirect('Wechat/edit');
		}*/
		// 获取配置信息
		$this->get_config();
		if(MODULE_NAME!='Wechat')
			L(include LANG_PATH.LANG_SET.'/Admin_wechat.php');
		$this->assign('lang', L());
	}
	/**
	 * 获取配置信息
	 */
	private function get_config()
	{
		#$without = array('append','edit','delete','set_default');
		//保存当前管理公众号id
		$id = I('get.wechat_id', 0, 'intval');//$this->wechat_id; 
		if (!empty($id)) {
			session('wechat_id', $id);
		}elseif(session('?wechat_id')){
			$id=session('wechat_id');
		}else{
			$id=$this->d->where(array('default_wx'=>1))->getField('id');
			session('wechat_id', $id);
		}
		$this->wechat_id = $id;
		//如果不是以上这些操作获取公众号配置
		#if (!in_array(ACTION_NAME, $without)) {	}		
		$wechat=S('current_wechat_row'.$id);
		if(empty($wechat)){
			$wechat = $this->d->where('id=' . $id)->find();
			S('current_wechat_row'.$id,$wechat);
		}
		if (empty($wechat['status'])){
			$this->message(L('open_wechat'), NULL, 'error');
		}
		// 公众号配置信息
		/*$config = array();
		$config['token'] = $wechat['token'];
		$config['appid'] = $wechat['appid'];
		$config['appsecret'] = $wechat['appsecret'];*/
		$this->weObj = new Wechat($wechat);
		$this->assign('type', $wechat['type']);
			
		
	}
	
}

<?php
defined('JETEE_PATH') or exit();

 /**
* 功能描述：微信公众平台管理
* @version 0.0.1 14:45 2016/11/21
*/
class WechatAction extends Wechat_baseAction{
	public function __construct(){
		parent::__construct();
	}
	/**
	 * 修改公众号 1
	 */
	public function edit(){
		$condition['id'] = $this->wechat_id;
		if (IS_POST) {
			$data = I('post.data',array(),null,1);
			$data=$this->d->create($data);
			if ($data === false) {
				$this->message($this->d->getError(), NULL, 'error');
			}
			S('current_wechat_row'.$condition['id'],null);
			S(C('default_wechat_config'),null);
			// 更新数据
			$this->d->data($data)->where($condition)->save();
			admin_log('ID:'.$condition['id'], 'edit', MODULE_NAME);
			$this->redirect('Wechat/edit');
		}
		$data = $this->d->where($condition)->find();
		$this->assign('data', $data);
		$this->display();
	}
}

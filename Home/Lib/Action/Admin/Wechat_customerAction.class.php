<?php
defined('JETEE_PATH') or exit();
class Wechat_customerAction extends Wechat_baseAction{
	public function __construct()
	{
		parent::__construct();
		$this->assign('ur_here', L('wechat'));
		$this->assign('action', ACTION_NAME);
	}

	/**
	 * 多客服设置
	 */
	public function service(){
		$wechat_extend = M('wechat_extend');
		if (IS_POST) {
			$command = I('post.command');
			$data = I('post.data');
			$config = I('post.config');
			$info = Check::rule(array(
				Check::must($command),
				'关键词不正确'
			));
			
			if ($info !== true) {
				$this->message($info, NULL, 'error');
			}
			
			if (! empty($config)) {
				$data['config'] = serialize($config);
			}
			$num = $wechat_extend->where('command = "' . $command . '" and wechat_id = ' . $this->wechat_id)->count();
			if ($num > 0) {
				$wechat_extend->data($data)->where('command = "' . $command . '" and wechat_id = ' . $this->wechat_id)->save();
			} else {
				$data['wechat_id'] = $this->wechat_id;
				$data['command'] = $command;
				$wechat_extend->data($data)->add();
			}
			
			$this->redirect('service');
		}
		
		$customer_service = $wechat_extend->field('name, enable, config')->where('command = "kefu" and wechat_id = ' . $this->wechat_id)->find();
		if ($customer_service['config']) {
			$customer_service['config'] = unserialize($customer_service['config']);
		}
		$this->assign('customer_service', $customer_service);
		$this->display();
	}


}

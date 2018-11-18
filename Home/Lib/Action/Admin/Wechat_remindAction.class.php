<?php
defined('JETEE_PATH') or exit();
class Wechat_remindAction extends Wechat_baseAction{
	public function __construct()
	{
		parent::__construct();
		$this->assign('ur_here', L('wechat'));
		$this->assign('action', ACTION_NAME);
	}


	/**
	 * 提醒设置
	 */
	public function lists()
	{
		$wechat_extend = M('wechat_extend');
		if (IS_POST) {
			$command = I('post.command');
			$data = I('post.data',array(),null,1);
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
			$data['wechat_id'] = $this->wechat_id;
			$num = $wechat_extend->where('command = "' . $command . '" and wechat_id = ' . $this->wechat_id)->count();
			if ($num > 0) {
				$wechat_extend->data($data)->where('command = "' . $command . '" and wechat_id = ' . $this->wechat_id)->save();
			} else {
				$data['command'] = $command;
				$wechat_extend->data($data)->add();
			}
			$this->redirect('lists');
		}
		
		$order_remind = $wechat_extend->field('name, enable, config')->where('command = "order_remind" and wechat_id = ' . $this->wechat_id)->find();
		if ($order_remind['config']) {
			$order_remind['config'] = unserialize($order_remind['config']);
		}
		$pay_remind = $wechat_extend->field('name, enable, config')->where('command = "pay_remind" and wechat_id = ' . $this->wechat_id)->find();
		if ($pay_remind['config']) {
			$pay_remind['config'] = unserialize($pay_remind['config']);
		}
		$send_remind = $wechat_extend->field('name, enable, config')->where('command = "send_remind" and wechat_id = ' . $this->wechat_id)->find();
		if ($send_remind['config']) {
			$send_remind['config'] = unserialize($send_remind['config']);
		}
		$register_remind = $wechat_extend->field('name, enable, config')->where('command = "register_remind" and wechat_id = ' . $this->wechat_id)->find();
		if ($register_remind['config']) {
			$register_remind['config'] = unserialize($register_remind['config']);
		}
		$this->assign('order_remind', $order_remind);
		$this->assign('pay_remind', $pay_remind);
		$this->assign('send_remind', $send_remind);
		$this->assign('register_remind', $register_remind);
		$this->display();
	}
}

<?php
defined('JETEE_PATH') or exit();

 /**
* 功能描述：微信公众平台管理
* @version 0.0.1 14:45 2016/11/21
*/
class Wechat_custom_messageAction extends Wechat_baseAction{
	public function __construct(){
		parent::__construct();
		$this->d=d('wechat_user');
	}
	

	/**
	 * 发送客服消息 1
	 */
	public function send()
	{
		if (IS_POST) {
			$data = I('post.data',array(),null,1);
			$openid = I('post.openid','',null,1);
			$rs = Check::rule(array(
				Check::must($openid),
				L('select_openid')
			), array(
				Check::must($data['msg']),
				L('message_content') . L('empty')
			));
			if ($rs !== true) {
				exit(json_encode(array(
					'status' => 0,
					'msg' => $rs
				)));
			}
			$data['send_time'] = NOW_TIME;
			$data['iswechat'] = $this->wechat_id;
			// 微信端发送消息
			$msg = array(
				'touser' => $openid,
				'msgtype' => 'text',
				'text' => array(
					'content' => $data['msg']
				)
			);
			$rs = $this->weObj->sendCustomMessage($msg);
			if (empty($rs)) {
				if($this->weObj->errCode=='45015'){
					$err='当用户微信不活跃时间超过24小时（回复文字即可），不能将信息推送到用户微信公众号。';
				}else{
					$err='错误代码：【' . $this->weObj->errCode . '】  错误信息：' . $this->weObj->errMsg;
				}
				exit(json_encode(array(
					'status' => 0,
					'error' => $err
				)));
			}
			// 添加数据
			m('wechat_custom_message')->data($data)->add();
			admin_log('wu_id:'.$data['wu_id'], 'send', 'custom_message');
			exit(json_encode(array(
				'status' => 1
			)));
		}
		$wu_id = I('get.wu_id');
		$openid = I('get.openid');
		$openid = $openid ? $where['openid'] = $openid : $where['wu_id'] = $wu_id;
		$info = $this->d->field('wu_id, nickname, openid')->where($where)->find();		
		$this->assign('info', $info);
		$this->display();
	}

	/**
	 * 客服消息列表 1
	 */
	public function lists()
	{
		$wu_id = I('get.wu_id');
		if (empty($wu_id)) {
			$this->message(L('select_openid'), NULL, 'error');
		}
		$nickname = $this->d->field('nickname')->where('wu_id = ' . $wu_id)->getOne();
		// 分页
		$total = m('wechat_custom_message')->where('wu_id = ' . $wu_id)->count();
		$page=new BootstrapPage($total,20,'','Admin/'.MODULE_NAME.'/'.ACTION_NAME);
		$list = m('wechat_custom_message')->field('msg, send_time, iswechat')->where('wu_id=' . $wu_id)->order('send_time desc, id desc')->page($page->nowPage,$page->listRows)->select();
		
		$this->assign('pager', $page->show());
		$this->assign('list', $list);
		$this->assign('nickname', $nickname);
		$this->display();
	}
	
}

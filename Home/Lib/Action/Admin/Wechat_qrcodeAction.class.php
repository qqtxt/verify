<?php
defined('JETEE_PATH') or exit();

 /**
* 功能描述：微信公众平台管理
* @version 0.0.1 14:45 2016/11/21
*/
class Wechat_qrcodeAction extends Wechat_baseAction{
	public function __construct(){
		parent::__construct();	
		$this->d=d(MODULE_NAME);	
	}
	/**
	 * 扫码引荐 1
	 */
	public function lists(){
	
		$total	= $this->d->count();// 查询满足要求的总记录数
		$page=new BootstrapPage($total,20,'','Admin/'.MODULE_NAME.'/'.ACTION_NAME);
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $this->d->order('id desc')->page($page->nowPage,$page->listRows)->select();

		/*/算提成
		if($list){
			$wechat = M('affiliate_log');
			foreach($list as $key=>$val){
				$list[$key]['share_account'] = $this->model->table('affiliate_log')->field('sum(money)')->where('separate_type = 0 and user_id = '.$val['scene_id'])->getOne();
			}
		}*/
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page', $page->show());
		$this->display();
		//成交量
		// 		$list[$key]['share_account'] = $this->model->table('affiliate_log')->field('sum(money)')->where('separate_type = 0 and user_id = '.$val['scene_id'])->getOne();
	}
	/**
	 * 编辑二维码 1
	 */
	public function edit()
	{	
		if (IS_POST) {
			$data = I('post.data',array(),null,1);
			$data['wechat_id'] = $this->wechat_id;
			// 验证数据
			$result = Check::rule(array(
				Check::must($data['function']),
				L('qrcode_function') . L('empty')
			), array(
				Check::must($data['scene_id']),
				L('qrcode_scene_value') . L('empty')
			));
			if ($result !== true) {
				exit(json_encode(array(
					'status' => 0,
					'msg' => $result
				)));
			}

			$rs = $this->d->where('scene_id = '.$data['scene_id'])->count();
			if($rs > 0){
				exit(json_encode(array('status'=>0, 'msg'=>L('qrcode_scene_limit'))));
			}
			
			/*
			Array
			(
				[username] => 张宏华
				[scene_id] => 10000
				[expire_seconds] => 
				[function] => 导流
				[sort] => 
				[wechat_id] => 1
			)*/

			$data['type']=$data['expire_seconds'] ? 0 : 1;
			$this->d->data($data)->add();	
			exit(json_encode(array(
				'status' => 1
			)));
		}
		$id = I('get.id', 0, 'intval');
		if (! empty($id)) {
			$status = I('get.status', 0, 'intval');
			$$this->d->data('status = ' . $status)->where('id = ' . $id)->save();
			$this->redirect('lists');
		}
		$this->display();
	}
	/**
	 * 删除二维码
	 */
	public function del()
	{
		$id = I('get.id', 0, 'intval');
		if (empty($id)) {
			$this->message(L('select_please') . L('qrcode'), NULL, 'error');
		}

		$this->d->where('id = ' . $id)->delete();
		$url=U(I('get.url', 'lists'));
		$this->message(L('qrcode') . L('drop') . L('success'), $url);
	}		


	/**
	 * 更新并获取二维码
	 */
	public function get(){
		$id = I('get.id', 0, 'intval');
		if (empty($id)) {
			exit(json_encode(array(
				'status' => 0,
				'msg' => L('select_please') . L('qrcode')
			)));
		}
		$rs = $this->d->field('type, scene_id, expire_seconds, qrcode_url, status')->where('id = ' . $id)->find();
		if (empty($rs['status'])) {
			exit(json_encode(array(
				'status' => 0,
				'msg' => '二维码不存在！'
			)));
		}
		if (empty($rs['qrcode_url'])) {
			// 获取二维码ticket
			$ticket = $this->weObj->getQRCode((int)$rs['scene_id'], $rs['type'], $rs['expire_seconds']);
			if (empty($ticket)) {
				exit(json_encode(array(
					'status' => 0,
					'msg' => L('errcode') . $this->weObj->errCode . L('errmsg') . $this->weObj->errMsg
				)));
			}
			$data['ticket'] = $ticket['ticket'];
			$data['expire_seconds'] = $ticket['expire_seconds'];
			$data['endtime'] = NOW_TIME + $ticket['expire_seconds'];
			// 二维码地址
			$qrcode_url = $this->weObj->getQRUrl($ticket['ticket']);
			$data['qrcode_url'] = $qrcode_url;
			$this->d->where('id = ' . $id)->data($data)->save();
		} else {
			$qrcode_url = $rs['qrcode_url'];
		}
		$this->assign('qrcode_url', $qrcode_url);
		$this->display();
	}	
  

}

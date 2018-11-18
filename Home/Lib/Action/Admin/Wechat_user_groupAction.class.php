<?php
defined('JETEE_PATH') or exit();

 /**
* 功能描述：微信公众平台管理
* @version 0.0.1 14:45 2016/11/21
*/
class Wechat_user_groupAction extends Wechat_baseAction{
	public function __construct(){
		parent::__construct();
		$this->d=d('wechat_user');
	}
	/**
	 * 同步分组 1
	 */
	public function sync()
	{
		if (empty($this->wechat_id)) {
			$this->message(L('wechat_empty'), NULL, 'error');
		}

		// 微信端分组列表
		$list = $this->weObj->getGroup();		
		if (empty($list)) {
			$this->message(L('errcode') . $this->weObj->errCode . L('errmsg') . $this->weObj->errMsg, NULL, 'error');
		}
		// 本地分组
		$where['wechat_id'] = $this->wechat_id;
		M('wechat_user_group')->where($where)->delete();
		foreach ($list['groups'] as $key => $val) {
			$data['wechat_id'] = $this->wechat_id;
			$data['group_id'] = $val['id'];
			$data['name'] = $val['name'];
			$data['count'] = $val['count'];
			M('wechat_user_group')->data($data)->add();
		}
		admin_log('分组同步', 'edit', 'wechat_user');
		$this->redirect('Wechat_subscribe/lists');
	}

	/**
	 * 添加、编辑分组
	 */
	public function edit()
	{
		if (empty($this->wechat_id)) {
			$this->message(L('wechat_empty'), NULL, 'error');
		}
		if (IS_POST) {
			$name = I('post.name');
			$id = I('post.id', 0, 'intval');
			$group_id = I('post.group_id');
			if (empty($name)) {
				exit(json_encode(array(
					'status' => 0,
					'msg' => L('group_name') . L('empty')
				)));
			}
			$data['name'] = $name;
			if (! empty($id)) {
				// 微信端更新
				$rs = $this->weObj->updateGroup($group_id, $name);
				if (empty($rs)) {
					exit(json_encode(array(
						'status' => 0,
						'msg' => L('errcode') . $this->weObj->errCode . L('errmsg') . $this->weObj->errMsg
					)));
				}
				// 数据更新
				$where['id'] = $id;
				M('wechat_user_group')
					->data($data)
					->where($where)
					->save();
				admin_log('分组名:'.$name, 'edit', 'wechat_user');
			} else {
				// 微信端新增
				$rs = $this->weObj->createGroup($name);
				if (empty($rs)) {
					exit(json_encode(array(
						'status' => 0,
						'msg' => L('errcode') . $this->weObj->errCode . L('errmsg') . $this->weObj->errMsg
					)));
				}
				// 数据新增
				$data['wechat_id'] = $this->wechat_id;
				$data['group_id'] = $rs['group']['id'];
				$data['name'] = $rs['group']['name'];
				M('wechat_user_group')
					->data($data)
					->add();
				admin_log('分组名:'.$name, 'add', 'wechat_user');
			}
			exit(json_encode(array(
				'status' => 1
			)));
		}
		$id = I('get.id', 0, 'intval');
		$group = array();
		if (! empty($id)) {
			$where['id'] = $id;
			$group = M('wechat_user_group')
				->field('id, group_id, name')
				->where($where)
				->find();
		}
		$this->assign('group', $group);
		$this->display();
	}
	

	
}

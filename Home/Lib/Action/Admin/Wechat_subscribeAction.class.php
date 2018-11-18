<?php
defined('JETEE_PATH') or exit();

 /**
* 功能描述：微信公众平台管理
* @version 0.0.1 14:45 2016/11/21
*/
class Wechat_subscribeAction extends Wechat_baseAction{
	public function __construct(){
		parent::__construct();
		$this->d=d('Wechat_user');
	}
	


	/**
	 * 关注用户列表搜索 1
	 */
	public function lists()
	{
		$keywords = I('request.keywords') ? I('request.keywords') : I('get.k');
		$group_id = I('get.group_id',null,'intval');
		$where = '';
		$where1 = '';
		$parameter='';
		if (! empty($keywords)) {
			$where  = ' and (u.nickname like "%' . $keywords . '%" or u.province like "%' . $keywords . '%" or u.city like "%' . $keywords . '%")';
			$where1 = ' and (nickname like "%' . $keywords . '%" or province like "%' . $keywords . '%" or city like "%' . $keywords . '%")';
			$parameter['k']=urlencode($keywords);
		}
		if (isset($_GET['group_id']) && $group_id >= 0) {
			$where  = ' and u.group_id = ' . $group_id;
			$where1 = ' and group_id = ' . $group_id;
			$parameter['group_id']=$group_id;
		}
		
		$total = $this->d->where('subscribe = 1 and wechat_id = '.$this->wechat_id. $where1)->count();
		$page=new BootstrapPage($total,20,$parameter,'Admin/'.MODULE_NAME.'/'.ACTION_NAME);
		$this->assign('pager', $page->show());

		$list = $this->d->alias('u')->field('u.*, g.name, us.username ')->join('__WECHAT_USER_GROUP__ as g ON u.group_id = g.group_id ')->join('__ADMIN__ as us ON us.admin_id = u.admin_id ')
			->where('u.subscribe = 1 and u.wechat_id = '.$this->wechat_id . $where)->order('u.subscribe_time desc')->page($page->nowPage,$page->listRows)->select();
		// 分组
		$group_list = M('wechat_user_group')->field('id, group_id, name, count')->where('wechat_id = '.$this->wechat_id)->order('id, sort desc')->select();
		$this->assign('list', $list);
		$this->assign('group_list', $group_list);
		$this->display();
	}

	/**
	 * 移动关注用户分组 1
	 */
	public function move(){
		if (IS_POST) {
			if (empty($this->wechat_id)) {
				$this->message(L('wechat_empty'), NULL, 'error');
			}
			$group_id = I('post.group_id');
			$openid = I('post.id');
			$data['group_id'] = $group_id;
			if (is_array($openid)) {
				foreach ($openid as $v) {
					// 微信端移动用户
					$this->weObj->updateGroupMembers($group_id, $v);
					// 数据处理
					$this->d->where('openid = "' . $v . '"')->save($data);
				}
				admin_log('转移分组', 'edit', 'wechat_user');
				$this->message(l('sub_move_sucess'), U('lists'));
			} else {
				$this->message(l('select_please'), NULL, 'error');
			}
		}
	}

	/**
	 * 同步更新全部用户信息 1
	 */
	public function update()
	{
		ignore_user_abort(true);
		set_time_limit (0);
		if (empty($this->wechat_id)) {
			$this->message(L('wechat_empty'), NULL, 'error');
		}
		// 本地数据
		$where['wechat_id'] = $this->wechat_id;
		$local_user = $this->d->field('wu_id,openid')->where($where)->select();
		if (empty($local_user)) {
			$local_user = array();
		}
		$user_list = array();
		foreach ($local_user as $v) {
			$user_list[] = $v['openid'];
		}
		// 微信端数据
		$wechat_user = $this->weObj->getUserList();
		
		if ($wechat_user['total'] <= 10000) {
			$wechat_user_list = $wechat_user['data']['openid'];
		} else {
			$num = ceil($wechat_user['total'] / 10000);
			$wechat_user_list = $wechat_user['data']['openid'];
			for ($i = 0; $i <= $num; $i ++) {
				$wechat_user1 = $this->weObj->getUserList($wechat_user['next_openid']);
				$wechat_user_list = array_merge($wechat_user_list, $wechat_user1['data']['openid']);
			}
		}
		// 新更新已在库内的   
		foreach ($local_user as $val) {
			// 数据在微信端存在
			if (in_array($val['openid'], $wechat_user_list)) {
				$info = $this->weObj->getUserInfo($val['openid']);
				$info['group_id'] = $this->weObj->getUserGroup($val['openid']);
				$where1['wu_id'] = $val['wu_id'];
				$this->d->data($info)->where($where1)->save();
			} else {
				$where2['wu_id'] = $val['wu_id'];
				$data['subscribe'] = 0;
				$this->d->data($data)->where($where2)->save();
			}
		}
		//再更新不在库内的
		foreach ($wechat_user_list as $vs) {
			if (! in_array($vs, $user_list)) {
				$info = $this->weObj->getUserInfo($vs);
				$info['group_id'] = $this->weObj->getUserGroup($vs);
				$info['wechat_id'] = $this->wechat_id;
				$this->d->data($info)->add();
			}
		}
		admin_log('全部', 'update', 'wechat_user');
		$this->redirect('lists');
	}


	
}

<?php
defined('JETEE_PATH') or exit();

 /**
* 功能描述：微信公众平台管理
* @version 0.0.1 19:07 2017/2/6
*/
class Wechat_replyAction extends Wechat_baseAction{
	public function __construct(){
		parent::__construct();
		$this->d = d(MODULE_NAME);
	}
    protected function check_priv($privilege) {
		//无登陆
        if(!session('?admin_id') && !in_array(ACTION_NAME, array('login','houtai')) ) {
			if(IS_AJAX)	make_json_response('没有操作权限,可能是登陆超时', 1, '没有操作权限,可能是登陆超时',array('quickClose'=>1));
            $this->redirect('Index/login');
        }
		//超级管理员
        if(session('admin_id')== 1) {
            return true;
        }
		//直接允许的
        elseif(ACTION_NAME =='auto_reply') {
            return true;
        }
		//权限检查
		$privilege=',' .$privilege. ',';
		$priv_str=',' .MODULE_NAME.'_'.ACTION_NAME. ',';
	
        if(strpos($privilege, $priv_str) === false) {
			if(IS_AJAX)	make_json_response('没有操作权限', 1, '没有操作权限',array('quickClose'=>1));
            $this->sys_msg('没有操作权限');
        }
    }	
	
/**
	 * 自动回复  不用权限
	 */
	public function auto_reply()
	{
		// 素材数据
		$type = I('get.type');
		if (! empty($type)) {
			$where = 'wechat_id = ' . $this->wechat_id . ' and file !="" ';
			if ('image' == $type) {
				$where .= ' and (type = "image" or type="news")';
			} elseif ('voice' == $type) {
				$where .= ' and type = "voice"';
			} elseif ('video' == $type) {
				$where = ' and type = "video"';
			}
			
			if ('news' != $type) {
				$list = M('wechat_media')->field('id, file, file_name, size, add_time, type')->where($where)->order('add_time desc')
					// ->limit($offset)
					->select();
			}
			elseif ('news' == $type) {
				// 只显示单图文
				$no_list = I('get.no_list', 0, 'intval');
				$this->assign('no_list', $no_list);
				if (! empty($no_list)) {
					$where = 'wechat_id = ' . $this->wechat_id . ' and type="news" and article_id =""';
				} else {
					$where = 'wechat_id = ' . $this->wechat_id . ' and type="news"';
				}
				$list = M('wechat_media')->field('id, title, file, file_name, size, content, add_time, type, article_id')->where($where)->order('add_time desc')
					// ->limit($offset)
					->select();
				foreach ((array) $list as $key => $val) {
					if (! empty($val['article_id'])) {
						$id = explode(',', $val['article_id']);
						foreach ($id as $v)
							$list[$key]['articles'][] = M('wechat_media')->field('id, title, file, add_time')->where('id = ' . $v)->find();
					}
					$list[$key]['content'] = strip_tags(html_out($val['content']));
				}
			}
			
			foreach ((array) $list as $key => $val) {
				if ($val['size'] > (1024 * 1024)) {
					$list[$key]['size'] = round(($val['size'] / (1024 * 1024)), 1) . 'MB';
				} else {
					$list[$key]['size'] = round(($val['size'] / 1024), 1) . 'KB';
				}
			}
			
			// $total = M('wechat_media')->where($where)->count();	
			$this->assign('list', $list);
			$this->assign('type', $type);
			$this->display();
		}
	}


	/**
	 * 关注回复(subscribe)  8
	 */
	public function subscribe(){
		if (IS_POST) {
			$content_type = I('post.content_type');
			if ($content_type == 'text') {
				$data['content'] = I('post.content');
				$data['media_id'] = 0;
			} else {
				$data['media_id'] = I('post.media_id');
				$data['content'] = '';
			}
			$data['type'] = 'subscribe';
			//有数据存
			if (! empty($data['media_id']) || ! empty($data['content'])) {
				$id = $this->d->field('id')->where('type = "' . $data['type'] . '" and wechat_id =' . $this->wechat_id)->find();
				if (! empty($id)) {
					$this->d->data($data)->where('type = "' . $data['type'] . '" and wechat_id =' . $this->wechat_id)->save();
				} else {
					$data['wechat_id'] = $this->wechat_id;
					$this->d->data($data)->add();
				}
				$this->redirect('subscribe');
			} else {
				$this->message('请填写内容', NULL, 'error');
			}
		}
		
		/*   显示关注回复  */
		// 自动回复数据
		$subscribe = $this->d->where('type = "subscribe" AND wechat_id ='.$this->wechat_id)	->find();
		if (! empty($subscribe['media_id'])) {
			$subscribe['media'] = M('wechat_media')->field('file, type, file_name')->where('id = ' . $subscribe['media_id'])->find();
		}	
		$this->assign('subscribe', $subscribe);
		$this->display();
	}

	/**
	 * 消息回复(msg)  8
	 */
	public function msg(){
		if (IS_POST) {
			$content_type = I('post.content_type');
			if ($content_type == 'text') {
				$data['content'] = I('post.content');
				$data['media_id'] = 0;
			} else {
				$data['media_id'] = I('post.media_id');
				$data['content'] = '';
			}
			$data['type'] = 'msg';
			if (! empty($data['media_id']) || ! empty($data['content'])) {
				$id = $this->d
					->field('id')
					->where('type = "' . $data['type'] . '" and wechat_id =' . $this->wechat_id)
					->find();
				if (! empty($id)) {
					$this->d
						->data($data)
						->where('type = "' . $data['type'] . '" and wechat_id =' . $this->wechat_id)
						->save();
				} else {
					$data['wechat_id'] = $this->wechat_id;
					$this->d
						->data($data)
						->add();
				}
				$this->redirect('msg');
			} else {
				$this->message('请填写内容', NULL, 'error');
			}
		}
		// 自动回复数据
		$msg = $this->d->where('type = "msg" and wechat_id =' . $this->wechat_id)->find();
		if (! empty($msg['media_id'])) {
			$msg['media'] = M('wechat_media')
				->field('file, type, file_name')
				->where('id = ' . $msg['media_id'])
				->find();
		}
		$this->assign('msg', $msg);
		$this->display();
	}

	/**
	 * 关键词自动回复	8
	 */
	public function keywords(){
		if(IS_POST){
			$this->rule_edit();
			return;
		}
		$list = $this->d->field('id, rule_name, content, media_id, reply_type')
			->where('type = "keywords" and wechat_id =' . $this->wechat_id)
			->order('add_time desc')
			->select();
		foreach ((array) $list as $key => $val) {
			// 内容不是文本
			if (! empty($val['media_id'])) {
				$media = M('wechat_media')
					->field('title, file, file_name, type, content, add_time, article_id')
					->where('id = ' . $val['media_id'])
					->find();
				$media['content'] = strip_tags(html_out($media['content']));
				if (! empty($media['article_id'])) {
					$artids = explode(',', $media['article_id']);
					foreach ($artids as $v) {
						$list[$key]['medias'][] = M('wechat_media')
							->field('title, file, add_time')
							->where('id = ' . $v)
							->find();
					}
				} else {
					$list[$key]['media'] = $media;
				}
			}
			$keywords = M('wechat_rule_keywords')
				->field('rule_keywords')
				->where('rid = ' . $val['id'])
				->order('id desc')
				->select();
			$list[$key]['rule_keywords'] = $keywords;
			// 编辑关键词时显示
			if (! empty($keywords)) {
				$rule_keywords = array();
				foreach ($keywords as $k => $v) {
					$rule_keywords[] = $v['rule_keywords'];
				}
				$rule_keywords = implode(',', $rule_keywords);
				$list[$key]['rule_keywords_string'] = $rule_keywords;
			}
		}
		$this->assign('list', $list);
		$this->display();
	}

	/**
	 * 关键词回复添加规则8
	 */
	public function rule_edit()
	{
		if (IS_POST) {
			$id = I('post.id');
			$content_type = I('post.content_type');
			$rule_keywords = I('post.rule_keywords');
			// 主表数据
			$data['rule_name'] = I('post.rule_name');
			$data['media_id'] = I('post.media_id');
			$data['content'] = I('post.content');
			$data['reply_type'] = $content_type;
			if ($content_type == 'text') {
				$data['media_id'] = 0;
			} else {
				$data['content'] = '';
			}
			// $rs = Check::rule(array(
			// 	Check::must($data['rule_name']),
			// 	'请填写规则名称'
			// ), array(
			// 	Check::must($rule_keywords),
			// 	'请至少填写1个关键词'
			// ));
			// if ($rs !== true) {
			// 	$this->message($rs, NULL, 'error');
			// }
			// if (empty($data['content']) && empty($data['media_id'])) {
			// 	$this->message('请填写或选择回复内容', NULL, 'error');
			// }
			$data['type'] = 'keywords';
			
			if (! empty($id)) {
				$this->d->data($data)->where('id = ' . $id)->save();
				M('wechat_rule_keywords')->where('rid = ' . $id)->delete();
			} else {
				$data['add_time'] = time();
				$data['wechat_id'] = $this->wechat_id;
				$id = $this->d->data($data)->add();
			}
			// 编辑关键词
			$rule_keywords = explode(',', $rule_keywords);
			foreach ($rule_keywords as $val) {
				$kdata['rid'] = $id;
				$kdata['rule_keywords'] = $val;
				M('wechat_rule_keywords')->data($kdata)->add();
			}
			$this->redirect('keywords');
		}
	}

	/**
	 * 关键词回复规则删除  8
	 */
	public function reply_del()
	{
		$id = I('get.id');
		if (empty($id)) {
			$this->message('请选择', NULL, 'error');
		}
		$this->d->where('id = ' . $id)->delete();
		$this->redirect('keywords');
	}
}

<?php
defined('JETEE_PATH') or exit();

 /**
* 功能描述：微信公众平台管理
* @version 0.0.1 19:06 2017/2/8
*/
class Wechat_mediaAction extends Wechat_baseAction{
	public function __construct(){
		parent::__construct();
		$this->d = d('wechat_media');
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
        elseif(in_array(ACTION_NAME,explode(',','articles_list,get_article,article_news_del,media_edit,media_del,download'))) {
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
	 * 图文回复(news)  news  1
	 */
	public function article()
	{
		$wechat_media = M('wechat_media'); // 实例化User对象
		$where='wechat_id = ' . $this->wechat_id . ' and type = "news"';		
		$count	= $wechat_media->where($where)->count();// 查询满足要求的总记录数		
		$page=new BootstrapPage($count,12,'','Admin/'.MODULE_NAME.'/'.ACTION_NAME);
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $wechat_media->where($where)->field('id, title, file, content, add_time, sort, article_id')->order('sort asc, add_time desc')->page($page->nowPage,$page->listRows)->select();
		foreach ((array) $list as $key => $val) {
			// 多图文
			if (! empty($val['article_id'])) {
				$id = explode(',', $val['article_id']);
				foreach ($id as $v) {
					$list[$key]['articles'][] = $this->d->field('id, title, file, add_time')->where('id = ' . $v)->find();
				}
			}
			$list[$key]['add_time'] = date('Y年m月d日', $list[$key]['add_time']);
			$list[$key]['content'] = msubstr(strip_tags(html_out($val['content'])), 100);
		}

		$this->assign('page', $page->show());
		$this->assign('list', $list);
		$this->display();
	}

	/**
	 * 图文回复编辑 news 1
	 */
	public function article_edit()
	{
		if (IS_POST) {
			$id = I('post.id');
			$data = I('post.data',array(),null,1);
			$data['content'] = I('post.content');
			$pic_path = I('post.file_path');
			// 封面处理
			if ($_FILES['pic']['name']) {
				$result = $this->upload('wechat_pic');
				if ($result['error'] > 0) {
					$this->message($result['message'], NULL, 'error');
				}
				$data['file'] = substr($result['message'][0]['savepath'], 2) . $result['message'][0]['savename'];
				$data['file_name'] = $result['message'][0]['name'];
				$data['size'] = $result['message'][0]['size'];
			} else {
				$data['file'] = $pic_path;
			}

			$rs = Check::rule(array(
				Check::must($data['title']),
				L('title') . L('empty')
			), array(
				Check::must($data['file']),
				L('please_upload')
			), array(
				Check::must($data['content']),
				L('content') . L('empty')
			), array(
				Check::url($data['link']),
				L('link_err')
			));
			if ($rs !== true) {
				$this->message($rs, NULL, 'error');
			}
						
			$data['wechat_id'] = $this->wechat_id;
			$data['type'] = 'news';
			if (! empty($id)) {
				// 删除图片
				if ($pic_path != $data['file']) {
					@unlink($pic_path);
				}
				$data['edit_time'] = NOW_TIME;
				$this->d->data($data)->where('id = ' . $id)->save();
			} else {
				$data['add_time'] = NOW_TIME;
				$this->d->data($data)->add();
			}
			$this->message(L('edit') . L('success'), U('article'));
		}
		$id = I('get.id');
		if (! empty($id)) {
			$article = $this->d->where('id = ' . $id)->find();
			$this->assign('article', $article);
		}
		$this->display();
	}

	/**
	 * 多图文回复编辑  1
	 */
	public function article_edit_news()
	{
		if (IS_POST) {
			$id = I('post.id');
			$article_id = I('post.article');
			$data['sort'] = I('post.sort');
			if (is_array($article_id)) {
				$data['article_id'] = implode(',', $article_id);
				$data['wechat_id'] = $this->wechat_id;
				$data['type'] = 'news';
				
				if (! empty($id)) {
					$data['edit_time'] = NOW_TIME;
					$this->d->data($data)->where('id = ' . $id)->save();
				} else {
					$data['add_time'] = NOW_TIME;
					$this->d->data($data)->add();
				}
				$this->redirect('article');
			} else {
				$this->message('请重新添加', NULL, 'error');
			}
		}
		$id = I('get.id');
		if (! empty($id)) {
			$rs = $this->d->field('article_id, sort')->where('id = ' . $id)->find();
			if (! empty($rs['article_id'])) {
				$articles = array();
				$art = explode(',', $rs['article_id']);
				foreach ($art as $key => $val) {
					$articles[] = $this->d->field('id, title, file, add_time')->where('id = ' . $val)->find();
				}
				foreach ($articles as $key => $value) {
					$articles[$key]['add_time'] = date('Y年m月d日', $articles[$key]['add_time']);
				}
				$this->assign('articles', $articles);
			}
			$this->assign('sort', $rs['sort']);
		}
		
		$this->assign('id', $id);
		$this->display('Wechat_media_edit_news');
	}

	/**
	 * 单图文列表供多图文选择 1    不用权限
	 */
	public function articles_list()
	{

		$wechat_media = M('wechat_media'); // 实例化User对象
		$count	= $wechat_media->where('wechat_id = ' . $this->wechat_id . ' and type = "news" and article_id=""')->count();// 查询满足要求的总记录数		
		$page=new BootstrapPage($count,10,'','Admin/'.MODULE_NAME.'/'.ACTION_NAME);		
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$article = $wechat_media->where('wechat_id = ' . $this->wechat_id . ' and type = "news" and article_id=""')->field('id, title, file, content, add_time')->order('sort asc, add_time desc')->page($page->nowPage,$page->listRows)->select();

		if (! empty($article)) {
			foreach ($article as $k => $v) {
				$article[$k]['content'] = strip_tags(html_out($v['content']));
				$article[$k]['add_time'] = date('Y年m月d日', $article[$k]['add_time']);
			}
		}
		$this->assign('page', $page->show());
		$this->assign('article', $article);
		$this->display();
	}

	/**
	 * ajax获取图文信息 1	不用权限 
	 */
	public function get_article()
	{
		if (IS_AJAX) {
			$data = I('post.article');
			$article = array();
			if (is_array($data)) {
				$id = implode(',', $data);
				$article = $this->d->field('id, title, file, link, content, add_time')->where('id in (' . $id . ')')->order('sort asc, add_time desc')->select();
				foreach ($article as $key => $val) {
					$article[$key]['add_time'] = date('Y年m月d日', $val['add_time']);
					$article[$key]['content'] = html_out($val['content']);
				}
			}
			echo json_encode($article);
		}
	}

	/**
	 * 多图文回复清空    不用权限
	 */
	public function article_news_del(){
		$id = I('get.id');
		if (! empty($id)) {
			$this->d->data('article_id = ""')->where('id  = ' . $id)->save();
		}
		$this->redirect('article_edit_news');
	}

	/**
	 * 图文回复删除 1
	 */
	public function article_del()
	{
		$id = I('get.id');
		$pic = $this->d->field('file')->where('id = ' . $id)->getOne();
		if (empty($id)) {
			$this->message(L('select_please') . L('article'), NULL, 'error');
		}
		$this->d->where('id = ' . $id)->delete();
		if (! empty($pic)) {
			@unlink($pic);
		}
		$this->redirect('article');
	}

	/**
	 * 图片管理(image)  1
	 */
	public function picture(){
		if (IS_POST) {
			if ($_FILES['pic']['name']) {
				$result = $this->upload('wechat_pic', true);
				if ($result['error'] > 0) {
					$this->message($result['message'], NULL, 'error');
				}
				$data['file'] = substr($result['message'][0]['savepath'], 2) . $result['message'][0]['savename'];
				$data['thumb'] = substr($result['message'][0]['savepath'], 2) . 'thumb_' . $result['message'][0]['savename'];
				$data['file_name'] = $result['message'][0]['name'];
				$data['size'] = $result['message'][0]['size'];
				$data['type'] = 'image';
				$data['add_time'] = NOW_TIME;
				$data['wechat_id'] = $this->wechat_id;				
				$this->d->data($data)->add();				
				$this->redirect('picture');
			}
		}

		$wechat_media = M('wechat_media'); // 实例化User对象
		$count	= $wechat_media->where('wechat_id = ' . $this->wechat_id . ' and file is NOT NULL and (type = "image" or type = "news")')->count();// 查询满足要求的总记录数		
		$page=new BootstrapPage($count,12,'','Admin/'.MODULE_NAME.'/'.ACTION_NAME);
		$this->assign('page', $page->show());	
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $wechat_media->where('wechat_id = ' . $this->wechat_id . ' and file is NOT NULL and (type = "image" or type = "news")')->field('id, file, file_name, thumb, size')->order('add_time desc, sort asc')->page($page->nowPage,$page->listRows)->select();

		if (empty($list)) {
			$list = array();
		}
		foreach ($list as $key => $val) {
			if ($val['size'] > (1024 * 1024)) {
				$list[$key]['size'] = round(($val['size'] / (1024 * 1024)), 1) . 'MB';
			} else {
				$list[$key]['size'] = round(($val['size'] / 1024), 1) . 'KB';
			}
		}
		$this->assign('list', $list);
		$this->display();
	}

	
	
	/**
	 * 素材编辑文件名称 1   不用权限  
	 */
	public function media_edit()
	{
		if (IS_POST) {
			$id = I('post.id');
			$pic_name = I('post.file_name');

			$rs = Check::rule(array(
				Check::must($id),
				'请选择'
			), array(
				Check::must($pic_name),
				'请输入名称'
			));
			if ($rs !== true) {
				exit(json_encode(array(
					'status' => 0,
					'error' => $rs
				)));
			}
			$data['file_name'] = $pic_name;
			$data['edit_time'] = NOW_TIME;
			$num = $this->d->data($data)->where('id = ' . $id)->save();			
			exit(json_encode(array(
				'status' => $num
			)));
		}
		$id = I('get.id');
		$pic = $this->d->field('id, file_name')->where('id = ' . $id)->find();
		if (empty($pic)) {
			$this->redirect(I('get.url'));
		}
		$this->assign('pic', $pic);
		$this->display();
	}

	/**
	 * 素材删除 1  不用权限
	 */
	public function media_del()
	{
		$id = I('get.id');
		if (empty($id)) {
			$this->message('请选择', NULL, 'error');
		}
		$pic = $this->d->field('file, thumb')->where('id = ' . $id)->find();
		if (! empty($pic)) {
			$this->d->where('id = ' . $id)->delete();
		}
		if (file_exists($pic['file'])) {
			@unlink($pic['file']);
		}
		if (file_exists($pic['thumb'])) {
			@unlink($pic['thumb']);
		}
		$this->redirect(I('get.url'));
	}

	/**
	 * 下载 1   不用权限
	 */
	public function download()
	{
		$id = I('get.id');
		$pic = $this->d->field('file, file_name')->where('id = ' . $id)->find();
		$filename = $pic['file'];
		if (file_exists($filename)) {
			Http::download($filename,$pic['file_name'],3600);
		} else {
			$this->message('文件不存在', NULL, 'error');
		}
	}



	/**
	 * 语音 1  
	 */
	public function voice()
	{
		if (IS_POST) {
			if ($_FILES['voice']['name']) {
				$result = $this->upload('voice');
				if ($result['error'] > 0) {
					$this->message($result['message'], NULL, 'error');
				}
				$data['file'] = substr($result['message'][0]['savepath'], 2) . $result['message'][0]['savename'];
				$data['file_name'] = $result['message'][0]['name'];
				$data['size'] = $result['message'][0]['size'];
				$data['type'] = 'voice';
				$data['add_time'] = NOW_TIME;
				$data['wechat_id'] = $this->wechat_id;
				$this->d->data($data)->add();
				$this->redirect('voice');
			}
		}

		$wechat_media = M('wechat_media'); // 实例化User对象
		$count	= $wechat_media->where('wechat_id = ' . $this->wechat_id . ' and type = "voice"')->count();// 查询满足要求的总记录数
		$page=new BootstrapPage($count,12,'','Admin/'.MODULE_NAME.'/'.ACTION_NAME);
		$this->assign('page', $page->show());		
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $wechat_media->where('wechat_id = ' . $this->wechat_id . ' and type = "voice"')->field('id, file, file_name, size')->order('add_time desc, sort asc')->page($page->nowPage,$page->listRows)->select();

		if (empty($list)) {
			$list = array();
		}
		foreach ($list as $key => $val) {
			if ($val['size'] > (1024 * 1024)) {
				$list[$key]['size'] = round(($val['size'] / (1024 * 1024)), 1) . 'MB';
			} else {
				$list[$key]['size'] = round(($val['size'] / 1024), 1) . 'KB';
			}
		}
		$this->assign('list', $list);
		$this->display();
	}

	/**
	 * 视频  1
	 */
	public function video()
	{
		$wechat_media = M('wechat_media'); // 实例化User对象
		$count	= $wechat_media->where('wechat_id = ' . $this->wechat_id . ' and type = "video"')->count();// 查询满足要求的总记录数
		
		$page=new BootstrapPage($count,12,'','Admin/'.MODULE_NAME.'/'.ACTION_NAME);
		$this->assign('page', $page->show());	
		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
		$list = $wechat_media->where('wechat_id = ' . $this->wechat_id . ' and type = "video"')->field('id, file, file_name, size')->order('add_time desc, sort asc')->page($page->nowPage,$page->listRows)->select();

		if (empty($list)) {
			$list = array();
		}
		foreach ($list as $key => $val) {
			if ($val['size'] > (1024 * 1024)) {
				$list[$key]['size'] = round(($val['size'] / (1024 * 1024)), 1) . 'MB';
			} else {
				$list[$key]['size'] = round(($val['size'] / 1024), 1) . 'KB';
			}
		}
		
		$this->assign('list', $list);
		$this->display();
	}

	/**
	 * 视频编辑 1
	 */
	public function video_edit(){
		if (IS_POST) {
			$data = I('post.data');
			$id = I('post.id',0,'intval');

			if (empty($data['file']) || empty($data['file_name']) || empty($data['size'])) {
				$this->message('请上传视频', NULL, 'error');
			}
			if (empty($data['title'])) {
				$this->message('请填写标题', NULL, 'error');
			}
			$data['type'] = 'video';
			$data['wechat_id'] = $this->wechat_id;
			if (! empty($id)) {
				// 删除原来的视频
				$video_path = $this->d->where('id = '.$id)->getField('file');
				if ($video_path != $data['file']) {
					@unlink($video_path);
				}
				$data['edit_time'] = NOW_TIME;
				$this->d->data($data)->where('id = ' . $id)->save();
			} else {
				$data['add_time'] = NOW_TIME;
				$this->d->data($data)->add();
			}
			
			$this->redirect('video');
		}
		$id = I('get.id');
		if (! empty($id)) {
			$video = $this->d->field('id, file, file_name, size, title, content')->where('id = ' . $id)->find();			
			$this->assign('video', $video);
		}
		$this->display();
	}

	/**
	 * 视频上传webuploader 1
	 */
	public function video_upload()
	{
		if (IS_POST && ! empty($_FILES['file']['name'])) {
			$vid = I('post.vid');
			if (! empty($vid)) {
				$file = $this->d->field('file')->where('id = ' . $vid)->find();
				if (file_exists($file)) {
					@unlink($file);
				}
			}
			$result = $this->upload('video');
			if ($result['error'] > 0) {
				$data['errcode'] = 1;
				$data['errmsg'] = $result['message'];
				echo json_encode($data);
				exit();
			}
			$data['errcode'] = 0;
			$data['file'] = substr($result['message'][0]['savepath'], 2) . $result['message'][0]['savename'];
			$data['file_name'] = $result['message'][0]['name'];
			$data['size'] = $result['message'][0]['size'];
			echo json_encode($data);
		}
	}

	//$upload_dir上传的目录名  私有函数
	protected function upload($upload_dir = 'images', $thumb = false, $width = 220, $height = 220) {
		$upload = new UploadFile();
		//设置上传文件大小
		$upload->maxSize = 1024 * 1024 * 10; //最大10M,但最佳5M以内。
		//设置上传文件类型
		$upload->allowExts = explode(',', 'jpg,jpeg,gif,png,bmp,mp3,amr,mp4');
		//生成缩略图
		$upload->thumb = $thumb;
		//缩略图大小
		$upload->thumbMaxWidth = $width;
		$upload->thumbMaxHeight = $height;

		//设置附件上传目录
		$upload->savePath = UPLOADS_PATH.$upload_dir.'/';
		make_dir($upload->savePath);
		if (!$upload->upload()) {
			//捕获上传异常
			return array('error' => 1, 'message' => $upload->getErrorMsg());
		} else {
			//取得成功上传的文件信息
			return array('error' => 0, 'message' => $upload->getUploadFileInfo());
		}
	}
}

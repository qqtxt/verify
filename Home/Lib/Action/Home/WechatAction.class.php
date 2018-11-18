<?php
defined('JETEE_PATH') or exit();
/** 
* 微信服务控制器
* @link http://www.jetee.cn/
* @version 0.0.1 10:30 2016/11/22
*/
class WechatAction{
	private $weObj = '';
	private $orgid = '';
	private $wechat_id = '';
	private $d;
	/**
	 * 构造函数
	 */
	public function __construct(){

		$this->d=D('Wechat');
		// 获取公众号配置
		$this->orgid = I('get.orgid');
		if (! empty($this->orgid)) {
			$wxinfo = $this->get_config($this->orgid);            
			$config['token'] = $wxinfo['token'];
			$config['appid'] = $wxinfo['appid'];
			$config['appsecret'] = $wxinfo['appsecret'];
			$this->weObj = new Wechat($config);
			$this->weObj->valid();
			$this->wechat_id = $wxinfo['id'];
		}


	   
	}
	/**
	 * 获取公众号配置
	 *
	 * @param string $orgid            
	 * @return array
	 */
	private function get_config($orgid)
	{
		$config = $this->d->field('id, token, appid, appsecret')->where('orgid = "' . $orgid . '" and status = 1')->find();
		if (empty($config)) {
			$config = array();
		}
		return $config;
	}
	/**
	 * 执行方法
	 */
	public function index()
	{
		//file_put_contents('jetee.txt',print_r('1',true));

		// 事件类型
		$type = $this->weObj->getRev()->getRevType();
		$wedata = $this->weObj->getRev()->getRevData();
		$keywords = '';
		if ($type == Wechat::MSGTYPE_TEXT) {
			$keywords = $wedata['Content'];
		}

		elseif ($type == Wechat::MSGTYPE_EVENT) {
			if ('subscribe' == $wedata['Event']) {		 //多次扫二维码 不会有关注事件			
				// 用户扫描带参数二维码(未关注)    多次扫码关注不会发推送过来
				if (isset($wedata['Ticket']) && ! empty($wedata['Ticket'])) {
					$scene_id = $this->weObj->getRevSceneId();
					$flag = true;
					// 关注
					$this->subscribe($wedata['FromUserName'], $scene_id);
					// 关注时回复信息
					$this->msg_reply('subscribe');
				}
				else{
					// 关注
					$this->subscribe($wedata['FromUserName']);
					// 关注时回复信息
					$this->msg_reply('subscribe');
					exit;
				}                
			}
			elseif ('unsubscribe' == $wedata['Event']) {
				// 取消关注
				$this->unsubscribe($wedata['FromUserName']);
				exit();
			}
			elseif ('MASSSENDJOBFINISH' == $wedata['Event']) {
				// 群发结果
				$data['status'] = $wedata['Status'];
				$data['totalcount'] = $wedata['TotalCount'];
				$data['filtercount'] = $wedata['FilterCount'];
				$data['sentcount'] = $wedata['SentCount'];
				$data['errorcount'] = $wedata['ErrorCount'];
				// 更新群发结果
				M('wechat_mass_history')->data($data)->where('msg_id = "' . $wedata['MsgID'] . '"')->update();
				exit();
			}
			elseif ('CLICK' == $wedata['Event']) {
				// 点击菜单
				$keywords = $wedata['EventKey'];
			}
			elseif ('VIEW' == $wedata['Event']) {
				$this->redirect($wedata['EventKey']);
			}
			elseif ('SCAN' == $wedata['Event']) {
				$scene_id = $this->weObj->getRevSceneId();
			}
		}
		else {
			$this->msg_reply('msg');
			exit();
		}
		//扫描二维码
		if(!empty($scene_id)){
			$qrcode_fun = M('wechat_qrcode')->where('scene_id = "'.$scene_id.'"')->getField('function');
			//扫码引荐
			if(!empty($qrcode_fun) && isset($flag)){
				//增加扫描量
				M('wechat_qrcode')->data('scan_num = scan_num + 1')->where('scene_id = "'.$scene_id.'"')->save();
			}
			$keywords = $qrcode_fun;
			//file_put_contents('jetee.txt',$keywords);
		}
		// 回复
		if (! empty($keywords)) {
			$keywords = escape($keywords);
			//记录用户操作信息
			$this->record_msg($wedata['FromUserName'], $keywords);
			// 功能插件
			$rs1 = $this->get_function($wedata['FromUserName'], $keywords);
			if (empty($rs1)) {

				// 关键词回复
				$rs2 = $this->keywords_reply($keywords);
				if(empty($rs2)){
					// 消息自动回复
					$this->msg_reply('msg');
				}
				
			}
			
		}
	}
	/**
	 * 关键词回复  素材回复还没测试  应当不会出错
	 *
	 * @param string $keywords            
	 * @return boolean
	 */
	private function keywords_reply($keywords)
	{
		$endrs = false;
		$result=M('wechat_reply')->alias('r')->join('__WECHAT_RULE_KEYWORDS__ k on r.id = k.rid')->field('r.content, r.media_id, r.reply_type')->where('k.rule_keywords = "' . $keywords . '" and r.wechat_id = ' . $this->wechat_id)->order('r.add_time desc')->limit(1)->select();
		if (! empty($result)) {
			// 素材回复
			if (! empty($result[0]['media_id'])) {
				$mediaInfo = M('wechat_media')->field('id, title, content, digest, file, type, file_name, article_id, link')->where('id = ' . $result[0]['media_id'])->find();
				
				// 回复数据重组
				if ($result[0]['reply_type'] == 'image' || $result[0]['reply_type'] == 'voice') {
					// 上传多媒体文件
					$rs = $this->weObj->uploadMedia(array(
						'media' => '@' .  $_SERVER['DOCUMENT_ROOT'].__ROOT__ .'/' . $mediaInfo['file']
					), $result[0]['reply_type']);
					
					$replyData = array(
						'ToUserName' => $this->weObj->getRev()->getRevFrom(),
						'FromUserName' => $this->weObj->getRev()->getRevTo(),
						'CreateTime' => time(),
						'MsgType' => $rs['type'],
						ucfirst($rs['type']) => array(
							'MediaId' => $rs['media_id']
						)
					);
					// 回复
					$this->weObj->reply($replyData);
					$endrs = true;
				} elseif ('video' == $result[0]['reply_type']) {
					// 上传多媒体文件
					$rs = $this->weObj->uploadMedia(array(
						'media' => '@' . $_SERVER['DOCUMENT_ROOT'].__ROOT__ .'/' . $mediaInfo['file']
					), $result[0]['reply_type']);
					
					$replyData = array(
						'ToUserName' => $this->weObj->getRev()->getRevFrom(),
						'FromUserName' => $this->weObj->getRev()->getRevTo(),
						'CreateTime' => time(),
						'MsgType' => $rs['type'],
						ucfirst($rs['type']) => array(
							'MediaId' => $rs['media_id'],
							'Title' => $replyInfo['media']['title'],
							'Description' => strip_tags($replyInfo['media']['content'])
						)
					);
					// 回复
					$this->weObj->reply($replyData);
					$endrs = true;
				} elseif ('news' == $result[0]['reply_type']) {
					// 图文素材
					$articles = array();
					if (! empty($mediaInfo['article_id'])) {
						$artids = explode(',', $mediaInfo['article_id']);
						foreach ($artids as $key => $val) {
							$artinfo = m('wechat_media')->field('id, title, digest, file, content, link')->where('id = ' . $val)->find();
							//$artinfo['content'] = strip_tags(html_out($artinfo['content']));
							$articles[$key]['Title'] = $artinfo['title'];
							$articles[$key]['Description'] = $artinfo['digest'];
							$articles[$key]['PicUrl'] = __URL__ . '/' . $artinfo['file'];
							$articles[$key]['Url'] = empty($artinfo['link']) ?  U('article/wechat_news_info', array('id'=>$artinfo['id'])) : $artinfo['link'];
						}
					} else {
						$articles[0]['Title'] = $mediaInfo['title'];
						//$articles[0]['Description'] = strip_tags(html_out($mediaInfo['content']));
						$articles[0]['Description'] = $mediaInfo['digest'];
						$articles[0]['PicUrl'] = __URL__ . '/' . $mediaInfo['file'];
						$articles[0]['Url'] = empty($mediaInfo['link']) ?  U('article/wechat_news_info', array('id'=>$mediaInfo['id'])) : $mediaInfo['link'];
					}
					// 回复
					$this->weObj->news($articles)->reply();
					//记录用户操作信息
					$this->record_msg($this->weObj->getRev()->getRevTo(), '图文信息', 1);
					$endrs = true;
				}
			}
			else {
				// 文本回复
				$this->weObj->text($result[0]['content'])->reply();
				//记录用户操作信息
				$this->record_msg($this->weObj->getRev()->getRevTo(), $result[0]['content'], 1);
				$endrs = true;
			}
		}
		return $endrs;
	}	
	/**
	 * 功能变量查询  还没改好 应该不会出错误
	 *
	 * @param unknown $tousername            
	 * @param unknown $fromusername            
	 * @param unknown $keywords            
	 * @return boolean
	 */
	public function get_function($fromusername, $keywords)
	{
		$return = false;
		$rs = M('wechat_extend')->field('name, command, config')->where('keywords like "%' . $keywords . '%" and enable = 1 and wechat_id = ' . $this->wechat_id)->order('id asc')->find();

		$file =  './plugins/wechat/' . $rs['command'] . '/' . $rs['command'] . '.class.php';

		// file_put_contents('abc.log', "第".__LINE__."行---".json_encode($replyInfo['media'])."---".date ( "Y-m-d H:i:s" ,time())."\r\n",FILE_APPEND);

		if (file_exists($file)) {
			file_put_contents('abc.log', "第".__LINE__."行---".session('openid')."---".date ( "Y-m-d H:i:s" ,time())."\r\n",FILE_APPEND);
			require_once($file);
			$wechat = new $rs['command']();
			$data = $wechat->show($fromusername, $rs,$this->weObj);
			
			file_put_contents('abc.log', "第".__LINE__."行---".$file."---".date ( "Y-m-d H:i:s" ,time())."\r\n",FILE_APPEND);
			if (! empty($data)) {
				// 数据回复类型
				if ($data['type'] == 'text') {
					$this->weObj->text($data['content'])->reply();
					//记录用户操作信息
					$this->record_msg($fromusername, $data['content'], 1);
				} elseif ($data['type'] == 'news') {
					$this->weObj->news($data['content'])->reply();
					//记录用户操作信息
					$this->record_msg($fromusername, '图文消息', 1);
				} elseif ($data['type'] == 'image') {
					$this->weObj->image($data['mediaid'])->reply();
					//记录用户操作信息
					$this->record_msg($fromusername, '图片消息', 1);
				}
				$return = true;
			}
		}
		return $return;
	}	
	/**
	 * 被动关注，消息回复1   回复 media_id  还没做好  应当不会出错
	 *
	 * @param string $type            
	 * @param string $return            
	 */
	private function msg_reply($type, $return = 0)
	{	
		//msg
		$replyInfo = M('wechat_reply')->field('content, media_id')->where('type = "' . $type . '" and wechat_id = ' . $this->wechat_id)->find();

		if (! empty($replyInfo)) {
			if (! empty($replyInfo['media_id'])) {
				$replyInfo['media'] = M('wechat_media')->field('title, content, file, type, file_name')->where('id = ' . $replyInfo['media_id'])->find();

				if ($replyInfo['media']['type'] == 'news') {
					$replyInfo['media']['type'] = 'image';
				}
				
				// 上传多媒体文件
				$rs = $this->weObj->uploadMedia(array(
					'media' => '@' . $_SERVER['DOCUMENT_ROOT'].__ROOT__ .'/'. $replyInfo['media']['file']
				), $replyInfo['media']['type']);

				// 回复数据重组
				if ($rs['type'] == 'image' || $rs['type'] == 'voice') {
					$replyData = array(
						'ToUserName' => $this->weObj->getRev()->getRevFrom(),
						'FromUserName' => $this->weObj->getRev()->getRevTo(),
						'CreateTime' => time(),
						'MsgType' => $rs['type'],
						ucfirst($rs['type']) => array(
							'MediaId' => $rs['media_id']
						)
					);
				} elseif ('video' == $rs['type']) {
					$replyData = array(
						'ToUserName' => $this->weObj->getRev()->getRevFrom(),
						'FromUserName' => $this->weObj->getRev()->getRevTo(),
						'CreateTime' => time(),
						'MsgType' => $rs['type'],
						ucfirst($rs['type']) => array(
							'MediaId' => $rs['media_id'],
							'Title' => $replyInfo['media']['title'],
							'Description' => strip_tags($replyInfo['media']['content'])
						)
					);
				}
				$this->weObj->reply($replyData);
				//记录用户操作信息
				$this->record_msg($this->weObj->getRev()->getRevTo(), '图文信息:'.$replyInfo['media_id'], 1);
			}
			else {
				// 文本回复
				if($replyInfo['content']){
					$this->weObj->text($replyInfo['content'])->reply();
					//记录用户操作信息
					$this->record_msg($this->weObj->getRev()->getRevTo(), $replyInfo['content'], 1);
				}
			}
		}
	}
	/**
	 * 关注处理1
	 *
	 * @param array $info            
	 */
	private function subscribe($openid = '', $scene_id = 0){

		if(!empty($openid)){
			// 用户信息
			$info = $this->weObj->getUserInfo($openid);
			if (empty($info)) {
				$info = array();
			}
			// 查找用户是否存在
			$where['openid'] = $openid;
			$wu_id = M('wechat_user')->where($where)->getField('wu_id');
			// 未找到 注册  
			if (empty($wu_id)) {
				$uid = 0;
				//查看公众号是否绑定
				if(isset($info['unionid'])){
					$uid = M('wechat_user')->where(array('unionid'=>$info['unionid']))->getField('uid');
				}
				
				// 获取用户所在分组ID
				$data['group_id'] = !empty($info['groupid']) ? $info['groupid'] : $this->weObj->getUserGroup($openid);
				// 获取被关注公众号信息
				#$data['admin_id'] = $admin_id;
				$data['wechat_id'] = $this->wechat_id;
				$data['subscribe'] = $info['subscribe'];
				$data['openid'] = $info['openid'];
				$data['nickname'] = $info['nickname'];
				$data['sex'] = $info['sex'];
				$data['city'] = $info['city'];
				$data['country'] = $info['country'];
				$data['province'] = $info['province'];
				$data['language'] = $info['country'];
				$data['headimgurl'] = $info['headimgurl'];
				$data['subscribe_time'] = $info['subscribe_time'];
				$data['remark'] = $info['remark'];
				$data['unionid'] = !empty($info['unionid']) ? $info['unionid'] : '';
				// 用户注册
				$scene_id = empty($scene_id) ? 0 : $scene_id;
				$scene_id = m('wechat_user')->where(array('wu_id'=>$scene_id))->getField('wu_id');
				$scene_id = empty($scene_id) ? 0 : $scene_id;
				$data['up_wu_id'] =$scene_id;
				$data['uid'] = $uid;//关联别的表占时定义1如果为0或无值有的功能不能用
				M('wechat_user')->data($data)->add();
				
			} 
			//有找到更新   应该是取消来再关注   才更新  多次扫二维码 不会有关注事件
			else {
				$data['wu_id'] = $wu_id;
				// 获取用户所在分组ID
				$data['group_id'] = !empty($info['groupid']) ? $info['groupid'] : $this->weObj->getUserGroup($openid);
				// 获取被关注公众号信息
				$data['wechat_id'] = $this->wechat_id;
				$data['subscribe'] = $info['subscribe'];
				$data['nickname'] = $info['nickname'];
				$data['sex'] = $info['sex'];
				$data['city'] = $info['city'];
				$data['country'] = $info['country'];
				$data['province'] = $info['province'];
				$data['language'] = $info['country'];
				$data['headimgurl'] = $info['headimgurl'];
				$data['subscribe_time'] = $info['subscribe_time'];
				$data['remark'] = $info['remark'];
				$scene_id = empty($scene_id) ? 0 : $scene_id;
				$scene_id = m('wechat_user')->where(array('wu_id'=>$scene_id))->getField('wu_id');
				$scene_id = empty($scene_id) ? 0 : $scene_id;
				$data['up_wu_id'] =$scene_id;//随意更换上级
				$data['unionid'] = !empty($info['unionid']) ? $info['unionid'] : '';

				M('wechat_user')->data($data)->save();
			}
		}
	}

	/**
	 * 取消关注1
	 *
	 * @param string $openid            
	 */
	public function unsubscribe($openid = '')
	{
		$where['openid'] = $openid;
		$data['subscribe'] = 0;
		M('wechat_user')->data($data)->where($where)->save();
	}
	/**
	 * 记录用户操作信息  $iswechat是否公众号回复1
	 */
	 public function record_msg($openid, $msg, $iswechat = 0){
		$wu_id = M('wechat_user')->where(array('openid'=>$openid))->getField('wu_id');
		if($wu_id){
			$data['wu_id'] = $wu_id;
			$data['msg'] = $msg;
			$data['send_time'] = NOW_TIME;
			//是公众号回复
			if($iswechat){
				$data['iswechat'] = 1;
			}
			M('wechat_message')->data($data)->add();
		}
	 }	
}
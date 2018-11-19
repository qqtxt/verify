<?

		db()->beginTrans();
		db()->commitTrans();
		db()->rollBackTrans();
		try{
		}catch(PDOException $e){
			db()->rollBackTrans();
			$msg=array('status'=>4,'data'=>'服务器忙，请稍后重试');
		}
		


		$count = db()->single('select count(*) from '.C('DB_PREFIX').'info '.$where);
		$rows=db()->query('select iid,title,type,view_count,collect_count,add_time,status from '.C('DB_PREFIX').'info '.$where." limit {$page->firstRow},{$page->listRows}");//p($rows);exit;


-- 公众号列表 {
DROP TABLE IF EXISTS `je_wechat`;
CREATE TABLE IF NOT EXISTS `je_wechat` (
	`id` 			int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name`			varchar(255) NOT NULL DEFAULT ''		COMMENT '公众号名称  如阶梯互联',
	`orgid`			varchar(255) NOT NULL DEFAULT ''		COMMENT '公众号原始ID',
	`weixin`		varchar(255) NOT NULL DEFAULT ''		COMMENT '微信号		',
	`token`			varchar(255) NOT NULL DEFAULT ''		COMMENT 'Token',
	`appid`			varchar(255) NOT NULL DEFAULT ''		COMMENT 'AppID',
	`appsecret`		varchar(255) NOT NULL DEFAULT ''		COMMENT 'AppSecret',
	`mchid`			varchar(255) NOT NULL DEFAULT ''		COMMENT '商户号  1900000109',
	`mchkey`		varchar(255) NOT NULL DEFAULT ''		COMMENT 'key为商户平台设置的密钥key',
	`type`			int(1) unsigned NOT NULL DEFAULT '0'	COMMENT '公众号类型: 0未认证的公众号 1订阅号  2服务号  3认证服务号 认证媒体/政府订阅号 认证订阅号  ',
	`oauth_status` 	tinyint(1) unsigned NOT NULL DEFAULT '0'  	COMMENT '是否开启微信登录',
	`oauth_name`	varchar(100) NOT NULL DEFAULT ''		 	COMMENT '',
	`oauth_redirecturi`	varchar(255) NOT NULL DEFAULT ''		 COMMENT '微信授权回调域名',
	`oauth_count`		int(11) unsigned NOT NULL DEFAULT '0'	COMMENT '微信授权次数',
	`add_time`			int(11) unsigned NOT NULL DEFAULT '0'	COMMENT '添加时间',
	`sort`				int(10) unsigned NOT NULL DEFAULT '0'	COMMENT '排序',
	`status`			int(1) unsigned NOT NULL DEFAULT '1'	COMMENT '状态 0关闭 1开启',
	`default_wx`		int(1) NOT NULL DEFAULT '1'				COMMENT '1为默认使用，0为不默认',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 	COMMENT '公众号列表';

INSERT INTO `je_wechat` (`id`, `name`, `orgid`, `weixin`, `token`, `appid`, `appsecret`, `type`, `oauth_status`, `oauth_name`, `oauth_redirecturi`, `oauth_count`, `add_time`, `sort`, `status`, `default_wx`) VALUES
(1, '阶梯互联', 'gh_98aceafcad39', '', 'l4FRxId46PP6XFYtvzSZs8sP2V0KWEFp', 'wx07db70702d9aec7c', 'b273be0dfb6c49b94cf8b87c59257eb4', 2, 0, '', 'http://zc.ma863.com', 0, 1479711047, 0, 1, 1);
-- }


-- 发送客服消息 {
DROP TABLE IF EXISTS `je_wechat_custom_message`;
CREATE TABLE IF NOT EXISTS `je_wechat_custom_message` (
	`id`		int(10) unsigned NOT NULL AUTO_INCREMENT,
	`wu_id`		int(10) unsigned NOT NULL DEFAULT '0' 		COMMENT 'wechat_user id',
	`msg`		varchar(255)  NOT NULL DEFAULT '' 			COMMENT '信息内容',
	`iswechat`	smallint(1) unsigned  DEFAULT '0' 			COMMENT '哪家wechat_id发的',
	`send_time`	int(11) unsigned NOT NULL DEFAULT '0' 		COMMENT '发送时间',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT '发送客服消息';
-- }

DROP TABLE IF EXISTS `je_wechat_extend`;
CREATE TABLE IF NOT EXISTS `je_wechat_extend` (
  `id` 			int(11) NOT NULL AUTO_INCREMENT,
  `name` 		varchar(100) DEFAULT NULL 		COMMENT '功能名称',
  `keywords` 	varchar(20) DEFAULT NULL 		COMMENT '关键词',
  `command` 	varchar(255) DEFAULT NULL 		COMMENT '扩展词',
  `config` 		text 							COMMENT '配置信息',
  `type` 		varchar(20) DEFAULT NULL		COMMENT '',
  `enable` 		int(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否安装，1为已安装，0未安装',
  `author` 		varchar(100) DEFAULT NULL,
  `website` 	varchar(100) DEFAULT NULL,
  `wechat_id` 	int(10) unsigned NOT NULL 		COMMENT '公众号id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `je_wechat_extend` (`id`, `name`, `keywords`, `command`, `config`, `type`, `enable`, `author`, `website`, `wechat_id`) VALUES
(1, '', NULL, 'pay_remind', 'a:1:{s:8:"template";s:0:"";}', NULL, 0, NULL, NULL, 1),
(2, '', NULL, 'order_remind', 'a:1:{s:8:"template";s:0:"";}', NULL, 0, NULL, NULL, 1),
(3, NULL, NULL, 'register_remind', 'a:4:{s:8:"user_pre";s:0:"";s:7:"pwd_pre";s:0:"";s:8:"pwd_rand";s:0:"";s:8:"template";s:0:"";}', NULL, 0, NULL, NULL, 1),
(4, '', NULL, 'send_remind', 'a:1:{s:8:"template";s:0:"";}', NULL, 0, NULL, NULL, 1),
(5, NULL, NULL, 'kefu', 'a:1:{s:8:"customer";s:5:"SDDDD";}', NULL, 0, NULL, NULL, 1),
(6, '生成推广海报', 'ad', 'ad', NULL, NULL, 1, NULL, NULL, 1),
(15, '绑定手机号', 'bdsjh,绑定手机号', 'bdsjh', 'a:6:{s:12:"point_status";s:1:"0";s:16:"rank_point_value";s:0:"";s:15:"pay_point_value";s:0:"";s:9:"point_num";s:0:"";s:14:"point_interval";s:5:"86400";s:8:"media_id";s:2:"35";}', 'function', 1, 'JETEE TEAM', 'http://www.jetee.cn', 1),
(8, '精品', 'best', 'best', 'a:4:{s:12:"point_status";s:1:"1";s:11:"point_value";s:0:"";s:9:"point_num";s:0:"";s:14:"point_interval";s:5:"86400";}', 'function', 0, 'JETEE TEAM', 'http://www.jetee.cn', 1),
(9, '关注送红包', 'bonus,关注送红包', 'bonus', 'a:7:{s:12:"bonus_status";s:1:"1";s:5:"bonus";s:2:"10";s:12:"point_status";s:1:"1";s:11:"point_value";s:0:"";s:9:"point_num";s:0:"";s:14:"point_interval";s:5:"86400";s:8:"media_id";a:1:{s:2:"id";s:2:"12";}}', 'function', 1, 'JETEE TEAM', 'http://www.jetee.cn', 1),
(13, '刮刮卡', '刮刮卡,ggk', 'ggk', 'a:16:{s:12:"point_status";s:1:"1";s:11:"point_value";s:3:"200";s:9:"point_num";s:5:"20000";s:14:"point_interval";s:5:"86400";s:10:"people_num";i:21;s:9:"prize_num";s:6:"200000";s:9:"starttime";s:10:"2017-03-27";s:7:"endtime";s:10:"2017-04-30";s:11:"prize_level";s:0:"";s:10:"prize_name";s:0:"";s:11:"prize_count";s:0:"";s:10:"prize_prob";s:0:"";s:11:"description";s:4:"2222";s:8:"media_id";s:2:"34";s:7:"haslist";s:1:"1";s:5:"prize";a:5:{i:0;a:4:{s:11:"prize_level";s:9:"一等奖";s:10:"prize_name";s:9:"法拉利";s:11:"prize_count";s:2:"20";s:10:"prize_prob";s:2:"20";}i:1;a:4:{s:11:"prize_level";s:9:"二等奖";s:10:"prize_name";s:2:"QQ";s:11:"prize_count";s:2:"20";s:10:"prize_prob";s:2:"30";}i:2;a:4:{s:11:"prize_level";s:9:"三等奖";s:10:"prize_name";s:6:"咖啡";s:11:"prize_count";s:3:"100";s:10:"prize_prob";s:2:"60";}i:3;a:4:{s:11:"prize_level";s:9:"四等奖";s:10:"prize_name";s:9:"反粉丝";s:11:"prize_count";s:4:"2000";s:10:"prize_prob";s:2:"80";}i:4;a:4:{s:11:"prize_level";s:9:"四等奖";s:10:"prize_name";s:12:"谢谢参与";s:11:"prize_count";s:8:"20000000";s:10:"prize_prob";s:2:"90";}}}', 'function', 1, 'JETEE TEAM', 'http://www.jetee.cn', 1),
(11, '砸金蛋', '砸金蛋,zjd', 'zjd', 'a:16:{s:12:"point_status";s:1:"1";s:11:"point_value";s:3:"100";s:9:"point_num";s:6:"100000";s:14:"point_interval";s:5:"86400";s:10:"people_num";s:1:"0";s:9:"prize_num";s:2:"20";s:9:"starttime";s:10:"2017-02-09";s:7:"endtime";s:10:"2017-08-24";s:11:"prize_level";s:0:"";s:10:"prize_name";s:0:"";s:11:"prize_count";s:0:"";s:10:"prize_prob";s:0:"";s:11:"description";s:0:"";s:8:"media_id";s:2:"36";s:7:"haslist";s:1:"1";s:5:"prize";a:4:{i:0;a:4:{s:11:"prize_level";s:9:"一等奖";s:10:"prize_name";s:9:"法拉利";s:11:"prize_count";s:1:"2";s:10:"prize_prob";s:2:"20";}i:1;a:4:{s:11:"prize_level";s:9:"二等奖";s:10:"prize_name";s:6:"大奔";s:11:"prize_count";s:1:"2";s:10:"prize_prob";s:2:"30";}i:2;a:4:{s:11:"prize_level";s:9:"三等级";s:10:"prize_name";s:9:"发酒疯";s:11:"prize_count";s:4:"1000";s:10:"prize_prob";s:2:"40";}i:3;a:4:{s:11:"prize_level";s:9:"四等奖";s:10:"prize_name";s:12:"谢谢参与";s:11:"prize_count";s:5:"10000";s:10:"prize_prob";s:2:"80";}}}', 'function', 1, 'JETEE TEAM', 'http://www.jetee.cn', 1),
(12, '大转盘', '大转盘,dzp', 'dzp', 'a:16:{s:12:"point_status";s:1:"0";s:11:"point_value";s:0:"";s:9:"point_num";s:0:"";s:14:"point_interval";s:5:"86400";s:10:"people_num";i:102;s:9:"prize_num";s:5:"10000";s:9:"starttime";s:10:"2017-03-27";s:7:"endtime";s:10:"2017-04-30";s:11:"prize_level";s:0:"";s:10:"prize_name";s:0:"";s:11:"prize_count";s:0:"";s:10:"prize_prob";s:0:"";s:11:"description";s:0:"";s:8:"media_id";s:2:"33";s:7:"haslist";s:1:"1";s:5:"prize";a:3:{i:0;a:4:{s:11:"prize_level";s:9:"特等奖";s:10:"prize_name";s:12:"航空母舰";s:11:"prize_count";s:3:"100";s:10:"prize_prob";s:2:"30";}i:1;a:4:{s:11:"prize_level";s:9:"一等奖";s:10:"prize_name";s:9:"原子弹";s:11:"prize_count";s:3:"100";s:10:"prize_prob";s:2:"40";}i:2;a:4:{s:11:"prize_level";s:9:"二等奖";s:10:"prize_name";s:6:"飞机";s:11:"prize_count";s:3:"100";s:10:"prize_prob";s:2:"70";}}}', 'function', 1, 'JETEE TEAM', 'http://www.jetee.cn', 1),
(14, '热卖商品', 'hot', 'hot', 'a:4:{s:12:"point_status";s:1:"0";s:11:"point_value";s:0:"";s:9:"point_num";s:0:"";s:14:"point_interval";s:5:"86400";}', 'function', 0, 'JETEE TEAM', 'http://www.jetee.cn', 1),
(16, '签到送积分', 'sign,签到', 'sign', 'a:6:{s:12:"point_status";s:1:"1";s:16:"rank_point_value";s:2:"10";s:15:"pay_point_value";s:2:"11";s:9:"point_num";s:1:"1";s:14:"point_interval";s:5:"86400";s:5:"prize";a:0:{}}', 'function', 1, 'JETEE TEAM', 'http://www.jetee.cn', 1),
(17, '新品', 'news', 'news', 'a:5:{s:12:"point_status";s:1:"0";s:11:"point_value";s:0:"";s:9:"point_num";s:0:"";s:14:"point_interval";s:5:"86400";s:5:"prize";a:0:{}}', 'function', 1, 'JETEE TEAM', 'http://www.jetee.cn', 1);


DROP TABLE IF EXISTS `je_wechat_media`;
CREATE TABLE IF NOT EXISTS `je_wechat_media` (
	`id`			int(10) NOT NULL AUTO_INCREMENT,
	`wechat_id`		int(10) unsigned NOT NULL DEFAULT '0' ,
	`title`			varchar(255) NOT NULL DEFAULT ''		COMMENT '图文消息标题',
	`command`		varchar(20) NOT NULL DEFAULT ''			COMMENT '关键词',
	`author`		varchar(20) NOT NULL DEFAULT ''			COMMENT '作者',
	`is_show`		int(1) unsigned NOT NULL DEFAULT '0'	COMMENT '是否显示封面，1为显示，0为不显示',
	`digest`		varchar(255) NOT NULL DEFAULT ''		COMMENT '图文消息的描述',
	`content`		text NOT NULL DEFAULT ''				COMMENT '图文消息页面的内容，支持HTML标签',
	`link`			varchar(255) NOT NULL DEFAULT ''		COMMENT '点击图文消息跳转链接',
	`file`			varchar(255) NOT NULL DEFAULT ''		COMMENT '图片链接',
	`size`			int(10) unsigned NOT NULL DEFAULT '0'	COMMENT '媒体文件上传后，获取时的唯一标识',
	`file_name`		varchar(255) NOT NULL DEFAULT ''		COMMENT '媒体文件名字',
	`thumb`			varchar(255) NOT NULL DEFAULT ''		COMMENT '缩略图',
	`add_time`		int(11) unsigned NOT NULL DEFAULT '0'	COMMENT '添加时间',
	`edit_time`		int(11) unsigned NOT NULL DEFAULT '0',
	`type`			varchar(10) NOT NULL DEFAULT ''			COMMENT 'text,news,voice,vedio',
	`article_id`	varchar(100)  NOT NULL DEFAULT ''		COMMENT '多个je_wechat_media id    逗号分隔',
	`sort`			int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;



DROP TABLE IF EXISTS `je_wechat_menu`;
CREATE TABLE IF NOT EXISTS `je_wechat_menu` (
	`id` 		int(10) unsigned NOT NULL AUTO_INCREMENT,
	`wechat_id` int(10) unsigned NOT NULL DEFAULT '0',
	`pid` 		int(3) unsigned NOT NULL DEFAULT '0'			COMMENT '父级ID',
	`name` 		varchar(255) NOT NULL							COMMENT '菜单标题',
	`type` 		varchar(10) NOT NULL							COMMENT '菜单的响应动作类型',
	`key`		varchar(255) NOT NULL							COMMENT '菜单KEY值，click类型必须',
	`url`		varchar(255) NOT NULL							COMMENT '网页链接，view类型必须',
	`sort`		int(10) unsigned NOT NULL DEFAULT '0'			COMMENT '排序',
	`status`	int(10) unsigned NOT NULL DEFAULT '0'			COMMENT '状态',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
INSERT INTO `je_wechat_menu` (`id`, `wechat_id`, `pid`, `name`, `type`, `key`, `url`, `sort`, `status`) VALUES
(1, 1, 0, '首页', 'view', '', 'https://zc.ma863.com/', 1, 1),
(20, 1, 0, '扩展功能', 'view', '', '#', 4, 1),
(21, 1, 20, '大转盘', 'click', 'dzp', '', 1, 1),
(22, 1, 20, '砸金蛋', 'click', 'zjd', '', 2, 1),
(19, 1, 0, '列表', 'view', '', '#', 2, 1),
(23, 1, 20, '手机绑定', 'click', 'bdsjh', '', 3, 1),
(24, 1, 20, '刮刮卡', 'click', 'ggk', '', 0, 1),
(25, 1, 20, '关注送红包', 'click', 'bonus', '', 5, 1),
(26, 1, 19, '我要推广', 'click', 'ad', '', 2, 1),
(30, 1, 19, '新品', 'click', 'news', '', 4, 1),
(29, 1, 19, '签到', 'click', 'sign', '', 0, 1);


DROP TABLE IF EXISTS `je_wechat_message`;
CREATE TABLE IF NOT EXISTS `je_wechat_message` (
	`id`		int(10) NOT NULL AUTO_INCREMENT,
	`wu_id`		int(10) unsigned NOT NULL DEFAULT '0'	COMMENT 'je_wechat_user id',
	`msg`		varchar(255) DEFAULT NULL				COMMENT '信息内容',
	`iswechat`	tinyint(1) unsigned DEFAULT NULL		COMMENT '是公众号回复',
	`send_time`	int(11) unsigned NOT NULL DEFAULT '0'	COMMENT '发送时间',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1	COMMENT '用户发给微信公众号信息与公众号回复表';

DROP TABLE IF EXISTS `je_wechat_point`;
CREATE TABLE IF NOT EXISTS `je_wechat_point` (
  `log_id` mediumint(8) unsigned NOT NULL COMMENT '积分增加记录id',
  `openid` varchar(100) DEFAULT NULL,
  `keywords` varchar(100) NOT NULL COMMENT '关键词',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `je_wechat_prize`;
CREATE TABLE IF NOT EXISTS `je_wechat_prize` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `wechat_id` int(11) unsigned NOT NULL,
  `openid` varchar(100) NOT NULL,
  `prize_name` varchar(100) NOT NULL,
  `issue_status` int(2) NOT NULL DEFAULT '0' COMMENT '发放状态，0未发放，1发放',
  `winner` varchar(255) DEFAULT NULL,
  `dateline` int(11) unsigned NOT NULL DEFAULT '0',
  `prize_type` int(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否中奖，0未中奖，1中奖',
  `activity_type` varchar(20) NOT NULL COMMENT '活动类型',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `je_wechat_qrcode`;
CREATE TABLE IF NOT EXISTS `je_wechat_qrcode` (
  `id` 			int(10) NOT NULL AUTO_INCREMENT,
  `type` 		int(1) NOT NULL DEFAULT '0'					COMMENT '二维码类型，0临时，1永久',
  `expire_seconds` int(4) DEFAULT NULL						COMMENT '二维码有效时间',
  `scene_id` 	int(10) NOT NULL 							COMMENT '场景值ID，临时二维码时为32位非0整型，永久二维码时最大值为100000（目前参数只支持1--100000）',
  `username`	varchar(60) DEFAULT NULL 					COMMENT '推荐人',
  `function`	varchar(255) NOT NULL						COMMENT '关键字  触发关键字',
  `ticket`		varchar(255) DEFAULT NULL					COMMENT '二维码ticket',
  `qrcode_url`	varchar(255) DEFAULT NULL					COMMENT '二维码路径',
  `endtime`		int(11) unsigned NOT NULL DEFAULT '0'		COMMENT '结束时间',
  `scan_num` 	int(10) unsigned NOT NULL DEFAULT '0'		COMMENT '扫描量',
  `wechat_id` 	int(10) NOT NULL							COMMENT '对应公众号 je_wechat id',
  `status`		int(1) NOT NULL DEFAULT '1'					COMMENT '状态',
  `sort` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `je_wechat_reply`;
CREATE TABLE IF NOT EXISTS `je_wechat_reply` (
	`id`		int(10) NOT NULL AUTO_INCREMENT,
	`wechat_id`	int(11) unsigned NOT NULL DEFAULT '0'			COMMENT '属于哪个微信公众号',
	`type`		varchar(10) NOT NULL DEFAULT ''					COMMENT '自动回复类型 keywords关键字自动回复 msg消息自动回复 subscribe关注自动回复',
	`content`	varchar(255) NOT NULL DEFAULT ''				COMMENT '回复文本时的内容',
	`media_id`	int(10) unsigned NOT NULL DEFAULT '0'			COMMENT 'news voice image 上传到微信服务器后的本地id',
	`rule_name`	varchar(180) NOT NULL DEFAULT ''				COMMENT '关键字回复的规则名',
	`add_time`	int(11) unsigned NOT NULL DEFAULT '0',
	`reply_type` varchar(10) NOT NULL DEFAULT ''				COMMENT '关键词回复内容的类型  news voice image ',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;



DROP TABLE IF EXISTS `je_wechat_rule_keywords`;
CREATE TABLE IF NOT EXISTS `je_wechat_rule_keywords` (
  `id` 	int(11) NOT NULL AUTO_INCREMENT,
  `rid` int(11) NOT NULL 						COMMENT 'je_wechat_reply  id',
  `rule_keywords` varchar(255) DEFAULT NULL		COMMENT '关键字',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT '自动回复';

DROP TABLE IF EXISTS `je_wechat_template`;
CREATE TABLE IF NOT EXISTS `je_wechat_template` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `open_id` varchar(255) DEFAULT NULL,
  `template_id` varchar(255) DEFAULT NULL,
  `contents` varchar(133) DEFAULT NULL,
  `template` text,
  `title` varchar(33) NOT NULL,
  `add_time` int(11) DEFAULT NULL,
  `switch` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


DROP TABLE IF EXISTS `je_wechat_user`;
CREATE TABLE IF NOT EXISTS `je_wechat_user` (
	`wu_id`		int(10) unsigned NOT NULL AUTO_INCREMENT		COMMENT '本系统的会员表也 统一用这个id',
	`wechat_id`	int(10) unsigned NOT NULL DEFAULT '0' 			COMMENT '公众号列表 id',
	`subscribe`	tinyint(1) unsigned NOT NULL DEFAULT '0'		COMMENT '用户是否订阅该公众号标识',
	`openid`	varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT ''				COMMENT '用户的标识',
	`nickname`	varchar(255) NOT NULL DEFAULT ''				COMMENT '用户的昵称',
	`sex`		tinyint(1) unsigned NOT NULL DEFAULT '0'		COMMENT '用户的性别',
	`city`		varchar(255) NOT NULL DEFAULT ''				COMMENT '用户所在城市',
	`country`	varchar(255) NOT NULL DEFAULT ''				COMMENT '用户所在国家',
	`province`	varchar(255) NOT NULL DEFAULT ''				COMMENT '用户所在省份',
	`language`	varchar(50) NOT NULL DEFAULT ''					COMMENT '用户的语言',
	`headimgurl` varchar(255) NOT NULL DEFAULT ''				COMMENT '用户头像',
	`subscribe_time` int(11) unsigned NOT NULL DEFAULT '0'		COMMENT '用户关注时间',
	`remark`	varchar(255) NOT NULL DEFAULT ''		,
	`privilege` varchar(255)  NOT NULL DEFAULT ''		,
	`unionid`	varchar(255)  NOT NULL DEFAULT ''				COMMENT '',
	`group_id`	int(11) unsigned NOT NULL DEFAULT '0'			COMMENT '用户组id',
	`admin_id`	int(11) unsigned NOT NULL DEFAULT '0'			COMMENT '',
	`uid` 		int(11) unsigned NOT NULL DEFAULT '0'			COMMENT '绑定的主表用户id',
	`up_wu_id`	int(11) unsigned NOT NULL DEFAULT '0'			COMMENT '关注时扫描谁的码 谁就是上级',
	`bein_kefu`	tinyint(1) unsigned NOT NULL DEFAULT '0'		COMMENT '是否处在多客服流程',
	`isbind`	tinyint(1) unsigned NOT NULL DEFAULT '0'		COMMENT '是否绑定过',
	`is_fenhong` tinyint(1) unsigned NOT NULL DEFAULT '0'		COMMENT '是否关注分红过 多次关注不分红',
	`is_verify` tinyint(1) unsigned NOT NULL DEFAULT '0'		COMMENT '是否核销员0否  1是',
	PRIMARY KEY (`wu_id`),
	KEY `openid` (`openid`),
	KEY `is_verify` (`is_verify`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 			COMMENT '微信粉丝';



DROP TABLE IF EXISTS `je_wechat_user_group`;
CREATE TABLE IF NOT EXISTS `je_wechat_user_group` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`wechat_id` int(10) unsigned NOT NULL DEFAULT '0',
	`group_id` int(10) unsigned NOT NULL DEFAULT '0'		COMMENT '分组id',
	`name` varchar(255) NOT NULL							COMMENT '分组名字，UTF8编码',
	`count` int(10) unsigned NOT NULL DEFAULT '0'			COMMENT '分组内用户数量',
	`sort` int(10) unsigned NOT NULL DEFAULT '0'			COMMENT '排序',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;



		
-- 广告列表 { 
DROP TABLE IF EXISTS `je_ad`;
CREATE TABLE IF NOT EXISTS `je_ad` (
	`aid` 			int(11) unsigned NOT NULL AUTO_INCREMENT 			COMMENT '编号',
	`position` 		int(11) unsigned NOT NULL DEFAULT '0'				COMMENT '广告位 1商家中心  2APP首页',
	`pic` 			varchar(200) NOT NULL DEFAULT '' 					COMMENT '',
	`alt` 			varchar(200) NOT NULL DEFAULT '' 					COMMENT '描述,放在图片上显示的提示',
	`url` 			varchar(2000) NOT NULL DEFAULT ''  					COMMENT '链接',
	`sort_order` 	int(11) unsigned NOT NULL DEFAULT '0'				COMMENT '',
	`add_time` 		int(11) unsigned NOT NULL DEFAULT '0'				COMMENT '',
	PRIMARY KEY (`aid`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='广告列表';
-- }



DROP TABLE IF EXISTS `je_event`;
CREATE TABLE IF NOT EXISTS `je_event`(
	`eid`			int(10) unsigned NOT NULL AUTO_INCREMENT	 		COMMENT '自增id', 
	`uid`			int(8) unsigned NOT NULL DEFAULT '0' 				COMMENT '谁', 
	`type`			tinyint(3) unsigned NOT NULL DEFAULT '0' 			COMMENT '动作：1注册 2发布 3自动接单 4手动接单 ',
	`id`		int(10) unsigned NOT NULL DEFAULT '0'		 			COMMENT '动作的对象', 
	`add_time`    	int(11) unsigned NOT NULL DEFAULT '0'       		COMMENT '事件时间', 
	`ip` 			char(15) NOT NULL DEFAULT ''	 					COMMENT '',
	PRIMARY KEY (`eid`), 
	KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='会员事件记录表'; 

DROP TABLE IF EXISTS `je_rank`;
CREATE TABLE IF NOT EXISTS `je_rank` (
	`rid` 		tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT '会员等级编号，其中0是非会员',
	`rname` 	varchar(20) NOT NULL COMMENT '会员等级名称',
	`min_score` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '该等级的最低积分',
	`max_score` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '该等级的最高积分',
	`special` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否特殊会员等级组.0,不是;1,是',
	PRIMARY KEY (`rid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='会员等级配置信息' AUTO_INCREMENT=4 ;
INSERT INTO `je_rank` (`rid`, `rname`, `min_score`, `max_score`, `special`) VALUES
(1, 'vip1', 0, 2000, 0),
(2, 'vip2', 2000, 5000, 0),
(3, 'vip3', 5000, 10000, 0);


DROP TABLE IF EXISTS `je_admin`;
CREATE TABLE IF NOT EXISTS `je_admin` (
  `admin_id` 	smallint(6) unsigned NOT NULL AUTO_INCREMENT 	COMMENT '自增ID号，管理员代号',
  `username` 	char(15) NOT NULL DEFAULT '' 					COMMENT '会员名',
  `password` 	char(32) NOT NULL DEFAULT '' 					COMMENT '密码',
  `salt` 		char(6) NOT NULL DEFAULT '' 					COMMENT '加密',
  `email` 		char(40) NOT NULL DEFAULT '' 					COMMENT 'Email地址',
  `reg_date` 	int(10) NOT NULL DEFAULT '0' 					COMMENT '注册时间',
  `privilege` 	varchar(8000) NOT NULL DEFAULT '' 				COMMENT '管理员管理权限列表',
  `nav_list` 	varchar(8000) NOT NULL DEFAULT '' 				COMMENT '管理员导航栏配置项',
  `last_login` 	int(10) NOT NULL DEFAULT '0' 					COMMENT '最后登陆时间',
  `last_ip` 	char(15) DEFAULT '' 							COMMENT '最后一次访问ip',
  `role_id` 	smallint(5) DEFAULT '0' 						COMMENT '角色',
  `notebook` 	longtext  NOT NULL DEFAULT ''					COMMENT '记事本记录的数据',
  PRIMARY KEY (`admin_id`),
  KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='管理员列表' AUTO_INCREMENT=2;
INSERT INTO `je_admin` (`admin_id`, `username`, `password`, `salt`, `email`, `reg_date`, `privilege`, `nav_list`, `last_login`, `last_ip`, `role_id`, `notebook`) VALUES (1, 'admin', '5755e82105a81865cacea2e37f270561', 'wZOdCA', 'asdf@163.com', 1328086840, 'all', '', 1504754050, '127.0.0.1', 1, '');


DROP TABLE IF EXISTS `je_admin_log`;
CREATE TABLE IF NOT EXISTS `je_admin_log` (
  `log_id` 		int(10) unsigned NOT NULL AUTO_INCREMENT 			COMMENT '自增ID号，权限号',
  `log_time` 	int(10) unsigned NOT NULL DEFAULT '0' 				COMMENT '记录时间',
  `admin_id` 	smallint(3) unsigned NOT NULL DEFAULT '0' 			COMMENT '记录管理员',
  `log_info` 	varchar(255) NOT NULL DEFAULT '' 					COMMENT '管理操作内容',
  `ip_address` 	varchar(15) NOT NULL DEFAULT '' 					COMMENT '管理者登录ip',
  PRIMARY KEY (`log_id`),
  KEY `log_time` (`log_time`),
  KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT='管理员日志';

DROP TABLE IF EXISTS `je_article`;
CREATE TABLE IF NOT EXISTS `je_article` (
	`article_id` 	mediumint(8) unsigned NOT NULL AUTO_INCREMENT 	COMMENT '自增ID号',
	`cat_id` 		smallint(5) unsigned NOT NULL DEFAULT '0' 	COMMENT  '该文章的分类，同article_cat的cat_id',
	`title` 		varchar(150) NOT NULL DEFAULT '' 			COMMENT '文章题目',
	`content` 		longtext NOT NULL DEFAULT ''				COMMENT '文章内容',
	`author` 		varchar(30) NOT NULL DEFAULT '' 			COMMENT '文章作者',
	`author_email` 	varchar(60) NOT NULL DEFAULT '' 			COMMENT '文章作者的email',
	`keywords` 		varchar(255) NOT NULL DEFAULT '' 			COMMENT '文章的关键字',
	`description` 	varchar(255) NOT NULL DEFAULT '' 			COMMENT '文章的描述',
	`add_time` 		int(10) unsigned NOT NULL DEFAULT '0' 		COMMENT '文章添加时间',
	`link` 			varchar(255) NOT NULL 						COMMENT '转发',
	`view_count`	int(10) unsigned DEFAULT '0'				COMMENT '浏览次数',
	`comment_count`	int(10) unsigned DEFAULT '0'				COMMENT '评论次数 缓冲',
	`digg_count`	int(10) unsigned DEFAULT '0'				COMMENT '顶次数 缓冲',
	`collect_count`	int(10) unsigned DEFAULT '0'				COMMENT '收藏次数 缓冲',
	`sort_order` 	tinyint(3) unsigned NOT NULL DEFAULT '0' 	COMMENT '显示顺序 升序',
	`status` 		tinyint(1) unsigned NOT NULL DEFAULT '0' 	COMMENT '审核 	0未审核 1已审核 4删除',
	PRIMARY KEY (`article_id`),
	KEY `cat_id` (`cat_id`),
	KEY `sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1		COMMENT='文章内容表';

DROP TABLE IF EXISTS `je_article_cat`;
CREATE TABLE IF NOT EXISTS `je_article_cat` (
  `cat_id` 		smallint(5)  NOT NULL AUTO_INCREMENT 		COMMENT '自增ID号',
  `cat_name` 	varchar(150) NOT NULL 						COMMENT '分类名称',
  `keywords` 	varchar(255) NOT NULL 						COMMENT '分类关键字',
  `cat_desc` 	varchar(255) NOT NULL 						COMMENT '分类说明文字',
  `sort_order` 	tinyint(3) unsigned NOT NULL DEFAULT '0' 	COMMENT '分类显示顺序 升序',
  `parent_id` 	smallint(5) unsigned NOT NULL DEFAULT '0' 	COMMENT '父节点id，取值于该表cat_id字段',
  PRIMARY KEY (`cat_id`),
  KEY `sort_order` (`sort_order`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1	 	COMMENT='文章分类信息表';

DROP TABLE IF EXISTS `je_feedback`;
CREATE TABLE IF NOT EXISTS `je_feedback` (
	`fid` 		int(11) unsigned NOT NULL AUTO_INCREMENT		COMMENT 'ID',
	`uid` 		int(11) unsigned NOT NULL DEFAULT '0'			COMMENT '用户',
	`admin_id` 	int(11) unsigned NOT NULL DEFAULT '0'			COMMENT '回复管理员  用户发送不用填',
	`is_new` 	tinyint(1) unsigned NOT NULL DEFAULT '1'		COMMENT '0已读 1为新通知',
	`msg` 		varchar(255) NOT NULL DEFAULT '' 				COMMENT '',
	`add_time` 	int(10) unsigned NOT NULL DEFAULT '0'			COMMENT '发送的时间戳',
	PRIMARY KEY (`fid`),
	KEY `uid` (`uid`),
	KEY `admin_id` (`admin_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 	COMMENT='反馈表 管理员与用户的对话';

DROP TABLE IF EXISTS `je_notification`;
CREATE TABLE IF NOT EXISTS `je_notification` (
	`nid` 		int(10) unsigned NOT NULL AUTO_INCREMENT			COMMENT 'ID',
	`uid` 		int(11) unsigned NOT NULL DEFAULT '0'				COMMENT '发给谁',
	`type` 		tinyint(1) unsigned NOT NULL DEFAULT '0'			COMMENT '',
	`new` 		tinyint(1) unsigned NOT NULL DEFAULT '1'			COMMENT '0已读 1为新通知',
	`note` 		varchar(255) NOT NULL DEFAULT ''					COMMENT '通知',
	`add_time` 	int(10) unsigned NOT NULL DEFAULT '0'				COMMENT '通知产生的时间戳',
	PRIMARY KEY (`nid`),
	KEY `type` (`type`),
	KEY `uid` (`uid`,`new`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 	COMMENT='通知表';

DROP TABLE IF EXISTS `je_config`;
CREATE TABLE IF NOT EXISTS `je_config` (
	`name` 		varchar(30) NOT NULL DEFAULT '' 	COMMENT '设置名',
	`value` 	text NOT NULL 						COMMENT '值',
	`comment` 	varchar(100) NOT NULL 				COMMENT '注释不起作用',
	PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='配置表';

INSERT INTO `je_config` (`name`, `value`, `comment`) VALUES
('corp_address', '广东省东莞市', ''),
('corp_tel', '0769-00000000', ''),
('corp_phone', '185888888888', ''),
('corp_contact', '张生', ''),
('site_email', 'admin@xxx.com', ''),
('icp', '粤ICP备88888888号-2', ''),
('site_name', '人气云', ''),
('site_title', '人气云', ''),
('keywords', '人气云', ''),
('description', '人气云', ''),
('sms_status', '0', ''),
('sms_uid', '', ''),
('sms_key', '', ''),
('xthtmm', '人气云', ''),
('upload_size_limit', '2097152', ''),
('time_format', 'Y-m-d H:i', ''),
('date_format', 'Y-m-d', ''),
('price', '100', ''),
('rate', '10', '');



	
DROP TABLE IF EXISTS `je_privilege`;
CREATE TABLE IF NOT EXISTS `je_privilege` (
	`pid`		smallint(6) unsigned NOT NULL AUTO_INCREMENT 		COMMENT '自增ID号，权限号',
	`up_id`		smallint(6) unsigned NOT NULL DEFAULT '0' 			COMMENT '父id',
	`name`		varchar(100) NOT NULL DEFAULT '' 					COMMENT '权限名',
	`relevance`	varchar(100) NOT NULL DEFAULT ''						COMMENT '关联权限',
	`comment`	varchar(100) NOT NULL DEFAULT ''						COMMENT '说明',
	PRIMARY KEY (`pid`),
	KEY `parent_id` (`up_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 COMMENT '管理权限表';

INSERT INTO `je_privilege` (`pid`, `up_id`, `name`, `relevance`, `comment`) VALUES
(1, 0, 'wechat', '', '公众号管理'),
(2, 0, 'article', '', '文章管理'),
(3, 0, 'admin', '', '系统用户'),
(4, 0, 'config', '', '系统设置'),

(NULL, 1, 'Wechat_edit', '', '公众号设置'),
(NULL, 1, 'Wechat_extend', '', '功能扩展'),
(NULL, 1, 'Wechat_remind_lists', '', '提醒设置'),
(NULL, 1, 'Wechat_customer_service', '', '多客服设置'),
(NULL, 1, 'Wechat_menu_lists', '', '查看微信菜单'),
(NULL, 1, 'Wechat_menu_edit', '', '添加/修改微信菜单'),
(NULL, 1, 'Wechat_menu_del', '', '删除微信菜单'),
(NULL, 1, 'Wechat_menu_creat', '', '创建微信菜单'),
(NULL, 1, 'Wechat_subscribe_lists', '', '查看粉丝列表'),
(NULL, 1, 'Wechat_subscribe_move', '',  '粉丝转移分组'),
(NULL, 1, 'Wechat_subscribe_update', '','同步粉丝管理'),
(NULL, 1, 'Wechat_subscribe_edit', '','修改粉丝'),
(NULL, 1, 'Wechat_user_group_sync', '', '更新同步用户分组'),
(NULL, 1, 'Wechat_user_group_edit', '', '添加/编辑用户分组'),
(NULL, 1, 'Wechat_reply_subscribe', '', '关注自动回复管理'),
(NULL, 1, 'Wechat_reply_msg', '', '消息自动回复管理'),
(NULL, 1, 'Wechat_reply_keywords', '', '关键字自动回复管理'),
(NULL, 1, 'Wechat_reply_reply_del', '', '自动回复删除管理'),
(NULL, 1, 'Wechat_template_message', 		'', '查看模板信息'),
(NULL, 1, 'Wechat_template_toggle', 			'', '模板信息开启/关闭'),
(NULL, 1, 'Wechat_qrcode_lists', 			'', '查看扫码引荐'),
(NULL, 1, 'Wechat_qrcode_edit', 				'', '编辑/添加扫码引荐'),
(NULL, 1, 'Wechat_qrcode_del', 				'', '删除扫码引荐'),
(NULL, 1, 'Wechat_qrcode_get', 				'', '获取扫码引荐二维码'),
(NULL, 1, 'Wechat_custom_message_lists', '', '查看客服消息'),
(NULL, 1, 'Wechat_custom_message_send',  '', '发送客服消息'),
(NULL, 1, 'wechat_media_article', 			'', '图文消息查看/删除'),
(NULL, 1, 'wechat_media_article_edit', 		'', '图文消息添加/编辑'),
(NULL, 1, 'wechat_media_article_edit_news', '', '多图文添加/编辑'),
(NULL, 1, 'wechat_media_article_del', 		'', '图文消息删除'),
(NULL, 1, 'wechat_media_picture', 			'', '图片管理'),
(NULL, 1, 'wechat_media_voice', 			'', '语音管理'),
(NULL, 1, 'wechat_media_video', 			'', '视频查看/删除'),
(NULL, 1, 'wechat_media_video_edit', 		'', '视频添加/编辑'),
(NULL, 1, 'wechat_media_video_upload', 		'', '视频上传'),

(NULL, 2, 'Article_lists', '', '查看文章'),
(NULL, 2, 'Article_add', '', '添加文章'),
(NULL, 2, 'Article_del', '', '删除文章'),
(NULL, 2, 'Article_edit', '', '编辑文章'),
(NULL, 2, 'Article_cat_lists', '', '查看分类'),
(NULL, 2, 'Article_cat_add', '', '添加分类'),
(NULL, 2, 'Article_cat_del', '', '删除分类'),
(NULL, 2, 'Article_cat_edit', '', '编辑分类'),

(NULL, 3, 'Admin_lists', '', '查看管理员'),
(NULL, 3, 'Admin_add', '', '添加管理员'),
(NULL, 3, 'Admin_del', '', '删除管理员'),
(NULL, 3, 'Admin_edit', '', '编辑管理员'),
(NULL, 3, 'Role_lists', '', '查看角色'),
(NULL, 3, 'Role_add', '', '添加角色'),
(NULL, 3, 'Role_del', '', '删除角色'),
(NULL, 3, 'Role_edit', '', '编辑角色'),
(NULL, 3, 'Admin_log_lists', '', '查看管理日志'),
(NULL, 3, 'Admin_log_del', '', '删除管理日志'),

(NULL, 4, 'Config_basic', '', '基本设置'),
(NULL, 4, 'Admin_clear_cache', '', '刷新缓冲');





DROP TABLE IF EXISTS `je_role`;
CREATE TABLE IF NOT EXISTS `je_role` (
  `role_id` 		smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` 		varchar(60) NOT NULL DEFAULT '',
  `action_list` 	text NOT NULL,
  `role_describe` 	text,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2;
INSERT INTO `je_role` (`role_id`, `role_name`, `action_list`, `role_describe`) VALUES
(1, '管理员', 'Config_basic,Admin_lists,Admin_add,Admin_del,Admin_edit,Role_lists,Role_add,Role_del,Role_edit,Admin_log_lists,Admin_log_del,Admin_clear_cache', '');


DROP TABLE IF EXISTS `je_notice`;
CREATE TABLE IF NOT EXISTS `je_notice` (
	`notice_id` 	mediumint(8) unsigned NOT NULL AUTO_INCREMENT 	COMMENT '自增ID号',
	`cat_id` 		tinyint(3) unsigned NOT NULL DEFAULT '0' 	COMMENT  '该文章的分类，0全平台   1电脑端  2手机端',
	`title` 		varchar(150) NOT NULL DEFAULT '' 			COMMENT '文章题目',
	`content` 		longtext NOT NULL DEFAULT ''				COMMENT '文章内容',
	`author` 		varchar(30) NOT NULL DEFAULT '' 			COMMENT '文章作者',
	`author_email` 	varchar(60) NOT NULL DEFAULT '' 			COMMENT '文章作者的email',
	`keywords` 		varchar(255) NOT NULL DEFAULT '' 			COMMENT '文章的关键字',
	`description` 	varchar(255) NOT NULL DEFAULT '' 			COMMENT '文章的描述',
	`add_time` 		int(10) unsigned NOT NULL DEFAULT '0' 		COMMENT '文章添加时间',
	`link` 			varchar(255) NOT NULL 						COMMENT '转发',
	`view_count`	int(10) unsigned DEFAULT '0'				COMMENT '浏览次数',
	`comment_count`	int(10) unsigned DEFAULT '0'				COMMENT '评论次数 缓冲',
	`digg_count`	int(10) unsigned DEFAULT '0'				COMMENT '顶次数 缓冲',
	`collect_count`	int(10) unsigned DEFAULT '0'				COMMENT '收藏次数 缓冲',
	`sort_order` 	tinyint(3) unsigned NOT NULL DEFAULT '0' 	COMMENT '显示顺序 升序',
	`status` 		tinyint(1) unsigned NOT NULL DEFAULT '0' 	COMMENT '是否启用 	0未审核 1已审核',
	PRIMARY KEY (`notice_id`),
	KEY `cat_id` (`cat_id`),
	KEY `sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1		COMMENT='公告表';


----------------------- 数据库结束  以下注释



















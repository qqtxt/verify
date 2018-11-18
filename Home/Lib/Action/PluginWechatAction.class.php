<?php
defined('JETEE_PATH') or die('Deny Access');

abstract class PluginWechatAction
{


	public function __construct(){

		if (empty(session('openid'))) {

			$appid = M('wechat')->field('appid')->where('id = "1" and status = 1')->getOne();

			//来源域名地址
			session('source','http://'.$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL']);

			$url  =  "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".$appid."&redirect_uri=http://".$_SERVER['SERVER_NAME']."/home/plugins/empower&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect" ;  

			echo " <script   language = 'javascript' type = 'text/javascript' > ";  
			echo " window.location.href = '$url' ";  
			echo " </script > "; 
		}
		

	}

	
	protected $layout = 'wechat_layout';
	protected $_data = array();

	protected function get_wechat_sdk(){
		$wxinfo   = M('wechat')->field('token, appid, appsecret')->find();
		$appid    = $wxinfo['appid'];
		$secret   = $wxinfo['appsecret'];

		//微信店信息
		$wx_title = ''; // C('shop_name');
		$wx_desc  = C('shop_desc');
		$wx_url   = ''; //  'index.php?u=' . $_SESSION['user_id'];
		$wx_pic   = '/Public/images/logo.png';
		
		//微信JS SDK
		require_once('./Home/Lib/Jssdk.class.php');

		$jssdk = new Jssdk($appid, $secret);

		$signPackage = $jssdk->GetSignPackage();
		$appid = $signPackage["appId"];
		$timestamp = $signPackage["timestamp"];
		$noncestr = $signPackage["nonceStr"];
		$signature = $signPackage["signature"];

		$output = "<script type='text/javascript' src='http://res.wx.qq.com/open/js/jweixin-1.0.0.js'></script>
<script type='text/javascript'>
wx.config({
	debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
	appId: '{$appid}', // 必填，公众号的唯一标识
	timestamp: {$timestamp}, // 必填，生成签名的时间戳
	nonceStr: '{$noncestr}', // 必填，生成签名的随机串
	signature: '{$signature}',// 必填，签名，见附录1
	jsApiList: ['onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo','onMenuShareQZone','scanQRCode'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
});
wx.ready(function(){
	//分享到朋友圈
	wx.onMenuShareTimeline({
		title: '{$wx_title}', // 分享标题
		link: '{$wx_url}', // 分享链接
		imgUrl: '{$wx_pic}', // 分享图标
		success: function () {
			// 用户确认分享后执行的回调函数
		},
		cancel: function () {
			// 用户取消分享后执行的回调函数
		}
	});
	//分享给朋友
	wx.onMenuShareAppMessage({
		title: '{$wx_title}', // 分享标题
		desc: '{$wx_desc}', // 分享描述
		link: '{$wx_url}', // 分享链接
		imgUrl: '{$wx_pic}', // 分享图标
		type: '', // 分享类型,music、video或link，不填默认为link
		dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
		success: function () {
			// 用户确认分享后执行的回调函数
		},
		cancel: function () {
			// 用户取消分享后执行的回调函数
		}
	});
	//分享到QQ
	wx.onMenuShareQQ({
		title: '{$wx_title}', // 分享标题
		desc: '{$wx_desc}', // 分享描述
		link: '{$wx_url}', // 分享链接
		imgUrl: '{$wx_pic}', // 分享图标
		success: function () {
		   // 用户确认分享后执行的回调函数
		},
		cancel: function () {
		   // 用户取消分享后执行的回调函数
		}
	});
	//分享到腾讯微博
	wx.onMenuShareWeibo({
		title: '{$wx_title}', // 分享标题
		desc: '{$wx_desc}', // 分享描述
		link: '{$wx_url}', // 分享链接
		imgUrl: '{$wx_pic}', // 分享图标
		success: function () {
		   // 用户确认分享后执行的回调函数
		},
		cancel: function () {
			// 用户取消分享后执行的回调函数
		}
	});
	//分享到QQ空间
	wx.onMenuShareQZone({
		title: '{$wx_title}', // 分享标题
		desc: '{$wx_desc}', // 分享描述
		link: '{$wx_url}', // 分享链接
		imgUrl: '{$wx_pic}', // 分享图标
		success: function () {
		   // 用户确认分享后执行的回调函数
		},
		cancel: function () {
			// 用户取消分享后执行的回调函数
		}
	});

	document.querySelector('#scanQRCode').onclick = function() {
		wx.scanQRCode();
	};
	document.querySelector('#translateVoice').onclick = function () {
		if (voice.localId == '') {
			alert('请先使用 startRecord 接口录制一段声音');
			return;
		}
		wx.translateVoice({
			localId: voice.localId,
			complete: function (res) {
				if (res.hasOwnProperty('translateResult')) {
					alert('识别结果：' + res.translateResult);
				} else {
					alert('无法识别');
				}
			}
		});
	};
});
wx.error(function(res){
	alert(res.errMsg);
	// config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
});
</script>";
		return $output;
	}

	/**
	 * 数据显示返回
	 */
	abstract protected function show($fromusername, $info);

	/**
	 * 积分赠送
	 */
	abstract protected function give_point($fromusername, $info);

	/**
	 * 行为处理
	 */
	abstract protected function action();

	/**
	 * 积分赠送处理
	 */
	public function do_point($fromusername, $info, $rank_points, $pay_points)
	{
		$uid = M('wechat_user')->field('uid')->where('openid = "' . $fromusername . '"')->getOne();
		// 增加等级积分
		$point = 'rank_points = rank_points +' . intval($rank_points);
		M('users')->data($point)->where('uid = ' . $uid)->save();
		// 增加消费积分
		$point = 'pay_points = pay_points +' . intval($pay_points);
		M('users')->data($point)->where('uid = ' . $uid)->save();
		// 积分记录
		$data['uid'] = $uid;
		$data['money'] = 0;
		$data['rank_points'] = $rank_points;
		$data['pay_points'] = $pay_points;
		$data['change_time'] = NOW_TIME;
		$data['change_desc'] = $info['name'] . '公众号积分赠送';
		$data['change_type'] = 99;
		
		$log_id = M('account_log')->data($data)->add();
		// 从表记录
		$data1['log_id'] = $log_id;
		$data1['openid'] = $fromusername;
		$data1['keywords'] = $info['command'];
		$data1['createtime'] = $time;
		$log_id = M('wechat_point')
			->data($data1)
			->add();
	}

	
	/**
	 * 中奖概率计算
	 *
	 * @param unknown $proArr
	 * @return Ambigous <string, unknown>
	 */
	function get_rand($proArr)
	{
		$result = '';
		// 概率数组的总概率精度
		$proSum = array_sum($proArr);
		// 概率数组循环
		foreach ($proArr as $key => $proCur) {
			$randNum = mt_rand(1, $proSum);
			if ($randNum <= $proCur) {
				$result = $key;
				break;
			} else {
				$proSum -= $proCur;
			}
		}
		unset($proArr);
		return $result;
	}
	
	public function __get($name) {
		return isset($this->_data [$name]) ? $this->_data [$name] : NULL;
	}
	
	public function __set($name, $value) {
		$this->_data [$name] = $value;
	}
}

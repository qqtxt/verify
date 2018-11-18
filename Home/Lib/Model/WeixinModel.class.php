<?php
defined('JETEE_PATH') or exit();

/** 		
*用户基础模型	与前后台用户相关的操作都定义在这里  自己写作废
* @version 0.0.1 11:17 2016/6/1
*/
class WeixinModel{
	public function __construct($options){
	}
	/**
	 * 获取JsApi使用签名
	 * @param string $url 本网页的URL，自动处理#及其后面部分
	 * @param string $timestamp 当前时间戳 (为空则自动生成)
	 * @param string $noncestr 随机串 (为空则自动生成)
	 * @param string $appid 用于多个appid时使用,可空
	 * @return array|bool 返回签名字串
	 */	 
	public function getSignPackage($url, $timestamp=0, $noncestr='', $appid=''){
	    if (!$appid) $appid = c('appid');
		$jsapi_ticket=$this->getJsTicket($appid);
	    if (!$jsapi_ticket || !$url) return false;
	    if (!$timestamp) $timestamp = time();
	    if (!$noncestr)  $noncestr = $this->generateNonceStr();		
	    $ret = strpos($url,'#');
	    if ($ret) $url = trim(substr($url,0,$ret));
	    if (empty($url)) return false;
	    $arrdata = array("timestamp" => $timestamp, "noncestr" => $noncestr, "url" => $url, "jsapi_ticket" => $jsapi_ticket);
	    $sign = $this->getSignature($arrdata);
	    if (!$sign) return false;
	    $signPackage = array(
	            "appId"     => c('appid'),
	            "nonceStr"  => $noncestr,
	            "timestamp" => $timestamp,
	            "url"       => $url,
	            "signature" => $sign
	    );
	    return $signPackage;
	}
	/**
	 * 获取JSAPI授权TICKET  1
	 * @param string $appid 用于多个appid时使用,可空
	 * @param string $jsapi_ticket 手动指定jsapi_ticket，非必要情况不建议用
	 */
	public function getJsTicket($appid='',$jsapi_ticket=''){
		if ($jsapi_ticket) { //手动指定token，优先使用
		    return $jsapi_ticket;
		}
		
		if (!$appid) $appid = c('appid');
		$authname = 'wechat_jsapi_ticket'.$appid;
		if ($rs = s($authname))  {
			return $rs;
		}
		
		$access_token=$this->get_access_token(c('appid'),c('appsecret'));
		if (!$access_token) return false;
		$result = curl_get("https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=$access_token&type=jsapi");
		if ($result){
			$json = json_decode($result,true);
			if (!$json || !empty($json['errcode'])) {
				$this->errCode = $json['errcode'];
				$this->errMsg = $json['errmsg'];
				return false;
			}
			$jsapi_ticket = $json['ticket'];
			$expire = $json['expires_in'] ? intval($json['expires_in'])-100 : 3600;
			s($authname,$jsapi_ticket,$expire);
			return $jsapi_ticket;
		}
		return false;
	}	
	/**
	 * 生成随机字串
	 * @param number $length 长度，默认为16，最长为32字节
	 * @return string
	 */
	public function generateNonceStr($length=16){
		// 密码字符集，可任意添加你需要的字符
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for($i = 0; $i < $length; $i++)
		{
			$str .= $chars[mt_rand(0, strlen($chars) - 1)];
		}
		return $str;
	}	
	/**
	 * 获取签名
	 * @param array $arrdata 签名数组
	 * @param string $method 签名方法
	 * @return boolean|string 签名值
	 */
	public function getSignature($arrdata,$method="sha1") {
		if (!function_exists($method)) return false;
		ksort($arrdata);
		$paramstring = "";
		foreach($arrdata as $key => $value)
		{
			if(strlen($paramstring) == 0)
				$paramstring .= $key . "=" . $value;
			else
				$paramstring .= "&" . $key . "=" . $value;
		}
		$Sign = $method($paramstring);
		return $Sign;
	}
	
	
	
	//获取access_token   要改
	function get_access_token($AppID,$AppSecret){
		$cache_name='wechat_access_token'.$AppID;		
		$access_token=s($cache_name);
		if(!$access_token){
			$token=json_decode(curl_get("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$AppID&secret=$AppSecret"));
			if(isset($token->access_token)){
				$access_token=$token->access_token;
				$expire = $token->expires_in ? intval($token->expires_in)-100 : 3600;
				s($cache_name,$access_token,$expire);
			}
		}
		return $access_token;
	}
	//发消息模板   可以用
	public function send_template($AppID,$AppSecret,$content){
		$url='https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$this->get_access_token($AppID,$AppSecret);
		$return=curl_post($url, urldecode($content));
		return json_decode($return,true);
	}
	//发消息模板   可以用
	public function get_content($template_id,$arr){
		switch($template_id){
			//开户成功  发代理或合作伙伴
			case 'Nfze3qDp6W3aMS7XMO_9cWhK2u8uZzd2ADXlFUCyhQg':
				return  json_encode(array(
					'touser'=>$arr[0],
					'template_id'=>'Nfze3qDp6W3aMS7XMO_9cWhK2u8uZzd2ADXlFUCyhQg',
					'url'=>$arr[1],
					'topcolor'=>'#FF0000',
					'data'=>array(
						'first'   =>array('value'=>$arr[2],'color'=>"#173177"),
						'keyword1'=>array('value'=>$arr[3],'color'=>"#173177"),
						'keyword2'=>array('value'=>$arr[4],'color'=>"#173177"),
						'keyword3'=>array('value'=>$arr[5],'color'=>"#173177"),	
						'keyword4'=>array('value'=>$arr[6],'color'=>"#173177"),	
						'remark'  =>array('value'=>$arr[7],'color'=>"#173177")
					)							
				));				
			break;
			//开户与续费  通知用户
			case 'FAOftR7PtektTvK9tQOlhSQv1uK7DBRwolFFcDztwgk':
				return  json_encode(array(
					'touser'=>$arr[0],
					'template_id'=>'FAOftR7PtektTvK9tQOlhSQv1uK7DBRwolFFcDztwgk',
					'url'=>$arr[1],
					'topcolor'=>'#FF0000',
					'data'=>array(
						'name'   =>array('value'=>$arr[2],'color'=>"#173177"),
						'remark'=>array('value'=>$arr[3],'color'=>"#173177")
					)							
				));				
			break;
		}
	}
	//获取微信的open_id   如果登陆过取session中的  没有直接取  可以用
	function getOpenID($appid,$appsecret)
	{
		if($_SESSION['open_id']){
			return $_SESSION['open_id'];
		}
		else
		{		
			$state = $_GET['state'];			
			//访问微信授权
			if("weixinback" != $state){
				$backurl = DOMAIN_URL.$_SERVER['REQUEST_URI'];
				$url ="https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$backurl}&response_type=code&scope=snsapi_base&state=weixinback#wechat_redirect";
				header("Location: ".$url);exit;
			}			
			else
			{
				$code = $_GET['code'];
				$url="https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$appsecret}&code={$code}&grant_type=authorization_code";
				$token = json_decode(curl_get($url));
				if(isset($token->access_token)){
					return $_SESSION['open_id'] =htmlspecialchars($token->openid);
				}
				return false;
			}
		}
		
	}

	//用户授权 获取微信用户信息
	function get_authorize_user($appid,$appsecret)
	{	
		$state = $_GET['state'];			
		//访问微信授权
		if("weixinback" != $state){
			$backurl = DOMAIN_URL.$_SERVER['REQUEST_URI'];
			$url ="https://open.weixin.qq.com/connect/oauth2/authorize?appid={$appid}&redirect_uri={$backurl}&response_type=code&scope=snsapi_userinfo&state=weixinback#wechat_redirect";
			header("Location: ".$url);exit;
		}			
		else
		{
			$code = $_GET['code'];
			$url="https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$appsecret}&code={$code}&grant_type=authorization_code";
			$token = json_decode(curl_get($url),true);
			if(isset($token['access_token'])){
				$_SESSION['open_id'] =htmlspecialchars($token['openid']);
				$url="https://api.weixin.qq.com/sns/userinfo?access_token={$token['access_token']}&openid={$_SESSION['open_id']}&lang=zh_CN";
				$user = json_decode(curl_get($url),true);
				if(isset($user['nickname'])){
					return $user;
				}
			}
			
			return false;
			
		}
		
	}
	
	
	//获取微信用户信息  可以用
	function getUserInfo($appid,$appsecret){
		$access_token=$this->get_access_token($appid,$appsecret);
		$open_id=$this->getOpenID($appid,$appsecret);
		$user=json_decode(curl_get("https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$open_id&lang=zh_CN"),true);
		if($user['nickname']){
			return $user;
		}
		return false;
	}

	//创建菜单   不用
	public function create_menu($AppID,$AppSecret,$weixin_menu){
		$url='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.$this->get_access_token($AppID,$AppSecret);
		$return=curl_post($url, $weixin_menu);
		return json_decode($return,true);
	}

	//第一步 服务器配置时 效验自己设置的token  不用
	public function valid($token)
	{
		//查询数据库的token值
		$token = M('wechat')->where("id=1")->field('token')->find();
		$echoStr = $_GET["echostr"];
		// file_put_contents('menu.log',$menu);
		if($this->checkSignature($token['token'])){
			echo $echoStr;
			exit;
		}
	}

	//第一步 服务器配置时 效验自己设置的token  不用
	private function checkSignature($token)
	{
		$signature = $_GET["signature"];
		$timestamp = $_GET["timestamp"];
		$nonce = $_GET["nonce"];
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}


	
	
}
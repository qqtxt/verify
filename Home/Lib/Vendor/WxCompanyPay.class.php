<?php
defined('JETEE_PATH') or die('Deny Access');
/**
 * 微信企业付款
 */
class WxCompanyPay{	
	private $error='';
	private $result='';
	
	private $mchid='';
	private $mch_appid='';
	private $api_key='';
	private $apiclient_cert='';
	private $apiclient_key='';
	
public function __construct($mchid,$mch_appid,$key,$apiclient_cert='',$apiclient_key=''){
	$dir=str_replace('\\','/',__DIR__);
	$this->mchid=$mchid;//商户号   1278884801
	$this->mch_appid=$mch_appid;//appid wxb70ea36668e0434
	$this->api_key=$key;		//支付密匙
	$this->apiclient_cert= empty($apiclient_cert) ? $dir.'/apiclient_cert.pem' : $apiclient_cert;
	$this->apiclient_key = empty($apiclient_key)  ? $dir.'/apiclient_key.pem'  : $apiclient_cert;
}
public function get_error() {
	return $this->error;
}
public function get_result() {
	return $this->result;
}
/**
 * 企业付款到零钱
 * @param string $openid 付给谁
 * @param string $partner_trade_no 唯一订单号
 * @param int $amount 付款金额 单位分
 * @param int  0成功 1curl错误 2支付出错
 */
public function pay($openid,$partner_trade_no,$amount) {
	$nonce_str='qyzf'.$this->randString(20);//随机数
	//$partner_trade_no='test'.time().rand(10000, 99999);//商户订单号
	$check_name='NO_CHECK';//校验用户姓名选项，NO_CHECK：不校验真实姓名 FORCE_CHECK：强校验真实姓名（未实名认证的用户会校验失败，无法转账）OPTION_CHECK：针对已实名认证的用户才校验真实姓名（未实名认证用户不校验，可以转账成功）
	$re_user_name='姓名';//用户姓名
	//$amount=100;//金额（以分为单位，必须大于100）
	$desc='佣金即时到帐。';//描述
	$spbill_create_ip=$_SERVER["SERVER_ADDR"];//调用接口的机器Ip地址
	//封装成数据
	$dataArr=array('amount'=>$amount,'check_name'=>$check_name,'desc'=>$desc,'mch_appid'=>$this->mch_appid,'mchid'=>$this->mchid,'nonce_str'=>$nonce_str,'openid'=>$openid,'partner_trade_no'=>$partner_trade_no,'re_user_name'=>$re_user_name,'spbill_create_ip'=>$spbill_create_ip);
	$sign=$this->getSign($dataArr,$this->api_key);


	$data="<xml>
	<mch_appid>".$this->mch_appid."</mch_appid>
	<mchid>".$this->mchid."</mchid>
	<nonce_str>".$nonce_str."</nonce_str>
	<partner_trade_no>".$partner_trade_no."</partner_trade_no>
	<openid>".$openid."</openid>
	<check_name>".$check_name."</check_name>
	<re_user_name>".$re_user_name."</re_user_name>
	<amount>".$amount."</amount>
	<desc>".$desc."</desc>
	<spbill_create_ip>".$spbill_create_ip."</spbill_create_ip>
	<sign>".$sign."</sign>
	</xml>";

	$ch = curl_init ();
	$MENU_URL="https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";
	curl_setopt ( $ch, CURLOPT_URL, $MENU_URL );
	curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
	curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
	curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );

	curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
	curl_setopt($ch,CURLOPT_SSLCERT,$this->apiclient_cert);
	curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
	curl_setopt($ch,CURLOPT_SSLKEY,$this->apiclient_key);
	curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
	curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
	//curl_setopt ($ch,  CURLOPT_POST, 1);
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
	$info = curl_exec ( $ch );
	if (curl_errno ( $ch )) {
		$this->error=curl_error($ch );
		return 1;
	}
	curl_close ( $ch );
	$this->result= (array)simplexml_load_string($info, 'SimpleXMLElement', LIBXML_NOCDATA);
	if(isset($this->result['result_code']) && strtoupper($this->result['result_code'])=='SUCCESS'){		
		return 0;
	}
	else{
		$this->error=$this->result['err_code_des'];
		return 2;
	}
	
}


/**
 * 	作用：格式化参数，签名过程需要使用
 */
private function formatBizQueryParaMap($paraMap, $urlencode)
{
	$buff = "";
	ksort($paraMap);
	foreach ($paraMap as $k => $v)
	{
		if($urlencode)
		{
			$v = urlencode($v);
		}
		$buff .= $k . "=" . $v . "&";
	}
	$reqPar;
	if (strlen($buff) > 0)
	{
		$reqPar = substr($buff, 0, strlen($buff)-1);
	}
	return $reqPar;
}

/**
 * 	作用：生成签名
 */
private function getSign($Obj,$key)
{
	foreach ($Obj as $k => $v)
	{
		$Parameters[$k] = $v;
	}
	//签名步骤一：按字典序排序参数
	ksort($Parameters);
	$String = $this->formatBizQueryParaMap($Parameters, false);
	//echo '【string1】'.$String.'</br>';
	//签名步骤二：在string后加入KEY
	$String = $String."&key=$key";
	//echo "【string2】".$String."</br>";
	//签名步骤三：MD5加密
	$String = md5($String);
	//echo "【string3】 ".$String."</br>";
	//签名步骤四：所有字符转为大写
	$result_ = strtoupper($String);
	//echo "【result】 ".$result_."</br>";
	return $result_;
}
/**
 * 产生随机字串，可用来自动生成密码
 * 默认长度6位 字母和数字混合 支持中文
 * @param string $len 长度
 * @param string $type 字串类型
 * 0 字母 1 数字 其它 混合
 * @param string $addChars 额外字符
 * @return string
 */
private function randString($len=6,$type='',$addChars='') {
	$str ='';
	switch($type) {
		case 0:
			$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'.$addChars;
			break;
		case 1:
			$chars= str_repeat('0123456789',3);
			break;
		case 2:
			$chars='ABCDEFGHIJKLMNOPQRSTUVWXYZ'.$addChars;
			break;
		case 3:
			$chars='abcdefghijklmnopqrstuvwxyz'.$addChars;
			break;		
		default :
			// 默认去掉了容易混淆的字符oOLl和数字01，要添加请使用addChars参数
			$chars='ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'.$addChars;
			break;
	}
	if($len>10 ) {//位数过长重复字符串一定次数
		$chars= $type==1? str_repeat($chars,$len) : str_repeat($chars,5);
	}
	$chars   =   str_shuffle($chars);
	$str     =   substr($chars,0,$len);
	return $str;
}
}

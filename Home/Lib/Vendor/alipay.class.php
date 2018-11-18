<?php
//支付宝付款
define('ALIPAY_DEBUG',defined('APP_DEBUG') ? APP_DEBUG :0);//此接口只要配置这两处  
define('ALIPAY_LOG_PATH',LOG_PATH);//log文件位置  /结尾
/**
 *   加密工具类
 *
 * User: jiehua
 * Date: 16/3/30
 * Time: 下午3:25
 */

/**
 * 加密方法1
 * @param string $str
 * @return string
 */
 function encrypt($str,$screct_key){
	//AES, 128 模式加密数据 CBC
	$screct_key = base64_decode($screct_key);
	$str = trim($str);
	$str = addPKCS7Padding($str);
	$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC),1);
	$encrypt_str =  mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $screct_key, $str, MCRYPT_MODE_CBC);
	return base64_encode($encrypt_str);
}

/**
 * 解密方法 1
 * @param string $str
 * @return string
 */
 function decrypt($str,$screct_key){
	//AES, 128 模式加密数据 CBC
	$str = base64_decode($str);
	$screct_key = base64_decode($screct_key);
	$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_CBC),1);
	$encrypt_str =  mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $screct_key, $str, MCRYPT_MODE_CBC);
	$encrypt_str = trim($encrypt_str);

	$encrypt_str = stripPKSC7Padding($encrypt_str);
	return $encrypt_str;

}

/**
 * 填充算法 1
 * @param string $source
 * @return string
 */
function addPKCS7Padding($source){
	$source = trim($source);
	$block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);

	$pad = $block - (strlen($source) % $block);
	if ($pad <= $block) {
		$char = chr($pad);
		$source .= str_repeat($char, $pad);
	}
	return $source;
}
/**
 * 移去填充算法 1
 * @param string $source
 * @return string
 */
function stripPKSC7Padding($source){
	$source = trim($source);
	$char = substr($source, -1);
	$num = ord($char);
	if($num==62)return $source;
	$source = substr($source,0,-$num);
	return $source;
}
/**
 * ALIPAY API: alipay.trade.query request  统一收单线下交易查询
 *
 * @author auto create
 * @since 1.0, 2017-01-09 15:37:43
 */
class AlipayTradeQueryRequest
{
	private $bizContent;
	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
	private $needEncrypt=false;

	
	public function setBizContent($bizContent)
	{
		$this->bizContent = $bizContent;
		$this->apiParas["biz_content"] = $bizContent;
	}

	public function getBizContent()
	{
		return $this->bizContent;
	}

	public function getApiMethodName()
	{
		return "alipay.trade.query";
	}

	public function setNotifyUrl($notifyUrl)
	{
		$this->notifyUrl=$notifyUrl;
	}

	public function getNotifyUrl()
	{
		return $this->notifyUrl;
	}

	public function setReturnUrl($returnUrl)
	{
		$this->returnUrl=$returnUrl;
	}

	public function getReturnUrl()
	{
		return $this->returnUrl;
	}

	public function getApiParas()
	{
		return $this->apiParas;
	}

	public function getTerminalType()
	{
		return $this->terminalType;
	}

	public function setTerminalType($terminalType)
	{
		$this->terminalType = $terminalType;
	}

	public function getTerminalInfo()
	{
		return $this->terminalInfo;
	}

	public function setTerminalInfo($terminalInfo)
	{
		$this->terminalInfo = $terminalInfo;
	}

	public function getProdCode()
	{
		return $this->prodCode;
	}

	public function setProdCode($prodCode)
	{
		$this->prodCode = $prodCode;
	}

	public function setApiVersion($apiVersion)
	{
		$this->apiVersion=$apiVersion;
	}

	public function getApiVersion()
	{
		return $this->apiVersion;
	}

	public function setNeedEncrypt($needEncrypt)
	{

		$this->needEncrypt=$needEncrypt;

	}

	public function getNeedEncrypt()
	{
		return $this->needEncrypt;
	}

}
/**
 * ALIPAY API: alipay.trade.close request 统一收单交易关闭接口
 *
 * @author auto create
 * @since 1.0, 2016-11-09 22:08:22
 */
class AlipayTradeCloseRequest
{
	private $bizContent;
	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
	private $needEncrypt=false;

	
	public function setBizContent($bizContent)
	{
		$this->bizContent = $bizContent;
		$this->apiParas["biz_content"] = $bizContent;
	}

	public function getBizContent()
	{
		return $this->bizContent;
	}

	public function getApiMethodName()
	{
		return "alipay.trade.close";
	}

	public function setNotifyUrl($notifyUrl)
	{
		$this->notifyUrl=$notifyUrl;
	}

	public function getNotifyUrl()
	{
		return $this->notifyUrl;
	}

	public function setReturnUrl($returnUrl)
	{
		$this->returnUrl=$returnUrl;
	}

	public function getReturnUrl()
	{
		return $this->returnUrl;
	}

	public function getApiParas()
	{
		return $this->apiParas;
	}

	public function getTerminalType()
	{
		return $this->terminalType;
	}

	public function setTerminalType($terminalType)
	{
		$this->terminalType = $terminalType;
	}

	public function getTerminalInfo()
	{
		return $this->terminalInfo;
	}

	public function setTerminalInfo($terminalInfo)
	{
		$this->terminalInfo = $terminalInfo;
	}

	public function getProdCode()
	{
		return $this->prodCode;
	}

	public function setProdCode($prodCode)
	{
		$this->prodCode = $prodCode;
	}

	public function setApiVersion($apiVersion)
	{
		$this->apiVersion=$apiVersion;
	}

	public function getApiVersion()
	{
		return $this->apiVersion;
	}

  public function setNeedEncrypt($needEncrypt)
  {

	 $this->needEncrypt=$needEncrypt;

  }

  public function getNeedEncrypt()
  {
	return $this->needEncrypt;
  }

}
/**
 * ALIPAY API: alipay.trade.fastpay.refund.query request 商户可使用该接口查询自已通过alipay.trade.refund提交的退款请求是否执行成功。
 *
 * @author auto create
 * @since 1.0, 2017-03-23 19:11:54
 */
class AlipayTradeFastpayRefundQueryRequest
{

	private $bizContent;
	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
	private $needEncrypt=false;

	
	public function setBizContent($bizContent)
	{
		$this->bizContent = $bizContent;
		$this->apiParas["biz_content"] = $bizContent;
	}

	public function getBizContent()
	{
		return $this->bizContent;
	}

	public function getApiMethodName()
	{
		return "alipay.trade.fastpay.refund.query";
	}

	public function setNotifyUrl($notifyUrl)
	{
		$this->notifyUrl=$notifyUrl;
	}

	public function getNotifyUrl()
	{
		return $this->notifyUrl;
	}

	public function setReturnUrl($returnUrl)
	{
		$this->returnUrl=$returnUrl;
	}

	public function getReturnUrl()
	{
		return $this->returnUrl;
	}

	public function getApiParas()
	{
		return $this->apiParas;
	}

	public function getTerminalType()
	{
		return $this->terminalType;
	}

	public function setTerminalType($terminalType)
	{
		$this->terminalType = $terminalType;
	}

	public function getTerminalInfo()
	{
		return $this->terminalInfo;
	}

	public function setTerminalInfo($terminalInfo)
	{
		$this->terminalInfo = $terminalInfo;
	}

	public function getProdCode()
	{
		return $this->prodCode;
	}

	public function setProdCode($prodCode)
	{
		$this->prodCode = $prodCode;
	}

	public function setApiVersion($apiVersion)
	{
		$this->apiVersion=$apiVersion;
	}

	public function getApiVersion()
	{
		return $this->apiVersion;
	}

  public function setNeedEncrypt($needEncrypt)
  {

	 $this->needEncrypt=$needEncrypt;

  }

  public function getNeedEncrypt()
  {
	return $this->needEncrypt;
  }

}

/**
 * ALIPAY API: alipay.trade.page.pay request 统一收单下单并支付页面接口
 *
 * @author auto create
 * @since 1.0, 2017-04-06 15:55:36
 */
class AlipayTradePagePayRequest
{
	private $bizContent;
	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
	private $needEncrypt=false;

	
	public function setBizContent($bizContent)
	{
		$this->bizContent = $bizContent;
		$this->apiParas["biz_content"] = $bizContent;
	}

	public function getBizContent()
	{
		return $this->bizContent;
	}

	public function getApiMethodName()
	{
		return "alipay.trade.page.pay";
	}

	public function setNotifyUrl($notifyUrl)
	{
		$this->notifyUrl=$notifyUrl;
	}

	public function getNotifyUrl()
	{
		return $this->notifyUrl;
	}

	public function setReturnUrl($returnUrl)
	{
		$this->returnUrl=$returnUrl;
	}

	public function getReturnUrl()
	{
		return $this->returnUrl;
	}

	public function getApiParas()
	{
		return $this->apiParas;
	}

	public function getTerminalType()
	{
		return $this->terminalType;
	}

	public function setTerminalType($terminalType)
	{
		$this->terminalType = $terminalType;
	}

	public function getTerminalInfo()
	{
		return $this->terminalInfo;
	}

	public function setTerminalInfo($terminalInfo)
	{
		$this->terminalInfo = $terminalInfo;
	}

	public function getProdCode()
	{
		return $this->prodCode;
	}

	public function setProdCode($prodCode)
	{
		$this->prodCode = $prodCode;
	}

	public function setApiVersion($apiVersion)
	{
		$this->apiVersion=$apiVersion;
	}

	public function getApiVersion()
	{
		return $this->apiVersion;
	}

  public function setNeedEncrypt($needEncrypt)
  {

	 $this->needEncrypt=$needEncrypt;

  }

  public function getNeedEncrypt()
  {
	return $this->needEncrypt;
  }

}
/**
 * ALIPAY API: alipay.data.dataservice.bill.downloadurl.query request 无授权模式的查询对账单下载地址
 *
 * @author auto create
 * @since 1.0, 2016-09-20 16:35:20
 */
class AlipayDataDataserviceBillDownloadurlQueryRequest
{

	private $bizContent;
	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
	private $needEncrypt=false;

	
	public function setBizContent($bizContent)
	{
		$this->bizContent = $bizContent;
		$this->apiParas["biz_content"] = $bizContent;
	}

	public function getBizContent()
	{
		return $this->bizContent;
	}

	public function getApiMethodName()
	{
		return "alipay.data.dataservice.bill.downloadurl.query";
	}

	public function setNotifyUrl($notifyUrl)
	{
		$this->notifyUrl=$notifyUrl;
	}

	public function getNotifyUrl()
	{
		return $this->notifyUrl;
	}

	public function setReturnUrl($returnUrl)
	{
		$this->returnUrl=$returnUrl;
	}

	public function getReturnUrl()
	{
		return $this->returnUrl;
	}

	public function getApiParas()
	{
		return $this->apiParas;
	}

	public function getTerminalType()
	{
		return $this->terminalType;
	}

	public function setTerminalType($terminalType)
	{
		$this->terminalType = $terminalType;
	}

	public function getTerminalInfo()
	{
		return $this->terminalInfo;
	}

	public function setTerminalInfo($terminalInfo)
	{
		$this->terminalInfo = $terminalInfo;
	}

	public function getProdCode()
	{
		return $this->prodCode;
	}

	public function setProdCode($prodCode)
	{
		$this->prodCode = $prodCode;
	}

	public function setApiVersion($apiVersion)
	{
		$this->apiVersion=$apiVersion;
	}

	public function getApiVersion()
	{
		return $this->apiVersion;
	}

  public function setNeedEncrypt($needEncrypt)
  {

	 $this->needEncrypt=$needEncrypt;

  }

  public function getNeedEncrypt()
  {
	return $this->needEncrypt;
  }

}

/* *
 * 功能：alipay.trade.page.pay  统一收单交易支付接口  构造提交数据
 * 版本：2.0
 * 修改日期：2017-05-01
 */
class AlipayTradePagePayContentBuilder
{

	// 订单描述，可以对交易或商品进行一个详细地描述，比如填写"购买商品2件共15.00元"
	private $body;
	// 订单标题，粗略描述用户的支付目的。
	private $subject;
	// 商户订单号.
	private $outTradeNo;
	// (推荐使用，相对时间) 该笔订单允许的最晚付款时间，逾期将关闭交易。取值范围：1m～15d。m-分钟，h-小时，d-天，1c-当天（1c-当天的情况下，无论交易何时创建，都在0点关闭）。 该参数数值不接受小数点， 如 1.5h，可转换为 90m
	private $timeExpress;

	// 订单总金额，整形，此处单位为元，精确到小数点后2位，不能超过1亿元
	private $totalAmount;

	// 产品标示码，固定值：QUICK_WAP_PAY
	private $productCode;

	private $bizContentarr = array();

	private $bizContent = NULL;

	public function getBizContent()
	{
		if(!empty($this->bizContentarr)){
			$this->bizContent = json_encode($this->bizContentarr,JSON_UNESCAPED_UNICODE);
		}
		return $this->bizContent;
	}

	public function __construct()
	{
		$this->bizContentarr['product_code'] = "FAST_INSTANT_TRADE_PAY";
	}

	public function AlipayTradeWapPayContentBuilder()
	{
		$this->__construct();
	}

	public function getBody()
	{
		return $this->body;
	}

	public function setBody($body)
	{
		$this->body = $body;
		$this->bizContentarr['body'] = $body;
	}

	public function setSubject($subject)
	{
		$this->subject = $subject;
		$this->bizContentarr['subject'] = $subject;
	}

	public function getSubject()
	{
		return $this->subject;
	}

	public function getOutTradeNo()
	{
		return $this->outTradeNo;
	}

	public function setOutTradeNo($outTradeNo)
	{
		$this->outTradeNo = $outTradeNo;
		$this->bizContentarr['out_trade_no'] = $outTradeNo;
	}

	public function setTimeExpress($timeExpress)
	{
		$this->timeExpress = $timeExpress;
		$this->bizContentarr['timeout_express'] = $timeExpress;
	}

	public function getTimeExpress()
	{
		return $this->timeExpress;
	}

	public function setTotalAmount($totalAmount)
	{
		$this->totalAmount = $totalAmount;
		$this->bizContentarr['total_amount'] = $totalAmount;
	}

	public function getTotalAmount()
	{
		return $this->totalAmount;
	}

}
class AlipayTradeFastpayRefundQueryContentBuilder
{

	// 商户订单号.
	private $outTradeNo;
	// 支付宝交易号
	private $tradeNo;  
	// 请求退款接口时，传入的退款请求号，如果在退款请求时未传入，则该值为创建交易时的外部交易号
	private $outRequestNo;
	
	private $bizContentarr = array();

	private $bizContent = NULL;

	public function getBizContent()
	{
		if(!empty($this->bizContentarr)){
			$this->bizContent = json_encode($this->bizContentarr,JSON_UNESCAPED_UNICODE);
		}
		return $this->bizContent;
	}

	public function getTradeNo()
	{
		return $this->tradeNo;
	}

	public function setTradeNo($tradeNo)
	{
		$this->tradeNo = $tradeNo;
		$this->bizContentarr['trade_no'] = $tradeNo;
	}

	public function getOutTradeNo()
	{
		return $this->outTradeNo;
	}

	public function setOutTradeNo($outTradeNo)
	{
		$this->outTradeNo = $outTradeNo;
		$this->bizContentarr['out_trade_no'] = $outTradeNo;
	}
	public function getOutRequestNo()
	{
		return $this->outRequestNo;
	}
	public function setOutRequestNo($outRequestNo)
	{
		$this->outRequestNo = $outRequestNo;
		$this->bizContentarr['out_request_no'] = $outRequestNo;
	}
}
class AlipayTradeCloseContentBuilder
{

	// 商户订单号.
	private $outTradeNo;

	// 支付宝交易号
	private $tradeNo;
	
	//卖家端自定义的的操作员 ID
	private $operatorId;

	private $bizContentarr = array();

	private $bizContent = NULL;

	public function getBizContent()
	{
		if(!empty($this->bizContentarr)){
			$this->bizContent = json_encode($this->bizContentarr,JSON_UNESCAPED_UNICODE);
		}
		return $this->bizContent;
	}

	public function getTradeNo()
	{
		return $this->tradeNo;
	}

	public function setTradeNo($tradeNo)
	{
		$this->tradeNo = $tradeNo;
		$this->bizContentarr['trade_no'] = $tradeNo;
	}

	public function getOutTradeNo()
	{
		return $this->outTradeNo;
	}

	public function setOutTradeNo($outTradeNo)
	{
		$this->outTradeNo = $outTradeNo;
		$this->bizContentarr['out_trade_no'] = $outTradeNo;
	}
	public function getOperatorId()
	{
		return $this->operatorId;
	}
	
	public function setOperatorId($operatorId)
	{
		$this->operatorId = $operatorId;
		$this->bizContentarr['operator_id'] = $operatorId;
	}

}
class AlipayTradeQueryContentBuilder
{

	// 商户订单号.
	private $outTradeNo;

	// 支付宝交易号
	private $tradeNo;
	
	private $bizContentarr = array();

	private $bizContent = NULL;

	public function getBizContent()
	{
		if(!empty($this->bizContentarr)){
			$this->bizContent = json_encode($this->bizContentarr,JSON_UNESCAPED_UNICODE);
		}
		return $this->bizContent;
	}

	public function getTradeNo()
	{
		return $this->tradeNo;
	}

	public function setTradeNo($tradeNo)
	{
		$this->tradeNo = $tradeNo;
		$this->bizContentarr['trade_no'] = $tradeNo;
	}

	public function getOutTradeNo()
	{
		return $this->outTradeNo;
	}

	public function setOutTradeNo($outTradeNo)
	{
		$this->outTradeNo = $outTradeNo;
		$this->bizContentarr['out_trade_no'] = $outTradeNo;
	}
}

class AlipayTradeRefundContentBuilder
{

	// 商户订单号.
	private $outTradeNo;

	// 支付宝交易号
	private $tradeNo;

	// 退款的金额
	private $refundAmount;

	// 退款原因说明
	private $refundReason;

	// 标识一次退款请求号，同一笔交易多次退款保证唯一，部分退款此参数必填
	private $outRequestNo;

	private $bizContentarr = array();

	private $bizContent = NULL;

	public function getBizContent()
	{
		if(!empty($this->bizContentarr)){
			$this->bizContent = json_encode($this->bizContentarr,JSON_UNESCAPED_UNICODE);
		}
		return $this->bizContent;
	}

	public function getTradeNo()
	{
		return $this->tradeNo;
	}

	public function setTradeNo($tradeNo)
	{
		$this->tradeNo = $tradeNo;
		$this->bizContentarr['trade_no'] = $tradeNo;
	}

	public function getOutTradeNo()
	{
		return $this->outTradeNo;
	}

	public function setOutTradeNo($outTradeNo)
	{
		$this->outTradeNo = $outTradeNo;
		$this->bizContentarr['out_trade_no'] = $outTradeNo;
	}

	public function getRefundAmount()
	{
		return $this->refundAmount;
	}

	public function setRefundAmount($refundAmount)
	{
		$this->refundAmount = $refundAmount;
		$this->bizContentarr['refund_amount'] = $refundAmount;
	}

	public function getRefundReason()
	{
		return $this->refundReason;
	}

	public function setRefundReason($refundReason)
	{
		$this->refundReason = $refundReason;
		$this->bizContentarr['refund_reason'] = $refundReason;
	}

	public function getOutRequestNo()
	{
		return $this->outRequestNo;
	}

	public function setOutRequestNo($outRequestNo)
	{
		$this->outRequestNo = $outRequestNo;
		$this->bizContentarr['out_request_no'] = $outRequestNo;
	}
}
/**
 * ALIPAY API: alipay.trade.refund request 统一收单交易退款接口
 *
 * @author auto create
 * @since 1.0, 2017-01-13 19:12:23
 */
class AlipayTradeRefundRequest
{
	private $bizContent;
	private $apiParas = array();
	private $terminalType;
	private $terminalInfo;
	private $prodCode;
	private $apiVersion="1.0";
	private $notifyUrl;
	private $returnUrl;
	private $needEncrypt=false;

	
	public function setBizContent($bizContent)
	{
		$this->bizContent = $bizContent;
		$this->apiParas["biz_content"] = $bizContent;
	}

	public function getBizContent()
	{
		return $this->bizContent;
	}

	public function getApiMethodName()
	{
		return "alipay.trade.refund";
	}

	public function setNotifyUrl($notifyUrl)
	{
		$this->notifyUrl=$notifyUrl;
	}

	public function getNotifyUrl()
	{
		return $this->notifyUrl;
	}

	public function setReturnUrl($returnUrl)
	{
		$this->returnUrl=$returnUrl;
	}

	public function getReturnUrl()
	{
		return $this->returnUrl;
	}

	public function getApiParas()
	{
		return $this->apiParas;
	}

	public function getTerminalType()
	{
		return $this->terminalType;
	}

	public function setTerminalType($terminalType)
	{
		$this->terminalType = $terminalType;
	}

	public function getTerminalInfo()
	{
		return $this->terminalInfo;
	}

	public function setTerminalInfo($terminalInfo)
	{
		$this->terminalInfo = $terminalInfo;
	}

	public function getProdCode()
	{
		return $this->prodCode;
	}

	public function setProdCode($prodCode)
	{
		$this->prodCode = $prodCode;
	}

	public function setApiVersion($apiVersion)
	{
		$this->apiVersion=$apiVersion;
	}

	public function getApiVersion()
	{
		return $this->apiVersion;
	}

  public function setNeedEncrypt($needEncrypt)
  {

	 $this->needEncrypt=$needEncrypt;

  }

  public function getNeedEncrypt()
  {
	return $this->needEncrypt;
  }

}

class AlipayTradeService {
	//支付宝网关地址
	public $gateway_url = "https://openapi.alipay.com/gateway.do";
	//支付宝公钥
	public $alipay_public_key;
	//商户私钥
	public $private_key;
	//应用id
	public $appid;
	//编码格式
	public $charset = "UTF-8";
	public $token = NULL;	
	//返回数据格式
	public $format = "json";
	//签名方式
	public $signtype = "RSA2";
	function __construct($alipay_config){
		$this->gateway_url = $alipay_config['gatewayUrl'];
		$this->appid = $alipay_config['app_id'];
		$this->private_key = $alipay_config['merchant_private_key'];
		$this->alipay_public_key = $alipay_config['alipay_public_key'];
		$this->charset = $alipay_config['charset'];
		$this->signtype=$alipay_config['sign_type'];

		if(empty($this->appid)||trim($this->appid)==""){
			throw new Exception("appid should not be NULL!");
		}
		if(empty($this->private_key)||trim($this->private_key)==""){
			throw new Exception("private_key should not be NULL!");
		}
		if(empty($this->alipay_public_key)||trim($this->alipay_public_key)==""){
			throw new Exception("alipay_public_key should not be NULL!");
		}
		if(empty($this->charset)||trim($this->charset)==""){
			throw new Exception("charset should not be NULL!");
		}
		if(empty($this->gateway_url)||trim($this->gateway_url)==""){
			throw new Exception("gateway_url should not be NULL!");
		}

	}

	/**
	 * alipay.trade.page.pay  1
	 * @param $builder 业务参数，使用buildmodel中的对象生成。
	 * @param $return_url 同步跳转地址，公网可以访问
	 * @param $notify_url 异步通知地址，公网可以访问
	 * @return $response 支付宝返回的信息
	*/
	function pagePay($builder,$return_url,$notify_url) {
	
		$biz_content=$builder->getBizContent();
		//打印业务参数
		$this->writeLog(__LINE__ . $biz_content);
		$request = new AlipayTradePagePayRequest();	
		$request->setNotifyUrl($notify_url);
		$request->setReturnUrl($return_url);
		$request->setBizContent($biz_content);
		// 首先调用支付api
		$response = $this->aopclientRequestExecute($request,true);
		// $response = $response->alipay_trade_wap_pay_response;
		return $response;
	}

	/**
	 * sdkClient  1
	 * @param $request 接口请求参数对象。
	 * @param $ispage  是否是页面接口，电脑网站支付是页面表单接口。
	 * @return $response 支付宝返回的信息
	*/
	function aopclientRequestExecute($request,$ispage=false) {
		$aop = new AopClient ();
		$aop->gatewayUrl = $this->gateway_url;
		$aop->appId = $this->appid;
		$aop->rsaPrivateKey =  $this->private_key;
		$aop->alipayrsaPublicKey = $this->alipay_public_key;
		$aop->apiVersion ="1.0";
		$aop->postCharset = $this->charset;
		$aop->format= $this->format;
		$aop->signType=$this->signtype;
		// 开启页面信息输出
		$aop->debugInfo=true;
		if($ispage)
		{
			$result = $aop->pageExecute($request,"post");
			echo $result;
		}
		else 
		{
			$result = $aop->Execute($request);
		}
		
		//打开后，将报文写入log文件
		$this->writeLog(__LINE__ . "response: ".var_export($result,true));
		return $result;
	}

	/**
	 * alipay.trade.query (统一收单线下交易查询)  1
	 * @param $builder 业务参数，使用buildmodel中的对象生成。
	 * @return $response 支付宝返回的信息
	*/
	function Query($builder){
		$biz_content=$builder->getBizContent();
		//打印业务参数
		$this->writeLog(__LINE__ . $biz_content);
		$request = new AlipayTradeQueryRequest();
		$request->setBizContent ( $biz_content );

		$response = $this->aopclientRequestExecute($request);
		$response = $response->alipay_trade_query_response;
		return $response;
	}
	
	/**
	 * alipay.trade.refund (统一收单交易退款接口)
	 * @param $builder 业务参数，使用buildmodel中的对象生成。
	 * @return $response 支付宝返回的信息
	 */
	function Refund($builder){
		$biz_content=$builder->getBizContent();
		//打印业务参数
		$this->writeLog(__LINE__ . $biz_content);
		$request = new AlipayTradeRefundRequest();
		$request->setBizContent ( $biz_content );
	
		$response = $this->aopclientRequestExecute($request);
		$response = $response->alipay_trade_refund_response;
		return $response;
	}

	/**
	 * alipay.trade.close (统一收单交易关闭接口) 1
	 * @param $builder 业务参数，使用buildmodel中的对象生成。
	 * @return $response 支付宝返回的信息
	 */
	function Close($builder){
		$biz_content=$builder->getBizContent();
		//打印业务参数
		$this->writeLog(__LINE__ . $biz_content);
		$request = new AlipayTradeCloseRequest();
		$request->setBizContent( $biz_content );	
		$response = $this->aopclientRequestExecute($request);
		$response = $response->alipay_trade_close_response;
		return $response;
	}
	
	/**
	 * 退款查询   alipay.trade.fastpay.refund.query (统一收单交易退款查询)  1
	 * @param $builder 业务参数，使用buildmodel中的对象生成。
	 * @return $response 支付宝返回的信息
	 */
	function refundQuery($builder){
		$biz_content=$builder->getBizContent();
		//打印业务参数
		$this->writeLog(__LINE__ . $biz_content);
		$request = new AlipayTradeFastpayRefundQueryRequest();
		$request->setBizContent ( $biz_content );
	
		$response = $this->aopclientRequestExecute($request);
		return $response;
	}
	/**
	 * alipay.data.dataservice.bill.downloadurl.query (查询对账单下载地址) 1
	 * @param $builder 业务参数，使用buildmodel中的对象生成。
	 * @return $response 支付宝返回的信息
	 */
	function downloadurlQuery($builder){
		$biz_content=$builder->getBizContent();
		//打印业务参数
		$this->writeLog(__LINE__ . $biz_content);
		$request = new alipaydatadataservicebilldownloadurlqueryRequest();
		$request->setBizContent ( $biz_content );
	
		$response = $this->aopclientRequestExecute($request);
		$response = $response->alipay_data_dataservice_bill_downloadurl_query_response;
		return $response;
	}

	/**
	 * 验签方法
	 * @param $arr 验签支付宝返回的信息，使用支付宝公钥。
	 * @return boolean
	 */
	function check($arr){
		$aop = new AopClient();
		$aop->alipayrsaPublicKey = $this->alipay_public_key;
		$result = $aop->rsaCheckV1($arr, $this->alipay_public_key, $this->signtype);
		return $result;
	}
	
	/**
	 * 请确保项目文件有可写权限，不然打印不了日志。
	 */
	function writeLog($text) {// $text=iconv("GBK", "UTF-8//IGNORE", $text);
		if(defined('ALIPAY_DEBUG') && ALIPAY_DEBUG){
			file_put_contents(ALIPAY_LOG_PATH.'alipay_log.txt', @date ( "Y-m-d H:i:s" ) . "  " . $text . "\r\n\r\n", FILE_APPEND );
		}
	}
}
/**
 * 多媒体文件客户端
 * @author yuanwai.wang
 * @version $Id: AlipayMobilePublicMultiMediaExecute.php, v 0.1 Aug 15, 2014 10:19:01 AM yuanwai.wang Exp $
 */

//namespace alipay\api ;
class AlipayMobilePublicMultiMediaExecute{

	private $code = 200 ;
	private $msg = '';
	private $body = '';
	private $params = '';

	private $fileSuffix = array(
		"image/jpeg" 		=> 'jpg', //+
		"text/plain"		=> 'text'
	);

	/*
	 * @$header : 头部
	 * */
	function __construct( $header, $body, $httpCode ){
		$this -> code = $httpCode;
		$this -> msg = '';
		$this -> params = $header ;
		$this -> body = $body;
	}

	/**
	 *
	 * @return text | bin
	 */
	public function getCode(){
		return $this -> code ;
	}

	/**
	 *
	 * @return text | bin
	 */
	public function getMsg(){
		return $this -> msg ;
	}

	/**
	 *
	 * @return text | bin
	 */
	public function getType(){
		$subject = $this -> params ;
		$pattern = '/Content\-Type:([^;]+)/';
		preg_match($pattern, $subject, $matches);
		if( $matches ){
			$type = $matches[1];
		}else{
			$type = 'application/download';
		}
		return str_replace( ' ', '', $type );
	}

	/**
	 *
	 * @return text | bin
	 */
	public function getContentLength(){
		$subject = $this -> params ;
		$pattern = '/Content-Length:\s*([^\n]+)/';
		preg_match($pattern, $subject, $matches);
		return (int)( isset($matches[1] ) ? $matches[1]  : '' );
	}


	public function getFileSuffix( $fileType ){
		$type = isset( $this -> fileSuffix[ $fileType ] ) ? $this -> fileSuffix[ $fileType ] : 'text/plain' ;
		if( !$type ){
			$type = 'json';
		}
		return $type;
	}
	/**
	 *
	 * @return text | bin
	 */
	public function getBody(){
		//header('Content-type: image/jpeg');
		return $this -> body ;
	}

	/**
	 * 获取参数
	 * @return text | bin
	 */
	public function getParams(){
		return $this -> params ;
	}


}

/**
 * 多媒体文件客户端
 * @author yikai.hu
 * @version $Id: AlipayMobilePublicMultiMediaClient.php, v 0.1 Aug 15, 2014 10:19:01 AM yikai.hu Exp $
 */
class AlipayMobilePublicMultiMediaClient{

	private				$DEFAULT_CHARSET = 'UTF-8';
	private				$METHOD_POST     = "POST";
	private				$METHOD_GET      = "GET";
	private				$SIGN			= 'sign'; //get name

	private				$timeout = 10 ;// 超时时间
	private				$serverUrl;
	private				$appId;
	private				$privateKey;
	private				$prodCode;
	private				$format          = 'json'; //todo
	private				$sign_type       = 'RSA'; //todo

	private				$charset;
	private				$apiVersion    = "1.0";
	private				$apiMethodName = "alipay.mobile.public.multimedia.download";
	private				$media_id = "L21pZnMvVDFQV3hYWGJKWFhYYUNucHJYP3Q9YW13ZiZ4c2lnPTU0MzRhYjg1ZTZjNWJmZTMxZGJiNjIzNDdjMzFkNzkw575";
	//此处写死的，实际开发中，请传入

	private				$connectTimeout  = 3000;
	private				$readTimeout     = 15000;



	function __construct($serverUrl = '', $appId = '', $partner_private_key = '', $format = '', $charset = 'GBK'){
		$this -> serverUrl = $serverUrl;
		$this -> appId = $appId;
		$this -> privateKey = $partner_private_key;
		$this -> format = $format;
		$this -> charset = $charset;
	}

	/**
	 * getContents 获取网址内容
	 * @param $request
	 * @return text | bin
	 */
	public function getContents(){
		//自己的服务器如果没有 curl，可用：fsockopen() 等
		//1:
		//2： 私钥格式
		$datas = array(
			"app_id" 		=> $this -> appId,
			"method" 		=> $this -> METHOD_POST,
			"sign_type" 	=> $this -> sign_type,
			"version" 		=> $this -> apiVersion,
			"timestamp" 	=> date('Y-m-d H:i:s')  ,//yyyy-MM-dd HH:mm:ss
			"biz_content" 	=> '{"mediaId":"'. $this -> media_id  .'"}',
			"charset" 		=> $this -> charset
		);
		//要提交的数据
		$data_sign = $this ->buildGetUrl( $datas );
		$post_data = $data_sign;
		//初始化 curl
		$ch = curl_init();
		//设置目标服务器
		curl_setopt($ch, CURLOPT_URL, $this -> serverUrl );
		curl_setopt($ch, CURLOPT_HEADER, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//超时时间
		curl_setopt($ch, CURLOPT_TIMEOUT, $this-> timeout);
		if( $this-> METHOD_POST == 'POST'){
			// post数据
			curl_setopt($ch, CURLOPT_POST, 1);
			// post的变量
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		}
		$output = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		echo $output;
		//分离头部
		//list($header, $body) = explode("\r\n\r\n", $output, 2);
		$datas = explode("\r\n\r\n", $output, 2);
		$header = $datas[0];
		if( $httpCode == '200'){
			$body = $datas[1];
		}else{
			$body = '';

		}
		return $this -> execute( $header, $body, $httpCode );
	}

	/**
	 *
	 * @param $request
	 * @return text | bin
	 */
	public function execute( $header = '', $body = '', $httpCode = '' ){
		$exe = new AlipayMobilePublicMultiMediaExecute( $header, $body, $httpCode );
		return $exe;
	}

	public function buildGetUrl( $query = array() ){

		if( ! is_array( $query ) ){
			//exit;
		}

		//排序参数，
		$data = $this -> buildQuery( $query );


		// 私钥密码
		$passphrase = '';
		$key_width = 64;

		//私钥
		$privateKey = $this -> privateKey;
		$p_key = array();
		//如果私钥是 1行
		if( ! stripos( $privateKey, "\n" )  ){
			$i = 0;
			while( $key_str = substr( $privateKey , $i * $key_width , $key_width) ){
				$p_key[] = $key_str;
				$i ++ ;
			}
		}else{
			//echo '一行？';
		}
		$privateKey = "-----BEGIN RSA PRIVATE KEY-----\n" . implode("\n", $p_key) ;
		$privateKey = $privateKey ."\n-----END RSA PRIVATE KEY-----";

		//私钥
		$private_id = openssl_pkey_get_private( $privateKey , $passphrase);
		// 签名
		$signature = '';
		if("RSA2"==$this->sign_type){
			openssl_sign($data, $signature, $private_id, OPENSSL_ALGO_SHA256 );
		}else{
			openssl_sign($data, $signature, $private_id, OPENSSL_ALGO_SHA1 );
		}

		openssl_free_key( $private_id );
		//加密后的内容通常含有特殊字符，需要编码转换下
		$signature = base64_encode($signature);
		$signature = urlencode( $signature );
		$out = $data .'&'. $this -> SIGN .'='. $signature;
		return $out ;
	}

	/*
	 * 查询参数排序 a-z
	 * */
	public function buildQuery( $query ){
		if ( !$query ) {
			return null;
		}
		//将要 参数 排序
		ksort( $query );
		//重新组装参数
		$params = array();
		foreach($query as $key => $value){
			$params[] = $key .'='. $value ;
		}
		$data = implode('&', $params);

		return $data;

	}
}
class LtInflector
{
	public $conf = array("separator" => "_");
	public function camelize($uncamelized_words){
		$uncamelized_words = $this->conf["separator"] . str_replace($this->conf["separator"] , " ", strtolower($uncamelized_words));
		return ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $this->conf["separator"] );
	}

	public function uncamelize($camelCaps){
		return strtolower(preg_replace('/([a-z])([A-Z])/', "$1" . $this->conf["separator"] . "$2", $camelCaps));
	}
}
class SignData {
	public $signSourceData=null;
	public $sign=null;
}
/**
 *  TODO 补充说明 
 *
 * User: jiehua
 * Date: 16/3/30
 * Time: 下午8:55
 */

class EncryptParseItem {
	public $startIndex;
	public $endIndex;
	public $encryptContent;
}

class EncryptResponseData {
	public $realContent;
	public $returnContent;

} 
class AopClient {
	//应用ID
	public $appId;	
	//私钥文件路径
	public $rsaPrivateKeyFilePath;
	//私钥值
	public $rsaPrivateKey;
	//网关
	public $gatewayUrl = "https://openapi.alipay.com/gateway.do";
	//返回数据格式
	public $format = "json";
	//api版本
	public $apiVersion = "1.0";
	// 表单提交字符集编码
	public $postCharset = "UTF-8";
	//使用文件读取文件格式，请只传递该值
	public $alipayPublicKey = null;
	//使用读取字符串格式，请只传递该值
	public $alipayrsaPublicKey;
	public $debugInfo = false;
	private $fileCharset = "UTF-8";
	private $RESPONSE_SUFFIX = "_response";
	private $ERROR_RESPONSE = "error_response";
	private $SIGN_NODE_NAME = "sign";
	//加密XML节点名称
	private $ENCRYPT_XML_NODE_NAME = "response_encrypted";
	private $needEncrypt = false;
	//签名类型
	public $signType = "RSA";
	//加密密钥和类型

	public $encryptKey;
	public $encryptType = "AES";
	protected $alipaySdkVersion = "alipay-sdk-php-20161101";
	public function generateSign($params, $signType = "RSA") {
		return $this->sign($this->getSignContent($params), $signType);
	}

	public function rsaSign($params, $signType = "RSA") {
		return $this->sign($this->getSignContent($params), $signType);
	}

	public function getSignContent($params) {
		ksort($params);
		$stringToBeSigned = "";
		$i = 0;
		foreach ($params as $k => $v) {
			if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {
				// 转换成目标字符集
				$v = $this->characet($v, $this->postCharset);
				if ($i == 0) {
					$stringToBeSigned .= "$k" . "=" . "$v";
				} else {
					$stringToBeSigned .= "&" . "$k" . "=" . "$v";
				}
				$i++;
			}
		}
		unset ($k, $v);
		return $stringToBeSigned;
	}


	//此方法对value做urlencode
	public function getSignContentUrlencode($params) {
		ksort($params);
		$stringToBeSigned = "";
		$i = 0;
		foreach ($params as $k => $v) {
			if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {
				// 转换成目标字符集
				$v = $this->characet($v, $this->postCharset);
				if ($i == 0) {
					$stringToBeSigned .= "$k" . "=" . urlencode($v);
				} else {
					$stringToBeSigned .= "&" . "$k" . "=" . urlencode($v);
				}
				$i++;
			}
		}
		unset ($k, $v);
		return $stringToBeSigned;
	}

	protected function sign($data, $signType = "RSA") {
		if($this->checkEmpty($this->rsaPrivateKeyFilePath)){
			$priKey=$this->rsaPrivateKey;
			$res = "-----BEGIN RSA PRIVATE KEY-----\n" .
				wordwrap($priKey, 64, "\n", true) .
				"\n-----END RSA PRIVATE KEY-----";
		}else {
			$priKey = file_get_contents($this->rsaPrivateKeyFilePath);
			$res = openssl_get_privatekey($priKey);
		}

		($res) or die('您使用的私钥格式错误，请检查RSA私钥配置'); 

		if ("RSA2" == $signType) {
			openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);
		} else {
			openssl_sign($data, $sign, $res);
		}

		if(!$this->checkEmpty($this->rsaPrivateKeyFilePath)){
			openssl_free_key($res);
		}
		$sign = base64_encode($sign);
		return $sign;
	}

	/**
	 * RSA单独签名方法，未做字符串处理,字符串处理见getSignContent()
	 * @param $data 待签名字符串
	 * @param $privatekey 商户私钥，根据keyfromfile来判断是读取字符串还是读取文件，false:填写私钥字符串去回车和空格 true:填写私钥文件路径 
	 * @param $signType 签名方式，RSA:SHA1     RSA2:SHA256 
	 * @param $keyfromfile 私钥获取方式，读取字符串还是读文件
	 * @return string 
	 * @author mengyu.wh
	 */
	public function alonersaSign($data,$privatekey,$signType = "RSA",$keyfromfile=false) {
		if(!$keyfromfile){
			$priKey=$privatekey;
			$res = "-----BEGIN RSA PRIVATE KEY-----\n" .
				wordwrap($priKey, 64, "\n", true) .
				"\n-----END RSA PRIVATE KEY-----";
		}
		else{
			$priKey = file_get_contents($privatekey);
			$res = openssl_get_privatekey($priKey);
		}

		($res) or die('您使用的私钥格式错误，请检查RSA私钥配置'); 
		if ("RSA2" == $signType) {
			openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);
		} else {
			openssl_sign($data, $sign, $res);
		}

		if($keyfromfile){
			openssl_free_key($res);
		}
		$sign = base64_encode($sign);
		return $sign;
	}


	protected function curl($url, $postFields = null) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FAILONERROR, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$postBodyString = "";
		$encodeArray = Array();
		$postMultipart = false;
		if (is_array($postFields) && 0 < count($postFields)) {
			foreach ($postFields as $k => $v) {
				if ("@" != substr($v, 0, 1)) //判断是不是文件上传
				{
					$postBodyString .= "$k=" . urlencode($this->characet($v, $this->postCharset)) . "&";
					$encodeArray[$k] = $this->characet($v, $this->postCharset);
				} else //文件上传用multipart/form-data，否则用www-form-urlencoded
				{
					$postMultipart = true;
					$encodeArray[$k] = new \CURLFile(substr($v, 1));
				}

			}
			unset ($k, $v);
			curl_setopt($ch, CURLOPT_POST, true);
			if ($postMultipart) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, $encodeArray);
			} else {
				curl_setopt($ch, CURLOPT_POSTFIELDS, substr($postBodyString, 0, -1));
			}
		}

		if ($postMultipart) {
			$headers = array('content-type: multipart/form-data;charset=' . $this->postCharset . ';boundary=' . $this->getMillisecond());
		} else {

			$headers = array('content-type: application/x-www-form-urlencoded;charset=' . $this->postCharset);
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$reponse = curl_exec($ch);
		if (curl_errno($ch)) {
			throw new Exception(curl_error($ch), 0);
		} else {
			$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if (200 !== $httpStatusCode) {
				throw new Exception($reponse, $httpStatusCode);
			}
		}
		curl_close($ch);
		return $reponse;
	}

	protected function getMillisecond() {
		list($s1, $s2) = explode(' ', microtime());
		return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
	}

	protected function logCommunicationError($apiName, $requestUrl, $errorCode, $responseTxt) {
		$localIp = isset ($_SERVER["SERVER_ADDR"]) ? $_SERVER["SERVER_ADDR"] : "CLI";
		$logger = new LtLogger;
		$logger->conf["log_file"] = rtrim(AOP_SDK_WORK_DIR, '\\/') . '/' . "logs/aop_comm_err_" . $this->appId . "_" . date("Y-m-d") . ".log";
		$logger->conf["separator"] = "^_^";
		$logData = array(
			date("Y-m-d H:i:s"),
			$apiName,
			$this->appId,
			$localIp,
			PHP_OS,
			$this->alipaySdkVersion,
			$requestUrl,
			$errorCode,
			str_replace("\n", "", $responseTxt)
		);
		$logger->log($logData);
	}

	/**
	 * 生成用于调用收银台SDK的字符串
	 * @param $request SDK接口的请求参数对象
	 * @return string 
	 * @author guofa.tgf
	 */
	public function sdkExecute($request) {		
		$this->setupCharsets($request);
		$params['app_id'] = $this->appId;
		$params['method'] = $request->getApiMethodName();
		$params['format'] = $this->format; 
		$params['sign_type'] = $this->signType;
		$params['timestamp'] = date("Y-m-d H:i:s");
		$params['alipay_sdk'] = $this->alipaySdkVersion;
		$params['charset'] = $this->postCharset;

		$version = $request->getApiVersion();
		$params['version'] = $this->checkEmpty($version) ? $this->apiVersion : $version;
		if ($notify_url = $request->getNotifyUrl()) {
			$params['notify_url'] = $notify_url;
		}

		$dict = $request->getApiParas();
		$params['biz_content'] = $dict['biz_content'];
		ksort($params);
		$params['sign'] = $this->generateSign($params, $this->signType);
		foreach ($params as &$value) {
			$value = $this->characet($value, $params['charset']);
		}
		return http_build_query($params);
	}

	/*
		页面提交执行方法
		@param：跳转类接口的request; $httpmethod 提交方式。两个值可选：post、get
		@return：构建好的、签名后的最终跳转URL（GET）或String形式的form（POST）
		auther:笙默
	*/
	public function pageExecute($request,$httpmethod = "POST") {
		$this->setupCharsets($request);
		if (strcasecmp($this->fileCharset, $this->postCharset)) {
			// writeLog("本地文件字符集编码与表单提交编码不一致，请务必设置成一样，属性名分别为postCharset!");
			throw new Exception("文件编码：[" . $this->fileCharset . "] 与表单提交编码：[" . $this->postCharset . "]两者不一致!");
		}
		$iv=null;
		if(!$this->checkEmpty($request->getApiVersion())){
			$iv=$request->getApiVersion();
		}else{
			$iv=$this->apiVersion;
		}

		//组装系统参数
		$sysParams["app_id"] = $this->appId;
		$sysParams["version"] = $iv;
		$sysParams["format"] = $this->format;
		$sysParams["sign_type"] = $this->signType;
		$sysParams["method"] = $request->getApiMethodName();
		$sysParams["timestamp"] = @date("Y-m-d H:i:s");
		$sysParams["alipay_sdk"] = $this->alipaySdkVersion;
		$sysParams["terminal_type"] = $request->getTerminalType();
		$sysParams["terminal_info"] = $request->getTerminalInfo();
		$sysParams["prod_code"] = $request->getProdCode();
		$sysParams["notify_url"] = $request->getNotifyUrl();
		$sysParams["return_url"] = $request->getReturnUrl();
		$sysParams["charset"] = $this->postCharset;

		//获取业务参数
		$apiParams = $request->getApiParas();
		if (method_exists($request,"getNeedEncrypt") &&$request->getNeedEncrypt()){
			$sysParams["encrypt_type"] = $this->encryptType;
			if ($this->checkEmpty($apiParams['biz_content'])) {
				throw new Exception(" api request Fail! The reason : encrypt request is not supperted!");
			}
			if ($this->checkEmpty($this->encryptKey) || $this->checkEmpty($this->encryptType)) {
				throw new Exception(" encryptType and encryptKey must not null! ");
			}
			if ("AES" != $this->encryptType) {
				throw new Exception("加密类型只支持AES");
			}
			// 执行加密
			$enCryptContent = encrypt($apiParams['biz_content'], $this->encryptKey);
			$apiParams['biz_content'] = $enCryptContent;
		}

		//print_r($apiParams);
		$totalParams = array_merge($apiParams, $sysParams);		
		//待签名字符串
		$preSignStr = $this->getSignContent($totalParams);
		//签名
		$totalParams["sign"] = $this->generateSign($totalParams, $this->signType);
		if ("GET" == strtoupper($httpmethod)) {			
			//value做urlencode
			$preString=$this->getSignContentUrlencode($totalParams);
			//拼接GET请求串
			$requestUrl = $this->gatewayUrl."?".$preString;			
			return $requestUrl;
		} else {
			//拼接表单字符串
			return $this->buildRequestForm($totalParams);
		}
	}

	/**
	 * 建立请求，以表单HTML形式构造（默认）
	 * @param $para_temp 请求参数数组
	 * @return 提交表单HTML文本
	 */
	protected function buildRequestForm($para_temp) {
		
		$sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='".$this->gatewayUrl."?charset=".trim($this->postCharset)."' method='POST'>";
		while (list ($key, $val) = each ($para_temp)) {
			if (false === $this->checkEmpty($val)) {
				//$val = $this->characet($val, $this->postCharset);
				$val = str_replace("'","&apos;",$val);
				//$val = str_replace("\"","&quot;",$val);
				$sHtml.= "<input type='hidden' name='".$key."' value='".$val."'/>";
			}
		}
		//submit按钮控件请不要含有name属性
		$sHtml = $sHtml."<input type='submit' value='ok' style='display:none;''></form>";		
		$sHtml = $sHtml."<script>document.forms['alipaysubmit'].submit();</script>";		
		return $sHtml;
	}


	public function execute($request, $authToken = null, $appInfoAuthtoken = null) {
		$this->setupCharsets($request);
		//		//  如果两者编码不一致，会出现签名验签或者乱码
		if (strcasecmp($this->fileCharset, $this->postCharset)) {
			// writeLog("本地文件字符集编码与表单提交编码不一致，请务必设置成一样，属性名分别为postCharset!");
			throw new Exception("文件编码：[" . $this->fileCharset . "] 与表单提交编码：[" . $this->postCharset . "]两者不一致!");
		}
		$iv = null;
		if (!$this->checkEmpty($request->getApiVersion())) {
			$iv = $request->getApiVersion();
		} else {
			$iv = $this->apiVersion;
		}
		//组装系统参数
		$sysParams["app_id"] = $this->appId;
		$sysParams["version"] = $iv;
		$sysParams["format"] = $this->format;
		$sysParams["sign_type"] = $this->signType;
		$sysParams["method"] = $request->getApiMethodName();
		$sysParams["timestamp"] = date("Y-m-d H:i:s");
		$sysParams["auth_token"] = $authToken;
		$sysParams["alipay_sdk"] = $this->alipaySdkVersion;
		$sysParams["terminal_type"] = $request->getTerminalType();
		$sysParams["terminal_info"] = $request->getTerminalInfo();
		$sysParams["prod_code"] = $request->getProdCode();
		$sysParams["notify_url"] = $request->getNotifyUrl();
		$sysParams["charset"] = $this->postCharset;
		$sysParams["app_auth_token"] = $appInfoAuthtoken;


		//获取业务参数
		$apiParams = $request->getApiParas();
		if (method_exists($request,"getNeedEncrypt") &&$request->getNeedEncrypt()){
			$sysParams["encrypt_type"] = $this->encryptType;
			if ($this->checkEmpty($apiParams['biz_content'])) {
				throw new Exception(" api request Fail! The reason : encrypt request is not supperted!");
			}
			if ($this->checkEmpty($this->encryptKey) || $this->checkEmpty($this->encryptType)) {
				throw new Exception(" encryptType and encryptKey must not null! ");
			}
			if ("AES" != $this->encryptType) {
				throw new Exception("加密类型只支持AES");
			}
			// 执行加密
			$enCryptContent = encrypt($apiParams['biz_content'], $this->encryptKey);
			$apiParams['biz_content'] = $enCryptContent;

		}
		//签名
		$sysParams["sign"] = $this->generateSign(array_merge($apiParams, $sysParams), $this->signType);

		//系统参数放入GET请求串
		$requestUrl = $this->gatewayUrl . "?";
		foreach ($sysParams as $sysParamKey => $sysParamValue) {
			$requestUrl .= "$sysParamKey=" . urlencode($this->characet($sysParamValue, $this->postCharset)) . "&";
		}
		$requestUrl = substr($requestUrl, 0, -1);

		//发起HTTP请求
		try {
			$resp = $this->curl($requestUrl, $apiParams);
		} catch (Exception $e) {
			$this->logCommunicationError($sysParams["method"], $requestUrl, "HTTP_ERROR_" . $e->getCode(), $e->getMessage());
			return false;
		}
		//解析AOP返回结果
		$respWellFormed = false;

		// 将返回结果转换本地文件编码
		$r = iconv($this->postCharset, $this->fileCharset . "//IGNORE", $resp);
		$signData = null;
		if ("json" == $this->format) {
			$respObject = json_decode($r);
			if (null !== $respObject) {
				$respWellFormed = true;
				$signData = $this->parserJSONSignData($request, $resp, $respObject);
			}
		} else if ("xml" == $this->format) {
			$respObject = @ simplexml_load_string($resp);
			if (false !== $respObject) {
				$respWellFormed = true;
				$signData = $this->parserXMLSignData($request, $resp);
			}
		}
		//返回的HTTP文本不是标准JSON或者XML，记下错误日志
		if (false === $respWellFormed) {
			$this->logCommunicationError($sysParams["method"], $requestUrl, "HTTP_RESPONSE_NOT_WELL_FORMED", $resp);
			return false;
		}
		// 验签
		$this->checkResponseSign($request, $signData, $resp, $respObject);
		// 解密
		if (method_exists($request,"getNeedEncrypt") &&$request->getNeedEncrypt()){
			if ("json" == $this->format) {

				$resp = $this->encryptJSONSignSource($request, $resp);

				// 将返回结果转换本地文件编码
				$r = iconv($this->postCharset, $this->fileCharset . "//IGNORE", $resp);
				$respObject = json_decode($r);
			}else{

				$resp = $this->encryptXMLSignSource($request, $resp);

				$r = iconv($this->postCharset, $this->fileCharset . "//IGNORE", $resp);
				$respObject = @ simplexml_load_string($r);
			}
		}

		return $respObject;
	}

	/**
	 * 转换字符集编码
	 * @param $data
	 * @param $targetCharset
	 * @return string
	 */
	function characet($data, $targetCharset) {		
		if (!empty($data)) {
			$fileType = $this->fileCharset;
			if (strcasecmp($fileType, $targetCharset) != 0) {
				$data = mb_convert_encoding($data, $targetCharset, $fileType);
				//				$data = iconv($fileType, $targetCharset.'//IGNORE', $data);
			}
		}


		return $data;
	}

	public function exec($paramsArray) {
		if (!isset ($paramsArray["method"])) {
			trigger_error("No api name passed");
		}
		$inflector = new LtInflector;
		$inflector->conf["separator"] = ".";
		$requestClassName = ucfirst($inflector->camelize(substr($paramsArray["method"], 7))) . "Request";
		if (!class_exists($requestClassName)) {
			trigger_error("No such api: " . $paramsArray["method"]);
		}

		$session = isset ($paramsArray["session"]) ? $paramsArray["session"] : null;
		$req = new $requestClassName;
		foreach ($paramsArray as $paraKey => $paraValue) {
			$inflector->conf["separator"] = "_";
			$setterMethodName = $inflector->camelize($paraKey);
			$inflector->conf["separator"] = ".";
			$setterMethodName = "set" . $inflector->camelize($setterMethodName);
			if (method_exists($req, $setterMethodName)) {
				$req->$setterMethodName ($paraValue);
			}
		}
		return $this->execute($req, $session);
	}

	/**
	 * 校验$value是否非空
	 *  if not set ,return true;
	 *    if is null , return true;
	 **/
	protected function checkEmpty($value) {
		if (!isset($value))
			return true;
		if ($value === null)
			return true;
		if (trim($value) === "")
			return true;

		return false;
	}

	/** rsaCheckV1 & rsaCheckV2
	 *  验证签名
	 *  在使用本方法前，必须初始化AopClient且传入公钥参数。
	 *  公钥是否是读取字符串还是读取文件，是根据初始化传入的值判断的。
	 **/
	public function rsaCheckV1($params, $rsaPublicKeyFilePath,$signType='RSA') {
			$sign = $params['sign'];
			$params['sign_type'] = null;
			$params['sign'] = null;
			return $this->verify($this->getSignContent($params), $sign, $rsaPublicKeyFilePath,$signType);
	}
	public function rsaCheckV2($params, $rsaPublicKeyFilePath, $signType='RSA') {
		$sign = $params['sign'];
		$params['sign'] = null;
		return $this->verify($this->getSignContent($params), $sign, $rsaPublicKeyFilePath, $signType);
	}

	function verify($data, $sign, $rsaPublicKeyFilePath, $signType = 'RSA') {
		if(!is_file($this->alipayPublicKey)){
			$pubKey= $this->alipayrsaPublicKey;
			$res = "-----BEGIN PUBLIC KEY-----\n" .
				wordwrap($pubKey, 64, "\n", true) .
				"\n-----END PUBLIC KEY-----";
		}else {
			//读取公钥文件
			$pubKey = file_get_contents($rsaPublicKeyFilePath);
			//转换为openssl格式密钥
			$res = openssl_get_publickey($pubKey);
		}
		($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确');  

		//调用openssl内置方法验签，返回bool值

		if ("RSA2" == $signType) {
			$result = (bool)openssl_verify($data, base64_decode($sign), $res, OPENSSL_ALGO_SHA256);
		} else {
			$result = (bool)openssl_verify($data, base64_decode($sign), $res);		}

		if(!$this->checkEmpty($this->alipayPublicKey)) {
			//释放资源
			openssl_free_key($res);
		}
		return $result;
	}

/** 
	 *  在使用本方法前，必须初始化AopClient且传入公私钥参数。
	 *  公钥是否是读取字符串还是读取文件，是根据初始化传入的值判断的。
	 **/
	public function checkSignAndDecrypt($params, $rsaPublicKeyPem, $rsaPrivateKeyPem, $isCheckSign, $isDecrypt, $signType='RSA') {
		$charset = $params['charset'];
		$bizContent = $params['biz_content'];
		if ($isCheckSign) {
			if (!$this->rsaCheckV2($params, $rsaPublicKeyPem, $signType)) {
				echo "<br/>checkSign failure<br/>";
				exit;
			}
		}
		if ($isDecrypt) {
			return $this->rsaDecrypt($bizContent, $rsaPrivateKeyPem, $charset);
		}

		return $bizContent;
	}

	/** 
	 *  在使用本方法前，必须初始化AopClient且传入公私钥参数。
	 *  公钥是否是读取字符串还是读取文件，是根据初始化传入的值判断的。
	 **/
	public function encryptAndSign($bizContent, $rsaPublicKeyPem, $rsaPrivateKeyPem, $charset, $isEncrypt, $isSign, $signType='RSA') {
		// 加密，并签名
		if ($isEncrypt && $isSign) {
			$encrypted = $this->rsaEncrypt($bizContent, $rsaPublicKeyPem, $charset);
			$sign = $this->sign($encrypted, $signType);
			$response = "<?xml version=\"1.0\" encoding=\"$charset\"?><alipay><response>$encrypted</response><encryption_type>RSA</encryption_type><sign>$sign</sign><sign_type>$signType</sign_type></alipay>";
			return $response;
		}
		// 加密，不签名
		if ($isEncrypt && (!$isSign)) {
			$encrypted = $this->rsaEncrypt($bizContent, $rsaPublicKeyPem, $charset);
			$response = "<?xml version=\"1.0\" encoding=\"$charset\"?><alipay><response>$encrypted</response><encryption_type>$signType</encryption_type></alipay>";
			return $response;
		}
		// 不加密，但签名
		if ((!$isEncrypt) && $isSign) {
			$sign = $this->sign($bizContent, $signType);
			$response = "<?xml version=\"1.0\" encoding=\"$charset\"?><alipay><response>$bizContent</response><sign>$sign</sign><sign_type>$signType</sign_type></alipay>";
			return $response;
		}
		// 不加密，不签名
		$response = "<?xml version=\"1.0\" encoding=\"$charset\"?>$bizContent";
		return $response;
	}

	/** 
	 *  在使用本方法前，必须初始化AopClient且传入公私钥参数。
	 *  公钥是否是读取字符串还是读取文件，是根据初始化传入的值判断的。
	 **/
	public function rsaEncrypt($data, $rsaPublicKeyPem, $charset) {
		if($this->checkEmpty($this->alipayPublicKey)){
			//读取字符串
			$pubKey= $this->alipayrsaPublicKey;
			$res = "-----BEGIN PUBLIC KEY-----\n" .
				wordwrap($pubKey, 64, "\n", true) .
				"\n-----END PUBLIC KEY-----";
		}else {
			//读取公钥文件
			$pubKey = file_get_contents($rsaPublicKeyFilePath);
			//转换为openssl格式密钥
			$res = openssl_get_publickey($pubKey);
		}

		($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确'); 
		$blocks = $this->splitCN($data, 0, 30, $charset);
		$chrtext  = null;
		$encodes  = array();
		foreach ($blocks as $n => $block) {
			if (!openssl_public_encrypt($block, $chrtext , $res)) {
				echo "<br/>" . openssl_error_string() . "<br/>";
			}
			$encodes[] = $chrtext ;
		}
		$chrtext = implode(",", $encodes);

		return base64_encode($chrtext);
	}

	/** 
	 *  在使用本方法前，必须初始化AopClient且传入公私钥参数。
	 *  公钥是否是读取字符串还是读取文件，是根据初始化传入的值判断的。
	 **/
	public function rsaDecrypt($data, $rsaPrivateKeyPem, $charset) {		
		if($this->checkEmpty($this->rsaPrivateKeyFilePath)){
			//读字符串
			$priKey=$this->rsaPrivateKey;
			$res = "-----BEGIN RSA PRIVATE KEY-----\n" .
				wordwrap($priKey, 64, "\n", true) .
				"\n-----END RSA PRIVATE KEY-----";
		}else {
			$priKey = file_get_contents($this->rsaPrivateKeyFilePath);
			$res = openssl_get_privatekey($priKey);
		}
		($res) or die('您使用的私钥格式错误，请检查RSA私钥配置'); 
		//转换为openssl格式密钥
		$decodes = explode(',', $data);
		$strnull = "";
		$dcyCont = "";
		foreach ($decodes as $n => $decode) {
			if (!openssl_private_decrypt($decode, $dcyCont, $res)) {
				echo "<br/>" . openssl_error_string() . "<br/>";
			}
			$strnull .= $dcyCont;
		}
		return $strnull;
	}

	function splitCN($cont, $n = 0, $subnum, $charset) {
		//$len = strlen($cont) / 3;
		$arrr = array();
		for ($i = $n; $i < strlen($cont); $i += $subnum) {
			$res = $this->subCNchar($cont, $i, $subnum, $charset);
			if (!empty ($res)) {
				$arrr[] = $res;
			}
		}
		return $arrr;
	}

	function subCNchar($str, $start = 0, $length, $charset = "gbk") {
		if (strlen($str) <= $length) {
			return $str;
		}
		$re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("", array_slice($match[0], $start, $length));
		return $slice;
	}

	function parserResponseSubCode($request, $responseContent, $respObject, $format) {
		if ("json" == $format) {
			$apiName = $request->getApiMethodName();
			$rootNodeName = str_replace(".", "_", $apiName) . $this->RESPONSE_SUFFIX;
			$errorNodeName = $this->ERROR_RESPONSE;
			$rootIndex = strpos($responseContent, $rootNodeName);
			$errorIndex = strpos($responseContent, $errorNodeName);
			if ($rootIndex > 0) {
				// 内部节点对象
				$rInnerObject = $respObject->$rootNodeName;
			} elseif ($errorIndex > 0) {

				$rInnerObject = $respObject->$errorNodeName;
			} else {
				return null;
			}
			// 存在属性则返回对应值
			if (isset($rInnerObject->sub_code)) {
				return $rInnerObject->sub_code;
			} else {
				return null;
			}
		} elseif ("xml" == $format) {
			// xml格式sub_code在同一层级
			return $respObject->sub_code;

		}


	}

	function parserJSONSignData($request, $responseContent, $responseJSON) {
		$signData = new SignData();
		$signData->sign = $this->parserJSONSign($responseJSON);
		$signData->signSourceData = $this->parserJSONSignSource($request, $responseContent);
		return $signData;
	}

	function parserJSONSignSource($request, $responseContent) {
		$apiName = $request->getApiMethodName();
		$rootNodeName = str_replace(".", "_", $apiName) . $this->RESPONSE_SUFFIX;
		$rootIndex = strpos($responseContent, $rootNodeName);
		$errorIndex = strpos($responseContent, $this->ERROR_RESPONSE);
		if ($rootIndex > 0) {
			return $this->parserJSONSource($responseContent, $rootNodeName, $rootIndex);
		} else if ($errorIndex > 0) {
			return $this->parserJSONSource($responseContent, $this->ERROR_RESPONSE, $errorIndex);
		} else {

			return null;
		}


	}

	function parserJSONSource($responseContent, $nodeName, $nodeIndex) {
		$signDataStartIndex = $nodeIndex + strlen($nodeName) + 2;
		$signIndex = strpos($responseContent, "\"" . $this->SIGN_NODE_NAME . "\"");
		// 签名前-逗号
		$signDataEndIndex = $signIndex - 1;
		$indexLen = $signDataEndIndex - $signDataStartIndex;
		if ($indexLen < 0) {
			return null;
		}
		return substr($responseContent, $signDataStartIndex, $indexLen);
	}

	function parserJSONSign($responseJSon) {
		return $responseJSon->sign;
	}
	function parserXMLSignData($request, $responseContent) {
		$signData = new SignData();
		$signData->sign = $this->parserXMLSign($responseContent);
		$signData->signSourceData = $this->parserXMLSignSource($request, $responseContent);
		return $signData;
	}

	function parserXMLSignSource($request, $responseContent) {
		$apiName = $request->getApiMethodName();
		$rootNodeName = str_replace(".", "_", $apiName) . $this->RESPONSE_SUFFIX;
		$rootIndex = strpos($responseContent, $rootNodeName);
		$errorIndex = strpos($responseContent, $this->ERROR_RESPONSE);
		//		$this->echoDebug("<br/>rootNodeName:" . $rootNodeName);
		//		$this->echoDebug("<br/> responseContent:<xmp>" . $responseContent . "</xmp>");
		if ($rootIndex > 0) {
			return $this->parserXMLSource($responseContent, $rootNodeName, $rootIndex);
		} else if ($errorIndex > 0) {
			return $this->parserXMLSource($responseContent, $this->ERROR_RESPONSE, $errorIndex);
		} else {
			return null;
		}


	}

	function parserXMLSource($responseContent, $nodeName, $nodeIndex) {
		$signDataStartIndex = $nodeIndex + strlen($nodeName) + 1;
		$signIndex = strpos($responseContent, "<" . $this->SIGN_NODE_NAME . ">");
		// 签名前-逗号
		$signDataEndIndex = $signIndex - 1;
		$indexLen = $signDataEndIndex - $signDataStartIndex + 1;
		if ($indexLen < 0) {
			return null;
		}
		return substr($responseContent, $signDataStartIndex, $indexLen);
	}

	function parserXMLSign($responseContent) {
		$signNodeName = "<" . $this->SIGN_NODE_NAME . ">";
		$signEndNodeName = "</" . $this->SIGN_NODE_NAME . ">";

		$indexOfSignNode = strpos($responseContent, $signNodeName);
		$indexOfSignEndNode = strpos($responseContent, $signEndNodeName);
		if ($indexOfSignNode < 0 || $indexOfSignEndNode < 0) {
			return null;
		}
		$nodeIndex = ($indexOfSignNode + strlen($signNodeName));
		$indexLen = $indexOfSignEndNode - $nodeIndex;
		if ($indexLen < 0) {
			return null;
		}
		// 签名
		return substr($responseContent, $nodeIndex, $indexLen);
	}

	/**
	 * 验签
	 * @param $request
	 * @param $signData
	 * @param $resp
	 * @param $respObject
	 * @throws Exception
	 */
	public function checkResponseSign($request, $signData, $resp, $respObject) {
		if (!$this->checkEmpty($this->alipayPublicKey) || !$this->checkEmpty($this->alipayrsaPublicKey)) {
			if ($signData == null || $this->checkEmpty($signData->sign) || $this->checkEmpty($signData->signSourceData)) {
				throw new Exception(" check sign Fail! The reason : signData is Empty");
			}
			// 获取结果sub_code
			$responseSubCode = $this->parserResponseSubCode($request, $resp, $respObject, $this->format);
			if (!$this->checkEmpty($responseSubCode) || ($this->checkEmpty($responseSubCode) && !$this->checkEmpty($signData->sign))) {
				$checkResult = $this->verify($signData->signSourceData, $signData->sign, $this->alipayPublicKey, $this->signType);
				if (!$checkResult) {
					if (strpos($signData->signSourceData, "\\/") > 0) {
						$signData->signSourceData = str_replace("\\/", "/", $signData->signSourceData);
						$checkResult = $this->verify($signData->signSourceData, $signData->sign, $this->alipayPublicKey, $this->signType);
						if (!$checkResult) {
							throw new Exception("check sign Fail! [sign=" . $signData->sign . ", signSourceData=" . $signData->signSourceData . "]");
						}
					} else {
						throw new Exception("check sign Fail! [sign=" . $signData->sign . ", signSourceData=" . $signData->signSourceData . "]");
					}
				}
			}
		}
	}

	private function setupCharsets($request) {
		if ($this->checkEmpty($this->postCharset)) {
			$this->postCharset = 'UTF-8';
		}
		$str = preg_match('/[\x80-\xff]/', $this->appId) ? $this->appId : print_r($request, true);
		$this->fileCharset = mb_detect_encoding($str, "UTF-8, GBK") == 'UTF-8' ? 'UTF-8' : 'GBK';
	}

	// 获取加密内容

	private function encryptJSONSignSource($request, $responseContent) {
		$parsetItem = $this->parserEncryptJSONSignSource($request, $responseContent);
		$bodyIndexContent = substr($responseContent, 0, $parsetItem->startIndex);
		$bodyEndContent = substr($responseContent, $parsetItem->endIndex, strlen($responseContent) + 1 - $parsetItem->endIndex);
		$bizContent = decrypt($parsetItem->encryptContent, $this->encryptKey);
		return $bodyIndexContent . $bizContent . $bodyEndContent;
	}
	private function parserEncryptJSONSignSource($request, $responseContent) {
		$apiName = $request->getApiMethodName();
		$rootNodeName = str_replace(".", "_", $apiName) . $this->RESPONSE_SUFFIX;
		$rootIndex = strpos($responseContent, $rootNodeName);
		$errorIndex = strpos($responseContent, $this->ERROR_RESPONSE);
		if ($rootIndex > 0) {
			return $this->parserEncryptJSONItem($responseContent, $rootNodeName, $rootIndex);
		} else if ($errorIndex > 0) {
			return $this->parserEncryptJSONItem($responseContent, $this->ERROR_RESPONSE, $errorIndex);
		} else {
			return null;
		}


	}


	private function parserEncryptJSONItem($responseContent, $nodeName, $nodeIndex) {
		$signDataStartIndex = $nodeIndex + strlen($nodeName) + 2;
		$signIndex = strpos($responseContent, "\"" . $this->SIGN_NODE_NAME . "\"");
		// 签名前-逗号
		$signDataEndIndex = $signIndex - 1;
		if ($signDataEndIndex < 0) {
			$signDataEndIndex = strlen($responseContent)-1 ;
		}
		$indexLen = $signDataEndIndex - $signDataStartIndex;
		$encContent = substr($responseContent, $signDataStartIndex+1, $indexLen-2);
		$encryptParseItem = new EncryptParseItem();
		$encryptParseItem->encryptContent = $encContent;
		$encryptParseItem->startIndex = $signDataStartIndex;
		$encryptParseItem->endIndex = $signDataEndIndex;
		return $encryptParseItem;
	}

	// 获取加密内容

	private function encryptXMLSignSource($request, $responseContent) {
		$parsetItem = $this->parserEncryptXMLSignSource($request, $responseContent);
		$bodyIndexContent = substr($responseContent, 0, $parsetItem->startIndex);
		$bodyEndContent = substr($responseContent, $parsetItem->endIndex, strlen($responseContent) + 1 - $parsetItem->endIndex);
		$bizContent = decrypt($parsetItem->encryptContent, $this->encryptKey);
		return $bodyIndexContent . $bizContent . $bodyEndContent;
	}

	private function parserEncryptXMLSignSource($request, $responseContent) {
		$apiName = $request->getApiMethodName();
		$rootNodeName = str_replace(".", "_", $apiName) . $this->RESPONSE_SUFFIX;
		$rootIndex = strpos($responseContent, $rootNodeName);
		$errorIndex = strpos($responseContent, $this->ERROR_RESPONSE);
		//		$this->echoDebug("<br/>rootNodeName:" . $rootNodeName);
		//		$this->echoDebug("<br/> responseContent:<xmp>" . $responseContent . "</xmp>");

		if ($rootIndex > 0) {
			return $this->parserEncryptXMLItem($responseContent, $rootNodeName, $rootIndex);
		} else if ($errorIndex > 0) {

			return $this->parserEncryptXMLItem($responseContent, $this->ERROR_RESPONSE, $errorIndex);
		} else {
			return null;
		}
	}

	private function parserEncryptXMLItem($responseContent, $nodeName, $nodeIndex) {
		$signDataStartIndex = $nodeIndex + strlen($nodeName) + 1;
		$xmlStartNode="<".$this->ENCRYPT_XML_NODE_NAME.">";
		$xmlEndNode="</".$this->ENCRYPT_XML_NODE_NAME.">";
		$indexOfXmlNode=strpos($responseContent,$xmlEndNode);
		if($indexOfXmlNode<0){
			$item = new EncryptParseItem();
			$item->encryptContent = null;
			$item->startIndex = 0;
			$item->endIndex = 0;
			return $item;
		}

		$startIndex=$signDataStartIndex+strlen($xmlStartNode);
		$bizContentLen=$indexOfXmlNode-$startIndex;
		$bizContent=substr($responseContent,$startIndex,$bizContentLen);

		$encryptParseItem = new EncryptParseItem();
		$encryptParseItem->encryptContent = $bizContent;
		$encryptParseItem->startIndex = $signDataStartIndex;
		$encryptParseItem->endIndex = $indexOfXmlNode+strlen($xmlEndNode);

		return $encryptParseItem;

	}


	function echoDebug($content) {

		if ($this->debugInfo) {
			echo "<br/>" . $content;
		}

	}


}


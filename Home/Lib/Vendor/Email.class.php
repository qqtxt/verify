<?php
defined('JETEE_PATH') or exit();

class Email{
/** var_dump(Email::send_mail( '112738102@qq.com', '张洪华','主题', '内容', 1,0,$f="30885427@qq.com")); qq sina sohu 163 gmail只有qq入垃圾箱
 * 邮件发送
 *
 * @param: $email[string]       接收人邮件地址
 * @param: $name[string]        接收人姓名
 * @param: $subject[string]     邮件标题
 * @param: $content[string]     邮件内容
 * @param: $type[int]           0 普通邮件， 1 HTML邮件
 * @param: $notification[bool]  true 要求回执， false 不用回执
 * @param: $f[string]  最后一参数  使用mail函数发送邮件 模拟发送邮箱增加发送成功率
如果用smtp include_once( 'class/smtp' );
 $cfg['mail_charset'] 发送邮件的字符编码
 $cfg['site_name']	
 $cfg['mail_service'] 0为使用mail函数发 1smtp发
 $cfg['smtp_mail'] 邮件回复地址 
 $cfg['smtp_host']发送邮件服务器地址(SMTP)
 $cfg['smtp_port']服务器端口
 $cfg['smtp_user']发件邮箱帐号
 $cfg['smtp_pass']帐号密码
 $cfg['smtp_ssl']邮件服务器是否要求加密连接(SSL) 0否 1
 * @return boolean
 */
static function send($email,$name,$subject='', $content='', $type = 0, $notification=false,$f=""){
	if(is_array($name)){
		$subject=$name['subject'];
		$content=$name['content'];
		$type=$name['type'];
		$notification=$name['notification'];
		$f=$name['f'];
		$name=$name['name'];
	}
	$site_charset='UTF-8'; 
	$cfg['smtp_ssl']=c('smtp_ssl'); $cfg['mail_charset']=c('mail_charset'); $cfg['site_name']=c('site_name'); 	$cfg['mail_service']=c('mail_service');//0使用mail函数发 1smtp发
	//$cfg['smtp_ssl']=0;$cfg['mail_charset']='UTF-8'; $cfg['site_name']='码863众创网'; 	$cfg['mail_service']='1';//0使用mail函数发 1smtp发
	//回复地址
	$cfg['smtp_mail']=c('smtp_mail'); $cfg['smtp_host']=c('smtp_host');	$cfg['smtp_port']=c('smtp_port');	$cfg['smtp_user']=c('smtp_user');	$cfg['smtp_pass']=c('smtp_pass');
	//$cfg['smtp_mail']='admin@16888ad.com'; $cfg['smtp_host']='smtp.ym.163.com';	$cfg['smtp_port']='25';	$cfg['smtp_user']='admin@16888ad.com';	$cfg['smtp_pass']='6891552';
	
	/* 如果邮件编码不是$site_charset，创建字符集转换对象，转换编码 */
	if (strtolower($cfg['mail_charset']) != strtolower($site_charset)){
		$name      = iconv($site_charset, $cfg['mail_charset'], $name);
		$subject   = iconv($site_charset, $cfg['mail_charset'], $subject);
		$content   = iconv($site_charset, $cfg['mail_charset'], $content);
		$cfg['site_name'] = iconv($site_charset, $cfg['mail_charset'], $cfg['site_name']);
	}
	$charset   = $cfg['mail_charset'];				

	/**
	 * 使用mail函数发送邮件
	 */
	if ($cfg['mail_service'] == 0 && function_exists('mail')){
		//使用mail函数发送邮件 模拟发送邮箱增加发送成功率
		$f?'':$f=$cfg['smtp_user'];
		/* 邮件的头部信息 */
		$content_type = ($type == 0) ? 'Content-Type: text/plain; charset=' . $charset : 'Content-Type: text/html; charset=' . $charset;
		$headers = array();
		$headers[] = 'From: "' . '=?' . $charset . '?B?' . base64_encode($cfg['site_name']) . '?='.'" <' . $cfg['smtp_mail'] . '>';
		$headers[] = $content_type . '; format=flowed';
		if ($notification){
			$headers[] = 'Disposition-Notification-To: ' . '=?' . $charset . '?B?' . base64_encode($cfg['site_name']) . '?='.'" <' . $cfg['smtp_mail'] . '>';
		}
		$res = @mail($email, '=?' . $charset . '?B?' . base64_encode($subject) . '?=', $content, implode("\r\n", $headers),'-f '.$f);//最后一参数  模拟发送邮箱增加发送成功率
		if (!$res){
			//var_dump($email. '=?' . $charset . '?B?' . base64_encode($subject) . '?='. $content. implode("\r\n", $headers).'-f '.$f);exit('ssssssaAaaaa11111aass');
			//$err_msg send_mail_error
			return false;
		}else{
			return true;
		}
	}
	/**
	 * 使用smtp服务发送邮件
	 */
	else{
		/* 邮件的头部信息 */
		$content_type = ($type == 0) ?
			'Content-Type: text/plain; charset=' . $charset : 'Content-Type: text/html; charset=' . $charset;
		$content   =  base64_encode($content);
		$headers = array();
		$headers[] = 'Date: ' . date('D, j M Y H:i:s',time()) . ' +0800';
		$headers[] = 'To: "' . '=?' . $charset . '?B?' . base64_encode($name) . '?=' . '" <' . $email. '>';
		$headers[] = 'From: "' . '=?' . $charset . '?B?' . base64_encode($cfg['site_name']) . '?='.'" <' . $cfg['smtp_mail'] . '>';
		$headers[] = 'Subject: ' . '=?' . $charset . '?B?' . base64_encode($subject) . '?=';
		$headers[] = $content_type . '; format=flowed';
		$headers[] = 'Content-Transfer-Encoding: base64';
		$headers[] = 'Content-Disposition: inline';
		if ($notification){
			$headers[] = 'Disposition-Notification-To: ' . '=?' . $charset . '?B?' . base64_encode($cfg['site_name']) . '?='.'" <' . $cfg['smtp_mail'] . '>';
		}
		/* 获得邮件服务器的参数设置 */
		$params['host'] = $cfg['smtp_host'];
		$params['port'] = $cfg['smtp_port'];
		$params['user'] = $cfg['smtp_user'];
		$params['pass'] = $cfg['smtp_pass'];
		$params['smtp_ssl'] = $cfg['smtp_ssl'];
		if (empty($params['host']) || empty($params['port']))
		{
			//$err_msg 如果没有设置主机和端口直接返回 false
			return false;
		}else{
			// 发送邮件
			if (!function_exists('fsockopen')){
				//$err_msg 如果fsockopen被禁用，直接返回
				return false;
			}
			static $smtp;
			$send_params['recipients'] = $email;
			$send_params['headers']    = $headers;
			$send_params['from']       = $cfg['smtp_mail'];
			$send_params['body']       = $content;
			if (!isset($smtp)){
				$smtp = new smtp($params);
			}
			if ($smtp->connect() && $smtp->send($send_params)){
				return true;
			}else{
				$err_msg = $smtp->error_msg();
				if (empty($err_msg)){
					//$err_msg Unknown
				}else{
					if (strpos($err_msg, 'Failed to connect to server') !== false){
						//$err_msg smtp_connect_failure
					}else if (strpos($err_msg, 'AUTH command failed') !== false){
						//$err_msg smtp_login_failure
					}elseif (strpos($err_msg, 'bad sequence of commands') !== false){
						//$err_msg smtp_refuse
					}else{
						//$err_msg
					}
				}
				return false;
			}
		}
	}
}
	
	
}



//一同加载
class smtp
{
	var $connection;
	var $recipients;
	var $headers;
	var $timeout;
	var $errors;
	var $status;
	var $body;
	var $from;
	var $host;
	var $port;
	var $helo;
	var $auth;
	var $user;
	var $pass;
	var $smtp_ssl;
	var $smtp_status_connected;//SMTP_STATUS_CONNECTED
	/**
	 *  参数为一个数组
	 *  host        SMTP 服务器的主机       默认：localhost
	 *  port        SMTP 服务器的端口       默认：25
	 *  helo        发送HELO命令的名称      默认：localhost
	 *  user        SMTP 服务器的用户名     默认：空值
	 *  pass        SMTP 服务器的登陆密码   默认：空值
	 *  timeout     连接超时的时间          默认：5
	 *  @return  bool
	 */
	function __construct($params = array())
	{
		if (!defined('CRLF'))
		{
			define('CRLF', "\r\n", true);
		}
		$this->timeout  = 10;
		$this->status   = 1;//SMTP_STATUS_NOT_CONNECTED
		$this->smtp_status_connected   = 2;//SMTP_STATUS_CONNECTED
		$this->host     = 'localhost';
		$this->port     = 25;
		$this->auth     = false;
		$this->user     = '';
		$this->pass     = '';
		$this->smtp_ssl = false;
		$this->errors   = array();
		foreach ($params AS $key => $value)
		{
			$this->$key = $value;
		}
		$this->helo     = $this->host;
		//  如果没有设置用户名则不验证
		$this->auth = ('' == $this->user) ? false : true;
	}
	function connect($params = array())
	{
		if (!isset($this->status))
		{
			$obj = new smtp($params);
			if ($obj->connect())
			{
				$obj->status = $this->smtp_status_connected;
			}
			return $obj;
		}
		else
		{
			if (!empty($this->smtp_ssl))
			{
				$this->host = "ssl://" . $this->host;
			}
			$this->connection = @fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);
			if ($this->connection === false)
			{
				$this->errors[] = 'Access is denied.';
				return false;
			}
			@socket_set_timeout($this->connection, 0, 250000);
			$greeting = $this->get_data();
			if (is_resource($this->connection))
			{
				$this->status = 2;
				return $this->auth ? $this->ehlo() : $this->helo();
			}
			else
			{
				//writelog('smtp', $errstr,__FILE__, __LINE__);
				$this->errors[] = 'Failed to connect to server: ' . $errstr;
				return false;
			}
		}
	}
	/**
	 * 参数为数组
	 * recipients      接收人的数组
	 * from            发件人的地址，也将作为回复地址
	 * headers         头部信息的数组
	 * body            邮件的主体
	 */
	function send($params = array())
	{
		foreach ($params AS $key => $value)
		{
			$this->$key = $value;
		}
		if ($this->is_connected())
		{
			//  服务器是否需要验证
			if ($this->auth)
			{
				if (!$this->auth())
				{
					return false;
				}
			}
			$this->mail($this->from);
			if (is_array($this->recipients))
			{
				foreach ($this->recipients AS $value)
				{
					$this->rcpt($value);
				}
			}
			else
			{
				$this->rcpt($this->recipients);
			}
			if (!$this->data())
			{
				return false;
			}
			$headers = str_replace(CRLF . '.', CRLF . '..', trim(implode(CRLF, $this->headers)));
			$body    = str_replace(CRLF . '.', CRLF . '..', $this->body);
			$body    = substr($body, 0, 1) == '.' ? '.' . $body : $body;
			$this->send_data($headers);
			$this->send_data('');
			$this->send_data($body);
			$this->send_data('.');
			return (substr($this->get_data(), 0, 3) === '250');
		}
		else
		{
			$this->errors[] = 'Not connected!';
			return false;
		}
	}
	function helo()
	{
		if (is_resource($this->connection)
				AND $this->send_data('HELO ' . $this->helo)
				AND substr($error = $this->get_data(), 0, 3) === '250' )
		{
			return true;
		}
		else
		{
			$this->errors[] = 'HELO command failed, output: ' . trim(substr($error, 3));
			return false;
		}
	}
	function ehlo()
	{
		if (is_resource($this->connection)
				AND $this->send_data('EHLO ' . $this->helo)
				AND substr($error = $this->get_data(), 0, 3) === '250' )
		{
			return true;
		}
		else
		{
			$this->errors[] = 'EHLO command failed, output: ' . trim(substr($error, 3));
			return false;
		}
	}
	function auth()
	{
		if (is_resource($this->connection)
				AND $this->send_data('AUTH LOGIN')
				AND substr($error = $this->get_data(), 0, 3) === '334'
				AND $this->send_data(base64_encode($this->user))            // Send username
				AND substr($error = $this->get_data(),0,3) === '334'
				AND $this->send_data(base64_encode($this->pass))            // Send password
				AND substr($error = $this->get_data(),0,3) === '235' )
		{
			return true;
		}
		else
		{
			$this->errors[] = 'AUTH command failed: ' . trim(substr($error, 3));
			return false;
		}
	}
	function mail($from)
	{
		if ($this->is_connected()
			AND $this->send_data('MAIL FROM:<' . $from . '>')
			AND substr($this->get_data(), 0, 2) === '250' )
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	function rcpt($to)
	{
		if ($this->is_connected()
			AND $this->send_data('RCPT TO:<' . $to . '>')
			AND substr($error = $this->get_data(), 0, 2) === '25')
		{
			return true;
		}
		else
		{
			$this->errors[] = trim(substr($error, 3));
			return false;
		}
	}
	function data()
	{
		if ($this->is_connected()
			AND $this->send_data('DATA')
			AND substr($error = $this->get_data(), 0, 3) === '354' )
		{
			return true;
		}
		else
		{
			$this->errors[] = trim(substr($error, 3));
			return false;
		}
	}
	function is_connected()
	{
		return (is_resource($this->connection) AND ($this->status === $this->smtp_status_connected));
	}
	function send_data($data)
	{
		if (is_resource($this->connection))
		{
			return fwrite($this->connection, $data . CRLF, strlen($data) + 2);
		}
		else
		{
			return false;
		}
	}
	function get_data()
	{
		$return = '';
		$line   = '';
		if (is_resource($this->connection))
		{
			while (strpos($return, CRLF) === false OR $line{3} !== ' ')
			{
				$line    = fgets($this->connection, 512);
				$return .= $line;
			}
			return trim($return);
		}
		else
		{
			return '';
		}
	}
	/**
	 * 获得最后一个错误信息
	 *
	 * @access  public
	 * @return  string
	 */
	function error_msg()
	{
		if (!empty($this->errors))
		{
			$len = count($this->errors) - 1;
			return $this->errors[$len];
		}
		else
		{
			return '';
		}
	}
}
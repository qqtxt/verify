<?php
defined('JETEE_PATH') or exit();

/**
*公用
* @version 0.0.1 3:02 2018/6/17
*/

/**
* 格式化时间
* @access  public
* @param   int   $time  时间戳
* @return  string
*/
function format_time($time){
	return empty($time) ? '' :date(C('TIME_FORMAT'), $time);
}
function format_date($time){
	return empty($time) ? '' :date(C('DATE_FORMAT'), $time);
}
/**
 *系统需要
 */
function jeHtmlspecialchars($str){
	return htmlspecialchars($str, ENT_QUOTES);
}


//设置配置
function common_config(){
	//没有设置才配置
	if(!C('site_name')){
		$cfg=json_decode(s('common_config'),true);
		if (!$cfg){//|| count($cfg)!=20){
			$cfg=db('config')->getField('name,value');
			s('common_config',json_encode($cfg));
		}
		C($cfg);
	}
}
function curl_post($url, $data){
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
	curl_setopt($ch, CURLOPT_POST, TRUE); 
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$ret = curl_exec($ch);
	curl_close($ch);
	return $ret;
}
/*
header("Content-type: text/html; charset=utf-8");	
require_once(APP_PATH.'Lib/Vendor/ipip.class.php');
$db = new Reader(APP_PATH.'Lib/Vendor/ip.ipdb');
*/
function getIpAddr($ip,$db){
	try{
		return $db->find($ip);
	}
	catch (Exception $e){
		return array('失败','失败','失败');
	}
}

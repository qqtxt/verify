<?php
defined('JETEE_PATH') or exit();
/**
*项目公用函数库
* @version 0.0.1 15:22 2018/9/15
*/
/**
 * json_encode   汉字不转 JSON_UNESCAPED_UNICODE
 * @return string
 */
function ejson($var){
	return json_encode($var,JSON_UNESCAPED_UNICODE);
}
/**
 * 字节格式化 把字节数格式为 B K M G T 描述的大小
 * @return string
 */
function byte_format($size, $dec=2) {
	$a = array("B", "KB", "MB", "GB", "TB", "PB");
	$pos = 0;
	while ($size >= 1024) {
		 $size /= 1024;
		   $pos++;
	}
	return round($size,$dec)." ".$a[$pos];
}
/**
 * 加反斜线 
 * @access public
 * @param mix	$string
 * @return  mix
 */
function daddslashes($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			unset($string[$key]);
			$string[addslashes($key)] = daddslashes($val);
		}
	} else {
		$string = addslashes($string);
	}
	return $string;
}
/**
 * 去反斜线 
 * @access public
 * @param mix	$string
 * @return  mix
 */
function dstripslashes($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			unset($string[$key]);
			$string[stripslashes($key)] = dstripslashes($val);
		}
	} else {
		$string = stripslashes($string);
	}
	return $string;
}

/**
 * 使用正则验证数据
 * @access public
 * @param string $value  要验证的数据
 * @param string $rule 验证规则
 * @return boolean
 */
function regex_check($value,$rule) {
	$validate = array(
		'require'   =>  '/.+/',
		'email'     =>  '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
		'url'       =>  '/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/',
		'currency'  =>  '/^\d+(\.\d+)?$/',
		'number'    =>  '/^\d+$/',
		'zip'       =>  '/^\d{6}$/',
		'integer'   =>  '/^[-\+]?\d+$/',
		'double'    =>  '/^[-\+]?\d+(\.\d+)?$/',
		'english'   =>  '/^[A-Za-z]+$/',
		'tel'   =>  '/^[\d\-]{7,11}$/',
		'phone'   =>  '/^1[34578]{1}\d{9}$/',
		'qq'   =>  '/^[1-9][0-9]{4,12}$/',
	);
	// 检查是否有内置的正则表达式
	if(isset($validate[strtolower($rule)]))
		$rule       =   $validate[strtolower($rule)];
	return preg_match($rule,$value)===1;
}
function is_qq($value) {
	return regex_check($value,'qq');
}
function is_email($value) {
	return regex_check($value,'email');
}
function is_phone($value) {
	return regex_check($value,'phone');
}
	
/**
 * 取指定目录下当月目录如201412下一唯一文件名，用于新建文件 目录不存在新建
 * @param       string      folder     目录路径。不能使用相对于网站根目录的URL
 * @return      str		不带目录路径的日期目录及一个唯一文件名  不带扩展名  如：201412/141517876792
 */
function random_name($folder){
	$time=microtime(true);
	$date_dir = date('Ym',$time);
	make_dir($folder.'/'.$date_dir);
	//当月一号时间戳
	$first_timestamp=strtotime(date('Y-m',$time).'-01');
	$file_name=sprintf("%.0f", ($time-$first_timestamp)*1000000);
	return $date_dir.'/'.$file_name;
}
/**
 * 检查目标文件夹是否存在，如果不存在则自动创建该目录
 *
 * @access      public
 * @param       string      folder     目录路径。
 *
 * @return      bool
 */
function make_dir($folder,$mode=0755)
{
	$reval = true;
	if (!is_dir($folder)){
		$reval = mkdir($folder,$mode,true);
	}
	return $reval;
}
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
function jeHtmlspecialchars_decode($str){
	return htmlspecialchars_decode($str, ENT_QUOTES);
}
function escape($str){
	return htmlspecialchars($str, ENT_QUOTES);
}
/**
 * 创建像这样的查询: "IN('a','b')";
 * @access   public
 * @param    mix      $item_list      列表数组或字符串
 * @param    string   $field_name     字段名称 *
 * @return   void
 */
function db_create_in($item_list, $field_name = ''){
	$field_name=' '.$field_name;
	if (empty($item_list)){
		return $field_name . " IN ('') ";
	}
	else{
		if (!is_array($item_list)){
			$item_list = explode(',', $item_list);
		}
		$item_list = array_unique($item_list);
		$item_list_tmp = '';
		foreach ($item_list AS $item){
			if ($item !== ''){
				$item_list_tmp .= $item_list_tmp ? ",'$item'" : "'$item'";
			}
		}
		if(empty($item_list_tmp)){
			return $field_name . " IN ('') ";
		}
		else{
			return $field_name . ' IN (' . $item_list_tmp . ') ';
		}
	}
}
/**
 * 递归删除文件夹
 * @access  public
 * @param  string     $dir   需要删除的文件夹名 编码为gbk *
 * @return bool
 */
function deleteDir($dir)
{
	$dir=rtrim($dir,'/\\');
	if (@rmdir($dir)==false && is_dir($dir)) //删除不了，进入删除所有文件
	{
		if ($dp = opendir($dir))
		{
			while (($file=readdir($dp)) != false)  
			{
				if($file!='.' && $file!='..')
				{
					$file=$dir.'/'.$file;
					if (is_dir($file))  //是真实目录   
					{
						deleteDir($file);   
					}else {
						unlink($file);   
					}
				}
			}
			closedir($dp);
		}else 
		{
			return false;   
		}
	}
	if (is_dir($dir) && @rmdir($dir)==false) //是目录删除不了
		return false;
	return true;
}
/**
 * 加http://
 * @param   string  $url  参数字符串，一个urld地址,对url地址进行校正
 * @return  返回校正过的url;
 */
function add_http($url , $check = 'https://')
{
	if (stripos($url, 'http://' ) === false && stripos($url, 'https://' ) === false){
		$url = $check . $url;
	}
	return $url;
}
function exchange($table, $id, $name){
	import("@.exchange");
	return new exchange($table, $id, $name);
}
/**
 * 取两个串之间内容 如参数一个为空返回为空
 * @param   str		first str;
 * @param   str		second str;
 * @param   str		haystack;
 * @return  str		成功返回串，失败返回空串
 */
function sub_getstr($f,$s,$h)
{
	if(empty($f) ||empty($s) ||empty($h)) return '';
	list($part1,$part2)=explode($f,$h,2);
	empty($part2) ? $get='' : list($get,$tem)=explode($s,$part2,2);
	if($get===$part2) $get='';
	return empty($get) ? '' : $get;
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
   curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Linux; U; Android 2.3.6; zh-cn; GT-S5660 Build/GINGERBREAD) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1 MicroMessenger/4.5.255');
	curl_setopt($ch, CURLOPT_REFERER, 'http://mp.weixin.qq.com/');

*/
function curl_get($url)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$ret = curl_exec($ch);
	curl_close($ch);
	return $ret;
}
/**
 * 检查是否是微信浏览器访问
 */
function is_wechat_browser()
{
	static $is_wechat=null;
	if($is_wechat===null){
		$user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		if (strpos($user_agent, 'micromessenger') === false) {
			$is_wechat=false;
		} else {
			$is_wechat=true;
		}
	}
	return $is_wechat;
}
/**
 *检查$_FILES['上传某name']有没有上传成功
 * @param   array  $files=$_FILES['上传某name']
 * @return  bool
 */
function chk_files($files){
	if (empty($files)){
	   return false;
	}
	if ((isset($files['error']) && $files['error'] == 0) || (!isset($files['error']) && isset($files['tmp_name']) && $files['tmp_name'] != 'none')){
		return true;
	}
	return false;
}

/**
 * 显示一个提示信息
 *
 * @access  public
 * @param   string  $content
 * @param   array   links		null不显示跳转 为跳转数组显示跳转链接   为空显示返回链接
 * @param   string  $type		消息類型， 0消息，1錯誤，2詢問
 * @param   string  $auto_redirect      是否自动跳转
 * @return  void
 */
function show_message($content, $links = array(), $type = 0,  $auto_redirect = true){
	//必须配置项
	$cfg=array('charset'=>'utf-8','img_icon'=>array(__ROOT__.'/Public/images/info.png', __ROOT__.'/Public/images/error.png'));
	//必须配置项

	$title = array('系统信息','错误信息');
	$msg['content'] = $content;
	if($links === null)	{
		$msg['url_info'] = null;
		$msg['back_url'] = '';
	}
	elseif (count($links) == 0 || $links == 0){
		$msg['url_info']['返回上一页'] = 'javascript:history.back()';
		$msg['back_url'] = 'javascript:history.back()';
	}
	else{
		$i=0;
		foreach($links as $text =>$url){
			$msg['url_info'][$text] = $url;
			$i++==0 ? $msg['back_url'] = $url :'';
		}
	}
	$msg['type']    = $type;
	?>
	<!DOCTYPE html>
	<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,Chrome=1" />
		<meta charset="<?php echo $cfg['charset']; ?>" />
		<base href="<?php echo DOMAIN_URL.__ROOT__;?>/"/>		
		<title><?php echo $title[$type]; ?></title>
		<?php if($auto_redirect && $msg['back_url'] !='javascript:history.back()'){?>
			<meta id="stop" http-equiv="refresh" content="3;URL=<?php echo $msg['back_url']; ?>" />
		<?php } ?>
		<?php if($auto_redirect){?>
			<script>
				//history.back() 防回退与意外不跳转
				setTimeout(function(){
					if('<?php echo $msg['back_url'];?>'=='javascript:history.back()'){
						javascript:history.back();
					}else if($msg['back_url']!=''){
						location='<?php echo $msg['back_url'];?>';
					}

				},3000);
			</script>
		<?php } ?>
		<style>
			html{font-size:10px;}
			body{margin:0;font-family: "微软雅黑", "宋体",   Verdana, sans-serif; width:100%; height:100%; background:#f5f5f5;  font-size:1.5rem;}
			.clearfix {zoom: 1}.clearfix:after {content: "\0020";display: block;clear: both;height: 0}
			a.btn{text-decoration:none;color:#ffffff; border:1px solid #059B5F; padding:5px 12px; border-radius:5px; background:#00C175; font-weight:500; display:inline-block;}
			a:hover {background:#3EC993;}
			.clear {clear: both;}
			.tal{text-align:left;}
			
			#error_head{box-shadow: 0 2px 20px 1px rgba(0, 0, 0, 0.5); height:5rem;}
			#error_head .title{ line-height:5rem;width:85%;margin-right: auto;margin-left: auto; }
			#error_head .title .err{font-weight:bold;color: #FFF;font-size:2rem;}
			#message{ text-align:center;width:90%;margin-top:5rem;line-height:1.8;font-weight:bold;margin-right: auto;margin-left: auto;}
			.error_icon{margin-bottom:1rem;}
			.wait{color:#096;}
			.error{color:#C00;font-size:1.8rem;}
			#message_txt .tip{margin-top:2rem;}
			#message_txt .tip p{display:inline-block; margin-right:8px;}
		</style>
	</head>
	<body>
		<div <?php if($type == 0) {?>style="background:#003399;"<?php }else { ?> style="background-color:#E02C36;" <?php }?> id="error_head">
			<div class="title clearfix">
				<span class="err"><?php echo $title[$type]; ?></span>
			</div>
		</div>
		<div id="message">
			<div class="error_icon">
				<img src="<?php echo $cfg['img_icon'][$type];?>">
			</div>
			<div id="message_txt">
				<div class="msg"><span class="error tal"><?php echo  $msg['content']; ?></span></div>
				<?php if($auto_redirect){?>
					<div class="tip"> 系统将在 <span class="wait">3</span> 秒后自动跳转，如果您的浏览器没有跳转请点击 <a href="<?php echo $msg['back_url']; ?>">这里</a></div>
				<?php } ?>
				<div class="tip">	<?php if($msg['url_info']){?>		<?php foreach( $msg['url_info'] as $info=>$url ){?>		<p><a class="btn" href="<?php echo $url; ?>"><?php echo $info; ?></a></p>		<?php } ?>	<?php } ?></div>
			</div>
			<div class="clear"></div>
		</div>
	</body></html>
	<?php
	exit;
}
/**
 * 返回给定参数'app=code&act=show'的相应url
 * @access  public 
 * @return  str
 */
function get_url($str){
	return $str;
}
/**
 * html代码输出
 * @param unknown $str
 * @return string
 */
function html_out($str) {
	if (function_exists('htmlspecialchars_decode')) {
		$str = htmlspecialchars_decode($str);
	} else {
		$str = html_entity_decode($str);
	}
	$str = stripslashes($str);
	return $str;
}
/**
 * 截取字符串，字节格式化
 * @param unknown $str
 * @param unknown $length
 * @param number $start
 * @param string $charset
 * @param string $suffix
 * @return string
 */
function msubstr($str, $length, $start = 0, $charset = "utf-8", $suffix = true) {
	if (function_exists("mb_substr"))
		$slice = mb_substr($str, $start, $length, $charset);
	elseif (function_exists('iconv_substr')) {
		$slice = iconv_substr($str, $start, $length, $charset);
	} else {
		$re['utf-8'] = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
		$re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
		$re['gbk'] = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
		$re['big5'] = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
		preg_match_all($re[$charset], $str, $match);
		$slice = join("", array_slice($match[0], $start, $length));
	}
	return $suffix ? $slice . '...' : $slice;
}

//防注入函数 
function inject_filter($str,$type='url'){
	$farr = array(
	   'url'=>'/select\b|insert\b|update\b|delete\b|drop\b|\"|\'|union\b|into\b|load_file|outfile|\s/i'
	);
	return preg_replace($farr[$type],'',$str);
}
/**
 * 向用户发一条通知
 * @param   string     	 	$uid      		向谁发通知
 * @param   string     	 	$note      		内容
 * @param   int     	 	$type      		1评论 2私信 3评价 4关注 5收藏 6站内通知
 * @return  bool
 */
function notice($uid,$note,$type=6){
	if(empty($uid) || empty($note)){
		return false;
	}
	return M('notification')->data(array('uid'=>$uid ,'note'=>$note,'add_time'=>NOW_TIME,'type'=>$type))->add();
}
/**
 * 重新获得商品图片与商品相册的地址
 *
 * @param int $goods_id 商品ID
 * @param string $image 原商品相册图片地址
 * @param boolean $thumb 是否为缩略图
 * @param string $call 调用方法(商品图片还是商品相册)
 * @param boolean $del 是否删除图片
 *
 * @return string   $url
 */
function get_image_path($goods_id, $image = '', $thumb = false, $call = 'goods', $del = false) {
	$url = '/images/no_picture.gif';
	if (!empty($image)) {
		if (strtolower(substr($image, 0, 4)) == 'http') {
			return $image;
		}
		$url = '/' . $image;
	}
	return $url;
}
/**
 * 生成本地 合并
 * @param str or array		$js 逗号分隔的
 * @return bool
 */
function js_creat($js) {
	static $mod=null;	if($mod===null) $mod=strpos(C('JS_MOD'),'cdn')!==false ? 'min' : C('JS_MOD');//cdn用min  其它按配置
	$str_js=$js;
	if(is_string($js))
		$js=explode(',',$js);
	else
		$str_js=implode(',',$js);
	$url = 'runtime/'.(APP_DEBUG ? 'debug_':'').md5($str_js).'.js';
	$path = PUBLIC_PATH.$url;
	$url =  DOMAIN_URL.ROOT_URL.PUBLIC_URL.$url;
	//调试模式删除正式模式的
	if(APP_DEBUG && file_exists(PUBLIC_PATH.'runtime/'.md5($str_js).'.js')) @unlink(PUBLIC_PATH.'runtime/'.md5($str_js).'.js') ;
	if(!file_exists($path) || APP_DEBUG){//调试模式覆盖
		$js_content = '';
		foreach($js as $v)
			if($i=js_get($v,$mod,PUBLIC_PATH))
				$js_content .= @file_get_contents($i)."\r\n";
			
		@file_put_contents($path,$js_content);
	}
	return $url.'?v=1';
}
function css_creat($js) {
	static $mod=null;	if($mod===null) $mod=strpos(C('JS_MOD'),'cdn')!==false ? 'min' : C('JS_MOD');//cdn用min  其它按配置
	$str_js=$js;
	if(is_string($js))
		$js=explode(',',$js);
	else
		$str_js=implode(',',$js);
	$url = 'runtime/'.(APP_DEBUG ? 'debug_':'').md5($str_js).'.css';
	$path = PUBLIC_PATH.$url;
	$url =  DOMAIN_URL.ROOT_URL.PUBLIC_URL.$url;
	//调试模式删除正式模式的
	if(APP_DEBUG && file_exists(PUBLIC_PATH.'runtime/'.md5($str_js).'.css')) @unlink(PUBLIC_PATH.'runtime/'.md5($str_js).'.css');
	if(!file_exists($path) || APP_DEBUG){//调试模式覆盖
		$js_content = '';
		foreach($js as $v)
			if($i=css_get($v,$mod,PUBLIC_PATH)){
				$js_content .= @file_get_contents($i)."\r\n";
			}
			
		@file_put_contents($path,$js_content);
	}
	return $url.'?v=1';	
}
/**
 * 设置为cdn不合并 
 * @param str or array		$js 逗号分隔的
 * @return bool
 */
function js_creat_cdn($js) {
	static $path=null;	if($path===null) $path=strpos(C('JS_MOD'),'cdn')!==false ? '' : DOMAIN_URL.ROOT_URL.PUBLIC_URL;  
	$str_js=$js;
	if(is_string($js))
		$js=explode(',',$js);
	else
		$str_js=implode(',',$js);
	$return = '';
	foreach($js as $v)
		if($i=js_get($v,C('JS_MOD'),$path))
			$return .= '<script src="'.$i.'"></script>'."\r\n";
	
	return $return;
}
//css不能合并  有路径问题
function css_creat_cdn($js) {
	static $path=null;	if($path===null) $path=strpos(C('JS_MOD'),'cdn')!==false ? '' : DOMAIN_URL.ROOT_URL.PUBLIC_URL;  
	$str_js=$js;
	if(is_string($js))
		$js=explode(',',$js);
	else
		$str_js=implode(',',$js);
	$return = '';
	foreach($js as $v)
		if($i=css_get($v,C('JS_MOD'),$path))
			 $return .= '<link href="'.$i.'" rel="stylesheet">';

	return $return;
}
/**
 *  根据设置 返回js文件  重复取返回false
 *
 * @param str 		$js 要返回的js
 * @param string 	$zip 值为zip压缩   其它不压缩
 * @return 返回js本地或cdn路径   失败false;   
 */
function js_get($js,$mod='',$path='') {
	static $get=array();
	if(isset($get[$js]))
		return false;
	else
		$get[$js]=true;
	$all=array(
		'jquery'=>array(
			'cdn'=>'https://cdn.bootcss.com/jquery/1.12.4/jquery.min.js',
			'min'=>'min/jquery/1.12.4/jquery.min.js',
			'no_min'=>'no_min/jquery/1.12.4/jquery.js',
		),	
		'jquery1123'=>array(
			'cdn'=>'https://cdn.bootcss.com/jquery/1.12.3/jquery.min.js',
			'min'=>'min/jquery/1.12.3/jquery.min.js',
			'no_min'=>'no_min/jquery/1.12.3/jquery.js',
		),	
		'jquery214'=>array(
			'cdn'=>'https://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js',
			'min'=>'min/jquery/2.1.4/jquery.min.js',
			'no_min'=>'no_min/jquery/2.1.4/jquery.js',
		),
		'jquery224'=>array(
			'cdn'=>'https://cdn.bootcss.com/jquery/2.2.4/jquery.min.js',
			'min'=>'min/jquery/2.2.4/jquery.min.js',
			'no_min'=>'no_min/jquery/2.2.4/jquery.js',
		),	
		'bootstrap'=>array(
			'cdn'=>'https://cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js',
			'min'=>'min/bootstrap/3.3.7/js/bootstrap.min.js',
			'no_min'=>'no_min/bootstrap/3.3.7/js/bootstrap.js',
		),	
		'artDialog'=>array(
			'cdn'=>'https://cdn.bootcss.com/artDialog/6.0.4/dialog-min.js',
			'min'=>'min/artDialog/6.0.4/dialog-min.js',
			'no_min'=>'no_min/artDialog/6.0.4/dialog.js',
		),	
		'jquery.cookie'=>array(
			'cdn'=>'https://apps.bdimg.com/libs/jquery.cookie/1.4.1/jquery.cookie.min.js',
			'min'=>'min/jquery.cookie/1.4.1/jquery.cookie.min.js',
			'no_min'=>'no_min/jquery.cookie/1.4.1/jquery.cookie.js',
		),	
		'jquery.qrcode'=>array(
			'cdn'=>'https://apps.bdimg.com/libs/jquery-qrcode/1.0.0/jquery.qrcode.min.js',
			'min'=>'min/jquery.qrcode/1.0/jquery.qrcode.min.js',
			'no_min'=>'no_min/jquery.qrcode/1.0/jquery.qrcode.js',
		),	
		'jquery.datetimepicker'=>array(
			'cdn'=>'https://cdn.bootcss.com/jquery-datetimepicker/2.5.4/jquery.datetimepicker.min.js',
			'min'=>'min/jquery.datetimepicker/2.5.4/jquery.datetimepicker.min.js',
			'no_min'=>'no_min/jquery.datetimepicker/2.5.4/jquery.datetimepicker.js',
		),	
		'fancybox'=>array(
			'cdn'=>'https://apps.bdimg.com/libs/fancybox/2.1.5/jquery.fancybox.min.js',
			'min'=>'min/fancybox/2.1.7/jquery.fancybox.min.js',
			'no_min'=>'no_min/fancybox/2.1.7/jquery.fancybox.js',
		),	
		'layui'=>array(
			'cdn'=>DOMAIN_URL.ROOT_URL.PUBLIC_URL.'min/layui/2.4.3/layui.js',
			'min'=>'min/layui/2.4.3/layui.js',
			'no_min'=>'no_min/layui/2.4.3/layui.js',
		),	
		'layui2245'=>array(
			'cdn'=>DOMAIN_URL.ROOT_URL.PUBLIC_URL.'min/layui/2.2.45/layui.js',
			'min'=>'min/layui/2.2.45/layui.js',
			'no_min'=>'no_min/layui/2.2.45/layui.js',
		),	
		'ueditor'=>array(
			'cdn'=>'https://apps.bdimg.com/libs/ueditor/1.4.3.1/ueditor.all.min.js',
			'min'=>'min/ueditor/1.4.3.3/ueditor.all.min.js',
			'no_min'=>'no_min/ueditor/1.4.3.3/ueditor.all.js',
		),
		'html5shiv'=>array(
			'cdn'=>'https://apps.bdimg.com/libs/html5shiv/3.7/html5shiv.min.js',
			'min'=>'min/html5shiv/3.7.3/html5shiv.min.js',
			'no_min'=>'no_min/html5shiv/3.7.3/html5shiv.js',
		),
		'respond'=>array(
			'cdn'=>'https://apps.bdimg.com/libs/respond.js/1.4.2/respond.js',
			'min'=>'min/respond/1.4.2/respond.min.js',
			'no_min'=>'no_min/respond/1.4.2/respond.js',
		),
		'summernote'=>array(
			'cdn'=>'https://cdn.bootcss.com/summernote/0.8.10/summernote.min.js',
			'min'=>'min/summernote/0.8.10/summernote.min.js',
			'no_min'=>'no_min/summernote/0.8.10/summernote.js',
		),
		'summernote088'=>array(
			'cdn'=>'https://cdn.bootcss.com/summernote/0.8.8/summernote.min.js',
			'min'=>'min/summernote/0.8.8/summernote.min.js',
			'no_min'=>'no_min/summernote/0.8.8/summernote.js',
		),
		'webuploader'=>array(
			'cdn'=>'https://cdn.staticfile.org/webuploader/0.1.5/webuploader.js',
			'min'=>'min/webuploader/0.1.5/webuploader.min.js',
			'no_min'=>'no_min/webuploader/0.1.5/webuploader.js',
		),
		'morris'=>array(
			'cdn'=>'https://cdn.bootcss.com/morris.js/0.5.1/morris.min.js',
			'min'=>'min/morris/0.5.1/morris.min.js',
			'no_min'=>'no_min/morris/0.5.1/morris.js',
		),
		'gritter'=>array(
			'cdn'=>DOMAIN_URL.ROOT_URL.PUBLIC_URL.'min/gritter/1.7.4/jquery.gritter.min.js',
			'min'=>'min/gritter/1.7.4/jquery.gritter.min.js',
			'no_min'=>'no_min/gritter/1.7.4/jquery.gritter.js',
		),
		'metisMenu'=>array(
			'cdn'=>'https://cdn.bootcss.com/metisMenu/2.7.1/metisMenu.min.js',
			'min'=>'min/metisMenu/2.7.1/metisMenu.min.js',
			'no_min'=>'no_min/metisMenu/2.7.1/metisMenu.js',
		),
		'slimscroll'=>array(
			'cdn'=>'https://cdn.bootcss.com/jQuery-slimScroll/1.3.8/jquery.slimscroll.min.js',
			'min'=>'min/slimscroll/1.3.8/jquery.slimscroll.min.js',
			'no_min'=>'no_min/slimscroll/1.3.8/jquery.slimscroll.js',
		),
		'layer'=>array(
			'cdn'=>'https://cdn.bootcss.com/layer/2.1/layer.min.js',
			'min'=>'min/layer/2.1/layer.min.js',
			'no_min'=>'no_min/layer/2.1/layer.js',
		),
		'zc_base'=>array(
			'cdn'=>DOMAIN_URL.ROOT_URL.PUBLIC_URL.'zc_base.js',
			'min'=>'zc_base.js',
			'no_min'=>'zc_base.js',
		),	
	);
	return $path.$all[$js][$mod];
}
function css_get($js,$mod='',$path='') {
	static $get=array();
	if(isset($get[$js]))
		return false;
	else
		$get[$js]=true;
	$all=array(
		'bootstrap'=>array(
			'cdn'=>'https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css',
			'min'=>'min/bootstrap/3.3.7/css/bootstrap.min.css',
			'no_min'=>'no_min/bootstrap/3.3.7/css/bootstrap.css',
		),
		'artDialog'=>array(
			'cdn'=>DOMAIN_URL.ROOT_URL.PUBLIC_URL.'min/artDialog/6.0.4/dialog-min.css',
			'min'=>'min/artDialog/6.0.4/dialog-min.css',
			'no_min'=>'no_min/artDialog/6.0.4/dialog.css',
		),	
		'jquery.datetimepicker'=>array(
			'cdn'=>'https://cdn.bootcss.com/jquery-datetimepicker/2.5.4/jquery.datetimepicker.min.css',
			'min'=>'min/jquery.datetimepicker/2.5.4/jquery.datetimepicker.min.css',
			'no_min'=>'no_min/jquery.datetimepicker/2.5.4/jquery.datetimepicker.css',
		),
		'fancybox'=>array(
			'cdn'=>'https://apps.bdimg.com/libs/fancybox/2.1.5/jquery.fancybox.min.css',
			'min'=>'min/fancybox/2.1.7/jquery.fancybox.min.css',
			'no_min'=>'no_min/fancybox/2.1.7/jquery.fancybox.css',
		),	
		'layui'=>array(
			'cdn'=>DOMAIN_URL.ROOT_URL.PUBLIC_URL.'min/layui/2.4.3/css/layui.css',
			'min'=>'min/layui/2.4.3/css/layui.css',
			'no_min'=>'no_min/layui/2.4.3/css/layui.css',
		),	
		'layui2245'=>array(
			'cdn'=>DOMAIN_URL.ROOT_URL.PUBLIC_URL.'min/layui/2.2.45/css/layui.css',
			'min'=>'min/layui/2.2.45/css/layui.css',
			'no_min'=>'no_min/layui/2.2.45/css/layui.css',
		),	
		'summernote'=>array(
			'cdn'=>'https://cdn.bootcss.com/summernote/0.8.10/summernote.css',
			'min'=>'min/summernote/0.8.10/summernote.css',
			'no_min'=>'no_min/summernote/0.8.10/summernote.css',
		),
		'summernote088'=>array(
			'cdn'=>'https://cdn.bootcss.com/summernote/0.8.8/summernote.css',
			'min'=>'min/summernote/0.8.8/summernote.css',
			'no_min'=>'no_min/summernote/0.8.8/summernote.css',
		),
		'webuploader'=>array(
			'cdn'=>'https://cdn.staticfile.org/webuploader/0.1.5/webuploader.css',
			'min'=>'min/webuploader/0.1.5/webuploader.css',
			'no_min'=>'no_min/webuploader/0.1.5/webuploader.css',
		),
		'font-awesome'=>array(
			'cdn'=>'https://cdn.bootcss.com/font-awesome/4.7.0/css/font-awesome.min.css',
			'min'=>'min/font-awesome/4.7.0/font-awesome.min.css',
			'no_min'=>'no_min/font-awesome/4.7.0/font-awesome.css',
		),
		'animate'=>array(
			'cdn'=>'https://cdn.bootcss.com/animate.css/3.0.0/animate.min.css',
			'min'=>'min/animate/3.0/animate.min.css',
			'no_min'=>'no_min/animate/3.0/animate.css',
		),
		'morris'=>array(
			'cdn'=>'https://cdn.bootcss.com/morris.js/0.5.1/morris.css',
			'min'=>'min/morris/0.5.1/morris.min.css',
			'no_min'=>'no_min/morris/0.5.1/morris.css',
		),
		'gritter'=>array(
			'cdn'=>DOMAIN_URL.ROOT_URL.PUBLIC_URL.'min/gritter/1.7.4/jquery.gritter.css',
			'min'=>'min/gritter/1.7.4/jquery.gritter.css',
			'no_min'=>'no_min/gritter/1.7.4/jquery.gritter.css',
		),
		'metisMenu'=>array(
			'cdn'=>'https://cdn.bootcss.com/metisMenu/1.1.3/metisMenu.min.css',
			'min'=>'min/metismenu/1.1.3/metisMenu.min.css',
			'no_min'=>'no_min/metismenu/1.1.3/metisMenu.css',
		),
		'zc_base'=>array(
			'cdn'=>DOMAIN_URL.ROOT_URL.PUBLIC_URL.'zc_base.css',
			'min'=>'css/zc_base.css',
			'no_min'=>'css/zc_base.css',
		),	
	);
	return $path.$all[$js][$mod];
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


/**
 * 利用thinkphp类上传  测试只能同时上传一张图片
 * @param   string     	 	$uploadDir        './abc/' 或 '/www/pic/'
 * @param   string     	 	$files_key        '上传字段名'
 * @param   string     	 	$allowExts        
 * @access  public 
 * @return  失败false  成功返回文件名
 */
function upload_one($uploadDir,$files_key,$allowExts=array('jpg', 'gif', 'png', 'jpeg')){
	$name ='';
	//上传logo 
	if(chk_files($_FILES[$files_key]) && make_dir($uploadDir)){
		import('ORG.Net.UploadFile');
		$upload = new UploadFile();
		$upload->maxSize= C('upload_size_limit');
		$upload->allowExts=$allowExts;
		$upload->savePath = $uploadDir;
		if(!($info = $upload->uploadOne($_FILES[$files_key]))){
			return false;
		}else{// 上传成功 获取上传文件信息
			 $name =$info[0]['savename'];
		}
	}
	return $name;
}
/**
 * 原生上传
 * @param   string     	 	$uploadDir        './abc'
 * @access  public 
 * @return  失败false  成功返回文件名  201505/asdfasd.gif
 */
function upload_pic($uploadDir,$files_key,$allowExts=array('jpg', 'gif', 'png', 'jpeg')){
	$name ='';
	$fileParts = pathinfo($_FILES[$files_key]['name']);
	//上传保存名
	$targetFile = random_name($uploadDir).'.'.$fileParts['extension'];//201502/asfas
	//上传logo 
	if(chk_files($_FILES[$files_key]) && make_dir($uploadDir) && in_array($fileParts['extension'],$allowExts)){		
		if(!move_uploaded_file($_FILES[$files_key]['tmp_name'],$uploadDir.'/'.$targetFile)){
			return false;
		}else{	
			$name =$targetFile;
		}
	}
	return $name;
}

/**
 * 生成一张空png图片并输出
 * @access  public 
 * @return  str
 */
function generateEmptyPng(){
	ob_clean();
	header("Content-type: image/png");
	$im = imageCreate(1, 1);
	$red = imagecolorallocate($im, 255, 0, 0);
	imagePng($im); 
	imageDestroy($im);
}
/**
 * 输出一张1像素gif
 * @access  public 
 * @return  str
 */
function generateEmptyGif1(){
	#ob_end_clean();
	header("Content-type: image/gif");		
	echo base64_decode('R0lGODdhAQABAIAAAAAAAAAAACwAAAAAAQABAAACAkQBADs=');
}
function generateEmptyGif(){
	$im = imageCreate(1,1);
	imageGif($im); 
	imageDestroy($im);
}
/**
 * 检查字符串是否是UTF8编码
 * @param string $string 字符串
 * @return Boolean
 */
function is_utf8($string) {
	return function_exists('mb_detect_encoding') ? mb_detect_encoding($string)=='UTF-8' :
	 preg_match('%^(?:
		 [\x09\x0A\x0D\x20-\x7E]            # ASCII
	   | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
	   |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
	   | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
	   |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
	   |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
	   | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
	   |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
	)*$%xs', $string);
}
// 自动转换字符集 支持数组转换
function auto_charset($fContents, $from='gbk', $to='utf-8') {
	$from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
	$to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
	if (strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents))) {
		//如果编码相同或者非字符串标量则不转换
		return $fContents;
	}
	if (is_string($fContents)) {
		if (function_exists('mb_convert_encoding')) {
			return mb_convert_encoding($fContents, $to, $from);
		} elseif (function_exists('iconv')) {
			return iconv($from, $to, $fContents);
		} else {
			return $fContents;
		}
	} elseif (is_array($fContents)) {
		foreach ($fContents as $key => $val) {
			$_key = auto_charset($key, $from, $to);
			$fContents[$_key] = auto_charset($val, $from, $to);
			if ($key != $_key)
				unset($fContents[$key]);
		}
		return $fContents;
	}
	else {
		return $fContents;
	}
}
//获取已过时间
function pass_date($time){
	$time_span = NOW_TIME - $time;
	if($time_span>3600*24*365){
		$time_span_lang = date('Y-m-d',$time);
	}elseif($time_span>3600*24*30){
		$time_span_lang = date('Y-m-d',$time);
	}elseif($time_span>3600*24){//天
		$time_span_lang = round($time_span/(3600*24))."天前";
	}elseif($time_span>3600){//小时
		$time_span_lang = round($time_span/(3600))."小时前";
	}elseif($time_span>60){//分
		$time_span_lang = round($time_span/(60))."分钟前";	
	}else{//秒
		$time_span_lang = "刚刚";
	}
	return $time_span_lang;
}
//获取缓冲 S('user') 中值
function user($item=''){
	$uid=session('uid');
	if(!$uid)
		return false;
	elseif($item==''){
		$return=hm('user-'.$uid);
	}else{
		$return=h('user-'.$uid,$item);
	}
	if($return){
		return $return;
	}else{
		$row=db()->row('select uid,password,salt,avatar,sex,qq,weixin,phone,type,money,gold_coin,last_visit,last_ip,visit_count,parent_id,status,is_taobaoke,is_pass,check_id from '.C('DB_PREFIX').'user where uid='.$uid);
		hm('user-'.$uid,$row);
		return $item ? $row[$item] : $row;
	}
}
/**
 * 关键字入库  如果重复记数
 *
 * @param   string      $key        被截取的字符串
 * @return  bool true 新增成功   false 加1
 */
function add_keyword($keyword){
	if(!db('keyword')->add(array('keyword'=>$keyword))){
		db()->query('update '.C('DB_PREFIX').'keyword set num=num+1 where keyword= :keyword',array('keyword'=>$keyword));
		return false;
	}
	return true;
}
/**
 * 截取UTF-8编码下字符串的函数
 *
 * @param   string      $str        被截取的字符串
 * @param   int         $length     截取的长度
 * @param   string      $append     附加省略号等
 *
 * @return  string
 */
function sub_str($str, $length = 0, $append ='...')
{
	$charset='utf-8';
	$str = trim($str);
	$strlength = mb_strlen($str,$charset);

	if ($length == 0 || $length >= $strlength){
		return $str;
	}elseif ($length < 0){
		$length = $strlength + $length;
		if ($length < 0){
			$length = $strlength;
		}
	}

	if (function_exists('mb_substr')){
		$newstr = mb_substr($str, 0, $length, $charset);
	}
	elseif (function_exists('iconv_substr')){
		$newstr = iconv_substr($str, 0, $length, $charset);
	}

	if ($append && $str != $newstr){//改变了
		$newstr .= $append;
	}

	return $newstr;
}

/**
 * 注册发短信验证码
 * @return  bool
 */
function sms_send_reg_code($phone,$code)
{
	$url='http://v.juhe.cn/sms/send?mobile='.$phone.'&tpl_id=66713&tpl_value=%23code%23%3D'.$code.'&key=xxxxxxxxxxxxx';
	$re=curl_get($url);
	$re=json_decode($re,true);
	if($re['error_code']==0){
		return true;
	}else return false;
}
/**
 * 发无变量短信
 * @return  bool
 */
function sms_send($phone,$tplId)
{
	$url='http://v.juhe.cn/sms/send?mobile='.$phone.'&tpl_id='.$tplId.'&key=xxxxxxxxxxxxxxxxx';
	$re=curl_get($url);
	$re=json_decode($re,true);
	if($re['error_code']==0){
		return true;
	}else return false;
}
/**
 * 获得指定级的所有下级地区
 *
 * @access      public
 * @param       int     type		0顶级 1省 2市 3县三级
 * @param       int     parent		parent_id
 * @return     Array ( Array ( region_id => 2 ,region_name => 北京 ) 
 */
function get_regions($type = 0, $parent = 0){
	static $region_array=array();
	$suffix=$type.'_'.$parent;
	if(isset($region_array[$suffix])) return $region_array[$suffix];
    return $region_array[$suffix]=db('region')->select('region_id,region_name')->where(array('region_type'=>$type,'parent_id'=>$parent))->query();
}
/**
 * 获得指定级的所有下级地区
 *
 * @access      public
 * @param       int     parent		parent_id
 * @return     Array( $region_id => $region_name) 
 */
function get_regions_assoc($parent = 0){
 	static $region_array=array();
	if(isset($region_array[$parent])) return $region_array[$parent];
   return  $region_array[$parent]=db('region')->where(array('parent_id'=>$parent))->getField('region_id,region_name');	
}
/**
 * 获得指定级的同级地区
 *
 * @access      public
 * @param       int     parent		parent_id
 * @return      array
 */
function get_regions_list($parent=0){
	if(empty($parent)) return array();
	$parent=db('region')->where(array('region_id'=>$parent))->getField('parent_id');
    return db('region')->select('region_id,region_name')->where(array('parent_id'=>$parent))->query();
}

/**
 * 获得指定id的地区名
 *
 * @access      public
 * @param       int     $id		 region_id
 * @return      str
 */
function getRegionName($id){
    return db('region')->where(array('region_id'=>$id))->getField('region_name');
}

/*商品所在地*/
function get_good_region_id($name){
    return trim($name) ? db()->single('SELECT grid FROM '.C('DB_PREFIX').'goods_region WHERE name=:name',array('name'=>$name)) : 0;
}
/*商品所在地*/
function goods_region($id){
    return $id ? db()->single('SELECT name FROM '.C('DB_PREFIX').'goods_region WHERE grid='.$id) :'- -';
}
/*type 0商家    1刷手*/
function is_login($type){
	if(session('uid') && session('type')==$type && user('status')===0){
		return true;
	}
	return false;
}
/**
 * 转为浮点数*100四舍五入  便于比较大小  失败返回0
 *
 * @access      public
 * @param       mixed    $num
 * @return      $num*100
 */
function int100($num){
	return intval(round(floatval($num)*100));
}
/**
 * 转为0或1
 */
function int01($num){
	return intval($num) ? 1 :0;
}
/**
 * 小于0转为0
 */
function minus0($num){
	$num=floatval($num);
	return $num<0?0:$num;
}
function div100($num){
	return intval($num)/100;
}
function plus($num){
	return $num>0?'+'.$num:$num;
}
/**
 * 格式化价格
 *
 * @access  public
 * @param   float  $price  价格
 * @param   bool   $change_price  是否格式化价格 
 * @param   bool   $div100  除100
 * @return  string
 */
function price_format($price, $change_price = true,$div100=true){
	if(!$price)
	{
	 $price=0;
	}elseif($div100) $price/=100;

	if ($change_price)
	{
		switch(C('price_format'))
		{
			case 0:// 四舍五入，保留2位
				$price = number_format($price, 2, '.', '');
				break;
			case 1: // 保留不为 0 的尾数
				$price = preg_replace('/(.*)(\\.)([0-9]*?)0+$/', '\1\2\3', number_format($price, 2, '.', ''));

				if (substr($price, -1) == '.')
				{
					$price = substr($price, 0, -1);
				}
				break;
			case 2: // 不四舍五入，保留1位
				$price = substr(number_format($price, 2, '.', ''), 0, -1);
				break;
			case 3: // 直接取整
				$price = intval($price);
				break;
			case 4: // 四舍五入，保留 1 位
				$price = number_format($price, 1, '.', '');
				break;
			case 5: // 先四舍五入，不保留小数
				$price = round($price);
				break;
		}
		return sprintf(C('currency_format'), $price);
	}
	else
	{
		return number_format($price, 2, '.', '');
	}

	
}
/**
*添加操作事件
* @param	int	$uid		该用户	
* @return array 列表
* @version 0.0.1 11:27 2015/1/22
*/
function event($uid,$type,$id){
	$data=array('uid'=>$uid,'type'=>$type,'id'=>$id,'add_time'=>NOW_TIME,'ip'=>get_client_ip());
	return db('event')->add($data);
}
/**
 * 把临时上传文件移到目标目录
 * @access public
 * @param   str	 tmp_file	UPLOADS_PATH.'tmp/'下文件    		'201803/1.jpg'
 * @param  str	 path		移到UPLOADS_PATH目录的些目录下    	'head_pic'
 * @return  bool
 */		
function move_file($tmp_file,$path){
	$i=UPLOADS_PATH.'tmp/'.$tmp_file;
	if($tmp_file && file_exists($i)){
		$j=UPLOADS_PATH.$path.'/'.$tmp_file;
		make_dir(dirname($j));
		return rename($i,$j);
	}
	return false;
}
/**
 * 检查是否有P图，有p图返回真
 * @access public
 * @param   str	 file路径 如'201803/1.jpg'
 * @return  bool  
 */	
function chk_p($file){
	if(stripos(file_get_contents($file),'adobe')!==false){
		return true;
	}else return false;
	
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
//type 0毫秒  1秒
function formatTime($unixTime,$type=0){
	if($type==0){
		$unixTime=ceil($unixTime/1000);
	}
	$minute='';
	$second=intval($unixTime).'"';
	//秒到分
	if($unixTime>=60){
		$minute=intval($unixTime/60)."'";
		$second=intval($unixTime%60).'"';
	}
	return $minute.$second;
}














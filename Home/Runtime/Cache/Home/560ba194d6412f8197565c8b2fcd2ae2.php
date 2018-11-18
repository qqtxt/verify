<?php if (!defined('JETEE_PATH')) exit();?><!doctype html>
<html>
<head>
	<meta charset=UTF-8 />
	<meta name="referrer" content="no-referrer">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,Chrome=1" />
	<meta name="renderer" content="webkit">
	<meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0">
	<meta name=keywords content="<?php echo ($keywords); ?>"/>
	<meta name=Description content="<?php echo ($description); ?>"/>
	<base href="__ROOT__/"/>
	<title><?php echo ($title); ?></title>
	<?php echo css_creat_cdn('layui,bootstrap');?>
	<link rel="stylesheet" href="<?php echo css_creat('zc_base');?>">
</head>
<body>
<script>
	var j= (function (j) {
		//js 基础配置 放这里可模板设置
		j= new Object();//自定义封装
		j.dialog={};//对话框气泡与表单只能唯一
		j.root='__ROOT__';
		j.images='__ROOT__/Public/images';　
		j.is_login=false;//登陆状态
		return j;
　　})(j);
</script>
<header class="container">
   <div class="row">
		<div class="col-xs-3">
			<a href="#"><img class="j_logo" src="Public/images/logo.png" alt=""></a>
		</div>
		<div class="col-xs-5">			
		</div>      
		<div class="col-xs-4 text-right" id="user">
			<div class="j_un_login">
				<a class="c_333" href="user/login">登陆</a>&nbsp;&nbsp;&nbsp;<a href="user/reg">注册</a>
			</div>
		</div>      
   </div>
</header>
<hr/>	


<div style="width:1200px;height:604px;background-image:url(Public/images/banner.jpg);margin:0 auto;">
</div>

<hr>
<footer class="j_footer">
	<div class="footer-wrap">
		<div class="copy_text">
			<div><?php echo c('copyright');?></div>
			<div><a href="http://www.miibeian.gov.cn/" target="_blank"><?php echo c('icp');?></a></div>
		</div>	
	</div>
</footer>
<div class="css3-spinner" id="css3-spinner">
	<div class="css3-spinner-bounce1"></div>
	<div class="css3-spinner-bounce2"></div>
	<div class="css3-spinner-bounce3"></div>
</div>
<?php echo js_creat_cdn('jquery1123,layui,bootstrap');?>
<script src="<?php echo js_creat('zc_base');?>"></script>


<script src="__ROOT__/Public/user.js"></script>

<div class="main-im">
    <div id="open_im" class="open-im">&nbsp;</div>
    <div class="im_main" id="im_main">
        <div id="close_im" class="close-im"><a href="javascript:void(0);" title="点击关闭">&nbsp;</a></div>
        <a href="http://wpa.qq.com/msgrd?v=3&uin=275386769&site=qq&menu=yes" class="im-qq qq-a" title="在线QQ客服" target="_blank">
            <div class="qq-container"></div>
            <div class="qq-hover-c">
                <img class="img-qq" src="Public/images/qq.png">
            </div>
            <span>QQ在线咨询</span> </a>
            <div class="im-tel">
                <div>客服工作时间</div>
                <div class="tel-num">09:00-18:00</div>
                <div>充值工作时间</div>
                <div class="tel-num">09:00-22:00</div>
            </div>
        <div class="im-footer" style="position: relative">
            <div class="weixing-container">
                <div class="weixing-show">
                    <div class="weixing-txt">
                        微信扫一扫 
                    </div>
                    <img class="weixing-ma" src="Public/images/weixing-ma.jpg"> 
                    <div class="weixing-sanjiao"></div>
                    <div class="weixing-sanjiao-big"></div>
                </div>
            </div>
            <div class="go-top"><a href="javascript:;" title="返回顶部"></a></div>
            <div style="clear: both"></div>
        </div>
    </div>
</div>
<script>
$(function () {
	$('#close_im').bind('click', function () {
        $('#main-im').css("height", "0");
        $('#im_main').hide();
        $('#open_im').show();
    });
    $('#open_im').bind('click', function (e) {
        $('#main-im').css("height", "272");
        $('#im_main').show();
        $(this).hide();
    });
    $('.go-top').bind('click', function () {
        $(window).scrollTop(0);
    });
    $(".weixing-container").bind('mouseenter', function () {
        $('.weixing-show').show();
    })
    $(".weixing-container").bind('mouseleave', function () {
        $('.weixing-show').hide();
    });
});
</script>
</body></html>
<?php if (!defined('JETEE_PATH')) exit();?><!DOCTYPE html>
<!--[if lt IE 7 ]> <html class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html class="ie8"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html> <!--<![endif]-->
<head>
<meta charset=UTF-8 />
<meta http-equiv="X-UA-Compatible" content="IE=edge,Chrome=1" />
<meta name="renderer" content="webkit">
<meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0">
<meta name=keywords content="<?php echo ($keywords); ?>"/>
<meta name=Description content="<?php echo ($description); ?>"/>
<meta name="robots" content="noindex, nofollow">
<base href="__ROOT__/"/>
<title><?php echo ($title); ?></title>
<?php echo css_creat_cdn('layui,bootstrap');?>
<link href="Public/admin/admin.css" rel="stylesheet">
<script>
	var jetee= (function () {
		//js 基础配置 放这里可模板设置
		j= new Object();//自定义封装
		j.dialog={};//对话框气泡与表单只能唯一
		j.root='__ROOT__';
		j.images='__ROOT__/Public/images';　
		j.is_login=false;//登陆状态
		return j;
　　})();
	if (window.top != window){
		window.top.location.href = document.location.href;
	}
</script>
</head>
<body id="to-top" class="sign-in-up">
    <section class="sign-in-up-section">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <!-- Logo -->
            <figure class="text-center" style="height:100px;">
              <!--a href="./index.html"><img class="img-logo" src="Public/images/logo1.png" alt=""></a-->
            </figure>         
          </div>
        </div>

        <section class="sign-in-up-content">
          <div class="row">
            <div class="col-md-12">
              <h4 class="text-center">管理登陆</h4>
              <form role="form" id="login_form" class="sign-in-up-form" autocomplete="off" method="post" action="<?php echo U('Index/login');?>">
                <div class="form-group">
					<input type="text" name="username" value="" class="form-control" id="login_phone" maxlength="11" placeholder="请输入您的帐号" required oninvalid="setCustomValidity('请输入您的帐号')" oninput="setCustomValidity('')"  title="请输入您的帐号"/>
                </div>
                <div class="form-group">
                  <input name="password" class="form-control" id="login_password" type="password" placeholder="请输入您的密码" required title="请输入您的密码" aautocomplete="off" oninvalid="setCustomValidity('请输入密码')" oninput="setCustomValidity('')"/>
                </div>
                <div class="form-group">
					<div class="input-group">
						<input name="verify" class="form-control" id="login_verfiy" type="text" placeholder="请输入验证码" pattern="^\d{4}$" required title="请输入验证码" autocomplete="off" oninvalid="setCustomValidity('请输入验证码')" oninput="setCustomValidity('')"/>
						<span class="input-group-addon" style="padding:1px 1px;"><img onclick="this.src='index.php?m=Light&a=verify_code&thin=200&radom='+(+new Date).toString(36);" src="index.php?m=Light&a=verify_code&thin=200"/></span>
					</div> 
                </div>
                <button class="btn btn-lg btn-success btn-block" type="submit">登陆</button>                
              </form>           
            </div>
          </div>  
        </section>


        <div class="row">
          <div class="col-md-12">
            <section class="footer-copyright text-center">
              <p><?php echo c('copyright');?></p>              
            </section>         
          </div>
        </div>      
      </div>
    </section>
<?php echo js_creat_cdn('jquery,bootstrap');?>
</body></html>
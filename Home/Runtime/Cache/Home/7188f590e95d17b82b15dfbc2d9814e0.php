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
	<base href="__ROOT__/"/>
	<title><?php echo ($title); ?></title>
	<?php echo css_creat_cdn('layui,bootstrap');?>
    <link href="Public/css/user.css" rel="stylesheet">
</head>
<body class="sign-in-up" id="to-top">

<script>
	var j= (function (j) {
		//js 基础配置 放这里可模板设置
		j= new Object();//自定义封装
		j.dialog={};//对话框气泡与表单只能唯一
		j.root='__ROOT__';
		j.images='__ROOT__/Public/images';　
		j.is_login=false;//登陆状态
		j.sms_send_gap=<?php echo C('sms_send_gap');?>;
		j.email_gap_time=<?php echo C('EMAIL_GAP_TIME');?>;
		return j;
　　})(j);
</script>


    <section class="sign-in-up-section">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <!-- Logo -->
            <figure class="text-center">
                <img class="img-logo" src="Public/images/logo.png" alt="">
            </figure>         
          </div>
        </div>

        <section class="sign-in-up-content">
          <div class="row">
            <div class="col-md-12">
              <h4 class="text-center" id="type_reg">&nbsp;</h4>
              <form role="form" id="reg_form" class="sign-in-up-form" autocomplete="off" action="javascript:void(0);" onsubmit="j.doReg();return false;">
                <div class="form-group">				 
				  <input name="phone" class="form-control" id="reg_phone" type="text" placeholder="请输入您的手机号" maxlength="11" required oninvalid="setCustomValidity('用户名必须为手机号码')" oninput="setCustomValidity('')" pattern="^1[34578]{1}\d{9}" title="用户名必须为手机号码" autocomplete="off"/>
                </div>
                <div class="form-group">
					<div class="input-group">
						<input class="form-control" id="reg_num_verfiy" type="text" placeholder="请输入图文验证码" pattern="^\d{4}$" title="请输入图文验证码" autocomplete="off" oninvalid="setCustomValidity('请输入图文验证码')" oninput="setCustomValidity('')"/>
						<span class="input-group-addon" style="padding:1px 1px;"><img onclick="this.src='index.php?m=Light&a=verify_code&thin=200&radom='+Math.floor(Math.random()*100000);" src="index.php?m=Light&a=verify_code&thin=200"/></span>
					</div> 
                </div>
                <div class="form-group">
					<div class="input-group">
						<input name="verfiy" class="form-control" id="reg_verfiy" type="text" placeholder="请输入验证码" required title="请输入验证码" autocomplete="off"/>
						<span class="input-group-btn"><button onclick="j.send_phone_verfiy();" class="btn btn-lg btn-success" type="button">获取验证码</button></span>
					</div>
                </div>
                <div class="form-group">
                  <input name="password" class="form-control" id="reg_password" type="password" placeholder="请输入您的密码" required title="请输入您的密码" autocomplete="off"/>
                </div>
                <div class="form-group">
					<input class="form-control" type="password" id="chk_password" name="chk_password" placeholder="请确认密码" autocomplete="off" onblur="j.checkPassword(this)" required title="两次输入不匹配" oninvalid="setCustomValidity('两次输入不匹配')" oninput="setCustomValidity('')"/>
                </div>
                <div class="form-group">
				  <input type="text" name="qq" class="form-control" id="reg_qq" placeholder="请输入QQ号码"  oninvalid="setCustomValidity('请输入QQ号码')" oninput="setCustomValidity('')" required pattern="^[1-9][0-9]{4,12}$" maxlength="13" />
                </div>
                <div class="form-group">
				  <input type="text" name="weixin" class="form-control" id="reg_weixin" placeholder="请输入微信帐号,手机及邮箱帐号不能通过"  oninvalid="setCustomValidity('请输入微信帐号,手机及邮箱帐号不能通过审核')" oninput="setCustomValidity('')" required pattern="^[a-zA-Z]+[a-zA-Z0-9_\-]*$" maxlength="20" />
                </div>
                <div class="form-group">
				  <input type="text" name="parent_id" class="form-control" id="parent_id" placeholder="请输入邀请码"  oninvalid="setCustomValidity('请输入邀请码')" oninput="setCustomValidity('')" required pattern="^[0-9]{1,11}$" title="请输入邀请码"/>
                </div>
				<div class="checkbox">
					<label>
					<input name="agree" value="1" type="checkbox" checked required  title="必须同意人气云协议" oninvalid="setCustomValidity('必须同意人气云协议')" oninput="setCustomValidity('')"> 同意<a href="javascript:void(0);" onclick="">人气云协议</a></label>
				</div>
				<input id="type" name="type" value="0" type="hidden">
				<input id="pc" name="pc" value="1" type="hidden">
                <button class="btn btn-lg btn-success btn-block" type="submit">确定</button>
				<div id="downApp" class="checkbox text-left mt_20">					
					<a href="<?php echo U('user/login');?>" class="ff_my">登陆页面</a>
				</div>        
                
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

<?php echo js_creat_cdn('jquery,bootstrap,layui');?>
<script src="__ROOT__/Public/user.js?ver=4"></script>
<script>
j.regType();
$.post(j.root+"?m=light&a=checkCloseReg&thin=200",{type:$('#type').val()},function(data){
	if(data.status) if($('#type').val()=='0') alert('本站已关闭商家注册');else alert('本站已关闭买手注册');
},'json');

if($('#type').val()=='1'){
	$('#downApp').html('<a href="<?php echo U('user/download');?>" class="btn btn-lg btn-success btn-block">下载安卓APP（苹果近期上线）</a>');
}else $('#type_reg').text('商家注册');
</script>
<div class="css3-spinner" id="css3-spinner">
	<div class="css3-spinner-bounce1"></div>
	<div class="css3-spinner-bounce2"></div>
	<div class="css3-spinner-bounce3"></div>
</div>
<script>
if(typeof(layui)!='undefined')
layui.use('layer', function(layer){setTimeout(function(){
	layer.tips(' ', '#css3-spinner', {
		tips: [1, '#3595CC'],
		time: 1
	});
},500);});
</script>

</body></html>
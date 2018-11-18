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
<?php echo css_creat_cdn('layui,bootstrap,font-awesome,animate');?>
<link href="Public/h+/css/style.css" rel="stylesheet">
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
</script>
<?php echo js_creat_cdn('jquery,bootstrap,layui,jquery.cookie');?>
<script src="__ROOT__/Public/admin/admin.js"></script>
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
<form method="post" action="Admin/Config/basic" enctype="multipart/form-data" class="form-horizontal">
<div class="ibox float-e-margins">
	<div class="ibox-title">
		<h5>通用设置</h5>
	</div>
	<div class="ibox-content">
		<div class="form-group"><label class="col-sm-2 control-label">网站名称</label><div class="col-sm-5">
			<input name="config[site_name]" value="<?php echo ($config["site_name"]); ?>" class="form-control"></div></div>
		<div class="form-group"><label class="col-sm-2 control-label">网站标题</label><div class="col-sm-5">
			<input name="config[site_title]" value="<?php echo ($config["site_title"]); ?>" class="form-control"></div></div>
		<div class="form-group"><label class="col-sm-2 control-label">keywords</label><div class="col-sm-5">
			<input name="config[keywords]" value="<?php echo ($config["keywords"]); ?>" class="form-control"></div></div>
		<div class="form-group"><label class="col-sm-2 control-label">description</label><div class="col-sm-5">
			<input name="config[description]" value="<?php echo ($config["description"]); ?>" class="form-control"></div></div>
		<div class="form-group"><label class="col-sm-2 control-label">公司名称</label><div class="col-sm-5">
			<input name="config[corp_name]" value="<?php echo ($config["corp_name"]); ?>" class="form-control"></div></div>
		<div class="form-group"><label class="col-sm-2 control-label">公司地址</label><div class="col-sm-5">
			<input name="config[corp_address]" value="<?php echo ($config["corp_address"]); ?>" class="form-control"></div></div>
		<div class="form-group"><label class="col-sm-2 control-label">联系电话</label><div class="col-sm-5">
			<input name="config[corp_tel]" value="<?php echo ($config["corp_tel"]); ?>" class="form-control"></div></div>
		<div class="form-group"><label class="col-sm-2 control-label">联系邮箱</label><div class="col-sm-5">
			<input name="config[site_email]" value="<?php echo ($config["site_email"]); ?>" class="form-control"></div></div>
		<div class="form-group"><label class="col-sm-2 control-label">版权信息</label><div class="col-sm-5">
			<input name="config[copyright]" value="<?php echo ($config["copyright"]); ?>" class="form-control"></div></div>
		<div class="form-group"><label class="col-sm-2 control-label">备案信息</label><div class="col-sm-5">
			<input name="config[icp]" value="<?php echo ($config["icp"]); ?>" class="form-control"></div></div>
	</div>
</div>
<div class="ibox float-e-margins">
	<div class="ibox-title">
		<h5>其他设置</h5>
	</div>
	<div class="ibox-content">
		<div class="form-group"><label class="col-sm-2 control-label">系统后台命名</label><div class="col-sm-5">
			<input name="config[xthtmm]" value="<?php echo ($config["xthtmm"]); ?>" class="form-control"></div></div>
		<div class="form-group"><label class="col-sm-2 control-label">上传文件限制(单位字节)</label><div class="col-sm-5">
			<input name="config[upload_size_limit]" value="<?php echo ($config["upload_size_limit"]); ?>" class="form-control"></div></div>
		<div class="form-group"><label class="col-sm-2 control-label">时间格式</label><div class="col-sm-5">
			<input name="config[time_format]" value="<?php echo ($config["time_format"]); ?>" class="form-control"></div></div>
		<div class="form-group"><label class="col-sm-2 control-label">日期格式</label><div class="col-sm-5">
			<input name="config[date_format]" value="<?php echo ($config["date_format"]); ?>" class="form-control"></div></div>
		<div class="form-group"><label class="col-sm-2 control-label">价格显示设置</label><div class="col-sm-10">
			<input name="config[price_format]" type="radio" value="0" <?php if($config[price_format] == '0'): ?>checked=true<?php endif; ?>> 四舍五入，保留2位小数
					<input name="config[price_format]" type="radio" value="1" <?php if($config[price_format] == '1'): ?>checked=true<?php endif; ?>> 保留不为 0 的尾数
					<input name="config[price_format]" type="radio" value="2" <?php if($config[price_format] == '2'): ?>checked=true<?php endif; ?>> 不四舍五入，保留1位
					<input name="config[price_format]" type="radio" value="3" <?php if($config[price_format] == '3'): ?>checked=true<?php endif; ?>> 直接取整
					<input name="config[price_format]" type="radio" value="4" <?php if($config[price_format] == '4'): ?>checked=true<?php endif; ?>> 四舍五入，保留1位
					<input name="config[price_format]" type="radio" value="5" <?php if($config[price_format] == '5'): ?>checked=true<?php endif; ?>> 四舍五入，不保留小数</div></div>
		<div class="form-group"><label class="col-sm-2 control-label">价格显示格式</label><div class="col-sm-5">
			<input name="config[currency_format]" value="<?php echo ($config["currency_format"]); ?>" class="form-control"></div></div>
		<div class="form-group"><label class="col-sm-2 control-label">每页显示行数</label><div class="col-sm-5">
			<input name="config[page_rows]" value="<?php echo ($config["page_rows"]); ?>" class="form-control"></div></div>
		<div class="form-group"><label class="col-sm-2 control-label">抓取统计数据服务器</label><div class="col-sm-5">
			<input name="config[server_statistics]" value="<?php echo ($config["server_statistics"]); ?>" class="form-control">
			<span class="help-block m-b-none">例：http://www.stat.com   不能有误,同步数据</span></div></div>
		<div class="form-group"><label class="col-sm-2 control-label">微信类名</label><div class="col-sm-5">
			<input name="config[weixin_class]" value="<?php echo ($config["weixin_class"]); ?>" class="form-control">
			<span class="help-block m-b-none">把class中的文本替换成微信号</span></div></div>
		<div class="form-group"><label class="col-sm-2 control-label">同一页面微信显示时间</label><div class="col-sm-5">
			<input name="config[weixin_change_time]" value="<?php echo ($config["weixin_change_time"]); ?>" class="form-control">
			<span class="help-block m-b-none">多长时间后随机显示另一微信</span></div></div>
	</div>
</div>
<input type="submit" class="btn btn-block btn-primary" value="保存更改" />
</form>
</div>
</body></html>
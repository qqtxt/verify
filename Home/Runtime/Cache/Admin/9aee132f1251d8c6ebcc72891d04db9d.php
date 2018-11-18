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
<script src="__ROOT__/Public/listtable.js"></script>
<script src="__ROOT__/Public/admin/admin.js"></script>
<script>
listTable.url = '/Admin';
listTable.app = "<?php echo (MODULE_NAME); ?>";
</script>
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
	<div class="ibox float-e-margins">
		<div class="ibox-title">			
			<span class="action-span1"><a href="Admin/Index/welcome"><?php echo C('xthtmm');?></a> </span>
			<span id="ur_here" class="action-span1"><?php if(isset($ur_here)): ?>-<?php echo ($ur_here); endif; ?></span>
		</div>		
<div class="ibox-content">
	<form name="theForm" id="theForm" method="post" action="Admin/Admin/<?php echo ($action); ?>" class="form-horizontal" onsubmit="return chk();">
		<div class="form-group"><label class="col-sm-2 control-label">管理员</label><div class="col-sm-5">
			<input name="name" maxlength="20" value="<?php echo ($user["username"]); ?>" class="form-control"></div></div>
		<div class="form-group"><label class="col-sm-2 control-label">邮箱地址</label><div class="col-sm-5">
			<input name="email" value="<?php echo ($user["email"]); ?>" class="form-control"></div></div>
		<?php if($action == 'edit'): ?><div class="form-group"><label class="col-sm-2 control-label">旧密码</label><div class="col-sm-5">
			<input type="password" name="oldpass" id="oldpass" maxlength="32" autocomplete="off" class="form-control"></div><div class="col-sm-5">密码不要修改请留空</div></div><?php endif; ?>
		<div class="form-group"><label class="col-sm-2 control-label"><?php if($action == 'edit'): ?>新<?php endif; ?>密码</label><div class="col-sm-5">
			<input type="password" name="pass" id="pass" maxlength="32" autocomplete="off" class="form-control"></div></div>
		<div class="form-group"><label class="col-sm-2 control-label">确认密码</label><div class="col-sm-5">
			<input type="password" id="pass_confirm" name="pass_confirm" maxlength="32" autocomplete="off" class="form-control"></div></div>
		<div class="form-group"><label class="col-sm-2 control-label">角色选择</label><div class="col-sm-5">
			<select name="select_role" id="select_role" class="form-control m-b">
				<option value="">请选择...</option>
				<?php if(is_array($select_role)): foreach($select_role as $key=>$list): ?><option value="<?php echo ($list["role_id"]); ?>" <?php if($list['role_id'] == $user['role_id']): ?>selected="selected"<?php endif; ?> ><?php echo ($list["role_name"]); ?></option><?php endforeach; endif; ?>
			  </select></div></div>
		<div class="form-group"><div class="col-sm-offset-2 col-sm-5">
			<input type="hidden" name="act" value="<?php echo ($form_act); ?>" />
			<input type="hidden" name="id" value="<?php echo ($user["admin_id"]); ?>" />
			<button class="btn btn-block btn-primary" type="submit">提 交</button>
		</div></div>	  
	</form>
</div>
<script>
function chk(){
	<?php if($action == 'edit'): ?>if($('#pass').val()!=$('#pass_confirm').val()){
			alert("确认密码不正确");
			return false;
		}<?php endif; ?>
	return true;
}
</script>
</div></div>
<div class="css3-spinner" id="css3-spinner">
	<div class="css3-spinner-bounce1"></div>
	<div class="css3-spinner-bounce2"></div>
	<div class="css3-spinner-bounce3"></div>
</div>
</body></html>
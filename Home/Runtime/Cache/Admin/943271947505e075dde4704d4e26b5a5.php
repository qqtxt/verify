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
<body>
<div class="wrapper wrapper-content animated fadeInRight">
	<div class="ibox float-e-margins">
		<div class="ibox-title je">			
			<span class="action-span1"><a href="Admin/Index/welcome"><?php echo C('xthtmm');?></a> </span>
			<span id="ur_here" class="action-span1"><?php if(isset($ur_here)): ?>-<?php echo ($ur_here); endif; ?></span>
			<?php if(isset($action_link)): ?><span class="pull-right"><a class="btn btn-primary btn-xs" href="<?php echo ($action_link["href"]); ?>"><?php echo ($action_link["text"]); ?></a></span><?php endif; ?>
			<?php if(isset($action_link2)): ?><span class="pull-right"><a class="btn btn-primary btn-xs" href="<?php echo ($action_link2["href"]); ?>"><?php echo ($action_link2["text"]); ?></a>&nbsp;&nbsp;</span><?php endif; ?>
		</div>
<table class="table table-striped table-hover">
  <tr>
    <th colspan="4" class="group-title"><?php echo L('system_info');?>
		<div class="pull-right">
			<a class="btn btn-primary btn-xs" href="/?sync=start" target="_blank" onclick="j.reload();return true;">开启同步数据</a>
			<a class="btn btn-primary btn-xs" href="/?sync=stop">停止同步数据</a>
		</div>
	</th>
  </tr>
  <tr>
    <td width="20%">版本:</td>
    <td width="30%"><?php echo C('VER');?></td>
    <td>最后同步数据时间</td>
    <td><?php echo (($a=file_get_contents(APP_PATH.'Runtime/Data/cron_check.txt'))?date('Y-m-d H:i:s',$a):'暂无');?></td>
  </tr>
  <tr>
    <td><?php echo L('php_version');?></td>
    <td><?php echo ($sys_info["php_ver"]); ?></td>
    <td><?php echo L('mysql_version');?></td>
    <td><?php echo ($sys_info["mysql_ver"]); ?></td>
  </tr>
  <tr>
    <td><?php echo L('safe_mode');?></td>
    <td><?php echo ($sys_info["safe_mode"]); ?></td>
    <td width="20%"><?php echo L('web_server');?></td>
    <td width="30%"><?php echo ($sys_info["os"]); ?> (<?php echo ($sys_info["ip"]); ?>) <?php echo ($sys_info["web_server"]); ?></td>
  </tr>
  <tr>
    <td><?php echo L('socket');?></td>
    <td><?php echo ($sys_info["socket"]); ?></td>
    <td><?php echo L('timezone');?></td>
    <td><?php echo ($sys_info["timezone"]); ?></td>
  </tr>
  <tr>
    <td><?php echo L('gd_version');?></td>
    <td><?php echo ($sys_info["gd"]); ?></td>
    <td><?php echo L('zlib');?></td>
    <td><?php echo ($sys_info["zlib"]); ?></td>
  </tr>
  <tr>
    <td><?php echo L('curl');?></td>
    <td><?php echo ($sys_info["curl"]); ?></td>
    <td><?php echo L('max_filesize');?></td>
    <td><?php echo ($sys_info["max_filesize"]); ?></td>
  </tr>
  <tr>
    <td>系统时间</td>
    <td><?php echo date('Y-m-d H:i:s');?></td>
    <td><?php echo L('short_tag');?></td>
    <td><?php echo ($sys_info["short_tag"]); ?></td>
  </tr>
  <tr>
    <td>charset</td>
    <td>UTF-8</td>
    <td>PHP信息（phpinfo）</td>
    <td><a target="_blank" href="Admin/Index/phpinfo">PHPINFO</a></td>
  </tr>
</table>
</div></div>
<div class="css3-spinner" id="css3-spinner">
	<div class="css3-spinner-bounce1"></div>
	<div class="css3-spinner-bounce2"></div>
	<div class="css3-spinner-bounce3"></div>
</div>
<script>
if (document.getElementById("listDiv")){
	document.getElementById("listDiv").onclick = function(e){
		e = e || window.event;
		var obj = document.all ? e.srcElement : e.target;
		if (obj.tagName == "INPUT" && obj.type == "checkbox"){
			if (!document.forms['listForm']){
				return;
			}
			var nodes = document.forms['listForm'].elements;
			var checked = false;
			for (i = 0; i < nodes.length; i++){
				if (nodes[i].checked && nodes[i].name=='checkboxes[]'){
					checked = true;
					break;
				}
			}			
			for (i = 0; i <= 10; i++){
				if (document.getElementById("btnSubmit" + i)){
					document.getElementById("btnSubmit" + i).disabled = !checked;
				}
			}
		}
	}

}
</script>
</body></html>
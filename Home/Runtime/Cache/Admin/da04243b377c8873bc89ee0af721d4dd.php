<?php if (!defined('JETEE_PATH')) exit(); if($full_page): ?><!DOCTYPE html>
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

<form method="post" action="/Admin/Admin/lists" name="listForm" onsubmit="return false">
<div class="list-div ibox-content" id="listDiv"><?php endif; ?>

<table class="table table-striped table-hover" id='list-table'>
<thead>
  <tr>
    <th>角色名</th>
    <th>角色描述</th>
    <th>操作</th>
  </tr>
</thead>
<tbody>
  <?php if(is_array($role_list)): foreach($role_list as $key=>$list): ?><tr>
    <td class="first-cell" ><?php echo ($list["role_name"]); ?></td>
    <td class="first-cell" ><?php echo ($list["role_describe"]); ?></td>
    <td align="center">
      <a href="Admin/<?php echo trim(MODULE_NAME);?>/edit?id=<?php echo ($list["role_id"]); ?>" title="编辑全局权限"><img src="Public/admin/images/icon_edit.gif" class="je_icon"></a>
	  &nbsp;<a href="Admin/Account_auth/edit?id=<?php echo ($list["role_id"]); ?>" title="编辑帐户权限"><img src="Public/admin/images/icon_add.gif" class="je_icon"/></a>
	  &nbsp;<a href="javascript:;" onclick="listTable.remove(<?php echo ($list["role_id"]); ?>, '您确定要删除？')" title="删除"><img src="Public/admin/images/icon_drop.gif" class="je_icon"></a>
</td>
  </tr><?php endforeach; endif; ?>
</tbody>
</table>

<?php if($full_page): ?></div>
</form>
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
</body></html><?php endif; ?>
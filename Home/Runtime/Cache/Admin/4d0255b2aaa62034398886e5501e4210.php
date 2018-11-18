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
<form method="post" action="" name="listForm" id="listForm">
<div class="list-div ibox-content" id="listDiv"><?php endif; ?>

<table class="table table-striped table-hover" id='list-table'>
<thead>
  <tr>
	<th>分类ID</th>
    <th>文章分类名称</th>
    <th>描述</th>
    <th>排序</th>
    <th>操作</th>
  </tr>
</thead>
<tbody>
  <?php if(is_array($lists)): foreach($lists as $key=>$cat): ?><tr align="left" class="<?php echo ($cat["level"]); ?>" id="<?php echo ($cat["level"]); ?>_<?php echo ($cat["cat_id"]); ?>">
	<td align="center"><?php echo ($cat["cat_id"]); ?></td>
    <td class="nowrap" valign="top" >
      <img src="Public/admin/images/menu_minus.gif" id="icon_<?php echo ($cat["level"]); ?>_<?php echo ($cat["cat_id"]); ?>" width="9" height="9" border="0" style="margin-left:<?php echo ($cat["level"]); ?>em" onclick="rowClicked(this)" />&nbsp;<span onclick="javascript:listTable.edit(this, 'edit/act/cat_name', <?php echo ($cat["id"]); ?>)"><?php echo (($cat["cat_name"])?($cat["cat_name"]):'N/A'); ?></span>
    </td>
    <td valign="top"><span onclick="javascript:listTable.edit(this, 'edit/act/cat_desc', <?php echo ($cat["id"]); ?>)"><?php echo (($cat["cat_desc"])?($cat["cat_desc"]):'N/A'); ?></span></td>
    <td><span onclick="listTable.edit(this, 'edit/act/sort_order', <?php echo ($cat["cat_id"]); ?>)"><?php echo ($cat["sort_order"]); ?></span></td>
    <td align="center">
      <a href="<?php echo ($cat["url"]); ?>" title="查看" ><img src="Public/admin/images/icon_view.gif" class="je_icon"/></a>&nbsp;
      <a href="javascript:;" onclick="jetee.moveTo(<?php echo ($cat["id"]); ?>)" title="转移分类"><img src="Public/admin/images/icon_edit.gif" class="je_icon"/></a>&nbsp;
      <a href="javascript:;" onclick="listTable.remove(<?php echo ($cat["id"]); ?>, '您确定要删除？')" title="删除"><img src="Public/admin/images/icon_drop.gif" class="je_icon"/></a></td>
  </tr><?php endforeach; endif; ?>
  <?php if(empty($lists)): ?><tr><td class="no-records" colspan="20">没有记录</td></tr><?php endif; ?>
</tbody>
</table>

<?php if($full_page): ?></div>
</form>
<script>
//类 转移分类
jetee.moveTo=function($id){
	j.minWin('Admin/<?php echo trim(MODULE_NAME);?>/edit/act/moveTo?id='+$id,'转移分类',['500px', '380px']);
}
</script>

<script type="text/javascript">
<!--
var imgPlus = new Image();
imgPlus.src = "Public/admin/images/menu_plus.gif";

/**
 * 折叠分类列表
 */
function rowClicked(obj)
{   
  img = obj;// 当前图像  
  obj = obj.parentNode.parentNode;// 取得上二级tr>td>img对像  
  var tbl = document.getElementById("list-table");// 整个分类列表表格  
  var lvl = parseInt(obj.className);// 当前分类级别 
  var fnd = false; // 是否找到元素
  var sub_display = img.src.indexOf('menu_minus.gif') > 0 ? 'none' : document.all ? 'block' : 'table-row' ;
  for (i = 0; i < tbl.rows.length; i++)// 遍历所有的分类
  {
      var row = tbl.rows[i];
      if (row == obj)
      {
          // 找到当前行
          fnd = true;
          //document.getElementById('result').innerHTML += 'Find row at ' + i +"<br/>";
      }
      else
      {
          if (fnd == true)
          {
              var cur = parseInt(row.className);
              var icon = 'icon_' + row.id;
              if (cur > lvl)
              {
                  row.style.display = sub_display;
                  if (sub_display != 'none')
                  {
                      var iconimg = document.getElementById(icon);
                      iconimg.src = iconimg.src.replace('plus.gif', 'minus.gif');
                  }
              }
              else
              {
                  fnd = false;
                  break;
              }
          }
      }
  }

  for (i = 0; i < obj.cells[1].childNodes.length; i++)
  {
      var imgObj = obj.cells[1].childNodes[i];
      if (imgObj.tagName == "IMG" && imgObj.src != 'Public/admin/images/menu_arrow.gif')
      {
          imgObj.src = (imgObj.src == imgPlus.src) ? 'Public/admin/images/menu_minus.gif' : imgPlus.src;
      }
  }
}
//-->
</script>

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
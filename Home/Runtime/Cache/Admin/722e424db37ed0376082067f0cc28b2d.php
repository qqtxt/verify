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
<div class="form-div">
<form action="javascript:search()" name="searchForm">
	<i class="fa fa-search"></i>
	<select name="cat_id" id="cat_id" class="select">
		<option value="">全部分类</option>
		<?php echo $article_cat_list=d('Article_cat')->article_cat_list();?>
	</select>
	<select name="status" id="status" class="select">
	  <option value="99">审核状态</option>
	  <option value="1">是</option>
	  <option value="0">否</option>
	  <option value="4">已删除</option>
	</select>
	关键字： <input class="keyword" type="text" name="keyword" id="keyword" /> 
	<input type="submit" value="搜索" class="button" />  信息标题
</form>
</div>
<script type="text/javascript">
function search(){
	listTable.filter.cat_id =   $('#cat_id').val(); 
	listTable.filter.status =   $('#status').val(); 
	listTable.filter.keyword =   $.trim($('#keyword').val()); 
	listTable.filter.page = 1;
	listTable.loadList();
}
</script>

<form method="post" action="" name="listForm" id="listForm">
<div class="list-div ibox-content" id="listDiv"><?php endif; ?>

<table class="table table-striped table-hover" id='list-table'>
<thead>
  <tr>
	<th width="6%"><input onclick='listTable.selectAll(this, "checkboxes")' type="checkbox"><a href="javascript:listTable.sort('a.article_id');">ID</a><?php echo ($sort_article_id); ?></th>
	<th><a href="javascript:listTable.sort('a.cat_id');">分类</a><?php echo ($sort_cat_id); ?></th>
	<th><a href="javascript:listTable.sort('a.title');">标题</a><?php echo ($sort_title); ?></th>
	<th><a href="javascript:listTable.sort('a.author');">作者</a><?php echo ($sort_author); ?></th>
	<th><a href="javascript:listTable.sort('a.add_time');">添加时间</a><?php echo ($sort_add_time); ?></th>
	<th><a href="javascript:listTable.sort('a.sort_order');" title="从大到小排序">排序</a><?php echo ($sort_sort_order); ?></th>
	<th><a href="javascript:listTable.sort('a.status');">审核状态</a><?php echo ($sort_status); ?></th>
	<th>操作</th>
  </tr>
</thead>
<tbody>
<?php if(is_array($lists)): foreach($lists as $key=>$list): ?><tr>
		<td align="center"><input name="checkboxes[]" type="checkbox" value="<?php echo ($list["article_id"]); ?>" /> <?php echo ($list["article_id"]); ?></td>
		<td><span onclick="javascript:listTable.edit(this, 'edit/act/cat_name', <?php echo ($list["article_id"]); ?>)"><?php echo (($list["cat_name"])?($list["cat_name"]):'N/A'); ?></span></td>
		<td><span onclick="javascript:listTable.edit(this, 'edit/act/title', <?php echo ($list["article_id"]); ?>)"><?php echo (($list["title"])?($list["title"]):'N/A'); ?></span></td>
		<td><span onclick="javascript:listTable.edit(this, 'edit/act/author', <?php echo ($list["article_id"]); ?>)"><?php echo (($list["author"])?($list["author"]):'N/A'); ?></span></td>
		<td><?php echo (format_time($list["add_time"])); ?></td>
		<td><span onclick="javascript:listTable.edit(this, 'edit/act/sort_order', <?php echo ($list["article_id"]); ?>)"><?php echo (($list["sort_order"])?($list["sort_order"]):'N/A'); ?></span></td>
		<td><?php if($list['status'] == 4): ?>已删除<?php else: ?>
		<img src="Public/admin/images/<?php if($list['status'] == 1): ?>yes<?php else: ?>no<?php endif; ?>.gif" onclick="listTable.toggle(this, 'edit/act/status', <?php echo ($list['article_id']); ?>)" /><?php endif; ?></td>
		<td>
			<a href="article/show/id/<?php echo ($list["article_id"]); ?>" title="查看" target="_blank"><img src="Public/admin/images/icon_view.gif" class="je_icon"></a>&nbsp;
			<a href="javascript:;" onclick="j.edit(<?php echo ($list["article_id"]); ?>)" title="编辑"><img src="Public/admin/images/icon_edit.gif" class="je_icon"></a>&nbsp;
			<a href="javascript:;" onclick="listTable.remove(<?php echo ($list["article_id"]); ?>, '您确定要删除？')" title="删除"><img src="Public/admin/images/icon_drop.gif" class="je_icon"></a></td>
	</tr><?php endforeach; endif; ?>
	<?php if(empty($lists)): ?><tr><td class="no-records" colspan="20">没有记录</td></tr><?php endif; ?>
	<tr>
		<td align="right" nowrap="true" colspan="20">
			<div id="turn-page">
		<?php if($record_count < (40000000000-1)): ?>总计 <span id="totalRecords"><?php echo ($record_count); ?></span>个记录分为 <span id="totalPages"><?php echo ($page_count); ?></span>页<?php endif; ?>
		当前第 <span id="pageCurrent"><?php echo ($filter["page"]); ?></span>
		页，每页 <input type='text' size='3' id='pageSize' value="<?php echo ($filter["page_size"]); ?>" onkeypress="return listTable.changePageSize(event)" />
		<span id="page-link">
			<a href="javascript:listTable.gotoPageFirst()">第一页</a>&nbsp;
			<a href="javascript:listTable.gotoPagePrev()">上一页</a>&nbsp;
			<a href="javascript:listTable.gotoPageNext()">下一页</a>&nbsp; 
			<a href="javascript:listTable.gotoPageLast()">最后一页</a>
			<select id="gotoPage" onchange="listTable.gotoPage(this.value)">
				<?php echo smarty_create_pages($page_count,$filter['page']);?>
			</select>
		</span>
	</div>

		</td>
	</tr>
</tbody>
</table>

<?php if($full_page): ?></div>
<div class="mt_5 batch">
	<input type="submit" id="btnSubmit1" value="批量审核" disabled="true" class="btn btn-primary btn-xs" onclick="$('#listForm').attr('action','Admin/<?php echo trim(MODULE_NAME);?>/edit/act/batch_check'); return confirm('您确定要批量通过审核？');"/>
	<input type="submit" id="btnSubmit2" value="批量删除" disabled="true" class="btn btn-primary btn-xs" onclick="$('#listForm').attr('action','Admin/<?php echo trim(MODULE_NAME);?>/del'); return confirm('您确定要批量删除？');"/>
	<span class="dib w80"></span>
	<select name="cat_id" id="moveToId" class="select">
		<option value="">全部分类</option>
		<?php echo $article_cat_list;?>
    </select>	
	<input type="submit" id="btnSubmit3" value="批量转移分类" disabled="true" class="btn btn-primary btn-xs" onclick="$('#listForm').attr('action','Admin/<?php echo trim(MODULE_NAME);?>/edit/act/batchMoveTo');if(!$('#moveToId').val()){alert('请选择要转移到的分类');return false;} return confirm('您确定要批量转移分类？');"/>
</div>
</form>

<script>
j.add=function(){
	j.minWin('Admin/<?php echo trim(MODULE_NAME);?>/add',"添加<?php echo L('log.'.strtolower(MODULE_NAME));?>");
};
j.edit=function(id){
	j.minWin('Admin/<?php echo trim(MODULE_NAME);?>/edit/act/showEdit/id/'+id,"修改<?php echo L('log.'.strtolower(MODULE_NAME));?>");
};
listTable.recordCount = <?php echo ($record_count); ?>;
listTable.pageCount = <?php echo ($page_count); ?>;
<?php if(is_array($filter)): foreach($filter as $key=>$item): ?>listTable.filter.<?php echo ($key); ?> = '<?php echo ($item); ?>';<?php endforeach; endif; ?>
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
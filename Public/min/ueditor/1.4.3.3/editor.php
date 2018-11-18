<!DOCTYPE html>
<html>
<head>
<meta charset=UTF-8 />
<title>Editor</title>
<script type="text/javascript" src="ueditor.config.js"></script>
<script type="text/javascript" src="ueditor.all.min.js"></script>
<script type="text/javascript" src="lang/zh-cn/zh-cn.js"></script>
<script src="../../jquery/1.12.4/jquery.min.js"></script>
<style type="text/css">
body {margin:0px; padding:0px;}
#container {width:640px; height:400px;}
</style>
</head>

<body>
<script type="text/plain" name="content" id="container"></script>
<script type="text/javascript">
<?php $item = htmlspecialchars($_GET['item']);?>
var cBox = $('#<?php echo $item;?>', parent.document);
var editor = UE.getEditor('container');
editor.addListener('ready', function() {
  $('#detail-table', parent.document).hide();//先显示再隐藏编辑器，兼容部分浏览在display:none时无法创建的问题
  var content = cBox.val();
  editor.setContent(content);
});
//editor.addListener("contentChange", function(){setSync()});//触发同步
$(function(){
  window.setInterval("setSync()",1000);//自动同步
})
function setSync(){
  var content = editor.getContent();
  cBox.val(content);
}
</script>
</body>
</html>
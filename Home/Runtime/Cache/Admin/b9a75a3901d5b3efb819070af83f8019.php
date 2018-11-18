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
	<?php echo css_creat_cdn('layui,bootstrap,font-awesome,animate,gritter');?>
    <link href="Public/h+/css/style.css" rel="stylesheet">
    <link href="Public/admin/admin.css" rel="stylesheet">
	<script>
		if (window.top != window){
			window.top.location.href = document.location.href;
		}
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
</head>
<body class="fixed-sidebar full-height-layout gray-bg" style="overflow:hidden">
    <div id="wrapper">
        <!--左侧导航开始-->
        <nav class="navbar-default navbar-static-side" role="navigation">
            <div class="nav-close"><i class="fa fa-times-circle"></i>
            </div>
            <div class="sidebar-collapse">
                <ul class="nav" id="side-menu">
                    <li class="nav-header">
                        <div class="dropdown profile-element">
                            <span><img alt="image" class="img-circle" src="Public/images/avatar.jpg" style="width:64px;" /></span>
                            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="clear">
									<span class="block m-t-xs"><strong class="font-bold">管理员:<?php echo ($username); ?></strong><b class="caret"></b></span>
                                </span>
                            </a>
                            <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                <!--li><a class="J_menuItem" href="user/head.html">修改头像</a></li-->
                                <li><a id="user_info" class="J_menuItem" href="Admin/Admin/edit?id=<?php echo session('admin_id');?>">个人设置</a></li>
                                <li class="divider"></li>
                                <li><a href="javascript:void(0);" onclick="j.logout();">安全退出</a>
                                </li>
                            </ul>
						</div>
                        <div class="logo-element glyphicon glyphicon-th-large"></div>
                    </li>
					
					<?php if(is_array($menus)): foreach($menus as $k=>$menu): ?><li>
                        <a><i class="<?php echo ($menu["ico"]); ?>"></i> <span class="nav-label"><?php echo ($menu["label"]); ?> </span><span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
							<?php if(is_array($menu["children"])): foreach($menu["children"] as $key=>$child): ?><li><a id="<?php echo ($child["priviledge"]); ?>" class="J_menuItem" href="<?php echo ($child["action"]); ?>"><?php echo ($child["label"]); ?></a></li><?php endforeach; endif; ?>
                        </ul>
                    </li><?php endforeach; endif; ?>
					
                   
                    <li class="hide">
                        <a id="help" class="J_menuItem" href="user/help.html?id=1"><span class="nav-label">帮助中心</span></a>
                    </li>


                </ul>
            </div>
        </nav>
        <!--左侧导航结束-->
        <!--右侧部分开始-->
        <div id="page-wrapper" class="gray-bg dashbard-1">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header"><a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="javascript:void(0);"><i class="glyphicon glyphicon-th-list"></i> </a></div>
                    <ul class="nav navbar-top-links navbar-right">
                        <li class="dropdown">
	
						</li>
 						<li class="dropdown">
							<a class="right-sidebar-toggle" href="index.html" target="_blank">
								<i class="fa fa-home"></i> 返回首页
							</a>
						</li>
                   </ul>
                </nav>
            </div>
            <div class="row content-tabs">
                <button class="roll-nav roll-left J_tabLeft"><i class="fa fa-backward"></i>
                </button>
                <nav class="page-tabs J_menuTabs">
                    <div class="page-tabs-content">
                        <a href="javascript:;" class="active J_menuTab" data-id="admin/index/welcome.html">欢迎使用</a>
                    </div>
                </nav>
                <button class="roll-nav roll-right J_tabRight"><i class="fa fa-forward"></i>
                </button>
                <div class="btn-group roll-nav roll-right">
                    <button class="dropdown J_tabClose" data-toggle="dropdown">相关操作<span class="caret"></span>

                    </button>
                    <ul role="menu" class="dropdown-menu dropdown-menu-right">
                        <li><a href="admin/index/clearTj">清空所有统计数据</a></li>
                        <li><a onclick="$('#content-main>iframe:visible').attr('src',$('#content-main>iframe:visible').attr('src'))">刷新当前窗口</a></li>
                        <li class="J_tabShowActive"><a>定位当前选项卡</a></li>
                        <li class="divider"></li>
                        <li class="J_tabCloseAll"><a>关闭全部选项卡</a></li>
                        <li class="J_tabCloseOther"><a>关闭其他选项卡</a></li>
                    </ul>
                </div>
                <a href="javascript:jetee.logout();" class="roll-nav roll-right J_tabExit"><i class="fa fa fa-sign-out"></i> 退出</a>
            </div>
            <div class="row J_mainContent" id="content-main">
                <iframe class="J_iframe" name="iframe0" width="100%" height="100%" src="admin/index/welcome.html" frameborder="0" data-id="admin/index/welcome.html" seamless></iframe>
            </div>
            <div class="footer">
                <div class="pull-right"><?php echo C('copyright');?></div>
            </div>
        </div>
        <!--右侧部分结束-->
		
    </div>

<div class="css3-spinner" id="css3-spinner">
	<div class="css3-spinner-bounce1"></div>
	<div class="css3-spinner-bounce2"></div>
	<div class="css3-spinner-bounce3"></div>
</div>
<?php echo js_creat_cdn('jquery,bootstrap,layui,metisMenu,slimscroll,jquery.cookie');?>
<script>
if(typeof(layui)!='undefined')
layui.use('layer', function(layer){setTimeout(function(){
	layer.tips(' ', '#css3-spinner', {
		tips: [1, '#3595CC'],
		time: 1
	});
},500);});
</script>
<script src="__ROOT__/Public/h+/js/hplus.js"></script>
<script src="__ROOT__/Public/h+/js/contabs.js"></script>
<script src="__ROOT__/Public/admin/admin.js?ver=2"></script>
</body></html>
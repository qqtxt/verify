<?php
defined('JETEE_PATH') or exit();
//exc d m
class IndexAction extends AdministrationAction{
    public function index(){
		$admin_id=session('admin_id');
		//菜单
		$menus=array(
				array('label'=>'公众平台管理','children'=>array(
						array('action'=>"Admin/Wechat/edit",'label'=>'公众号设置','priviledge'=>'Wechat_edit'),
						array('action'=>"Admin/Wechat_menu/lists",'label'=>'微信菜单','priviledge'=>'Wechat_menu_lists'),
						array('action'=>"Admin/Wechat_subscribe/lists",'label'=>'粉丝管理','priviledge'=>'Wechat_subscribe_lists'),
						array('action'=>"Admin/Wechat_reply/subscribe",'label'=>'自动回复','priviledge'=>'Wechat_reply_subscribe'),
						array('action'=>"Admin/Wechat_media/article",'label'=>'素材管理','priviledge'=>'Wechat_media_article'),
						array('action'=>"Admin/Wechat_template/message",'label'=>'模板信息','priviledge'=>'Wechat_template_message'),
						array('action'=>"Admin/Wechat_qrcode/lists",'label'=>'扫码引荐','priviledge'=>'Wechat_qrcode_lists'),
						array('action'=>"Admin/Wechat_extend/index",'label'=>'功能扩展','priviledge'=>'Wechat_extend'),
						array('action'=>"Admin/Wechat_remind/lists",'label'=>'提醒设置','priviledge'=>'Wechat_remind_lists'),
						array('action'=>"Admin/Wechat_customer/service",'label'=>'多客服设置','priviledge'=>'Wechat_customer_service'),
					),'ico'=>'glyphicon glyphicon-credit-card'
				),
				array('label'=>'文章管理','children'=>array(
						array('action'=>"Admin/Article/lists",'label'=>'文章列表','priviledge'=>'Article_lists'),
						array('action'=>"Admin/Article_cat/lists",'label'=>'文章分类','priviledge'=>'Article_cat_lists'),															
					),'ico'=>'glyphicon glyphicon-info-sign'
				),
				array('label'=>'系统用户','children'=>array(
						array('action'=>"Admin/Admin/lists",'label'=>'管理员列表','priviledge'=>'Admin_lists'),
						array('action'=>"Admin/Admin/add",'label'=>'增加管理员','priviledge'=>'Admin_add'),
						array('action'=>"Admin/Role/lists",'label'=>'角色管理','priviledge'=>'Role_lists'),
						array('action'=>'Admin/Admin_log/lists','label'=>'管理日志','priviledge'=>'Admin_log_lists'),
					),'ico'=>'glyphicon glyphicon-minus-sign'
				),
				array('label'=>'系统设置','children'=>array(
						array('action'=>"Admin/Config/basic",'label'=>'基本设置','priviledge'=>'Config_basic'),
					),'ico'=>'glyphicon glyphicon-paperclip'
				),
		);
		//检查权限  删除没有权限的菜单项
		foreach($menus as $k=>$v){
			foreach($v['children'] as $kk=>$vv){
				if(!$this->check_authz($vv['priviledge'])){
					unset($menus[$k]['children'][$kk]);
				}
			}
			if(count($menus[$k]['children'])==0){//上级菜单
				unset($menus[$k]);
			}
		}
		$this->assign('username',$this->admin['username']);
		$this->assign('menus',$menus);
		$this->display();
    }
	public function login() {
		if($this->admin){
			$this->redirect('Index/index');
		}
		if(IS_POST){
			$username = 	i('post.username','','trim');
			$password =  	i('post.password','','trim');
			$verify = 		i('post.verify','','trim');
			$d=E('Admin');
			$admin=$d->getByUserName($username);
			if(!$admin){
				$this->location('',3,'用户名或密码错误！');
			}
			if(!$d->checkPassword($password,$admin)){
				$this->location('',3,'用户名或密码错误！');
			}
			if(!$d->check_verify($verify)){$this->location('',3,'验证码错误');}
			$d->setLogin($admin);
			$this->redirect('Index/index');
		} else {
			$this->display();
		}
	}

	public function welcome(){
		$this->assign('ur_here',     '欢迎使用');		
		$mysql_ver = db('admin')->getField('VERSION()');   // 获得 MySQL 版本
		$gd_info = @gd_info();
		/* 系统信息 */
		$sys_info['version']       = 20180901;
		$sys_info['os']            = PHP_OS;
		$sys_info['ip']            = $_SERVER['SERVER_ADDR'];
		$sys_info['web_server']    = $_SERVER['SERVER_SOFTWARE'];
		$sys_info['php_ver']       = PHP_VERSION;
		$sys_info['mysql_ver']     = $mysql_ver;
		$sys_info['zlib']          = function_exists('gzclose') ?  '是':'否';
		$sys_info['safe_mode']     = (boolean) ini_get('safe_mode') ?  '是':'否';
		$sys_info['safe_mode_gid'] = (boolean) ini_get('safe_mode_gid') ? '是':'否';
		$sys_info['timezone']      = function_exists("date_default_timezone_get") ? date_default_timezone_get() : '无设置';
		$sys_info['socket']        = function_exists('fsockopen') ? '是':'否';
		$sys_info['gd']        	   = function_exists('gd_info') ? $gd_info["GD Version"] :'否';
		$sys_info['curl']          = function_exists('curl_init') ?'是':'否';
		$sys_info['short_tag']     = get_cfg_var('short_open_tag') ? '是':'否';

		/* 允许上传的最大文件大小 */
		$sys_info['max_filesize'] = ini_get('upload_max_filesize');
		$this->assign('sys_info', $sys_info);
		$this->display();
	}
    /**
     * 用户退出
     */
    public function ajaxLogout(){
		//登陆过程
		E('Admin')->logout();
		echo ejson(array('status'=>true));
	}	
    /**
     * 用户退出
     */
    public function logout(){
		//登陆过程
		if($this->ajaxLogout())
			$this->location('admin',2,'已成功退出登陆！');
		$this->location('admin',2,'退出登陆失败，请重试！');
			
	}
    /**
     * 清空所有统计数据
     */
    public function clearTj(){
		db()->query('TRUNCATE TABLE je_visit');
		db()->query('TRUNCATE TABLE je_visit_s');
		db()->query('TRUNCATE TABLE je_visit_tmp');
		db()->query('TRUNCATE TABLE je_visit_u');
		db()->query('TRUNCATE TABLE je_visit_v');
		db()->query('TRUNCATE TABLE je_visit_z');
		db()->query('TRUNCATE TABLE je_visit_sd');
		db()->query('TRUNCATE TABLE je_visit_w');		
		$link['返回管理中心']='Admin/Admin';
		show_message('清理完成', $link, 0);			
	}
}
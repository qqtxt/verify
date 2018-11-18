<?php
defined('JETEE_PATH') or exit();
//exc d m
class IndexAction extends AdministrationAction{
    public function index(){
		$admin_id=session('admin_id');
		//菜单
		$menus=array(
				array('label'=>'控制面板','children'=>array(
						array('action'=>"Admin/Visit/lists",'label'=>'实时数据','priviledge'=>'Visit_lists'),
						array('action'=>"Admin/Weixin/lists",'label'=>'微信列表','priviledge'=>'Weixin_lists'),
						array('action'=>"Admin/Account/lists",'label'=>'帐户列表','priviledge'=>'Account_lists'),
						array('action'=>"Admin/Domain/lists",'label'=>'域名列表','priviledge'=>'Domain_lists'),
					),'ico'=>'glyphicon glyphicon-credit-card'
				),
				array('label'=>'统计报表','children'=>array(
						array('action'=>"Admin/Visit_v/lists",'label'=>'实时访客','priviledge'=>'Visit_v_lists'),
						array('action'=>"Admin/Visit_z/lists",'label'=>'实时转化','priviledge'=>'Visit_z_lists'),
						array('action'=>"Admin/Visit_sd/lists",'label'=>'时段统计','priviledge'=>'Visit_sd_lists'),
						array('action'=>"Admin/Visit_w/lists",'label'=>'时段统计(微信)','priviledge'=>'Visit_w_lists'),
						array('action'=>"Admin/Weixin_fen/lists",'label'=>'报粉','priviledge'=>'Weixin_fen_lists'),
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
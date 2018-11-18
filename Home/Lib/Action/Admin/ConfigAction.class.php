<?php
defined('JETEE_PATH') or exit();
//exc d m
class ConfigAction extends AdministrationAction{
	public function __construct() {
        parent::__construct();
		$this->d=E('Config');
	}
	public function basic()
	{
		if(IS_POST){
			$this->save_basic();exit;
		}
		
		$price_format_option=array('四捨五入保留两位小数','不四舍五入，保留一位小数','不四舍五入，不保留小数','先四舍五入，保留一位小数','先四舍五入，不保留小数');
		$upload_size_option=array(0=>'服务器默认设置',0=>'0KB',64*1024=>'64KB',128*1024=>'128KB',512*1024=>'512KB',1024*1024=>'1MB',2*1024*1024=>'2MB',4*1024*1024=>'4MB',);

		/* 模板赋值 */
		$this->assign('ur_here',     '编辑基本设置');		
		$this->assign( 'option_toggle', array( 0 => '关闭', 1 => '开启') ); //开关选项
		$this->assign( 'config', $this->d->get_basic());
		$this->assign( 'price_format_option',$price_format_option );
		$this->assign( 'upload_size_option',$upload_size_option );
		$this->display();
	}
	//保存基本设置
	public function save_basic(){
		$msg='编辑基本设置成功!';
		//上传logo  查询等待
		if (chk_files($_FILES['logo'])){
			import('ORG.Net.UploadFile');
			$upload = new UploadFile();
			$upload->maxSize  = c('upload_size_limit');
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');
			$upload->savePath = UPLOADS_PATH;
			if(!$upload->upload()) {// 上传错误提示错误信息
				$msg=$upload->getErrorMsg().'，请重新上传logo图片。';
			}else{// 上传成功 获取上传文件信息
				$info =  $upload->getUploadFileInfo();		
				$_REQUEST['config']['logo'] =$info[0]['savename'];				
			}			
			if(isset($_REQUEST['config']['logo'])){				
				@unlink(UPLOADS_PATH.c('logo'));//删除以前的
			}else{
				$_REQUEST['config']['logo']=c('logo');
			}		
		}		
		$configs=&$_REQUEST['config'];		
		$this->d->set_configs($configs);

		
		admin_log('基本设置', 'edit', 'config');
		S('common_config',null);
		common_config();
		show_message($msg, array('返回编辑基本设置'=>'Admin/Config/basic'));
	}
}
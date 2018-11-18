<?php
defined('JETEE_PATH') or exit();
/**
* exc d m
* @version 0.0.1 9:41 2015/1/19
*/
class Admin_logAction extends AdministrationAction{
	public function __construct() {
        parent::__construct();
		$this->d=E(MODULE_NAME);
		$this->exc=exchange(strtolower(MODULE_NAME),'log_id','log_info');
	}
	public function del(){
		if(IS_AJAX){
			$id = intval($_REQUEST['id']);
			if ( $this->exc->drop($id) ){			
				admin_log('ID:'.$id, 'remove', MODULE_NAME);
			}		
			$url = U(MODULE_NAME.'/lists?is_ajax=1&lastfilter=1');
			header("Location: $url\n");
			exit;
		}
		elseif(IS_POST){
			$lnk = array('返回上一页'=>'Admin/'.MODULE_NAME.'/lists/lastfilter/1');
			$checkboxes=i('checkboxes','','intval',1);
			if (!empty($checkboxes) && $this->exc->drop($checkboxes)){
				admin_log('批量删除ID '.implode(',',$checkboxes),'remove',MODULE_NAME);
				show_message('操作成功！', $lnk);
			}
			else{
				show_message('操作失败!', array(),1);
			}
		
		}elseif(IS_GET){
			$log_date=i('log_date',0,'intval');
			if(empty($log_date)){
				show_message('操作失败!', array(),1);
			}
			$time=0;
			switch($log_date){
				case 1: $time=strtotime("-1 week");break;
				case 2: $time=strtotime('-1 month');break;
				case 3: $time=strtotime('-3 month');break;
				case 4: $time=strtotime('-6 month');break;
				case 5: $time=strtotime('-1 year');break;
			}
			$w=array('log_time<?'=>$time);
			if (!empty($time) && db('admin_log')->where($w)->drop()){
				admin_log('清除'.format_time($time).'之前日志','remove',MODULE_NAME);
				show_message('操作成功！', $lnk);
			}
			else{
				show_message('未清除任何数据!', array(),1);
			}
		}
		
	}
}
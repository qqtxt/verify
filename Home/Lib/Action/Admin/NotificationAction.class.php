<?php
defined('JETEE_PATH') or exit();

/**
*通知 exc d m
* @link http://www.jetee.cn/
* @author jetee				
* @version 0.0.1 8:59 2015/1/22
*/
class NotificationAction extends AdministrationAction{
	public function __construct() {
        parent::__construct();
		$this->d=E(MODULE_NAME);
		$this->exc=exchange(strtolower(MODULE_NAME),'nid','note');
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
			$lnk[] = array('text' => "返回上一页", 'href'=>'Admin/'.MODULE_NAME.'/lists/lastfilter/1');
			$checkboxes=I('checkboxes',array(),'intval',1);
			if (!empty($checkboxes) && $this->exc->drop($checkboxes)){
				admin_log('批量删除ID '.implode(',',$checkboxes),'remove',MODULE_NAME);
				$this->sys_msg("批量删除成功！", 0, $lnk);
			}		
			else $this->sys_msg('批量删除失败,请重试！', 0, $lnk);
		}
	}
}
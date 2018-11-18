<?php
//exc d m
defined('JETEE_PATH') or exit();
class FeedbackAction extends AdministrationAction{
	public function __construct() {
        parent::__construct();
	}

	public function add() {
       $data['uid']=I('uid',0,'intval');
       $data['msg']=I('msg','');
	   $data['admin_id']=session('admin_id');
	   $data['add_time']=NOW_TIME;
	   if(!$data['msg'] || !$data['uid']){
			$msg=array('status'=>false,'data'=>'消息为空');
		}elseif(!($id=db('feedback')->add($data))){
			$msg=array('status'=>false,'data'=>'发送失败,请重试');
		}else{
			db('feedback')->where(array('uid'=>$data['uid'],'admin_id'=>0,'is_new'=>1))->save(array('is_new'=>0));
			admin_log('回复'.$id,'add',MODULE_NAME);
			$msg=array('status'=>true,'data'=>'回复成功');
		}
	   echo json_encode($msg);
	}

}
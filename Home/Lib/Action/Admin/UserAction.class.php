<?php
defined('JETEE_PATH') or exit();

class UserAction extends AdministrationAction{
	public function __construct() {
        parent::__construct();
		$this->d=E(MODULE_NAME);
		$this->exc=exchange(strtolower(MODULE_NAME),'uid','phone');
		define('USER_TYPE',0);
	}
	public function edit(){
		if(IS_AJAX){
			$id  = i('id',0,'intval');
			$val = i('val','','trim,jeHtmlspecialchars');
			switch($_REQUEST['act']){
				case 'password':
					$data['uid']=$id;
					$data['salt']=substr(sha1(NOW_TIME),0,6);
					$data['password']=$this->d->getPassword($_POST['val'],$data['salt']);
					if(!empty($id) && $this->d->updateUser($data)){
						admin_log('UID:'.$id.' 密码', 'edit', MODULE_NAME);
						make_json_result($val);
					}
					break;
				case 'status':
					$val = intval($val);
					if(!empty($id) && $this->d->updateUser(array('status'=>$val,'uid'=>$id))){
						admin_log('审核ID:'.$id.' 状态:'.$val, 'edit', MODULE_NAME);
						make_json_result($val);
					}
					break;
				case 'phone':
					if(!empty($id) && !empty($val) && is_phone($val) && !$this->d->getByPhone($val,0) && $this->d->updateUser(array('phone'=>$val,'uid'=>$id))){
						admin_log('ID:'.$id.' 手机:'.$val, 'edit', 'user');
						make_json_result($val);
					}else{
						make_json_error('手机输入不正确,或手机已经注册过！');
					}
					break;
				case 'weixin':
					if(!empty($id) && !empty($val) && $this->d->updateUser(array('weixin'=>$val,'uid'=>$id))){
						admin_log('ID:'.$id.' weixin:'.$val, 'edit', 'user');
						make_json_result($val);
					}
					break;
				case 'qq':
					if(!empty($id) && !empty($val) && is_qq($val) && $this->d->updateUser(array('qq'=>$val,'uid'=>$id))){
						admin_log('ID:'.$id.' QQ:'.$val, 'edit', 'user');
						make_json_result($val);
					}else{
						make_json_error('QQ输入不正确！');
					}
					break;
				case 'edit':
					$this->assign('user',db('user')->where('uid='$id)->getRow());
					make_json_echo($this->fetch('edit'),"编辑用户",array('padding'=>0,'zIndex'=>1));
					break;
			}
			make_json_error('操作失败，请重试！');
		}
		else{
			//批量锁定
			if($_REQUEST['act']=='batch_status'){
				$lnk[] = array('text' => "返回上一页", 'href'=>'Admin/User/lists/lastfilter/1');
				$in_id=db_create_in($_REQUEST['checkboxes']);
				if (!empty($_REQUEST['checkboxes']) && 
					$this->d->where('uid '. $in_id)->save(array('status'=>1))
				){
					admin_log('批量锁定ID '.$in_id,'edit','user');
					$this->sys_msg("批量锁定成功！", 0, $lnk);
				}
				else{
					$this->sys_msg('批量锁定失败,请重试！', 0, $lnk);
				}

			}
			elseif($_REQUEST['act']=='doEdit'){ 
				$lnk[] = array('text' => "返回上一页", 'href'=>'Admin/User/lists/lastfilter/1');
				$data['uid']=I('post.uid',0,'intval');
				$data['full_name']=I('post.full_name','');
				$data['sex']=I('post.sex',0,'intval');
				$data['type']=I('post.type',0,'intval');
				$data['position']=I('post.position',0,'intval');
				$data['focus1']=I('post.focus1',0,'intval');
				$data['focus2']=I('post.focus2',0,'intval');
				$data['focus3']=I('post.focus3',0,'intval');
				$data['security']=I('post.security',0,'intval');
				$data['department']=I('post.department','');
				$data['annual_turnover']=I('post.annual_turnover',0,'intval');
				$data['net_asset']=I('post.net_asset',0,'intval');
				$data['company_addr']=I('post.company_addr',0);
				$data['company_site']=I('post.company_site',0);
				if (M('user')->data($data)->save()!==false){
					admin_log('用户ID：'.$data['uid'],'edit','user');
					$this->sys_msg("修改成功！", 0, $lnk);
				}
				else{
					$this->sys_msg("修改失败！", 0, $lnk);
				}
			
					
			}
		}
	}
}
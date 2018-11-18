<?php
//exc d m
defined('JETEE_PATH') or exit();
class NoticeAction extends AdministrationAction{	
	public function __construct() {
        parent::__construct();
		$this->add=true;
		$this->d=E(MODULE_NAME);
		$this->exc=exchange(strtolower(MODULE_NAME),'notice_id','title');
	}	
	public function add(){
		if(isset($_POST['title'])){
			$this->insert();exit;
		}
		$notice['status']=1;
		$this->assign('notice',$notice);
		$content=$this->fetch('info');
		make_json_echo($content,"添加公告",array('padding'=>0,'zIndex'=>1));		
	}
	//-- 添加公告
	public function insert(){
		$data=array();
		$data['title']=i('title','');
		$data['cat_id']=i('cat_id',0,'intval');
		$data['status']=i('status',0,'intval');
		$data['keywords']=i('keywords','');
		$data['description']=i('description','');
		$data['content']=i('content','','trim');
		#$data['content']=strip_tags($data['content'],c('ALLOWABLE_TAGS'));
		$data['content']=$data['content'];
		$data['author']=$this->admin['username'];
		$data['add_time']=NOW_TIME;
		if(empty($data['title'])){
			$this->sys_msg('公告标题不能为空！', 1);
		}
		elseif(db('notice')->add($data)){
			$link[0]['text'] = '返回公告列表';
			$link[0]['href'] = 'Admin/Notice/lists';
			admin_log($data['title'],'add','notice');
			$this->sys_msg('公告已经添加成功',0, $link);			
		}
	}
	//-- 更新公告
	public function update(){
		$data=array();
		$id=i('id',0,'intval');
		$data['title']=i('title','');
		$data['cat_id']=i('cat_id',0,'intval');
		$data['status']=i('status',0,'intval');
		$data['keywords']=i('keywords','');
		$data['description']=i('description','');
		$data['content']=i('content','','trim');
		//$data['content']=strip_tags($data['content'],c('ALLOWABLE_TAGS'));
		if(empty($data['title'])){
			$this->sys_msg('公告标题不能为空！', 1);
		}
		elseif(db('notice')->where('notice_id='$id)->save($data)){
			$link[0]['text'] = '返回公告列表';
			$link[0]['href'] = 'Admin/Notice/lists/lastfilter/1';
			admin_log($data['title'],'edit','notice');
			$this->sys_msg('公告编辑成功',0, $link);			
		}
		$this->sys_msg('公告编辑失败',1, $link);
	}
	public function edit(){
		if(IS_AJAX && isset($_REQUEST['act'])){
			$id  = i('id',0,'intval');
			$val = i('val','','trim,jeHtmlspecialchars');
			$error='';
			switch($_REQUEST['act']){
				//修改标题
				case 'title':
					if(!empty($id) && !empty($val) && $this->exc->edit(array('title'=>$val), $id)){
						admin_log('ID:'.$id.' 编辑标题:'.$val, 'edit', MODULE_NAME);
						make_json_result($val);
					}
					break;
				//修改作者
				case 'author':
					if(!empty($id) && !empty($val) && $this->exc->edit(array('author'=>$val), $id)){
						admin_log('ID:'.$id.' 编辑作者:'.$val, 'edit', MODULE_NAME);
						make_json_result($val);
					}
					break;
				//修改关键字 
				case 'keywords':
					if(!empty($id) && $this->exc->edit(array('keywords'=>$val), $id)){
						admin_log('ID:'.$id.' 编辑关键字:'.$val, 'edit', MODULE_NAME);
						make_json_result($val);
					}
					break;
				//修改描述
				case 'description':
					if(!empty($id) && $this->exc->edit(array('description'=>$val), $id)){
						admin_log('ID:'.$id.' 编辑描述:'.$val, 'edit', MODULE_NAME);
						make_json_result($val);
					}
					break;
				//修改排序
				case 'sort_order':
					$val=intval($val);
					if(!empty($id) && $this->exc->edit(array('sort_order'=>$val), $id)){
						admin_log('ID:'.$id.' 编辑排序:'.$val, 'edit', MODULE_NAME);
						make_json_result($val);
					}
					break;
				//修改状态
				case 'status':
					$val=intval($val);
					if(!empty($id) && $this->exc->edit(array('status'=>$val), $id)){
						admin_log('ID:'.$id.' 编辑状态:'.$val, 'edit', MODULE_NAME);
						make_json_result($val);
					}
					break;
				//修改分类
				case 'cat_id':
					if($val=='电脑端'){
						$val=1;
					}elseif($val=='手机端'){
						$val=2;
					}else{
						$val=0;
					}
					if(!empty($id) && $this->exc->edit(array('cat_id'=>$val), $id)!==false){
						admin_log('ID:'.$id.' 编辑分类:'.$val, 'edit', MODULE_NAME);
						make_json_result(L('notice_type.'.$val));
					}
					break;
				//显示修改公告
				case 'showEdit':
					if (!empty($id) && ($row=db('notice')->where('notice_id='$id)->getRow()) ){
						$this->assign('notice',$row);
						make_json_echo($this->fetch('info'),"编辑公告",array('padding'=>0,'zIndex'=>1));
					}
					else{
						make_json_echo('要编辑公告已经不存在',"系统信息",array('showModal'=>1));
					}
					break;
			}
			make_json_error($error.'修改失败，请重试！');
		}
		//批量审核
		elseif($_REQUEST['act']=='batch_check'){
			$lnk[] = array('text' => "返回上一页", 'href'=>'Admin/'.MODULE_NAME.'/lists/lastfilter/1');
			$checkboxes=I('checkboxes',array(),'intval',1);
			if (!empty($checkboxes) && $this->exc->edit(array('status'=>1),$checkboxes)	){
				admin_log('批量启用ID '.implode(',',$checkboxes),'edit',MODULE_NAME);
				$this->sys_msg("批量启用成功！", 0, $lnk);
			}				
			else $this->sys_msg('批量启用失败,请重试！', 0, $lnk);				
		}

		//更新公告
		elseif(isset($_POST['title'])){
			$this->update();
		}
		
	}	

}
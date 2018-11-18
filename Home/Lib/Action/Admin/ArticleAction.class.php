<?php
//exc d m
defined('JETEE_PATH') or exit();
class ArticleAction extends AdministrationAction{	
	public function __construct() {
        parent::__construct();
		$this->add=true;
		$this->d=E(MODULE_NAME);
		$this->exc=exchange(strtolower(MODULE_NAME),'article_id','title');
	}	
	public function add(){
		if(isset($_POST['title'])){
			$this->insert();return;
		}	
		$this->display('info');//($content,"添加文章",array('padding'=>0,'zIndex'=>1));		
	}
	//-- 添加文章
	public function insert(){
		$data=array();
		$data['title']=i('title','');
		$data['cat_id']=i('article_cat',0,'intval');
		$data['status']=i('status',0,'intval');
		$data['keywords']=i('keywords','');
		$data['description']=i('description','');
		$data['content']=i('content','','trim');
		#$data['content']=strip_tags($data['content'],c('ALLOWABLE_TAGS'));
		$data['content']=$data['content'];
		$data['author']=$this->admin['username'];
		$data['add_time']=NOW_TIME;
		if(empty($data['title']) || empty($data['cat_id'])){
			$msg=array('status'=>false,'data'=>'文章标题及分类不能为空！');
		}elseif(db('article')->add($data)){
			admin_log($data['title'],'add','article');
			$msg=array('status'=>true,'data'=>'操作成功');
		}
		echo ejson($msg);
	}
	//-- 更新文章
	public function update(){
		$data=array();
		$id=i('id',0,'intval');
		$data['title']=i('title','');
		$data['cat_id']=i('article_cat',0,'intval');
		$data['status']=i('status',0,'intval');
		$data['keywords']=i('keywords','');
		$data['description']=i('description','');
		$data['content']=i('content','','trim');
		//$data['content']=strip_tags($data['content'],c('ALLOWABLE_TAGS'));
		$msg=array('status'=>false,'data'=>'未作修改');
		if(empty($data['title']) || empty($data['cat_id'])){
			$msg=array('status'=>false,'data'=>'文章标题及分类不能为空！');
		}elseif(db('article')->where('article_id='.$id)->save($data)){
			admin_log($data['title'],'edit','article');
			$msg=array('status'=>true,'data'=>'操作成功');
		}
		echo ejson($msg);
	}
	public function edit(){
		if(IS_AJAX || isset($_REQUEST['act']) && $_REQUEST['act']=='showEdit'){
			$id  = i('id',0,'intval');
			$val = i('val','','trim,jeHtmlspecialchars');
			$error='';
			$result=NULL;
			switch($_REQUEST['act']){
				//修改标题
				case 'title':
					if(!empty($id) && !empty($val) && $this->exc->edit(array('title'=>$val), $id)){
						admin_log('ID:'.$id.' 编辑标题:'.$val, 'edit', MODULE_NAME);
						$result=$val;
					}
					break;
				//修改作者
				case 'author':
					if(!empty($id) && !empty($val) && $this->exc->edit(array('author'=>$val), $id)){
						admin_log('ID:'.$id.' 编辑作者:'.$val, 'edit', MODULE_NAME);
						$result=$val;
					}
					break;
				//修改关键字 
				case 'keywords':
					if(!empty($id) && $this->exc->edit(array('keywords'=>$val), $id)){
						admin_log('ID:'.$id.' 编辑关键字:'.$val, 'edit', MODULE_NAME);
						$result=$val;
					}
					break;
				//修改描述
				case 'description':
					if(!empty($id) && $this->exc->edit(array('description'=>$val), $id)){
						admin_log('ID:'.$id.' 编辑描述:'.$val, 'edit', MODULE_NAME);
						$result=$val;
					}
					break;
				//修改排序
				case 'sort_order':
					$val=intval($val);
					if(!empty($id) && $this->exc->edit(array('sort_order'=>$val), $id)){
						admin_log('ID:'.$id.' 编辑排序:'.$val, 'edit', MODULE_NAME);
						$result=$val;
					}
					break;
				//修改状态
				case 'status':
					$val=intval($val);
					if(!empty($id) && $this->exc->edit(array('status'=>$val), $id)){
						admin_log('ID:'.$id.' 编辑状态:'.$val, 'edit', MODULE_NAME);
						$result=$val;
					}
					break;
				//修改分类
				case 'cat_name':
					$cat_id=m('article_cat')->where(array('cat_name'=>$val))->getField('cat_id');				
					if($cat_id && $this->exc->edit(array('cat_id'=>$cat_id), $id)){
						admin_log('ID:'.$id.' 编辑分类:'.$val, 'edit', MODULE_NAME);
						$result=$val;
					}
					$error='分类不存在，';
					break;
				//显示修改文章
				case 'showEdit':
					if (!empty($id) && ($row=db('article')->where('article_id='.$id)->getRow()) ){
						$this->assign('article',$row);
						$this->assign('ur_here','修改文章');
						$this->display('info');
						return;
					}
					else{
						show_message('要编辑文章已经不存在', null, 1,  false);
					}
					break;
			}
			if($result!==NULL){
				echo ejson(array('status'=>true,'data'=>$result));
			}elseif(isset($_POST['title'])){//更新文章
				$this->update();
			}else{	
				echo ejson(array('status'=>false,'data'=>$error.'操作失败，请重试！'));
			}
		}
		//批量审核
		elseif($_REQUEST['act']=='batch_check'){
			$checkboxes=I('checkboxes',array(),'intval',1);
			if (!empty($checkboxes) && $this->exc->edit(array('status'=>1),$checkboxes)	){
				admin_log('批量审核ID '.implode(',',$checkboxes),'edit',MODULE_NAME);
				$lnk = array('返回上一页' =>'Admin/'.MODULE_NAME.'/lists/lastfilter/1');
				show_message('操作成功',$lnk,0,false);
			}
			show_message('操作失败', array(),1);				
		}
		//批量转移分类
		elseif($_REQUEST['act']=='batchMoveTo'){
			$checkboxes=I('checkboxes',array(),'intval',1);
			$cat_id=i('cat_id',0,'intval');
			if (!empty($checkboxes) && !empty($cat_id) && $this->exc->edit(array('cat_id'=>$cat_id),$checkboxes)){
				admin_log('批量转移分类 文章ID：'.implode(',',$checkboxes)." 分类ID：$cat_id",'edit',MODULE_NAME);
				$lnk = array('返回上一页' =>'Admin/'.MODULE_NAME.'/lists/lastfilter/1');
				show_message('操作成功',$lnk,0,false);
			}				
			show_message('操作失败', array(),1);
		}
		
		
	}	

}
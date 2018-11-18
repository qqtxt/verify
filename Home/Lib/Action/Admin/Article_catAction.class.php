<?php
defined('JETEE_PATH') or exit();
//exc d m
class Article_catAction extends AdministrationAction{
	public function __construct() {
        parent::__construct();
		$this->d=E(MODULE_NAME);
		$this->exc=exchange(strtolower(MODULE_NAME),'cat_id','cat_name');
	}
	//分类列表
	public function lists(){
		$articlecat = $this->d->article_cat_list(0, 0, false);
		$this->assign('lists',        $articlecat);
		if(IS_AJAX){			
			echo ejson(array('status'=>true,'data'=>$this->fetch()));return;
		}		
		$this->assign('ur_here',     '分类列表');
		$this->assign('action_link', array('text' => '添加分类', 'href' => 'Admin/Article_cat/add'));
		$this->assign('full_page',   1);
		$this->display();
	}
	public function del(){
		$id = i('id',0,'intval');
		$result='操作失败';
		if (db('article_cat')->where(array('parent_id'=>$id))->count() > 0){/* 还有子分类，不能删除 */			
			$result='该分类下还有子分类';
		}elseif(db('article')->where(array('cat_id'=>$id))->count() > 0){/* 非空的分类不允许删除 */
			$result='分类下还有文章';
		}elseif(($name=$this->exc->get_name($id)) && $this->exc->drop($id)){
			//删除缓冲
			S('article_cat_list',null);
			admin_log("ID:{$id} 分类名{$name}", 'remove', 'article_cat');
			$url = U('Article_cat/lists?is_ajax=1');
			header("Location: $url\n");
			return;
		}
		echo ejson(array('status'=>false,'data'=>$result));
	}
	public function add(){
		if($id=$this->exc->add(array('cat_name'=>''))){
			admin_log('ID:'.$id, 'add', 'article_cat');
			//删除缓冲
			S('article_cat_list',null);
			$this->redirect('Article_cat/lists');			
		}
		else{
			show_message('操作失败', array(),1);
		}
	}
	public function edit(){
		if(IS_AJAX || isset($_REQUEST['act']) &&  $_REQUEST['act']=='moveTo'){
			$id  = i('id',0,'intval');
			$val = i('val','','trim,jeHtmlspecialchars');
			$error='';
			$result=NULL;
			switch($_REQUEST['act']){
				//修改分类名
				case 'cat_name':
					if(!empty($id) && !empty($val) && !$this->d->countCat_name($id,$val) && $this->exc->edit(array('cat_name'=>$val), $id)){
						admin_log('ID:'.$id.' 编辑分类名:'.$val, 'edit', MODULE_NAME);
						//删除分类缓冲
						S('article_cat_list',null);
						$result=$val;
					}else $error='分类名已存在或为空,';
					break;
				//修改关键字
				case 'keywords':
					if(!empty($id) && $this->exc->edit(array('keywords'=>$val), $id)){
						admin_log('ID:'.$id.' 编辑关键字:'.$val, 'edit', MODULE_NAME);
						//删除分类缓冲
						S('article_cat_list',null);
						$result=$val;
					}
					break;
				//修改描述
				case 'cat_desc':
					if(!empty($id) && $this->exc->edit(array('cat_desc'=>$val), $id)){
						admin_log('ID:'.$id.' 编辑描述:'.$val, 'edit', MODULE_NAME);
						//删除分类缓冲
						S('article_cat_list',null);
						$result=$val;
					}
					break;
				//修改排序
				case 'sort_order':
					$val=intval($val);
					if(!empty($id) && $this->exc->edit(array('sort_order'=>$val), $id)){
						admin_log('ID:'.$id.' 编辑排序:'.$val, 'edit', MODULE_NAME);
						//删除分类缓冲
						S('article_cat_list',null);
						$result=$val;
					}
					break;
				//转移分类
				case 'moveTo':
					if(empty($id) || !($row=db('article_cat')->where(array('cat_id'=>$id))->getRow('*'))){
						show_message('分类不存在', null, 1,  false);
					}else{
						$this->assign('ur_here','转移分类');
						$this->assign('row',$row);
						$this->assign('article_cat_list',$this->d->article_cat_list(0,$row['parent_id'],true));
						$this->display();
						return;
					}
					break;
			}
			if($result!==NULL){
				echo ejson(array('status'=>true,'data'=>$result));
			}else{
				echo ejson(array('status'=>false,'data'=>$error.'操作失败，请重试！'));
			}
		}
		elseif($_REQUEST['act']=='doMoveTo'){
			$id  = i('id',0,'intval');
			$parent_id  = i('parent_id',0,'intval');
			$lnk[] = array('text' => "返回上一页", 'href'=>'Admin/Article_cat/lists');
			/* 检查设定的分类的父分类是否合法 */
			$catid_array=array();
			$child_cat = $this->d->article_cat_list($id, 0, false);
			if(!empty($child_cat)){
				foreach ($child_cat as $child_data){
					$catid_array[] = $child_data['cat_id'];
				}
			}
			if(in_array($parent_id, $catid_array)){
				show_message('父分类不能设置成本身或本身的子分类', array(),1);
			}
			if(!empty($id) && $this->exc->edit(array('parent_id'=>$parent_id), $id)){
				admin_log('ID:'.$id.' 转移分类:'.$parent_id, 'edit', MODULE_NAME);
				//删除分类缓冲
				S('article_cat_list',null);
				echo "<script>parent.location.reload();</script>";
			}
			else{
				show_message('转移分类失败，请重试！', array(),1);
			}
		}
	}
	
}
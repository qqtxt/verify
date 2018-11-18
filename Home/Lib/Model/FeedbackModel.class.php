<?php
// db
defined('JETEE_PATH') or exit();
class FeedbackModel{
	//列表
	public function lists(){
		$result = get_filter();
		if ($result === false){
			$sql=array();$sql['where']=array();
			$keyword=i('keyword','','strip_tags,trim');
			$sort_by=i('sort_by','m.fid','strip_tags,trim');
			$sort_order=i('sort_order','desc','strip_tags,trim');
			if(isset($_REQUEST['new']) && $_REQUEST['new']!='99'){
				$filter['new']=i('new',1,'intval');
				$sql['where'][]=array('m.is_new',$filter['new']);
				$sql['where'][]=array('m.admin_id',0);
			}
			if(!empty($keyword)){
				$filter['keyword']=$keyword;
				$sql['where'][]=array('m.msg like ?','%'.db()->filterLike($keyword).'%');
			}
			$filter['sort_by']    =  $sort_by;
			$filter['sort_order'] =  $sort_order;				
			$filter['record_count'] = db('feedback m')->where($sql['where'])->count();			
			/* 分页大小 */
			$filter = page_and_size($filter);
			$sql['order']=$filter['sort_by'].' '.$filter['sort_order'];
			$sql['page']=$filter['page'];
			$sql['page_size']=$filter['page_size'];
			set_filter($filter, $sql);
		}else{
			$sql    = $result['sql'];
			$filter = $result['filter'];
		}
		$list =db('feedback m')->select('m.*,u.phone')->join('user u','u.uid=m.uid')->where($sql['where'])->order($sql['order'])->page($sql['page'],$sql['page_size'])->query();
		return array('list' => $list, 'filter' => $filter,'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
	}

    public function getNewCount(){
		return db('feedback')->where(array('uid'=>session('uid'),'is_new'=>1,'admin_id>?'=>0))->count();
		
	}
    public function getFeedback(){
		$return='';
		$feedback=db('feedback')->select('*')->where(array('uid'=>session('uid')))->order('fid desc')->limit(8)->query();
		while($row=array_pop($feedback)){
			$return.= $row['admin_id']>0 
			? '<div class="left"><div class="author-name">管理员 <small class="chat-date">'.date('Y-m-d H:i',$row['add_time']).'</small></div><div class="chat-message active">'.$row['msg'].'</div></div>' 
			: '<div class="right"><div class="author-name">我 <small class="chat-date">'.date('Y-m-d H:i',$row['add_time']).'</small></div><div class="chat-message">'.$row['msg'].'</div></div>';
		}
		return $return; 
	}
}

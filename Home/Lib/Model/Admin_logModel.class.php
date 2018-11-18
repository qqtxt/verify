<?php
defined('JETEE_PATH') or exit();

/**
*日志模型  db
* @version 0.0.1 9:41 2015/1/19
*/
class Admin_logModel{
	//列表
	public function lists(){
		$result = get_filter();
		if ($result === false){
			$sql=array();$sql['where']=array();
			$admin_id=i('admin_id','','intval');
			$keyword=i('keyword','','strip_tags,trim');
			$sort_by=i('sort_by','','strip_tags,trim');
			$sort_order=i('sort_order','','strip_tags,trim');
			if(isset($_REQUEST['admin_id']) && $admin_id>0){
				$filter['admin_id']=$admin_id;
				$sql['where'][]=array('l.admin_id',$admin_id);
			}
			/* 过滤条件 */
			if(!empty($keyword)){
				$filter['keyword']=$keyword;
				$sql['where'][]=array('(l.log_info like ? or l.ip_address=?)','%'.db()->filterLike($keyword).'%',$keyword);
			}
			$filter['sort_by']    = empty($sort_by)    ? 'l.log_id' 	: $sort_by;
			$filter['sort_order'] = empty($sort_order) ? 'DESC'     : $sort_order;				
			$filter['record_count'] = db('admin_log l')->where($sql['where'])->count();			
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
		$list =db('admin_log l')->select('l.*,a.username')->join('admin a','a.admin_id=l.admin_id')->where($sql['where'])->order($sql['order'])->page($sql['page'],$sql['page_size'])->query();
		return array('list' => $list, 'filter' => $filter,'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
	}
}

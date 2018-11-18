<?php
defined('JETEE_PATH') or exit();

/**
*事件 模型  db
* @link http://www.jetee.cn/
* @author jetee				
* @version 0.0.1 14:43 2018/9/20
*/
class EventModel{
	//列表
	public function lists(){
		$result = get_filter();
		if ($result === false){
			$sql=array();$sql['where']=array();
			$type=i('type','','intval');
			$keyword=i('keyword','','strip_tags,trim');
			$sort_by=i('sort_by','','strip_tags,trim');
			$sort_order=i('sort_order','','strip_tags,trim');
			if(isset($_REQUEST['type']) && $type>0){
				$filter['type']=$type;
				$sql['where'][]=array('d.type',$type);
			}
			/* 过滤条件 */
			if(!empty($keyword)){
				$filter['keyword']=$keyword;
				$sql['where'][]=array('(d.uid=? or d.id=? or d.ip=?)',$keyword,$keyword,$keyword);
			}
			$filter['sort_by']    = empty($sort_by)    ? 'd.eid' 	: $sort_by;
			$filter['sort_order'] = empty($sort_order) ? 'DESC'     : $sort_order;				
			$filter['record_count'] = db('event d')->where($sql['where'])->count();			
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
		$list =db('event d')->select('d.*,u.phone')->join('user u','u.uid=d.uid')->where($sql['where'])->order($sql['order'])->page($sql['page'],$sql['page_size'])->query();
		return array('list' => $list, 'filter' => $filter,'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
	}
}

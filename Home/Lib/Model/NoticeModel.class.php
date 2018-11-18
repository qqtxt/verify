<?php
defined('JETEE_PATH') or exit();
/** 		
*公告基础模型 db
* @version 0.0.1 18:48 2018/5/25
*/
class NoticeModel{
	public function lists(){
		$result = get_filter();
		if ($result === false){
			$sql=array();$sql['where']=array();
			$status=i('status',0,'intval');
			$cat_id=i('cat_id',0,'intval');
			$keyword=i('keyword','','strip_tags,trim');
			$sort_by=i('sort_by','','strip_tags,trim');
			$sort_order=i('sort_order','','strip_tags,trim');
			/* 过滤条件 */
			if(isset($_REQUEST['status']) && $_REQUEST['status']!='99'){
				$filter['status']=$status;
				$sql['where'][]=array('a.status',$status);
			}
			if(!empty($cat_id) && $cat_id!='99'){
				$filter['cat_id']=$cat_id;			
				$sql['where'][]=array('a.cat_id',$cat_id);
			}
			if(!empty($_REQUEST['keyword'])){
				$filter['keyword']=$keyword;
				$sql['where'][]=array('a.title like ?','%'.db()->filterLike($keyword).'%');
			}
			$filter['sort_by']    = empty($sort_by)    ? 'a.notice_id' 	: $sort_by;
			$filter['sort_order'] = empty($sort_order) ? 'DESC'     : $sort_order;				
			$filter['record_count'] = db('notice a')->where($sql['where'])->count();
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
		$list =db('notice a')->select('a.*')->where($sql['where'])->order($sql['order'])->page($sql['page'],$sql['page_size'])->query();
		return array('list' => $list, 'filter' => $filter,'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
	}

}
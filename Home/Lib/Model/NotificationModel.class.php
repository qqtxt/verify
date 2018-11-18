<?php
defined('JETEE_PATH') or exit();

/**
*通知模型   db
* @link http://www.jetee.cn/
* @author jetee				
* @version 0.0.1 9:19 2015/1/22
*/
class NotificationModel{
	//列表 2
	public function lists(){
		$result = get_filter();
		if ($result === false){
			$sql=array();$sql['where']=array();
			$new=i('new',0,'intval');
			$keyword=i('keyword','','strip_tags,trim');
			$sort_by=i('sort_by','l.nid','strip_tags,trim');
			$sort_order=i('sort_order','DESC','strip_tags,trim');
			if(isset($_REQUEST['new']) && $new!=99){
				$filter['new']=$new;
				$sql['where'][]=array('l.new',$new);
			}
			/* 过滤条件 */
			if(!empty($keyword)){
				$filter['keyword']=$keyword;
				$sql['where'][]=array('(l.note like ? or l.uid=?)','%'.db()->filterLike($keyword).'%',$keyword);
			}
			$filter['sort_by']    = $sort_by;
			$filter['sort_order'] = $sort_order;				
			$filter['record_count'] = db('notification l')->where($sql['where'])->count();			
			/* 分页大小 */
			$filter = page_and_size($filter);
			$sql['order']=$filter['sort_by'].' '.$filter['sort_order'];
			$sql['page']=$filter['page'];
			$sql['page_size']=$filter['page_size'];
			set_filter($filter, $sql);
		}
		else{
			$sql    = $result['sql'];
			$filter = $result['filter'];
		}
		$field=;
		$list =db('notification l')->select('l.*,u.nickname')->join('user u','u.uid=l.uid')->where($sql['where'])->order($sql['order'])->page($sql['page'],$sql['page_size'])->query();
		return array('list' => $list, 'filter' => $filter,'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
	}

	/**
	 * 保存通知
	 * @access  public 
	 * @return  str
	 */
	public function addOne($uid,$note,$type=0){
		return db('notification')->add(array('uid'=>$uid,'note'=>$note,'type'=>$type,'add_time'=>NOW_TIME));
	}
	/**
	*取列表
	* @param	int	$uid		该用户	
	* @return array 列表
	* @version 0.0.1 16:23 2015/4/11
	*/
	public function getNotificationList($uid){
		$page_size=30;
		$record_count=$this->where(array('uid'=>$uid))->count();
		$pager=get_pager(MODULE_NAME.'/notification.html',array(), $record_count, I('request.page',1,'intval'), $page_size,'page');
		$list=db('notification')->select('nid,uid,note,add_time')->where(array('uid'=>$uid))->order('nid desc')->page($pager['page'],$pager['size'])->query();
		return array('list'=>$list,'pager'=>$pager);
	}
	/**
	*取最新列表
	* @param	int	$uid		该用户	
	* @param	int	$num		条数
	* @return array 列表
	* @version 0.0.1 16:23 2015/4/11
	*/
	public function getNotice($uid,$num=5){
		return db('notification')->field('nid,uid,note,add_time')->where(array('uid'=>$uid))->order('nid desc')->limit($num)->query();
	}
	
}

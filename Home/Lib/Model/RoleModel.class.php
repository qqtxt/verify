<?php
defined('JETEE_PATH') or exit();

/** 		
*角色模型 db
* @version 0.0.1 11:00 2018/9/20
*/
class RoleModel{
	public function get_list(){	
		return db()->query('select * from '.C('DB_PREFIX').'role order by role_id desc');
	}
}
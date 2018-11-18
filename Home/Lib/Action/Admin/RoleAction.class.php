<?php
defined('JETEE_PATH') or exit();
//exc d m
class RoleAction extends AdministrationAction{
	public function __construct() {
        parent::__construct();
		$this->d=E('Role');
	}

	public function lists()
	{
		if(IS_AJAX){			
			$this->assign('role_list',  $this->d->get_list());
			echo ejson(array('status'=>true,'data'=>$this->fetch()));return;
		}
		
		$this->assign('ur_here',     '角色管理');
		$this->assign('action_link',  array('href'=>'Admin/Role/add', 'text' => '添加角色'));
		$this->assign('full_page',   1);
		$this->assign('role_list',  $this->d->get_list());
		$this->display();
	}

	public function add()
	{
		if(IS_POST){	
			$this->insert();return;
		}

		$priv_str = '';
		
		/* 获取权限的分组数据 */
		$res = db('privilege')->select('pid,up_id,name,comment')->where('up_id=0')->query();
		while(list($key,$rows)=each($res)){
			$priv_arr[$rows['pid']] = $rows;
		}
		/* 按权限组查询底级的权限名称 */
		$result = db('privilege')->select('pid,up_id,name,relevance,comment')->where("up_id " .db_create_in(array_keys($priv_arr)))->query(); 
		while(list($key,$priv)=each($result)){//分组存入同一父分类下
			$priv_arr[$priv["up_id"]]["priv"][$priv["name"]] = $priv;
		}
		
		// 将同一组的权限使用 "," 连接起来，供JS全选
		foreach ($priv_arr AS $pid => $action_group){
			$priv_arr[$pid]['priv_list'] = join(',', @array_keys($action_group['priv']));
			foreach ($action_group['priv'] AS $key => $val){
				$priv_arr[$pid]['priv'][$key]['cando'] = ($priv_str == 'all' || strpos(",$priv_str,", ",$val[name],") !== false) ? 1 : 0;
			}
		}

		 /* 模板赋值 */
		$this->assign('ur_here',     '添加角色');
		$this->assign('action_link', array('href'=>'Admin/Role/lists', 'text' => '角色列表'));
		$this->assign('priv_arr',    $priv_arr);
		$this->display();
	}

	public function insert()
	{
		$role_name=trim(strip_tags($_REQUEST['role_name']));
		$role_describe=trim(strip_tags($_REQUEST['role_describe']));
		$act_list = @join(",", $_REQUEST['action_code']);
		$data=array('role_name'=>$role_name,'action_list'=>$act_list,'role_describe'=>$role_describe);
		$new_id =db('role')->add($data);
		admin_log($role_name, 'add', 'role');
		
		show_message('操作成功', array('角色列表'=>'Admin/Role/lists'), 0);
	}
	public function update(){		
		/* 更新管理员的权限 */
		$role_name=trim(strip_tags($_REQUEST['role_name']));
		$role_describe=trim(strip_tags($_REQUEST['role_describe']));
		$act_list = @join(",", $_REQUEST['action_code']);
		$id=i('id',0,'intval');
		$data=array('role_name'=>$role_name,'action_list'=>$act_list,'role_describe'=>$role_describe,'role_id'=>$id);
		$new_id =db('role')->where('role_id='.$id)->save($data);
		$data=array('privilege'=>$act_list);
		$new_id =db('admin')->where('admin_id!=1 and role_id='.$id)->save($data);
		/* 记录管理员操作 */
		admin_log($role_name, 'edit', 'role');
		show_message('操作成功', array('角色列表'=>'Admin/Role/lists'));
	}

	public function del(){		
		$exc_role=exchange('role','role_id','role_name');		
		$id = intval($_REQUEST['id']);
		$role_name=$exc_role->get_name($id);
		$remove_num =db('admin')->where(array('role_id'=>$id))->count();
		if($remove_num > 0){
			echo ejson(array('status'=>false,'data'=>'此角色有管理员在使用，暂时不能删除'));
		}
		else{
			$exc_role->drop($id);
			$url = U('Role/lists?is_ajax=1');
			admin_log($role_name, 'remove', 'role');
			header("Location: $url\n");
		}
	}
	public function edit(){	
		if(IS_POST){	
			$this->update();return;
		}
		$id =i('request.id',0,'intval');
		$priv_str = db('role')->where(array('role_id'=> $id))->getField('action_list');
		/* 获取权限的分组数据 */
		$res = db('privilege')->select('pid,up_id,name,comment')->where('up_id=0')->query();
		while(list($key,$rows)=each($res)){
			$priv_arr[$rows['pid']] = $rows;
		}
		/* 按权限组查询底级的权限名称 */
		$result = db('privilege')->select('pid,up_id,name,relevance,comment')->where("up_id " .db_create_in(array_keys($priv_arr)))->query(); 
		while(list($key,$priv)=each($result)){//分组存入同一父分类下
			$priv_arr[$priv["up_id"]]["priv"][$priv["name"]] = $priv;
		}
		
		// 将同一组的权限使用 "," 连接起来，供JS全选
		foreach ($priv_arr AS $pid => $action_group){
			$priv_arr[$pid]['priv_list'] = join(',', @array_keys($action_group['priv']));
			foreach ($action_group['priv'] AS $key => $val){
				$priv_arr[$pid]['priv'][$key]['cando'] = ($priv_str == 'all' || strpos(",$priv_str,", ",$val[name],") !== false) ? 1 : 0;
			}
		}
		 /* 模板赋值 */
		$this->assign('role',        db('role')->where('role_id='.$id)->getRow());
		$this->assign('ur_here',     '编辑角色');
		$this->assign('action_link', array('href'=>'Admin/Role/lists', 'text' => '角色列表'));
		$this->assign('priv_arr',    $priv_arr);
		$this->assign('role_id',     $id);
		$this->display('Role:add');
	}

}
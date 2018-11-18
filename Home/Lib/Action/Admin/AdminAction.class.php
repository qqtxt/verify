<?php
defined('JETEE_PATH') or exit();
//exc d m
class AdminAction extends AdministrationAction{	
	//管理員列表
	public function lists(){
		if(IS_AJAX){
			$this->assign('admin_list',  E('Admin')->get_list());
			echo ejson(array('status'=>true,'data'=>$this->fetch()));return;
		}
		$this->assign('ur_here',     '管理员列表');
		$this->assign('action_link', array('href'=>'Admin/Admin/add', 'text' => '添加管理员'));
		$this->assign('full_page',   1);
		$this->assign('admin_list',  E('Admin')->get_list());
		$this->display();
	}
	//添加管理員
	public function add()
	{
		if(IS_POST){	
			$this->insert();
		}
		 /* 模板賦值 */
		$this->assign('ur_here',     '添加管理员');
		$this->assign('action_link', array('href'=>'Admin/Admin/lists', 'text' => '管理員列表'));
		$this->assign('form_act',    'insert');
		$this->assign('action',      'add');
		$this->assign('select_role',  E('Role')->get_list());
		$this->display();
	}
	//添加管理員1
	public function insert(){		
		$d=E('Admin');
		$name  =i('name','','trim');
		$email =i('email','','trim');
		$pass = i('pass','','trim');
		$select_role = i('select_role',0,'intval');
		$salt=Str::randString(6);
		$pass=$d->getPassword($pass,$salt);
		$reg_date=NOW_TIME;

		/* 判斷管理員是否已經存在 */
		if(!empty($name)){
			$tmp=$d->getByUserName($name);
			if ($tmp){
				show_message('该管理员已经存在', array(), 1);
			}
		}
		/* Email地址是否有重複 */
		if (!empty($email)){
			$tmp=db('admin')->where(array('email'=>$email))->count();
			if ($tmp) show_message('该邮箱已经注册过', array(), 1);
		}
		if(mb_strlen($name,'utf-8')<2 || $_REQUEST['pass']&&mb_strlen(trim($_REQUEST['pass']),'utf-8')<5 || !is_email($email) || preg_match('/[^\x{4e00}-\x{9fa5}a-zA-Z0-9_@\.]/u', $name)) {//存在非法字符
			show_message('存在非法字符或用户名密码太短', array(), 1);
		}
		
		$role_id = 0;
		$action_list = '';
		if (!empty($select_role)){			
			$action_list = db('role')->where(array('role_id'=>$select_role))->getField('action_list');
			$role_id = $select_role;
		}
		db('admin')->add(array('email'=>$email,'username'=>$name,'password'=>$pass,'salt'=>$salt,'reg_date'=>$reg_date,'privilege'=>$action_list,'role_id'=>$role_id));		
		admin_log($name, 'add', 'admin');

		$link['返回列表']='Admin/Admin/lists';
		$link['继续添加']= 'Admin/Admin/add';
		show_message('添加管理员' . "&nbsp;" .$name . "&nbsp;成功", $link, 0);
	}
	public function update()
	{
		$d=E('Admin');
		$admin_id = i('id',0,'intval');
		$name  =i('name','','trim');
		$email =i('email','','trim');
		$pass = i('pass','','trim');
		$oldpass =i('oldpass','','trim');
		$select_role = i('select_role',0,'intval');
		$salt=Str::randString(6);
		$pass=$d->getPassword($pass,$salt);
		
		$user=$d->getById($admin_id); //原来的
		//如果要修改密碼
		$pwd_modified = false;
		if (!empty($_REQUEST['pass'])){	
			/* 查詢舊密碼並與輸入的舊密碼比較是否相同 */
			if(session('admin_id')!=1 && !$d->checkPassword($oldpass,$user)){
				show_message('原密码错误', array(), 1);
			}
			$pwd_modified = true;
		}

		/* 判斷管理員是否已經存在 */
		if ($name !=$user['username'] && !empty($name)){
			$tmp=$d->getByUserName($name);
			if ($tmp){
				show_message('该管理员已经存在', array(), 1);
			}
		}
		/* Email地址是否有重複 */
		if ($email !=$user['email'] && !empty($email)){
			$tmp= db('admin')->where(array('email'=>$email))->count();
			if ($tmp) show_message('该邮箱已经注册过', array(), 1);	
		}
		if(mb_strlen($name,'utf-8')<2 || $_REQUEST['pass']&&mb_strlen(trim($_REQUEST['pass']),'utf-8')<5 || !is_email($email) || preg_match('/[^\x{4e00}-\x{9fa5}a-zA-Z0-9_@\.]/u', $name)) {//存在非法字符
			show_message('存在非法字符', array(), 1);	
		}
			
		$role_id = 0;
		$action_list = '';
		if (!empty($select_role)){			
			$action_list = db('role')->where(array('role_id'=>$select_role))->getField('action_list');
			$role_id = $select_role;
		}
		
		//更新管理員信息	
		$data=array('username'=>$name,'email'=>$email,'role_id'=>$role_id);
		$user['admin_id']==1 ? '' : $data['privilege']=$action_list;
		if($pwd_modified){$data['password']=$pass;$data['salt']=$salt;}
		db('admin')->where('admin_id='.$user['admin_id'])->save($data);
		
		admin_log("ID[$user[admin_id]]", 'edit', 'admin');

		/* 如果修改了密碼，則需要將session中該管理員的數據清空 */
		if ($pwd_modified && session('admin_id') == $admin_id ){
			$msg = '编辑密码成功';
		}
		else{
			$msg = '编辑管理员成功';
		}
		/* 提示信息 */
		show_message($msg, array('返回管理员列表'=>'Admin/Admin/lists'));	
	}

	public function del(){
		$exc=exchange('admin','admin_id','username');
		$id = i('id',0,'intval');
		if ($id == 1){
			echo ejson(array('status'=>false,'data'=>'创始人不能删除'));
		}
		elseif ($id == session('admin_id')){
			echo ejson(array('status'=>false,'data'=>'不能删除自己'));
		}
		elseif(($admin_name=$exc->get_name($id)) && $exc->drop($id)){
			admin_log($admin_name, 'remove', 'admin');
			$url = U('Admin/lists?is_ajax=1');
			header("Location: $url\n");
		}else echo ejson(array('status'=>false,'data'=>'操作失败'));
	}
	//修改管理员
	public function edit()
	{	
		if(IS_POST){	
			$this->update();
		}
		$d=E('Admin');
		$admin_id = i('id',0,'intval');
		/* 獲取管理員信息 */
		$user = $d->getById($admin_id);
		/* 模板賦值 */
		$this->assign('ur_here',     ' 編輯管理員');
		$this->assign('action_link', array('text' => '管理員列表', 'href'=>U('Admin/lists')));
		$this->assign('user',        $user);
		$this->assign('form_act',    'update');
		$this->assign('action',      'edit');
		$this->assign('select_role',  E('Role')->get_list());
		$this->display('Admin:add');
	}
	public function clear_cache(){
		$link[] = array('text' => '返回欢迎使用', 'href'=>'Admin/Index/welcome'); 
		deleteDir(TEMP_PATH);
		deleteDir(HTML_PATH);
		if(deleteDir(CACHE_PATH)){
			make_dir(TEMP_PATH);
			make_dir(HTML_PATH);
			make_dir(CACHE_PATH);
			S('article_cat_list',null);
			//@unlink(DATA_PATH.'access_token.php');
			show_message('清除成功', array('返回欢迎使用'=>'Admin/Index/welcome'));
		}
		show_message('清除失败', array(),1);
	}
}
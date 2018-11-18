<?php
defined('JETEE_PATH') or exit();
/** 		
*管理员基础模型  db
* @version 0.0.1 11:18 2018/8/13
*/
class AdminModel{
	public function get_list(){
		$list = array();
		$result= db('admin')->order('admin_id desc')->select('*')->query();
		while (list($key, $row) = each($result)){
			$row['reg_date']     = format_time($row['reg_date']);
			$row['last_login']   = format_time($row['last_login']);
			$row['role_name']   =$row['role_id']>0 ? db('role')->select('role_name')->where('role_id='.$row['role_id'])->single() : 'N/A';
			$list[]=$row;
		}
		return $list;
	}

	/**
	* 设置登陆状态及登陆记录等处理   根据checkUser设置的$this->user处理
	* @return void
	*/	
    public function setLogin($user){
        //更新用户信息
        db('admin')->where('admin_id='.$user['admin_id'])->save(array('last_login' => time(), 'last_ip' =>get_client_ip()));
		session('admin_id',$user['admin_id']);
		#S('admin-'.$user['admin_id'],db()->row('select * from '.C('DB_PREFIX').'admin where admin_id='.$user['admin_id']));
	}
	/**
	* 设置登陆状态及登陆记录等处理
	* @return void
	*/	
    public function logout(){
		S('admin-'.session('admin_id'),null);
		session('admin_id',null);		
		return true;
	}
	/**
	* 统一密码处理  给定密码与$this->user对比 
	* @return bool
	*/
    public function checkPassword($pass,$user){
		if(!empty($pass)){
			$salt=$user['salt'];
			$password=$this->getPassword($pass,$salt);
			if($user['password']===$password){
				return true;
			}
		}
		return false;
	}
	/**
	* 统一密码处理
	* @param str $str 	要加密的
	* @param str $salt 	加点盐
	* @return str 加密后的密码
	*/
    public function getPassword($str,$salt){
		$password=sha1($str);
		$password=md5($password.$salt);		
		return $password;
	}
    public function getByUserName($user_name) {
		return db('admin')->select('*')->where('username=:username')->bindValues(array('username'=>$user_name))->row();
	}	
    public function getById($id) {
		if(!$id){
			return false;
		} 
		return db('admin')->select('*')->where('admin_id='.$id)->row(); 
	}	
	/**
	 * 检查验证码
	 * @param str $str 用户输入的
	 * @return boolean
	 */
	public function check_verify($str){
		$return=false;
		if(!empty($str) && session('verify')===md5($str)){//防都为空
			$return=true;
			//验证码用过失效
			session('verify',null);
		}
		return $return;
	}
}
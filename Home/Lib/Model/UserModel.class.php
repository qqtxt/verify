<?php
defined('JETEE_PATH') or exit();

/** 		
*用户基础模型	用户注册登陆要做成可以api调用  db
* @version 0.0.1 19:11 2017/9/19
    array('verify','require','验证码必须！'), //默认情况下用正则进行验证
    array('name','','帐号名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一
    array('value',array(1,2,3),'值的范围不正确！',2,'in'), // 当值不为空的时候判断是否在一个范围内
    array('repassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致
    array('password','checkPwd','密码格式不正确',0,'function'), // 自定义函数验证密码格式

*/
class UserModel{
	protected $user;//临时用
	public	$error='';//错误信息
	//列表 1
	public function lists(){
		$result = get_filter();
		if ($result === false){
			$sql=array();$sql['where']=array();
			$uid=i('uid','','intval');
			$keyword=i('keyword','');
			$sort_by=i('sort_by','a.uid');
			$sort_order=i('sort_order','DESC');
			$status=i('status',99,'intval');
			$is_taobaoke=i('is_taobaoke',99,'intval');
			$check_id=i('check_id',0,'intval');
			$card_status=i('card_status',99,'intval');
			/* 过滤条件 */
			$sql['where'][]=array('a.type='.USER_TYPE);
			if($uid){
				$filter['uid']=$uid;
				$sql['where'][]=array('a.uid',$uid);
			}			
			if($is_taobaoke!='99'){
				$filter['is_taobaoke']=$is_taobaoke;
				$sql['where'][]=array('a.is_taobaoke',$is_taobaoke);
			}
			if($status!='99'){
				$filter['status']=$status;
				$sql['where'][]=array('a.status',$status);
			}
			if($check_id!='0'){
				$filter['check_id']=$check_id;
				$sql['where'][]=array('a.check_id',$check_id);
			}
			if($card_status!='99'){
				$filter['card_status']=$card_status;
				$sql['where'][]=array('b.status',$card_status);
			}
			if(isset($_REQUEST['keyword']) && $keyword!=''){
				$filter['keyword']=$keyword;
				$sql['where'][]=array('(a.phone=? or a.qq=? or a.uid=?)',$keyword,$keyword,$keyword);
			}
			$filter['sort_by']    = $sort_by;
			$filter['sort_order'] = $sort_order;				
			$filter['record_count'] = db('user a')->join('bank_card b','a.uid=b.uid')->where($sql['where'])->count();
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
		$list =db('user a')->select('a.*,b.card_code,b.card_my_name,b.bank_name,b.bank_province,b.bank_city,b.bank_subname,b.status as s')->join('bank_card b','a.uid=b.uid')->where($sql['where'])->order($sql['order'])->page($sql['page'],$sql['page_size'])->query();
		return array('list' => $list, 'filter' => $filter,'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
	}

	/**
	* 取昵称 1
	* @return str
	*/	
    public function getNickname($user){
		return $user['nickname'] ? $user['nickname'] : $user['email'];
	}
	/**
	* 违规帐号，已被管理员锁定 1
	* @return bool
	*/	
    public function isLock($phone){
		if(db()->one('select count(uid) from '.C('DB_PREFIX').'user where phone=? and status=2',array($phone))==1){
			return true;
		}
		return false;
	}

	//用户名以字母开始,5-15位字母数字
	public function checkUsername($name){
		return preg_match('/^[a-zA-Z][a-zA-Z0-9]{4,14}$/',$name);
	}
	
	//汉字字母数字组合
	public function checkQQ($name){
		return preg_match('/^[1-9][0-9]{3-11}$/',$name);
	}
	
	/**
	*10秒发布项目 快速注册  自动登陆
	* @author jetee				
	* @return 随机生成的密码
	* @version 0.0.1 17:46 2018/6/20
	*/
	public function quickAdd($email,$phone){
		
		//伪造
		$data['user_name']=$this->getRedomUserName();
		$data['password']=strtolower(Str::randString());
		$data['email']=$email;
		$data['phone']=$phone;
		$this->_validate= array(
			array('user_name','','用户名已经存在',1,'unique'), // 在新增的时候验证name字段是否唯一  
			array('email','email','邮箱不正确',1),
			array('email','','邮箱已经注册过',1,'unique'),
			array('phone','is_phone','手机不正确',1,'function'),
			array('phone','','手机已经注册过',1,'unique'),
		);
		$row=$this->insertUser($data);		
		if(!$row){
			return false;
		}
		//成功插入用户后设置为已登陆
		$this->setLogin();
		$row['password']=$data['password'];
		return $row;
	}
	/**
	*获取一个不重复的随机用户名
	* @return boolean
	* @version 0.0.1 17:46 2018/6/20
	*/
	public function getRedomUserName(){
		do{
			$user_name='user'.strtolower(Str::randString());
		}while(db('user')->where(array('user_name'=>$user_name))->count());
		return $user_name;
	}
	/**
	*通过用户名删除用户
	* @return boolean
	* @version 0.0.1 17:46 2018/6/20
	*/
	public function deleteUserName($user_name){
		return  db()->one('delete from '.C('DB_PREFIX').'user where user_name=?',array($user_name));
	}
	/**
	*根据post提交 登陆 $_POST['user_name']
	* @author jetee				
	* @return array
	* @version 0.0.1 15:39 2014/11/15
	*/
	public function doLogin(){
		$this->_validate= array(
			array('user_name','isLock','违规帐号，已被管理员锁定',1,'callback',3),
			array('user_name','require','请输入登陆帐号',1),
			array('user_name','checkUser','帐号不存在',1,'callback',3),
			array('password','require','请输入密码',1),
			array('password','checkPassword','密码不正确',1,'callback',3),
		);
		if($this->autoValidation($_POST,3)){			
			$this->setLogin();
			return true;
		}
		return false;
    }
	/**
	* 查找用户是否存在 取用户存入$this->user
	* @return bool
	*/	
    public function checkUser($user_name){
		if($this->getUser($user_name)){
			return true;
		}
		return false;
	}





	/**
	* 取用户存入 $this->user
	 * @return array row
	*/	
    public function getUser($user_name){
		$user=false;
		if(is_phone($user_name)){
			$user=$this->getByPhone($user_name);			
		}elseif(is_email($user_name)){
			$user=$this->getByEmail($user_name);
		}else{
			$user=$this->getByUserName($user_name);
		}
		return $user;
	}
	/**
	 * 不做输入检查，调用端处理
	 * @return array row
	 */	
    public function getByUserName($user_name) {
		$row=db()->row('select * from '.C('DB_PREFIX').'user where user_name=?',array($user_name));
		$this->user=$row ? $row : false;
		return $this->user;
	}
    public function isOnlyNickname($nickname) {
		return db()->one('select count(uid) from '.C('DB_PREFIX').'user where nickname=?',array($nickname))==0;
	}

    public function isOnlyQQ($qq) {
		return db()->one('select count(uid) from '.C('DB_PREFIX').'user where qq=?',array($qq))==0;
	}


/****************可当控制器*/
	//邮箱登陆
    public function loginByEmail() {
		$email=i('post.email','','trim');
		$password=$_POST['password'];
		$verify=i('post.verify','','trim');
		if(empty($verify) || !Image::chkVerify('verify',$verify)){
			$this->error='验证码不正确！';
		}elseif(is_email($email)){
			$user=$this->getByEmail($email);
			if($user)
				if($this->getPassword($password,$user['salt'])!==$user['password'])
					$this->error='密码不正确！';
				elseif($user['status']!=0)
					$this->error='帐号被锁定！';
				else
					return $this->setLogin($user['uid']);
			else
				$this->error='帐号不存在！';
		}
		return false;
	}
	//手机登陆
    public function loginByPhone() {
		$type=i('post.type',0,'intval');
		$phone=i('post.phone','','trim');
		$password=$_POST['password'];
		$verify=i('post.verify','','trim');
		if(empty($verify) || !Image::chkVerify('verify',$verify)){
			$this->error='验证码不正确！';
		}elseif(is_phone($phone)){
			$user=$this->getByPhone($phone,$type);
			if($user)
				if($this->getPassword($password,$user['salt'])!==$user['password'])
					$this->error='密码不正确！';
				elseif($user['status']!=0)
					$this->error='帐号被锁定！';
				else
					return $this->setLogin($user['uid']);
			else
				$this->error='帐号不存在！';
		}else $this->error='请用手机登陆';
		return false;
	}
	/**
	*根据post提交 注册用户 正常注册  自动登陆
	* @author jetee				
	* @return array
	* @version 0.0.1 15:39 2014/11/15
	*/
	public function reg(){
		$pc=i('post.pc',0,'intval');
		$data['type']=i('post.type',0,'intval');
		$data['phone']=i('post.phone','');
		$verfiy=i('post.verfiy','');
		$data['password']=$_POST['password'];
		$data['qq']=i('post.qq','');
		$data['weixin']=i('post.weixin','');
		$data['parent_id']=i('post.parent_id',0,'intval');
		$agree=i('post.agree',0,'intval');
		$msg='';
		if($data['type']==0 && C('close_reg_shop')==1 || $data['type']==1 && C('close_reg_buyer')==1){
			$msg='本站已关闭注册';
		}elseif(!is_phone($data['phone'])){
			$msg='手机号码不正确';
		}elseif($this->isRegByPhone($data['phone'])>0){
			$msg='手机号码已经注册过';
		}elseif(!$this->check_captcha_phone($data['phone'])){
			$msg='请先获取手机验证码';
		}elseif($verfiy==''){
			$msg='手机验证码为空';
		}elseif($data['password']==''){
			$msg='请填写密码';
		}elseif(!is_numeric($data['qq'])){
			$msg='QQ号码只能为数字';
		}elseif(!preg_match('/^[a-zA-Z]+[a-zA-Z0-9_\-]*$/',$data['weixin'])){
			$msg='微信帐号不正确';
		}elseif(!$data['parent_id']){
			$msg='请填写邀请码';
		}elseif(!$this->isParent($data['parent_id'])){
			$msg='请填写正确的邀请码';
		}elseif(!$agree){
			$msg='请同意人气云协议';
		}elseif(!$this->check_captcha($verfiy,'regVerify')){
			$msg='手机验证码不正确';
		}
		if($msg){
			$this->error=$msg;
			return false;
		}
		
		//成功插入用户后设置为已登陆
		if($uid=$this->insertUser($data)){
			if(!($pc&&$data['type']))$this->setLogin($uid);
			event($uid,1,0);
			account_log($uid,$uid,0,C('price_reg_bonus'), 5, '新会员注册送');
			return true;
		}else{
			return false;
		}
	}
	
	
/****************本地用*/
	//没注册过1  注册过0 
	public function isParent($id){
		return db()->count('select count(uid) from '.C('DB_PREFIX').'user where uid=?',array($id));
	}
	public function isRegByPhone($phone){
		return db()->count('select count(uid) from '.C('DB_PREFIX').'user where phone=?',array($phone));
	}
    public function getByPhone($phone,$type) {
		return db()->row('select * from '.C('DB_PREFIX').'user where type=? and phone=?',array($type,$phone));
	}

	//已经注册过
	public function isRegByEmail($email){
		return db()->count('select count(uid) from '.C('DB_PREFIX').'user where email=?',array($email));
	}
    public function getByEmail($email) {
		return db()->row('select * from '.C('DB_PREFIX').'user where email=?',array($email));
	}
	/**
	* 设置登陆状态及登陆记录等处理   根据checkUser设置的$this->user处理
	* @return void
	*/	
    public function setLogin($uid){
		if(!$uid) return false;		
		db()->query('update '.C('DB_PREFIX').'user set last_visit=?,last_ip=?,visit_count=visit_count+1  where uid=?',array(NOW_TIME,get_client_ip(),$uid));
		session('uid',$uid);
		session('type',user('type'));
		return true;
	}	
    public function getByUid($id) {
		$id=intval($id);
		$row=array();
		if($id){
			$row=db()->row('select * from '.C('DB_PREFIX').'user where uid=?',array($id));
		}
		return $row;
	}

	/**
	* 取登陆uid
	* @return bool
	*/	
    public function getUid(){
		$uid=session('uid');
		if($uid>0){
			return $uid;
		}
		return false;
	}
	/**
	* 取已登陆用户
	* @return row
	*/	
    public function getLoginUser(){
		return user();
	}
/****************私有*/
	/**
	 * 检查验证码时检查手机是否更改
	 * @param str 邮箱
	 * @return boolean
	 */
	public function check_captcha_phone($str){	
		$return=false;
		if(trim(session('regPhone'))===trim($str)){
			$return=true;
		}
		return $return;
	}
	/**
	 * 检查验证码时检查邮箱是否更改
	 * @param str 邮箱
	 * @return boolean
	 */
	public function check_captcha_email($str){	
		$return=false;
		if(trim(session(C('sn_captcha_email')))===trim($str)){
			#session(C('sn_captcha_email'),null);  验证码失效它才能失效
			$return=true;
		}
		return $return;
	}
	/**
	 * 检查验证码
	 * @param str $str 用户输入的验证码
	 * @param str $str 验证码名字
	 * @return boolean
	 */
	public function check_captcha($str,$verify_name='verify'){
		$return=false;
		if(!empty($str) && session($verify_name)===md5($str)){//防都为空
			$return=true;
			//验证码用过失效
			session($verify_name,null);
		}
		return $return;
	}
	
	//更新用户信息 及更新session
	public function updateUser($data){
		if(!$data['uid']) return false;		
		$uid=$data['uid'];unset($data['uid']);
		$return=db('user')->where('uid='.$uid)->save($data);
		if($return)// && S('user-'.$uid)){
			hm('user-'.$uid,null);
		}
		return $return;
	}
	/**
	* 只负责新用户入库,不作检查   成功插入设置临时user
	* @return array
	* @version 0.0.1 14:30 2014/11/27
	*/
	public function insertUser($data){
		$data['reg_ip']	=get_client_ip();
		$data['reg_date']	=NOW_TIME;
		$data['salt']=substr(sha1($data['reg_date']),0,6);
		$data['password']=$this->getPassword($data['password'],$data['salt']);
		$id=db('user')->add($data);
		if(!$id){
			return false;
		}
		return $id;
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
	/**
	* 统一密码处理  给定密码与$user 对比 
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
	* 设置登陆状态及登陆记录等处理
	* @return void
	*/	
    public function logout(){
		hm('user-'.session('uid'),null);
		session('uid',null);
		return true;
	}
}
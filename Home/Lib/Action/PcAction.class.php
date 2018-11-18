<?php
defined('JETEE_PATH') or exit();
/**
* 轻量级pc端控制器  处理不用后台设置，不用登陆 不用模板引擎 ?m=pc&a=ajaxChkLogin&thin=200
* @version 1.0.1 11:44 2016/5/28 
*/
class PcAction{
	public function __construct(){
		//需要开session的统一开  
		if(!in_array(ACTION_NAME,array('index','getUserMoney','getPhone','getQQ','calc','ajaxChkLogin'))){
			session(C('SESSION_OPTIONS'));
			//先检查令牌 没成功退出
			if(!check_token()){
				$name   = C('TOKEN_NAME');
				if(!session('?'.$name)) {
					exit('session');
				}
				exit('cookie');
			}
			buildToken();//令牌成功后刷新

			//检查有没有登陆  且不是后台操作返款
			if(!is_login(0) && !(session('admin_id') && ACTION_NAME=='backMoney') ){
				exit(json_encode(array('status'=>false,'data'=>'登陆已超时')));
			}
		}
	}

	//检查后台权限
	protected function check_authz($priv_str) {
		$admin_id=session('admin_id');
		if($admin_id==1) {
			return true;
		}
		$privilege=db()->single('select privilege from '.C('DB_PREFIX').'admin where admin_id='.$admin_id);		
		$priv_str=',' .$priv_str. ',';
		$privilege=',' .$privilege. ',';
		return strpos($privilege, $priv_str) !== false;
	}
	
	public function getUserMoney(){
		session(C('SESSION_OPTIONS'));
		if(!session('uid')){
			$msg=array('status'=>false,'data'=>'登陆已超时');
		}else $msg=array('status'=>true,'data'=>user('money'));
		echo json_encode($msg);
	}
	// 检查是否登陆	
	public function ajaxChkLogin(){
		session(C('SESSION_OPTIONS'));
		buildToken();
		if(is_login(0)){
			$avatar=user('avatar');
			echo json_encode(array('status'=>true,'avatar'=>$avatar));
		}else	echo json_encode(array('status'=>false));
	}
	//保存用户资料
	public function ajaxDoUserInfo(){
		$data['qq']=I('post.qq','');
		$data['weixin']=I('post.weixin','');
		$data['sex']=I('post.sex','','intval');
		$data['uid']=session('uid');
		$user=E('User');
		$msg=array('status'=>false,'data'=>'登陆已超时');
		if(!$data['uid']){
			$msg['data']='登陆已超时';
		}elseif(!is_qq($data['qq'])){
			$msg['data']='QQ号码不对';
		}elseif(!preg_match('/^[a-zA-Z]+[a-zA-Z0-9_\-]*$/',$data['weixin'])){
			$msg['data']='微信帐号不对';
		}elseif($user->updateUser($data)){
			$msg=array('status'=>true,'data'=>'保存成功');
		}else $msg=array('status'=>false,'data'=>'未作修改！');

		echo json_encode($msg);
	}	
	public function ajaxUploadHead(){
		$user=E('User');
		$base64_image_content = $_POST['imgBase64'];
		$msg=array('status'=>false,'data'=>'保存头像失败，请重试');
		//匹配出图片的格式
		if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
			$type = $result[2];
			$new_file = UPLOADS_PATH.'head';
			if(!file_exists($new_file)){
				mkdir($new_file, 0755,true);
			}
			$png = $new_file.'/'.random_name($new_file).".{$type}";
			$new_file1= $new_file.'/'.random_name($new_file).'.jpg';
			if (file_put_contents($png, base64_decode(str_replace($result[1], '', $base64_image_content)))){
				//转成jpg
				$data = GetImageSize($png);
				switch ($data[2])
				{
					case 1:
						$im = imagecreatefromgif($png);break;
					case 2:
						$im = imagecreatefromjpeg($png);break;
					case 3:
						$im = imagecreatefrompng($png);break;
					default:
						'';
				}
				imagejpeg($im,$new_file1,100);
				@unlink($png);
				imagedestroy($im);
				if($user->updateUser(array('uid'=>session('uid'),'avatar'=>$new_file1))){
					$msg=array('status'=>true,'data'=>'保存头像成功');
				}
			}
		}
		echo json_encode($msg);
	}
	public function ajaxDoEditPassword(){
		$old_pass=I('old_pass','','trim');
		$password=I('password','','trim');
		$user=E('User');
		if(empty($old_pass) || empty($password)){
			$msg=array('status'=>false,'data'=>'密码不能为空');
		}elseif($user->checkPassword($old_pass,user())==false){
			$msg=array('status'=>false,'data'=>'旧密码不正确');
		}else{
			$salt=Str::randString(6);
			$password=$user->getPassword($password,$salt);
			if($user->updateUser(array('uid'=>session('uid'),'password'=>$password,'salt'=>$salt))){
				$msg=array('status'=>true,'data'=>'密码修改成功');
			}else{
				$msg=array('status'=>false,'data'=>'重置密码失败');
			}

		}		
		echo json_encode($msg);
	}
	public function ajaxDoFeedbackClearNew(){//清除反馈最新为0
		db('feedback')->where('uid='.session('uid').' and is_new=1 and admin_id>0')->save(array('is_new'=>0));
		$msg=array('status'=>true);
		echo json_encode($msg);
	}	
}
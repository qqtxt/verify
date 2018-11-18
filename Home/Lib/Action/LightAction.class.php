<?php
defined('JETEE_PATH') or exit();
/**
* 轻量级前后台公用控制器  处理不用后台设置，不用登陆 不用模板引擎
* @version 1.0.1 11:44 2016/5/28 
*/
class LightAction{
	public function __construct(){//?m=Light&a=verify_code&thin=200
		//需要开session的统一开
		if(in_array(ACTION_NAME,array('ajaxDoFeedbackSend','ajaxDoForget','ajaxForgetSendPhone','ajaxSendPhone','ajaxSendEmailVerfiy','ajaxDoLogin','ajaxSendEmailCaptcha','ajaxSendPhoneCaptcha','ajaxDoReg','ajaxDoLogout'))){
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
		}
	}
	public function ajaxDoLogout(){
		#session(C('SESSION_OPTIONS'));
		echo json_encode(array('status'=>E('User')->logout(),'data'=>'退出登陆成功'));
	}
	public function ajaxDoLogin(){
		$user=E('User');
		if($user->loginByPhone()){
			echo json_encode(array('status'=>true,'data'=>'登陆成功','avatar'=>user('avatar'),'uid'=>session('uid')));
		}
		else echo json_encode(array('status'=>false,'data'=>$user->error));	
		
	}
	public function ajaxDoReg(){
		common_config();
		$pc=i('post.pc',0,'intval');
		$type=i('post.type',0,'intval');
		//注册过程
		$user=E('User');
		if($user->reg()){
			$msg=array('status'=>true,'data'=>'注册成功！');
			if($pc && !$type){
				$msg['avatar']=user('avatar');
				$msg['uid']=session('uid');
			}elseif(!$pc && $type){
				$msg['uid']=session('uid');
			}
		}else $msg=array('status'=>false,'data'=>$user->error);

		echo json_encode($msg);
	}
    public function ajaxGetJsonRegion(){//取地区
		$type   = !empty($_REQUEST['type'])   ? intval($_REQUEST['type'])   : 0;
		$parent = !empty($_REQUEST['parent']) ? intval($_REQUEST['parent']) : 0;

		$arr['regions'] = get_regions($type, $parent);
		$arr['type']    = $type;
		$arr['target']  = !empty($_REQUEST['target']) ? stripslashes(trim($_REQUEST['target'])) : '';
		$arr['target']  = htmlspecialchars($arr['target']);
		echo json_encode($arr);	
	}	
	public function ajaxUploadPic(){//上传图片  chk=1检查是否P图
		common_config();
		$chk=I('request.chk',0,'intval');
		//上传图片
		if (isset($_FILES['myFile']) && chk_files($_FILES['myFile'])){//检查图片是否有上传
			$img_path=date('Ym').'/';//图片保存路径
			$upload = new UploadFile();
			$upload->maxSize  =C('upload_size_limit');
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');
			$upload->savePath = UPLOADS_PATH.'/tmp/'.$img_path;
			make_dir($upload->savePath);
			if($upload->upload()){// 上传成功 获取上传文件信息
				$info =  $upload->getUploadFileInfo();
				if(!empty($info[0]['savename'])){
					if($chk && chk_p(UPLOADS_PATH.'/tmp/'.$img_path.$info[0]['savename'])){
						echo '1';
					}else{
						echo $img_path.$info[0]['savename'];
					}
					return;
				}
			}
		}
		echo '';
	}
	public function ajaxUploadPhoto(){//暂时为拍照上传
		common_config();
		$msg=array('code'=>0,'message'=>'上传失败！');
		//上传图片
		if (isset($_FILES['myFile']) && chk_files($_FILES['myFile'])){//检查图片是否有上传
			$img_path=date('Ym').'/';//图片保存路径
			$upload = new UploadFile();
			$upload->maxSize  =C('upload_size_limit');
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');
			$upload->savePath = UPLOADS_PATH.'/tmp/'.$img_path;
			make_dir($upload->savePath);
			if($upload->upload()){// 上传成功 获取上传文件信息
				$info =  $upload->getUploadFileInfo();
				if(!empty($info[0]['savename'])){
					$msg=array('code'=>200,'url'=>$img_path.$info[0]['savename']);
				}
			}
		}
		echo json_encode($msg);
	}
	public function ajaxUploadImage(){//上传图片
		common_config();
		$chatPic='';
		$msg='';
		$code=0;
		//上传图片
		if (chk_files($_FILES['file'])){//检查图片是否有上传
			$img_path=date('Ym').'/';//图片保存路径
			$upload = new UploadFile();
			$upload->maxSize  =C('upload_size_limit');
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');
			$upload->savePath = UPLOADS_PATH.'/chatImage/'.$img_path;
			make_dir($upload->savePath);
			if($upload->upload()){// 上传成功 获取上传文件信息
				$info =  $upload->getUploadFileInfo();
				if(!empty($info[0]['savename'])){
					$chatPic=$img_path.$info[0]['savename'];
				}
			}else{
				$msg=$upload->getErrorMsg();
				$code=1;
			}
		}
		echo json_encode(array('code'=>$code,'msg'=>$msg,'data'=>array('src'=>UPLOADS_URL.'chatImage/'.$chatPic)));
	}
	public function ajaxUploadFile(){//上传文件
		common_config();
		$chatPic='';
		$msg='';
		$code=0;
		//上传图片
		if (chk_files($_FILES['file'])){//检查图片是否有上传
			$img_path=date('Ym').'/';//图片保存路径
			$upload = new UploadFile();
			$upload->maxSize  = C('upload_size_limit');
			$upload->allowExts  = array('doc', 'docx', 'zip', 'rar', '7z', 'jpg', 'gif', 'png', 'jpeg');
			$upload->savePath = UPLOADS_PATH.'/chatFile/'.$img_path;
			make_dir($upload->savePath);
			if($upload->upload()){// 上传成功 获取上传文件信息
				$info =  $upload->getUploadFileInfo();
				if(!empty($info[0]['savename'])){
					$chatPic=$img_path.$info[0]['savename'];
				}
			}else{
				$msg=$upload->getErrorMsg();
				$code=1;
			}
		}		
		echo json_encode(array('code'=>$code,'msg'=>$msg,'data'=>array('src'=>UPLOADS_URL.'chatFile/'.$chatPic,'name'=>basename($chatPic))));
	}
	public function ajaxSendEmailVerfiy(){//注册发邮箱验证码
		//发送时间间隔  180秒  
		$email =i('post.email','','trim');
		$verify =i('post.verify','','trim');
		$t=session(C('sn_email_send_time')) ? session(C('sn_email_send_time')) :0;
		if(NOW_TIME-$t<C('EMAIL_GAP_TIME')){
			$msg=array('status'=>false,'data'=>'请稍候再发');		
		}elseif(!is_email($email)){
			$msg=array('status'=>false,'data'=>'邮箱不正确');		
		}elseif(!Image::chkVerify('verify',$verify)){
			$msg=array('status'=>false,'data'=>'验证码不正确');		
		}elseif(D('User')->getByEmail($email)){
			$msg=array('status'=>false,'data'=>'此邮箱已注册过');		
		}else{
			$content='您的验证码为:'.Str::build_verify(6,1,C('sn_email_captcha')).'<br/>如果进了垃圾箱请回复数字1，以便以后顺利接收。';
			if(is_email($email) && Email::send($email,'您好！','验证码', $content, 1,0)){
				session(C('sn_captcha_email'),$email);
				session(C('sn_email_send_time'),NOW_TIME);
				$msg=array('status'=>true,'data'=>'发送成功');		
			}else{
				$msg=array('status'=>false,'data'=>'发送失败,请重试');		
			}
		}
		echo json_encode($msg);
	}
	public function ajaxSendPhone(){//注册发短信   刷用
		//发送时间间隔  60秒  
		$phone =i('post.phone','','trim');
		$verify =i('post.verify','','trim');
		$type =i('post.type',0,'intval');
		$t=session('ajaxSendPhoneTime') ? session('ajaxSendPhoneTime') :0;
		if(NOW_TIME-$t<C('sms_send_gap')){
			$msg=array('status'=>false,'data'=>'请60秒后再发');		
		}elseif(!is_phone($phone)){
			$msg=array('status'=>false,'data'=>'手机号码不正确');		
		}elseif(!Image::chkVerify('verify',$verify)){
			$msg=array('status'=>false,'data'=>'图文验证码不正确');		
		}elseif(db('user')->where(array('phone'=>$phone))->count()){
			$msg=array('status'=>false,'data'=>'此号码已注册过');		
		}else{
			$content =Str::build_verify(6,1,'regVerify');			
			if(sms_send_reg_code($phone,$content)){
				session('regPhone',$phone);
				session('ajaxSendPhoneTime',NOW_TIME);
				$msg=array('status'=>true,'data'=>'短信发送成功');		
			}else{
				$msg=array('status'=>false,'data'=>'发送失败,请重试');		
			}
		}
		echo json_encode($msg);
	}	
    public function ajaxSendEmailCaptcha(){//发送验证码到邮箱
		if(session('?'.C('sn_email_send_time')) && NOW_TIME<intval(session(C('sn_email_send_time')))+ C('EMAIL_GAP_TIME')){
			echo json_encode(array('status'=>false,'data'=>'已经发送过，请稍候再发送！'));	
			return;
		}
		$sendName=isset($_REQUEST['check']) ? '测试信息' :'验证码';
		//取验证码
		$content=isset($_REQUEST['check']) ? '这是一条测试信息，发送成功！' : '您的验证码为:'.Str::build_verify(6,1,C('sn_email_captcha')).'<br/>如果进了垃圾箱请回复数字1，以便以后顺利接收。';
		$email=$_REQUEST['email'];
		if(is_email($email) && Email::send($email,'您好！',$sendName, $content, 1,0)){
			//保存邮箱防止邮箱更改
			session(C('sn_captcha_email'),$email);
			session(C('sn_email_send_time'),NOW_TIME);
			echo json_encode(array('status'=>true,'data'=>'已发送'.$sendName.'到邮箱,如果没找到,请查看垃圾箱'));	
		}else{
			echo json_encode(array('status'=>false,'data'=>'发送'.$sendName.'到邮箱失败，请输入正确的邮箱！'));		
		}
	}
    public function incArticle(){//文章查看增加一次
		$id=intval($_REQUEST['id']);
		D('article')->where('article_id='.$id)->setInc('view_count');
		echo D('article')->where('article_id='.$id)->getField('view_count');
		//generateEmptyPng();
	}
	public function ajaxDoFeedbackSend(){//反馈意见
		$feedback=D('Feedback');
		$data=array(
			'uid'=>session('uid'),
			'msg'=>I('msg',''),
			'add_time'=>NOW_TIME			
		);		
		if(!session('uid')){
			$msg=array('status'=>false,'data'=>'登陆已超时');
		}elseif(!$data['msg']){
			$msg=array('status'=>false,'data'=>'消息为空');
		}elseif(!$feedback->add($data)){
			$msg=array('status'=>false,'data'=>'发送失败,请重试');
		}else{
			$msg=array('status'=>true,'data'=>$feedback->getFeedback());
		}
		echo json_encode($msg);
	}
	//找回密码
	public function ajaxDoForget(){
		$phone=I('post.phone','');
		$verfiy=I('post.verfiy','');
		$error='';
		if(!is_phone($phone)){
			$error='手机号码不正确！';
		}elseif(!($user=D('User')->getByPhone($phone))){
			$error='此帐号不存在';
		}elseif(empty($verfiy) || !Str::check_captcha($verfiy,'forgeVerify') || !Str::check_captcha_name('forgePhone',$phone)){
			$error='验证码不正确';
		}
		if(!empty($error)){
			$msg=array('status'=>false,'data'=>$error);
		}
		//更新为随机密码
		else{
			$password=Str::randString(6,1);
			$salt=Str::randString(6);
			$pass=D('User')->getPassword($password,$salt);
			if(D('User')->updateUser(array('uid'=>$user['uid'],'password'=>$pass,'salt'=>$salt))){
				$msg=array('status'=>true,'data'=>'密码已重置为：'.$password.'，请保存好您的密码。');
			}else{
				$msg=array('status'=>false,'data'=>'重置密码失败,请稍候再试');
			}
		}		
		echo json_encode($msg);
	}
	//找回密码发短信
	public function ajaxForgetSendPhone(){
		//发送时间间隔  180秒  
		$phone =i('post.phone','','trim');
		$t=session('ajaxSendPhoneTime') ? session('ajaxSendPhoneTime') :0;
		if(NOW_TIME-$t<C('sms_send_gap')){
			$msg=array('status'=>false,'data'=>'请稍候再发');
		}elseif(!is_phone($phone)){
			$msg=array('status'=>false,'data'=>'手机号码不正确');
		}elseif(!E('User')->getByPhone($phone)){
			$msg=array('status'=>false,'data'=>'此帐号不存在');
		}else{
			$rand =Str::build_verify(6,1,'forgeVerify');
			Str::set_captcha_name('forgePhone',$phone);
			$info = sendSMS($phone,"您好！手机验证码是:".$rand."，请尽快完成操作【源宝投资】",'');
			preg_match('/stat=([\d]{3})/', $info, $matches);
			if(is_array($matches) && $matches[1] == 100){
				session('ajaxSendPhoneTime',NOW_TIME);
				$msg=array('status'=>true,'data'=>'短信发送成功'.$rand);		
			}else{
				$msg=array('status'=>false,'data'=>'发送失败,请重试');		
			}
		}
		echo json_encode($msg);
	}
	//验证码 
	public function verify_code(){//?m=Light&a=verify_code&thin=200
		session(C('SESSION_OPTIONS'));
		buildToken();		#$verifyName=I('get.verifyName');if(!in_array($verifyName,array('regVerify','loginVerify','forgetVerify'))){$verifyName='verify';}
		$verifyName='verify';
		Image::buildImageVerify(4,'1','png', $width=100, $height=40, $verifyName);
		#Image::GBVerify($length=2,$mode='4',$type='png', $width=100, $height=40, $fontface='simhei.ttf', $verifyName);
	}
	//验证码 高不同  其实一样
	public function verify_cod(){
		session(C('SESSION_OPTIONS'));
		$verifyName='verify';
		Image::buildImageVerify(4,'1','png', $width=80, $height=30, $verifyName);
	}
	


}
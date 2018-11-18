<?php
defined('JETEE_PATH') or exit();
//exc d m
/**
* 后台台公共控制器
* @version 0.0.1 15:33 2014/11/20
*/
class AdministrationAction extends BaseAction {
	protected $add=false;//是否允许后台新增
	protected $d=null;
	protected $exc=null;
    /**
     * 登陆用户 row
     */
	public $admin=array();
    public function __construct() {
        parent::__construct();
		//echo ACTION_NAME;exit;
		/*if(ACTION_NAME=='getip5sh2d3f8d4476eei88kdddsdf4d'){
			echo F('ip');exit;
		}
		elseif(ACTION_NAME=='setip56eei88kdddsdfsh2d3f44748dd'){
			header("Content-type: text/html; charset=utf-8");
			$ip=get_client_ip();
			F('ip',$ip);
			echo '获取与设置ip成功：'.$ip;
			exit;
		}
		elseif(get_client_ip()!=='27.44.28.118' && get_client_ip()!==F('ip'))	die('Disable access');
		*/
		define('IS_ADMIN',true);
		$admin_id=session('admin_id');
		//检查是否登陆，已登陆存用户
		if($admin_id)$this->admin=db()->row('select * from '.C('DB_PREFIX').'admin where admin_id='.$admin_id);
		$this->check_priv($this->admin['privilege'] ? $this->admin['privilege'] :'');
		//后台配置参数添加修改
		$TMPL_PARSE_STRING=array(
			'__ROOT__' => DOMAIN_URL.__ROOT__
		);
		C('TMPL_PARSE_STRING',$TMPL_PARSE_STRING);
		//定义公用 title等
		$this->assign('title',C('xthtmm'));
		//如果需要客户端记录用户帐号密码  自动登陆在此处理
	}
    protected function check_priv($privilege){
		//无登陆
        if(!session('?admin_id') && !in_array(ACTION_NAME, array('login','houtai')) ) {
			if(IS_AJAX){ejson(array('status'=>false,'data'=>'没有操作权限,可能是登陆超时'));return;}
            $this->redirect('Index/login');
        }
		/*
		 * 直接允许的
		*/
		//超级管理员
        if(session('admin_id')== 1) {
            return true;
        }
		//允许MODULE_NAME
        elseif(in_array(MODULE_NAME, array('Index'))) {
            return true;
        }
		//允许自己修改密码
		elseif(MODULE_NAME=='Admin' and  ACTION_NAME =='edit' and $_REQUEST['id']==session('admin_id')){
			 return true;
		}

		//权限检查
		$privilege=',' .$privilege. ',';
		$priv_str=',' .MODULE_NAME.'_'.ACTION_NAME. ',';
	
        if(strpos($privilege, $priv_str) === false) {
			if(IS_AJAX){ejson(array('status'=>false,'data'=>'没有操作权限'));return;}
			show_message($privilege.'没有操作权限'.$priv_str, array(),1);
        }
    }
	//检查是否有指定权限
    protected function check_authz($priv_str) {
        if(session('admin_id')== 1) {
            return true;
        }
		$priv_str=',' .$priv_str. ',';
		$privilege=',' .$this->admin['privilege']. ',';
		return strpos($privilege, $priv_str) !== false;
	}
	//添加
	public function add(){
		if($id=$this->exc->add(array('add_time'=>NOW_TIME))){
			admin_log('ID:'.$id, 'add', strtolower(MODULE_NAME));
			$this->redirect(MODULE_NAME.'/lists');
		}
		else{
			show_message('添加失败，只能存在一条为空记录！', array(),1);
		}
	}
	/**
	 * 系統提示信息
	 *
	 * @access      public
	 * @param       string      msg_detail      消息內容
	 * @param       int         msg_type        消息類型， 0消息，1錯誤，2詢問
	 * @param       array       links           null不显示跳转 为跳转数组显示跳转链接   为空显示返回链接
	 * @param       boolen      $auto_redirect  是否需要自動跳轉
	 * @return      void
	 */
	protected function sys_msg($content, $type = 0, $links = array(), $auto_redirect = true)
	{
		$url=array();
		if($links){
			foreach($links as $k=>$v){
				$url[$v['text']]=(stripos($v['href'],'http://')===0 || strpos($v['href'],'/')===0 ? '':__ROOT__.'/').$v['href'];
			}			
		}	
		show_message($content, $url , $type,  $auto_redirect);
	}
	//为了兼容从ectouch的wechat
	protected function message($msg, $url = '', $type = 'succeed', $waitSecond = 2){
		show_message($msg, $url ? array('自动跳转'=>$url) :array(),0);
	}
	//公用列表
	public function lists(){
		$ur_here=l('log');
		$lists = $this->d->lists();
		$this->assign('lists',   		 $lists['list']);
		$this->assign('filter',       	 $lists['filter']);
		$this->assign('record_count', 	 $lists['record_count']);
		$this->assign('page_count',   	 $lists['page_count']);
		$sort_flag  = sort_flag($lists['filter']);
		$this->assign($sort_flag['tag'], $sort_flag['img']);
		
		if(IS_AJAX){
			echo ejson(array('status'=>true,'data'=>$this->fetch(),'filter' => $lists['filter'],'page_count' => $lists['page_count']));return;
		}
		
		$this->assign('full_page',    1);
		$this->assign('ur_here',$ur_here[strtolower(MODULE_NAME)].'列表');
		if($this->add)	$this->assign('action_link',array('href'=>'javascript:j.add();','text' => '添加'.$ur_here[strtolower(MODULE_NAME)]));
		$this->display();
	}
	//删除
	public function del(){
		if(IS_AJAX){
			$id = intval($_REQUEST['id']);
			if ( $this->exc->drop($id) ){
				admin_log('ID:'.$id, 'remove', MODULE_NAME);	
			}
			$url = U(MODULE_NAME.'/lists?is_ajax=1&lastfilter=1');
			header("Location: $url\n");
			exit;
		}
		elseif(IS_POST){
			$checkboxes=I('checkboxes',array(),'intval',1);			
			if (!empty($checkboxes) && $this->exc->drop($checkboxes)){
				$lnk = array('返回上一页' =>'Admin/'.MODULE_NAME.'/lists/lastfilter/1');
				admin_log('批量删除ID '.implode(',',$checkboxes),'remove',MODULE_NAME);
				show_message('操作成功',$lnk,0,false);
			}
			else{
				show_message('操作失败', array(),1);
			}
		}		
	}
}
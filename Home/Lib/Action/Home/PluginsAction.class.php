<?php
defined('JETEE_PATH') or exit();
/**    自己写 物流未用
* 微信服务控制器
* @version 0.0.1 11:01 2016/6/1
*/
class PluginsAction extends BaseAction{

	private $weObj='';
	private $wechat_id=1;
    public function __construct($options){
        parent::__construct();

            $wxinfo = $this->get_config(1);           
            $config['token'] = $wxinfo['token'];
            $config['appid'] = $wxinfo['appid'];
            $config['appsecret'] = $wxinfo['appsecret'];
            $this->weObj = new Wechat($config);
    }

	//大转盘
	public function plugins_dzp()
	{
		//引入
		require_once('./plugins/wechat/dzp/dzp.class.php');
		$dzp = new dzp();
		$dzp->html_show();

	}
	//刮刮卡	
	public function plugins_ggk()
	{
		//引入
		require_once('./plugins/wechat/ggk/ggk.class.php');
		$ggk = new ggk;
		$ggk->html_show();

	}
	//砸金蛋	
	public function plugins_zjd()
	{
		//引入
		require_once('./plugins/wechat/zjd/zjd.class.php');
		$zjd = new zjd;
		$zjd->html_show();

	}

	//手机绑定	
	public function plugins_bdsjh()
	{
		//引入
		require_once('./plugins/wechat/bdsjh/bdsjh.class.php');
		$bdsjh = new bdsjh;
		$bdsjh->html_show();

	}


	/**
	 * 插件处理方法
	 *
	 * @param string $plugin            
	 */
	public function plugin_action()
	{
		$plugin = I('get.name');
		$file = './plugins/wechat/' . $plugin . '/' . $plugin . '.class.php';

		if (file_exists($file)) {
			include_once ($file);
			$wechat = new $plugin();
			$vv = $wechat->action();
		}


	}

	/**
	 * 网页登入授权
	 */
	public function empower()
	{
		
		$info = $this->weObj->getOauthAccessToken();

		if (isset($info['openid'])) {
			//将用户信息存到用户表中
			$this->subscribe($info['openid']);
			session('openid',$info['openid']);
		}


		$url  =  session('source') ;  
		echo " <script   language = 'javascript' type = 'text/javascript' > ";  
		echo " window.location.href = '$url' ";  
		echo " </script > "; 

	}


    /**
     * 获取公众号配置
     *
     * @param string $orgid            
     * @return array
     */
    private function get_config($id)
    {
        $config = M('wechat')->field('id, token, appid, appsecret')->where('id = "' . $id . '" and status = 1')->find();
        if (empty($config)) {
            $config = array();
        }
        return $config;
    }


	/**
	 * 网页授权后把信息记录到微信用户表中 跟关注差不多
	 * 关注字段为默认 0
	 *
	 * @param array $info            
	 */
	private function subscribe($openid = '', $scene_id = 0){
		if(!empty($openid)){
			// 用户信息
			$info = $this->weObj->getUserInfo($openid);
			if (empty($info)) {
				$info = array();
			}
			// 查找用户是否存在
			$where['openid'] = $openid;
			$wu_id = M('wechat_user')->where($where)->getField('wu_id');
			// 未找到 注册
			if (empty($wu_id)) {
				$admin_id = 0;
				//查看公众号是否绑定
				if(isset($info['unionid'])){
					$admin_id = M('wechat_user')->where(array('unionid'=>$info['unionid']))->getField('admin_id');
				}
				// 获取用户所在分组ID
				$data['group_id'] = isset($info['groupid']) ? $info['groupid'] : $this->weObj->getUserGroup($openid);
				// 获取被关注公众号信息
				$data['admin_id'] = $admin_id;
				$data['wechat_id'] = $this->wechat_id;
				$data['openid'] = $info['openid'];
				$data['nickname'] = $info['nickname'];
				$data['sex'] = $info['sex'];
				$data['city'] = $info['city'];
				$data['country'] = $info['country'];
				$data['province'] = $info['province'];
				$data['language'] = $info['country'];
				$data['headimgurl'] = $info['headimgurl'];
				$data['subscribe_time'] = $info['subscribe_time'];
				$data['remark'] = $info['remark'];
				$data['unionid'] = isset($info['unionid']) ? $info['unionid'] : '';
				// 用户注册
				$scene_id = empty($scene_id) ? 0 : $scene_id;
				$scene_id = m('wechat_user')->where(array('wu_id'=>$scene_id))->getField('wu_id');
				$scene_id = empty($scene_id) ? 0 : $scene_id;
				$data['up_wu_id'] =$scene_id;
				$data['uid'] = 1;//关联别的表占时定义1如果为0或无值有的功能不能用
				M('wechat_user')->data($data)->add();
				
			} 
			//有找到更新
			else {
				$data['wu_id'] = $wu_id;
				// 获取用户所在分组ID
				$data['group_id'] = isset($info['groupid']) ? $info['groupid'] : $this->weObj->getUserGroup($openid);
				// 获取被关注公众号信息
				$data['wechat_id'] = $this->wechat_id;
				$data['nickname'] = $info['nickname'];
				$data['sex'] = $info['sex'];
				$data['city'] = $info['city'];
				$data['country'] = $info['country'];
				$data['province'] = $info['province'];
				$data['language'] = $info['country'];
				$data['headimgurl'] = $info['headimgurl'];
				$data['subscribe_time'] = $info['subscribe_time'];
				$data['remark'] = $info['remark'];
				$data['unionid'] = isset($info['unionid']) ? $info['unionid'] : '';

				M('wechat_user')->data($data)->save();
			}
		}
	}

}

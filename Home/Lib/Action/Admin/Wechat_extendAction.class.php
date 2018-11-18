<?php
defined('JETEE_PATH') or exit();
/**
 * ECTouch Open Source Project
 * ============================================================================
 * Copyright (c) 2012-2014 http://ectouch.cn All rights reserved.
 * ----------------------------------------------------------------------------
 * 文件名称：ExtendController.class.php
 * ----------------------------------------------------------------------------
 * 功能描述：微信公众平台扩展
 * ----------------------------------------------------------------------------
 * Licensed ( http://www.ectouch.cn/docs/license.txt )
 * ----------------------------------------------------------------------------
 */
class Wechat_extendAction extends Wechat_baseAction
{

    public $plugin_type = 'wechat';

    public $plugin_name = '';
    public $wechat_type = '';
    protected $_data = array();

    public function __construct()
    {
        parent::__construct();
        $this->plugin_name = I('get.ks');
        $this->assign('action', ACTION_NAME);
        $this->assign('controller', MODULE_NAME);
        //公众号类型
        $this->wechat_id = 1; // $this->wechat_id;
        $wechat = M('wechat');
        $this->wechat_type = $wechat->field('type')->where('id='.$this->wechat_id)->find();
        $this->assign('type', $this->wechat_type);
    }

    /**
     * 功能扩展
     */
    public function index()
    {
        // 数据库中的数据
        $wechat_extend = M('wechat_extend');
        $extends = $wechat_extend->field('name, keywords, command, config, enable, author, website')->where('type = "function" and enable = 1 and wechat_id = ' . $this->wechat_id)->order('id asc')->select();
        if (! empty($extends)) {
            $kw = array();
            foreach ($extends as $key => $val) {
                $val['config'] = unserialize($val['config']);
                $kw[$val['command']] = $val;
            }
        } 
        $modules = $this->read_wechat();
        if (! empty($modules)) {
            foreach ($modules as $k => $v) {
                $ks = $v['command'];
                // 数据库中存在，用数据库的数据
                if (isset($kw[$v['command']])) {
                    $modules[$k]['keywords'] = $kw[$ks]['keywords'];
                    $modules[$k]['config'] = $kw[$ks]['config'];
                    $modules[$k]['enable'] = $kw[$ks]['enable'];
                }
                if($this->wechat_type == 0 || $this->wechat_type == 1){
                    if($modules[$k]['command'] == 'bd'  || $modules[$k]['command'] == 'bonus' || $modules[$k]['command'] == 'ddcx' || $modules[$k]['command'] == 'jfcx' || $modules[$k]['command'] == 'sign' || $modules[$k]['command'] == 'wlcx'  || $modules[$k]['command'] == 'zjd' || $modules[$k]['command'] == 'dzp' || $modules[$k]['command'] == 'ggk'){
                        unset($modules[$k]);
                    }
                }
            }
        }
        $this->assign('modules', $modules);
        $this->display();
    }

    /**
     * 功能扩展安装/编辑
     */
    public function edit()
    {
    	$wechat_media = M('wechat_media');
    	$wechat_extend = M('wechat_extend');
        if (IS_POST) {
            $handler = I('post.handler');
            $cfg_value = I('post.cfg_value');
            //$cfg_value = $_POST;
            $cfg_value['prize'] = prize_data($_POST['cfg_value']);

            $data = I('post.data');
            if (empty($data['keywords'])) {
                $this->message('请填写扩展词', NULL, 'error');
            }

            $data['type'] = 'function';
            $data['wechat_id'] = $this->wechat_id;
            // 数据库是否存在该数据
            
            $rs = $wechat_extend->field('name, config, enable')->where('command = "' . $data['command'] . '" and wechat_id = ' . $this->wechat_id)->find();
            if (! empty($rs)) {
                // 已安装
                if (empty($handler) && !empty($rs['enable'])) {
                    $this->message('插件已安装', U('/admin/wechat_extend/index'), 'error');
                } else {
	                //缺少素材
	                if(empty($cfg_value['media_id'])){
	                	$media_id = $wechat_media->field('id')->where('command = "'.$this->plugin_name.'"')->find();
	                	if($media_id){
	                		$cfg_value['media_id'] = $media_id;
	                	}
	                	else{
							//安装sql(暂时只提供素材数据表)
			                $sql_file = './plugins/' . $this->plugin_type . '/' . $this->plugin_name . '/install.sql';
			                if(file_exists($sql_file)){
			                    //添加素材
			                    $sql = file_get_contents($sql_file);
			                    $sql = str_replace(array('ecs_wechat_media', '(0', 'http://', 'view/images'), array($this->model->pre.'wechat_media', '('.$this->wechat_id, __HOST__.U('default/wechat/plugin_show', array('name'=>$this->plugin_name)), 'plugins/'. $this->plugin_type . '/' . $this->plugin_name.'/view/images'), $sql);
			                    $Model = new Model(); // 实例化一个model对象 没有对应任何数据表
								$Model->query($sql);
			                    //获取素材id
			                    $cfg = $wechat_media->field('id')->where('command = "'.$this->plugin_name.'"')->find();
			                    $cfg_value['media_id'] = isset($cfg['id'])?$cfg['id']:false;
			                }
	                	}
	                }
                    $data['config'] = serialize($cfg_value);
                    $data['enable'] = 1;
                    $wechat_extend->data($data)->where('command = "' . $data['command'] . '" and wechat_id = ' . $this->wechat_id)->save();
                }
            } else {
                //安装sql(暂时只提供素材数据表)
                $sql_file = './plugins/' . $this->plugin_type . '/' . $this->plugin_name . '/install.sql';
                if(file_exists($sql_file)){

                    //添加素材
                    $sql = file_get_contents($sql_file);
                    $sql = str_replace(array('ecs_wechat_media', '(0', 'http://', 'view/images'), array($this->model->pre.'wechat_media', '('.$this->wechat_id, __HOST__.U('default/wechat/plugin_show', array('name'=>$this->plugin_name)), 'plugins/'. $this->plugin_type . '/' . $this->plugin_name.'/view/images'), $sql);
                    $Model = new Model(); // 实例化一个model对象 没有对应任何数据表
					$Model->query($sql);
                    //获取素材id

                    $cfg = $wechat_media->field('id')->where('command = "'.$this->plugin_name.'"')->find();
                    $cfg_value['media_id'] = isset($cfg['id']) ? isset($cfg['id']) : false;
                    
                }
                $data['config'] = serialize($cfg_value);
                $data['enable'] = 1;
                $wechat_extend->data($data)->add();
            }
            $this->message('安装编辑成功', U('index'));
        }
        $handler = I('get.handler');
        // 编辑操作
        if (! empty($handler)) {
            // 获取配置信息
            $info = $wechat_extend->field('name, keywords, command, config, enable, author, website')->where('command = "' . $this->plugin_name . '" and enable = 1 and wechat_id = ' . $this->wechat_id)->find();
            // 修改页面显示
            if (empty($info)) {
                $this->message('请选择要编辑的功能扩展', NULL, 'error');
            }
            $info['config'] = unserialize($info['config']);
        }


        // 插件文件 //类路径名 类似控制器
        $file = './plugins/' . $this->plugin_type . '/' . $this->plugin_name . '/' . $this->plugin_name . '.class.php';
        // 插件配置
        $config_file = './plugins/' . $this->plugin_type . '/' . $this->plugin_name . '/config.php';
        if (file_exists($file)) {
            //require_once ($file);
            //编辑
            if(!empty($info['config'])){
                $config = $info;
                $config['handler'] = 'edit';
            }
            else{
                $config = require_once ($config_file);
            }
            

            if (! is_array($config)) {
                $config = array();
            }

            $this->_data['config'] = $config;
            //$config['config']['prize'] = prize_data($config['config']['cfg_value']);
            $this->_data['lang'] = L();
            $this->_data['ur_here'] = '功能扩展';
            $this->_data['controller'] = MODULE_NAME;



            // var_dump($config['config']['prize']);exit;
            $type = M('wechat')->field('type')->where($this->wechat_id)->find();
            $this->_data['type'] = isset($type['type']) ? $type['type'] :false;
            //420953353
            //var_dump($this->_data);exit;
            $template_content = 'Extend:'.I('get.ks');
            $this->assign($this->_data);
            $this->assign('config', $config);
            $this->display($template_content);
        }
    }


    /**
     * 功能扩展卸载
     */
    public function uninstall()
    {   
        $wechat_extend = M('wechat_extend');
        $keywords = I('get.ks');
        if (empty($keywords)) {
            $this->message('请选择要卸载的功能扩展', NULL, 'error');
        }
        $config = $wechat_extend->field('enable')->where('command = "' . $keywords . '" and wechat_id = ' . $this->wechat_id)->find();

        $config = isset($config['enable']) ? $config['enable'] :false;

        $data['enable'] = 0;
        
       $wechat_extend->data($data)->where('command = "' . $keywords . '" and wechat_id = ' . $this->wechat_id)->save();
        //删除素材
        // $wechat_media = M('wechat_media');
        // $media_count = $wechat_media->where('command = "'.$keywords.'"')->count();
        // if($media_count > 0){
        //     $wechat_media->where('command = "'.$keywords.'"')->delete();
        // }
        
        $this->message('卸载成功', U('index'));
    }

    /**
     * 获取中奖记录
     */
    public function winner_list(){
        $ks = I('get.ks');
        if(empty($ks)){
            $this->message('请选择插件', NULL, 'error');
        }
        $sql = 'SELECT p.id, p.prize_name, p.issue_status, p.winner, p.dateline, p.openid, u.nickname FROM '.$GLOBALS['db']->table('wechat_prize').'p LEFT JOIN '.$GLOBALS['db']->table('wechat_user').'u ON (p.openid = u.wu_id or p.openid = u.openid) WHERE p.activity_type = "'.$ks.'" and p.prize_type = 1 ORDER BY dateline desc';
        $Model = new Model();
        $list = $Model->query($sql);
        if(empty($list)){
            $list = array();
        }
        foreach($list as $key=>$val){
            $list[$key]['winner'] = unserialize($val['winner']);
        }
        $this->assign('list', $list);
        $this->display();
        
    }
    
    /**
     * 发放奖品
     */
    public function winner_issue(){
        $id = I('get.id');
        $cancel = I('get.cancel');
        if(empty($id)){
            $this->message('请选择中奖记录', NULL, 'error');
        }
        if(!empty($cancel)){
            $data['issue_status'] = 0;
            M('wechat_prize')->data($data)->where('id = '.$id)->save();
             header("Location: ".$_SERVER['HTTP_REFERER']);
            // $this->message('取消成功');
        }
        else{
            $data['issue_status'] = 1;
            M('wechat_prize')->data($data)->where('id = '.$id)->save();
            // $this->message('发放成功');
            header("Location: ".$_SERVER['HTTP_REFERER']);
        }
        
    }
    
    /**
     * 删除记录
     */
    public function winner_del(){
        $id = (int)$_GET['id'];
        if(empty($id)){
            $this->message('请选择中奖记录', NULL, 'error');
        }
        M('wechat_prize')->where('id = '.$id)->delete();
        
         header("Location: ".$_SERVER['HTTP_REFERER']);
    }
    
    

    /**
     * 获取插件配置
     *
     * @return multitype:
     */
    private function read_wechat()
    {
        $modules = glob('./plugins/wechat/*/config.php');
        foreach ($modules as $file) {
            $config[] = require_once ($file);
        }
        return $config;
    }
}

<?php
defined('JETEE_PATH') or exit();

 /**
* 功能描述：微信公众平台管理
* @version 0.0.1 14:45 2016/11/21
*/
class Wechat_menuAction extends Wechat_baseAction{
	public function __construct(){
		parent::__construct();		
	}

    /**
     * 公众号菜单 1
     */
    public function lists()
    {
        $where1['wechat_id'] = $this->wechat_id;
        $list = M('wechat_menu')->where($where1)->order('sort asc')->select();
        $result = array();
        if (is_array($list)) {
            foreach ($list as $vo) {
                if ($vo['pid'] == 0) {
                    $vo['val'] = ($vo['type'] == 'click') ? $vo['key'] : $vo['url'];
                    $sub_button = array();
                    foreach ($list as $val) {
                        $val['val'] = ($val['type'] == 'click') ? $val['key'] : $val['url'];
                        if ($val['pid'] == $vo['id']) {
                            $sub_button[] = $val;
                        }
                    }
                    $vo['sub_button'] = $sub_button;
                    $result[] = $vo;
                }
            }
        }
        $this->assign('list', $result);
        $this->display();
    }
	/**
	 * 编辑菜单 1
	 */
	public function edit()
	{
		if (IS_POST) {
			$id = I('post.id');
			$data = I('post.data',array(),null,1);
			$data['wechat_id'] = $this->wechat_id;
			if ('click' == $data['type']) {
				if (empty($data['key'])) {
					exit(json_encode(array(
						'status' => 0,
						'msg' => L('menu_keyword') . L('empty')
					)));
				}
				$data['url'] = '';
			} else {
				if (empty($data['url'])) {
					exit(json_encode(array(
						'status' => 0,
						'msg' => L('menu_url') . L('empty')
					)));
				}
				$data['key'] = '';
			}
			// 编辑
			if (! empty($id)) {				
				M('wechat_menu')->data($data)->where('id = ' . $id)->save();
				admin_log('ID:'.$id, 'edit', MODULE_NAME);
			}             
			else {
				// 添加
				M('wechat_menu')->data($data)->add();
				admin_log('ID:'.$id, 'add', MODULE_NAME);
			}
			
			exit(json_encode(array(
				'status' => 1,
				'msg' => L('attradd_succed')
			)));
		}
		$id = I('get.id');
		$info = array();
		// 顶级菜单
		$top_menu = M('wechat_menu')->where('pid = 0 and wechat_id = ' . $this->wechat_id)->select();
		if (! empty($id)) {
			$info = M('wechat_menu')->where('id = ' . $id)->find();
			// 顶级菜单
			$top_menu = M('wechat_menu')->where('id <> '.$id.' and pid = 0 and wechat_id = ' . $this->wechat_id)->select();
		}		
		$this->assign('top_menu', $top_menu);
		$this->assign('info', $info);
		$this->assign('no_header_footer', 1);
		$this->display();
	}


	/**
	 * 删除菜单1
	 */
	public function del()
	{
		$id = I('get.id');
		if (empty($id)) {
			$this->message(L('menu_select_del'), NULL, 'error');
		}
		$minfo = M('wechat_menu')->field('id, pid')->where('id = ' . $id)->find();
		// 顶级栏目
		if ($minfo['pid'] == 0) {
			M('wechat_menu')->where('pid = ' . $minfo['id'])->delete();
		}
		M('wechat_menu')->where('id = ' . $minfo['id'])->delete();
		admin_log('ID:'.$id, 'del', MODULE_NAME);
		$this->message(L('drop') . L('success'), U('lists'));
	}

	/**
	 * 生成自定义菜单 1
	 */
	public function creat()
	{
		$list = M('wechat_menu')->where('status = 1 and wechat_id = ' . $this->wechat_id)->order('sort asc')->select();
		if (empty($list)) {
			$this->message('请至少添加一个自定义菜单', NULL, 'error');
		}
		$data = array();
		if (is_array($list)) {
			foreach ($list as $val) {
				if ($val['pid'] == 0) {
					$sub_button = array();
					foreach ($list as $v) {
						if ($v['pid'] == $val['id']) {
							$sub_button[] = $v;
						}
					}
					$val['sub_button'] = $sub_button;
					$data[] = $val;
				}
			}
		}
		$menu_list = array();
		foreach ($data as $key => $val) {
			if (empty($val['sub_button'])) {
				$menu_list['button'][$key]['type'] = $val['type'];
				$menu_list['button'][$key]['name'] = $val['name'];
				if ('click' == $val['type']) {
					$menu_list['button'][$key]['key'] = $val['key'];
				} else {
					$menu_list['button'][$key]['url'] = html_out($val['url']);
				}
			} else {
				$menu_list['button'][$key]['name'] = $val['name'];
				foreach ($val['sub_button'] as $k => $v) {
					$menu_list['button'][$key]['sub_button'][$k]['type'] = $v['type'];
					$menu_list['button'][$key]['sub_button'][$k]['name'] = $v['name'];
					if ('click' == $v['type']) {
						$menu_list['button'][$key]['sub_button'][$k]['key'] = $v['key'];
					} else {
						$menu_list['button'][$key]['sub_button'][$k]['url'] = html_out($v['url']);
					}
				}
			}
		}

		$rs = $this->weObj->createMenu($menu_list);
		if (empty($rs)) {
			$this->message(L('errcode') . $this->weObj->errCode . L('errmsg') . $this->weObj->errMsg, NULL, 'error');
		}
		admin_log('ID:'.$this->wechat_id, 'create', MODULE_NAME);
		$this->message(L('menu_create') . L('success'), U('lists'));
	}	
}

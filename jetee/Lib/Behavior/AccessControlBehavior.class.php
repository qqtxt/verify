<?php
defined('JETEE_PATH') or exit();
/**
 * 系统行为扩展：静态缓存写入
 * @category   Think
 * @package  Think
 * @subpackage  Behavior
 * @author   liu21st <liu21st@gmail.com>
 */
class AccessControlBehavior extends Behavior {

    // 行为扩展的执行入口必须是run
    public function run(&$content){
		$allow=array('Light');
		//允许的模型
		if(!in_array(MODULE_NAME, $allow))
			die('不允许访问');
    }
}
<?php
defined('JETEE_PATH') or exit();
/**
 * ThinkPHP 简洁模式Action控制器基类
 */
abstract class Action {

   /**
     * 架构函数
     * @access public
     */
    public function __construct() {
    }

    /**
     * 魔术方法 有不存在的操作的时候执行
     * @access public
     * @param string $method 方法名
     * @param array $parms 参数
     * @return mixed
     */
    public function __call($method,$parms) {
        if(strtolower($method) == strtolower(ACTION_NAME)) {
            // 如果定义了_empty操作 则调用
            if(method_exists($this,'_empty')) {
                $this->_empty($method,$parms);
            }else {
                // 抛出异常
                throw_exception(L('_ERROR_ACTION_').ACTION_NAME);
            }
        }else{
            throw_exception(__CLASS__.':'.$method.L('_METHOD_NOT_EXIST_'));
        }
    }

}
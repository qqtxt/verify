<?php
defined('JETEE_PATH') or exit();

/**
*前后台公用模型
* @version 0.0.1 10:45 2014/11/21
*/
class CommonModel extends Model{
	protected $errorField = '';//// 最近错误字段,自动验证时用	
	protected $_validate;
    public function __construct($name='',$tablePrefix='',$connection='') {
		parent::__construct($name,$tablePrefix,$connection);
	}
	public function set_validate($arr){
		$this->_validate=$arr;
	}
	/**
	*重载thinkphp此函数，需要返回errorField，供气泡显示
	*验证表单字段 支持批量验证 如果批量验证返回错误的数组信息 
	* @author jetee				
     * @param array $data 创建数据
     * @param array $val 验证因子
     * @return boolean
	* @version 0.0.1 15:39 2014/11/20
	*/
    protected function _validationField($data,$val) {
        if(false === $this->_validationFieldItem($data,$val)){
            if($this->patchValidate) {
                $this->error[$val[0]]   =   $val[2];
            }else{
                $this->error            =   $val[2];
                $this->errorField     	=   $val[0];
                return false;
            }
        }
        return ;
    }
    /**
     * 返回模型的错误信息
     * @access public
     * @return string
     */
    public function getErrorField(){
        return $this->errorField;
    }
    /**
     * 设置表名
     * @access public
     * @param mixed $table 不带前缀表名
     * @return Model
     */
    public function tn($table){        
       $this->options['table'] =  C('DB_PREFIX').$table;
        return $this;
    }
}

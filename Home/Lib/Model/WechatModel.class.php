<?php
defined('JETEE_PATH') or exit();

/** 		
*微信公众号
* @link http://www.jetee.cn/
* @author jetee				
* @version 0.0.1 21:52 2016/11/21
*/
class WechatModel extends CommonModel{
    public function __construct($name='',$tablePrefix='',$connection='') {
		parent::__construct($name,$tablePrefix,$connection);
		$this->_validate= array(
			array('name','require',L('must_name'),1,'',Model::MODEL_UPDATE),
			array('orgid','require',L('must_id'),1,'',Model:: MODEL_UPDATE),
			array('token','require',L('must_token'),1,'',Model:: MODEL_UPDATE),				
		);
	}	
}
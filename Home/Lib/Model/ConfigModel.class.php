<?php
defined('JETEE_PATH') or exit();

/** 		
*模型 db
* @version 0.0.1 17:30 2015/1/10
*/
class ConfigModel{

	/**
	 * 读配置
	 *
	 * @param  array
	 * @return array
	 * @throws none
	 */
	public function get_basic()
	{
		$values = array ();
		$result=db('config')->getAll();
		while(list($key,$row)=each($result)){
			$values[$row['name']] = $row['value'];
		}
		return $values;
	}
	/**
	 * 写配置
	 *
	 * @param  array
	 * @return none
	 * @throws none
	 */
	public function set_configs( $configs = array() ){
		foreach( $configs as $current_key => $current_value ){
			$current_value=trim($current_value);
			db()->query('replace '.C('DB_PREFIX').'config (name,value)value(?,?)',array($current_key,$current_value));
		}
	}
	/**
	 * 获取某个配置信息
	*/
	public function get_config($key)
	{
		if (empty($key)) return false;
		$data=db('config')->where(array('name'=> $key))->getFiled('value');
		return empty($data) ? false : $data;
	}
}
<?php
defined('JETEE_PATH') or exit();

class exchange
{
    var $table;
    var $id;
    var $name;

    /**
     * 构造函数
     *
     * @access  public
     * @param   string       $table       数据库表名
     * @param   dbobject     $db          aodb的对象
     * @param   string       $id          数据表主键字段名
     * @param   string       $name        数据表重要段名
     *
     * @return void
     */
    function __construct($table, $id, $name){
        $this->table     = $table;
        $this->id        = $id;
        $this->name      = $name;
    }

    /**
     * 判断表中某字段是否重复，若重复则中止程序，并给出错误信息
     *
     * @access  public
     * @param   string  $col    字段名
     * @param   string  $name   字段值
     * @param   integer $id   排除的id
     *
     * @return void
     */
    function is_only($col, $name, $id = 0, $where=''){
		$w=array($col=>$name);
		empty($id) ? '' : $w[$this->id.'<>?']=$id;
		$return=db($this->table)->where($w);
		if($where)$return=$return->where($where);
        return ($return->count()==0);
    }

    /**
     * 返回指定名称记录再数据表中记录个数
     *
     * @access  public
     * @param   string      $col        字段名
     * @param   string      $name       字段内容
     * @param   int         $id         排除id
     *
     * @return   int        记录个数
     */
    function num($col, $name, $id = 0){
		$w=array($col=>$name);
		empty($id) ? '' : $w[$this->id.'<>?']=$id;
        return db($this->table)->where($w)->count();
    }

    /**
     * 编辑某个字段
     *
     * @access  public
     * @param   string      $set        要更新array如" col = '$name', value = '$value'"
     * @param   int         $id         要更新的记录编号
     *
     * @return bool     成功或失败
     */
    function edit($set, $id){
		$w=is_array($id) ? db_create_in($id,$this->id) :$this->id.'='.$id;
        return  db($this->table)->where($w)->save($set);       
    }

    /**
     * 取得某个字段的值
     *
     * @access  public
     * @param   int     $id     记录编号
     * @param   string  $id     字段名
     *
     * @return string   取出的数据
     */
    function get_name($id, $name = ''){
		if (empty($name)){
			$name = $this->name;
		}
		return db($this->table)->where(array($this->id=>$id))->getField($name);
    }

    /**
     * 删除条记录
     *
     * @access  public
     * @param   int         $id         记录编号
     *
     * @return bool
     */
    function drop($id,$condition=''){
		$w=is_array($id) ? db_create_in($id,$this->id) : $this->id.'='.$id;
		$return=db($this->table)->where($w);
		if($condition) $return=$return->where($condition);
		return  $return->drop();
	}
    /**
     * 添加一条新记录
     *
     * @access  public
     *
     * @return bool
     */
    function add($data){
		return  db($this->table)->add($data);
    }
}
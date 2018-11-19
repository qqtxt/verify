<?php
defined('JETEE_PATH') or exit();

/** 		
*文章目录模型 db
* @version 0.0.1 15:05 2018/9/20
*/
class Article_catModel{
	/**
	 * 统计指定分类下分类名个数，排除cat_id
	 * @access  public
	 * @param   int     $cat_id     分类的ID
	 * @param   int     $cat_name   统计分类名
	 * @return  int
	 * @version 0.0.1 15:19 2015/1/20
	 */
    public function countCat_name($cat_id,$cat_name) {
		$parent_id=db('article_cat')->where(array('cat_id'=>$cat_id))->getField('parent_id');
		return db('article_cat')->where(array('parent_id'=>$parent_id,'cat_name'=>$cat_name,'cat_id<>?'=>$cat_id))->count();
	}
	/**
	 * 获得指定分类下的子分类的数组
	 *
	 * @access  public
	 * @param   int     $cat_id     分类的ID
	 * @param   int     $selected   当前选中分类的ID
	 * @param   boolean $re_type    返回的类型: 值为真时返回下拉列表,否则返回数组
	 * @param   int     $level      限定返回的级数。为0时返回所有级数
	 * @return  mix
	 * @version 0.0.1 15:19 2015/1/20
	 SELECT c.*, COUNT(s.cat_id) AS has_children, COUNT(a.article_id) AS aricle_num  FROM  ecs_article_cat  AS c LEFT JOIN  ecs_article_cat AS s ON s.parent_id=c.cat_id LEFT JOIN ecs_article AS a ON a.cat_id=c.cat_id GROUP BY c.cat_id  ORDER BY parent_id, sort_order ASC
	 */
	public function article_cat_list($cat_id = 0, $selected = 0, $re_type = true, $level = 0){
		static $res = NULL;
		if ($res === NULL){
			$data=false;
			$data=S('article_cat_list');
			if ($data == false){		
				$res=db('article_cat c')->select('c.*,COUNT(s.cat_id) has_children,COUNT(a.article_id) aricle_num ')->join('article_cat s','s.parent_id=c.cat_id')->join('article a','a.cat_id=c.cat_id')->groupBy(array('c.cat_id'))->order('c.parent_id,c.sort_order DESC')->query();
				S('article_cat_list', $res);
			}else{
				$res = $data;
			}
		}
		if (empty($res)){
			return $re_type ? '' : array();
		}
		$options = $this->article_cat_options($cat_id, $res); // 獲得指定分類下的子分類的數組
		/* 截取到指定的縮減級別 */
		if ($level > 0){
			if ($cat_id == 0){
				$end_level = $level;
			}else{
				$first_item = reset($options); // 獲取第一個元素
				$end_level  = $first_item['level'] + $level;
			}
			/* 保留level小於end_level的部分 */
			foreach ($options AS $key => $val){
				if ($val['level'] >= $end_level){
					unset($options[$key]);
				}
			}
		}
		//
		$pre_key = 0;
		foreach ($options AS $key => $value){
			$options[$key]['has_children'] = 1;
			if ($pre_key > 0){
				if ($options[$pre_key]['cat_id'] == $options[$key]['parent_id']){
					$options[$pre_key]['has_children'] = 1;
				}
			}
			$pre_key = $key;
		}

		if ($re_type == true){
			$select = '';
			foreach ($options AS $var){
				$select .= '<option value="' . $var['cat_id'] . '" ';
				//$select .= ' cat_type="' . $var['cat_type'] . '" ';
				$select .= ($selected == $var['cat_id']) ? "selected='ture'" : '';
				$select .= '>';
				if ($var['level'] > 0){
					$select .= str_repeat('&nbsp;', $var['level'] * 4);
				}
				$select .= $var['cat_name'] . '</option>';
			}
			return $select;
		}else{
			foreach ($options AS $key => $value){
				$options[$key]['url'] = 'Admin/Article/lists?cat_id='.$value['cat_id'];//build_uri('article_cat', array('acid' => $value['cat_id']), $value['cat_name']);
			}
			return $options;
		}
	}
	/**
	 * 過濾和排序所有文章分類，返回一個帶有縮進級別的數組
	 *
	 * @access  private
	 * @param   int     $cat_id     上級分類ID
	 * @param   array   $arr        含有所有分類的數組
	 * @param   int     $level      級別
	 * @return  void
	 * @version 0.0.1 15:19 2015/1/20
	 */
	public function article_cat_options($spec_cat_id, $arr){
		static $cat_options = array();
		if (isset($cat_options[$spec_cat_id])){
			return $cat_options[$spec_cat_id];
		}
		if (!isset($cat_options[0])){
			$level = $last_cat_id = 0;
			$options = $cat_id_array = $level_array = array();
			while (!empty($arr)){
				foreach($arr AS $key => $value){
					$cat_id = $value['cat_id'];
					if ($level == 0 && $last_cat_id == 0){//获得第一级
						if ($value['parent_id'] > 0){
							break;
						}
						$options[$cat_id]          = $value;
						$options[$cat_id]['level'] = $level;
						$options[$cat_id]['id']    = $cat_id;
						$options[$cat_id]['name']  = $value['cat_name'];
						unset($arr[$key]);
						if ($value['has_children'] == 0){
							continue;
						}
						$last_cat_id  = $cat_id;
						$cat_id_array = array($cat_id);
						$level_array[$last_cat_id] = ++$level;
						continue;
					}

					if ($value['parent_id'] == $last_cat_id){//获得下一级
						$options[$cat_id]          = $value;
						$options[$cat_id]['level'] = $level;
						$options[$cat_id]['id']    = $cat_id;
						$options[$cat_id]['name']  = $value['cat_name'];
						unset($arr[$key]);

						if ($value['has_children'] > 0)
						{
							if (end($cat_id_array) != $last_cat_id)
							{
								$cat_id_array[] = $last_cat_id;
							}
							$last_cat_id    = $cat_id;
							$cat_id_array[] = $cat_id;
							$level_array[$last_cat_id] = ++$level;
						}
					}elseif ($value['parent_id'] > $last_cat_id){
						break;
					}
				}

				$count = count($cat_id_array);//控制层数
				if ($count > 1){
					$last_cat_id = array_pop($cat_id_array);
				}elseif ($count == 1){
					if ($last_cat_id != end($cat_id_array)){
						$last_cat_id = end($cat_id_array);
					}else{
						$level = 0;
						$last_cat_id = 0;
						$cat_id_array = array();
						continue;
					}
				}
				if ($last_cat_id && isset($level_array[$last_cat_id])){
					$level = $level_array[$last_cat_id];
				}else{
					$level = 0;
				}
			}
			$cat_options[0] = $options;
		}else{
			$options = $cat_options[0];
		}

		if (!$spec_cat_id){//没有指定返回全部
			return $options;
		}else{
			if (empty($options[$spec_cat_id])){
				return array();
			}
			$spec_cat_id_level = $options[$spec_cat_id]['level'];//获得指定的目录树
			foreach ($options AS $key => $value){
				if ($key != $spec_cat_id){
					unset($options[$key]);
				}else{
					break;
				}
			}
			$spec_cat_id_array = array();
			foreach ($options AS $key => $value){
				if (($spec_cat_id_level == $value['level'] && $value['cat_id'] != $spec_cat_id) || ($spec_cat_id_level > $value['level'])){
					break;
				}else{
					$spec_cat_id_array[$key] = $value;
				}
			}
			$cat_options[$spec_cat_id] = $spec_cat_id_array;
			return $spec_cat_id_array;
		}
	}

	/**
	 * 获得指定文章分类下所有底层分类的ID
	 *
	 * @access  public
	 * @param   integer     $cat        指定的分类ID
	 * @return  array
	 * @version 0.0.1 9:04 2015/1/21
	 */
	public function get_article_children ($cat = 0){
		return array_unique(array_merge(array($cat), array_keys($this->article_cat_list($cat, 0, false))));
	}
}
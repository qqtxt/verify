<?php
// db
defined('JETEE_PATH') or exit();

/**
 * 保存过滤条件
 * @param   array   $filter     过滤条件
 * @param   string  $sql        查询语句
 * @param   string  $param_str  鉴别参数，一般可以为空
 */
function set_filter($filter, $sql, $param_str = ''){
    $filterfile = APP_NAME.'_'.GROUP_NAME.'_'.MODULE_NAME.'_'.ACTION_NAME;
    if ($param_str){
		$filterfile .= $param_str;
    }
	cookie('filterfile', sprintf('%X', crc32($filterfile)));
    cookie('filter',     urlencode(serialize($filter)));
    cookie('filtersql',  base64_encode(var_export($sql,true)));
}


/**
 * 取得上次的过滤条件
 * @param   string  $param_str  鉴别参数
 * @return  如果有，返回array('filter' => $filter, 'sql' => $sql)；否则返回false
 */
function get_filter($param_str = '')
{
    $filterfile = APP_NAME.'_'.GROUP_NAME.'_'.MODULE_NAME.'_'.ACTION_NAME;
    if ($param_str){
        $filterfile .= $param_str;
    }

    if (isset($_REQUEST['lastfilter']) && cookie('filterfile') == sprintf('%X', crc32($filterfile))){
		eval('$sql='.base64_decode(cookie('filtersql')).';');
        return array(
            'filter' => unserialize(urldecode(cookie('filter'))),
            'sql'    => $sql
        );
    }else{
        return false;
    }
}


/**
 * 分頁的信息加入條件的數組
 *
 * @access  public
 * @return  array
 */
function page_and_size($filter)
{
	$page_size=i('page_size',0,'intval');
	$cookie_page_size=i('cookie.admin_page_size',0,'intval');
	$page=i('page',0,'intval');	
	/* 每頁顯示 */
    if ($page_size > 0){
        $filter['page_size'] = $page_size;
    }
    elseif ($cookie_page_size > 0){
        $filter['page_size'] = $cookie_page_size;
    }
    else{
        $filter['page_size'] = 15;
    }
    $filter['page'] = (empty($page) || intval($page) <= 0) ? 1 : $page;
    /* page 總數 */
    $filter['page_count'] = (!empty($filter['record_count']) && $filter['record_count'] > 0) ? ceil($filter['record_count'] / $filter['page_size']) : 1;
    /* 邊界處理 */
    ($filter['page'] > $filter['page_count']) ? $filter['page'] = $filter['page_count'] :'';	
    $filter['start'] = ($filter['page'] - 1) * $filter['page_size'];
    return $filter;

}


/**
 * 根据过滤条件获得排序的标记
 *
 * @access  public
 * @param   array   $filter
 * @param   string  $path		'admin/'
 * @return  array
 */
function sort_flag($filter)
{
	$filter['sort_by']=str_replace('`','',$filter['sort_by']);
    $flag['tag']    = 'sort_' . preg_replace('/^.*\./', '', $filter['sort_by']);//去掉别名
    $flag['img']    = strtolower($filter['sort_order']) == 'desc' ? '<i class="fa fa-angle-double-down"></i>' : '<i class="fa fa-angle-double-up"></i>';
    return $flag;
}


function smarty_create_pages($count=0,$page=0)//100  10     99  13
{
	if (empty($page))
	{
		$page = 1;
	}

	if (!empty($count))
	{
		$str = "<option value='1'>1</option>";
		$min = min($count - 1, $page + 8);
		for ($i = $page - 8 ; $i <= $min ; $i++)
		{
			if ($i < 2)
			{
				continue;
			}
			$str .= "<option value='$i'";
			$str .= $page == $i ? " selected='true'" : '';
			$str .= ">$i</option>";
		}
		if ($count > 1 && $count<100000000)
		{
			$str .= "<option value='$count'";
			$str .= $page == $count ? " selected='true'" : '';
			$str .= ">$count</option>";
		}
	}
	else
	{
		$str = '';
	}

	return $str;
}


/**
 * 记录管理员的操作内容
 *
 * @access  public
 * @param   string      $sn         详细描述
 * @param   string      $action     操作的类型
 * @param   string      $content    操作的内容
 * @return  void
 */
function admin_log($sn = '', $action, $content){
	$admin_id=session('admin_id');
	$data =array(
		'log_time'=>NOW_TIME,
		'admin_id'=>$admin_id,
		'log_info'=>jeHtmlspecialchars(L('log.'.strtolower($action)) . L('log.'.strtolower($content)) .': '. $sn),
		'ip_address'=>get_client_ip()
	);
	db('admin_log')->add($data);
}

/**
 * 生成编辑器
 * @param   string  input_name  输入框名称
 * @param   string  input_value 输入框值
 */
function create_html_editor($input_name, $input_value = '')
{
    $editor = '<input type="hidden" id="'.$input_name.'" name="'.$input_name.'" value="'.htmlspecialchars($input_value).'" />
    <iframe id="'.$input_name.'_frame" src="Public/min/ueditor/1.4.3.3/editor.php?item='.$input_name.'" width="642" height="482" frameborder="0" scrolling="no"></iframe>';
    return $editor;
}

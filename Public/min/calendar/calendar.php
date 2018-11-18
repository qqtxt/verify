<?php
header('Content-type: application/x-javascript; charset=UTF-8');
require_once(dirname(__FILE__).'/lang_cn.php');
foreach ($_LANG['calendar_lang'] AS $cal_key => $cal_data)
{
    echo 'var ' . $cal_key . " = \"" . $cal_data . "\";\r\n";
}
require_once(dirname(__FILE__).'/calendar.js');
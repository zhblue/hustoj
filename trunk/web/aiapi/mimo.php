<?php
// 这个文件用于对接小米Mimo，解析编译报错和运行错误信息。
// 需要在db_info.inc.php中配置 $OJ_AI_API_URL指向本文件;
// 登录小米开发者平台 https://platform.xiaomimimo.com/ ，打开 https://platform.xiaomimimo.com/#/console/api-keys  创建新的API KEY
// 注意这个功能可能会导致小米云付费账单，
// 访问类似 https://platform.xiaomimimo.com/#/console/usage
// 关注所用模型的剩余免费额度
require_once("../include/db_info.inc.php");
require_once("../include/my_func.inc.php");
// 设置请求的URL
$url = 'https://api.xiaomimimo.com/v1/chat/completions';
$apiKey = "sk-申请你自己的apikey";   //https://platform.xiaomimimo.com/#/console/api-keys  创建新的API KEY
$models=array("mimo-v2-flash");
$temperature=0.8;
if(basename(get_included_files()[0])!="cron.php")
  require_once(dirname(__FILE__)."/common.php");

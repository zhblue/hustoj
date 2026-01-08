<?php
// 这个文件用于对接deepseek，解析编译报错和运行错误信息。
// 需要在db_info.inc.php中配置 $OJ_AI_API_URL指向本文件;
// 登录 https://platform.deepseek.com/ ，打开 [API Keys](https://platform.deepseek.com/api_keys)  创建新的API KEY
// 注意这个功能可能会导致付费账单，
// 访问类似 https://platform.deepseek.com/usage
// 关注所用账户的剩余额度
require_once("../include/db_info.inc.php");
require_once("../include/my_func.inc.php");
// 设置请求的URL
$url = 'https://api.deepseek.com/chat/completions';
$apiKey = "sk-你自己申请的api-key";   // https://platform.deepseek.com/api_keys  创建新的API KEY
$models=array("deepseek-chat");
require_once(dirname(__FILE__)."/common.php");

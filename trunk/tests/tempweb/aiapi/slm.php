<?php
// 这个文件用于对接算了么，解析编译报错和运行错误信息。
// 需要在db_info.inc.php中配置 $OJ_AI_API_URL指向本文件;  
// 登录算了么，打开 https://api.suanli.cn/token  创建新的API KEY
// 注意这个功能可能会导致阿里云付费账单，
// 访问类似 https://api.suanli.cn/detail
// 关注所用模型的剩余免费额度
require_once("../include/db_info.inc.php");
require_once("../include/my_func.inc.php");
$url = "https://api.suanli.cn/v1/chat/completions";  // 设置请求的URL
$apiKey ="算了么平台申请的API key";   //https://api.suanli.cn/token
$models=array("free:Qwen3-30B-A3B");
$temperature=0.8;
if(basename(get_included_files()[0])!="cron.php")
  require_once(dirname(__FILE__)."/common.php");

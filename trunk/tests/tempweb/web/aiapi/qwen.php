<?php
// 这个文件用于对接阿里千问，解析编译报错和运行错误信息。
// 需要在db_info.inc.php中配置 $OJ_AI_API_URL指向本文件; 
// 登录阿里云，打开 https://bailian.console.aliyun.com/?tab=model#/api-key  创建新的API KEY
// 注意这个功能可能会导致阿里云付费账单，
// 访问类似 https://bailian.console.aliyun.com/?tab=model#/model-market/detail/qwen3-coder-480b-a35b-instruct
// 关注所用模型的剩余免费额度
require_once("../include/db_info.inc.php");
require_once("../include/my_func.inc.php");
// 设置请求的URL
$url = 'https://dashscope.aliyuncs.com/compatible-mode/v1/chat/completions';
$apiKey = "设置为阿里云的API-KEY";   //https://bailian.console.aliyun.com/?tab=model#/api-key  创建新的API KEY
$models=array("qwen-turbo");  //,"qwen3-coder-480b-a35b-instruct","qwen3-max","qwen3-coder-30b-a3b-instruct"
$temperature=0.8;
if(basename(get_included_files()[0])!="cron.php")
  require_once(dirname(__FILE__)."/common.php");

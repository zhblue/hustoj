<?php
// 这个文件用于对接huggingface [ https://huggingface.co/]，解析编译报错和运行错误信息。
// 需要在 db_info.inc.php 中配置 $OJ_AI_API_URL 指向本文件; 
// 登录https://huggingface.co/，打开 https://huggingface.co/settings/tokens 创建新的API KEY [create new token ]
// 注意这个功能可能会导致付费账单，
// 访问 https://huggingface.co/settings/billing
// 关注所用模型的剩余免费额度
require_once("../include/db_info.inc.php");
require_once("../include/my_func.inc.php");
$url = "https://router.huggingface.co/v1/chat/completions";
$apiKey ="hf_api_key";  // 配置你在 https://huggingface.co/settings/tokens 生成的key
$models=array("Qwen/Qwen3-Coder-480B-A35B-Instruct:novita");

$temperature=0.8;
if(basename(get_included_files()[0])!="cron.php")
  require_once(dirname(__FILE__)."/common.php");

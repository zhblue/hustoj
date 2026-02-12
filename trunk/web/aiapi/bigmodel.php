<?php
// 这个文件用于对接智普bigmodel.cn，解析编译报错和运行错误信息。
// 需要在db_info.inc.php中配置 $OJ_AI_API_URL指向本文件; 
// 注册邀请 智谱 GLM Coding 超值订阅，邀你一起薅羊毛！Claude Code、Cline 等 10+ 大编程工具无缝支持，“码力”全开，越拼越爽！立即开拼，享限时惊喜价！
//
// 链接：https://www.bigmodel.cn/claude-code?ic=YVNXOQOZX5
//
// 登录bigmodel.cn，打开 https://bigmodel.cn/usercenter/proj-mgmt/apikeys 创建新的API KEY
// 注意这个功能可能会导致付费账单，
// 访问类似 https://bigmodel.cn/finance-center/resource-package/package-mgmt
// 关注所用模型的剩余免费额度
require_once("../include/db_info.inc.php");
require_once("../include/my_func.inc.php");
// 请用智普API Key将下行 $QWEN_API_KEY ;
$url = 'https://open.bigmodel.cn/api/paas/v4/chat/completions';
$apiKey = "填写你申请的api-key";
$models=array("glm-4.5","glm-4.5-air","glm-4.5-flash","glm-4.5-airx");

//$temperature=0.8;
if(basename(get_included_files()[0])!="cron.php")
    require_once(dirname(__FILE__)."/common.php");

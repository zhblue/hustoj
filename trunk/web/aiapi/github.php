<?php 
// 参考此文 to https://docs.github.com/zh/github-models/quickstart
// 这个文件用于对接github-models，解析编译报错和运行错误信息。
// 需要在db_info.inc.php中配置 $OJ_AI_API_URL指向本文件;
// 登录github，打开  
//  https://github.com/settings/personal-access-tokens/new?description=Used+to+call+GitHub+Models+APIs+to+easily+run+LLMs%3A+https%3A%2F%2Fdocs.github.com%2Fgithub-models%2Fquickstart%23step-2-make-an-api-call&name=GitHub+Models+token&user_models=read  创建新的API KEY
// 创建新的API KEY
// 注意这个功能可能会导致付费账单，
// 访问类似 https://docs.github.com/zh/github-models/use-github-models/prototyping-with-ai-models#rate-limits
// 关注所用模型的速率限制
require_once("../include/db_info.inc.php");
require_once("../include/my_func.inc.php");
// 设置请求的URL
$url = 'https://models.github.ai/inference/chat/completions';
$apiKey = "申请你自己的github api key 填写到这里";   //  https://github.com/settings/personal-access-tokens/new  创建新的API KEY
$models=array("openai/gpt-4.1");
$temperature=0.8;
require_once(dirname(__FILE__)."/common.php");

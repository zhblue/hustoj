<?php
// 这个文件用于对接腾讯混元大模型，目前免费5路并发。
// 需要在 db_info.inc.php 文件中配置 $OJ_AI_API_URL
// 混元大模型目前提供后付费日结的计费模式，且为每个开通服务的腾讯云账号提供累计100万token的调用额度（以资源包形式发放）；
// 在账单结算时，系统将按照“免费资源包 > 后付费”的顺序进行结算，即免费资源包是优先扣除的，免费额度内不收取任何费用。后付费在开通服务时默认关闭，需手动开启。 详情请查看购买指南。
// 登录腾讯云，打开 https://console.cloud.tencent.com/hunyuan/api-key 创建新的API KEY
// 注意这个功能滥用可能会导致付费账单，请关注混元的计费规则变化
// 访问类似 https://console.cloud.tencent.com/hunyuan/packages
// 关注所用模型的剩余免费额度
require_once("../include/db_info.inc.php");
require_once("../include/my_func.inc.php");
$url = "https://api.hunyuan.cloud.tencent.com/v1/chat/completions";
$apiKey ="你在腾讯云生成的API-KEY填在这里 ";  //https://console.cloud.tencent.com/hunyuan/api-key
$models=array("hunyuan-turbos-latest");
require_once(dirname(__FILE__)."/common.php");

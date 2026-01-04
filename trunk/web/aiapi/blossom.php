<?php
// 这个文件用于调用 Blossom
// https://huggingface.co/Azure99/Blossom-V6.2-14B
require_once("../include/db_info.inc.php");
require_once("../include/my_func.inc.php");
// 设置请求的URL
$url = "http://m.hustoj.com:8092/v1/chat/completions";    // Bloosom没有公开API可以用，需要自行部署。
$apiKey ="set your own key";
$models=array("blossom-v6.2-14b-q4_k_s.gguf");
require_once(dirname(__FILE__)."/common.php");

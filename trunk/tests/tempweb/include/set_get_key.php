<?php if(!isset($_SESSION[$OJ_NAME.'_'.'getkey']) && isset($_SESSION[$OJ_NAME.'_'.'user_id'])) $_SESSION[$OJ_NAME.'_'.'getkey']=strtoupper(substr(MD5($_SESSION[$OJ_NAME.'_'.'user_id'].rand(0,9999999)),0,10));?>


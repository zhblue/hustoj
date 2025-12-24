<?php
// 在校园网、局域网中部署此文件，用于检测和记录客户端的局域网IP地址。

/**
 * 获取客户端真实IP地址
 * 优先级：HTTP_X_FORWARDED_FOR > HTTP_X_REAL_IP > REMOTE_ADDR
 * 并对IP地址进行安全过滤处理
 */
$ip = ($_SERVER['REMOTE_ADDR'] ?? "");
if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty(trim($_SERVER['HTTP_X_FORWARDED_FOR']))) {
    // 从HTTP_X_FORWARDED_FOR头部获取IP地址（通常为代理服务器添加的原始客户端IP）
    $REMOTE_ADDR = $_SERVER['HTTP_X_FORWARDED_FOR'];
    $tmp_ip = explode(',', $REMOTE_ADDR);
    $ip = (htmlentities($tmp_ip[0], ENT_QUOTES, "UTF-8"));
} else if (isset($_SERVER['HTTP_X_REAL_IP']) && !empty(trim($_SERVER['HTTP_X_REAL_IP']))) {
    // 从HTTP_X_REAL_IP头部获取IP地址（通常为Nginx等反向代理添加的原始客户端IP）
    $REMOTE_ADDR = $_SERVER['HTTP_X_REAL_IP'];
    $tmp_ip = explode(',', $REMOTE_ADDR);
    $ip = (htmlentities($tmp_ip[0], ENT_QUOTES, "UTF-8"));
}

// 将IP地址转换为长整型并重定向到公网服务器进行会话处理
header("location:http://your.public.domain.com/session.php?lip=" . ip2long($ip));
// 修改上面的域名、服务地址为你的公网服务器地址。


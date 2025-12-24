<?php
// 检查会话是否已启动
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 检查请求来源是否为当前域名，如果是则执行CSRF防护逻辑
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
$httpHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

// 改进来源验证逻辑，检查referer是否包含当前主机名
$isSameOrigin = false;
if (!empty($referer) && !empty($httpHost)) {
    $refererHost = parse_url($referer, PHP_URL_HOST);
    $isSameOrigin = ($refererHost === $httpHost);
}

if ($isSameOrigin) {
    require_once("include/db_info.inc.php");
    require_once("include/my_func.inc.php");
    
    // 生成CSRF令牌
    $token = getToken();
    
    // 初始化CSRF令牌数组
    $sessionKey = (isset($OJ_NAME) ? $OJ_NAME : '') . '_' . 'csrf_keys';
    if (!isset($_SESSION[$sessionKey])) {
        $_SESSION[$sessionKey] = array();
    }
    
    // 将新令牌添加到会话中的CSRF令牌数组
    array_push($_SESSION[$sessionKey], $token);
    
    // 限制CSRF令牌数组大小，最多保留10个令牌
    while (count($_SESSION[$sessionKey]) > 10) {
        array_shift($_SESSION[$sessionKey]);
    }

    // 输出隐藏的CSRF令牌字段，对输出进行转义
    $escapedToken = htmlspecialchars($token, ENT_QUOTES, 'UTF-8');
    $tokenExists = in_array($token, $_SESSION[$sessionKey]);
    $classValue = $tokenExists ? 'true' : 'false';
    $escapedClassValue = htmlspecialchars($classValue, ENT_QUOTES, 'UTF-8');
    ?>
    <input type="hidden" name="csrf" value="<?php echo $escapedToken ?>" 
           class="<?php echo $escapedClassValue ?>">
    <?php
}

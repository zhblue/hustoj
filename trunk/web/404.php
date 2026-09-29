<?php
/**
 * HustOJ 自定义 404 错误页
 *   - 管理员（$_SESSION[$OJ_NAME.'_administrator']）看到详细调试信息
 *   - 其他用户看到简洁提示
 */

http_response_code(404);

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// ---------- 获取 $OJ_NAME ----------
// 方式一（推荐）：直接引入 db_info.inc.php 获取真实的 $OJ_NAME
// 但 db_info.inc.php 会连接数据库，为避免 404 页面因数据库异常二次崩溃，
// 使用 @ 抑制错误并用 try/catch 兜底。
$dbInfoFile = __DIR__ . '/include/db_info.inc.php';
if (is_readable($dbInfoFile)) {
    try {
        // db_info.inc.php 内部通常用 require 加载，这里用 include 且抑制警告
        @include_once $dbInfoFile;
        if (isset($OJ_NAME) && $OJ_NAME !== '') {
            $OJ_NAME = (string)$OJ_NAME;
        }
    } catch (Throwable $e) {
        // 忽略数据库错误，走下面的回退逻辑
    }
}

// 方式二（回退）：扫描 SESSION，找出任何以 _administrator 结尾的键
// 这样可以不依赖 db_info.inc.php 也能识别管理员
$isAdmin = false;

// 优先用 $OJ_NAME 精确判断
if ($OJ_NAME !== '' && !empty($_SESSION[$OJ_NAME . '_administrator'])) {
    $isAdmin = true;
}

// 回退：扫描所有 session key
if (!$isAdmin && !empty($_SESSION) && is_array($_SESSION)) {
    foreach ($_SESSION as $key => $val) {
        if (!empty($val) && substr($key, -14) === '_administrator') {
            $isAdmin = true;
            break;
        }
    }
}

// 额外兜底：user_id === 1（HustOJ 内置超级管理员）
if (!$isAdmin && isset($_SESSION['user_id']) && intval($_SESSION['user_id']) === 1) {
    $isAdmin = true;
}

// 安全转义
function h($v) {
    if (is_scalar($v) || $v === null) {
        return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
    return htmlspecialchars(print_r($v, true), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// 隐藏敏感字段（管理员也只看必要部分，避免密码 hash 泄露到浏览器）
function mask_session_value($key, $val) {
    $sensitive = ['password', 'passwd', 'pwd', 'token', 'secret', 'private'];
    $lk = strtolower($key);
    foreach ($sensitive as $s) {
        if (strpos($lk, $s) !== false) {
            return '*** masked ***';
        }
    }
    return $val;
}

$requestUri  = $_SERVER['REQUEST_URI']    ?? '/';
$requestMeth = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$serverName  = $_SERVER['SERVER_NAME']    ?? '';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="robots" content="noindex,nofollow">
<title>404 - 页面未找到</title>
<style>
    :root { --danger:#d9534f; --primary:#337ab7; --border:#e3e6ea; }
    body { font-family:-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial,"PingFang SC","Microsoft YaHei",sans-serif;
           background:#f5f7fa; color:#333; margin:0; padding:40px 20px; }
    .wrap { max-width:960px; margin:0 auto; background:#fff; border-radius:8px;
            box-shadow:0 2px 8px rgba(0,0,0,.08); padding:32px 40px; }
    h1 { font-size:28px; margin:0 0 8px; color:var(--danger); }
    h2 { font-size:18px; margin:28px 0 8px; color:#555; border-bottom:1px solid #eee; padding-bottom:4px; }
    p { line-height:1.7; color:#555; }
    code { background:#f3f3f3; padding:1px 6px; border-radius:4px; color:#c7254e;
           font-family:Consolas,Monaco,"Courier New",monospace; }
    table { width:100%; border-collapse:collapse; margin-top:8px; font-size:13px; }
    th, td { border:1px solid var(--border); padding:6px 10px; text-align:left;
             vertical-align:top; word-break:break-all; }
    th { background:#f7f9fb; width:240px; color:#333; font-weight:600; }
    .admin-badge { display:inline-block; background:var(--danger); color:#fff; font-size:12px;
                   padding:2px 8px; border-radius:10px; margin-left:8px; vertical-align:middle; }
    .back { display:inline-block; margin-top:28px; padding:8px 20px; background:var(--primary);
            color:#fff; text-decoration:none; border-radius:4px; }
    .back:hover { background:#286090; }
    pre { background:#2d2d2d; color:#f8f8f2; padding:12px; border-radius:6px;
          overflow-x:auto; font-size:12.5px; line-height:1.5; margin:0; }
    .hint { font-size:12.5px; color:#888; margin-top:4px; }
</style>
</head>
<body>
<div class="wrap">

<?php if ($isAdmin): ?>
    <h1>404 Not Found <span class="admin-badge">管理员模式</span></h1>
    <p>服务器未能找到请求的资源。以下为调试信息（仅管理员可见）。</p>
    <p class="hint">识别依据：<code>$_SESSION[<?= h($OJ_NAME) ?>_administrator]</code>
        <?= $OJ_NAME === '' ? '（$OJ_NAME 未获取到，已回退扫描 SESSION 键）' : '' ?></p>

    <h2>请求概要</h2>
    <table>
        <tr><th>请求方法 (REQUEST_METHOD)</th><td><?= h($requestMeth) ?></td></tr>
        <tr><th>请求 URI (REQUEST_URI)</th><td><?= h($requestUri) ?></td></tr>
        <tr><th>主机 (SERVER_NAME)</th><td><?= h($serverName) ?></td></tr>
        <tr><th>文档根 (DOCUMENT_ROOT)</th><td><?= h($_SERVER['DOCUMENT_ROOT'] ?? '') ?></td></tr>
        <tr><th>脚本名称 (SCRIPT_NAME)</th><td><?= h($_SERVER['SCRIPT_NAME'] ?? '') ?></td></tr>
        <tr><th>脚本路径 (SCRIPT_FILENAME)</th><td><?= h($_SERVER['SCRIPT_FILENAME'] ?? '') ?></td></tr>
        <tr><th>查询串 (QUERY_STRING)</th><td><?= h($_SERVER['QUERY_STRING'] ?? '') ?></td></tr>
        <tr><th>来源页 (HTTP_REFERER)</th><td><?= h($_SERVER['HTTP_REFERER'] ?? '(无)') ?></td></tr>
        <tr><th>客户端 IP (REMOTE_ADDR)</th><td><?= h($_SERVER['REMOTE_ADDR'] ?? '') ?></td></tr>
        <tr><th>X-Forwarded-For</th><td><?= h($_SERVER['HTTP_X_FORWARDED_FOR'] ?? '(无)') ?></td></tr>
        <tr><th>X-Real-IP</th><td><?= h($_SERVER['HTTP_X_REAL_IP'] ?? '(无)') ?></td></tr>
        <tr><th>User-Agent</th><td><?= h($_SERVER['HTTP_USER_AGENT'] ?? '') ?></td></tr>
    </table>

    <h2>登录会话 (SESSION)</h2>
    <?php if (!empty($_SESSION)): ?>
        <table>
        <?php foreach ($_SESSION as $k => $v): ?>
            <tr><th><?= h($k) ?></th><td><?= h(mask_session_value($k, $v)) ?></td></tr>
        <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p class="hint">会话为空（可能未登录，或 session 名与 HustOJ 不一致）。</p>
    <?php endif; ?>

    <h2>GET 参数</h2>
    <?php if (!empty($_GET)): ?>
        <table>
        <?php foreach ($_GET as $k => $v): ?>
            <tr><th><?= h($k) ?></th><td><?= h($v) ?></td></tr>
        <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p class="hint">无 GET 参数。</p>
    <?php endif; ?>

    <h2>POST 参数</h2>
    <?php if (!empty($_POST)): ?>
        <table>
        <?php foreach ($_POST as $k => $v): ?>
            <tr><th><?= h($k) ?></th><td><?= h($v) ?></td></tr>
        <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p class="hint">无 POST 参数（Nginx 内部重定向到 404.php 时，POST 体会被丢弃，方法退化为 GET）。</p>
    <?php endif; ?>

    <h2>Cookie</h2>
    <?php if (!empty($_COOKIE)): ?>
        <table>
        <?php foreach ($_COOKIE as $k => $v): ?>
            <tr><th><?= h($k) ?></th><td><?= h(mask_session_value($k, $v)) ?></td></tr>
        <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p class="hint">无 Cookie。</p>
    <?php endif; ?>

    <h2>完整 $_SERVER</h2>
    <pre><?= h(print_r($_SERVER, true)) ?></pre>

    <h2>PHP 环境</h2>
    <table>
        <tr><th>PHP 版本</th><td><?= h(PHP_VERSION) ?></td></tr>
        <tr><th>SAPI</th><td><?= h(php_sapi_name()) ?></td></tr>
        <tr><th>已加载 php.ini</th><td><?= h(php_ini_loaded_file() ?: '(未加载)') ?></td></tr>
        <tr><th>上游软件</th><td><?= h($_SERVER['SERVER_SOFTWARE'] ?? '') ?></td></tr>
        <tr><th>识别到的 $OJ_NAME</th><td><?= h($OJ_NAME === '' ? '(未获取到)' : $OJ_NAME) ?></td></tr>
    </table>

<?php else: ?>
    <h1>404 - 页面未找到</h1>
    <p>抱歉，您访问的页面 <code><?= h($requestUri) ?></code> 不存在或已被移除。</p>
    <p>请检查链接是否正确，或返回首页继续浏览。</p>
<?php endif; ?>

<a class="back" href="/">返回首页</a>
</div>
</body>
</html>

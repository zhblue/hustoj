<?php
////////////////////////////Common head
$cache_time = 120;
$OJ_CACHE_SHARE = true;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/memcache.php');
require_once('./include/setlang.php');

// ---- 服务端拉取远端比赛数据（解决浏览器跨域访问经常失败的问题） ----
// 优先使用 curl，回退到 file_get_contents；解析失败或网络不可达时返回 null。
function recent_contest_fetch_json($url) {
    $ua = "Mozilla/5.0 (compatible; HUSTOJ-RecentContest/1.0)";
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_USERAGENT      => $ua,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,   // 兼容部分缺少 CA 包的服务器
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_HTTPHEADER      => array('Accept: application/json'),
        ));
        $body = curl_exec($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($body !== false && $code >= 200 && $code < 300) {
            $data = json_decode($body, true);
            if (json_last_error() === JSON_ERROR_NONE) return $data;
        }
        return null;
    }
    // 回退：file_get_contents + stream context（需要 allow_url_fopen=On）
    $ctx = stream_context_create(array(
        'http' => array(
            'timeout' => 10,
            'method'  => 'GET',
            'header'  => "User-Agent: $ua\r\nAccept: application/json\r\n",
        ),
        'ssl' => array('verify_peer' => false, 'verify_peer_name' => false),
    ));
    $body = @file_get_contents($url, false, $ctx);
    if ($body === false) return null;
    $data = json_decode($body, true);
    return (json_last_error() === JSON_ERROR_NONE) ? $data : null;
}

// 输出转义，避免远端数据造成的 XSS
function rc_h($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES); }
// 仅允许 http/https 链接，杜绝 javascript: 等危险协议
function rc_href($v) {
    $v = (string)($v ?? '');
    return preg_match('#^https?://#i', $v) ? htmlspecialchars($v, ENT_QUOTES) : '#';
}

$jsonUrl  = "https://algcontest.rainng.com/contests.json";
$contests = array();
$fetchError = "";

$raw = recent_contest_fetch_json($jsonUrl);
if (is_array($raw)) {
    // 兼容顶层数组，或包在 data / contests 键中的情况
    if (isset($raw['data']) && is_array($raw['data']))            $contests = $raw['data'];
    elseif (isset($raw['contests']) && is_array($raw['contests'])) $contests = $raw['contests'];
    else                                                           $contests = $raw;
} else {
    $fetchError = "暂时无法获取比赛数据（远端无响应或服务器出网受限），请稍后重试，或直接在数据源查看：";
}

require("template/" . $OJ_TEMPLATE . "/recent-contest.php");
/////////////////////////Common foot
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');

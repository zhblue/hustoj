<?php
/**
 * HUSTOJ Page Crawler - 页面访问速度测试
 * 
 * 遍历tempweb下的PHP页面，测量响应时间
 * Usage: php crawl_pages.php [base_url]
 */

$BASE_URL = $argv[1] ?? 'http://127.0.0.1';
$TIMEOUT = 30; // 秒
$WARM_UP = 1;  // 预热次数

echo "=== HUSTOJ 页面爬虫 ===\n";
echo "Base URL: $BASE_URL\n";
echo "Timeout: {$TIMEOUT}s\n\n";

// 页面列表（排除ajax和子页面）
$pages = [
    '/' => 'index.php - 首页',
    '/problemset.php' => 'problemset.php - 题目列表',
    '/status.php' => 'status.php - 判题状态',
    '/problem.php?id=1000' => 'problem.php?id=1000 - 题目详情',
    '/login.php' => 'login.php - 登录',
    '/registerpage.php' => 'registerpage.php - 注册',
    '/contest.php' => 'contest.php - 竞赛列表',
    '/ranklist.php' => 'ranklist.php - 排行榜',
    '/userinfo.php' => 'userinfo.php - 用户信息',
    '/mail.php' => 'mail.php - 站内消息',
    '/forum.php' => 'forum.php - 论坛',
    '/submitpage.php?id=1000' => 'submitpage.php - 提交页面',
];

// 管理页面
$admin_pages = [
    '/admin/' => 'admin/index.php - 管理首页',
    '/admin/problem_list.php' => 'admin/problem_list.php - 题目管理',
    '/admin/user_list.php' => 'admin/user_list.php - 用户管理',
    '/admin/contest_list.php' => 'admin/contest_list.php - 竞赛管理',
    '/admin/news_list.php' => 'admin/news_list.php - 新闻管理',
    '/admin/rejudge.php' => 'admin/rejudge.php - 重新判题',
    '/admin/privilege_list.php' => 'admin/privilege_list.php - 权限管理',
];

$results = [];
$errors = [];

echo "--- 预热 ---\n";
// 预热请求
foreach (array_slice($pages, 0, 3) as $url => $name) {
    curl_request($BASE_URL . $url, false);
}
echo "预热完成\n\n";

echo "--- 测试主站页面 ---\n";
foreach ($pages as $url => $name) {
    $result = measure_page($BASE_URL . $url, $name);
    $results[] = $result;
}

echo "\n--- 测试管理页面 ---\n";
foreach ($admin_pages as $url => $name) {
    $result = measure_page($BASE_URL . $url, $name);
    $results[] = $result;
}

// 按响应时间排序
usort($results, function($a, $b) {
    return $b['time'] <=> $a['time'];
});

echo "\n";
echo "=== 慢页面排行榜 (按响应时间排序) ===\n\n";

echo sprintf("%-50s %10s %10s\n", "页面", "时间(ms)", "状态");
echo str_repeat("-", 72) . "\n";

foreach ($results as $r) {
    $mark = $r['time'] > 1000 ? "⚠️" : ($r['time'] > 500 ? "🔶" : "✅");
    echo sprintf("%-50s %8d %s %s\n", 
        substr($r['name'], 0, 48), 
        $r['time'],
        $r['status'],
        $mark
    );
}

echo "\n=== 统计 ===\n";
$times = array_column($results, 'time');
$success = count(array_filter($results, fn($r) => $r['code'] == 200));
$fail = count($results) - $success;
echo "总页面数: " . count($results) . "\n";
echo "成功(200): $success\n";
echo "失败: $fail\n";
echo "最快: " . min($times) . "ms\n";
echo "最慢: " . max($times) . "ms\n";
echo "平均: " . round(array_sum($times) / count($times)) . "ms\n";

/**
 * 测量单个页面响应时间
 */
function measure_page($url, $name) {
    global $TIMEOUT, $WARM_UP;
    
    // 多次测量取平均值
    $times = [];
    $last_error = '';
    $last_code = 0;
    
    for ($i = 0; $i < 3; $i++) {
        $result = curl_request($url, true);
        if ($result['code'] == 200) {
            $times[] = $result['time'];
        }
        $last_code = $result['code'];
        $last_error = $result['error'];
    }
    
    $avg_time = count($times) > 0 ? round(array_sum($times) / count($times)) : 0;
    
    return [
        'url' => $url,
        'name' => $name,
        'time' => $avg_time,
        'code' => $last_code,
        'error' => $last_error,
        'status' => $last_code == 200 ? 'OK' : "HTTP $last_code"
    ];
}

/**
 * curl请求
 */
function curl_request($url, $measure = true) {
    global $TIMEOUT;
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => $TIMEOUT,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_NOBODY => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 3,
    ]);
    
    $start = microtime(true);
    $response = curl_exec($ch);
    $time = round((microtime(true) - $start) * 1000); // ms
    
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'response' => $response,
        'time' => $time,
        'code' => $code,
        'error' => $error
    ];
}

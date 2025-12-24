<?php
require_once("./include/db_info.inc.php");

// 验证用户ID格式，防止文件名注入
$user_id = $_SESSION[$OJ_NAME . '_' . 'user_id'] ?? '';
if (empty($user_id) || !preg_match('/^[a-zA-Z0-9_-]+$/', $user_id)) {
    $view_errors = "<a href=./loginpage.php>$MSG_Login</a>";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

// 设置响应头，安全的文件名
$filename = "ac-" . $user_id . ".txt";
header("content-type: application/octet-stream");
header("content-disposition: attachment; filename=\"" . $filename . "\"");

// 检查用户是否已登录，未登录则跳转到登录页面
if (!isset($_SESSION[$OJ_NAME . '_' . 'user_id']) || empty($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
    $view_errors = "<a href=./loginpage.php>$MSG_Login</a>";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

// 查询用户AC的题目代码，排除竞赛中的题目
$sql = "SELECT DISTINCT source, problem_id FROM source_code INNER JOIN 
        (SELECT solution_id, problem_id FROM solution WHERE user_id=? AND result=4) S 
        ON source_code.solution_id=S.solution_id  
        WHERE S.problem_id NOT IN (SELECT problem_id FROM contest_problem WHERE contest_id 
                        IN (SELECT contest_id FROM contest WHERE start_time < NOW() AND end_time > NOW())) 
        ORDER BY problem_id";

// 输出用户ID
echo $user_id . "\r\n";

// 执行查询并处理结果
try {
    $result = pdo_query($sql, $user_id);
    if ($result !== false) {
        foreach ($result as $row) {
            // 验证并输出题目信息
            $problem_id = (int)$row['problem_id'];
            $source = $row['source'];
            
            echo "Problem" . $problem_id . ":\r\n";
            // 安全地替换换行符，防止输出格式问题
            echo preg_replace('/(\r\n|\n|\r)/', "\r\n", $source);
            echo "\r\n------------------------------------------------------\r\n";
        }
    }
} catch (Exception $e) {
    // 记录错误日志，但不向用户暴露详细错误信息
    error_log("Database query failed: " . $e->getMessage());
    $view_errors = "查询失败，请稍后重试";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}


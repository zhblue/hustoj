<?php
ini_set("display_errors", "Off");
// 如果不是 CLI 环境，则直接退出
if (php_sapi_name() !== 'cli') {
    echo "This script can only be run from command line.\n";
    exit(1);
}
// 防止 PHP 进程超时（CLI 模式下 set_time_limit(0) 不会受 php.ini max_execution_time 影响）
set_time_limit(0);

require_once("../include/db_info.inc.php");
require_once("../include/my_func.inc.php");
// 设置请求的URL
require_once(dirname(dirname(__FILE__))."/".$OJ_AI_API_URL);

function uniqueSource($str) {
    // 用正则分割字符串，支持多个连续空格
    $arr = preg_split('/\s+/', trim($str));
    // 去除数组中的重复项
    $uniqueArr = array_unique($arr);
    // 重新拼接为空格分割的字符串
    $result = implode(' ', $uniqueArr);
    return $result;
}

// ---------------------------------------------------------------------------
// 修复1: 恢复孤儿任务（status=1 的任务可能是上次进程崩溃留下的）
//         这些任务没有被标记为完成，应该允许重新被处理
// ---------------------------------------------------------------------------
$orphan = pdo_query("SELECT id FROM openai_task_queue WHERE status=1");
if (!empty($orphan)) {
    $count = count($orphan);
    echo "[CRON] 发现 {$count} 个卡住的任务（status=1），尝试恢复...\n";
    foreach ($orphan as $t) {
        // 只有超时的任务才恢复（比如超过2分钟还在 status=1 说明确实卡住了）
        // update_date 是"最后更新时间"字段
        pdo_query("UPDATE openai_task_queue SET status=0 WHERE id=? AND status=1 AND update_date < DATE_SUB(NOW(), INTERVAL 2 MINUTE)", $t['id']);
    }
    echo "[CRON] 孤儿任务恢复完毕。\n";
}

// ---------------------------------------------------------------------------
// 主循环
// ---------------------------------------------------------------------------
$max_loops = 1000; // 防止意外死循环
$loop = 0;

do {
    $loop++;
    if ($loop > $max_loops) {
        echo "[CRON] 已达到最大循环次数 {$max_loops}，退出。\n";
        break;
    }

    $sql = "SELECT * FROM openai_task_queue WHERE status=0 ORDER BY id ASC LIMIT 1";
    $tasks = pdo_query($sql);

    if (empty($tasks)) {
        // 没有任务，休息后退出（避免空转）
        echo "[CRON] 没有待处理任务，休息1秒后退出。\n";
        sleep(1);
        break;
    }

    // --- 请求头准备（只构造一次） ---
    $version = '2026.3.23';
    $nodeVersion = '25.8.2';
    $userAgent = "OpenClaw/{$version} (Node.js {$nodeVersion}; Linux x64)";
    $clientInfo = json_encode([
        "platform" => "OpenClaw",
        "version" => $version,
        "runtime" => "Node.js",
        "runtime_version" => $nodeVersion
    ]);
    $headers = [
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json',
        'User-Agent: ' . $userAgent,
        'X-DashScope-Client: ' . $clientInfo,
        'X-Client-Id: openclaw-official-plugin',
        'X-Requested-With: XMLHttpRequest'
    ];
    $model = $models[array_rand($models)];

    $did = 0;
    foreach ($tasks as $task) {
        $data = $task['request_body'];

        // 抢锁：只有 status=0 的任务才能被当前进程抢到
        $affected = pdo_query("UPDATE openai_task_queue SET status=1 WHERE id=? AND status=0", $task['id']);
        if (intval($affected) === 0) {
            // 已被其他进程抢走，跳过
            continue;
        }

        echo "[CRON] 开始处理任务 #{$task['id']} ...\n";

        // 初始化 cURL
        $ch = curl_init();
        $timeout = $timeout ?? 600;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_REFERER, 'http://127.0.0.1:18789/' . $OJ_NAME);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        // 不使用 CURLOPT_FOLLOWLOCATION（防止重定向劫持）
        curl_setopt($ch, CURLOPT_MAXREDIRS, 0);
        // 不把 HTTP 错误当异常，让我们在代码里自行判断
        curl_setopt($ch, CURLOPT_FAILONERROR, false);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);

        // ---------------------------------------------------------------------------
        // 修复2: 检查 cURL 错误（网络层）
        // ---------------------------------------------------------------------------
        if ($curl_errno) {
            echo "[CRON] cURL 错误 #{$curl_errno}: {$curl_error}，重置任务 #{$task['id']} 为待处理\n";
            pdo_query("UPDATE openai_task_queue SET status=0 WHERE id=? AND status=1", $task['id']);
            continue;  // 不 exit，继续处理下一个任务
        }

        // ---------------------------------------------------------------------------
        // 修复3: 检查 HTTP 状态码（非200的都是异常响应）
        // ---------------------------------------------------------------------------
        if ($http_code !== 200) {
            echo "[CRON] HTTP 错误: {$http_code}，响应内容: " . substr($response, 0, 200) . "\n";
            // 把错误信息存进去，标记为失败（避免无限重试）
            $err_msg = "HTTP {$http_code}: " . substr($response, 0, 500);
            pdo_query("UPDATE openai_task_queue SET error_message=?, status=3 WHERE id=?", $err_msg, $task['id']);
            continue;
        }

        // ---------------------------------------------------------------------------
        // 修复4: 检查响应是否为空或过短
        // ---------------------------------------------------------------------------
        $response = trim($response);
        if (strlen($response) < 10) {
            echo "[CRON] 响应内容过短或为空，重置任务 #{$task['id']} 为待处理\n";
            pdo_query("UPDATE openai_task_queue SET status=0 WHERE id=? AND status=1", $task['id']);
            continue;
        }

        echo "[CRON] 响应长度: " . strlen($response) . " 字节\n";

        // ---------------------------------------------------------------------------
        // 修复5: 检查 JSON 解析结果
        // ---------------------------------------------------------------------------
        $data = json_decode($response, true);
        if ($data === null) {
            echo "[CRON] JSON 解析失败，原始响应: " . substr($response, 0, 300) . "\n";
            pdo_query("UPDATE openai_task_queue SET error_message=?, status=3 WHERE id=?", "JSON解析失败: " . substr($response, 0, 500), $task['id']);
            continue;
        }

        // 检查 API 业务层错误（如 qps 超限、参数错误等）
        if (isset($data['error'])) {
            $err_msg = is_array($data['error']) ? json_encode($data['error']) : strval($data['error']);
            echo "[CRON] API 错误: {$err_msg}\n";
            pdo_query("UPDATE openai_task_queue SET error_message=?, status=3 WHERE id=?", $err_msg, $task['id']);
            continue;
        }

        // ---------------------------------------------------------------------------
        // 正常处理
        // ---------------------------------------------------------------------------
        $response_body = is_array($response) ? json_encode($response) : $response;
        pdo_query("UPDATE openai_task_queue SET response_body=?, status=2 WHERE id=?", $response_body, $task['id']);

        // 异步错误解析（solution_id > 0）
        if ($task['solution_id'] > 0) {
            if (isset($data['choices'][0]['message']['content'])) {
                $answer = $data['choices'][0]['message']['content'];
                $answer .= "<br> --- $model <br>";
                $answer .= "<a href='https://github.com/zhblue/hustoj/' target=_blank>如果你觉得这个系统对你有帮助，请到Github来给我们加个Star⭐吧</a>";
                $sql = "INSERT INTO solution_ai_answer (solution_id, answer) VALUES (?, ?)";
                pdo_query($sql, $task['solution_id'], $answer);
            } else {
                echo "[CRON] 警告: solution_id={$task['solution_id']} 但响应中无 choices[0].message.content\n";
            }
        }
        // 批量生成题目分类（problem_id > 0）
        else if ($task['problem_id'] > 0 && $task['task_type'] === 'problem_list.php') {
            if (isset($data['choices'][0]['message']['content'])) {
                $answer = $data['choices'][0]['message']['content'];
                $pid = $task['problem_id'];
                $new_source = uniqueSource($answer);
                $rows = pdo_query("SELECT source FROM problem WHERE problem_id=?", $pid);
                $old_source = $rows[0][0] ?? '';
                $new_source = uniqueSource($new_source . " " . $old_source);
                pdo_query("UPDATE problem SET source=? WHERE problem_id=?", $new_source, $pid);
                echo "[CRON] 题目 #{$pid} 分类已更新。\n";
            } else {
                echo "[CRON] 警告: problem_id={$task['problem_id']} 但响应中无 choices[0].message.content\n";
            }
        }

        $did++;
        echo "[CRON] 任务 #{$task['id']} 处理完成。\n";
    }

    echo "[CRON] 本轮处理 {$did} 个任务。\n";

} while ($did > 0);

echo "[CRON] 全部任务处理完毕，退出。\n";
exit(0);

<?php
require_once("admin-header.php");

if (!isset($_SESSION[$OJ_NAME.'_'.'administrator'])) {
    http_response_code(403);
    exit($MSG_BATCH_DELETE_FORBIDDEN);
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit($MSG_BATCH_DELETE_METHOD);
}
require_once("../include/check_post_key.php");

function batch_recursive_delete($dir) {
    if (!is_dir($dir)) return;
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === "." || $file === "..") continue;
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        if (is_dir($path)) batch_recursive_delete($path);
        else @unlink($path);
    }
    @rmdir($dir);
}

$ids = array();
if (isset($_POST['pid']) && is_array($_POST['pid'])) {
    foreach ($_POST['pid'] as $raw_id) {
        $id = intval($raw_id);
        if ($id > 0) $ids[$id] = $id;
    }
}
if (isset($_POST['hlist'])) {
    foreach (explode(',', (string)$_POST['hlist']) as $raw_id) {
        $id = intval($raw_id);
        if ($id > 0) $ids[$id] = $id;
    }
}
$ids = array_values($ids);
if (count($ids) === 0) {
    header("Location: problem_list.php");
    exit;
}
// 限制单次操作规模，避免误提交或长时间占用管理端请求。
$ids = array_slice($ids, 0, 200);
$deleted = 0;

foreach ($ids as $id) {
    $exists = pdo_query("SELECT `problem_id` FROM `problem` WHERE `problem_id`=? LIMIT 1", $id);
    if (count($exists) === 0) continue;

    if (strlen($OJ_DATA) > 8) {
        $data_dir = $OJ_DATA . "/" . $id;
        // 仅删除 OJ_DATA 下的数字题目目录，避免路径穿越或误删其他目录。
        if (preg_match('/^[0-9]+$/', (string)$id) && strlen($data_dir) > 16) {
            batch_recursive_delete($data_dir);
        }
    }

    pdo_query("DELETE FROM `contest_problem` WHERE `problem_id`=?", $id);
    pdo_query("DELETE FROM `problem` WHERE `problem_id`=?", $id);
    pdo_query("DELETE FROM `privilege` WHERE `rightstr`=?", "p$id");
    pdo_query("UPDATE `solution` SET `problem_id`=0, `result`=13 WHERE `problem_id`=?", $id);
    $deleted++;
}

echo "<meta charset='utf-8'>";
echo "<div class='container'><div class='alert alert-success'>" . htmlspecialchars(str_replace('{count}', intval($deleted), $MSG_BATCH_DELETE_RESULT), ENT_QUOTES, 'UTF-8') . "</div>";
echo "<a class='btn btn-primary' href='problem_list.php'>" . htmlspecialchars($MSG_BATCH_DELETE_BACK, ENT_QUOTES, 'UTF-8') . "</a></div>";
?>

<?php
require_once("admin-header.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit("Method Not Allowed");
}

require_once("../include/check_post_key.php");

if (!isset($_SESSION[$OJ_NAME . '_administrator'])) {
    http_response_code(403);
    exit("Forbidden");
}

if (isset($_POST['uid'], $_POST['rightstr'])) {
    $user_id = (string)$_POST['uid'];
    $rightstr = (string)$_POST['rightstr'];

    if ($_SESSION[$OJ_NAME . "_user_id"] === $user_id &&
        $rightstr === "administrator") {
        exit("Can't remove administrator for yourself!");
    }

    $sql = "delete from `privilege` where user_id=? and rightstr=?";
    pdo_query($sql, $user_id, $rightstr);
    echo htmlspecialchars("$user_id $rightstr deleted!", ENT_QUOTES, 'UTF-8');
}
?>

<?php
////////////////////////////Common head
require_once('./include/db_info.inc.php');
require_once('./include/my_func.inc.php');

// 检查下载功能是否启用
if ((!isset($OJ_DOWNLOAD)) || !$OJ_DOWNLOAD) {
    $view_errors = "Download Disabled!";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}

$sid = intval($_GET['sid']);

// 验证name参数，只允许字母数字下划线和点号
$name_param = $_GET['name'];
if (!preg_match('/^[a-zA-Z0-9_.]+$/', $name_param)) {
    $view_errors = "Invalid file name!";
    require("template/" . $OJ_TEMPLATE . "/error.php");
    exit(0);
}
$name = basename($name_param, ".out");

$sql = "select problem_id,contest_id,user_id from solution where solution_id=?";
$data = pdo_query($sql, $sid);
//var_dump($sql);
if (!empty($data)) {
    $row = $data[0];
    $pid = $row[0];
    
    // 检查题目是否被锁定
    if (problem_locked($pid, 2)) {
        $view_errors = "<h2> $MSG_NOIP_WARNING </h2>";
        require("template/" . $OJ_TEMPLATE . "/error.php");
        exit(0);
    }
    $cid = $row[1];
    $uid = $row[2];
    
    // 检查用户权限，确保是当前用户提交或管理员
    if (!(isset($_SESSION[$OJ_NAME . '_' . 'user_id']) && $uid == $_SESSION[$OJ_NAME . '_' . 'user_id']
        || isset($_SESSION[$OJ_NAME . '_' . 'administrator'])
    )) {
        $view_errors = "not your submission";
        require("template/" . $OJ_TEMPLATE . "/error.php");
        exit(0);
    }
    
    // 检查是否存在NOIP关键词的竞赛
    if (isset($OJ_NOIP_KEYWORD) && $OJ_NOIP_KEYWORD) {
        $now = date('Y-m-d H:i', time());
        $sql = "select 1 from `contest` where contest_id=? and `start_time` < ? and `end_time` > ? and `title` like ?";
        $search_keyword = '%' . $OJ_NOIP_KEYWORD . '%';
        $rrs = pdo_query($sql, $cid, $now, $now, $search_keyword);
        $flag = count($rrs) > 0;
        if ($flag) {
            $view_errors = "<h2> $MSG_NOIP_WARNING </h2>";
            require("template/" . $OJ_TEMPLATE . "/error.php");
            exit(0);
        }
    }
    
    // 验证文件路径，防止路径遍历
    $safe_pid = basename($pid);
    $safe_name = basename($name);
    $infile = "$OJ_DATA/$safe_pid/$safe_name.in";
    $outfile = "$OJ_DATA/$safe_pid/$safe_name.out";
    
    // 确保文件路径在预期目录内
    $expected_data_dir = realpath($OJ_DATA);
    if (!$expected_data_dir || 
        strpos(realpath($infile) ?: '', $expected_data_dir) !== 0 || 
        strpos(realpath($outfile) ?: '', $expected_data_dir) !== 0) {
        $view_errors = "Invalid file path!";
        require("template/" . $OJ_TEMPLATE . "/error.php");
        exit(0);
    }
    
    // 检查文件是否存在
    if (!file_exists($infile) && !file_exists($outfile)) {
        $view_errors = "File not found!";
        require("template/" . $OJ_TEMPLATE . "/error.php");
        exit(0);
    }

    $zipname = tempnam(__dir__ . '/upload', '');
    $zip = new ZipArchive();

    if ($zip->open($zipname, ZIPARCHIVE::CREATE) !== TRUE) {
        exit ('无法打开文件，或者文件创建失败');
    }
    $files = [$infile, $outfile];

    foreach ($files as $file) {
        if (file_exists($file)) {
            $fileContent = file_get_contents($file);
            if ($fileContent === false) {
                continue; // 跳过无法读取的文件
            }
            $file_basename = iconv('utf-8', 'GBK', basename($file));
            $zip->addFromString($file_basename, $fileContent);
        }
    }
    $zip->close();

    header('Content-Type: application/zip;charset=utf8');
    header('Content-disposition: attachment; filename=' . $name . date('Y-m-d') . '.zip');
    header('Content-Length: ' . filesize($zipname));
    readfile($zipname);
    unlink($zipname);
    die();
}

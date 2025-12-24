<?php
/**
 * 题目列表页面 - 群组统计功能
 * 
 * 该页面用于显示题目列表和群组统计数据，支持NOIP赛制限制、权限控制、
 * 数据导出等功能。主要功能包括获取用户列表、查询解题状态、生成统计表格等。
 */

// 设置缓存时间为30秒
$cache_time = 30;
// 设置缓存共享开关
$OJ_CACHE_SHARE = false;
require_once('./include/cache_start.php');
require_once('./include/db_info.inc.php');
require_once('./include/memcache.php');
require_once('./include/setlang.php');
require_once('./include/bbcode.php');
// 设置页面标题
$view_title = "$MSG_PROBLEM$MSG_LIST - $MSG_GROUP_NAME$MSG_STATISTICS";
$result = false;

// 检查是否为现场比赛，如果是则跳转到比赛页面
if (isset($OJ_ON_SITE_CONTEST_ID)) {
    header("location:contest.php?cid=" . $OJ_ON_SITE_CONTEST_ID);
    exit();
}
///////////////////////////MAIN	
//NOIP赛制比赛时，移除相关题目
$exceptions = array();

/**
 * 检查NOIP关键词限制
 * 当存在NOIP关键词且当前用户不是管理员时，获取正在进行的NOIP相关比赛ID
 * 用于在统计中排除这些比赛中的题目
 */
if (isset($OJ_NOIP_KEYWORD) && $OJ_NOIP_KEYWORD && !isset($_SESSION[$OJ_NAME . "_administrator"])) {  // 管理员不受限
    $now = date('Y-m-d H:i', time());
    $sql = "select contest_id from contest c where  c.start_time<'$now' and c.end_time>'$now' and (c.title like '%$OJ_NOIP_KEYWORD%' or (c.contest_type & 20 )> 0 )";
    $row = pdo_query($sql);
    if (count($row) > 0) {
        $exceptions = array_column($row, 'contest_id');
        //      var_dump($exceptions);
    }
}

if (isset($_GET['list'])) {
    // 获取群组名称，优先使用GET参数，否则使用会话中的群组名称
    if (isset($_GET['group_name'])) {
        $group_name = basename($_GET['group_name']);
        // 验证文件名格式，防止恶意输入
        if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $group_name)) {
            $group_name = $_SESSION[$OJ_NAME . '_group_name'];
        }
    } else {
        $group_name = $_SESSION[$OJ_NAME . '_group_name'];
    }
    
    // 检查是否需要下载Excel文件
    if (isset($_GET['down'])) {
        $safe_filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', "$MSG_GROUP_NAME.$MSG_STATISTICS" . "_" . $group_name . ".xls");
        header("Content-type:   application/excel");
        header("content-disposition:   attachment;   filename=" . $safe_filename);
    }
    
    /**
     * 根据用户权限设置用户查询限制
     * 普通用户：10个用户
     * 比赛创建者：80个用户（10+70）
     * 管理员：190个用户（10+70+100）
     */
    $limit = 10;
    if (isset($_SESSION[$OJ_NAME . '_contest_creator'])) $limit += 70;
    if (isset($_SESSION[$OJ_NAME . '_administrator'])) $limit += 100;
    
    /**
     * 根据群组名称获取用户列表
     * 按解决题目数降序排列，限制返回用户数量以防止DoS攻击
     */
    $user_ida = array();
    if (!empty($group_name)) {
        $users = pdo_query("select user_id from users where group_name=? and defunct='N'  order by solved desc  limit $limit ", $group_name);  // 预防出现DoS攻击
        $user_ida = array_column($users, 0);
    } else {
        $user_ida = ['admin'];
    }
    
    /**
     * 构建用户ID查询参数
     * 将用户ID数组转换为SQL查询中的占位符格式
     */
    $user_ids = "";
    if (!empty($user_ida) && is_array($user_ida) && count($user_ida) > 0 && strlen($user_ida[0]) > 0) {
        $len = count($user_ida);
        for ($i = 0; $i < $len; $i++) {
            if ($user_ids) $user_ids .= ",";
            $user_ids .= "?";
            $user_ida[$i] = trim($user_ida[$i]);
        }
    } else {
        // 如果没有用户，设置为空数组以避免后续错误
        $user_ida = array();
    }
    //echo implode(",",$user_ida),"<br>";

    $sql = "select user_id,nick ";

    /**
     * 处理题目ID列表
     * 解析GET参数中的题目ID，去重并转换为整数类型
     * 排除NOIP比赛中的题目ID
     */
    $pida = array_unique(explode(',', $_GET['list']));
    $len = count($pida);
    for ($i = 0; $i < $len; $i++) {
        $pida[$i] = intval($pida[$i]);
    }

    $pida = array_unique($pida);
    $pida = array_filter($pida, function($pid) { return $pid > 0; }); // 过滤掉无效的ID
    $pida = array_diff($pida, $exceptions);
    
    // 构建安全的题目ID列表
    $pids_safe = array_map('intval', $pida);
    $pids = implode(",", $pids_safe);

    /**
     * 构建SQL查询语句
     * 为每个题目ID添加条件查询字段，用于统计用户对各题目的解答状态
     */
    foreach ($pida as $pid) {
        $sql .= " ,min(case problem_id when $pid then result else 15 end) P$pid";
    }
    //$user_ids=implode("','",$user_ida);
    
    /**
     * 执行解题状态查询
     * 根据是否存在例外比赛ID来决定是否排除特定比赛的提交记录
     */
    if (empty($exceptions)) {
        if (!empty($user_ids)) {
            $sql .= " from solution where user_id in ($user_ids) group by user_id,nick ";
            $result = pdo_query($sql, $user_ida);
        } else {
            $result = array(); // 如果没有用户ID，返回空结果
        }
    } else {
        if (!empty($user_ids)) {
            $sql .= " from solution where user_id in ($user_ids) and contest_id not in (" . implode(",", $exceptions) . ") group by user_id,nick ";
            $result = pdo_query($sql, $user_ida);
        } else {
            $result = array(); // 如果没有用户ID，返回空结果
        }
    }

//  echo $sql;
    
    /**
     * 获取题目标题信息
     * 查询题目表获取题目ID和标题的对应关系
     */
    if (!empty($pids)) {
        $ptitle = pdo_query("select problem_id,title from problem where problem_id in (" . $pids . ")  and defunct='N' ");
    } else {
        $ptitle = array(); // 如果没有题目ID，返回空结果
    }
    
    /**
     * 构建题目ID到标题的映射数组
     * 提取题目ID作为键，题目标题作为值，便于后续使用
     */
    if (!empty($ptitle)) {
        // 提取 id 列作为键
        $keys = array_column($ptitle, 'problem_id');

        // 提取 name 列作为值
        $values = array_column($ptitle, 'title');

        // 使用 array_combine 将键和值组合成一个映射
        $ptitle = array_combine($keys, $values);
    } else {
        $ptitle = array(); // 如果没有题目数据，返回空数组
    }
    // var_dump($ptitle);

}
/////////////////////////Template
require("template/" . $OJ_TEMPLATE . "/" . basename(__FILE__));
/////////////////////////Common foot
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');

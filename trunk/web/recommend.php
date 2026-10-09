<?php
// 题目推荐页（控制器）
// 以 syzoj 模板的 skeleton.php 结构为基准：本文件负责收集数据并设置视图变量，
// 再由 template/<模板>/recommend.php 渲染页面（header + 内容 + footer）。
require_once('include/init.php');   // 初始化：配置、数据库、会话、语言、$OJ_TEMPLATE、$OJ_CDN_URL 等

// 必须登录
if (!isset($_SESSION[$OJ_NAME . '_user_id'])) {
    header("location:loginpage.php");
    exit(0);
}
$user_id = $_SESSION[$OJ_NAME . '_user_id'];

// ---------- 1) 用户最近做过的 10 道不同题目（标题 + 分类 source） ----------
// 从全部历史中，按“最近一次提交时间”取该用户提交过的 10 道不同题目。
// 不论是否首次 AC、不论题目当前是否可见（比赛题/隐藏题 defunct='Y' 也计入），
// 用 LEFT JOIN 避免题目被隐藏/删除时整条历史丢失（这正是之前显示“0 道题目”的根因）。
// 仅“系统分类”与“可做题量”才要求 defunct='N'（见下）。
$recent = array();
$rows = pdo_query(
    "SELECT p.problem_id, p.title, p.source, MAX(s.in_date) AS last_time
     FROM solution s
     LEFT JOIN problem p ON p.problem_id = s.problem_id
     WHERE s.user_id = ?
     GROUP BY p.problem_id, p.title, p.source
     ORDER BY last_time DESC
     LIMIT 10",
    $user_id
);
foreach ($rows as $r) {
    if(empty($r['title'])) continue;
    $recent[] = array(
        'title'  => isset($r['title'])  ? $r['title']  : '',
        'source' => isset($r['source']) ? $r['source'] : '',
    );
}

// ---------- 2) 系统中的分类（题目来源 source 标签）与可做题量 ----------
$categories = array();
$cat_rows = pdo_query("SELECT source FROM problem WHERE defunct='N' AND source IS NOT NULL AND source<>'' and source not like 'http%' ");
foreach ($cat_rows as $c) {
    foreach (preg_split('/\s+/', trim($c['source'])) as $tag) {
        $tag = trim($tag);
        if ($tag !== '' && !in_array($tag, $categories, true)) {
            $categories[] = $tag;
        }
    }
}
$pc = pdo_query("SELECT COUNT(1) cnt FROM problem WHERE defunct='N'");
$problem_count = isset($pc[0]['cnt']) ? intval($pc[0]['cnt']) : 0;

// ---------- 3) 传递数据给视图 ----------
$view_title = "题目推荐";
$view_recent = $recent;
$view_categories = $categories;
$view_problem_count = $problem_count;
// 用于前端 POST 给 aiapi/demo.php 的上下文（转义 < 以防 </script> 截断）
$view_recent_json     = str_replace('<', '\u003C', json_encode($recent, JSON_UNESCAPED_UNICODE));
$view_categories_json = str_replace('<', '\u003C', json_encode($categories, JSON_UNESCAPED_UNICODE));

// ---------- 4) 渲染同名视图（优先当前模板，缺省回退 syzoj） ----------
$tpl = "template/" . $OJ_TEMPLATE . "/recommend.php";
if (!file_exists($tpl)) {
    $tpl = "template/syzoj/recommend.php";
}
require($tpl);

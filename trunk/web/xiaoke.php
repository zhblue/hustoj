<?php
/**
 * 消课统计 — 一体化入口
 *
 * 模式（?mode=）：
 *   self    默认，已登录用户查自己
 *   user    ?user_id=xxx，admin/teacher 查任意账号
 *   parent  ?phone=xxx&student=xxx，家长手机号查（不需登录）
 *   csv     ?...同上，导出 CSV
 *
 * 共用参数：
 *   start=YYYY-MM-DD  起（默认本月）
 *   end=YYYY-MM-DD    止（默认今天）
 *   min_ac=N          最少 AC 数（默认 1）
 *
 * 数据访问全部走 HUSTOJ 自带 pdo_query（PDO prepared statements）。
 */
$cache_time = 60;
require_once("./include/db_info.inc.php");

// ============================================================
// 自愈式 schema 升级 — 自动检测 + 自动 ALTER
// ============================================================
function xiaoke_ensure_schema() {
    $cache = '/tmp/xiaoke_schema_v1.done';
    if (file_exists($cache)) return true; // 已升级过

    try {
        $cols = pdo_query("SHOW COLUMNS FROM `users` LIKE 'parent_phone'");
        if (empty($cols)) {
            pdo_query("ALTER TABLE `users` ADD COLUMN `parent_phone` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '家长手机号' AFTER `school`");
            pdo_query("ALTER TABLE `users` ADD INDEX `idx_parent_phone` (`parent_phone`)");
        }
        @file_put_contents($cache, date('Y-m-d H:i:s'));
        return true;
    } catch (Exception $e) {
        error_log("[xiaoke] schema upgrade failed: " . $e->getMessage()
            . " — 请管理员手动运行: mysql jol < install/xiaoke_parent_phone.sql");
        return false;
    }
}
$xiaoke_schema_ok = xiaoke_ensure_schema();

require_once("./include/cache_start.php");
require_once("./include/const.inc.php");
require_once("./include/my_func.inc.php");

$mode = isset($_GET['mode']) ? trim($_GET['mode']) : 'self';
// 默认时间段：起 = 1 个月前，止 = 1 年后
// 这样扫码后家长可以长期使用同一个 URL（一年有效）
$start_dt = isset($_GET['start']) ? trim($_GET['start']) : date('Y-m-d', strtotime('-1 month'));
$end_dt   = isset($_GET['end'])   ? trim($_GET['end'])   : date('Y-m-d', strtotime('+1 year'));
$min_ac   = isset($_GET['min_ac']) ? max(0, intval($_GET['min_ac'])) : 1;

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $start_dt)) $start_dt = date('Y-m-d', strtotime('-1 month'));
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $end_dt))   $end_dt   = date('Y-m-d', strtotime('+1 year'));

// 时间窗：end_dt 取下一天 00:00:00，让 BETWEEN 包含 end_dt 当天
$start_dt_sql = $start_dt . ' 00:00:00';
$end_dt_sql   = date('Y-m-d', strtotime($end_dt . ' +1 day')) . ' 00:00:00';

// 设备检测：手机 / 微信 / 扫码访问 → 不显示二维码（避免套娃）
$ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
$is_mobile    = preg_match('/Mobile|Android|iPhone|iPad|iPod|BlackBerry|Windows Phone/i', $ua);
$is_wechat    = stripos($ua, 'MicroMessenger') !== false;
$is_miniprog  = stripos($ua, 'miniprogram') !== false;
$is_qr_visit  = isset($_GET['from']) && $_GET['from'] === 'qr';
$hide_qr      = $is_mobile || $is_wechat || $is_miniprog || $is_qr_visit;

// ============================================================
// 限速（家长/csv 模式专用，零新表 — 文件日志）
// ============================================================
function rate_limit_check($key, $max_per_hour = 30) {
    $log_file = "/tmp/xiaoke_query.log";
    $ip = $_SERVER['REMOTE_ADDR'];
    $now = time();
    $hour_ago = $now - 3600;
    $lines = is_file($log_file) ? file($log_file, FILE_IGNORE_NEW_LINES) : [];
    $cnt = 0; $kept = [];
    foreach ($lines as $l) {
        $parts = explode('|', $l);
        if (count($parts) >= 3 && intval($parts[0]) > $hour_ago && $parts[2] == $ip) $cnt++;
        if (intval($parts[0]) > $hour_ago) $kept[] = $l;
    }
    if ($cnt >= $max_per_hour) return false;
    $kept[] = "$now|$key|$ip";
    @file_put_contents($log_file, implode("\n", $kept) . "\n");
    return true;
}

// ============================================================
// 共用：参数解析 + 鉴权（按 mode 分支）
// ============================================================
$err = '';
$matched = null;          // 用户信息 [user_id, nick, school, parent_phone]
$cur_user = '';           // 当前登录用户
$is_privileged = false;

if ($mode == 'parent' || ($mode == 'csv' && isset($_GET['phone']) && isset($_GET['student']))) {
    $student = isset($_GET['student']) ? trim($_GET['student']) : '';
    if (!preg_match('/^1[3-9]\d{9}$/', $phone)) {
        $err = "请输入 11 位有效手机号";
    } elseif (!rate_limit_check("$phone|$student")) {
        $err = "查询过于频繁，请 1 小时后再试";
    } else {
        // PDO prepared statements — student 同时匹配 user_id 和 nick
        $sql_match = "SELECT user_id, nick, school, parent_phone
                      FROM `users`
                      WHERE `defunct`='N'
                        AND `parent_phone`=?
                        AND (`user_id`=? OR `nick`=?)
                      LIMIT 1";
        $rows = pdo_query($sql_match, $phone, $student, $student);
        if (!$rows || $rows === -1 || count($rows) == 0) {
            $err = "未找到该手机号关联的学员，请检查手机号和学员姓名";
        } else {
            $matched = $rows[0];
        }
    }
} else {
    // self / user — 都需要登录
    if (!isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
        echo "<a href='loginpage.php'>请先登录</a>";
        exit(0);
    }
    $cur_user = $_SESSION[$OJ_NAME . '_' . 'user_id'];
    $is_privileged = isset($_SESSION[$OJ_NAME . '_' . 'administrator'])
                  || isset($_SESSION[$OJ_NAME . '_' . 'contest_creator'])
                  || isset($_SESSION[$OJ_NAME . '_' . 'teacher']);

    if ($mode == 'user') {
        $view_user = isset($_GET['user_id']) ? trim($_GET['user_id']) : '';
        if ($view_user == '' || ($view_user != $cur_user && !$is_privileged)) {
            $err = "无权查看他人的消课记录";
        } else {
            $rows = pdo_query("SELECT user_id, nick, school FROM `users` WHERE `user_id`=? LIMIT 1", $view_user);
            if (!$rows || $rows === -1 || count($rows) == 0) {
                $err = "用户不存在";
            } else {
                $matched = $rows[0];
            }
        }
    } else {
        // self 默认看自己
        $rows = pdo_query("SELECT user_id, nick, school FROM `users` WHERE `user_id`=? LIMIT 1", $cur_user);
        if ($rows && $rows !== -1 && count($rows) > 0) $matched = $rows[0];
        if (!$matched) {
            $err = "用户不存在";
        }
    }
}

// ============================================================
// 共用：核心 SQL（按 user 查消课）
// 注意：min_ac 已 intval()，可安全直接拼接进 HAVING（必须是整数）
// ============================================================
function fetch_attendance($user_id, $start_dt_sql, $end_dt_sql, $min_ac, $is_summary = false) {
    if ($is_summary) {
        $sql = "SELECT COUNT(DISTINCT s.contest_id) lessons,
                       COUNT(s.solution_id) submits,
                       SUM(s.result=4) acs,
                       MIN(s.in_date) first_try,
                       MAX(s.in_date) last_try
                FROM `solution` s
                JOIN `contest`   c ON s.contest_id = c.contest_id
                WHERE s.user_id=?
                  AND s.contest_id>0
                  AND s.num>=0
                  AND c.start_time>=?
                  AND c.start_time<?";
        $rows = pdo_query($sql, $user_id, $start_dt_sql, $end_dt_sql);
        return ($rows && $rows !== -1 && count($rows) > 0)
            ? $rows[0]
            : ['lessons'=>0,'submits'=>0,'acs'=>0,'first_try'=>null,'last_try'=>null];
    }
    $sql = "SELECT s.contest_id,
                   c.title,
                   c.start_time,
                   c.end_time,
                   c.contest_type,
                   COUNT(s.solution_id) submits,
                   SUM(s.result=4)      acs,
                   ROUND(AVG(s.pass_rate)*100,1) avg_rate,
                   MIN(s.in_date) first_try,
                   MAX(s.in_date) last_try,
                   TIMESTAMPDIFF(MINUTE, MIN(s.in_date), MAX(s.in_date)) duration_min
            FROM `solution` s
            JOIN `contest`   c ON s.contest_id = c.contest_id
            WHERE s.user_id=?
              AND s.contest_id>0
              AND s.num>=0
              AND c.start_time>=?
              AND c.start_time<?
            GROUP BY s.contest_id
            HAVING acs >= $min_ac
            ORDER BY c.start_time DESC";
    $rows = pdo_query($sql, $user_id, $start_dt_sql, $end_dt_sql);
    return ($rows && $rows !== -1) ? $rows : [];
}

// ============================================================
// CSV 导出（独立分支，无 HTML 输出）
// ============================================================
if ($mode == 'csv') {
    if ($err) die($err);
    $view_uid = $matched['user_id'];
    $rows = fetch_attendance($view_uid, $start_dt_sql, $end_dt_sql, $min_ac);
    $filename = "xiaoke_{$view_uid}_{$start_dt}_to_{$end_dt}.csv";
    header("Content-Type: text/csv; charset=utf-8");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    echo "\xEF\xBB\xBF";
    echo "课次ID,课程名称,开始时间,结束时间,提交数,AC数,AC率%,首次提交,末次提交\n";
    foreach ($rows as $r) {
        $rate = $r['submits'] > 0 ? round($r['acs'] / $r['submits'] * 100, 1) : 0;
        echo implode(',', array_map(function($v){
            return '"' . str_replace('"','""',$v) . '"';
        }, [
            $r['contest_id'], $r['title'], $r['start_time'], $r['end_time'],
            $r['submits'], $r['acs'], $rate,
            $r['first_try'], $r['last_try']
        ])) . "\n";
    }
    exit(0);
}

// ============================================================
// HTML 输出
// ============================================================
require("template/" . $OJ_TEMPLATE . "/header.php");

// 计算通用 URL 参数（用于表单默认值 + 链接）
$base_qs = http_build_query(array_filter([
    'mode'   => $mode,
    'start'  => $start_dt,
    'end'    => $end_dt,
    'min_ac' => $min_ac,
    'user_id'=> ($mode=='user') ? $matched['user_id'] : '',
    'phone'  => ($mode=='parent') ? $phone : '',
    'student'=> ($mode=='parent') ? $student : '',
]));
$form_action = "xiaoke.php";
?>

<div class="container">
  <h3>
    消课统计
    <?php if ($mode == 'parent') { ?>
      <small>家长查询入口</small>
    <?php } elseif ($mode == 'user') { ?>
      <small>查看学员：<?php echo htmlspecialchars($matched['user_id']); ?></small>
    <?php } else { ?>
      <small>我的消课记录</small>
    <?php } ?>
  </h3>

  <?php if ($mode == 'parent') { ?>
    <div class="alert alert-info" style="max-width:760px">
      <p><strong>使用说明</strong></p>
      <ol>
        <li>输入您预留的家长手机号 + 学员账号或姓名</li>
        <li>系统会显示该学员 <?php echo htmlspecialchars($start_dt); ?> 至 <?php echo htmlspecialchars($end_dt); ?> 之间的消课明细</li>
        <li>判定规则：AC 数 ≥ <?php echo $min_ac; ?> 道即视为已上课</li>
        <li>每小时最多查询 30 次</li>
        <li>手机扫码可一键查询：机构 / 教师生成下方二维码，家长用微信扫描即可跳转本页面</li>
      </ol>
    </div>
  <?php } ?>

  <?php if ($err) { ?>
    <div class="alert alert-danger" style="max-width:760px"><?php echo htmlspecialchars($err); ?></div>
  <?php } ?>

  <?php if (!$xiaoke_schema_ok) { ?>
    <div class="alert alert-warning" style="max-width:760px">
      ⚠️ <strong>数据库字段未自动升级</strong>。请管理员手动执行：
      <code>mysql jol &lt; install/xiaoke_parent_phone.sql</code><br>
      家长查询功能可能不可用。
    </div>
  <?php } ?>

  <form class="form-inline" method="get" action="<?php echo $form_action; ?>" style="margin-bottom:16px">
    <input type="hidden" name="mode" value="<?php echo htmlspecialchars($mode); ?>">
    <?php if ($mode == 'user') { ?>
      <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($matched ? $matched['user_id'] : ''); ?>">
    <?php } ?>
    <?php if ($mode == 'parent') { ?>
      <label>家长手机号&nbsp;<input type="tel" name="phone" maxlength="11" required
        value="<?php echo htmlspecialchars($phone); ?>" class="form-control" placeholder="13800138000"></label>
      &nbsp;
      <label>学员账号/姓名&nbsp;<input type="text" name="student" required
        value="<?php echo htmlspecialchars($student); ?>" class="form-control" placeholder="zhanglei 或 张三"></label>
      &nbsp;
    <?php } ?>
    <label>起&nbsp;<input type="date" name="start" value="<?php echo htmlspecialchars($start_dt); ?>" class="form-control"></label>
    &nbsp;
    <label>止&nbsp;<input type="date" name="end"   value="<?php echo htmlspecialchars($end_dt); ?>"   class="form-control"></label>
    &nbsp;
    <label>最少 AC&nbsp;<input type="number" name="min_ac" value="<?php echo $min_ac; ?>" min="0" max="99" style="width:60px" class="form-control"></label>
    &nbsp;
    <button type="submit" class="btn btn btn-primary">查询</button>
    <?php if ($mode == 'self') { ?>
      &nbsp;&nbsp;<a href="xiaoke.php?mode=user" class="btn btn-default">查看学员（特权）</a>
    <?php } elseif ($mode == 'user' && $is_privileged) { ?>
      &nbsp;&nbsp;<a href="xiaoke.php" class="btn btn-default">看我自己</a>
    <?php } ?>
  </form>

  <?php
  if ($matched && !$err) {
      $view_uid = $matched['user_id'];
      $rows = fetch_attendance($view_uid, $start_dt_sql, $end_dt_sql, $min_ac);
      $sum  = fetch_attendance($view_uid, $start_dt_sql, $end_dt_sql, $min_ac, true);

      $lesson_cnt    = count($rows);
      $total_submits = intval($sum['submits']);
      $total_acs     = intval($sum['acs']);
      $ac_rate       = $total_submits > 0 ? round($total_acs / $total_submits * 100, 1) : 0;

      // CSV 下载链接
      $csv_qs = http_build_query(array_filter([
          'mode' => 'csv',
          'start'=> $start_dt,
          'end'  => $end_dt,
          'min_ac' => $min_ac,
          'user_id' => ($mode == 'user' || $mode == 'self') ? $view_uid : '',
          'phone' => ($mode == 'parent') ? $phone : '',
          'student' => ($mode == 'parent') ? $student : '',
      ]));
  ?>
      <?php if ($mode == 'parent') { ?>
        <div class="panel panel-success" style="max-width:760px">
          <div class="panel-heading">学员信息</div>
          <div class="panel-body">
            <p><strong>账号：</strong><?php echo htmlspecialchars($matched['user_id']); ?></p>
            <p><strong>姓名：</strong><?php echo htmlspecialchars($matched['nick']); ?></p>
            <?php if ($matched['school']) { ?>
              <p><strong>学校：</strong><?php echo htmlspecialchars($matched['school']); ?></p>
            <?php } ?>
          </div>
        </div>
      <?php } ?>

      <div class="row" style="margin-bottom:20px">
        <div class="col-md-3"><div class="panel panel-primary">
          <div class="panel-heading">上课课次</div>
          <div class="panel-body"><h2><?php echo $lesson_cnt; ?></h2></div>
        </div></div>
        <div class="col-md-3"><div class="panel panel-info">
          <div class="panel-heading">总提交数</div>
          <div class="panel-body"><h2><?php echo $total_submits; ?></h2></div>
        </div></div>
        <div class="col-md-3"><div class="panel panel-success">
          <div class="panel-heading">AC 数</div>
          <div class="panel-body"><h2><?php echo $total_acs; ?> <small style="font-size:14px">(<?php echo $ac_rate; ?>%)</small></h2></div>
        </div></div>
        <div class="col-md-3"><div class="panel panel-warning">
          <div class="panel-heading">平均每节</div>
          <div class="panel-body"><h2><?php echo $lesson_cnt > 0 ? round($total_submits/$lesson_cnt, 1) : 0; ?>
            <small style="font-size:14px">提交</small></h2>
        </div></div>
      </div>

      <p>
        <a href="xiaoke.php?<?php echo $csv_qs; ?>" class="btn btn-sm btn-default">导出 CSV</a>
      </p>

      <table class="table table-striped table-hover table-condensed">
        <thead>
          <tr>
            <th>#</th>
            <th>课程/比赛</th>
            <th>开始时间</th>
            <th>提交</th>
            <th>AC</th>
            <th>AC率</th>
            <th>首次提交</th>
            <th>末次提交</th>
            <th>用时</th>
            <th>操作</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $i = 1;
        foreach ($rows as $r) {
            $cid   = intval($r['contest_id']);
            $title = htmlspecialchars($r['title']);
            $start = htmlspecialchars($r['start_time']);
            $sub   = intval($r['submits']);
            $acs   = intval($r['acs']);
            $rate  = htmlspecialchars($r['avg_rate']);
            $first = htmlspecialchars($r['first_try']);
            $last  = htmlspecialchars($r['last_try']);
            $dur   = intval($r['duration_min']);
            echo "<tr>";
            echo "<td>$i</td>";
            echo "<td><a href='contestrank.php?cid=$cid'>$title</a></td>";
            echo "<td><small>$start</small></td>";
            echo "<td>$sub</td>";
            echo "<td>$acs</td>";
            echo "<td>$rate%</td>";
            echo "<td><small>$first</small></td>";
            echo "<td><small>$last</small></td>";
            echo "<td>{$dur}分</td>";
            echo "<td><a href='status.php?user_id=".urlencode($view_uid)."&cid=$cid' class='btn btn-xs btn-default'>提交</a></td>";
            echo "</tr>";
            $i++;
        }
        if ($lesson_cnt == 0) {
            echo "<tr><td colspan='10' class='text-center text-muted'>该时间段内没有消课记录</td></tr>";
        }
        ?>
        </tbody>
      </table>

      <?php if ($mode == 'parent' && !$err && !$hide_qr) { ?>
      <!-- 扫码查询二维码：只在 PC 端 / 非微信环境显示 -->
      <div id="xiaoke-qr-block" class="panel panel-warning" style="max-width:760px; text-align:center; margin-top:20px">
        <div class="panel-heading">📱 扫码查询 — 家长手机扫一扫</div>
        <div class="panel-body">
          <div id="qrcode" style="display:inline-block; padding:12px; background:#fff; border:1px solid #ddd;"></div>
          <p style="margin-top:10px">
            <small class="text-muted">手机扫码后会自动打开本页并显示数据</small><br>
            <a id="qr-link" href="#" style="font-size:12px; word-break:break-all;"></a>
          </p>
          <p><small class="text-muted">ⓘ 手机端或微信打开本页面时不会显示二维码（避免扫码套娃）。</small></p>
        </div>
      </div>
      <script src="include/qrcode.min.js"></script>
      <script>
      (function(){
        var url = window.location.href;
        document.getElementById('qr-link').href = url;
        document.getElementById('qr-link').textContent = url;
        new QRCode(document.getElementById('qrcode'), {
          text: url, width: 200, height: 200,
          colorDark: '#000', colorLight: '#fff',
          correctLevel: QRCode.CorrectLevel.M
        });
      })();
      </script>
      <?php } ?>
  <?php } ?>

</div>

<?php require("template/" . $OJ_TEMPLATE . "/footer.php"); ?>

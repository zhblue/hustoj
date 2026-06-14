<?php
require_once("../include/db_info.inc.php");
require_once ("../include/my_func.inc.php");

// 权限校验
if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'.'contest_creator']) || isset($_SESSION[$OJ_NAME.'_'.'problem_editor']) || isset($_SESSION[$OJ_NAME.'_'.'password_setter']))){
    exit(1);
}

// 高效读取系统内存信息
$meminfo = @file_get_contents('/proc/meminfo');
if ($meminfo === false) $meminfo = '';
preg_match('/MemTotal:\s+(\d+) kB/', $meminfo, $mt);
preg_match('/MemAvailable:\s+(\d+) kB/', $meminfo, $ma);
if (empty($ma)) preg_match('/MemFree:\s+(\d+) kB/', $meminfo, $ma);
preg_match('/SwapTotal:\s+(\d+) kB/', $meminfo, $st);
preg_match('/SwapFree:\s+(\d+) kB/', $meminfo, $sf);

$total_mem = isset($mt[1]) ? round($mt[1] / 1024) : 0;
$free_mem = isset($ma[1]) ? round($ma[1] / 1024) : 0;
$total_swap = isset($st[1]) ? round($st[1] / 1024) : 0;
$free_swap = isset($sf[1]) ? round($sf[1] / 1024) : 0;
$used_swap = $total_swap - $free_swap;

$delay = 2;          // 前端实时刷新间隔（秒）
$log_interval = 60;   // 历史数据落盘间隔（秒），每分钟记录一个点以完美支撑1周跨度
$logfile = "/dev/shm/" . basename(__FILE__, "php") . "log";

$history = @file_get_contents($logfile);
$history = ($history !== false && $history !== "") ? json_decode($history, true) : array();
if (!is_array($history)) $history = array();

$current_time_ms = time() * 1000;
$one_week_ago_ms = $current_time_ms - (7 * 24 * 3600 * 1000);

// 【核心修复】严格筛选：只保留过去1周内且合法的毫秒级时间戳，彻底清除脏数据
$history = array_filter($history, function($sample) use ($one_week_ago_ms) {
    return isset($sample[4]) && $sample[4] > $one_week_ago_ms;
});
$history = array_values($history);
$HL = count($history) - 1;

$info = array();
if (function_exists('exec')) {
    date_default_timezone_set("PRC");

    // 1. CPU 利用率
    $cpu_out = array();
    exec("LC_ALL=C top -bn1 | grep -i 'Cpu(s)' | awk -F',' '{print $4}' | grep -o '[0-9.]*'", $cpu_out);
    $cpu_usage = isset($cpu_out[0]) ? (100 - floatval($cpu_out[0])) : 0;
    $info[0] = round($cpu_usage, 2);

    // 2. 内存与 Swap (MB)
    $info[1] = $free_mem;
    $info[2] = $used_swap;

    // 3. TCP 连接数
    $sockstat = @file_get_contents('/proc/net/sockstat');
    preg_match('/TCP: inuse (\d+)/', $sockstat, $tcp_match);
    $info[3] = isset($tcp_match[1]) ? floatval($tcp_match[1]) : 0;

    // 4. 当前时间戳 (毫秒)
    $info[4] = $current_time_ms;

    // 5. 磁盘与总空间
    $info[5] = round((disk_total_space("/") - disk_free_space("/")) / 1048576);
    $info[6] = round(disk_total_space("/") / 1073741824);

    // 【性能优化】达到设定的时间间隔（1分钟）才允许写入历史文件
    if ($HL < 0 || ($current_time_ms - $history[$HL][4] >= $log_interval * 1000)) {
        array_push($history, $info);
        while (count($history) > 10500) array_shift($history); // 1周 = 7天 * 1440分钟 = 10080个点
        file_put_contents($logfile, json_encode($history));
        $HL = count($history) - 1;
    }
}

// 拼装输出给图表的数据：历史轨迹 + 当前最新实时点（保证实时刷新的连贯性）
$output_history = $history;
if ($HL >= 0 && $history[$HL][4] < $current_time_ms) {
    array_push($output_history, $info);
}

$chart_cpu = array();
$chart_mem = array();
$chart_swap = array();
$chart_tcp = array();

foreach ($output_history as $sample) {
    array_push($chart_cpu, array($sample[4], $sample[0]));
    array_push($chart_mem, array($sample[4], $total_mem ? ($sample[1] / $total_mem * 100) : 0));
    array_push($chart_swap, array($sample[4], $total_swap ? ($sample[2] / $total_swap * 100) : 0));
    array_push($chart_tcp, array($sample[4], $sample[3]));
}

if (isset($_GET['json'])) {
    echo json_encode(array($chart_cpu, $chart_mem, $chart_swap, $chart_tcp));
    exit();
} else {
    $AG = array("_", ".", ":", "i", "!");
    $cpu_ag = "";
    $al = 9 - strlen(strval($info[0] ?? '0'));
    $OL = count($output_history) - 1;
    if ($OL > $al) {
        for ($i = $al; $i >= 0; $i--) {
            $val = $output_history[$OL - $i][0] ?? 1;
            $cpu_ag .= $AG[min(4, intval(log($val ?: 1, 3)))];
        }
    }
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>系统负荷监控</title>
</head>
<body bgcolor="white">
<?php if (!isset($_GET['notext'])) { ?>
<h1 style="color:#ffffff">
    <span style="color:rgb(175,216,248)">CPU :<span id="cpu"><?php echo ($info[0] ?? 0) . $cpu_ag ?></span>%</span><br>
    <span style="color:rgb(237,194,64)">FREE:<span id="mem"><?php echo $info[1] ?? 0 ?></span>MB</span><br>
    <span style="color:rgb(77,167,77)">SWAP:<span id="swap"><?php echo $info[2] ?? 0 ?></span>MB</span><br>
    <span style="color:rgb(203,75,75)">TCP :<span id="tcp"><?php echo $info[3] ?? 0 ?></span> </span> <br>
    <span style="color:rgb(150,150,150)">DISK:<span id="disk"><?php echo $info[5] ?? 0 ?></span>MB (Used)</span> <br>
    <span style="color:rgb(150,150,150)">TOTAL:<span id="nas"><?php echo $info[6] ?? 0 ?></span>GB</span> <br>
</h1>
<?php } ?>
    <script src="../template/bs3/jquery.min.js"></script>
    <script src="/include/jquery.flot.js"></script>
    <div id="panel" style="width:98%;height:180px" onclick='update()'>loading data ...</div>
    <script type="text/javascript">
        function update() {
            $.getJSON("<?php echo basename(__FILE__) ?>?json", function(result) {
let cpu = result[0] || [];
let mem = result[1] || [];
let swap = result[2] || [];
let tcp = result[3] || [];
if (!cpu.length || !mem.length || !swap.length || !tcp.length) {
    $("#cpu,#mem,#swap,#tcp").text("N/A");
    return;
}
                $.plot($("#panel"), [
                    { label: "FREE M: " + (<?php echo $total_mem ?> * mem[mem.length - 1][1] / 100).toFixed(0), data: mem, lines: { show: true } },
                    { label: "CPU: " + cpu[cpu.length - 1][1] + "%", data: cpu, bars: { show: true } },
                    { label: "TCP: " + tcp[tcp.length - 1][1], data: tcp, lines: { show: true } },
                    { label: "SWAP: " + (<?php echo $total_swap ?> * swap[swap.length - 1][1] / 100).toFixed(0), data: swap, lines: { show: true } }
                ], {
                    grid: {
                        backgroundColor: { colors: ["#aaaaee", "#ffffff"] },
                        color: "#00aa00",
                        show: true
                    },
                    xaxis: { mode: "time" },
                    legend: { position: "nw" }
                });
                
                $("#cpu").text(cpu[cpu.length - 1][1].toFixed(2) + "%");
                $("#mem").text((<?php echo $total_mem ?> * mem[mem.length - 1][1] / 100).toFixed(0));
                $("#swap").text((<?php echo $total_swap ?> * swap[swap.length - 1][1] / 100).toFixed(0));
                $("#tcp").text(tcp[tcp.length - 1][1]);
            });
        }
        $(document).ready(function() {
            window.setInterval(update, <?php echo $delay * 1000 ?>);
        });
    </script>

<?php if (!isset($_GET['notext'])) { ?>
<br><br><br><br><br><br><br><br>
<?php } ?>
</body>
</html>
<?php } ?>

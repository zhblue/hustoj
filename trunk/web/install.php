<?php
////////////////////////////Common head
if (file_exists("include/db_info.inc.php")) {
    header("location:index.php");
    exit(0);
}

// 配置键名映射数组
$configNameMap = array(
    "DB_HOST" => "数据库服务器",
    "DB_NAME" => "数据库名",
    "DB_USER" => "数据库用户名",
    "DB_PASS" => "数据库密码",
    "OJ_NAME" => "系统名称",
    "OJ_HOME" => "网站根目录",
    "OJ_ADMIN" => "管理员email",
    "OJ_DATA" => "测试数据目录",
    "OJ_BBS" => "论坛模块",
    "OJ_ONLINE" => "记录在线",
    "OJ_LANG" => "默认语言",
    "OJ_SIM" => "显示相似度",
    "OJ_DICT" => "显示在线翻译",
    "OJ_LANGMASK" => "可用编程语言掩码",
    "OJ_ACE_EDITOR" => "代码高亮",
    "OJ_AUTO_SHARE" => "代码共享",
    "OJ_CSS" => "色系样式表",
    "OJ_SAE" => "新浪云",
    "OJ_VCODE" => "验证码",
    "OJ_REG_SPEED" => "注册速度限制",
    "OJ_APPENDCODE" => "附加代码模式",
    "OJ_CE_PENALTY" => "编译错误是否罚时",
    "OJ_PRINTER" => "打印模块",
    "OJ_MAIL" => "内邮系统",
    "OJ_MARK" => "显示得分还是错误百分比",
    "OJ_MEMCACHE" => "是否启用Memcache",
    "OJ_MEMSERVER" => "Memcached服务器地址",
    "OJ_MEMPORT" => "Memcached服务器端口",
    "OJ_JUDGE_HUB_PATH" => "SaaS模式时本OJ所在Hub子目录",
    "OJ_CDN_URL" => "外挂CDN根路径",
    "OJ_TEMPLATE" => "选用皮肤",
    "OJ_BG" => "背景图URL",
    "OJ_REGISTER" => "允许注册",
    "OJ_REG_NEED_CONFIRM" => "注册是否需要管理员确认",
    "OJ_NEED_LOGIN" => "强制登录",
    "OJ_LONG_LOGIN" => "长时间保持登录",
    "OJ_KEEP_TIME" => "保持登录的时间",
    "OJ_SHOW_DIFF" => "显示错误对比",
    "OJ_DOWNLOAD" => "允许下载",
    "OJ_TEST_RUN" => "测试运行",
    "OJ_MATHJAX" => "激活mathjax",
    "OJ_BLOCKLY" => "启用Blockly",
    "OJ_ENCODE_SUBMIT" => "启用base64编码提交",
    "OJ_OI_1_SOLUTION_ONLY" => "仅保留最后一次提交",
    "OJ_OI_MODE" => "开启OI比赛模式",
    "OJ_SHOW_METAL" => "显示奖牌",
    "OJ_BENCHMARK_MODE" => "压测模式",
    "OJ_CONTEST_RANK_FIX_HEADER" => "固定名单",
    "OJ_NOIP_KEYWORD" => "NOIP关键词",
    "OJ_BEIAN" => "备案号",
    "OJ_FRIENDLY_LEVEL" => "系统友好级别",
    "OJ_FREE_PRACTICE" => "自由练习",
    "OJ_SUBMIT_COOLDOWN_TIME" => "冷却时间",
    "OJ_MARKDOWN" => "启Markdown语法",
    "OJ_INDEX_NEWS_TITLE" => "首页文章标题",
    "OJ_DIV_FILTER" => "过滤题面中的div",
    "OJ_LIMIT_TO_1_IP" => "限制登录IP",
    "OJ_REMOTE_JUDGE" => "远程评测",
    "OJ_NO_CONTEST_WATCHER" => "禁止无权限用户观战",
    "OJ_SHARE_CODE" => "代码分享",
    "OJ_MENU_NEWS" => "新闻菜单"
);

// 需要过滤的配置项
$filteredConfigKeys = array(
    "session.cookie_httponly",
    "OJ_ON_SITE_TEAM_TOTAL",
    "OJ_LOG",
    "OJ_QQ",
    "OJ_RR",
    "OJ_REDIS",
    "SAE",
    "BENCH",
    "CONTEST",
    "OJ_BBS",
    "OJ_RANK",
    "OJ_OPEN",
    "OJ_SaaS",
    "OJ_UDP",
    "OJ_WEIBO"
);

/**
 * 将配置键名转换为中文名称
 * 
 * 该函数接收一个配置键名（去掉第一个字符后），并将其转换为对应的中文描述
 * 如果找不到对应的中文名称，则返回原始键名
 * 
 * @param string $k 配置键名（去掉第一个字符后的部分）
 * @return string 对应的中文名称或原始键名
 */
function toName($k)
{
    global $configNameMap;
    $k = mb_substr($k, 1);
    if (isset($configNameMap[$k])) {
        return $configNameMap[$k];
    } else {
        return $k;
    }
}

/**
 * 将配置项转换为HTML输入框
 * 
 * 该函数将配置项转换为HTML表单输入框，同时过滤掉一些不需要显示的配置项
 * 
 * @param string $temp 配置项字符串，格式为"key=value"
 * @return string HTML输入框代码，如果配置项被过滤则返回空字符串
 */
function toInput($temp)
{
    global $filteredConfigKeys;
    
    $kv = explode("=", $temp);
    if (!isset($kv[0]) || !isset($kv[1])) {
        return "";
    }
    
    // 检查是否需要过滤
    foreach ($filteredConfigKeys as $filterKey) {
        if (mb_strpos($kv[0], $filterKey) !== false) {
            return "";
        }
    }

    $escapedValue = htmlspecialchars($kv[1], ENT_QUOTES, 'UTF-8');
    $escapedKey = htmlspecialchars($kv[0], ENT_QUOTES, 'UTF-8');
    $oldValue = htmlspecialchars($kv[1], ENT_QUOTES, 'UTF-8');
    
    $temp = toName($kv[0]) . ":<input name='" . $escapedKey . "' value='" . $escapedValue . "' >\n";
    $temp .= "\t<input name='old_" . $escapedKey . "' type='hidden' value='" . $oldValue . "' >\n";

    return $temp . "\n";
}

/**
 * 判断字符串是否为布尔值
 * 
 * 检查给定的字符串是否为"true"或"false"（不区分大小写）
 * 
 * @param string $v 待检查的字符串
 * @return bool 如果字符串为"true"或"false"则返回true，否则返回false
 */
function isBool($v)
{
    $lowerV = strtolower($v);
    if ($lowerV === "true" || $lowerV === "false") {
        return true;
    } else {
        return false;
    }
}

/**
 * 验证配置键名是否合法
 * 
 * @param string $key 配置键名
 * @return bool 如果键名合法则返回true
 */
function isValidConfigKey($key) {
    // 只允许字母、数字、下划线和点号
    return preg_match('/^[a-zA-Z0-9_.]+$/', $key) === 1;
}

/**
 * 根据POST数据生成配置文件内容
 * 
 * 该函数遍历POST数据，将配置项的值替换到原始配置内容中
 * 
 * @param string $config 原始配置文件内容
 * @return string 更新后的配置文件内容
 */
function generate_config($config)
{
    $ret = "";
    foreach ($_POST as $k => $v) {
        if (mb_substr($k, 0, 4) == "old_") {
            $ret = "old";
        } else {
            // 验证配置键名
            if (!isValidConfigKey($k)) {
                continue; // 跳过非法键名
            }
            
            $oldKey = 'old_' . $k;
            if (isset($_POST[$oldKey])) {
                $oldValue = $_POST[$oldKey];
                
                // 验证旧值是否为布尔值或数字
                if (isBool($oldValue) || is_numeric($oldValue)) {
                    $new = $k . "=" . $v;
                } else {
                    // 对于字符串值，进行适当的转义
                    $escapedValue = addslashes($v);
                    $new = $k . "='" . $escapedValue . "'";
                }
                
                $old = $k . "=" . $oldValue;
                $config = str_replace($old, $new, $config);
            }
        }
    }
    return $config;
}

// 设置缓存时间和模板配置
$cache_time = 30;
$OJ_CACHE_SHARE = false;
$OJ_LANG = "en";
$OJ_TEMPLATE = "syzoj";
$view_title = "Install HUST Online Judge";

// 从远程获取配置模板
$config = @file_get_contents("https://gitee.com/zhblue/hustoj/raw/master/trunk/web/include/db_info.inc.php");
if ($config === false) {
    die("无法获取配置模板文件");
}

if (isset($_POST['$DB_HOST'])) {
    $ret = generate_config($config);
    
    // 验证路径，防止路径遍历
    $targetPath = "include/db_info.inc.php";
    if (realpath(dirname($targetPath)) !== realpath(dirname(__FILE__) . '/include')) {
        die("非法路径");
    }
    
    $result = file_put_contents($targetPath, $ret);
    if ($result === false) {
        die("无法写入配置文件");
    }
    
    header("Location: index.php");
    exit(0);
}

$view_errors .= "<div class='ui main container'><h3>
        HUSTOJ Web installation tools / db_info.inc.php generator </h3><br>
        <form action='install.php' method='post'>
";

// 解析配置文件内容，生成表单输入框
$cur = 0;
while (($strpos = mb_strpos($config, "\$", $cur)) !== false) {
    $cur = $strpos;
    $end = mb_strpos($config, ";", $cur);
    if ($end === false) break;
    
    $comment_start = mb_strpos($config, "//", $end + 1);
    $comment_end = mb_strpos($config, "\n", $end + 1);

    $input = toInput(mb_substr($config, $cur, $end - $cur));
    if ($input != "") {
        $view_errors .= $input;
        if ($comment_start > 0 && $comment_end > $comment_start) {
            $view_errors .= mb_substr($config, $comment_start + 2, $comment_end - $comment_start - 2);
        }
        $view_errors .= "<br>\n";
    }
    $cur = $end + 1;
}

$view_errors .= "
                <input type='submit'>
        </form></div>

";
require("template/" . $OJ_TEMPLATE . "/css.php");
require("template/" . $OJ_TEMPLATE . "/js.php");
require("template/" . $OJ_TEMPLATE . "/error.php");
/////////////////////////Common foot


<?php
////////////////////////////Common head
if (file_exists("include/db_info.inc.php")) {
    header("location:index.php");
    exit(0);
}
function toName($k)
{
    $k = mb_substr($k, 1);
    $name = array();
    $name["DB_HOST"] = "数据库服务器";
    $name["DB_NAME"] = "数据库名";
    $name["DB_USER"] = "数据库用户名";
    $name["DB_PASS"] = "数据库密码";
    $name["OJ_NAME"] = "系统名称";
    $name["OJ_HOME"] = "网站根目录";
    $name["OJ_ADMIN"] = "管理员email";
    $name["OJ_DATA"] = "测试数据目录";
    $name["OJ_BBS"] = "论坛模块";
    $name["OJ_ONLINE"] = "记录在线";
    $name["OJ_LANG"] = "默认语言";
    $name["OJ_SIM"] = "显示相似度";
    $name["OJ_DICT"] = "显示在线翻译";
    $name["OJ_LANGMASK"] = "可用编程语言掩码";
    $name["OJ_ACE_EDITOR"] = "代码高亮";
    $name["OJ_AUTO_SHARE"] = "代码共享";
    $name["OJ_CSS"] = "色系样式表";
    $name["OJ_SAE"] = "新浪云";
    $name["OJ_VCODE"] = "验证码";
    $name["OJ_REG_SPEED"] = "注册速度限制";
    $name["OJ_APPENDCODE"] = "附加代码模式";
    $name["OJ_CE_PENALTY"] = "编译错误是否罚时";
    $name["OJ_PRINTER"] = "打印模块";
    $name["OJ_MAIL"] = "内邮系统";
    $name["OJ_MARK"] = "显示得分还是错误百分比";
    $name["OJ_MEMCACHE"] = "是否启用Memcache";
    $name["OJ_MEMSERVER"] = "Memcached服务器地址";
    $name["OJ_MEMPORT"] = "Memcached服务器端口";
    $name["OJ_JUDGE_HUB_PATH"] = "SaaS模式时本OJ所在Hub子目录";
    $name["OJ_CDN_URL"] = "外挂CDN根路径";
    $name["OJ_TEMPLATE"] = "选用皮肤";
    $name["OJ_BG"] = "背景图URL";
    $name["OJ_REGISTER"] = "允许注册";
    $name["OJ_REG_NEED_CONFIRM"] = "注册是否需要管理员确认";
    $name["OJ_NEED_LOGIN"] = "强制登录";
    $name["OJ_LONG_LOGIN"] = "长时间保持登录";
    $name["OJ_KEEP_TIME"] = "保持登录的时间";
    $name["OJ_SHOW_DIFF"] = "显示错误对比";
    $name["OJ_DOWNLOAD"] = "允许下载";
    $name["OJ_TEST_RUN"] = "测试运行";
    $name["OJ_MATHJAX"] = "激活mathjax";
    $name["OJ_BLOCKLY"] = "启用Blockly";
    $name["OJ_ENCODE_SUBMIT"] = "启用base64编码提交";
    $name["OJ_OI_1_SOLUTION_ONLY"] = "仅保留最后一次提交";
    $name["OJ_OI_MODE"] = "开启OI比赛模式";
    $name["OJ_SHOW_METAL"] = "显示奖牌";
    $name["OJ_BENCHMARK_MODE"] = "压测模式";
    $name["OJ_CONTEST_RANK_FIX_HEADER"] = "固定名单";
    $name["OJ_NOIP_KEYWORD"] = "NOIP关键词";
    $name["OJ_BEIAN"] = "备案号";
    $name["OJ_FRIENDLY_LEVEL"] = "系统友好级别";
    $name["OJ_FREE_PRACTICE"] = "自由练习";
    $name["OJ_SUBMIT_COOLDOWN_TIME"] = "冷却时间";
    $name["OJ_MARKDOWN"] = "启Markdown语法";
    $name["OJ_INDEX_NEWS_TITLE"] = "首页文章标题";
    $name["OJ_DIV_FILTER"] = "过滤题面中的div";
    $name["OJ_LIMIT_TO_1_IP"] = "限制登录IP";
    $name["OJ_REMOTE_JUDGE"] = "远程评测";
    $name["OJ_NO_CONTEST_WATCHER"] = "禁止无权限用户观战";
    $name["OJ_SHARE_CODE"] = "代码分享";
    $name["OJ_MENU_NEWS"] = "新闻菜单";
    if (isset($name[$k])) return $name[$k];
    else return $k;
}

function toInput($temp)
{
    $kv = explode("=", $temp);
    if (mb_strpos($kv[0], "session.cookie_httponly") > 0) return "";
    if (mb_strpos($kv[0], "OJ_ON_SITE_TEAM_TOTAL") > 0) return "";
    if (mb_strpos($kv[0], "OJ_LOG") > 0) return "";
    if (mb_strpos($kv[0], "OJ_QQ") > 0) return "";
    if (mb_strpos($kv[0], "OJ_RR") > 0) return "";
    if (mb_strpos($kv[0], "OJ_REDIS") > 0) return "";
    if (mb_strpos($kv[0], "SAE") > 0) return "";
    if (mb_strpos($kv[0], "BENCH") > 0) return "";
    if (mb_strpos($kv[0], "CONTEST") > 0) return "";
    if (mb_strpos($kv[0], "OJ_BBS") > 0) return "";
    if (mb_strpos($kv[0], "OJ_RANK") > 0) return "";
    if (mb_strpos($kv[0], "OJ_OPEN") > 0) return "";
    if (mb_strpos($kv[0], "OJ_SaaS") > 0) return "";

    if (mb_strpos($kv[0], "OJ_UDP") > 0) return "";
    if (mb_strpos($kv[0], "OJ_WEIBO") > 0) return "";
    $temp = toName($kv[0]) . ":<input name='" . $kv[0] . "' value=" . $kv[1] . " >\n";
    $temp .= "\t<input name='old_" . $kv[0] . "' type='hidden' value='" . htmlentities($kv[1], ENT_QUOTES, 'UTF-8') . "' >\n";

    return $temp . "\n";
}

function isBool($v)
{
    if ($v == "true" || $v == "false") {
        return true;
    } else {
        return false;
    }
}

function generate_config($config)
{
    $ret = "";
    foreach ($_POST as $k => $v) {

        if (mb_substr($k, 0, 4) == "old_") {
            $ret = "old";
        } else {
            //      echo "\$k=$k  \$v=".$_POST['old_'.$k]." <br>";
            $old = $k . "=" . $_POST['old_' . $k];
            if (isset($_POST['old_' . $k]) && (isBool($_POST['old_' . $k]) || is_numeric($_POST['old_' . $k])))
                $new = $k . "=" . $_POST[$k];
            else
                $new = $k . "='" . $_POST[$k] . "'";
            $config = str_replace($old, $new, $config);
        }
    }
    return $config;
}

$cache_time = 30;
$OJ_CACHE_SHARE = false;
$OJ_LANG = "en";
$OJ_TEMPLATE = "syzoj";
$view_title = "Install HUST Online Judge";
$config = file_get_contents("https://gitee.com/zhblue/hustoj/raw/master/trunk/web/include/db_info.inc.php");
if (isset($_POST['$DB_HOST'])) {

    $ret = generate_config($config);
    file_put_contents("include/db_info.inc.php", $ret);
    header("location:index.php");
    exit(0);
}
$view_errors .= "<div class='ui main container'><h3>
        HUSTOJ Web installation tools / db_info.inc.php generator </h3><br>
        <form action='install.php' method='post'>
";
$cur = 0;
while (mb_strpos($config, "\$", $cur) > 0) {
    $cur = mb_strpos($config, "\$", $cur);
    $end = mb_strpos($config, ";", $cur);
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
?>

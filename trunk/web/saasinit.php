<?php

require_once("./include/db_info.inc.php");
require_once("./include/my_func.inc.php");

/**
 * 处理用户子域名创建和重定向逻辑
 * 根据SaaS配置状态，决定用户访问权限和页面跳转
 * 
 * 当SaaS功能启用时：
 * - 验证用户是否已登录且在正确域名下
 * - 允许用户通过POST参数自定义模板和友好模式
 * - 创建用户子域名并重定向到对应子域名页面
 * - 未登录用户重定向到修改页面
 * 
 * 当SaaS功能禁用时：
 * - 直接重定向到首页
 */
if (isset($OJ_SaaS_ENABLE) && $OJ_SaaS_ENABLE) {
    // 验证当前域名和用户登录状态
    if ($_SERVER['HTTP_HOST'] == $DOMAIN && isset($_SESSION[$OJ_NAME . '_user_id'])) {
        // 设置默认模板和友好模式
        $template = "bs3";
        $friendly = 0;

        // 获取用户提交的模板设置
        if (isset($_POST['template'])) $template = basename($_POST['template']);
        // 获取用户提交的友好模式设置
        if (isset($_POST['friendly'])) $friendly = intval($_POST['friendly']);
        
        $user_id = $_SESSION[$OJ_NAME . '_user_id'];
        // 创建用户子域名
        create_subdomain($user_id, $template, $friendly);
        // 重定向到用户子域名
        header("location:http://$user_id.$DOMAIN");
        exit();
    }
    // 未通过验证的用户重定向到修改页面
    header("location:modifypage.php");
} else {
    // SaaS功能禁用时重定向到首页
    header("location:index.php");
}

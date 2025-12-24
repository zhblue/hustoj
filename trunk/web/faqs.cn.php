<?php
////////////////////////////Common head
/**
 * 页面缓存时间设置，单位为秒
 * @var int
 */
$cache_time = 10;

/**
 * OJ缓存共享开关，控制是否启用缓存共享功能
 * @var bool
 */
$OJ_CACHE_SHARE = false;

/**
 * 引入缓存开始处理文件
 * 负责初始化缓存机制和相关配置
 */
require_once('./include/cache_start.php');

/**
 * 引入数据库信息配置文件
 * 包含数据库连接参数和相关信息
 */
require_once('./include/db_info.inc.php');

/**
 * 引入语言设置文件
 * 负责处理多语言支持和当前语言环境设置
 */
require_once('./include/setlang.php');

/**
 * 设置页面标题
 * @var string 页面显示的标题内容
 */
$view_title = "Welcome To Online Judge";

/////////////////////////Template
/**
 * 根据模板配置加载FAQ中文页面
 * 动态包含指定模板目录下的faqs.cn.php文件
 */
require("template/" . $OJ_TEMPLATE . "/faqs.cn.php");

/////////////////////////Common foot
/**
 * 检查并加载缓存结束处理文件
 * 如果缓存结束文件存在，则执行缓存清理和保存操作
 */
if (file_exists('./include/cache_end.php'))
    require_once('./include/cache_end.php');
?>



<?php
require_once(dirname(__FILE__)."/../../include/memcache.php");

if (!function_exists('lg_icon')) {
    function lg_icon($name)
    {
        $paths = array(
            'home'     => '<path d="M3 10.8 12 3l9 7.8"/><path d="M5.5 9.5V21h13V9.5"/>',
            'book'     => '<path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5z"/><path d="M4 18.5A2.5 2.5 0 0 1 6.5 16H20"/>',
            'flag'     => '<path d="M6 21V4"/><path d="M6 4h11l-2 4 2 4H6"/>',
            'activity' => '<path d="M4 6h16M4 12h16M4 18h10"/>',
            'chart'    => '<path d="M4 20V10M10 20V4M16 20v-6M22 20H2"/>',
            'comments' => '<path d="M21 12a8 8 0 0 1-8 8H6l-4 2 1.5-4A8 8 0 1 1 21 12z"/>',
            'help'     => '<circle cx="12" cy="12" r="9"/><path d="M9.6 9.2a2.6 2.6 0 1 1 3.5 2.4c-.8.3-1.1.9-1.1 1.7"/><path d="M12 17.2v.1"/>',
            'folder'   => '<path d="M3 6.5h6l2 2h10V19H3z"/>',
            'clock'    => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.2 2"/>',
            'trophy'   => '<path d="M8 4h8v5a4 4 0 0 1-8 0z"/><path d="M8 5.5H5.5A2.5 2.5 0 0 0 8 10M16 5.5h2.5A2.5 2.5 0 0 1 16 10"/><path d="M12 13v4M9 21h6M10 17h4l1 4H9z"/>',
            'star'     => '<path d="M12 3.8l2.5 5.4 5.9.8-4.3 4.1 1.1 5.9L12 17.1 6.8 20l1.1-5.9-4.3-4.1 5.9-.8z"/>',
            'more'     => '<circle cx="5" cy="12" r="1.4"/><circle cx="12" cy="12" r="1.4"/><circle cx="19" cy="12" r="1.4"/>',
            'user'     => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
            'settings' => '<circle cx="12" cy="12" r="3"/><path d="M12 2.8v2.4M12 18.8v2.4M2.8 12h2.4M18.8 12h2.4M5.5 5.5l1.7 1.7M16.8 16.8l1.7 1.7M18.5 5.5l-1.7 1.7M7.2 16.8l-1.7 1.7"/>',
            'mail'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3.5 6.5 8.5 6 8.5-6"/>',
            'logout'   => '<path d="M14 4H5v16h9"/><path d="M10 12h11"/><path d="m18 8.5 3.5 3.5-3.5 3.5"/>',
            'menu'     => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        );
        $body = isset($paths[$name]) ? $paths[$name] : $paths['more'];
        return '<svg class="lg-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.$body.'</svg>';
    }
}

if (!function_exists('get_menu_news')) {
    function get_menu_news()
    {
        $result = "";
        $sql_news_menu = "select `news_id`,`title` FROM `news` WHERE `menu`=1 AND `title`!='faqs.cn' ORDER BY `importance` ASC,`time` DESC LIMIT 10";
        $sql_news_menu_result = mysql_query_cache($sql_news_menu);
        if ($sql_news_menu_result) {
            foreach ($sql_news_menu_result as $row) {
                $result .= '<a class="lg-nav-item lg-nav-sub" href="'.$GLOBALS['path_fix'].'viewnews.php?id='.$row['news_id'].'" title="'.htmlentities($row['title'], ENT_QUOTES, 'UTF-8').'">'.lg_icon('star').'<span class="lg-nav-label">'.htmlentities($row['title'], ENT_QUOTES, 'UTF-8').'</span></a>';
            }
        }
        return $result;
    }
}

$url = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$dir = basename(getcwd());
if ($dir == "discuss3") $path_fix = "../";
else $path_fix = "";

if (isset($OJ_NEED_LOGIN) && $OJ_NEED_LOGIN && (
    $url != 'loginpage.php' &&
    $url != 'lostpassword.php' &&
    $url != 'lostpassword2.php' &&
    $url != 'registerpage.php'
) && !isset($_SESSION[$OJ_NAME.'_user_id'])) {
    header("location:".$path_fix."loginpage.php");
    exit();
}

if ($OJ_ONLINE) {
    require_once($path_fix.'include/online.php');
    $on = new online();
}

$sql_news_menu_result_html = "";
if (!empty($OJ_MENU_NEWS)) {
    if ($OJ_REDIS) {
        $redis = new Redis();
        $redis->connect($OJ_REDISSERVER, $OJ_REDISPORT);
        if (isset($OJ_REDISAUTH)) {
            $redis->auth($OJ_REDISAUTH);
        }
        $redisDataKey = $OJ_REDISQNAME.'_'.$OJ_TEMPLATE.'_MENU_NEWS_CACHE';
        if ($redis->exists($redisDataKey)) {
            $sql_news_menu_result_html = $redis->get($redisDataKey);
        } else {
            $sql_news_menu_result_html = get_menu_news();
            $redis->set($redisDataKey, $sql_news_menu_result_html);
            $redis->expire($redisDataKey, 300);
        }
        $redis->close();
    } else {
        $sessionDataKey = $OJ_NAME.'_'.$OJ_TEMPLATE.'_MENU_NEWS_CACHE';
        if (isset($_SESSION[$sessionDataKey])) {
            $sql_news_menu_result_html = $_SESSION[$sessionDataKey];
        } else {
            $sql_news_menu_result_html = get_menu_news();
            $_SESSION[$sessionDataKey] = $sql_news_menu_result_html;
        }
    }
}

$lg_logged_in = isset($_SESSION[$OJ_NAME.'_user_id']);
$lg_user = $lg_logged_in ? htmlentities($_SESSION[$OJ_NAME.'_user_id'], ENT_QUOTES, 'UTF-8') : '';
$lg_nick = (!empty($_SESSION[$OJ_NAME.'_nick'])) ? htmlentities($_SESSION[$OJ_NAME.'_nick'], ENT_QUOTES, 'UTF-8') : '';
$lg_is_admin = isset($_SESSION[$OJ_NAME.'_administrator']) || isset($_SESSION[$OJ_NAME.'_contest_creator']) || isset($_SESSION[$OJ_NAME.'_problem_editor']);
$lg_cid = (isset($_GET['cid']) && intval($_GET['cid']) > 0) ? intval($_GET['cid']) : 0;
$lg_in_contest = $lg_cid > 0;
$lg_on_site_contest = isset($OJ_ON_SITE_CONTEST_ID) && $OJ_ON_SITE_CONTEST_ID;

if ($url == 'problemset.php' || $url == 'problem.php' || $url == 'category.php' || $url == 'problemstatus.php' || $url == 'submitpage.php') $lg_active = 'problems';
elseif ($url == 'contest.php' || $url == 'contestset.php' || $url == 'recent-contest.php' || $url == 'conteststatus.php' || $url == 'contestrank.php' || $url == 'contestrank-oi.php' || $url == 'contestrank2.php' || $url == 'contestrank3.php' || $url == 'contestrank4.php' || $url == 'contestrank5.php' || $url == 'conteststatistics.php') $lg_active = 'contests';
elseif ($url == 'status.php' || $url == 'showsource.php' || $url == 'showsource2.php' || $url == 'reinfo.php' || $url == 'ceinfo.php' || $url == 'comparesource.php') $lg_active = 'status';
elseif ($url == 'ranklist.php' || $url == 'userinfo.php') $lg_active = 'rank';
elseif ($url == 'discuss.php' || $url == 'viewnews.php' || $url == 'faqs.php') $lg_active = 'discuss';
elseif ($url == 'index.php' || $url == '') $lg_active = 'home';
else $lg_active = '';

$lg_crumb = array(
    'home' => '主页',
    'problems' => '题库',
    'contests' => '比赛',
    'status' => '评测',
    'rank' => '排名',
    'discuss' => '讨论',
);
$lg_site_name = $domain == $DOMAIN ? $OJ_NAME : ucwords(str_replace('_', ' ', $OJ_NAME))."'s OJ";
$lg_initial = $lg_user !== '' ? mb_strtoupper(mb_substr($lg_user, 0, 1, 'UTF-8'), 'UTF-8') : '?';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $show_title ?></title>
    <link rel="icon" href="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/favicon.svg" type="image/svg+xml">
    <?php include(dirname(__FILE__)."/css.php");?>
    <script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE"?>/jquery.min.js"></script>
</head>
<body class="lg-body" data-page="<?php echo $lg_active ?>">
<header id="lg-topbar">
    <a class="lg-logo" href="<?php echo $path_fix ?>index.php" title="<?php echo htmlentities($lg_site_name, ENT_QUOTES, 'UTF-8') ?>">
        <svg class="lg-logo-mark" viewBox="0 0 48 48" aria-hidden="true">
            <path d="M11 8c5 6 4 11 1 15" stroke="#8ec8ee" stroke-width="6" stroke-linecap="round" fill="none"/>
            <path d="M12 34c8-3 15-9 20-17" stroke="#3498db" stroke-width="7" stroke-linecap="round" fill="none"/>
            <path d="M29 31c4 2 7 5 9 8" stroke="#61b35c" stroke-width="5" stroke-linecap="round" fill="none"/>
        </svg>
        <span class="lg-logo-text"><?php echo htmlentities($lg_site_name, ENT_QUOTES, 'UTF-8') ?></span>
    </a>
    <nav class="lg-crumb" aria-label="当前位置">
        <a href="<?php echo $path_fix ?>index.php"><?php echo $MSG_HOME ?></a>
        <?php if (isset($lg_crumb[$lg_active]) && $lg_active != 'home') { ?>
            <span class="lg-crumb-sep">/</span><span class="lg-crumb-current"><?php echo $lg_crumb[$lg_active] ?></span>
        <?php } ?>
    </nav>
    <div class="lg-top-actions">
        <?php if ($lg_logged_in) { ?>
            <?php if (!empty($OJ_MAIL) && !$lg_in_contest) { ?>
                <a class="lg-top-link" href="<?php echo $path_fix ?>mail.php"><?php echo lg_icon('mail') ?><span>站内信</span></a>
            <?php } ?>
            <div class="lg-user" id="lg-user">
                <button class="lg-user-btn" type="button" aria-haspopup="true" aria-expanded="false">
                    <span class="lg-avatar"><?php echo $lg_initial ?></span>
                    <span class="lg-user-name"><?php echo $lg_user ?></span>
                    <svg class="lg-caret" viewBox="0 0 12 8" aria-hidden="true"><path d="M1 1.5 6 6.5l5-5" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
                </button>
                <div class="lg-user-menu" role="menu">
                    <div class="lg-user-menu-head">
                        <span class="lg-avatar lg-avatar-lg"><?php echo $lg_initial ?></span>
                        <div>
                            <div class="lg-user-menu-name"><?php echo $lg_user ?></div>
                            <div class="lg-user-menu-sub"><?php echo $lg_nick !== '' ? $lg_nick : $MSG_USER ?></div>
                        </div>
                    </div>
                    <a class="lg-user-link" href="<?php echo $path_fix ?>userinfo.php?user=<?php echo urlencode($_SESSION[$OJ_NAME.'_user_id']) ?>"><?php echo lg_icon('user') ?><span>我的主页</span></a>
                    <a class="lg-user-link" href="<?php echo $path_fix ?>modifypage.php"><?php echo lg_icon('settings') ?><span><?php echo $MSG_REG_INFO ?></span></a>
                    <a class="lg-user-link" href="<?php echo $path_fix ?>portal.php"><?php echo lg_icon('activity') ?><span><?php echo $MSG_TODO ?></span></a>
                    <?php if ($lg_is_admin) { ?>
                        <a class="lg-user-link" href="<?php echo $path_fix ?>admin/"><?php echo lg_icon('settings') ?><span><?php echo $MSG_ADMIN ?></span></a>
                    <?php } ?>
                    <?php if (isset($_SESSION[$OJ_NAME.'_balloon'])) { ?>
                        <a class="lg-user-link" href="<?php echo $path_fix ?>balloon.php"><?php echo lg_icon('trophy') ?><span><?php echo $MSG_BALLOON ?></span></a>
                    <?php } ?>
                    <?php if (isset($OJ_PRINTER) && $OJ_PRINTER) { ?>
                        <a class="lg-user-link" href="<?php echo $path_fix ?>printer.php"><?php echo lg_icon('book') ?><span><?php echo $MSG_PRINTER ?></span></a>
                    <?php } ?>
                    <div class="lg-user-sep"></div>
                    <a class="lg-user-link lg-user-logout" href="<?php echo $path_fix ?>logout.php"><?php echo lg_icon('logout') ?><span><?php echo $MSG_LOGOUT ?></span></a>
                </div>
            </div>
        <?php } else { ?>
            <a class="lg-top-link" href="<?php echo $path_fix ?>loginpage.php"><?php echo $MSG_LOGIN ?></a>
            <?php if (isset($OJ_REGISTER) && $OJ_REGISTER) { ?>
                <a class="lg-register-btn" href="<?php echo $path_fix ?>registerpage.php"><?php echo $MSG_REGISTER ?></a>
            <?php } ?>
        <?php } ?>
    </div>
</header>

<nav id="lg-side" class="lg-side" aria-label="主导航">
    <div class="lg-side-title">导航</div>
    <div class="lg-nav-group">
        <a class="lg-nav-item <?php if ($lg_active == 'home') echo 'is-active'; ?>" href="<?php echo $path_fix ?>index.php" title="<?php echo $MSG_HOME ?>">
            <?php echo lg_icon('home') ?><span class="lg-nav-label"><?php echo $MSG_HOME ?></span>
        </a>
        <a class="lg-nav-item <?php if ($lg_active == 'problems') echo 'is-active'; ?>" href="<?php echo $path_fix ?>problemset.php" title="<?php echo $MSG_PROBLEMS ?>">
            <?php echo lg_icon('book') ?><span class="lg-nav-label">题库</span>
        </a>
        <a class="lg-nav-item <?php if ($lg_active == 'contests') echo 'is-active'; ?>" href="<?php echo $path_fix ?>contest.php<?php if ($lg_logged_in) echo '?my'; ?>" title="<?php echo $MSG_CONTEST ?>">
            <?php echo lg_icon('flag') ?><span class="lg-nav-label">比赛</span>
        </a>
        <a class="lg-nav-item <?php if ($lg_active == 'status') echo 'is-active'; ?>" href="<?php echo $path_fix ?>status.php" title="<?php echo $MSG_STATUS ?>">
            <?php echo lg_icon('activity') ?><span class="lg-nav-label">评测</span>
        </a>
        <a class="lg-nav-item <?php if ($lg_active == 'rank') echo 'is-active'; ?>" href="<?php echo $path_fix ?>ranklist.php" title="<?php echo $MSG_RANKLIST ?>">
            <?php echo lg_icon('chart') ?><span class="lg-nav-label"><?php echo $MSG_RANKLIST ?></span>
        </a>
        <?php if (!empty($OJ_BBS)) { ?>
            <a class="lg-nav-item <?php if ($lg_active == 'discuss') echo 'is-active'; ?>" href="<?php echo $path_fix ?>discuss.php" title="<?php echo $MSG_BBS ?>">
                <?php echo lg_icon('comments') ?><span class="lg-nav-label"><?php echo $MSG_BBS ?></span>
            </a>
        <?php } ?>
        <a class="lg-nav-item" href="<?php echo $path_fix ?>faqs.php" title="<?php echo $MSG_FAQ ?>">
            <?php echo lg_icon('help') ?><span class="lg-nav-label"><?php echo $MSG_FAQ ?></span>
        </a>
    </div>

    <?php if ($lg_in_contest && !$lg_on_site_contest) { ?>
        <div class="lg-nav-group">
            <div class="lg-nav-title"><?php echo $MSG_CONTEST ?></div>
            <a class="lg-nav-item lg-nav-sub" href="<?php echo $path_fix ?>contest.php?cid=<?php echo $lg_cid ?>" title="<?php echo $MSG_PROBLEMS ?>">
                <?php echo lg_icon('book') ?><span class="lg-nav-label"><?php echo $MSG_PROBLEMS ?></span>
            </a>
            <a class="lg-nav-item lg-nav-sub" href="<?php echo $path_fix ?>status.php?cid=<?php echo $lg_cid ?>" title="<?php echo $MSG_STATUS ?>">
                <?php echo lg_icon('activity') ?><span class="lg-nav-label"><?php echo $MSG_STATUS ?></span>
            </a>
            <a class="lg-nav-item lg-nav-sub" href="<?php echo $path_fix ?>contestrank.php?cid=<?php echo $lg_cid ?>" title="<?php echo $MSG_RANKLIST ?>">
                <?php echo lg_icon('chart') ?><span class="lg-nav-label"><?php echo $MSG_RANKLIST ?></span>
            </a>
            <a class="lg-nav-item lg-nav-sub" href="<?php echo $path_fix ?>contestrank-oi.php?cid=<?php echo $lg_cid ?>" title="OI-<?php echo $MSG_RANKLIST ?>">
                <?php echo lg_icon('trophy') ?><span class="lg-nav-label">OI-<?php echo $MSG_RANKLIST ?></span>
            </a>
            <?php if ($lg_is_admin) { ?>
                <a class="lg-nav-item lg-nav-sub" href="<?php echo $path_fix ?>conteststatistics.php?cid=<?php echo $lg_cid ?>" title="<?php echo $MSG_STATISTICS ?>">
                    <?php echo lg_icon('chart') ?><span class="lg-nav-label"><?php echo $MSG_STATISTICS ?></span>
                </a>
            <?php } ?>
            <a class="lg-nav-item lg-nav-sub" href="<?php echo $path_fix ?>contest.php" title="<?php echo $MSG_CONTEST.$MSG_LIST ?>">
                <?php echo lg_icon('flag') ?><span class="lg-nav-label">比赛列表</span>
            </a>
        </div>
    <?php } ?>

    <div class="lg-nav-group">
        <div class="lg-nav-title">发现</div>
        <a class="lg-nav-item" href="<?php echo $path_fix ?>category.php" title="<?php echo $MSG_SOURCE ?>">
            <?php echo lg_icon('folder') ?><span class="lg-nav-label">分类</span>
        </a>
        <?php if (!empty($OJ_RECENT_CONTEST)) { ?>
            <a class="lg-nav-item" href="<?php echo $path_fix ?>recent-contest.php" title="<?php echo $MSG_RECENT_CONTEST ?>">
                <?php echo lg_icon('clock') ?><span class="lg-nav-label"><?php echo $MSG_RECENT_CONTEST ?></span>
            </a>
        <?php } ?>
        <a class="lg-nav-item" href="<?php echo $path_fix ?>recommend.php" title="推荐自学方向">
            <?php echo lg_icon('trophy') ?><span class="lg-nav-label">推荐</span>
        </a>
        <?php echo $sql_news_menu_result_html; ?>
    </div>

    <div class="lg-nav-group lg-nav-group-last">
        <?php if ($lg_logged_in) { ?>
            <a class="lg-nav-item" href="<?php echo $path_fix ?>userinfo.php?user=<?php echo urlencode($_SESSION[$OJ_NAME.'_user_id']) ?>" title="我的主页">
                <?php echo lg_icon('user') ?><span class="lg-nav-label">我的主页</span>
            </a>
            <a class="lg-nav-item" href="<?php echo $path_fix ?>modifypage.php" title="<?php echo $MSG_REG_INFO ?>">
                <?php echo lg_icon('settings') ?><span class="lg-nav-label">设置</span>
            </a>
            <?php if ($lg_is_admin) { ?>
                <a class="lg-nav-item" href="<?php echo $path_fix ?>admin/" title="<?php echo $MSG_ADMIN ?>">
                    <?php echo lg_icon('trophy') ?><span class="lg-nav-label"><?php echo $MSG_ADMIN ?></span>
                </a>
            <?php } ?>
        <?php } else { ?>
            <a class="lg-nav-item" href="<?php echo $path_fix ?>loginpage.php" title="<?php echo $MSG_LOGIN ?>">
                <?php echo lg_icon('user') ?><span class="lg-nav-label"><?php echo $MSG_LOGIN ?></span>
            </a>
        <?php } ?>
    </div>
</nav>

<nav id="lg-bottomnav" aria-label="快捷导航">
    <a class="lg-bnav-item <?php if ($lg_active == 'home') echo 'is-active'; ?>" href="<?php echo $path_fix ?>index.php"><?php echo lg_icon('home') ?><span><?php echo $MSG_HOME ?></span></a>
    <a class="lg-bnav-item <?php if ($lg_active == 'problems') echo 'is-active'; ?>" href="<?php echo $path_fix ?>problemset.php"><?php echo lg_icon('book') ?><span>题库</span></a>
    <a class="lg-bnav-item <?php if ($lg_active == 'contests') echo 'is-active'; ?>" href="<?php echo $path_fix ?>contest.php"><?php echo lg_icon('flag') ?><span>比赛</span></a>
    <a class="lg-bnav-item <?php if ($lg_active == 'status') echo 'is-active'; ?>" href="<?php echo $path_fix ?>status.php"><?php echo lg_icon('activity') ?><span>评测</span></a>
    <button class="lg-bnav-item" type="button" id="lg-more-btn"><?php echo lg_icon('more') ?><span>更多</span></button>
</nav>
<div id="lg-mask" class="lg-mask"></div>

<main class="lg-shell">
    <div id="main" class="lg-main">

<?php
require_once("admin-header.php");

if(isset($OJ_LANG)){
    require_once("../lang/$OJ_LANG.php");
}
$path_fix = "../";
$OJ_TP = $OJ_TEMPLATE;
$OJ_TEMPLATE = "bs3";
?>
<html>
<head>
    <title><?php echo $MSG_ADMIN?></title>
    <link rel="stylesheet" href="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE/"?>bootstrap-theme.min.css">
    <!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
    <script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE/"?>jquery.min.js"></script>
    <!-- 最新的 Bootstrap 核心JavaScript文件 -->
    <script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE/"?>bootstrap.min.js"></script>
    <link rel="stylesheet" href="admin.css">
</head>
<body style="text-align:center;min-width:100px; margin-left: 250px;"> <!-- 给主体内容预留侧边栏宽度的边距 -->
    <div id="sidebar">
        <a class='btn btn-sm' href="help.php" target="main" title="<?php echo $MSG_ADMIN?>"><i class="glyphicon glyphicon-star-empty"></i><b><?php echo $MSG_ADMIN?></b></a>
        <a class='btn btn-sm' href="../status.php" target="_top" title="<?php echo $MSG_HELP_SEEOJ?>"><i class="glyphicon glyphicon-eye"></i><b><?php echo $MSG_SEEOJ?></b></a>

        <div class="sidebar-section">
            <h3><i class="glyphicon glyphicon-volume-up"></i><?php echo $MSG_NEWS."-".$MSG_ADMIN?></h3>
            <ul>
                <?php if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])){?>
                    <?php if ($OJ_TP=="bs3"){?>
                        <li><a href="setmsg.php" target="main" title="<?php echo $MSG_HELP_SETMESSAGE?>"><i class="glyphicon glyphicon-edit"></i><?php echo $MSG_NEWS."-".$MSG_SETMESSAGE?></a></li>
                    <?php }?>
                    <li><a href="news_list.php" target="main" title="<?php echo $MSG_HELP_NEWS_LIST?>"><i class="glyphicon glyphicon-list"></i><?php echo $MSG_NEWS."-".$MSG_LIST?></a></li>
                    <li><a href="news_add_page.php" target="main" title="<?php echo $MSG_HELP_ADD_NEWS?>"><i class="glyphicon glyphicon-plus"></i><?php echo $MSG_NEWS."-".$MSG_ADD?></a></li>
                <?php }?>
            </ul>
        </div>

        <div class="sidebar-section">
            <h3><i class="glyphicon glyphicon-user"></i><?php echo $MSG_USER."-".$MSG_ADMIN?></h3>
            <ul>
                <?php if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset( $_SESSION[$OJ_NAME.'_'.'password_setter'])){?>
                    <li><a href="user_list.php" target="main" title="<?php echo $MSG_HELP_USER_LIST?>"><i class="glyphicon glyphicon-list"></i><?php echo $MSG_USER."-".$MSG_LIST?></a></li>
                <?php }?>
                <?php if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'user_adder'])){?>
                    <li><a href="user_add.php" target="main" title="<?php echo $MSG_HELP_USER_ADD?>"><i class="glyphicon glyphicon-plus"></i><?php echo $MSG_USER."-".$MSG_ADD?></a></li>
                    <li><a href="user_import.php" target="main" title="<?php echo $MSG_HELP_USER_IMPORT?>"><i class="glyphicon glyphicon-upload"></i><?php echo $MSG_USER."-".$MSG_IMPORT?></a></li>
                <?php }?>
                <?php if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset( $_SESSION[$OJ_NAME.'_'.'password_setter'])){?>
                    <li><a href="changepass.php" target="main" title="<?php echo $MSG_HELP_SETPASSWORD?>"><i class="glyphicon glyphicon-lock"></i><?php echo $MSG_USER."-".$MSG_SETPASSWORD?></a></li>
                <?php }?>
                <?php if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])){?>
                    <li><a href="privilege_list.php" target="main" title="<?php echo $MSG_HELP_PRIVILEGE_LIST?>"><i class="glyphicon glyphicon-list-alt"></i><?php echo $MSG_USER."-".$MSG_PRIVILEGE."-".$MSG_LIST?></a></li>
                    <li><a href="privilege_add.php" target="main" title="<?php echo $MSG_HELP_ADD_PRIVILEGE?>"><i class="glyphicon glyphicon-plus-sign"></i><?php echo $MSG_USER."-".$MSG_PRIVILEGE."-".$MSG_ADD?></a></li>
                <?php }?>
            </ul>
        </div>

        <div class="sidebar-section">
            <h3><i class="glyphicon glyphicon-question-sign"></i><?php echo $MSG_PROBLEM."-".$MSG_ADMIN?></h3>
            <ul>
                <?php if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])||isset($_SESSION[$OJ_NAME.'_'.'contest_creator'])) {?>
                    <li><a href="problem_list.php" target="main" title="<?php echo $MSG_HELP_PROBLEM_LIST?>"><i class="glyphicon glyphicon-list"></i><?php echo $MSG_PROBLEM."-".$MSG_LIST?></a></li>
                <?php }
                if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])) {?>
                    <li><a href="problem_add_page.php" target="main" title="<?php echo html_entity_decode($MSG_HELP_ADD_PROBLEM)?>"><i class="glyphicon glyphicon-plus"></i><?php echo $MSG_PROBLEM."-".$MSG_ADD?></a></li>
                    <li><a href="problem_import.php" target="main" title="<?php echo $MSG_HELP_IMPORT_PROBLEM?>"><i class="glyphicon glyphicon-import"></i><?php echo $MSG_PROBLEM."-".$MSG_IMPORT?></a></li>
                    <li><a href="problem_export.php" target="main" title="<?php echo $MSG_HELP_EXPORT_PROBLEM?>"><i class="glyphicon glyphicon-export"></i><?php echo $MSG_PROBLEM."-".$MSG_EXPORT?></a></li>
                <?php }?>
            </ul>
        </div>


        <div class="sidebar-section">
            <h3><i class="glyphicon glyphicon-flag"></i><?php echo $MSG_CONTEST."-".$MSG_ADMIN?></h3>
            <ul>
                <?php if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'contest_creator'])){?>
                    <li><a href="contest_list.php" target="main"  title="<?php echo $MSG_HELP_CONTEST_LIST?>"><i class="glyphicon glyphicon-list"></i><?php echo $MSG_CONTEST."-".$MSG_LIST?></a></li>
                    <li><a href="contest_add.php" target="main"  title="<?php echo $MSG_HELP_ADD_CONTEST?>"><i class="glyphicon glyphicon-plus"></i><?php echo $MSG_CONTEST."-".$MSG_ADD?></a></li>
                    <li><a href="user_set_ip.php" target="main" title="<?php echo $MSG_HELP_SET_LOGIN_IP?>"><i class="glyphicon glyphicon-check"></i><?php echo $MSG_CONTEST."-".$MSG_SET_LOGIN_IP?></a></li>
                    <li><a href="team_generate.php" target="main" title="<?php echo $MSG_HELP_TEAMGENERATOR?>"><i class="glyphicon glyphicon-share"></i><?php echo $MSG_CONTEST."-".$MSG_TEAMGENERATOR?></a></li>
                    <li><a href="team_generate2.php" target="main" title="<?php echo $MSG_HELP_TEAMGENERATOR?>"><i class="glyphicon glyphicon-share"></i><?php echo $MSG_CONTEST."-".$MSG_TEAMGENERATOR?></a></li>
                    <li><a href="offline_import.php" target="main" title="<?php echo $MSG_IMPORT.$MSG_CONTEST?>"><i class="glyphicon glyphicon-import"></i><?php echo $MSG_CONTEST."-".$MSG_IMPORT?></a></li>
                <?php }?>
            </ul>
        </div>

        <?php if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])){?>
            <div class="sidebar-section">
                <h3><i class="glyphicon glyphicon-leaf"></i><?php echo $MSG_SYSTEM."-".$MSG_ADMIN?></h3>
                <ul>
                    <li><a href="rejudge.php" target="main" title="<?php echo $MSG_HELP_REJUDGE?>"><i class="glyphicon glyphicon-repeat"></i><?php echo $MSG_SYSTEM."-".$MSG_REJUDGE?></a></li>
                    <li><a href="source_give.php" target="main" title="<?php echo $MSG_HELP_GIVESOURCE?>"><i class="glyphicon glyphicon-random"></i><?php echo $MSG_SYSTEM."-".$MSG_GIVESOURCE?></a></li>
                    <li><a href="../online.php" target="main"><i class="glyphicon glyphicon-globe"></i><?php echo $MSG_SYSTEM."-".$MSG_HELP_ONLINE?></a></li>
                    <li><a href="update_db.php" target="main" title="<?php echo $MSG_HELP_UPDATE_DATABASE?>"><i class="glyphicon glyphicon-hdd"></i><?php echo $MSG_SYSTEM."-".$MSG_UPDATE_DATABASE?></a></li>
                    <li><a href="backup.php" target="main" title="<?php echo $MSG_HELP_BACKUP_DATABASE?>"><i class="glyphicon glyphicon-folder-close"></i><?php echo $MSG_SYSTEM."-".$MSG_BACKUP_DATABASE?></a></li>
                    <li><a href="ranklist_export.php" target="main" title="<?php echo $MSG_EXPORT.$MSG_RANKLIST?>"><i class="glyphicon glyphicon-export"></i><?php echo  $MSG_EXPORT.$MSG_RANKLIST?></a></li>
                </ul>
            </div>
        <?php }?>

        <?php if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])){?>
            <a class='btn btn-sm' href="https://github.com/zhblue/hustoj/" target="_blank"><i class="fab glyphicon-github"></i><b>HUSTOJ</b></a><br>
            <a class='btn btn-sm' href="https://yuanqi.tencent.com/agent/jADpOEWqLvTv" target="_blank"><i class="glyphicon glyphicon-robot"></i><b>小张老师(AI-help)</b></a><br>
            <div><a class="btn btn-sm" target='main' href="http://hustoj.com"><i class="glyphicon glyphicon-question-circle"></i><?php echo $MSG_ADMIN." ".$MSG_FAQ?></a></div>
            <a class='btn btn-sm' href="https://github.com/zhblue/freeproblemset/" target="_blank"><i class="fab glyphicon-github"></i><b>FreeProblemSet</b></a><br>
            <a class='btn btn-sm' href="http://tk.hustoj.com" target="_blank"><i class="glyphicon glyphicon-book"></i><b>自助题库</b></a><br>
            <?php if(isset($OJ_REMOTE_JUDGE)&&$OJ_REMOTE_JUDGE){?>
                <a class='btn btn-sm' href="https://www.ssoier.cn/api/" target="_blank"><i class="glyphicon glyphicon-link"></i><b>一本通远程账户管理</b></a><br>
            <?php }?>
            <a class='btn btn-sm' href="https://mp.weixin.qq.com/s?__biz=MzI1MTAwMTI2NA==&mid=2656403287&idx=1&sn=2b1b9a5cd0b271aa4a050c349981e715" target="_blank"><i class="glyphicon glyphicon-book-open"></i><b>二次开发教程</b></a><br>
        <?php }?>

        <?php if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])&&!$OJ_SAE){?>
            <a href="solution_statistics.php" target="main" title="Create your own data"><i class="glyphicon glyphicon-chart-line"></i><font color="eeeeee">SS Report</font></a> <br>
            <a href="problem_copy.php" target="main" title="Create your own data"><i class="glyphicon glyphicon-copy"></i><font color="eeeeee">CopyProblem</font></a> <br>
            <a href="problem_changeid.php" target="main" title="Danger,Use it on your own risk"><i class="glyphicon glyphicon-exchange-alt"></i><font color="eeeeee">ReOrderProblem</font></a>
        <?php }?>
    </div>
</body>
</html>

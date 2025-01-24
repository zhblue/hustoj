<?php require_once("admin-header.php");

if(isset($OJ_LANG)){
    require_once("../lang/$OJ_LANG.php");
}


?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>layout</title>
    <link rel="stylesheet" href="layui/css/layui.css">
    <script src="layui/layui.js"></script>
</head>
<body class="layui-layout-body">
<div class="layui-layout">
<!--    <div class="layui-header">-->
<!--        <div class="layui-logo">layui 后台布局</div>-->
        <!-- 头部区域（可配合layui已有的水平导航） -->
<!--        <ul class="layui-nav layui-layout-left">-->
<!--            <li class="layui-nav-item"><a href="">控制台</a></li>-->
<!--            <li class="layui-nav-item"><a href="">商品管理</a></li>-->
<!--            <li class="layui-nav-item"><a href="">用户</a></li>-->
<!--            <li class="layui-nav-item">-->
<!--                <a href="javascript:;">其它系统</a>-->
<!--                <dl class="layui-nav-child">-->
<!--                    <dd><a href="">邮件管理</a></dd>-->
<!--                    <dd><a href="">消息管理</a></dd>-->
<!--                    <dd><a href="">授权管理</a></dd>-->
<!--                </dl>-->
<!--            </li>-->
<!--        </ul>-->
<!--        <ul class="layui-nav layui-layout-right">-->
<!--            <li class="layui-nav-item">-->
<!--                <a href="javascript:;">-->
<!--                    <img src="http://t.cn/RCzsdCq" class="layui-nav-img">-->
<!--                    贤心-->
<!--                </a>-->
<!--                <dl class="layui-nav-child">-->
<!--                    <dd><a href="">基本资料</a></dd>-->
<!--                    <dd><a href="">安全设置</a></dd>-->
<!--                </dl>-->
<!--            </li>-->
<!--            <li class="layui-nav-item"><a href="">退了</a></li>-->
<!--        </ul>-->
<!--    </div>-->

    <div class="layui-side layui-bg-black">
        <div class="layui-side-scroll">
            <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
            <ul class="layui-nav layui-nav-tree"  lay-filter="test">
<!--                <li class="layui-nav-item layui-nav-itemed">-->
<!--                    <a class="" href="javascript:;">所有商品</a>-->
<!--                    <dl class="layui-nav-child">-->
<!--                        <dd><a href="javascript:;">列表一</a></dd>-->
<!--                        <dd><a href="javascript:;">列表二</a></dd>-->
<!--                        <dd><a href="javascript:;">列表三</a></dd>-->
<!--                        <dd><a href="">超链接</a></dd>-->
<!--                    </dl>-->
<!--                </li>-->
<!--                <li class="layui-nav-item">-->
<!--                    <a href="javascript:;">解决方案</a>-->
<!--                    <dl class="layui-nav-child">-->
<!--                        <dd><a href="javascript:;">列表一</a></dd>-->
<!--                        <dd><a href="javascript:;">列表二</a></dd>-->
<!--                        <dd><a href="">超链接</a></dd>-->
<!--                    </dl>-->
<!--                </li>-->
                <li class="layui-nav-item"><a href="../status.php" target="_top" title="<?php echo $MSG_HELP_SEEOJ?>"><?php echo $MSG_SEEOJ?></a></li>
                <?php if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])){?>
                <li class="layui-nav-item"><a class='btn btn-primary' href="../admin/setmsg.php" target="main" title="<?php echo $MSG_HELP_SETMESSAGE?>"><b><?php echo $MSG_SETMESSAGE?></b></a>
                <li class="layui-nav-item"><a class='btn btn-primary' href="../admin/news_list.php" target="main" title="<?php echo $MSG_HELP_NEWS_LIST?>"><b><?php echo $MSG_NEWS.$MSG_LIST?></b></a>
                <li class="layui-nav-item"><a class='btn btn-primary' href="../admin/news_add_page.php" target="main" title="<?php echo $MSG_HELP_ADD_NEWS?>"><b><?php echo $MSG_ADD.$MSG_NEWS?></b></a>
                <li class="layui-nav-item"><a class='btn btn-primary' href="../admin/user_list.php" target="main" title="<?php echo $MSG_HELP_USER_LIST?>"><b><?php echo $MSG_USER.$MSG_LIST?></b></a>
                <li class="layui-nav-item"><a class='btn btn-primary' href="../admin/user_set_ip.php" target="main" title="<?php echo $MSG_SET_LOGIN_IP?>"><b><?php echo $MSG_SET_LOGIN_IP?></b></a>
                    <?php }
                    if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset( $_SESSION[$OJ_NAME.'_'.'password_setter'] )){?>
                <li class="layui-nav-item"><a class='btn btn-primary' href="../admin/changepass.php" target="main" title="<?php echo $MSG_HELP_SETPASSWORD?>"><b><?php echo $MSG_SETPASSWORD?></b></a>
                    <?php }
                    if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])){?>
                <li class="layui-nav-item"><a class='btn btn-primary' href="../admin/source_give.php" target="main" title="<?php echo $MSG_HELP_GIVESOURCE?>"><b><?php echo $MSG_GIVESOURCE?></b></a>
                <li class="layui-nav-item"><a class='btn btn-primary' href="../admin/privilege_list.php" target="main" title="<?php echo $MSG_HELP_PRIVILEGE_LIST?>"><b><?php echo $MSG_PRIVILEGE.$MSG_LIST?></b></a>
                <li class="layui-nav-item"><a class='btn btn-primary' href="../admin/privilege_add.php" target="main" title="<?php echo $MSG_HELP_ADD_PRIVILEGE?>"><b><?php echo $MSG_ADD.$MSG_PRIVILEGE?></b></a>
                    <?php }
                    if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'contest_creator'])||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])){?>
                <li class="layui-nav-item"><a class='btn btn-primary' href="../admin/problem_list.php" target="main" title="<?php echo $MSG_HELP_PROBLEM_LIST?>"><b><?php echo $MSG_PROBLEM.$MSG_LIST?></b></a>
                    <?php }
                    if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])){?>
                <li class="layui-nav-item"><a class='btn btn-primary' href="../admin/problem_add_page.php" target="main" title="<?php echo $MSG_HELP_ADD_PROBLEM?>"><b><?php echo $MSG_ADD.$MSG_PROBLEM?></b></a>
                    <?php }
                    if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])){?>
                <li class="layui-nav-item"><a class='btn btn-primary' href="../admin/problem_import.php" target="main" title="<?php echo $MSG_HELP_IMPORT_PROBLEM?>"><b><?php echo $MSG_IMPORT.$MSG_PROBLEM?></b></a>
                <li class="layui-nav-item"><a class='btn btn-primary' href="../admin/problem_export.php" target="main" title="<?php echo $MSG_HELP_EXPORT_PROBLEM?>"><b><?php echo $MSG_EXPORT.$MSG_PROBLEM?></b></a>
                    <?php }?>
                <li class="layui-nav-item"><a class='btn btn-primary' href="https://github.com/zhblue/freeproblemset/" target="_blank"><b>FreeProblemSet</b></a>
                    <?php
                    if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'contest_creator'])){?>
                <li class="layui-nav-item"><a class='btn btn-primary' href="../admin/contest_list.php" target="main"  title="<?php echo $MSG_HELP_CONTEST_LIST?>"><b><?php echo $MSG_CONTEST.$MSG_LIST?></b></a>
                <li class="layui-nav-item"><a class='btn btn-primary' href="../admin/contest_add.php" target="main"  title="<?php echo $MSG_HELP_ADD_CONTEST?>"><b><?php echo $MSG_ADD.$MSG_CONTEST?></b></a>
                    <?php }
                    if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])){?>
                <li class="layui-nav-item"><a class='btn btn-primary' href="../admin/team_generate.php" target="main" title="<?php echo $MSG_HELP_TEAMGENERATOR?>"><b><?php echo $MSG_TEAMGENERATOR?></b></a>
                    <?php }
                    if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])){?>
                <li class="layui-nav-item"><a class='btn btn-primary' href="../admin/rejudge.php" target="main" title="<?php echo $MSG_HELP_REJUDGE?>"><b><?php echo $MSG_REJUDGE?></b></a>
                    <?php }?>
                <li class="layui-nav-item"><a class='btn btn-primary' href="https://github.com/zhblue/hustoj/" target="_blank"><b>HUSTOJ</b></a>
                    <?php
                    if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])){?>
                <li class="layui-nav-item"><a class='btn btn-primary' href="../admin/update_db.php" target="main" title="<?php echo $MSG_HELP_UPDATE_DATABASE?>"><b><?php echo $MSG_UPDATE_DATABASE?></b></a>
                    <?php }
                    if (isset($OJ_ONLINE)&&$OJ_ONLINE){?>
                <li class="layui-nav-item"><a class='btn btn-primary' href="../online.php" target="main"><b><?php echo $MSG_ONLINE?></b></a>
                    <?php }?>
<!--                <li class="layui-nav-item"><a href="">云市场</a></li>-->
<!--                <li class="layui-nav-item"><a href="">发布商品</a></li>-->

                <li class="layui-nav-item"><a class='btn btn-primary' href="http://tk.hustoj.com" target="_blank"><b>自助题库</b></a></li>

                <li class="layui-nav-item"><a href="../admin/problem_copy.php" target="main" title="Create your own data"><font color="eeeeee">CopyProblem</font></a>
                <li class="layui-nav-item"><a href="../admin/problem_changeid.php" target="main" title="Danger,Use it on your own risk"><font color="eeeeee">ReOrderProblem</font></a>


            </ul>
        </div>
    </div>

    <div class="layui-body">
        <iframe name="main" style="width: 100%;height: 100%" src="../status.php">

        </iframe>
        <!-- 内容主体区域 -->
<!--        <div style="padding: 15px;">内容主体区域</div>-->
    </div>

</div>
<script>
    //JavaScript代码区域
    layui.use('element', function(){
        var element = layui.element;

    });
</script>
</body>
</html>

<?php
$cur_path = "template/$OJ_TEMPLATE/";
$url = basename($_SERVER['REQUEST_URI']);
$realurl = basename($_SERVER['REQUEST_URI']);
$url = str_replace(strrchr($url, "?"), "", $url);

$dir = basename(getcwd());
if ($dir == "discuss3")
    $path_fix = "../";
else
    $path_fix = "";

$_SESSION[$OJ_NAME . '_' . 'profile_csrf'] = rand();

if (
    isset($OJ_NEED_LOGIN) && $OJ_NEED_LOGIN && (
        $url != 'loginpage.php' &&
        $url != 'lostpassword.php' &&
        $url != 'lostpassword2.php' &&
        $url != 'registerpage.php'
    ) && !isset($_SESSION[$OJ_NAME . '_' . 'user_id'])
) {
    header("location:" . $path_fix . "loginpage.php");
    exit();
}
?>
<style id="navbarstyles">
</style>
<div class="ui top fixed borderless menu bsharkMenu topmenu" id="navbar">
    <div class="ui container">
        <a class="item" id="logolink" href="<?php echo $OJ_HOME; ?>">
            <b>
                <?php echo $OJ_NAME; ?>
            </b>
        </a>
        <a class="item<?php if ($url == '')
            echo ' active'; ?>" href="<?php echo $OJ_HOME; ?>">
            <?php echo $MSG_HOME ?>
        </a>
        <?php
        if (file_exists("moodle")) {
            ?>
            <a class="item" href="moodle">Moodle</a>
            <?php
        }
        ?>

        <?php
        if (!isset($OJ_ON_SITE_CONTEST_ID)) {
            ?>
            <a class="item<?php if ($url == 'faqs.php')
                echo ' active'; ?>" href="<?php echo $path_fix ?>faqs.php">
                <?php echo $MSG_FAQ ?>
            </a>
        <?php } ?>


        <?php
        if (isset($OJ_PRINTER) && $OJ_PRINTER) {
            ?>
            <a class="item<?php if ($url == "printer.php")
                echo " active"; ?>" href="<?php echo $path_fix ?>printer.php">
                <?php echo $MSG_PRINTER ?>
            </a>
            <?php
        }
        ?>


        <?php
        if (!isset($OJ_ON_SITE_CONTEST_ID)) {
            ?>
            <?php
            if (isset($OJ_BBS) && $OJ_BBS) {
                ?>
                <a class="item<?php if ($dir == "discuss3")
                    echo " active"; ?>" href="<?php echo $path_fix; ?>bbs.php">
                    <?php echo $MSG_BBS ?>
                </a>
                <?php
            }
            ?>
            <a class="item<?php if ($url == 'problemset.php')
                echo ' active'; ?>" href="<?php echo $path_fix ?>problemset.php">
                <?php echo $MSG_PROBLEMS ?>
            </a>
            <a class="item<?php if ($url == "category.php")
                echo " active"; ?>" href="<?php echo $path_fix ?>category.php"><span class="glyphicon glyphicon-th"
                    aria-hidden="true"></span>
                <?php echo $MSG_SOURCE ?>
            </a>
            <a class="item<?php if ($url == 'contest.php')
                echo ' active'; ?>" href="<?php echo $path_fix ?>contest.php<?php if (isset($_SESSION[$OJ_NAME . "_user_id"]))
                         echo "?my" ?>">
                <?php echo $MSG_CONTEST ?>
            </a>
            <a class="item<?php if ($url == 'status.php')
                echo ' active'; ?>" href="<?php echo $path_fix ?>status.php">
                <?php echo $MSG_STATUS ?>
            </a>
            <a class="item<?php if ($url == 'ranklist.php')
                echo ' active'; ?>" href="<?php echo $path_fix ?>ranklist.php">
                <?php echo $MSG_RANKLIST ?>
            </a>
            <?php
        } else {
            ?>
            <a class="item<?php if ($url == "contest.php")
                echo " active"; ?>" href="<?php echo $path_fix ?>contest.php">
                <?php echo $MSG_CONTEST ?>
            </a>
            <?php
        }
        ?>
        <div class="right menu">
            <div class="ui dropdown item">
                <?php echo $MSG_LANG; ?>
                <i class="dropdown icon"></i>
                <div class="menu">
                    <a class="item" href="<?php echo $path_fix ?>setlang.php?lang=cn">中文</a>
                    <a class="item" href="<?php echo $path_fix ?>setlang.php?lang=en">English</a>
                    <a class="item" href="<?php echo $path_fix ?>setlang.php?lang=ug">ئۇيغۇرچە</a>
                    <a class="item" href="<?php echo $path_fix ?>setlang.php?lang=fa">فارسی</a>
                    <a class="item" href="<?php echo $path_fix ?>setlang.php?lang=th">ไทย</a>
                    <a class="item" href="<?php echo $path_fix ?>setlang.php?lang=ko">한국어</a>
                </div>
            </div>
            <?php
            if (isset($_SESSION[$OJ_NAME . '_user_id'])) {
                ?>
                <div class="ui dropdown item">
                    <?php $if_new_mail = count(pdo_query('select * from `mail` where `to_user`=? and `new_mail`=1', $_SESSION[$OJ_NAME . '_' . 'user_id'])) > 0 ? 1 : 0; ?>
                    <?php echo $_SESSION[$OJ_NAME . '_' . 'user_id']; ?>
                    <i class="dropdown icon"></i>
                    <div class="menu">
                        <a class="item"
                            href="<?php echo $path_fix ?>userinfo.php?user=<?php echo $_SESSION[$OJ_NAME . '_' . 'user_id']; ?>">
                            <?php echo $MSG_USERINFO; ?>
                        </a>
                        <a class="item" href="<?php echo $path_fix ?>modifypage.php">
                            <?php echo $MSG_REG_INFO; ?>
                        </a>
                        <a class="item" href="<?php echo $path_fix ?>mail.php" style="position:relative">
                            <?php echo $MSG_MAIL; ?>
                            <?php if ($if_new_mail) { ?>
                                <span class="ui mini empty red circular ui label"></span>
                            <?php } ?>
                        </a>
                        <a class="item" href="<?php echo $path_fix ?>logout.php">
                            <?php echo $MSG_LOGOUT; ?>
                        </a>
                        <?php
                        if (isset($_SESSION[$OJ_NAME . '_' . 'administrator'])) {
                            ?>

                            <a class="item" href="<?php echo $path_fix ?>swadmin">
                                <?php echo $MSG_ADMIN; ?>
                            </a>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            <?php } else { ?>
                <a class="item" href="<?php echo $path_fix ?>loginpage.php">
                    <?php echo $MSG_LOGIN; ?>
                </a>
                <a class="item" href="<?php echo $path_fix ?>registerpage.php">
                    <?php echo $MSG_REGISTER; ?>
                </a>
            <?php } ?>
        </div>
    </div>
</div>

<?php if ($THEME_BANNER != "hidden") { ?>
    <div id=banner
        style="width:100%;height:200px;margin:0;background-image: url('./template/bshark/1.jpg');
                                                                                                        background-repeat: no-repeat;
                                                                                                        background-attachment: fixed;
                                                                                                        background-position: center center;
                                                                                                        background-size: cover;
                                                                                                        background-color: #ffffff;position:relative">
        <div style="position:absolute;bottom:10px;margin-left:10%" id="bannerbox">
            <a href="/"><img src="/template/bshark/logo.png" height="120" id="bannerlogo" style="display:inline"></a>
        </div>
    </div>
    <script>
        var tac = 0;
        var ooottt = window.setInterval(function () {
            var pps = window.pageYOffset;
            $('.topmenu').mouseover(function (e) {
                tac = 1;
            }).mouseout(function (e) {
                tac = 0;
            });
            if (tac == 1) {
                $('.topmenu').css('backgroundColor', '#fff');
                document.getElementById("navbarstyles").innerHTML = ".topmenu .item{color:#212223 !important;}";
            } else if (pps > 150) {
                $('.topmenu').css('backgroundColor', '#fff');
                document.getElementById("navbarstyles").innerHTML = ".topmenu .item{color:#212223 !important;}";
            } else {
                $('.topmenu').css('backgroundColor', 'transparent');
                document.getElementById("navbarstyles").innerHTML = ".topmenu .item{color:#fff !important;}";
            }
        }, 200);
    </script>
<?php } ?>
<div class="ui borderless top fixed menu bsharkMiniMenu topmenu">
    <a class="item" id="logolink" href="/">
        <b>
            <?php echo $OJ_NAME; ?>
        </b>
    </a>
    <div class="right menu">
        <div class="ui dropdown item">
            <i class="sidebar icon"></i>
            <div class="menu">
                <a class="item<?php if ($url == '')
                    echo ' active'; ?>" href="./">
                    <?php echo $MSG_HOME ?>
                </a>
                <a class="item<?php if ($url == 'faqs.php')
                    echo ' active'; ?>" href="./faqs.php">
                    <?php echo $MSG_FAQ ?>
                </a>
                <?php
                if (isset($OJ_BBS) && $OJ_BBS) {
                    ?>
                    <a class="item<?php if ($dir == "discuss3")
                        echo " active"; ?>" href="<?php echo $path_fix; ?>bbs.php">
                        <?php echo $MSG_BBS ?>
                    </a>
                    <?php
                }
                ?>
                <a class="item<?php if ($url == 'problemset.php')
                    echo ' active'; ?>" href="./problemset.php">
                    <?php echo $MSG_PROBLEMS ?>
                </a>
                <a class="item<?php if ($url == "category.php")
                    echo " active"; ?>" href="<?php echo $path_fix ?>category.php"><span class="glyphicon glyphicon-th"
                        aria-hidden="true"></span>
                    <?php echo $MSG_SOURCE ?>
                </a>
                <a class="item<?php if ($url == 'contest.php')
                    echo ' active'; ?>" href="./contest.php">
                    <?php echo $MSG_CONTEST ?>
                </a>
                <a class="item<?php if ($url == 'status.php')
                    echo ' active'; ?>" href="./status.php">
                    <?php echo $MSG_STATUS ?>
                </a>
                <a class="item<?php if ($url == 'ranklist.php')
                    echo ' active'; ?>" href="./ranklist.php">
                    <?php echo $MSG_RANKLIST ?>
                </a>
                <div class="divider"></div>
                <div class="item">
                    <i class="angle left icon"></i>
                    <span class="float right">
                        <?php echo $MSG_LANG; ?>
                    </span>
                    <div class="menu">
                        <a class="item" href="./setlang.php?lang=cn">中文</a>
                        <a class="item" href="./setlang.php?lang=en">English</a>
                        <a class="item" href="./setlang.php?lang=ug">ئۇيغۇرچە</a>
                        <a class="item" href="./setlang.php?lang=fa">فارسی</a>
                        <a class="item" href="./setlang.php?lang=th">ไทย</a>
                        <a class="item" href="./setlang.php?lang=ko">한국어</a>
                    </div>
                </div>
                <?php
                if (isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
                    ?>
                    <div class="item">
                        <i class="angle left icon"></i>
                        <?php $if_new_mail = count(pdo_query('select * from `mail` where `to_user`=? and `new_mail`=1', $_SESSION[$OJ_NAME . '_' . 'user_id'])) > 0 ? 1 : 0; ?>
                        <?php echo $_SESSION[$OJ_NAME . '_' . 'user_id']; ?>
                        <div class="menu">
                            <a class="item" href="./userinfo.php?user=<?php echo $_SESSION[$OJ_NAME . '_' . 'user_id']; ?>">
                                <?php echo $MSG_USERINFO; ?>
                            </a>
                            <a class="item" href="./modifypage.php">
                                <?php echo $MSG_REG_INFO; ?>
                            </a>
                            <a class="item" href="./mail.php" style="position:relative">
                                <?php echo $MSG_MAIL; ?>
                                <?php if ($if_new_mail) { ?>(有新消息)<?php } ?>
                            </a>
                            <a class="item" href="./logout.php">
                                <?php echo $MSG_LOGOUT; ?>
                            </a>
                            <?php
                            if ($_SESSION[$OJ_NAME . '_' . 'administrator']) {
                                ?>

                                <a class="item" href="./swadmin">
                                    <?php echo $MSG_ADMIN; ?>
                                </a>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                <?php } else { ?>
                    <a class="item" href="./loginpage.php">
                        <?php echo $MSG_LOGIN; ?>
                    </a>
                    <a class="item" href="./registerpage.php">
                        <?php echo $MSG_REGISTER; ?>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
</div>

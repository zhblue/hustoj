<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>
        <?php echo $MSG_LOGIN; ?> - <?php echo $OJ_NAME; ?>
    </title>
    <?php require("./template/bshark/header-files.php"); ?>
    <style>
        body {
            background-image: url(<?php echo $THEME_LOGIN_BG; ?>);
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            background-color: #464646;
        }

        .input-o {
            background-color: rgba(255, 255, 255, .3);
            width: 300px;
            height: 40px;
            border-radius: 20px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            padding: 5px 20px;
            color: #fff !important;
            margin-bottom: 20px;
            transition: all 0.5s;
            backdrop-filter: blur(3px);
        }

        .input-o:hover {
            border: 2px solid rgba(255, 255, 255, 0.5);
        }

        .input-o input {
            background-color: rgba(0, 0, 0, 0) !important;
            outline: 0;
            border: 0;
            line-height: 26px;
            width: 220px;
            color: #fff !important;
        }

        .button-login {
            background-color: #0067f4;
            color: #fff;
            border: 0;
            height: 40px;
            width: 300px;
            border-radius: 20px;
        }

        .is-valid {
            border: 2px solid rgba(71, 175, 80, 0.5) !important;
        }

        input::-webkit-input-placeholder {
            color: rgba(255, 255, 255, 0.8);
        }

        input:-moz-placeholder {
            color: rgba(255, 255, 255, 0.8);
        }

        input:-ms-input-placeholder {
            color: rgba(255, 255, 255, 0.8);
        }

        .more-links {
            display: block;
            width: 300px;
            margin-top: 20px;
            color: #fff;
        }

        .more-links a {
            color: #ccc;
        }
    </style>
</head>

<body>
    <center>
        <div style="margin-top:10%;">
            <h2 style="color:#fff">
                <?php echo $MSG_LOGIN; ?>
            </h2>
            <form id="login" action="/login.php" method="post" role="form" onSubmit="return jsMd5();">
                <div class="input-o">
                    <i class="iconfont icon-user"></i>
                    <input placeholder="<?php echo $MSG_USER_ID; ?>" autocomplete="off" name="user_id" type="text">
                </div>
                <div class="input-o">
                    <i class="iconfont icon-key"></i>
                    <input placeholder="<?php echo $MSG_PASSWORD; ?>" autocomplete="new-password" type="password"
                        name="password">
                </div>
                <?php if ($OJ_VCODE) { ?>
                    <div class="input-o" style="position: relative;">
                        <i class="iconfont icon-ecurityCode"></i>
                        <input name="vcode" placeholder="<?php echo $MSG_VCODE; ?>" type="text" autocomplete=off >
                        <img id="vcode-img" src="vcode.php" onclick="this.src='vcode.php?'+Math.random()" height="26px"
                            style="position: absolute; right: 10px; ">
                    </div>
                <?php } ?>

                <button class="button-login"><?php echo $MSG_LOGIN; ?></button>
                <div class="more-links">
                    <div class="ui three column grid">
                        <div class="column left aligned">
                            <a href="<?php echo $OJ_HOME; ?>">返回<?php echo $MSG_HOME; ?></a>
                        </div>
                        <div class="column center aligned">
                            <a href="lostpassword.php">
                                <?php echo $MSG_LOST_PASSWORD; ?>
                            </a>
                        </div>
                        <div class="column right aligned">
                            <a href="registerpage.php">
                                <?php echo $MSG_REGISTER . $MSG_USER; ?>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
            <script src="<?php echo $OJ_CDN_URL ?>/include/md5-min.js"></script>
            <script>
                function jsMd5() {
                    if ($("input[name=password]").val() == "") return false;
                    $("input[name=password]").val(hex_md5($("input[name=password]").val()));
                    return true;
                }
            </script>
        </div>
    </center>
    <?php require("./template/bshark/footer-files.php"); ?>
    <?php if ($OJ_VCODE) { ?>
        <!--script>
                                $(document).ready(function () {
                                    $("#vcode-img").attr("src", "vcode.php?" + Math.random());
                                })
                            </script-->
    <?php } ?>
</body>

</html>
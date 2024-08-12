<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>
        <?php echo $MSG_LOST_PASSWORD; ?> - <?php echo $OJ_NAME; ?>
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
            <!-- Main component for a primary marketing message or call to action -->
            <h2 style="color:#fff">
                <?php echo $MSG_LOST_PASSWORD; ?>
            </h2>
            <form action=lostpassword.php method=post>
                <div class="input-o">
                    <i class="iconfont icon-user"></i>
                    <input placeholder="<?php echo $MSG_USER_ID; ?>" autocomplete="off" name="user_id" type="text">
                </div>
                <div class="input-o" style="display:inline-block">
                    <i class="iconfont icon-mail"></i>
                    <input placeholder="<?php echo $MSG_EMAIL; ?>" autocomplete="off" type="email" name="email">
                </div>
                <?php //if ($OJ_VCODE) { ?>
                <div class="input-o" style="position: relative;">
                    <i class="iconfont icon-ecurityCode"></i>
                    <input name="vcode" placeholder="<?php echo $MSG_VCODE; ?>" type="text" autocomplete=off >
                    <img id="vcode-img" src="vcode.php" onclick="this.src='vcode.php?'+Math.random()" height="26px"
                        style="position: absolute; right: 10px; ">
                </div>
                <?php //} ?>
                <button class="button-login" type=submit><?php echo $MSG_SUBMIT; ?></button>
                <div class="more-links">
                    <div class="ui two column grid">
                        <div class="column left aligned">
                            <a href="<?php echo $OJ_HOME; ?>">返回<?php echo $MSG_HOME; ?></a>
                        </div>
                        <div class="column right aligned">
                            <a href="loginpage.php">
                                <?php echo $MSG_LOGIN . $MSG_USER; ?>
                            </a>
                        </div>
                    </div>
                </div>
            </form>

        </div>

        <?php require("./template/bshark/footer-files.php"); ?>
    </center>
</body>

</html>
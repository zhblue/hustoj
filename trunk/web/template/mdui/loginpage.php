<!DOCTYPE html>
<html lang="cn">

<head>
    <?php $page_title = "登录"; ?>
    <?php include('_includes/head.php'); ?>
</head>

<body class="mdui-drawer-body-left mdui-theme-primary-indigo mdui-theme-accent-indigo mdui-appbar-with-toolbar">
    <?php include('_includes/header.php'); ?>
    <?php include('_includes/sidebar.php'); ?>
    <div class="mdui-container">
        <div class="mdui-row" style="display: flex; justify-content: center;">
            <div class="mdui-card mdui-col-sm-6" style="text-align: left;">
                <div class="mdui-card-primary">
                    <div class="mdui-card-primary-title" style="text-align: center">登录</div>
                    <!-- <div class="mdui-card-primary-subtitle"></div> -->
                </div>
                <form id="login" action="login.php" method="post" role="form"
                    class="mdui-m-a-2 mdui-card-content" onSubmit="return jsMd5();">
                    <div style="position: relative;">
                        <a class="mdui-btn mdui-btn-icon" style="display: inline-block; top: 40px; position: absolute;">
                            <i class="mdui-icon material-icons">account_circle</i>
                        </a>
                        <div class="mdui-textfield mdui-textfield-floating-label"
                            style="width: calc(100% - 50px); display: inline-block; margin-left: 45px;">
                            <label class="mdui-textfield-label">用户名</label>
                            <input class="mdui-textfield-input" name="user_id" type="text" required />
                            <div class="mdui-textfield-error">请输入用户名</div>
                        </div>
                    </div>
                    <div style="position: relative;">
                        <a class="mdui-btn mdui-btn-icon" style="display: inline-block; top: 40px; position: absolute;">
                            <i class="mdui-icon material-icons">vpn_key</i>
                        </a>
                        <div class="mdui-textfield mdui-textfield-floating-label"
                            style="width: calc(100% - 50px); display: inline-block; margin-left: 45px;">
                            <label class="mdui-textfield-label">密码</label>
                            <input class="mdui-textfield-input" name="password" type="password" autocomplete="off" required />
                            <div class="mdui-textfield-error">请输入密码</div>
                        </div>
                    </div>
                    <?php if($OJ_VCODE) { ?>
                        <div class="mdui-row" style="position: relative;">
                            <a class="mdui-btn mdui-btn-icon" style="display: inline-block; position: absolute; top: 40px; left: 10px">
                                <i class="mdui-icon material-icons">code</i>
                            </a>
                            <div class="mdui-textfield mdui-textfield-floating-label mdui-col-xs-4" style="display: inline-block; margin-left: 45px;">
                                <label class="mdui-textfield-label">验证码</label>
                                <input class="mdui-textfield-input" name="vcode" type="text" autocomplete=off  required/>
                                <div class="mdui-textfield-error">请输入验证码</div>
                            </div>
                            <div class="mdui-col-xs-2" style="display: inline-block; position: absolute; top: 40px;">
                                <img id="vcode-img" alt="点击更换验证码" onclick="this.src='vcode.php?'+Math.random()" height="30px">
                            </div>
                        </div>
                    <?php } ?>
                    <div class="mdui-card-actions mdui-m-t-4">
                        <div class="mdui-float-right">
                            <button name="submit" type="submit" class="mdui-btn mdui-m-a-0 mdui-color-theme-accent">登录</button>
                        </div>
                        <div class="mdui-float-left">
                            <a class="mdui-btn mdui-color-red" href="lostpassword.php">忘记密码</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <script src="<?php echo $OJ_CDN_URL?>include/md5-min.js"></script>
        <script>
        function jsMd5() {
            if ($("input[name=password]").val() == "") return false;
            $("input[name=password]").val(hex_md5($("input[name=password]").val()));
            return true;
        }
        </script>
    </div>
    <?php if ($OJ_VCODE) { ?>
    <script>
    document.addEventListener('DOMContentLoaded', (event) => {
        $("#vcode-img").attr("src", "vcode.php?" + Math.random());
    })
    </script>
    <?php } ?>
</body>

</html>
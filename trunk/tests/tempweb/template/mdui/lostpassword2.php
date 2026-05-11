<!DOCTYPE html>
<html lang="cn">

<head>
    <?php $page_title = "忘记密码"; ?>
    <?php include('_includes/head.php'); ?>
</head>

<body class="mdui-drawer-body-left mdui-theme-primary-indigo mdui-theme-accent-indigo mdui-appbar-with-toolbar">
    <?php include('_includes/header.php'); ?>
    <?php include('_includes/sidebar.php'); ?>
    <div class="mdui-container">
        <div class="jumbotron">
            <div class="mdui-row" style="display: flex; justify-content: center;">
                <div class="mdui-card mdui-col-sm-6">
                    <div class="mdui-card-primary">
                        <div class="mdui-card-primary-title" style="text-align: center">忘记密码</div>
                        <div class="mdui-card-primary-subtitle" style="text-align: center">验证 (2/2)</div>
                    </div>
                    <form action="lostpassword.php" method="post" role="form"
                        class="mdui-m-a-2 mdui-card-content" style="text-align: left;">
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
                                <label class="mdui-textfield-label">验证码</label>
                                <input class="mdui-textfield-input" name="lost_key" type="text" autocomplete="off" required />
                                <div class="mdui-textfield-error">请输入验证码</div>
                                <div class="mdui-textfield-helper">请填写发送到您邮箱的验证码。如果填写正确，这个验证码就自动成为您的新密码！</div>
                            </div>
                        </div>
                        <div class="mdui-row" style="position: relative;">
                            <a class="mdui-btn mdui-btn-icon" style="display: inline-block; position: absolute; top: 40px; left: 10px">
                                <i class="mdui-icon material-icons">code</i>
                            </a>
                            <div class="mdui-textfield mdui-textfield-floating-label mdui-col-xs-4" style="display: inline-block; margin-left: 45px;">
                                <label class="mdui-textfield-label">验证码</label>
                                <input class="mdui-textfield-input" name="vcode" type="text" autocomplete="off" autocomplete=off  required/>
                                <div class="mdui-textfield-error">请输入图片中显示的验证码</div>
                            </div>
                            <div class="mdui-col-xs-2" style="display: inline-block; position: absolute; top: 40px;">
                                <img id="vcode-img" alt="点击更换验证码" onclick="this.src='vcode.php?'+Math.random()"
                                    src="vcode.php" height="30px">
                            </div>
                        </div>
                        <div class="mdui-card-actions mdui-m-t-4">
                            <div class="mdui-float-right">
                                <button name="submit" type="submit" class="mdui-btn mdui-m-a-0 mdui-color-theme-accent">提交</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<!DOCTYPE html>
<html lang="cn">

<head>
    <?php $page_title = "注册"; ?>
    <?php include('_includes/head.php') ?>
</head>

<body class="mdui-drawer-body-left mdui-theme-primary-indigo mdui-theme-accent-indigo mdui-appbar-with-toolbar">
    <?php include('_includes/header.php'); ?>
    <?php include('_includes/sidebar.php'); ?>
    <div class="mdui-container">
        <div class="mdui-row" style="display: flex; justify-content: center;">
            <div class="mdui-card mdui-col-sm-6">
                <div class="mdui-card-primary">
                    <div class="mdui-card-primary-title" style="text-align: center">注册</div>
                    <!-- <div class="mdui-card-primary-subtitle"></div> -->
                </div>
                <form id="register" action="register.php" method="post" role="form" name="register"
                    class="mdui-m-a-2 mdui-card-content">
                    <div style="position: relative;">
                        <a class="mdui-btn mdui-btn-icon" style="display: inline-block; top: 40px; position: absolute;">
                            <i class="mdui-icon material-icons">account_circle</i>
                        </a>
                        <div class="mdui-textfield mdui-textfield-floating-label"
                            style="width: calc(100% - 50px); display: inline-block; margin-left: 45px;">
                            <label class="mdui-textfield-label">用户名</label>
                            <input class="mdui-textfield-input" id="user_id" name="user_id" type="text" required />
                            <div class="mdui-textfield-error">请输入用户名</div>
                        </div>
                    </div>
                    <div style="position: relative;">
                        <a class="mdui-btn mdui-btn-icon" style="display: inline-block; top: 40px; position: absolute;">
                            <i class="mdui-icon material-icons">face</i>
                        </a>
                        <div class="mdui-textfield mdui-textfield-floating-label"
                            style="width: calc(100% - 50px); display: inline-block; margin-left: 45px;">
                            <label class="mdui-textfield-label">昵称</label>
                            <input class="mdui-textfield-input" id="nick" name="nick" type="text" required />
                            <div class="mdui-textfield-error">请输入昵称</div>
                        </div>
                    </div>
                    <div style="position: relative;">
                        <a class="mdui-btn mdui-btn-icon" style="display: inline-block; top: 40px; position: absolute;">
                            <i class="mdui-icon material-icons">vpn_key</i>
                        </a>
                        <div class="mdui-textfield mdui-textfield-floating-label"
                            style="width: calc(100% - 50px); display: inline-block; margin-left: 45px;">
                            <label class="mdui-textfield-label">密码</label>
                            <input class="mdui-textfield-input" id="password" name="password" type="password"
                                pattern="^.*(?=.{6,})(?=.*[a-z])(?=.*[A-Z]).*$" autocomplete="off" required />
                            <div class="mdui-textfield-error">请输入至少 6 位，且包含大小写字母的密码</div>
                            <div class="mdui-textfield-helper">请输入至少 6 位，且包含大小写字母的密码</div>
                        </div>
                    </div>
                    <div style="position: relative;">
                        <a class="mdui-btn mdui-btn-icon" style="display: inline-block; top: 40px; position: absolute;">
                            <i class="mdui-icon material-icons">vpn_key</i>
                        </a>
                        <div id="rptpasswordf" class="mdui-textfield mdui-textfield-floating-label"
                            style="width: calc(100% - 50px); display: inline-block; margin-left: 45px;">
                            <label class="mdui-textfield-label">重复密码</label>
                            <input class="mdui-textfield-input" id="rptpassword" name="rptpassword" type="password"
                                pattern="^.*(?=.{6,})(?=.*[a-z])(?=.*[A-Z]).*$" autocomplete="off" required />
                            <div class="mdui-textfield-error">请再输入一遍密码</div>
                            <div class="mdui-textfield-helper">请再输入一遍密码</div>
                        </div>
                    </div>
                    <div style="position: relative;">
                        <a class="mdui-btn mdui-btn-icon" style="display: inline-block; top: 40px; position: absolute;">
                            <i class="mdui-icon material-icons">email</i>
                        </a>
                        <div class="mdui-textfield mdui-textfield-floating-label"
                            style="width: calc(100% - 50px); display: inline-block; margin-left: 45px;">
                            <label class="mdui-textfield-label">邮箱</label>
                            <input class="mdui-textfield-input" id="email" name="email" type="email" required />
                            <div class="mdui-textfield-error">请输入一个合法的邮箱</div>
                        </div>
                    </div>
                    <div style="position: relative;">
                        <a class="mdui-btn mdui-btn-icon" style="display: inline-block; top: 40px; position: absolute;">
                            <i class="mdui-icon material-icons">border_color</i>
                        </a>
                        <div class="mdui-textfield mdui-textfield-floating-label"
                            style="width: calc(100% - 50px); display: inline-block; margin-left: 45px;">
                            <label class="mdui-textfield-label">个性签名</label>
                            <input class="mdui-textfield-input" id="school" name="school" type="text" />
                        </div>
                    </div>
                    <?php if($OJ_VCODE) { ?>
                    <div class="mdui-row" style="position: relative;">
                        <a class="mdui-btn mdui-btn-icon"
                            style="display: inline-block; position: absolute; top: 40px; left: 10px">
                            <i class="mdui-icon material-icons">code</i>
                        </a>
                        <div class="mdui-textfield mdui-textfield-floating-label mdui-col-xs-4"
                            style="display: inline-block; margin-left: 45px;">
                            <label class="mdui-textfield-label">验证码</label>
                            <input class="mdui-textfield-input" name="vcode" type="text"  autocomplete=off  required />
                            <div class="mdui-textfield-error">请输入验证码</div>
                        </div>
                        <div class="mdui-col-xs-2" style="display: inline-block; position: absolute; top: 40px;">
                            <img id="vcode-img" alt="点击更换验证码"
                                onclick="this.src='vcode.php?'+Math.random()" height="30px">
                        </div>
                    </div>
                    <script>
                    document.addEventListener('DOMContentLoaded', (event) => {
                        $("#vcode-img").attr("src", "vcode.php?" + Math.random());
                    })
                    </script>
                    <?php } ?>
                    <div class="mdui-card-actions mdui-m-t-4">
                        <div class="mdui-float-right">
                            <button type="button" onclick="check();"
                                class="mdui-btn mdui-m-a-0 mdui-color-theme-accent">注册</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    function check() {
        if ($("#user_id").val().length < 3) {
            mdui.alert("用户名过短！");
            $("#user_id").trigger('focus');
            return;
        }
        if ($("#password").val().length < 6) {
            mdui.alert("密码过短！");
            $("#password").trigger('focus');
            return;
        }

        if ($("#password").val() !== $("#rptpassword").val()) {
            mdui.alert("两次输入的密码不一致");
            if(!$('#rptpasswordf').hasClass('mdui-textfield-invalid')) {
                $('#rptpasswordf').addClass('mdui-textfield-invalid');
            }
            $('#rptpasswordf>.mdui-textfield-error').text("两次输入的密码不一致");
            $("#rptpassword").trigger('focus');
            return;
        }
        document.getElementById('register').submit();
    }
    $('#rptpassword').on('input', function() {
        if ($("#password").val() != $("#rptpassword").val()) {
            if(!$('#rptpasswordf').hasClass('mdui-textfield-invalid')) {
                $('#rptpasswordf').addClass('mdui-textfield-invalid');
            }
            $('#rptpasswordf>.mdui-textfield-error').text('两次输入的密码不一致');
        } else {
            $('#rptpasswordf').removeClass('mdui-textfield-invalid');
            $('#rptpasswordf>.mdui-textfield-error').text('请输入至少 6 位，且包含大小写字母的密码');
        }
    });
    </script>
</body>

</html>
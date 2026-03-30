<!DOCTYPE html>
<html lang="cn">

<head>
    <?php $page_title = '修改信息: '.$user; ?>
    <?php include('_includes/head.php'); ?>
</head>

<body class="mdui-drawer-body-left mdui-theme-primary-indigo mdui-theme-accent-indigo mdui-appbar-with-toolbar">
    <?php include('_includes/header.php'); ?>
    <?php include('_includes/sidebar.php'); ?>
    <div class="mdui-container">
        <div class="mdui-row" style="display: flex; justify-content: center;">
            <div class="mdui-card mdui-col-sm-6">
                <div class="mdui-card-primary">
                    <div class="mdui-card-primary-title" style="text-align: center">修改信息</div>
                    <!-- <div class="mdui-card-primary-subtitle"></div> -->
                </div>
                <form id="modify" action="modify.php" method="post" role="form" name="modify"
                    class="mdui-m-a-2 mdui-card-content">
                    <div style="position: relative;">
                        <a class="mdui-btn mdui-btn-icon" style="display: inline-block; top: 40px; position: absolute;">
                            <i class="mdui-icon material-icons">account_circle</i>
                        </a>
                        <div class="mdui-textfield mdui-textfield-floating-label"
                            style="width: calc(100% - 50px); display: inline-block; margin-left: 45px;">
                            <label class="mdui-textfield-label">用户名</label>
                            <input class="mdui-textfield-input" id="user_id"
                                value="<?php echo htmlentities($_SESSION[$OJ_NAME.'_'.'user_id'], ENT_QUOTES, 'UTF-8')?>" type="text" disabled />
                        </div>
                    </div>
                    <?php require_once('./include/set_post_key.php');?>
                    <div style="position: relative;">
                        <a class="mdui-btn mdui-btn-icon" style="display: inline-block; top: 40px; position: absolute;">
                            <i class="mdui-icon material-icons">face</i>
                        </a>
                        <div class="mdui-textfield mdui-textfield-floating-label"
                            style="width: calc(100% - 50px); display: inline-block; margin-left: 45px;">
                            <label class="mdui-textfield-label">昵称</label>
                            <input class="mdui-textfield-input" id="nick" name="nick"
                                value="<?php echo htmlentities($row['nick'],ENT_QUOTES,"UTF-8")?>" type="text" required />
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
                            <input class="mdui-textfield-input" id="opassword" name="opassword" type="password"
                                autocomplete="off" required />
                            <div class="mdui-textfield-error">请输入密码</div>
                        </div>
                    </div>
                    <div style="position: relative;">
                        <a class="mdui-btn mdui-btn-icon" style="display: inline-block; top: 40px; position: absolute;">
                            <i class="mdui-icon material-icons">vpn_key</i>
                        </a>
                        <div id="rptpasswordf" class="mdui-textfield mdui-textfield-floating-label"
                            style="width: calc(100% - 50px); display: inline-block; margin-left: 45px;">
                            <label class="mdui-textfield-label">新密码</label>
                            <input class="mdui-textfield-input" id="npassword" name="npassword" type="password"
                                pattern="^.*(?=.{6,})(?=.*[a-z])(?=.*[A-Z]).*$" autocomplete="off" />
                            <div class="mdui-textfield-error">请输入密码</div>
                            <div class="mdui-textfield-helper">不修改密码可留空</div>
                        </div>
                    </div>
                    <div style="position: relative;">
                        <a class="mdui-btn mdui-btn-icon" style="display: inline-block; top: 40px; position: absolute;">
                            <i class="mdui-icon material-icons">vpn_key</i>
                        </a>
                        <div id="rptpasswordf" class="mdui-textfield mdui-textfield-floating-label"
                            style="width: calc(100% - 50px); display: inline-block; margin-left: 45px;">
                            <label class="mdui-textfield-label">重复新密码</label>
                            <input class="mdui-textfield-input" id="rptpassword" name="rptpassword" type="password"
                                pattern="^.*(?=.{6,})(?=.*[a-z])(?=.*[A-Z]).*$" autocomplete="off" />
                            <div class="mdui-textfield-error">请再输入一遍密码</div>
                            <div class="mdui-textfield-helper">请再输入一遍新密码，不修改密码可留空</div>
                        </div>
                    </div>
                    <div style="position: relative;">
                        <a class="mdui-btn mdui-btn-icon" style="display: inline-block; top: 40px; position: absolute;">
                            <i class="mdui-icon material-icons">email</i>
                        </a>
                        <div class="mdui-textfield mdui-textfield-floating-label"
                            style="width: calc(100% - 50px); display: inline-block; margin-left: 45px;">
                            <label class="mdui-textfield-label">邮箱</label>
                            <input class="mdui-textfield-input" id="email" name="email"
                                type="email" value="<?php echo htmlentities($row['email'],ENT_QUOTES,"UTF-8")?>"/>
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
                            <input class="mdui-textfield-input" id="school" name="school" type="text"
                                value="<?php echo htmlentities($row['school'], ENT_QUOTES, "UTF-8")?>" />
                        </div>
                    </div>
                    <div class="mdui-card-actions mdui-m-t-4">
                        <div class="mdui-float-left" style="display: inline-block;">
                            <a class="mdui-btn mdui-color-red" href=export_ac_code.php>导出全部 AC 代码</a>
                        </div>
                        <div class="mdui-float-right" style="display: inline-block;">
                            <button type="submit" class="mdui-btn mdui-m-a-0 mdui-color-theme-accent">提交</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
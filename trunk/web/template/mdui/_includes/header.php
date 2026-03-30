<header class="mdui-appbar mdui-appbar-fixed" style="z-index: 999;">
    <div class="mdui-toolbar mdui-color-theme">
        <a href="javascript:;" class="mdui-btn mdui-btn-icon" mdui-tooltip="{content: '菜单'}" mdui-drawer="{target: '#main-drawer', swipe: true}">
            <i class="mdui-icon material-icons">menu</i>
        </a>
        <a class="mdui-typo-title" href="<?php echo $OJ_HOME; ?>"><?php echo $OJ_NAME ?></a>
        <div class="mdui-toolbar-spacer"></div>
        <div class="mdui-btn" mdui-tooltip="{content: '账户'}" mdui-menu="{target: '#account-menu', position: 'bottom', fixed: true}">
            <i class="mdui-icon material-icons">account_circle</i>
            <?php if(isset($_SESSION[$OJ_NAME.'_'.'user_id'])) { ?>
                <?php echo htmlentities($_SESSION[$OJ_NAME.'_'.'user_id'], ENT_QUOTES, 'UTF-8'); ?>
            <?php } else { ?>
                请登录
            <?php } ?>
        </div>
        <ul class="mdui-menu mdui-menu-cascade" id="account-menu">
            <?php
            if(isset($_SESSION[$OJ_NAME.'_'.'user_id'])){
                $sid = $_SESSION[$OJ_NAME.'_'.'user_id']; ?>
                <li class="mdui-menu-item">
                    <a href="<?php echo $path_fix; ?>userinfo.php?user=<?php echo $sid; ?>"
                        class="mdui-ripple">我的信息</a>
                </li>
                <li class="mdui-menu-item">
                    <a href="<?php echo $path_fix; ?>modifypage.php" class="mdui-ripple">修改信息</a>
                </li>
                <li class="mdui-menu-item">
                    <a href="<?php echo $path_fix; ?>logout.php" class="mdui-ripple">退出登录</a>
                </li>
            <?php } else { ?>
                <li class="mdui-menu-item">
                    <a href="<?php echo $path_fix; ?>loginpage.php" class="mdui-ripple">登录</a>
                </li>
                <?php if(isset($OJ_REGISTER) && $OJ_REGISTER){ ?>
                    <li class="mdui-menu-item">
                        <a href="<?php echo $path_fix; ?>registerpage.php" class="mdui-ripple">注册</a>
                    </li>
                <?php } ?>
            <?php } ?>
        </ul>
    </div>
</header>
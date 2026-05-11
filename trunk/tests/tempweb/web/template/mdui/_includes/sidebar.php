<div class="mdui-drawer" id="main-drawer">
    <div class="mdui-list mdui-m-t-1 mdui-color-white">
        <!-- Menu -->
        <a class="mdui-list-item mdui-ripple <?php if (!$url || strpos($url, "index.php") !== false) echo "mdui-list-item-active"; ?>"
            href="<?php echo $path_fix; ?>index.php">
            <i class="mdui-list-item-icon mdui-icon material-icons">home</i>
            <div class="mdui-list-item-content">首页</div>
        </a>
        <a class="mdui-list-item mdui-ripple <?php if (strpos($url, "problemset.php") !== false || strpos($url, "problem.php") !== false) echo "mdui-list-item-active"; ?>"
            href="<?php echo $path_fix; ?>problemset.php">
            <i class="mdui-list-item-icon mdui-icon material-icons">book</i>
            <div class="mdui-list-item-content">题目</div>
        </a>
        <a class="mdui-list-item mdui-ripple <?php if (strpos($url, "category.php") !== false) echo "mdui-list-item-active"; ?>"
            href="<?php echo $path_fix; ?>category.php">
            <i class="mdui-list-item-icon mdui-icon material-icons">apps</i>
            <div class="mdui-list-item-content">分类</div>
        </a>
        <a class="mdui-list-item mdui-ripple <?php if (strpos($url, "status.php") !== false && strpos($url, "status.php?user_id=$sid") === false) echo "mdui-list-item-active"; ?>"
            href="<?php echo $path_fix; ?>status.php">
            <i class="mdui-list-item-icon mdui-icon material-icons">play_circle_outline</i>
            <div class="mdui-list-item-content">状态</div>
        </a>
        <a class="mdui-list-item mdui-ripple <?php if (strpos($url, "contest.php") !== false && $url !== "contest.php?my") echo "mdui-list-item-active"; ?>"
            href="<?php echo $path_fix; ?>contest.php">
            <i class="mdui-list-item-icon mdui-icon material-icons">layers</i>
            <div class="mdui-list-item-content">竞赛/作业</div>
        </a>
        <a class="mdui-list-item mdui-ripple <?php if (strpos($url, "ranklist.php") !== false) echo "mdui-list-item-active"; ?>"
            href="<?php echo $path_fix; ?>ranklist.php">
            <i class="mdui-list-item-icon mdui-icon material-icons">equalizer</i>
            <div class="mdui-list-item-content">排名</div>
        </a>
        <div class="mdui-divider"></div>

        <!-- User -->
        <?php
        if(isset($_SESSION[$OJ_NAME.'_'.'user_id'])){
            $sid = $_SESSION[$OJ_NAME.'_'.'user_id']; ?>
            <a class="mdui-list-item mdui-ripple <?php if (strpos($url, "status.php?user_id=$sid") !== false) echo "mdui-list-item-active"; ?>"
                href="<?php echo $path_fix; ?>status.php?user_id=<?php echo $sid; ?>">
                <i class="mdui-list-item-icon mdui-icon material-icons">play_circle_outline</i>
                <div class="mdui-list-item-content">我的提交</div>
            </a>
            <a class="mdui-list-item mdui-ripple <?php if ($url === "contest.php?my") echo "mdui-list-item-active"; ?>"
                href="<?php echo $path_fix; ?>contest.php?my">
                <i class="mdui-list-item-icon mdui-icon material-icons">apps</i>
                <div class="mdui-list-item-content">我参加的竞赛/作业</div>
            </a>
            <?php if(isset($_SESSION[$OJ_NAME.'_'.'administrator'])
                    || isset($_SESSION[$OJ_NAME.'_'.'contest_creator'])
                    || isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])
                    || isset($_SESSION[$OJ_NAME.'_'.'password_setter'])) { ?>
            <div class="mdui-divider"></div>

            <!-- Admin -->
            <a class="mdui-list-item mdui-ripple" href="<?php echo $path_fix; ?>admin/">
                <i class="mdui-list-item-icon mdui-icon material-icons">settings</i>
                <div class="mdui-list-item-content">后台管理</div>
            </a>
            <?php } ?>
        <?php } else { ?>
            <a class="mdui-list-item mdui-ripple <?php if ($url == "loginpage.php") echo "mdui-list-item-active"; ?>"
                href="<?php echo $path_fix; ?>loginpage.php">
                <i class="mdui-list-item-icon mdui-icon material-icons">assignment_ind</i>
                <div class="mdui-list-item-content">登录</div>
            </a>
            <?php if(isset($OJ_REGISTER) && $OJ_REGISTER){ ?>
                <a class="mdui-list-item mdui-ripple <?php if ($url == "registerpage.php") echo "mdui-list-item-active"; ?>"
                    href="<?php echo $path_fix; ?>registerpage.php">
                    <i class="mdui-list-item-icon mdui-icon material-icons">person_add</i>
                    <div class="mdui-list-item-content">注册</div>
                </a>
            <?php } ?>
        <?php } ?>
        
    </div>
</div>
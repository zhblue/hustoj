<!DOCTYPE html>
<html lang="cn"><head><?php $page_title = $view_title; include('_includes/head.php'); ?></head>
<body class="mdui-drawer-body-left mdui-theme-primary-indigo mdui-theme-accent-indigo mdui-appbar-with-toolbar">
<?php include('_includes/header.php'); include('_includes/sidebar.php'); ?>
<div class="mdui-container"><div class="mdui-card"><div class="mdui-card-primary"><div class="mdui-card-primary-title"><?php echo htmlentities($view_contest['title'] ?? $view_title, ENT_QUOTES, 'UTF-8'); ?></div></div>
<?php if ($view_error) echo '<div class="mdui-alert">'.htmlentities($view_error, ENT_QUOTES, 'UTF-8').'</div>'; ?>
<?php if ($view_message) echo '<div class="mdui-alert">'.htmlentities($view_message, ENT_QUOTES, 'UTF-8').'</div>'; ?>
<?php if ($view_contest) { ?><div class="mdui-card-content"><?php echo $view_contest['description']; ?><p><?php echo $MSG_START_TIME; ?>: <?php echo htmlentities($view_start_time, ENT_QUOTES, 'UTF-8'); ?><br><?php echo $MSG_END_TIME; ?>: <?php echo htmlentities($view_end_time, ENT_QUOTES, 'UTF-8'); ?><br><?php echo $MSG_CONTEST_CREATOR; ?>: <?php echo htmlentities($view_contest['user_id'], ENT_QUOTES, 'UTF-8'); ?></p>
<?php if ($view_registered) echo '<p class="mdui-text-color-green">'.$MSG_CONTEST_REGISTERED.'</p>'; else if (!$view_error && isset($_SESSION[$OJ_NAME.'_user_id'])) { ?><form method="post" action="contest_register.php?cid=<?php echo intval($cid); ?>"><input type="hidden" name="cid" value="<?php echo intval($cid); ?>"><?php require_once('./include/set_post_key.php'); ?><button class="mdui-btn mdui-color-theme-accent" type="submit"><?php echo $MSG_REGISTER; ?></button></form><?php } ?><p><a class="mdui-btn" href="contest.php?cid=<?php echo intval($cid); ?>"><?php echo $MSG_RETURN_CONTEST; ?></a></p></div><?php } ?>
</div></div></body></html>

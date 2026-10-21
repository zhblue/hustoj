<?php $show_title = $view_title . ' - ' . $OJ_NAME; include("template/$OJ_TEMPLATE/header.php"); ?>
<div class="padding">
  <h1><?php echo htmlentities($view_contest['title'] ?? $view_title, ENT_QUOTES, 'UTF-8'); ?></h1>
  <?php if ($view_error) echo '<div class="ui negative message">'.htmlentities($view_error, ENT_QUOTES, 'UTF-8').'</div>'; ?>
  <?php if ($view_message) echo '<div class="ui positive message">'.htmlentities($view_message, ENT_QUOTES, 'UTF-8').'</div>'; ?>
  <?php if ($view_contest) { ?>
    <div class="ui segment"><?php echo $view_contest['description']; ?></div>
    <p><?php echo $MSG_START_TIME; ?>: <?php echo htmlentities($view_start_time, ENT_QUOTES, 'UTF-8'); ?><br><?php echo $MSG_END_TIME; ?>: <?php echo htmlentities($view_end_time, ENT_QUOTES, 'UTF-8'); ?><br><?php echo $MSG_CONTEST_CREATOR; ?>: <?php echo htmlentities($view_contest['user_id'], ENT_QUOTES, 'UTF-8'); ?></p>
    <?php if ($view_registered) echo '<p class="ui positive message">'.$MSG_CONTEST_REGISTERED.'</p>'; else if (!$view_error && isset($_SESSION[$OJ_NAME.'_user_id'])) { ?><form method="post" action="contest_register.php?cid=<?php echo intval($cid); ?>"><input type="hidden" name="cid" value="<?php echo intval($cid); ?>"><?php require_once('./include/set_post_key.php'); ?><button class="ui primary button" type="submit"><?php echo $MSG_REGISTER; ?></button></form><?php } ?>
    <a class="ui button" href="contest.php?cid=<?php echo intval($cid); ?>"><?php echo $MSG_RETURN_CONTEST; ?></a>
  <?php } ?>
</div>
<?php include("template/$OJ_TEMPLATE/footer.php"); ?>

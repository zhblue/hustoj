<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlentities($view_title, ENT_QUOTES, 'UTF-8'); ?></title>
</head>
<body>
<div class="container">
  <?php include("template/$OJ_TEMPLATE/nav.php"); ?>
  <div class="jumbotron">
    <h2><?php echo htmlentities($view_contest['title'] ?? $view_title, ENT_QUOTES, 'UTF-8'); ?></h2>
    <?php if ($view_error) echo '<p class="alert alert-danger">'.htmlentities($view_error, ENT_QUOTES, 'UTF-8').'</p>'; ?>
    <?php if ($view_message) echo '<p class="alert alert-success">'.htmlentities($view_message, ENT_QUOTES, 'UTF-8').'</p>'; ?>
    <?php if ($view_contest) { ?>
      <div><?php echo $view_contest['description']; ?></div>
      <p><?php echo $MSG_START_TIME; ?>: <?php echo htmlentities($view_start_time, ENT_QUOTES, 'UTF-8'); ?><br><?php echo $MSG_END_TIME; ?>: <?php echo htmlentities($view_end_time, ENT_QUOTES, 'UTF-8'); ?><br><?php echo $MSG_CONTEST_CREATOR; ?>: <?php echo htmlentities($view_contest['user_id'], ENT_QUOTES, 'UTF-8'); ?></p>
      <?php if ($view_registered) { echo '<p class="text-success">'.$MSG_CONTEST_REGISTERED.'</p>'; } else if (!$view_error && isset($_SESSION[$OJ_NAME.'_user_id'])) { ?>
        <form method="post" action="contest_register.php?cid=<?php echo intval($cid); ?>"><input type="hidden" name="cid" value="<?php echo intval($cid); ?>"><?php require_once('./include/set_post_key.php'); ?><button type="submit" class="btn btn-primary"><?php echo $MSG_REGISTER; ?></button></form>
      <?php } ?><p><a href="contest.php?cid=<?php echo intval($cid); ?>"><?php echo $MSG_RETURN_CONTEST; ?></a></p>
    <?php } ?>
  </div>
</div>
<?php include("template/$OJ_TEMPLATE/js.php"); ?>
</body>
</html>

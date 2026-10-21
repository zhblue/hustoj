<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php echo htmlentities($view_title, ENT_QUOTES, 'UTF-8'); ?></title>
  <?php include("template/$OJ_TEMPLATE/css.php"); ?>
</head>
<body>
<div class="container">
  <?php include("template/$OJ_TEMPLATE/nav.php"); ?>
  <div class="jumbotron">
    <h2><?php echo htmlentities($view_contest['title'] ?? $view_title, ENT_QUOTES, 'UTF-8'); ?></h2>
    <?php if ($view_error) { ?><div class="alert alert-danger"><?php echo htmlentities($view_error, ENT_QUOTES, 'UTF-8'); ?></div><?php } ?>
    <?php if ($view_message) { ?><div class="alert alert-success"><?php echo htmlentities($view_message, ENT_QUOTES, 'UTF-8'); ?></div><?php } ?>
    <?php if ($view_contest) { ?>
      <p><?php echo $view_contest['description']; ?></p>
      <p><?php echo $MSG_START_TIME; ?>: <?php echo htmlentities($view_start_time, ENT_QUOTES, 'UTF-8'); ?><br>
        <?php echo $MSG_END_TIME; ?>: <?php echo htmlentities($view_end_time, ENT_QUOTES, 'UTF-8'); ?><br>
        <?php echo $MSG_CONTEST_CREATOR; ?>: <?php echo htmlentities($view_contest['user_id'], ENT_QUOTES, 'UTF-8'); ?></p>
      <?php if ($view_registered) { ?><p class="text-success"><?php echo $MSG_CONTEST_REGISTERED; ?></p><?php }
      else if (!$view_error && isset($_SESSION[$OJ_NAME . '_user_id'])) { ?>
        <form method="post" action="contest_register.php?cid=<?php echo intval($cid); ?>">
          <input type="hidden" name="cid" value="<?php echo intval($cid); ?>">
          <?php require_once('./include/set_post_key.php'); ?>
          <button class="btn btn-primary" type="submit"><?php echo $MSG_REGISTER; ?></button>
        </form>
      <?php } ?>
      <p><a class="btn btn-default" href="contest.php?cid=<?php echo intval($cid); ?>"><?php echo $MSG_RETURN_CONTEST; ?></a></p>
    <?php } ?>
  </div>
</div>
<?php include("template/$OJ_TEMPLATE/js.php"); ?>
</body>
</html>

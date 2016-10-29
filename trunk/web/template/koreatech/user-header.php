      <ul class="nav navbar-nav navbar-right">
<?php
if (isset($_SESSION['user_id'])) {
  require_once("./include/userinfo.php");
?>
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            <?php echo($sid)?><span class="caret"></span>
          </a>
          <ul class="dropdown-menu">
            <li><a href="./userinfo.php?user=<?php echo($sid);?>">Status</a></li>
            <li><a href="./modifypage.php"><?php echo($MSG_USERINFO)?></a></li>
            <li><a href="./mail.php">Mail (<?php echo($mail)?>)</a></li>
            <li><a href="./status.php?user_id=<?php echo($sid);?>">Recent</a></li>
<?php
  if (isset($_SESSION['administrator'])||isset($_SESSION['contest_creator'])||isset($_SESSION['problem_editor'])) {
?>
            <li><a href="./admin"><?php echo($MSG_ADMIN);?></a></li>
<?php
  }
?>
            <li role="separator" class="divider"></li>
            <!--li class="dropdown-header">Nav header</li-->
            <li><a href="./logout.php?url=<?php echo urlencode($_SERVER["REQUEST_URI"])?>"><?php echo($MSG_LOGOUT);?></a></li>
          </ul>
        </li>
<?php
} else {
?>
        <li><a href="./loginpage.php?url=<?php echo urlencode($_SERVER["REQUEST_URI"])?>"><?php echo($MSG_LOGIN)?></a></li>
        <li><a href="./registerpage.php"><?php echo($MSG_REGISTER)?></a></li>
<?php
}
?>
      </ul>

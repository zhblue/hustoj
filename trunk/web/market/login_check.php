<?php
@session_start ();
require_once (dirname(__FILE__)."/../include/db_info.inc.php");
if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'problem_editor']))
{
    echo "isLogin";
}
else
    echo "noLogin";
?>

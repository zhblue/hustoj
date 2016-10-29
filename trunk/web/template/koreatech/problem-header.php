<ul class="nav nav-tabs">
<?php
  if (isset($_GET['id'])) {
    if($problem_tab == "problem") echo "<li role='presentation' class='active'><a>$id</a></li>";
    else                          echo "<li role='presentation'><a href='problem.php?id=$id'>$id</a></li>";
    if($problem_tab == "submitpage") echo "<li role='presentation' class='active'><a>$MSG_SUBMIT</a></li>";
    else                             echo "<li role='presentation'><a href='submitpage.php?id=$id'>$MSG_SUBMIT</a></li>";
    if($problem_tab == "problemstatus") echo "<li role='presentation' class='active'><a>$MSG_STATUS</a></li>";
    else                                echo "<li role='presentation'><a href='problemstatus.php?id=$id'>$MSG_STATUS</a></li>";
    if(isset($_SESSION['administrator'])){
      require_once("include/set_get_key.php");
      echo "<li role='presentation'><a href='admin/problem_edit.php?id=$id&getkey=".$_SESSION['getkey']."'>Edit</a></li>";
      echo "<li role='presentation'><a href='admin/quixplorer/index.php?action=list&dir=$row->problem_id&order=name&srt=yes'>TestData</a></li>";
    }
  } else {
	  $PID="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    if($problem_tab == "problem") echo "<li role='presentation' class='active'><a>$MSG_PROBLEM $PID[$pid]</a></li>";
    else                          echo "<li role='presentation'><a href='problem.php?cid=$cid&pid=$pid'>$MSG_PROBLEM $PID[$pid]</a></li>";
    if($problem_tab == "submitpage") echo "<li role='presentation' class='active'><a>$MSG_SUBMIT</a></li>";
    else                             echo "<li role='presentation'><a href='submitpage.php?cid=$cid&pid=$pid'>$MSG_SUBMIT</a></li>";
  }
?>
</ul>


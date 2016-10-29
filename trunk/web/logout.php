<?php session_start();
unset($_SESSION['user_id']);
session_destroy();

$nextUrl=trim($_GET['url']);

echo "<script language='javascript'>\n";
  if ($nextUrl) {
    echo "location.href='$nextUrl';\n";
  } else {
    echo "location.href='\\';\n";
  }
echo "</script>";
?>

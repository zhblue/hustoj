<?php require_once ("admin-header.php");
if (!(isset($_SESSION['administrator']))){
	echo "<a href='../loginpage.php'>Please Login First!</a>";
	exit(1);
}
?>

<html>
  <head>
    <title>OJ Administration</title>
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Cache-Control" content="no-cache">
    <meta http-equiv="Content-Language" content="zh-cn">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link rel=stylesheet href='admin.css' type='text/css'>
  </head>
<body>



<div class="container-fluid">
  <?php require_once("admin-bar.php"); ?>
  <div class="row-fluid top-space">
    <div class="span2" >
      <div class="menu-group"  >
        <?php require_once("menu.php") ?>
      </div>
    </div>
    <div class="span10">
      <div class=" col copy-problem align-center">
        <div class="news-header">COPY PROBLEM<hr/></div>
          <ol>
          <li>
          Copy from http://plg1.cs.uwaterloo.ca/~acm00/
          <form method=POST action=problem_add_page_waterloo.php>
            <input name=url type=text size=100>
            <input type=submit>
          </form>
          </li>
          <li>
          Copy from acm.pku.edu.cn
          <form method=POST action=problem_add_page_pku.php>
            <input name=url type=text size=100>
            <input type=submit>
          </form>
          </li>

          <li>
          Copy from acm.hdu.edu.cn
          <form method=POST action=problem_add_page_hdu.php>
            <input name=url type=text size=100>
            <input type=submit>
          </form>
          </li>

          <li>
          Copy from acm.zju.edu.cn
          <form method=POST action=problem_add_page_zju.php>
            <input name=url type=text size=100>
            <input type=submit>
          </form>
          </li>

          </ol>

      </div>
    </div>
  </div>
</div>

</body>
</html>


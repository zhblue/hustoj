<!DOCTYPE html>
<html lang="ko">
<head>
  <title><?php echo $view_title?></title>
  <?php require_once("./template/".$OJ_TEMPLATE."/include-header.php");?>
</head>
<body>
  <?php require_once("oj-header.php");?>
  <?php echo $view_news?>
  <div class="container">
    <div class="col-md-12 jumbotron">
      <h1>Welcome!</h1>
      <p><a href="http://www.koreatech.ac.kr" target="_blank">한국기술교육대학교</a> <a href="http://cse.koreatech.ac.kr" target="_blank">컴퓨터공학부</a> Online Judge 에 오신것을 환영합니다.</p>
      <p>교내 프로그래밍 경시대회와, 학부생 대상의 Problem Solving교육을 위해 개선하고 있습니다.</p>
      <p>함께 개발 하실 분은 <a href="https://github.com/koreatech" target="_blank">github page</a>에 join하세요!</p>
    </div>
  </div>
  <?php require_once("oj-footer.php");?>
  <?php require_once("include-bottom.php");?>
</html>

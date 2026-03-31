<?php require_once ("admin-header.php");
if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator']))){
	exit(1);
}
?>
<ol>
<li>
Copy from http://uoj.ac
<form method=POST action=problem_add_page_uoj.php>
  <input name=url type=text size="100" class="input input-xxlarge" value="http://uoj.ac/problem/1">
  <input type=submit>
</form>
</li>
<!-- <li>
Copy from http://ybt.ssoier.cn:8088/......
<form method=POST action=problem_add_page_ybt.php>
  <input name=url type=text size=100  class="input input-xxlarge" value="http://ybt.ssoier.cn:8088/problem_show.php?pid=1000">
  <input type=submit>
</form>
</li>
-->
<li>
Copy from http://hustoj......
<form method=POST action=problem_add_page_hustoj.php>
  <input name=url type=text size=100 value="http://hustoj.com/oj/problem.php?id=1000">
  <input type=submit>
</form>
</li>
<li>
Copy from https://www.luogu.com.cn/problem/P1000
<form method=POST action="problem_add_page_luogu.php" >
  <input name=url type=text size=100 class="input input-xxlarge" value="https://www.luogu.com.cn/problem/P1000">
  <input type=submit>
</form>
</li>
<li>
Copy from https://loj.ac/problem/
<form method=POST action=problem_add_page_loj.php>
  <input name=url type=text size=100 >
  <input type=submit>
</form>
</li>
<li>
Copy from http://acm.student.cs.uwaterloo.ca/~acm00
<form method=POST action=problem_add_page_waterloo.php>
  <input name=url type=text size=100>
  <input type=submit>
</form>
</li>
<li>
Copy from acm.pku.edu.cn
<form method=POST action=problem_add_page_pku.php>
  <input name=url class='input input-xxlarge' type=text size=100 value='http://poj.org/problem?id=1000' >
  <input type=submit>
</form>
</li>

<li>
Copy from acm.hdu.edu.cn
<form method=POST action=problem_add_page_hdu.php>
  <input name=url class="input input-xxlarge" value="https://acm.hdu.edu.cn/showproblem.php?pid=1172" type=text size=100>
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

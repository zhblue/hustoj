<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<script src="<?php echo $OJ_CDN_URL.$path_fix ?>include/jquery-latest.js"></script>

<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE/"?>bootstrap.min.js"></script>


<!--<script src="--><?php //echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE/js/"?><!--vendors.js"></script>-->

<!--<script src="--><?php //echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE/js/"?><!--index.js"></script>-->
<!--<script src="--><?php //echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE/js/"?><!--login.js"></script>-->




<?php
if(file_exists("./admin/msg/$domain.txt"))
      $view_marquee_msg=file_get_contents($OJ_SAE?"saestor://web/msg.txt":"./admin/msg/$domain.txt");


?>
<!--  to enable mathjax in hustoj:
svn export http://github.com/mathjax/MathJax/trunk /home/judge/src/web/mathjax
<script type="text/javascript"
  src="mathjax/MathJax.js?config=TeX-AMS-MML_HTMLorMML">
</script>
or
<script type="text/javascript"
  src="https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML">
</script>

-->
<script>
//$(document).ready(function(){
//  var msg="<marquee style='margin-top:10px' id=broadcast direction='up' scrollamount=3 scrolldelay=50 onMouseOver='this.stop()'"+
//      " onMouseOut='this.start()' class=toprow>"+<?php //echo json_encode($view_marquee_msg); ?>//+"</marquee>";
//  $(".jumbotron").prepend(msg);
//  $("form").append("<div id='csrf' />");
//  $("#csrf").load("<?php //echo $path_fix?>//csrf.php");
//  $("body").append("<div id=footer class=center >GPLv2 licensed by <a href='https://github.com/zhblue/hustoj' >HUSTOJ</a> "+(new Date()).getFullYear()+" </div>");
//  $("body").append("<div class=center > <img src='http://hustoj.com/wx.jpg' width='120px'><img src='http://hustoj.com/alipay.png' width='120px'><br> 欢迎关注微信公众号onlinejudge</div>");
//});
//
//$(".hint pre").each(function(){
//	var plus="<span class='glyphicon glyphicon-plus'>Click</span>";
//	var content=$(this);
//	$(this).before(plus);
//	$(this).prev().click(function(){
//		content.toggle();
//	});
//
//});

  console.log("If you want to change the appearance of the web pages, make a copy of bs3 under template directory.\nRename it to whatever you like, and change the $OJ_TEMPLATE value in db_info.inc.php\nAfter that modify files under your own directory .\n");
  console.log("To enable mathjax in hustoj, check line 15 in /home/judge/src/web/template/bs3/js.php");

</script>


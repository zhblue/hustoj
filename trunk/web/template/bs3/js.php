<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<script src="<?php echo $path_fix."template/$OJ_TEMPLATE/"?>jquery.min.js"></script>

<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="<?php echo $path_fix."template/$OJ_TEMPLATE/"?>bootstrap.min.js"></script>

<?php
$msg_file = dirname(__FILE__)."/../../config/msg.txt";
if(file_exists($msg_file)){
    $view_marquee_msg=file_get_contents($OJ_SAE?"saestor://web/msg.txt":$msg_file);
}
?>
<!--<script type="text/javascript"
  src="https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML">
</script>
-->
<script>
$(document).ready(function(){
  var msg="<marquee style='margin-top:10px' id=broadcast scrolldelay=1000 scrollamount=50 onMouseOver='this.stop()'"+
      " onMouseOut='this.start()' class=toprow>"+<?php echo json_encode($view_marquee_msg); ?>+"</marquee>";
  $(".jumbotron").prepend(msg);
  $("form").append("<div id='csrf' />");
  $("#csrf").load("<?php echo $path_fix?>csrf.php");
  $("body").append("<div id=footer class=center >GPLv2 licensed by <a href='https://github.com/zhblue/hustoj' >HUSTOJ</a> "+(new Date()).getFullYear()+" </div>");
  $("body").append("<div class=center > <img src='http://hustoj.com/wx.jpg' width='96px'><br> 欢迎关注微信公众号onlinejudge</div>");
});
  console.log("If you want to change the appearance of the web pages, make a copy of bs3 under template directory.\nRename it to whatever you like, and change the $OJ_TEMPLATE value in db_info.inc.php\nAfter that modify files under your own directory .\n");

	if (window!=top){   
        top.location.href =window.location.href; 
    }  
</script>


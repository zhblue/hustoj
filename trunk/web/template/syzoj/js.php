
<?php
$msg_path=realpath(dirname(__FILE__)."/../../admin/msg/$domain.txt");
if(file_exists($msg_path))
	$view_marquee_msg=file_get_contents($OJ_SAE?"saestor://web/msg.txt":$msg_path);
else
	$view_marquee_msg="";


?>
<!--  to enable mathjax in hustoj:
svn export http://github.com/mathjax/MathJax/trunk /home/judge/src/web/mathjax
<script type="text/javascript"
  src="mathjax/MathJax.js?config=TeX-AMS-MML_HTMLorMML">
</script>
-->
<!--
or
<script type="text/javascript"
  src="https://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML">
</script>

-->
<script src="<?php echo $OJ_CDN_URL.$path_fix."template/bs3/"?>bootstrap.min.js"></script>
<script>
$(document).ready(function(){
  var msg="<marquee style='margin-top:-10px;margin-bottom:10px' id=broadcast direction='left' scrollamount=3 scrolldelay=50 onMouseOver='this.stop()'"+
      " onMouseOut='this.start()' class='padding' >"+<?php echo json_encode($view_marquee_msg); ?>+"</marquee>";
  <?php if ($view_marquee_msg!="") { ?>
		$("#main").prepend(msg);
  <?php } ?>
  $("form").append("<div id='csrf' />");
  $("#csrf").load("<?php echo $path_fix?>csrf.php");
  let left=window.innerWidth-parseInt($("#menu").css("width")) - 100;
  left/=2;
  $("#menu").attr("style","margin-left:auto;margin-right:auto;");
  var screen_width = window.screen.width;
  var screen_height = window.screen.height;
  if(screen_width < 800) $("#main").attr("class","");
  if(screen_width < 800) $("#MainBg-C").attr("class","");


});

$(".hint pre").each(function(){
        var plus="<span class='glyphicon glyphicon-plus'><?php echo $MSG_CLICK_VIEW_HINT?></span>";
        var content=$(this);
        $(this).before(plus);
        $(this).prev().click(function(){
                content.toggle();
        });
        $(this).hide();
});


  console.log("If you want to change the appearance of the web pages, make a copy of bs3 under template directory.\nRename it to whatever you like, and change the $OJ_TEMPLATE value in db_info.inc.php\nAfter that modify files under your own directory .\n");
  console.log("To enable mathjax in hustoj, check line 15 in /home/judge/src/web/template/bs3/js.php");

</script>


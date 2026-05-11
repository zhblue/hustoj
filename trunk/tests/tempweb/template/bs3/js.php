<!-- jQuery文件。务必在bootstrap.min.js 之前引入 -->
<script src="<?php echo $OJ_CDN_URL.$path_fix."include/"?>jquery-latest.js"></script>

<?php if(isset($OJ_MARKDOWN)&&$OJ_MARKDOWN){ ?>
<script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE/"?>marked.min.js"></script>
<?php } ?>

<!-- 最新的 Bootstrap 核心 JavaScript 文件 -->
<script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE/"?>bootstrap.min.js"></script>

<?php
$msg_path=realpath(dirname(__FILE__)."/../../admin/msg/$domain.txt");
if(file_exists($msg_path))
	$view_marquee_msg=file_get_contents($OJ_SAE?"saestor://web/msg.txt":$msg_path);
else
	$view_marquee_msg="";
?>

<script>
$(document).ready(function(){
	<?php if($view_marquee_msg!="") { ?> 
  	var msg="<marquee style='margin-top:10px' direction='left' scrollamount=3 scrolldelay=50 onMouseOver='this.stop()'"+
      " onMouseOut='this.start()' class=toprow>"+<?php echo json_encode($view_marquee_msg); ?>+"</marquee>";
  	$(".jumbotron").prepend(msg);
  <?php } ?>
  
  $("form").append("<div id='csrf' />");
  $("#csrf").load("<?php echo $path_fix?>csrf.php");
  $("body").append("<div id=footer class=center >GPLv2 licensed by <a href='https://github.com/zhblue/hustoj' >HUSTOJ</a> "+(new Date()).getFullYear()+" </div>");
  $("body").append("<center><?php echo $MSG_HELP_HUSTOJ?></center>");  
  $("body").append("<div class=center > <img src='http://cdn.hustoj.com/wx.jpg' width='120px'><img src='http://cdn.hustoj.com/alipay.png' width='120px'><br> 欢迎关注微信公众号onlinejudge</div>");
  
  <?php if(isset($OJ_BEIAN)&&$OJ_BEIAN){ ?>
         $("body").append("<br><center><img src='image/icp.png'><a href='http://beian.miit.gov.cn/' target='_blank'><?php echo $OJ_BEIAN?></a></center>");
  <?php } ?>

	
  <?php 
	if(isset($_SESSION[$OJ_NAME."_administrator"])) echo "admin_mod();";
  ?>
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
  console.log("To enable mathjax in hustoj, set \n static  $OJ_MATHJAX=true;  // 激活mathjax \n in db_info.inc.php");
function admin_mod(){
	$("div[fd=source]").each(function(){
		let pid=$(this).attr('pid');	
		$(this).append("<span><span class='label label-success' pid='"+pid+"' onclick='problem_add_source(this,"+pid+");'>+</span></span>");

	});
	$("span[fd=time_limit]").each(function(){
		let sp=$(this);
		let pid=$(this).attr('pid');	
		$(this).dblclick(function(){
			let time=sp.text();
			console.log("pid:"+pid+"  time_limit:"+time);	
			sp.html("<form onsubmit='return false;'><input type=hidden name='m' value='problem_update_time'><input type='hidden' name='pid' value='"+pid+"'><input type='text' name='t' value='"+time+"' selected='true' class='input-mini' size=2 ></form>");
			let ipt=sp.find("input[name=t]");
			ipt.focus();
			ipt[0].select();
			sp.find("input").change(function(){
				let newtime=sp.find("input[name=t]").val();
				$.post("admin/ajax.php",sp.find("form").serialize()).done(function(){
					console.log("new time_limit:"+time);
					sp.html(newtime);
				});
			
			});
		});

	});
}
function problem_add_source(sp,pid){
	console.log("pid:"+pid);
	let p=$(sp).parent();
	p.html("<form onsubmit='return false;'><input type='hidden' name='m' value='problem_add_source'><input type='hidden' name='pid' value='"+pid+"'><input type='text' class='input input-large' name='ns'></form>");
	p.find("input").focus();
	p.find("input").change(function(){
		console.log(p.find("form").serialize());
		let ns=p.find("input[name=ns]").val();
		console.log("new source:"+ns);
		$.post("admin/ajax.php",p.find("form").serialize());
		p.parent().append("<span class='label label-success'>"+ns+"</span>");
		p.html("<span class='label label-success' pid='"+pid+"' onclick='problem_add_source(this,"+pid+");'>+</span>");
	});
}
</script>


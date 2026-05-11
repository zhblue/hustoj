<?php
$cur_path = "template/$OJ_TEMPLATE/";
	$url=basename($_SERVER['REQUEST_URI']);
	$dir=basename(getcwd());
	if($dir=="discuss3") {
        $cur_path = "../template/$OJ_TEMPLATE/";
        $path_fix="../";
	}
	else $path_fix="";
 	if(isset($OJ_NEED_LOGIN)&&$OJ_NEED_LOGIN&&(
                  $url!='loginpage.php'&&
                  $url!='lostpassword.php'&&
                  $url!='lostpassword2.php'&&
                  $url!='registerpage.php'
                  ) && !isset($_SESSION[$OJ_NAME.'_'.'user_id'])){
 
           header("location:".$path_fix."loginpage.php");
           exit();
        }

	if($OJ_ONLINE){
		require_once($path_fix.'include/online.php');
		$on = new online();
	}

?>
<link rel="stylesheet" href="<?php echo $cur_path?>layui/css/layui.css">
<script src="<?php echo $cur_path?>layui/layui.js"></script>


<ul class="layui-nav" lay-filter="">
    <li class='layui-nav-item <?php if ($url=="index.php") echo "layui-this";?>'><a href="<?php echo $path_fix?>index.php">主页</a></li>
    <li class='layui-nav-item <?php if ($url=="problemset.php") echo "layui-this";?>'><a href="<?php echo $path_fix?>problemset.php">练习</a></li>
    <li class='layui-nav-item <?php if ($url=="contest.php") echo "layui-this";?>'><a href="<?php echo $path_fix?>contest.php">竞赛</a></li>
    <li class='layui-nav-item <?php if ($url=="category.php") echo "layui-this";?>'><a href="<?php echo $path_fix?>category.php">分类</a></li>
    <li class='layui-nav-item <?php if ($url=="status.php") echo "layui-this";?>'><a href="<?php echo $path_fix?>status.php">状态</a></li>
    <li class='layui-nav-item <?php if ($url=="ranklist.php") echo "layui-this";?>'><a href="<?php echo $path_fix?>ranklist.php">排名</a></li>
	<?php if($OJ_BBS){?>
    <li class='layui-nav-item <?php if ($url=="bbs.php") echo "layui-this";?>'><a href="<?php echo $path_fix?>bbs.php">社区</a></li>
	<?php } ?>
    <li class='layui-nav-item <?php if ($url=="faqs.php") echo "layui-this";?>'><a href="<?php echo $path_fix?>faqs.php">问答</a></li>


    <li class="layui-nav-item">
        <a href="javascript:;"><span id="profile">Login</span></a>
        <dl class="layui-nav-child"> <!-- 二级菜单 -->
            <script src="<?php echo $path_fix."template/$OJ_TEMPLATE/swProfile.php?".rand();?>" ></script>
        </dl>
    </li>


</ul>
<!--<ul class="layui-nav layui-layout-right">-->
<!--    <li class="layui-nav-item">-->
<!--        <a href="javascript:;"><span id="profile">Login</span></a>-->
<!--        <dl class="layui-nav-child"> <!-- 二级菜单 -->
<!--            <script src="--><?php //echo $path_fix."template/$OJ_TEMPLATE/swProfile.php?".rand();?><!--" ></script>-->
<!--        </dl>-->
<!--    </li>-->
<!--</ul>-->

<script>
    //注意：导航 依赖 element 模块，否则无法进行功能性操作
    layui.use('element', function(){
        var element = layui.element;
        //…
    });
</script>

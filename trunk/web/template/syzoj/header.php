<?php
        require_once(dirname(__FILE__)."/../../include/memcache.php");
        function checkmail(){  // check if has mail
          global $OJ_NAME;
          $sql="SELECT count(1) FROM `mail` WHERE new_mail=1 AND `to_user`=?";
          $result=pdo_query($sql,$_SESSION[$OJ_NAME.'_'.'user_id']);
          if(!$result) return false;
          $row=$result[0];
          //if(intval($row[0])==0) return false;
          $retmsg="<span id=red>(".$row[0].")</span>";
          return $retmsg;
        }

        function get_menu_news() {
            $result = "";
            $sql_news_menu = "select `news_id`,`title` FROM `news` WHERE `menu`=1 AND `title`!='faqs.cn' ORDER BY `importance` ASC,`time` DESC LIMIT 10";
            $sql_news_menu_result = mysql_query_cache( $sql_news_menu );
            if ( $sql_news_menu_result ) {
                foreach ( $sql_news_menu_result as $row ) {
                    $result .= '<a class="item" href="/viewnews.php?id=' . $row['news_id'] . '">' ."<i class='star icon'></i>" . $row['title'] . '</a>';
                }
            }
            return $result;
        }
        $url=basename($_SERVER['REQUEST_URI']);
        $dir=basename(getcwd());
        if($dir=="discuss3") $path_fix="../";
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

        $sql_news_menu_result_html = "";

        if ($OJ_MENU_NEWS) {
            if ($OJ_REDIS) {
                $redis = new Redis();
                $redis->connect($OJ_REDISSERVER, $OJ_REDISPORT);

                if (isset($OJ_REDISAUTH)) {
                  $redis->auth($OJ_REDISAUTH);
                }
                $redisDataKey = $OJ_REDISQNAME . '_MENU_NEWS_CACHE';
                if ($redis->exists($redisDataKey)) {
                    $sql_news_menu_result_html = $redis->get($redisDataKey);
                } else {
                    $sql_news_menu_result_html = get_menu_news();
                    $redis->set($redisDataKey, $sql_news_menu_result_html);
                    $redis->expire($redisDataKey, 300);
                }

                $redis->close();
            } else {
                $sessionDataKey = $OJ_NAME.'_'."_MENU_NEWS_CACHE";
                if (isset($_SESSION[$sessionDataKey])) {
                    $sql_news_menu_result_html = $_SESSION[$sessionDataKey];
                } else {
                    $sql_news_menu_result_html = get_menu_news();
                    $_SESSION[$sessionDataKey] = $sql_news_menu_result_html;
                }
            }
        }
?>

<!DOCTYPE html>
<html lang="cn" style="position:fixed; width: 100%; overflow: hidden; ">

<head>
    <meta charset="utf-8">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=0.5">
    <title><?php echo $show_title ?></title>
    <?php include(dirname(__FILE__)."/css.php");?>
        <style>
@media (max-width: 991px) {
        .mobile-only {
                display:block !important;
        }

        .desktop-only {
            display:none !important;
        }
}

/**毛玻璃背景*/
.padding {
  background: rgba(255,255,255,0.6);
  box-shadow: inset 5px 5px 20px 0px rgba(255,255,255,0.1);
  border-radius: 20px;
      box-shadow: 10px -10px 20px rgb(255 255 255 / 20%), -10px 10px 20px rgb(255 255 255 / 10%);
  backdrop-filter: blur(7px);
  border-bottom:3px solid rgba(255,255,255,0.4);
  border-right: 3px solid rgba(255,255,255,0.4);
  border-left: 3px solid rgba(255, 255, 255, 0.4);
  /*filter: brightness(1.1);*/
}

</style>
    <script src="<?php echo "$OJ_CDN_URL/include/"?>jquery-latest.js"></script>

<!-- Scripts -->
<script>
    console.log('\n %c HUSTOJ %c https://github.com/zhblue/hustoj %c\n', 'color: #fadfa3; background: #000000; padding:5px 0;', 'background: #fadfa3; padding:5px 0;', '');
    console.log('\n %c Theme By %c Baoshuo ( @renbaoshuo ) %c https://baoshuo.ren %c\n', 'color: #fadfa3; background: #000000; padding:5px 0;', 'background: #fadfa3; padding:5px 0;', 'background: #ffbf33; padding:5px 0;', '');
    console.log('\n GitHub Homepage: https://github.com/zhblue/hustoj \n Document: https://zhblue.github.io/hustoj \n Bug report URL: https://github.com/zhblue/hustoj/issues \n \n%c ★ Please give us a star on GitHub! ★ %c \n', 'color: red;', '')
</script>
</head>

<?php
        if(!isset($_GET['spa'])){
?>
   
<body id="MainBg-C" style="position: relative; margin-top: 49px; height: calc(100% - 49px); overflow-y: overlay;">
    <div id="page-header" class="ui fixed borderless menu" style="position: fixed; height: 49px; z-index:99999">
        <div id="menu" class="ui stackable mobile ui container computer" style="margin-left:auto;margin-right:auto;">
            <a class="header item"  href="/"><span
                    style="font-family: 'Exo 2'; font-size: 1.5em; font-weight: 600; "><?php echo $domain==$DOMAIN?$OJ_NAME:ucwords($OJ_NAME)."'s OJ"?></span></a>
            
          <?php
            if(isset($OJ_AI_HTML)&&$OJ_AI_HTML) echo $OJ_AI_HTML;
            else echo '<a class="desktop-only item" href="/"><i class="home icon"></i>'.$MSG_HOME.'</a>';
            if(file_exists("moodle"))  // 如果存在moodle目录，自动添加链接
            {
              echo '<a class="item" href="moodle"><i class="group icon"></i>Moodle</a>';
            }
            if(!isset($_GET['cid'])||$cid==0){
          ?>
            <!-- 问题 -->
            <a class="item <?php if ($url=="problemset.php") echo "active";?>"
                href="<?php echo $path_fix?>problemset.php"><i class="list icon"></i><?php echo $MSG_PROBLEMS?> </a>
            <!-- 来源/分类 -->
            <a class="item <?php if ($url=="category.php") echo "active";?>"
                href="<?php echo $path_fix?>category.php"><i class="globe icon"></i><?php echo $MSG_SOURCE?></a>
            <!-- 竞赛/作业 -->
            <a class="item <?php if ($url=="contest.php") echo "active";?>" href="<?php echo $path_fix?>contest.php<?php if(isset($_SESSION[$OJ_NAME."_user_id"])) echo "?my" ?>" ><i
                    class="trophy icon"></i> <?php echo $MSG_CONTEST?></a>
            <!-- 状态 -->
            <a class="item <?php if ($url=="status.php") echo "active";?>" href="<?php echo $path_fix?>status.php"><i
                    class="tasks icon"></i><?php echo $MSG_STATUS?></a>
            <!-- 排名 -->
            <a class="desktop-only item <?php if ($url=="ranklist.php") echo "active";?> "
                href="<?php echo $path_fix?>ranklist.php"><i class="signal icon"></i> <?php echo $MSG_RANKLIST?></a>
            <!--<a class="item <?php //if ($url=="contest.php") echo "active";?>" href="/discussion/global"><i class="comments icon"></i> 讨论</a>-->
            <!-- 近期比赛 -->    
<?php if(isset($OJ_RECENT_CONTEST)&&$OJ_RECENT_CONTEST){    ?>
            <a class="desktop-only item <?php if ($url=="recent-contest.php") echo "active";?> "
                href="<?php echo $path_fix?>recent-contest.php"><i class="bullhorn icon"></i> <?php echo $MSG_RECENT_CONTEST?></a>
<?php } ?>
            <!-- 常见问答 -->
            <a class="desktop-only item <?php if ($url=="faqs.php") echo "active";?>" href="<?php echo $path_fix?>faqs.php"><i
                    class="help circle icon"></i> <?php echo $MSG_FAQ?></a>
            <!-- 讨论板 -->
              <?php if (isset($OJ_BBS)&& $OJ_BBS){ ?>
                  <a class='item' href="discuss.php"><i class="clipboard icon"></i> <?php echo $MSG_BBS?></a>
              <?php }

            }
                ?>
            <?php if( isset($_GET['cid']) && intval($_GET['cid'])>0 ){
                $cid=intval($_GET['cid']);
            ?>
            <a id="" class="item" href="<?php echo $path_fix?>contest.php" ><i class="arrow left icon"></i><?php echo $MSG_CONTEST.$MSG_LIST?></a>
            <a id="" class="item active" href="<?php echo $path_fix?>contest.php?cid=<?php echo $cid?>" ><i class="list icon"></i><?php echo $MSG_PROBLEMS.$MSG_LIST?></a>
            <a id="" class="item active" href="<?php echo $path_fix?>status.php?cid=<?php echo $cid?>" ><i class="tasks icon"></i><?php echo $MSG_STATUS.$MSG_LIST?></a>
            <a id="" class="item active" href="<?php echo $path_fix?>contestrank.php?cid=<?php echo $cid?>" ><i class="numbered list icon"></i><?php echo $MSG_RANKLIST?></a>
            <a id="" class="item active" href="<?php echo $path_fix?>contestrank-oi.php?cid=<?php echo $cid?>" ><i class="child icon"></i>OI-<?php echo $MSG_RANKLIST?></a>
            <?php if (isset($OJ_BBS)&& $OJ_BBS){ ?>
                  <a class='item active' href="discuss.php?cid=<?php echo $cid?>"><i class="clipboard icon"></i> <?php echo $MSG_BBS?></a>
             <?php } ?>

                    <?php if(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'contest_creator'])||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])){ ?>
                            <a id="" class="item active" href="<?php echo $path_fix?>conteststatistics.php?cid=<?php echo $cid?>" ><i class="eye icon"></i><?php echo $MSG_STATISTICS?></a>
                    <?php }  ?>
            <?php }  ?>
            <?php echo $sql_news_menu_result_html; ?>
            <div class="right menu">
                <?php if(isset($_SESSION[$OJ_NAME.'_'.'user_id'])) { ?>
                <a href="<?php echo $path_fix?>/userinfo.php?user=<?php echo $_SESSION[$OJ_NAME.'_'.'user_id']?>"
                    style="color: inherit; ">
                    <div class="ui simple dropdown item">
                        <?php echo $_SESSION[$OJ_NAME.'_'.'user_id']; 
                              if(!empty($_SESSION[$OJ_NAME.'_nick'])) echo "(".$_SESSION[$OJ_NAME.'_nick'].")";
                              if(!empty($_SESSION[$OJ_NAME.'_group_name'])) echo "[".$_SESSION[$OJ_NAME.'_group_name']."]";
                                      
                        ?>
                        <i class="dropdown icon"></i>
                        <div class="menu">
                                <a class="item" href="modifypage.php"><i class="edit icon"></i><?php echo $MSG_REG_INFO;?></a>
                                <a class="item" href="portal.php"><i class="tasks icon"></i><?php echo $MSG_TODO;?></a>
                                <?php if ($OJ_SaaS_ENABLE){ ?>
                                <?php if($_SERVER['HTTP_HOST']==$DOMAIN)
                                        echo  "<a class='item' href='http://".  $_SESSION[$OJ_NAME.'_'.'user_id'].".$DOMAIN'><i class='globe icon' ></i>MyOJ</a>";?>
                                <?php } ?>
                            <?php if(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'contest_creator'])||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])){ ?>
                            <a class="item" href="admin/"><i class="settings icon"></i><?php echo $MSG_ADMIN;?></a>
                            <?php }
if(isset($_SESSION[$OJ_NAME.'_'.'balloon'])){
  echo "<a class=item href='balloon.php'><i class='golf ball icon'></i>$MSG_BALLOON</a>";
}
                              if((isset($OJ_EXAM_CONTEST_ID)&&$OJ_EXAM_CONTEST_ID>0)||
                                     (isset($OJ_ON_SITE_CONTEST_ID)&&$OJ_ON_SITE_CONTEST_ID>0)||
                                     (isset($OJ_MAIL)&&!$OJ_MAIL)){
                                      // mail can not use in contest or mail is turned off
                              }else{
                                    $mail=checkmail();
                                    if($mail) echo "<a class='item mail' href=".$path_fix."mail.php><i class='mail icon'></i>$MSG_MAIL$mail</a>";
                              }




                            ?>
        <?php
        if(isset($OJ_PRINTER) && $OJ_PRINTER)
        {
        ?>
          <a  class="item"  href="printer.php">
            <i class="print icon"></i> <?php echo $MSG_PRINTER?>
          </a>
        <?php
        }
        ?>
                            <a class="item" href="logout.php"><i class="power icon"></i><?php echo $MSG_LOGOUT;?></a>
                        </div>
                    </div>
                </a>
                <?php } else { ?>


                <div class="item">
                    <a class="ui button" style="margin-right: 0.5em; " href="loginpage.php">
                       <?php echo $MSG_LOGIN?>
                    </a>
                    <?php if(isset($OJ_REGISTER)&&$OJ_REGISTER ){ ?>
                    <a class="ui primary button" href="registerpage.php">
                       <?php echo $MSG_REGISTER?>
                    </a>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div style="margin-top: 28px; ">
        <div id="main" class="ui main container">
<?php } ?>

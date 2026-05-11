<?php
        require_once(dirname(__FILE__)."/../../include/memcache.php");

        function checkmail(){
          global $OJ_NAME;
          $sql="select count(1) cnt FROM `mail` WHERE new_mail=1 AND `to_user`=?";
          $result=pdo_query($sql,$_SESSION[$OJ_NAME.'_'.'user_id']);
          if(empty($result)) return false;
          $row=$result[0];
          $retmsg="<span id=red>(".$row['cnt'].")</span>";
          return $retmsg;
        }

        function get_menu_news() {
            $result = "";
            $sql_news_menu = "select `news_id`,`title` FROM `news` WHERE `menu`=1 AND `title`!='faqs.cn' ORDER BY `importance` ASC,`time` DESC LIMIT 10";
            $sql_news_menu_result = mysql_query_cache( $sql_news_menu );
            if ( $sql_news_menu_result ) {
                foreach ( $sql_news_menu_result as $row ) {
                    $result .= '<a class="dropdown-item" href="/viewnews.php?id=' . $row['news_id'] . '"><i class="bi bi-star"></i> ' . $row['title'] . '</a>';
                }
            }
            return $result;
        }

        $url=isset($_SERVER['REQUEST_URI']) ? basename($_SERVER['REQUEST_URI']) : '';
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
<html lang="cn">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $show_title ?></title>
    <?php include(dirname(__FILE__)."/css.php");?>
</head>
<?php
        if(!isset($_GET['spa'])){
?>
<body style="padding-top: 70px; min-height: calc(100vh - 70px);">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" style="z-index: 9999;">
        <div class="container">
            <a class="navbar-brand" href="/">
                <span style="font-family: 'Exo 2', sans-serif; font-weight: 600;">
                    <?php echo $domain==$DOMAIN?$OJ_NAME:ucwords($OJ_NAME)."'s OJ"?>
                </span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto">
                    <?php
                        if(isset($OJ_AI_HTML)&&$OJ_AI_HTML && !isset($OJ_ON_SITE_CONTEST_ID) ) echo $OJ_AI_HTML;
                        else echo '<li class="nav-item"><a class="nav-link" href="/"><i class="bi bi-house"></i> '.$MSG_HOME.'</a></li>';

                        if(file_exists("moodle")){
                            echo '<li class="nav-item"><a class="nav-link" href="moodle"><i class="bi bi-people"></i> Moodle</a></li>';
                        }
                        if(file_exists("hello")){
                            echo '<li class="nav-item"><a class="nav-link" onclick="window.open(\'/hello/index.html\', \'_blank\', \'width=600,height=850,left=\' + (window.screen.width-600) + \',top=0,toolbar=no,menubar=no,location=no,status=no,resizable=yes\');"><i class="bi bi-book"></i> Hello算法</a></li>';
                        }

                        if( !isset($OJ_ON_SITE_CONTEST_ID) && (!isset($_GET['cid'])||$cid==0) ){
                    ?>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($url=="problemset.php") echo "active";?>" href="<?php echo $path_fix?>problemset.php">
                            <i class="bi bi-list-ol"></i> <?php echo $MSG_PROBLEMS?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($url=="category.php") echo "active";?>" href="<?php echo $path_fix?>category.php">
                            <i class="bi bi-globe"></i> <?php echo $MSG_SOURCE?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($url=="contest.php") echo "active";?>" href="<?php echo $path_fix?>contest.php<?php if(isset($_SESSION[$OJ_NAME."_user_id"])) echo "?my" ?>">
                            <i class="bi bi-trophy"></i> <?php echo $MSG_CONTEST?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($url=="status.php") echo "active";?>" href="<?php echo $path_fix?>status.php">
                            <i class="bi bi-check2-square"></i> <?php echo $MSG_STATUS?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($url=="ranklist.php") echo "active";?>" href="<?php echo $path_fix?>ranklist.php">
                            <i class="bi bi-bar-chart"></i> <?php echo $MSG_RANKLIST?>
                        </a>
                    </li>
                    <?php if(isset($OJ_RECENT_CONTEST)&&$OJ_RECENT_CONTEST){ ?>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($url=="recent-contest.php") echo "active";?>" href="<?php echo $path_fix?>recent-contest.php">
                            <i class="bi bi-megaphone"></i> <?php echo $MSG_RECENT_CONTEST?>
                        </a>
                    </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a class="nav-link <?php if ($url=="faqs.php") echo "active";?>" href="<?php echo $path_fix?>faqs.php">
                            <i class="bi bi-question-circle"></i> <?php echo $MSG_FAQ?>
                        </a>
                    </li>
                    <?php if (isset($OJ_BBS)&& $OJ_BBS){ ?>
                    <li class="nav-item">
                        <a class="nav-link" href="discuss.php"><i class="bi bi-chat-left-text"></i> <?php echo $MSG_BBS?></a>
                    </li>
                    <?php }
                        }
                    ?>

                    <?php if( isset($_GET['cid']) && intval($_GET['cid'])>0 ){
                         $cid=intval($_GET['cid']);
                         if(!isset($OJ_ON_SITE_CONTEST_ID)){
                    ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $path_fix?>contest.php"><i class="bi bi-arrow-left"></i> <?php echo $MSG_CONTEST.$MSG_LIST?></a>
                    </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo $path_fix?>contest.php?cid=<?php echo $cid?>">
                            <i class="bi bi-list-ol"></i> <?php echo $MSG_PROBLEMS.$MSG_LIST?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo $path_fix?>status.php?cid=<?php echo $cid?>">
                            <i class="bi bi-check2-square"></i> <?php echo $MSG_STATUS.$MSG_LIST?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo $path_fix?>contestrank.php?cid=<?php echo $cid?>">
                            <i class="bi bi-list-ol"></i> <?php echo $MSG_RANKLIST?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo $path_fix?>contestrank-oi.php?cid=<?php echo $cid?>">
                            <i class="bi bi-person"></i> OI-<?php echo $MSG_RANKLIST?>
                        </a>
                    </li>
                    <?php if (isset($OJ_BBS)&& $OJ_BBS){ ?>
                    <li class="nav-item">
                        <a class="nav-link active" href="discuss.php?cid=<?php echo $cid?>"><i class="bi bi-chat-left-text"></i> <?php echo $MSG_BBS?></a>
                    </li>
                    <?php } ?>
                    <?php if(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'contest_creator'])||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])){ ?>
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo $path_fix?>conteststatistics.php?cid=<?php echo $cid?>">
                            <i class="bi bi-eye"></i> <?php echo $MSG_STATISTICS?>
                        </a>
                    </li>
                    <?php }
                       } ?>

                    <?php if($OJ_MENU_DROPDOWN && $sql_news_menu_result_html){ ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="studyDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-book"></i> 学习资料
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="studyDropdown">
                            <?php echo $sql_news_menu_result_html; ?>
                        </ul>
                    </li>
                    <?php } ?>
                </ul>

                <ul class="navbar-nav">
                    <?php if(isset($_SESSION[$OJ_NAME.'_'.'user_id'])) { ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle"></i>
                            <?php echo htmlentities($_SESSION[$OJ_NAME.'_'.'user_id']);
                                  if(!empty($_SESSION[$OJ_NAME.'_nick'])) echo " (".htmlentities($_SESSION[$OJ_NAME.'_nick']).")";
                                  if(!empty($_SESSION[$OJ_NAME.'_group_name'])) echo " [".htmlentities($_SESSION[$OJ_NAME.'_group_name'])."]";
                            ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="modifypage.php"><i class="bi bi-pencil-square"></i> <?php echo $MSG_REG_INFO;?></a></li>
                            <li><a class="dropdown-item" href="portal.php"><i class="bi bi-check2-square"></i> <?php echo $MSG_TODO;?></a></li>
                            <?php if ($OJ_SaaS_ENABLE && $_SERVER['HTTP_HOST']==$DOMAIN){ ?>
                            <li><a class="dropdown-item" href="http://<?php echo htmlentities($_SESSION[$OJ_NAME.'_'.'user_id'])?>.<?php echo $DOMAIN?>"><i class="bi bi-globe"></i> MyOJ</a></li>
                            <?php } ?>
                            <?php if(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'contest_creator'])||isset($_SESSION[$OJ_NAME.'_'.'user_adder'])||isset($_SESSION[$OJ_NAME.'_'.'password_setter'])||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])){ ?>
                            <li><a class="dropdown-item" href="admin/"><i class="bi bi-gear"></i> <?php echo $MSG_ADMIN;?></a></li>
                            <?php } ?>
                            <?php if(isset($_SESSION[$OJ_NAME.'_'.'balloon'])){ ?>
                            <li><a class="dropdown-item" href="balloon.php"><i class="bi bi-dice-5"></i> <?php echo $MSG_BALLOON?></a></li>
                            <?php } ?>
                            <?php
                              if((isset($OJ_EXAM_CONTEST_ID)&&$OJ_EXAM_CONTEST_ID>0)||
                                     (isset($OJ_ON_SITE_CONTEST_ID)&&$OJ_ON_SITE_CONTEST_ID>0)||
                                     (isset($OJ_MAIL)&&!$OJ_MAIL)){
                              }else{
                                    $mail=checkmail();
                                    if($mail) echo "<li><a class='dropdown-item mail' href='".$path_fix."mail.php'><i class='bi bi-envelope'></i> $MSG_MAIL$mail</a></li>";
                              }
                            ?>
                            <?php if(isset($OJ_PRINTER) && $OJ_PRINTER){ ?>
                            <li><a class="dropdown-item" href="printer.php"><i class="bi bi-printer"></i> <?php echo $MSG_PRINTER?></a></li>
                            <?php } ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="bi bi-power"></i> <?php echo $MSG_LOGOUT;?></a></li>
                        </ul>
                    </li>
                    <?php } else { ?>
                    <li class="nav-item">
                        <a class="btn btn-outline-light me-2" href="loginpage.php">
                            <i class="bi bi-box-arrow-in-right"></i> <?php echo $MSG_LOGIN?>
                        </a>
                    </li>
                    <?php if(isset($OJ_REGISTER)&&$OJ_REGISTER ){ ?>
                    <li class="nav-item">
                        <a class="btn btn-primary" href="registerpage.php">
                            <i class="bi bi-person-plus"></i> <?php echo $MSG_REGISTER?>
                        </a>
                    </li>
                    <?php } ?>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div id="main">
<?php } ?>

<?php
$dir=basename(getcwd());
if( $OJ_CDN_URL=="" && ($dir=="discuss3"||$dir=="admin"||$dir=="include")) $path_fix="../";
else $path_fix="";
?>

<?php
$msg_path=realpath(dirname(__FILE__)."/../../admin/msg/$domain.txt");
if(file_exists($msg_path))
	$view_marquee_msg=file_get_contents($OJ_SAE?"saestor://web/msg.txt":$msg_path);
else
	$view_marquee_msg="";
?>

<script>
$(document).ready(function(){
  var msg = "<marquee class='mb-3 text-body-secondary' id=broadcast direction='left' scrollamount=3 scrolldelay=50 onMouseOver='this.stop()' onMouseOut='this.start()'>"+<?php echo json_encode($view_marquee_msg); ?>+"</marquee>";
  <?php if ($view_marquee_msg!="") { ?>
    $("#main").prepend(msg);
  <?php } ?>
  $("form").append("<div id='csrf' />");
  $("#csrf").load("<?php echo $path_fix?>csrf.php");

<?php if(isset($OJ_BG)&&$OJ_BG!="") echo " $('body').css('background','url($OJ_BG)').css('background-repeat','no-repeat').css('background-size','100%'); " ?>
  if(window.location.href.indexOf("rank")==-1){
    $("tr").mouseover(function(){$(this).addClass("table-active")});
    $("tr").mouseout(function(){$(this).removeClass("table-active")})
  }

<?php if(isset($_SESSION[$OJ_NAME."_administrator"]) ||isset($_SESSION[$OJ_NAME."_problem_editor"]) || isset($_SESSION[$OJ_NAME."_tag_adder"])  ){  ?>
  $("div[fd=source]").each(function(){
    let pid=$(this).attr('pid');
    $(this).append("<span><span class='badge bg-success' pid='"+pid+"' onclick='problem_add_source(this,"+pid+");'>+</span></span>");
  });
<?php } ?>
});

function problem_add_source(sp, pid){
  let p=$(sp).parent();
  p.html("<form onsubmit='return false;'><input type='hidden' name='m' value='problem_add_source'><input type='hidden' name='pid' value='"+pid+"'><input type='text' class='form-control' name='ns'></form>");
  p.find("input[name=ns]").focus();
  p.find("input[name=ns]").change(function(){
    let ns=p.find("input[name=ns]").val();
    $.post("admin/ajax.php",p.find("form").serialize());
    p.parent().append("<span class='badge bg-success'>"+ns+"</span>");
    p.html("<span class='badge bg-success' pid='"+pid+"' onclick='problem_add_source(this,"+pid+");'>+</span>");
  });
}

console.log("HUSTOJ Bootstrap 5 Template - See template/bs5 for customization.");
</script>

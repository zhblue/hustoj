<?php 
  require_once("../include/db_info.inc.php");
  require_once("admin-header.php");

  if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'.'contest_creator']) || isset($_SESSION[$OJ_NAME.'_'.'problem_editor']))) {
    echo "<a href='../loginpage.php'>Please Login First!</a>";
    exit(1);
  }

  function writable($path) {
    $ret = false;
    $fp = fopen($path."/testifwritable.tst","w");
    $ret = !($fp===false);

    fclose($fp);
    unlink($path."/testifwritable.tst");
    return $ret;
  }

  $maxfile = min(ini_get("upload_max_filesize"), ini_get("post_max_size"));

  echo "<center><h3>".$MSG_PROBLEM."-".$MSG_IMPORT."</h3></center>";

?>

<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Cache-Control" content="no-cache">
  <meta http-equiv="Content-Language" content="zh-cn">
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Problem Import</title>
</head>
<body leftmargin="30">
  <div class="container">
    <?php 
    $show_form = true;

    if (!isset($OJ_SAE) || !$OJ_SAE) {
      if (!writable($OJ_DATA)) {
        echo "- You need to add  $OJ_DATA into your open_basedir setting of php.ini,<br>
        or you need to execute:<br>
        <b>chmod 775 -R $OJ_DATA && chgrp -R ".get_current_user()." $OJ_DATA</b><br>
        you can't use import function at this time.<br>"; 

        if($OJ_LANG == "cn")
          echo "权限异常，请先去执行sudo chmod 775 -R $OJ_DATA <br> 和 sudo chgrp -R ".get_current_user()." $OJ_DATA <br>";
	  
        $show_form = false;
	if(get_current_user()=="www")
	  echo "如果你是宝塔用户，请关闭宝塔的跨站防护功能，如果你是lnmp或者centos用户，请禁用open_basedir。如果坚持使用，请将/home/jduge/data目录加进去。";
      }
	    

      if (!file_exists("../upload"))
				mkdir("../upload");

      if (!writable("../upload")) {
        echo "../upload is not writable, <b>chmod 770</b> to it.<br>";
        $show_form = false;
      }
    }
    ?>

    <?php if ($show_form) { ?>
    - Import Problem <b>FPS(.xml)/ZIP(.xml inside) 导入xml或zip压缩的xml文件，支持一个或多个，不支持子目录。</b> <br><br>
    <form class='form-inline' action='problem_import_xml.php' method=post enctype="multipart/form-data">
      <div class='form-group'>
        <input class='form-control' type=file name=fps>
        <button class='btn btn-success btn-sm' type=submit>Upload to HUSTOJ</button>
      </div>
      <?php require_once("../include/set_post_key.php");?>
    </form>
	  <hr>
-  QDUOJ - json - zip<br>应该是真的QDUOJ，未严格测试，感谢[温十六中]吴晓阳提供例子文件<br>
    <form class='form-inline' action='problem_import_qduoj.php' method=post enctype="multipart/form-data">
      <div class='form-group'>
        <input class='form-control' type=file name=fps>
        <button class='btn btn-info btn-sm' type=submit>Upload to HUSTOJ</button>
      </div>
      <?php require_once("../include/set_post_key.php");?>
    </form> <hr>
    - unkownOJ - json - zip<br>曾经以为是QDUOJ，但似乎不是，谁知道请告诉我<br>
    <form class='form-inline' action='problem_import_unkownoj.php' method=post enctype="multipart/form-data">
      <div class='form-group'>
        <input class='form-control' type=file name=fps>
        <button class='btn btn-primary btn-sm' type=submit>Upload to HUSTOJ</button>
      </div>
      <?php require_once("../include/set_post_key.php");?>
    </form> <hr>
    - SYZOJ - zip<br><br>
    <form class='form-inline' action='problem_import_syzoj.php' method=post enctype="multipart/form-data">
      <div class='form-group'>
        <input class='form-control' type=file name=fps>
        <button class='btn btn-warning btn-sm' type=submit>Upload to HUSTOJ</button>
      </div>
      <?php require_once("../include/set_post_key.php");?>
    </form> <hr>
    - HydroOJ - zip<br><br>
    <form class='form-inline' action='problem_import_hydro.php' method=post enctype="multipart/form-data">
      <div class='form-group'>
        <input class='form-control' type=file name=fps>
        <button class='btn btn-danger btn-sm' type=submit>Upload to HUSTOJ</button>
      </div>
      <?php require_once("../include/set_post_key.php");?>
    </form> <hr>
    - HOJ - zip<br><br>
    <form class='form-inline' action='problem_import_hoj.php' method=post enctype="multipart/form-data">
      <div class='form-group'>
        <input class='form-control' type=file name=fps>
        <button class='btn btn-info btn-sm' type=submit>Upload to HUSTOJ</button>
      </div>
      <?php require_once("../include/set_post_key.php");?>
    </form>
   - Markdown - zip<br>zip压缩的.md文件，首行为标题<br>
    <form class='form-inline' action='problem_import_md.php' method=post enctype="multipart/form-data">
      <div class='form-group'>
        <input class='form-control' type=file name=fps>
        <button class='btn btn-primary btn-sm' type=submit>Upload to HUSTOJ</button>
      </div>
      <?php require_once("../include/set_post_key.php");?>
    </form>

    <?php } ?>

    <br><br>

    <?php if ($OJ_LANG == "cn") { ?>
    免费题目<a href="https://github.com/zhblue/freeproblemset/tree/master/fps-examples" target="_blank">下载</a><br>
    更多题目请到 <a href="http://tk.hustoj.com/problemset.php?search=free" target="_blank">TK 题库免费专区</a>。
    <?php } ?>

    <br><br>

    - Import FPS data, please make sure you file is smaller than [<?php echo $maxfile?>] or set upload_max_filesize and post_max_size in <span style='color:blue'>php.ini</span><br>
    - If you fail on import big files[10M+],try enlarge your [memory_limit] setting in <span style='color:blue'>php.ini</span><br>
    - To find the php configuration file, use <span style='color:blue'> find /etc -name php.ini </span>

  </div>

</body>
</html>

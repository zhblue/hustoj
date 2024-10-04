<?php
 require_once("admin-header.php");
ini_set("display_errors","On");
require_once("../include/check_get_key.php");
$pid=intval($_GET['id']);
if(!(isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'."p".$pid]) )){
  echo "<a href='../loginpage.php'>Please Login First!</a>";
  exit(1);
}

?> 
<?php
function recursiveDelete($dir) {
    if (is_dir($dir)) {
        $files = scandir($dir);
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                $path = $dir . DIRECTORY_SEPARATOR . $file;
                if (is_dir($path)) {
                    recursiveDelete($path);
                } else {
                    unlink($path);
                }
            }
        }
        rmdir($dir);
    }
}

// 使用示例
  $id=intval($_GET['id']);
  if($id>0&&strlen($OJ_DATA)>8){
        $basedir = "$OJ_DATA/$id";
	if(strlen($basedir)>16&&$id>0){
			//system("rm -rf $basedir");
			recursiveDelete($basedir);
	}
        $sql="delete FROM `problem` WHERE `problem_id`=?";
        pdo_query($sql,$id) ;
	$sql = "delete from `privilege` where `rightstr`=? ";
	pdo_query($sql, "p$id");
	$sql = "update solution set problem_id=0 where `problem_id`=? ";
	pdo_query($sql, $id);
	  
        $sql="select max(problem_id) FROM `problem`" ;
        $result=pdo_query($sql);
        $row=$result[0];
        $max_id=$row[0];
        $max_id++;
        if($max_id<1000)$max_id=1000;
        
        $sql="ALTER TABLE problem AUTO_INCREMENT = $max_id";
        pdo_query($sql);
        ?>
        <script language=javascript>
                history.go(-1);
        </script>
<?php 
  }else{
  
  
  ?>
        <script language=javascript>
                alert("Nees enable system() in php.ini");
                history.go(-1);
        </script>
  <?php 
  
  }

?>

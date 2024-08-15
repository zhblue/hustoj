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
  if($OJ_SAE||function_exists('system')){
        $id=intval($_GET['id']);
        
        $basedir = "$OJ_DATA/$id";
        if($OJ_SAE){
			;//need more code to delete files
	}else{
	    if(strlen($basedir)>16){
			system("rm -rf $basedir");
	    }
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

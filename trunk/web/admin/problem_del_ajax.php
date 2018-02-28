<?php
//ini_set("display_errors","On");
require_once('../include/db_info.inc.php');
//require_once("../include/check_get_key.php");
if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator']))){
        echo "Please Login First!";
        exit(1);
}
?> 
<?php
  if($OJ_SAE||function_exists('system')){
		$ids = explode(',',$_POST['ids']);
		foreach ($ids as $idstr) {
			$id=intval($idstr);
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
			$sql="select max(problem_id) FROM `problem`" ;
			$result=pdo_query($sql);
			$row=$result[0];
			$max_id=$row[0];
			$max_id++;
			if($max_id<1000)$max_id=1000;
			
			$sql="ALTER TABLE problem AUTO_INCREMENT = $max_id";
			pdo_query($sql);
		}
		ob_clean();
		echo 'ok';
  }else{
	echo "Nees enable system() in php.ini";
  }
?>

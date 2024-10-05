<?php
function pdo_query($sql){
    $num_args = func_num_args();
    $args = func_get_args();       //获得传入的所有参数的数组
    $args = array_slice($args,1,--$num_args);
    if(isset($args[0])&&is_array($args[0])) $args=$args[0]; 
    global $DB_HOST,$DB_NAME,$DB_USER,$DB_PASS,$dbh,$OJ_SAE,$OJ_TEMPLATE;
    try{
	    if(!$dbh||stripos($sql,"create") === 0||stripos($sql,"drop") === 0|| stripos($sql,"grant") === 0){
				
			if(isset($OJ_SAE)&&$OJ_SAE)	{
				$OJ_DATA="saestor://data/";
			//  for sae.sina.com.cn
				$DB_NAME=SAE_MYSQL_DB;
				$dbh=new PDO("mysql:host=".SAE_MYSQL_HOST_M.';dbname='.SAE_MYSQL_DB, SAE_MYSQL_USER, SAE_MYSQL_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8mb4"));
			}else{
				
	    			if(stripos($sql,"create") === 0||stripos($sql,"drop") === 0|| stripos($sql,"grant") === 0){
				$dbh=new PDO("mysql:host=".$DB_HOST, $DB_USER, $DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8mb4;"));
//					echo "General SQL";
				$sql="use $DB_NAME; ".$sql;
				}else{
				$dbh=new PDO("mysql:host=".$DB_HOST.';dbname='.$DB_NAME, $DB_USER, $DB_PASS,array(PDO::ATTR_PERSISTENT=>true,PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8mb4"));
//					echo "$DB_NAME SQL";
				}
			}
			
	    }
	    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    $sth = $dbh->prepare($sql);
	    $sth->execute($args);
	    $result=array();
	    if(stripos($sql,"select") === 0){
		$result=$sth->fetchAll();
	    }else if(stripos($sql,"insert") === 0){
		$result=$dbh->lastInsertId();
	    }else{
		$result=$sth->rowCount();
	    }
	    //print($sql);
	    $sth->closeCursor();
	    return $result;
    }catch(PDOException $e){
//	    echo "<span class=red>".$e->getMessage()."</span>";    // open this line to debug SQL fail problems 
//	$view_errors="SQL:".$sql."\n".$e->getMessage();
//	echo htmlentities($view_errors."\n\n");
	GLOBAL $MSG_UPDATE_DATABASE,$MSG_HELP_UPDATE_DATABASE;
        GLOBAL $POP_UPED,$OJ_NAME,$_SESSION;
        if(!$POP_UPED&&isset($_SESSION[$OJ_NAME.'_administrator'])){
                echo " $MSG_HELP_UPDATE_DATABASE <a href='/admin/update_db.php'>$MSG_UPDATE_DATABASE</a>。";
		$view_errors="SQL:".$sql."\n".$e->getMessage();
		echo htmlentities($view_errors."\n\n");
                $POP_UPED=true;
        }

	if(stripos($sql,"create") === 0||stripos($sql,"drop") === 0) echo "continue\n";
	//else exit(0);
	return -1;
    }
}

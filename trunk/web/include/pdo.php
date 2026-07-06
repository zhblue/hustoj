<?php
function rdo_query($sql){
    $query_start_time=microtime(true);
    $num_args = func_num_args();
    $args = func_get_args();       //获得传入的所有参数的数组
    $args = array_slice($args,1,--$num_args);
    if(isset($args[0])&&is_array($args[0])) $args=$args[0]; 
    global $DB_RO_HOST,$DB_RO_NAME,$DB_RO_USER,$DB_RO_PASS,$db_ro_h,$OJ_TEMPLATE;
    $DB_RO_HOST=$DB_RO_HOST??$DB_HOST; 
    $DB_RO_NAME=$DB_RO_NAME??$DB_NAME;
    $DB_RO_USER=$DB_RO_USER??$DB_USER;
    $DB_RO_PASS=$DB_RO_PASS??$DB_PASS;
    try{
	    if(!$db_ro_h||stripos($sql,"create") === 0||stripos($sql,"drop") === 0|| stripos($sql,"grant") === 0){
				
				
	    		if(stripos($sql,"create") === 0||stripos($sql,"drop") === 0|| stripos($sql,"grant") === 0){
					$db_ro_h=new PDO("mysql:host=".$DB_RO_HOST, $DB_RO_USER, $DB_RO_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8mb4;"));
	//					echo "General SQL";
					$sql="use $DB_NAME; ".$sql;
			}else{
				$db_ro_h=new PDO("mysql:host=".$DB_RO_HOST.';dbname='.$DB_RO_NAME, $DB_RO_USER, $DB_RO_PASS,array(PDO::ATTR_PERSISTENT=>true,PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8mb4",PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC));
//					echo "$DB_NAME SQL";
			}
			
			
	    }
	    $db_ro_h->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
	    $sth = $db_ro_h->prepare($sql);
	    $sth->execute($args);
	    $result=array();
	    if(stripos($sql,"select") === 0){
		$result=$sth->fetchAll();
	    }else if(stripos($sql,"insert") === 0){
		$result=$db_ro_h->lastInsertId();
	    }else{
		$result=$sth->rowCount();
	    }
	    //print($sql);
	    $sth->closeCursor();
        $query_used_time=microtime(true)-$query_start_time;
        if($query_used_time>3) {
            global $logger;
            $logger->warn("slow SQL of [$query_used_time] sec : $sql \n
                    in page [".$_SERVER['REQUEST_URI']."]");
        }
	    return $result;
    }catch(PDOException $e){
//	    echo "<span class=red>".$e->getMessage()."</span>";    // open this line to debug SQL fail problems 
//	$view_errors="SQL:".$sql."\n".$e->getMessage();
//	echo htmlentities($view_errors."\n\n");
	GLOBAL $MSG_UPDATE_DATABASE,$MSG_HELP_UPDATE_DATABASE;
        GLOBAL $POP_UPED,$OJ_NAME,$_SESSION;
        if(php_sapi_name ()=='cli'||(!$POP_UPED&&isset($_SESSION[$OJ_NAME.'_administrator']))){
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
function pdo_query($sql){
    $query_start_time=microtime(true);
    $num_args = func_num_args();
    $args = func_get_args();       //获得传入的所有参数的数组
    $args = array_slice($args,1,--$num_args);
    if(isset($args[0])&&is_array($args[0])) $args=$args[0]; 
    global $DB_HOST,$DB_NAME,$DB_USER,$DB_PASS,$dbh,$OJ_TEMPLATE;
    try{
	    if(!$dbh||stripos($sql,"create") === 0||stripos($sql,"drop") === 0|| stripos($sql,"grant") === 0){
				
				
	    		if(stripos($sql,"create") === 0||stripos($sql,"drop") === 0|| stripos($sql,"grant") === 0){
					$dbh=new PDO("mysql:host=".$DB_HOST, $DB_USER, $DB_PASS,array(PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8mb4;"));
	//					echo "General SQL";
					$sql="use $DB_NAME; ".$sql;
				}else{
					$dbh=new PDO("mysql:host=".$DB_HOST.';dbname='.$DB_NAME, $DB_USER, $DB_PASS,array(PDO::ATTR_PERSISTENT=>true,PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8mb4"));
//					echo "$DB_NAME SQL";
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
        $query_used_time=microtime(true)-$query_start_time;
        if($query_used_time>3) {
            global $logger;
            $logger->warn("slow SQL of [$query_used_time] sec : $sql \n
                    in page [".$_SERVER['REQUEST_URI']."]");
        }
	    return $result;
    }catch(PDOException $e){
//	    echo "<span class=red>".$e->getMessage()."</span>";    // open this line to debug SQL fail problems 
//	$view_errors="SQL:".$sql."\n".$e->getMessage();
//	echo htmlentities($view_errors."\n\n");
	GLOBAL $MSG_UPDATE_DATABASE,$MSG_HELP_UPDATE_DATABASE;
        GLOBAL $POP_UPED,$OJ_NAME,$_SESSION;
        if(php_sapi_name ()=='cli'||(!$POP_UPED&&isset($_SESSION[$OJ_NAME.'_administrator']))){
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

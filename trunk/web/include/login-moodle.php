<?php 
	require_once("./include/my_func.inc.php");
    
	function check_login($user_id,$password){
		session_destroy();
		session_start();
		$moodle_host="127.0.0.1";
		$moodle_port="3306";
		$moodle_user="root";
		$moodle_db="moodle";
		$moodle_pass="";
		//$moodle_conn=mysql_connect($moodle_host.":".$moodle_port,$moodle_user,$moodle_pass);
		$moodle_dbh=new PDO("mysql:host=".$moodle_host.';dbname='.$moodle_db, $moodle_user, $moodle_pass,array(PDO::ATTR_PERSISTENT=>true,PDO::MYSQL_ATTR_INIT_COMMAND => "set names utf8mb4"));
		$moodle_dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		        		
		$moodle_salt= '-Y9-h0;),c@<i)D~*i/j7.pD6lh/,B';
		$password=md5($password.$moodle_salt);
		$ret=false;
		$moodle_pre="mdl_";
		$sql="select password from ".$moodle_db.".".$moodle_pre."user where username=?";
		if($moodle_dbh){
			$sth = $moodle_dbh->prepare($sql);
			$args=array();
			$args[0]=$user_id;
		        $sth->execute($args);
	                $result=$sth->fetchAll();
			// $result=pdo_query($sql,$user_id);
			$row=$result[0];
			if($row&&$password==$row[0]){
				$ret=$user_id;
			}
			$sth->closeCursor();
		}
		
		return $ret; 
	}
?>

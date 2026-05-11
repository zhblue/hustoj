<?php 
	require_once("./include/my_func.inc.php");
    
	function check_login($user_id,$password){
		session_destroy();
		session_start();
		$ldap_host="ldap://127.0.0.1";
		$ldap_port="389";
		$ldap_conn=ldap_connect($ldap_host,$ldap_port);
		ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
   	 	ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 0);
		$dn="uid=$user_id,ou=people,dc=example,dc=com";
		$ret=false;
		if($ldap_conn){
			$login=ldap_bind($ldap_conn,$dn,$password);
			if($login){
				$ret=$user_id;	
			}
		}
		ldap_unbind($ldap_conn);
		return $ret; 
	}
?>

<?php
require_once( "./include/db_info.inc.php" );
require_once( './include/setlang.php' );

// 当前user_id
$user_id = $_SESSION[ $OJ_NAME . '_' . 'user_id' ];

if($user_id){
	// 已登录的
	$sql = "SELECT * FROM `privilege` WHERE `user_id`=? or `user_id`=?";
	$result = pdo_query( $sql, $user_id, $_SESSION[ $OJ_NAME . '_group_name']);
	
	// 刷新各种权限
	foreach ( $result as $row ){
		if(isset($row[ 'valuestr' ])){
			$_SESSION[ $OJ_NAME . '_' . $row[ 'rightstr' ] ] = $row[ 'valuestr' ];
		}else {
			$_SESSION[ $OJ_NAME . '_' . $row[ 'rightstr' ] ] = true;
		}   
	}
    if(isset($_SESSION[ $OJ_NAME . '_vip' ])) {  // VIP mark can access all [VIP] marked contest
		$sql="select contest_id from contest where title like '%[VIP]%'";
		$result=pdo_query($sql);
		foreach ($result as $row){
			$_SESSION[ $OJ_NAME . '_c' .$row['contest_id'] ] = true;
		}
	};
	?>
	<script>console.log("Privilege refreshed !!");</script>
	<?php
}else {
	// 没登录的
	?>
	<script>
		alert("<?php echo $MSG_Login; ?>");
		window.location.href="./loginpage.php";
	</script>
	<?php
}


<?php require("admin-header.php");
if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'.'contest_creator']))) {
	exit(1);
}?>
<?php if(isset($_POST['prefix'])){
	require_once("../include/check_post_key.php");
	$prefix=$_POST['prefix'];
	require_once("../include/my_func.inc.php");
	if (!is_valid_user_name($prefix)){
		echo "Prefix is not valid.";
		exit(0);
	}
	$teamnumber=intval($_POST['teamnumber']);
        $pieces = explode("\n", trim($_POST['ulist']));
	
	if ($teamnumber>0){
		echo "<table border=1>";
		echo "<tr><td colspan=3>Copy these accounts to distribute</td></tr>";
		echo "<tr><td>team_name<td>login_id</td><td>password</td></tr>";
		$max_length=20;
		for($i=1;$i<=$teamnumber;$i++){
			
        $user_id=$prefix.($i<10?('0'.$i):$i);
			$password=strtoupper(substr(MD5($user_id.rand(0,9999999)),0,10));
                        if(isset($pieces[$i-1]))
                        	$nick=$pieces[$i-1];
                        else
				$nick="your_own_nick";
			if($teamnumber==1) $user_id=$prefix;

			echo "<tr><td>$nick<td>$user_id</td><td>$password</td></tr>";
			
			$password=pwGen($password);
			$email="your_own_email@internet";
                       
			$school="your_own_school";
			$ip = ($_SERVER['REMOTE_ADDR']);
			if( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ){
			    $REMOTE_ADDR = $_SERVER['HTTP_X_FORWARDED_FOR'];
			    $tmp_ip=explode(',',$REMOTE_ADDR);
			    $ip =(htmlentities($tmp_ip[0],ENT_QUOTES,"UTF-8"));
			}
			
			if(mb_strlen($nick,'utf-8')>20){
				$new_len=mb_strlen($nick,'utf-8');
				if($new_len>$max_length){
					$max_length=$new_len;
					$longer="ALTER TABLE `users` MODIFY COLUMN `nick` varchar($max_length) NULL DEFAULT '' ";
					pdo_query($longer);
				}
			}
			if( $user_id == "admin" ) continue;
			$sql="INSERT INTO `users`("."`user_id`,`email`,`ip`,`accesstime`,`password`,`reg_time`,`nick`,`school`)".
			"VALUES(?,?,?,NOW(),?,NOW(),?,?)on DUPLICATE KEY UPDATE `email`=?,`ip`=?,`accesstime`=NOW(),`password`=?,`reg_time`=now(),nick=?,`school`=?";
			pdo_query($sql,$user_id,$email,$ip,$password,$nick,$school,$email,$ip,$password,$nick,$school) ;
		}
		echo  "</table>";
		
		
	}
	
}
?>
<div class="container">
<b>TeamGenerator:</b>
	
	<form action='team_generate.php' method=post>
	    Prefix:<input type='test' name='prefix' value='team' placeholder="Team"><br>
		Generate<input class="input-mini" type=input name='teamnumber' value=5 size=3 >Teams.<br>
                Users:<textarea name="ulist" rows="12" cols="40" placeholder="Preset nicknames of the teams. One name per line."></textarea>
		<?php require_once("../include/set_post_key.php");?>
		<input type=submit value=Generate><br>
	</form>
	<span style="color:red;font-weight:bold">以相同的前缀生成账号，将覆盖昵称和密码，请谨慎操作！</span>

</div>

<?php require("admin-header.php");
if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'.'contest_creator']))) {
	exit(1);
}?>
<?php

if(isset($_POST['idlist']) && isset($_POST['pwdlist']) && isset($_POST['nicklist']))
{
	require_once("../include/check_post_key.php");
	require_once("../include/my_func.inc.php");
	
    $idPieces = explode("\n", trim($_POST['idlist']));
    $pwdPieces = explode("\n", trim($_POST['pwdlist']));
	$nickPieces = explode("\n", trim($_POST['nicklist']));

	$teamnumber=count($idPieces);

	if($teamnumber==count($pwdPieces) && $teamnumber==count($nickPieces))
	{
		$ok=true;
		for($i=0;$i<$teamnumber;$i++)
		{
			$idPieces[$i]=trim($idPieces[$i]);
			$pwdPieces[$i]=trim($pwdPieces[$i]);
			$nickPieces[$i]=trim($nickPieces[$i]);

			if(strlen($idPieces[$i])==0)
			{
				echo "<h3>one empty line existed in user id field. all of the users is not inserted.</h3>";
				$ok=false;
			}
			if(strlen($pwdPieces[$i])==0)
			{
				echo "<h3>one empty line existed in password field. all of the users is not inserted.</h3>";
				$ok=false;
			}
			if(strlen($nickPieces[$i])==0)
			{
				echo "<h3>one empty line existed in nickname field. all of the users is not inserted.</h3>";
				$ok=false;
			}
			if (!is_valid_user_name($idPieces[$i])){
				echo "<h3>user id \"".$idPieces[$i]."\" is not valid. all of the users is not inserted.</h3>";
				$ok=false;				
			}
		}

		if($ok)
		{
			echo "<table border=1>";
			echo "<tr><td colspan=3>Copy these accounts to distribute</td></tr>";
			echo "<tr><td>team_name<td>login_id</td><td>password</td></tr>";

			$max_length=20;
	
			for($i=0;$i<$teamnumber;$i++){
				$user_id=$idPieces[$i];
				$nick=$nickPieces[$i];

				echo "<tr><td>$nick<td>$user_id</td><td>$pwdPieces[$i]</td></tr>";

				$password=pwGen($pwdPieces[$i]);

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
		}

		echo  "</table><br/>";
		
	}else{
		echo "<h3>sorry,the sum of user id is not equal to the sum of Password or Nickname</h3>";
	}
	
	
}

?>
<div class="container">
<b>TeamGenerator2:</b>
<p>the account will be replaced if Login ID exists.</p>
	<form action='team_generate2.php' method=post>
		<div class="row">
			<div class="col-md-4">
			Login ID:<textarea name="idlist" rows="12" cols="40" placeholder="Preset ID of the teams. One ID per line."></textarea>
			</div>
			<div class="col-md-4">
			Password:<textarea name="pwdlist" rows="12" cols="40" placeholder="Preset password of the teams. One password per line."></textarea>
			</div>
			<div class="col-md-4">
			Nickname:<textarea name="nicklist" rows="12" cols="40" placeholder="Preset nicknames of the teams. One name per line."></textarea>
			</div>
		</div>
		<div class="row">
		<br/>
		<?php require_once("../include/set_post_key.php");?>
		<input type=submit value=Generate><br>
		</div>
	</form>

</div>

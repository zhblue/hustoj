<?php 
require_once("../include/db_info.inc.php");
require_once("../include/my_func.inc.php");
if (!(isset($_SESSION[$OJ_NAME.'_'.'http_judge']))){
	echo "0";
	exit(1);
}
header('Content-Type: text/plain');

if(isset($_POST['manual'])){

        $sid=intval($_POST['sid']);
        $result=intval($_POST['result']);
        if($result>=0){
          if($result==4)
                $sql="UPDATE solution SET result=?,pass_rate=1.0 WHERE solution_id=? LIMIT 1";
          else
                $sql="UPDATE solution SET result=?,pass_rate=0 WHERE solution_id=? LIMIT 1";
          pdo_query($sql,$result,$sid);
        }
        if(isset($_POST['explain'])){
             $sql="DELETE FROM runtimeinfo WHERE solution_id=? ";
             pdo_query($sql,$sid);
             $reinfo=$_POST['explain'];
             if (false) {
                 $reinfo= stripslashes ( $reinfo);
             }
             $sql="INSERT INTO runtimeinfo VALUES(?,?)";
             pdo_query($sql,$sid,$reinfo);
        }
	if (isset($OJ_UDP) && $OJ_UDP) {
           trigger_judge($sid);
        }
	echo "<script>history.go(-1);</script>";
}

if(isset($_POST['update_solution'])){
	//require_once("../include/check_post_key.php");
	$sid=intval($_POST['sid']);
	$result=intval($_POST['result']);
	$time=intval($_POST['time']);
	$memory=intval($_POST['memory']);
	$sim=intval($_POST['sim']);
	$simid=intval($_POST['simid']);
	$pass_rate=floatval($_POST['pass_rate']);
    $judger=$_SESSION[$OJ_NAME.'_'.'user_id'];
	$sql="UPDATE solution SET result=?,time=?,memory=?,judgetime=NOW(),pass_rate=?,judger=? WHERE solution_id=? LIMIT 1";
	//echo $sql;
	pdo_query($sql,$result,$time,$memory,$pass_rate,$judger,$sid);
	
    if ($sim) {
		$sql="insert into sim(s_id,sim_s_id,sim) values(?,?,?) on duplicate key update  sim_s_id=?,sim=?";
		pdo_query($sql,$sid,$simid,$sim,$simid,$sim);
	}
	
}else if(isset($_POST['checkout'])){
	
	$sid=intval($_POST['sid']);
	$result=intval($_POST['result']);
	$sql="update solution SET result=?,time=0,memory=0,judgetime=NOW() WHERE solution_id=? and (result<2 or (result<4 and NOW()-judgetime>60)) LIMIT 1";
	$rows=pdo_query($sql,$result,$sid);
	if($rows>0)
		echo "1";
	else
		echo "0";
}else if(isset($_POST['getpending'])){
        $max_running=intval($_POST['max_running']);
        if($OJ_REDIS && !isset($_SESSION[$OJ_NAME.'_'.'problem_start'])){
           $redis = new Redis();
           $redis->connect($OJ_REDISSERVER, $OJ_REDISPORT);
	   if(isset($OJ_REDISAUTH)) $redis->auth($OJ_REDISAUTH);
		
           for(;$max_running>0;$max_running--){
                $sid=$redis->rpop($OJ_REDISQNAME);
                if($sid>0){
                        echo $sid."\n";
                }else{
                        break;
                }

           }
	   $redis->close();     
        }else{

                $oj_lang_set=$_POST['oj_lang_set'];
				$langs=explode(",",$oj_lang_set);
				$oj_lang_set="";
				foreach($langs as $lang){
					$lang=intval($lang);
					if($oj_lang_set!="") $oj_lang_set.=",";
					$oj_lang_set.=$lang;
				}
				
                $sql="SELECT solution_id FROM solution WHERE language in ($oj_lang_set)
                        and (result<2 or (result<4 and NOW()-judgetime>60)) ";
		if(isset($_SESSION[$OJ_NAME.'_'.'problem_start'])){
			$start=intval($_SESSION[$OJ_NAME.'_'.'problem_start']);
			$sql.="and problem_id>=$start ";
		}
		if(isset($_SESSION[$OJ_NAME.'_'.'problem_end'])){
			$end=intval($_SESSION[$OJ_NAME.'_'.'problem_end']);
			$sql.="and problem_id<=$end ";
		}
		
		$sql.=" ORDER BY result ASC,solution_id ASC limit $max_running";
                $result=pdo_query($sql);
                foreach($result as $row){
                        echo $row['solution_id']."\n";
                }
                
        }

}else if(isset($_POST['getsolutioninfo'])){
	
	$sid=intval($_POST['sid']);
	$sql="select problem_id, user_id, language,contest_id from solution WHERE solution_id=?";
	$result=pdo_query($sql,$sid);
	if ( $row=$result[0]){
		echo $row['problem_id']."\n";
		echo $row['user_id']."\n";
		echo $row['language']."\n";
		echo intval($row['contest_id'])."\n";
		
	}
	
	
}else if(isset($_POST['getsolution'])){
	
	$sid=intval($_POST['sid']);
	$sql="SELECT source FROM source_code WHERE solution_id=? ";
	$result=pdo_query($sql,$sid);
	if ( $row=$result[0]){
		echo $row['source']."\n";
	}
	
	
}else if(isset($_POST['getcustominput'])){
	
	$sid=intval($_POST['sid']);
	$sql="SELECT input_text FROM custominput WHERE solution_id=? ";
	$result=pdo_query($sql,$sid);
	if ( $row=$result[0]){
		echo $row['input_text']."\n";
	}
	
	
}else if(isset($_POST['getprobleminfo'])){
	
	$pid=intval($_POST['pid']);
	$sql="SELECT time_limit,memory_limit,spj FROM problem where problem_id=?";
	$result=pdo_query($sql,$pid );
	if ( $row=$result[0]){
		echo $row['time_limit']."\n";
		echo $row['memory_limit']."\n";
		echo $row['spj']."\n";
		
	}
	
	
}else if(isset($_POST['addceinfo'])){
	
	$sid=intval($_POST['sid']);
	$sql="DELETE FROM compileinfo WHERE solution_id=? ";
	pdo_query($sql,$sid);
	$ceinfo=$_POST['ceinfo'];
	$sql="INSERT INTO compileinfo VALUES(?,?)";
	pdo_query($sql,$sid,$ceinfo);
	
}else if(isset($_POST['addreinfo'])){
	
	$sid=intval($_POST['sid']);
	$sql="DELETE FROM runtimeinfo WHERE solution_id=? ";
	pdo_query($sql,$sid);
	$reinfo=$_POST['reinfo'];
	$sql="INSERT INTO runtimeinfo VALUES(?,?)";
	pdo_query($sql,$sid,$reinfo);
	
}else if(isset($_POST['updateuser'])){
	
  	$user_id=$_POST['user_id'];
	$sql="UPDATE `users` SET `solved`=(SELECT count(DISTINCT `problem_id`) FROM `solution` WHERE `user_id`=? AND `result`=4) WHERE `user_id`=?";
	pdo_query($sql,$user_id,$user_id);
  //  echo $sql;
	
	$sql="UPDATE `users` SET `submit`=(SELECT count(*) FROM `solution` WHERE `user_id`=? and problem_id>0) WHERE `user_id`=?";
	pdo_query($sql,$user_id,$user_id);
  //	echo $sql;
	
}else if(isset($_POST['updateproblem'])){
	$cid=intval($_POST['cid']);
	$pid=intval($_POST['pid']);
	if($cid>0){
		$sql="UPDATE `contest_problem` SET `c_accepted`=(SELECT count(1) FROM `solution` WHERE `problem_id`=? and contest_id=? AND `result`=4) WHERE `problem_id`=? and contest_id=?";
		pdo_query($sql,$pid,$cid,$pid,$cid);
	//	$sql="UPDATE `contest_problem` SET `c_submit`=(SELECT count(1) FROM `solution` WHERE `problem_id`=? and contest_id=?) WHERE `problem_id`=? and contest_id=?";
	//	pdo_query($sql,$pid,$cid,$pid,$cid);
	}else{
	//	$sql="UPDATE `problem` SET `submit`=(SELECT count(1) FROM `solution` WHERE `problem_id`=?) WHERE `problem_id`=?";
	//echo $sql;
	//	pdo_query($sql,$pid,$pid);
		$sql="UPDATE `problem` SET `accepted`=(SELECT count(1) FROM `solution` WHERE `problem_id`=? AND `result`=4) WHERE `problem_id`=?";
	//echo $sql;
		pdo_query($sql,$pid,$pid);
	}

	
}else if(isset($_POST['checklogin'])){
	echo "1";
}else if(isset($_POST['gettestdatalist'])){


	$pid=intval($_POST['pid']);
      
  	if($OJ_SAE){
          //echo $OJ_DATA."$pid";
         
           $store = new SaeStorage();
           $ret = $store->getList("data", "$pid" ,100,0);
            foreach($ret as $file) {
              if(!strstr($file,"sae-dir-tag")){
                     $file=pathinfo($file);
                     $file=$file['basename'];
                    		 echo $file."\n";   
              }
                    
            }


        } else{
        
            $dir=opendir($OJ_DATA."/$pid");
            while (($file = readdir($dir)) != ""){
              if(!is_dir($file)&&$file!="ac"){
		    if(isset($_POST['time'])){
                        echo filemtime($OJ_DATA."/$pid/".$file)."\n";
                    }

               	    $file=pathinfo($file);
                    $file=$file['basename'];
                    echo "$file\n";
              }
            }
            closedir($dir);
        }
        
	
}else if(isset($_POST['gettestdata'])){
	$file=$_POST['filename'];
        if($OJ_SAE){ 
		$store = new SaeStorage();
                if($store->fileExists("data",$file)){
                       
                		echo $store->read("data",$file);
                }
                
        }else{
          	echo file_get_contents($OJ_DATA.'/'.$file);
        }
           
}else{
?>

<form action='problem_judge.php' method=post>
	<b>HTTP Judge:</b><br />
	sid:<input type=text size=10 name="sid" value=1244><br />
	pid:<input type=text size=10 name="pid" value=1000><br />
	result:<input type=text size=10 name="result" value=4><br />
	time:<input type=text size=10 name="time" value=500><br />
	memory:<input type=text size=10 name="memory" value=1024><br />
	sim:<input type=text size=10 name="sim" value=100><br />
	simid:<input type=text size=10 name="simid" value=0><br />
  	gettestdata:<input type=text size=10 name="filename" value="1000/test.in"><br />
	
        <input type='hidden' name='gettestdatalist' value='do'>
	<input type=submit value='Judge'>
</form>
<?php 
}
?>

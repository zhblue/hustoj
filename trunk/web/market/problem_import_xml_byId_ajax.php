<?php 
//require_once("../include/check_post_key.php");
require_once (dirname(__FILE__)."/../include/db_info.inc.php");
if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator']))){
	echo "Please Login First!";
	exit(1);
}
if(isset($OJ_LANG)){
		require_once(dirname(__FILE__)."/../lang/$OJ_LANG.php");
	}	
	require_once (dirname(__FILE__)."/../include/const.inc.php");
?>
<?php function image_save_file($filepath ,$base64_encoded_img){
	$fp=fopen($filepath ,"wb");
	fwrite($fp,base64_decode($base64_encoded_img));
	fclose($fp);
}
require_once (dirname(__FILE__)."/../include/problem.php");
require_once (dirname(__FILE__)."/../include/db_info.inc.php");
function getLang($language){
	$language_name=$GLOBALS['language_name'];
	
	for($i=0;$i<count($language_name);$i++){
		//echo "$language=$language_name[$i]=".($language==$language_name[$i]);
		if($language==$language_name[$i]){
			//$language=$i;
			//echo $language;
			return $i;
		}
	}
	return $i;
}
function submitSolution($pid,$solution,$language)
{
	global $OJ_NAME;
	$language=getLang($language);
	$len=mb_strlen($solution,'utf-8');
	$sql="INSERT INTO solution(problem_id,user_id,in_date,language,ip,code_length,result)
						VALUES(?,?,NOW(),?,'127.0.0.1',?,14)";
	$insert_id = pdo_query( $sql,$pid,$_SESSION[$OJ_NAME.'_'.'user_id'],$language,$len );
	//echo "submiting$language.....";
	$sql = "INSERT INTO `source_code`(`solution_id`,`source`)VALUES(?,?)";
	pdo_query( $sql ,$insert_id,$solution);
	$sql = "INSERT INTO `source_code_user`(`solution_id`,`source`)VALUES(?,?)";
	pdo_query( $sql,$insert_id,$solution );
    pdo_query("update solution set result=0 where solution_id=?",$insert_id);
}
?>


<?php
function getValue($Node, $TagName) {
	
	return $Node->$TagName;
}
function getAttribute($Node, $TagName,$attribute) {
	return $Node->children()->$TagName->attributes()->$attribute;
}
function hasProblem($title){
	
	$md5=md5($title);
	$sql="select 1 from problem where md5(title)=?";  
	$result=pdo_query( $sql,$md5 );
	$rows_cnt=count($result);		
	
	//echo "row->$rows_cnt";			
	return  ($rows_cnt>0);

}
function mkpta($pid,$prepends,$node){
	$language_ext=$GLOBALS['language_ext'];
        $OJ_DATA=$GLOBALS['OJ_DATA'];	
	foreach($prepends as $prepend) {
		$language =$prepend->attributes()->language;
		$lang=getLang($language);
		$file_ext=$language_ext[$lang];
		$basedir = "$OJ_DATA/$pid";
		$file_name="$basedir/$node.$file_ext";
		file_put_contents($file_name,$prepend);
	}
}
function get_extension($file){
	$info = pathinfo($file);
	return $info['extension'];
}
function import_fps($tempfile){
	global $OJ_DATA,$OJ_SAE,$OJ_REDIS,$OJ_REDISSERVER,$OJ_REDISPORT,$OJ_REDISQNAME;
	$xmlDoc=simplexml_load_string($tempfile, 'SimpleXMLElement', LIBXML_PARSEHUGE);
	$searchNodes = $xmlDoc->xpath ( "/fps/item" );
	$spid=0;
	foreach($searchNodes as $searchNode) {
		//echo $searchNode->title,"\n";

		$title =$searchNode->title;
		
		$time_limit = $searchNode->time_limit;
    	$unit=getAttribute($searchNode,'time_limit','unit');
    	//echo $unit;
		if($unit=='ms') $time_limit/=1000;
		
		$memory_limit = getValue ( $searchNode, 'memory_limit' );
		$unit=getAttribute($searchNode,'memory_limit','unit');
		if($unit=='kb') $memory_limit/=1024;
		
		$description = getValue ( $searchNode, 'description' );
		$input = getValue ( $searchNode, 'input' );
		$output = getValue ( $searchNode, 'output' );
		$sample_input = getValue ( $searchNode, 'sample_input' );
		$sample_output = getValue ( $searchNode, 'sample_output' );
//		$test_input = getValue ( $searchNode, 'test_input' );
//		$test_output = getValue ( $searchNode, 'test_output' );
		$hint = getValue ( $searchNode, 'hint' );
		$source = getValue ( $searchNode, 'source' );
		
		
		$spjcode = getValue ( $searchNode, 'spj' );
		$spj = trim($spjcode)?1:0;
		if(!hasProblem($title )){
			$pid=addproblem ( $title, $time_limit, $memory_limit, $description, $input, $output, $sample_input, $sample_output, $hint, $source, $spj, $OJ_DATA );
			if($spid==0) $spid=$pid;
			$basedir = "$OJ_DATA/$pid";
			mkdir ( $basedir );
			if(strlen($sample_input)) mkdata($pid,"sample.in",$sample_input,$OJ_DATA);
			if(strlen($sample_output)) mkdata($pid,"sample.out",$sample_output,$OJ_DATA);
        //  	if(!isset($OJ_SAE)||!$OJ_SAE){
				$testinputs=$searchNode->children()->test_input;
				$testno=0;
			
				foreach($testinputs as $testNode){
					//if($testNode->nodeValue)
					mkdata($pid,"test".$testno++.".in",$testNode,$OJ_DATA);
				}
				unset($testinputs);
				$testinputs=$searchNode->children()->test_output;
				$testno=0;
				foreach($testinputs as $testNode){
					//if($testNode->nodeValue)
					mkdata($pid,"test".$testno++.".out",$testNode,$OJ_DATA);
				}
				unset($testinputs);
       // }
			$images=($searchNode->children()->img);
			$did=array();
			$testno=0;
			foreach($images as $img){
			//	
				$src=getValue($img,"src");
				if(!in_array($src,$did)){
						$base64=getValue($img,"base64");
						$ext=pathinfo($src);
						$ext=strtolower($ext['extension']);
						if(!stristr(",jpeg,jpg,png,gif,bmp",$ext)){
							$ext="bad";
							exit(1);
						}
						$testno++;
						$newpath="../upload/pimg".$pid."_".$testno.".".$ext;
						if($OJ_SAE) $newpath="saestor://web/upload/pimg".$pid."_".$testno.".".$ext;
						 
						image_save_file($newpath,$base64);
						$newpath=dirname($_SERVER['REQUEST_URI'] )."/../upload/pimg".$pid."_".$testno.".".$ext;
						if($OJ_SAE) $newpath=$SAE_STORAGE_ROOT."upload/pimg".$pid."_".$testno.".".$ext;
						
						$sql="update problem set description=replace(description,?,?) where problem_id=?";  
						pdo_query( $sql,$src,$newpath,$pid);
						$sql="update problem set input=replace(input,?,?) where problem_id=?";  
						pdo_query( $sql,$src,$newpath,$pid);
						$sql="update problem set output=replace(output,?,?) where problem_id=?";  
						pdo_query( $sql,$src,$newpath,$pid);
						$sql="update problem set hint=replace(hint,?,?) where problem_id=?";  
						pdo_query( $sql,$src,$newpath,$pid);
						array_push($did,$src);
				}
				
			}
			
			if(!isset($OJ_SAE)||!$OJ_SAE){
				if($spj) {
					$basedir = "$OJ_DATA/$pid";
					$fp=fopen("$basedir/spj.cc","w");
					fputs($fp, $spjcode);
					fclose($fp);
					////system( " g++ -o $basedir/spj $basedir/spj.cc  ");
					if(!file_exists("$basedir/spj") ){
						$fp=fopen("$basedir/spj.c","w");
						fputs($fp, $spjcode);
						fclose($fp);
						////system( " gcc -o $basedir/spj $basedir/spj.c  ");
						if(!file_exists("$basedir/spj")){
							echo "you need to compile $basedir/spj.cc for spj[  g++ -o $basedir/spj $basedir/spj.cc   ]<br> and rejudge $pid";
						
						}else{
							
							unlink("$basedir/spj.cc");
						}
					
					
					}
				}
			}
			
			$solutions = $searchNode->children()->solution;
			foreach($solutions as $solution) {
				$language =$solution->attributes()->language;
				submitSolution($pid,$solution,$language);
			}
			unset($solutions);
			$prepends = $searchNode->children()->prepend;
			mkpta($pid,$prepends,"prepend");
			$prepends = $searchNode->children()->template;
			mkpta($pid,$prepends,"template");
			$prepends = $searchNode->children()->append;
			mkpta($pid,$prepends,"append");
			
			
		}else{
			echo "$title is already in this OJ";		
		}
		
	}
	// unlink ( $tempfile );
	// if(isset($OJ_REDIS)&&$OJ_REDIS){
           // $redis = new Redis();
           // $redis->connect($OJ_REDISSERVER, $OJ_REDISPORT);
                // $sql="select solution_id from solution where result=0 and problem_id>0";
                 // $result=pdo_query($sql);
                 // foreach($result as $row){
                        // echo $row['solution_id']."\n";
                        // $redis->lpush($OJ_REDISQNAME,$row['solution_id']);
                // }
                
        // }

	if($spid>0){
	//	require_once("../include/set_get_key.php");
	//	echo "<br><a class=blue href=contest_add.php?spid=$spid&getkey=".$_SESSION[$OJ_NAME.'_'.'getkey'].">Use these problems to create a contest.</a>";
	 }
}
require_once(dirname(__FILE__)."/../market/market.inc.php");
require_once(dirname(__FILE__)."/../include/simple_html_dom.php");
$problem_id=$_POST ['problem_id'];
$data_url=$OJ_MARKET_HOST . "/market/problem_export_xml_byId.php?problem_id=" . $problem_id;
$html = http_request($data_url);

import_fps($html);
//ob_clean();
echo "ok";
?>


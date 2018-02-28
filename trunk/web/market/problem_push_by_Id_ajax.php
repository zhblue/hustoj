<?php
@session_start ();
require_once (dirname(__FILE__)."/../include/db_info.inc.php");

if(!isset($OJ_LANG)){
	$OJ_LANG="en";
}
require_once(dirname(__FILE__)."/../lang/$OJ_LANG.php");
require_once(dirname(__FILE__)."/../include/const.inc.php");
require_once(dirname(__FILE__)."/../market/market.inc.php");

function fixcdata($content){
    return str_replace("]]>","]]]]><![CDATA[>",$content);
}
function getTestFileIn($pid, $testfile,$OJ_DATA) {
	if ($testfile != "")
		return file_get_contents ( "$OJ_DATA/$pid/" . $testfile . ".in" );
	else
		return "";
}
function getTestFileOut($pid, $testfile,$OJ_DATA) {
	if ($testfile != "")
		return file_get_contents (  );
	else
		return "";
}
function printTestCases($pid,$OJ_DATA){
	$ret = "";
	$xml ="";
	
	$pdir = opendir ( "$OJ_DATA/$pid/" );
	while ( $file = readdir ( $pdir ) ) {
		$pinfo = pathinfo ( $file );
		if (isset($pinfo ['extension'])
			&&$pinfo ['extension'] == "in" 
			&& $pinfo ['basename'] != "sample.in") {
			$ret = basename ( $pinfo ['basename'], "." . $pinfo ['extension'] );
			
			$outfile="$OJ_DATA/$pid/" . $ret . ".out";
			$infile="$OJ_DATA/$pid/" . $ret . ".in";
			if(file_exists($infile)){
				$xml .=  "<test_input><![CDATA[".fixcdata(file_get_contents ($infile))."]]></test_input>\n";
			}if(file_exists($outfile)){
				$xml .=  "<test_output><![CDATA[".fixcdata(file_get_contents ($outfile))."]]></test_output>\n";
			}
		}
	}
	closedir ( $pdir );
	return $xml;
}
class Solution{
  var $language="";
  var $source_code="";	
}
function getSolution($pid,$lang){
	$ret=new Solution();
	
	$language_name=$GLOBALS['language_name'];

	$sql = "select `solution_id`,`language` from solution where problem_id=? and result=4 and language=? limit 1";
//	echo $sql;
	$result = pdo_query($sql,$pid, $lang) ;
	if($result&&$row = $result[0] ){
		$solution_id=$row[0];
		$ret->language=$language_name[$row[1]];
		$sql = "select source from source_code where solution_id=?";
		$result = pdo_query( $sql,$solution_id ) ;
		if($row = $result[0] ){
			$ret->source_code=$row['source'];
		}
	}
       
	return $ret;
}
function fixurl($img_url){
   $img_url= html_entity_decode( $img_url,ENT_QUOTES,"UTF-8");
   
   if (substr($img_url,0,7)!="http://"){
     if(substr($img_url,0,1)=="/"){
	     	$ret='http://'.$_SERVER['HTTP_HOST'].':'.$_SERVER["SERVER_PORT"].$img_url;
     }else{
     		$path= dirname($_SERVER['PHP_SELF']);
	      $ret='http://'.$_SERVER['HTTP_HOST'].':'.$_SERVER["SERVER_PORT"].$path."/../".$img_url;
     }
   }else{
   	$ret=$img_url;
   }
   return  $ret;
} 
function image_base64_encode($img_url){
    $img_url=fixurl($img_url);
    if (substr($img_url,0,7)!="http://") return false;
	$handle = @fopen($img_url, "rb");
	if($handle){
		$contents = stream_get_contents($handle);
		$encoded_img= base64_encode($contents);
		fclose($handle);
		return $encoded_img;
	}else
		return false;
}
function getImages($content){
    preg_match_all("<[iI][mM][gG][^<>]+[sS][rR][cC]=\"?([^ \"\>]+)/?>",$content,$images);
    return $images;
}

function fixImageURL(&$html,&$did){
   $images=getImages($html);
   $imgs=array_unique($images[1]);
   foreach($imgs as $img){
		  $html=str_replace($img,fixurl($img),$html); 
		  //print_r($did);
		  if(!in_array($img,$did)){
			  $base64=image_base64_encode($img);
			  if($base64){
				  echo "<img><src><![CDATA[";
				  echo fixurl($img);
				  echo "]]></src><base64><![CDATA[";
				  echo $base64;
				  echo "]]></base64></img>";   
			 }
			 array_push($did,$img);
		 }
   }   	
}


function export_fps($problem_id,$OJ_DATA){
	$id = $problem_id;
	$sql = "select * from problem where problem_id=?";
	$result = pdo_query( $sql,$id);
	$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
	$xml .= "<fps version=\"1.2\" url=\"https://github.com/zhblue/freeproblemset/\">";
	$xml .= "<generator name=\"HUSTOJ\" url=\"https://github.com/zhblue/hustoj/\"/>";
	foreach ( $result as  $row ) {
		$xml .= "<item>";
		$xml .= "<title><![CDATA[".$row['title']."]]></title>";
		$xml .= "<time_limit unit=\"s\"><![CDATA[".$row['time_limit']."]]></time_limit>";
		$xml .= "<memory_limit unit=\"mb\"><![CDATA[".$row['memory_limit']."]]></memory_limit>";
		$did=array();
		fixImageURL($row['description'],$did);
		fixImageURL($row['input'],$did);
		fixImageURL($row['output'],$did);
		fixImageURL($row['hint'],$did);
		$xml .= "<description><![CDATA[<?php echo ".$row['description']."]]></description>";
		$xml .= "<input><![CDATA[".$row['input']."]]></input> ";
		$xml .= "<output><![CDATA[".$row['output']."]]></output>";
		$xml .= "<sample_input><![CDATA[".$row['sample_input']."]]></sample_input>";
		$xml .= "<sample_output><![CDATA[". $row['sample_output']."]]></sample_output>";
		$xml .= printTestCases($row['problem_id'],$OJ_DATA);
		$xml .= "<hint><![CDATA[". $row['hint']."]]></hint>";
		$xml .= "<source><![CDATA[". fixcdata($row['source'])."]]></source>";
		 if($row['spj']!=0){
			$filec="$OJ_DATA/".$row['problem_id']."/spj.c";
			$filecc="$OJ_DATA/".$row['problem_id']."/spj.cc";
			
			if(file_exists( $filec )){
				$xml .=  "<spj language=\"C\"><![CDATA[";
				$xml .=  fixcdata(file_get_contents ($filec ));
				$xml .=  "]]></spj>";
			}
			elseif(file_exists( $filecc )){
				$xml .=  "<spj language=\"C++\"><![CDATA[";
				$xml .=  fixcdata(file_get_contents ($filecc ));
				$xml .=  "]]></spj>";
			}
		 }
		 $xml .= "</item>";
	}
	$xml .= "</fps>";
	return $xml;
}
function send($xml,$OJ_MARKET_HOST)
{
	$header[] = 'Content-type: text/xml';
	$url = $OJ_MARKET_HOST . '/market/problem_receive_by_xml.php';
    $response = http_request($url,$xml,$header);
    //$response = http_request($url,false,$xml);
	return $response;//显示返回信息,
}
if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])
                ||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])
                )){
        echo "Please Login First! msg from problem_push_by_Id_ajax.php";
        exit(1);
}
if (isset($_POST ['problem_id'])) {
	$id = intval ( $_POST['problem_id'] );
    $rtn = 'error';
//	header("Content-type:text/xml;charset=utf-8");
	$xml= export_fps($id,$OJ_DATA);
	$rtn = send($xml,$OJ_MARKET_HOST);
	echo $rtn;
}
?>


<?php
@session_start ();
require_once (dirname(__FILE__)."/../include/db_info.inc.php");

if(!isset($OJ_LANG)){
	$OJ_LANG="en";
}
require_once(dirname(__FILE__)."/../lang/$OJ_LANG.php");
require_once(dirname(__FILE__)."/../include/const.inc.php");
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

if(strstr($OJ_DATA,"saestor:"))     {
  // echo "<debug>$pid</debug>";
       $store = new SaeStorage();
           $ret = $store->getList("data", "$pid",100,0 );
            foreach($ret as $file) {
              //          echo "<debug>$file</debug>";
              
              if(!strstr($file,"sae-dir-tag")){
                    
                    $pinfo = pathinfo ( $file );
		if (isset($pinfo ['extension'])
			&&$pinfo ['extension'] == "in" 
			&& $pinfo ['basename'] != "sample.in") {
			$f = basename ( $pinfo ['basename'], "." . $pinfo ['extension'] );
			
			$outfile="$pid/" . $f . ".out";
			$infile="$pid/" . $f . ".in";
			if( $store->fileExists ("data",$infile)){
				echo "<test_input><![CDATA[".fixcdata($store->read ("data",$infile))."]]></test_input>\n";
			}if($store->fileExists ("data",$outfile)){
				echo "<test_output><![CDATA[".fixcdata($store->read ("data",$outfile))."]]></test_output>\n";
			}
//			break;
		}
                    
                    
                    
              }
                    
            }

}else{


	$ret = "";
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
				echo "<test_input><![CDATA[".fixcdata(file_get_contents ($infile))."]]></test_input>\n";
			}if(file_exists($outfile)){
				echo "<test_output><![CDATA[".fixcdata(file_get_contents ($outfile))."]]></test_output>\n";
			}
//			break;
		}
	}
	closedir ( $pdir );
	return $ret;
}
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

if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])
                ||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])
                )){
        echo "Please Login First!";
        exit(1);
}
if (isset($_GET ['problem_id'])) {
	//require_once("../include/check_post_key.php");
	$id = intval ( $_GET['problem_id'] );
	$sql = "select * from problem where problem_id=?";
	$result = pdo_query( $sql,$id);
    $filename="$id"; 
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
  
	?>
   
<fps version="1.2" url="https://github.com/zhblue/freeproblemset/">
	<generator name="HUSTOJ" url="https://github.com/zhblue/hustoj/"/>
	<?php
	foreach ( $result as  $row ) {
		
		?>
<item>
<title><![CDATA[<?php echo $row['title']?>]]></title>
<time_limit unit="s"><![CDATA[<?php echo $row['time_limit']?>]]></time_limit>
<memory_limit unit="mb"><![CDATA[<?php echo $row['memory_limit']?>]]></memory_limit>

<?php
	$did=array();
	fixImageURL($row['description'],$did);
	fixImageURL($row['input'],$did);
	fixImageURL($row['output'],$did);
	fixImageURL($row['hint'],$did);
	
?>
<description><![CDATA[<?php echo $row['description']?>]]></description>
<input><![CDATA[<?php echo $row['input']?>]]></input> 
<output><![CDATA[<?php echo $row['output']?>]]></output>
<sample_input><![CDATA[<?php echo $row['sample_input']?>]]></sample_input>
<sample_output><![CDATA[<?php echo $row['sample_output']?>]]></sample_output>
  <?php printTestCases($row['problem_id'],$OJ_DATA)?>
<hint><![CDATA[<?php echo $row['hint']?>]]></hint>
<source><![CDATA[<?php echo fixcdata($row['source'])?>]]></source>
<?php
$pid=$row['problem_id'];
for ($lang=0;1>2 && $lang<count($language_ext);$lang++){

	$solution=getSolution($pid,$lang);
	if ($solution->language){?>
		<solution language="<?php echo $solution->language?>"><![CDATA[<?php echo fixcdata($solution->source_code)?>]]></solution>
	<?php
	}
	$pta=array("prepend","template","append");
	foreach($pta as $pta_file){
		$append_file="$OJ_DATA/$pid/$pta_file.".$language_ext[$lang];
//		echo "<filename value=\"$lang  $append_file $language_ext[$lang]\"/>";
		if (file_exists($append_file)){?>
			<<?php echo $pta_file?> language="<?php echo $language_name[$lang]?>"><![CDATA[<?php echo fixcdata(file_get_contents($append_file))?>]]></<?php echo $pta_file?>>
		<?php 
		}
	}
}
?>
<?php
 if($row['spj']!=0){
 	$filec="$OJ_DATA/".$row['problem_id']."/spj.c";
 	$filecc="$OJ_DATA/".$row['problem_id']."/spj.cc";
 	
 	if(file_exists( $filec )){
		echo "<spj language=\"C\"><![CDATA[";
 		echo fixcdata(file_get_contents ($filec ));
 		echo "]]></spj>";
	}
 	elseif(file_exists( $filecc )){
 	    echo "<spj language=\"C++\"><![CDATA[";
 		echo fixcdata(file_get_contents ($filecc ));
 		echo "]]></spj>";
 	}
 }
?>
</item>
<?php }
	
	
	echo "</fps>";

}
?>


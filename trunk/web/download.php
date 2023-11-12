<?php
////////////////////////////Common head
require_once( './include/db_info.inc.php' );
if((!isset($OJ_DOWNLOAD))||!$OJ_DOWNLOAD){
    $view_errors="Download Disabled!";
    require("template/".$OJ_TEMPLATE."/error.php");
    exit(0);

}
$sid=intval($_GET['sid']);
$name=basename($_GET['name'],".out");
$sql="select problem_id,contest_id,user_id from solution where solution_id=?";
$data=pdo_query($sql,$sid);
//var_dump($sql);
if(count($data)>0){
   $row=$data[0];
   $pid=$row[0];
   $cid=$row[1];
   $uid=$row[2];
   if(!(isset($_SESSION[$OJ_NAME.'_'.'user_id']) && $uid == $_SESSION[$OJ_NAME.'_'.'user_id']
         || isset($_SESSION[$OJ_NAME.'_'.'administrator'])
       )){
       	    $view_errors="not your submission"; 
	    require("template/".$OJ_TEMPLATE."/error.php");
	    exit(0);
   }
   if(isset($OJ_NOIP_KEYWORD)&&$OJ_NOIP_KEYWORD){
	$now =  date('Y-m-d H:i', time());
	$sql = "select 1 from `contest` where contest_id=? and `start_time` < ? and `end_time` > ? and `title` like ?";
        $rrs = pdo_query($sql, $cid ,$now , $now , "%$OJ_NOIP_KEYWORD%");
        $flag = count($rrs) > 0 ;
	if($flag){
	    $view_errors = "<h2> $MSG_NOIP_WARNING </h2>";
	    require("template/".$OJ_TEMPLATE."/error.php");
	    exit(0);
	}

   }
   $infile="$OJ_DATA/$pid/$name.in"; 
   $outfile="$OJ_DATA/$pid/$name.out"; 
   
   $zipname = tempnam(__dir__.'/upload', '');
   $zip = new ZipArchive();

        if ($zip->open($zipname, ZIPARCHIVE::CREATE) !== TRUE) {
            exit ('无法打开文件，或者文件创建失败');
        }
        $files = [ $infile,$outfile ];

        $zip->open($zipname, ZipArchive::CREATE);
        foreach ($files as $file) {
              
            $fileContent = file_get_contents($file);
            $file = iconv('utf-8', 'GBK', basename($file));
            $zip->addFromString($file, $fileContent);
        }
        $zip->close();

        header('Content-Type: application/zip;charset=utf8');
        header('Content-disposition: attachment; filename='.$name. date('Y-m-d') . '.zip');
        header('Content-Length: ' . filesize($zipname));
        readfile($zipname);
        unlink($zipname);
        die();
}
?>

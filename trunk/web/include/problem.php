<?php
function addproblem($title, $time_limit, $memory_limit, $description, $input, $output, $sample_input, $sample_output, $hint, $source, $spj, $OJ_DATA) {
  //$spj=($spj);
  $sql = "INSERT INTO `problem` (`title`,`time_limit`,`memory_limit`,`description`,`input`,`output`,`sample_input`,`sample_output`,`hint`,`source`,`spj`,`in_date`,`defunct`) VALUES(?,?,?,?,?,?,?,?,?,?,?,NOW(),'Y')";
  //echo $sql;
  $pid = pdo_query($sql, $title, $time_limit, $memory_limit, $description, $input, $output, $sample_input, $sample_output, $hint, $source, $spj);

  echo "New Problem:<a target=_blank href='../problem.php?id=$pid'>$pid ".htmlentities($title,ENT_QUOTES)."</a> added!<br>";

  if (isset($_POST['contest_id']) && intval($_POST['contest_id'])>0) {
    $cid = intval($_POST['contest_id']);
    $sql = "SELECT count(*) FROM `contest_problem` WHERE `contest_id`=?";
    $result = pdo_query($sql, $cid);
    $row = $result[0];
    $num = $row[0];
    
    echo "&nbsp;&nbsp;- Contest Problem Num = ".$num.":";

    $sql = "INSERT INTO `contest_problem` (`problem_id`,`contest_id`,`num`) VALUES(?,?,?)";	
    pdo_query($sql, $pid, $cid, $num);
  }

  $basedir = "$OJ_DATA/$pid";

  if (!isset($OJ_SAE) || !$OJ_SAE) {
    //echo "[$title]data in $basedir";
  }
  return $pid;
}

function mkdata($pid, $filename, $input, $OJ_DATA) {
  $basedir = "$OJ_DATA/$pid";

  $fp = @fopen($basedir."/$filename","w");

  if ($fp) {
    fputs($fp, preg_replace("(\r\n)", "\n", $input));
    fclose($fp);
  }
  else {
    echo "- Error while opening".$basedir."/$filename ,try [chgrp -R www-data $OJ_DATA] and [chmod -R 771 $OJ_DATA ] ";
  }
}
?>

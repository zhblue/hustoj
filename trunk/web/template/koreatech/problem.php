<!DOCTYPE html>
<html>
<head>
	<title><?php echo $view_title?></title>
  <?php require_once("./template/".$OJ_TEMPLATE."/include-header.php");?>
  <link rel="next" href="submitpage.php?<?php
        if ($pr_flag){
		      echo "id=$id";
      	}else{
		      echo "cid=$cid&pid=$pid&langmask=$langmask";
	      }
  ?>">
</head>
<body>
<?php
  $navigation_tab = "problem";
	if(isset($_GET['id']))
		require_once("oj-header.php");
	else
		require_once("./template/$OJ_TEMPLATE/contest-header.php");
?>

<div class="container">
<?php
  $problem_tab = "problem";
  require_once("./template/$OJ_TEMPLATE/problem-header.php");

	if ($pr_flag){
    echo "<h2>$row->title";
	  if ($row->spj) echo " <small><span class='label label-primary'>Special Judge</span></small>";
    echo "</h2>";
    if(strlen($row->source) > 0) {
		  echo "<h4><span class='label label-default'>$MSG_Source</span> <a href='problemset.php?search=$row->source'>".nl2br($row->source)."</a></h4>";
    }
	}else{
		echo "<h2>$row->title</h2>";
	}
?>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th><?php echo $MSG_Time_Limit;?></th>
        <th><?php echo $MSG_Memory_Limit;?></th>
      </tr>
    </thead>
    <tbody>
      <tr>
      <td><?php echo $row->time_limit;?> Sec</td>
      <td><?php echo $row->memory_limit;?> MB</td>
    </tbody>
  </table>

<?php


	echo "<h2>$MSG_Description</h2><div class=content>".$row->description."</div>";
	echo "<h2>$MSG_Input</h2><div class=content>".$row->input."</div>";
	echo "<h2>$MSG_Output</h2><div class=content>".$row->output."</div>";



	$sinput=str_replace("<","&lt;",$row->sample_input);
  $sinput=str_replace(">","&gt;",$sinput);
	$soutput=str_replace("<","&lt;",$row->sample_output);
  $soutput=str_replace(">","&gt;",$soutput);
  if($sinput) {
      echo "<h2>$MSG_Sample_Input</h2>
			<pre class=content><span class=sampledata>".($sinput)."</span></pre>";
  }
  if($soutput){
	echo "<h2>$MSG_Sample_Output</h2>
			<pre class=content><span class=sampledata>".($soutput)."</span></pre>";
  }
  if(strlen($row->hint) > 0) {
		echo "<h2>$MSG_HINT</h2>
			<div class=content><p>".nl2br($row->hint)."</p></div>";
  }
?>
</div>
<?php require_once("oj-footer.php");?>
<?php require_once("include-bottom.php");?>
</body>
</html>

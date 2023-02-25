<?php
require("admin-header.php");
require_once("../include/set_get_key.php");

if(!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'contest_creator']))){
  echo "<a href='../loginpage.php'>Please Login First!</a>";
  exit(1);
}


if(isset($OJ_LANG)){
  require_once("../lang/$OJ_LANG.php");
}
?>

<title>Problem List</title>
<hr>
<center><h3><?php echo $MSG_PROBLEM."-".$MSG_SOURCE?></h3></center>

<div class='container'>

<?php

$view_category="";

if (isset($_GET['keyword']) && $_GET['keyword']!=""){
    $keyword = $_GET['keyword'];
    $keyword = "%$keyword%";

    $sql = "select distinct source "
        . "FROM `problem` where defunct='N' and source LIKE ?"
        . "LIMIT 500";
    $result = pdo_query($sql, $keyword);
}  else {
    $sql = "select distinct source "
        . "FROM `problem` where defunct='N'"
        . "LIMIT 500";
    $result = pdo_query($sql);
}

$category=array();
foreach ($result as $row){
    $cate=explode(" ",$row['source']);
    foreach($cate as $cat){
        array_push($category,trim($cat));
    }
}
$category = array_unique(array_filter($category));

foreach ($category as $source){
    $sql = "select id FROM `problem_source` where source=? limit 1";
    $result = pdo_query($sql, $source);
    if (empty($result)){
        $sql = "insert into problem_source (source,summary,in_date) value(?,?,now())";
        pdo_query($sql, $source,'未创建分类描述');
    }

}

$sql = "select * FROM `problem_source` ";
$result = pdo_query($sql);
?>


<?php
/*
echo "<select class='input-mini' onchange=\"location.href='problem_list.php?page='+this.value;\">";
for ($i=1;$i<=$cnt;$i++){
        if ($i>1) echo '&nbsp;';
        if ($i==$page) echo "<option value='$i' selected>";
        else  echo "<option value='$i'>";
        echo $i+9;
        echo "**</option>";
}
echo "</select>";
*/
?>

<center>
<table width=100% border=1 style="text-align:center;">
  <form method=post action=contest_add.php>
<input type="hidden" name=keyword value="<?php echo htmlentities($_GET['keyword'],ENT_QUOTES,"utf-8")?>">
    <tr>
      <td><?php echo $MSG_SOURCE_LIST?></td>
      <td><?php echo $MSG_SOURCE_SUMMARY?></td>
      <td><?php echo $MSG_DATE?></td>
      <td><?php echo $MSG_EDIT?></td>
    </tr>
    <?php
    foreach($result as $row){
      echo "<tr>";
        echo "<td>".$row['source']."</td>";
        echo '<td>' . $row['summary'] . '</td>';
        echo '<td>' . $row['in_date'] . '</td>';
        echo "<td><a href=problem_source_edit.php?id=".$row['id'].">$MSG_EDIT</a></td>";
    echo "</tr>";
  }
?>

  </form>
</table>
</center>

<script src='../template/bs3/jquery.min.js' ></script>

<script>
function phpfm(pid){
  //alert(pid);
  $.post("phpfm.php",{'frame':3,'pid':pid,'pass':''},function(data,status){
    if(status=="success"){
      document.location.href="phpfm.php?frame=3&pid="+pid;
    }
  });
}
</script>
</div>



</div>

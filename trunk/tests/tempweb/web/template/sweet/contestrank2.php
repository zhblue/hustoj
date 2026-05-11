<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title><?php echo $OJ_NAME?></title>  
    <?php include("template/$OJ_TEMPLATE/css.php");?>	    


    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <div class="container">
    <?php include("template/$OJ_TEMPLATE/nav.php");?>	    
      <!-- Main component for a primary marketing message or call to action -->
      <div class="jumbotron">
<?php
$rank=1;
?>
<center><h3>Contest RankList -- <?php echo $title?></h3><a href="contestrank.xls.php?cid=<?php echo $cid?>" >Download</a></center>
<table id=rank><thead><tr class=toprow align=center><td class="{sorter:'false'}" width=5%>Rank<th width=10%>User</th><th width=10%>Nick</th><th width=5%>Solved</th><th width=5%>Penalty</th>
<?php
for ($i=0;$i<$pid_cnt;$i++)
echo "<td><a href=problem.php?cid=$cid&pid=$i>$PID[$i]</a></td>";
echo "</tr></thead>\n<tbody>";
if(false)for ($i=0;$i<$user_cnt;$i++){
if ($i&1) echo "<tr class=oddrow align=center>\n";
else echo "<tr class=evenrow align=center>\n";
echo "<td>";
$uuid=$U[$i]->user_id;
$nick=$U[$i]->nick;
if($nick[0]!="*")
echo $rank++;
else
echo "*";
$usolved=$U[$i]->solved;
if(isset($_GET['user_id'])&&$uuid==$_GET['user_id']) echo "<td bgcolor=#ffff77>";
else echo"<td>";
echo "<a name=\"$uuid\" href=userinfo.php?user=$uuid>$uuid</a>";
echo "<td><a href=userinfo.php?user=$uuid>".htmlentities($U[$i]->nick,ENT_QUOTES,"UTF-8")."</a>";
echo "<td><a href=status.php?user_id=$uuid&cid=$cid>$usolved</a>";
echo "<td>".sec2str($U[$i]->time);
for ($j=0;$j<$pid_cnt;$j++){
$bg_color="eeeeee";
if (isset($U[$i]->p_ac_sec[$j])&&$U[$i]->p_ac_sec[$j]>0){
$aa=0x33+$U[$i]->p_wa_num[$j]*32;
$aa=$aa>0xaa?0xaa:$aa;
$aa=dechex($aa);
$bg_color="$aa"."ff"."$aa";
//$bg_color="aaffaa";
if($uuid==$first_blood[$j]){
$bg_color="aaaaff";
}
}else if(isset($U[$i]->p_wa_num[$j])&&$U[$i]->p_wa_num[$j]>0) {
$aa=0xaa-$U[$i]->p_wa_num[$j]*10;
$aa=$aa>16?$aa:16;
$aa=dechex($aa);
$bg_color="ff$aa$aa";
}
echo "<td class=well style='background-color:#$bg_color'>";
if(isset($U[$i])){
if (isset($U[$i]->p_ac_sec[$j])&&$U[$i]->p_ac_sec[$j]>0)
echo sec2str($U[$i]->p_ac_sec[$j]);
if (isset($U[$i]->p_wa_num[$j])&&$U[$i]->p_wa_num[$j]>0)
echo "(-".$U[$i]->p_wa_num[$j].")";
}
}
echo "</tr>\n";
}
echo "</tbody></table>";
?>
      </div>

    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php include("template/$OJ_TEMPLATE/js.php");?>	    
<script type="text/javascript" src="include/jquery.tablesorter.js"></script>
<script type="text/javascript">
$(document).ready(function()
{
$.tablesorter.addParser({
// set a unique id
id: 'punish',
is: function(s) {
// return false so this parser is not auto detected
return false;
},
format: function(s) {
// format your data for normalization
var v=s.toLowerCase().replace(/\:/,'').replace(/\:/,'').replace(/\(-/,'.').replace(/\)/,'');
//alert(v);
v=parseFloat('0'+v);
return v>1?v:v+Number.MAX_VALUE-1;
},
// set type, either numeric or text
type: 'numeric'
});
$("#rank").tablesorter({
headers: {
4: {
sorter:'punish'
}
<?php
for ($i=0;$i<$pid_cnt;$i++){
echo ",".($i+5).": { ";
echo " sorter:'punish' ";
echo "}";
}
?>
}
});
}
);
</script>
<script>
  function getTotal(rows){
    var total=0;
    return rows.length-1;
    for(var i=0;i<rows.length&&total==0;i++){
      try{
         total=parseInt(rows[rows.length-i].cells[0].innerHTML);
          if(isNaN(total)) total=0;
      }catch(e){
      
      }
    }
    return total;
  
  }
function metal(){
  var tb=window.document.getElementById('rank');
  var rows=tb.rows;
  try{
  var total=getTotal(rows);
  //alert(total);
	  for(var i=1;i<rows.length;i++){
	  	var cell=rows[i].cells[0];
      var acc=rows[i].cells[3];
      var ac=parseInt(acc.innerHTML);
      if (isNaN(ac)) ac=parseInt(acc.textContent);
                
                
	  	if(cell.innerHTML!="*"&&ac>0){
	 
	  	     var r=i;
	  	     if(r==1){
	  	       cell.innerHTML="Winner";
                       //cell.style.cssText="background-color:gold;color:red";
                       cell.className="badge btn-warning";
	  	     }else{
	  	       cell.innerHTML=r;
		     }
	  	     if(r>1&&r<=total*.05+1)
	  	        cell.className="badge btn-warning";
	  	     if(r>total*.05+1&&r<=total*.20+1)
	  	        cell.className="badge";
	  	     if(r>total*.20+1&&r<=total*.45+1)
	  	        cell.className="badge btn-danger";
	  	     if(r>total*.45+1&&ac>0)
              		cell.className="badge badge-info";
	  	}
	  }
  }catch(e){
     alert(e);
  }
}
metal();
replay();
<?php if (isset($solution_json)) echo "var solutions=$solution_json;"?>
var replay_index=0;
function replay(){
  replay_index=0;
  window.setTimeout("add()",1000);
}
function add(){
  if(replay_index>=solutions.length) return metal();
  var solution=solutions[replay_index];
  var tab=$("#rank");
  var row=findrow(tab,solution);
  if(row==null)
	tab.append(newrow(tab,solution));
  row=findrow(tab,solution);
  update(tab,row,solution);
  replay_index++;
  sort(tab[0].rows);
  metal();
  window.setTimeout("add()",5);
}
function sec2str(sec){
   var ret="";
   if(sec<36000) ret="0" ;
   ret+=parseInt(sec/3600);
   ret+=":";
   if(sec%3600/60<10) ret+="0" ;
   ret+=parseInt(sec%3600/60);
   ret+=":";
   if(sec%60<10) ret+="0";
   ret+=parseInt(sec%60);
   return ret;
}
function str2sec(str){
   var s=str.split(":");
   var h=parseInt(s[0]);
   var m=parseInt(s[1]);
   var s=parseInt(s[2]);
   return h*3600+m*60+s;
}
function colorful(td,ac,num){
  if(num<0) num=-num;else num=0;
  num*=10
  if(num>255) num=255;
  if(ac&&num>200) num=200;
  var rb=ac?num:255-num;
  if(ac){
//	td.className="well green";
	td.style="background-color: rgb("+rb+",255,"+rb+");";
  }else{
	td.style="background-color: rgb(255,"+rb+","+rb+");";
  }
}
function update(tab,row,solution){
 var col=parseInt(solution["num"])+5;
 var old=row.cells[col].innerHTML;
 var time=0;
 if(old!="") time=parseInt(old);
 if(!(old.charAt(0)=='-'||old=='')) return;
 if(parseInt(solution["result"])==4){
 	if(old.charAt(0)=='-'||old=='') {
		var pt=time;
		time= parseInt(solution["in_date"])-time*1200;

        	penalty=str2sec(row.cells[4].innerHTML);
 		penalty+=time;
 		row.cells[4].innerHTML=sec2str(penalty);
 		row.cells[col].innerHTML=sec2str( parseInt(solution["in_date"]));
		if(pt!=0)
	 		row.cells[col].innerHTML+="("+pt+")";
 		colorful(row.cells[col],true,pt);
	}else{
		if(row.cells[col].className=="well green");
	}
	row.cells[3].innerHTML=parseInt(row.cells[3].innerHTML)+1;
 }else{
        	time--;
 		row.cells[col].innerHTML=time;
 	colorful(row.cells[col],false,time);
 }
 /*
 if(parseInt(solution["result"])==4){
 	if(row.cells[col].className!="well green"){
	}
	row.cells[col].className="well green";
 }else{
 	if(row.cells[col].className!="well green") 
		row.cells[col].className="well red";
 }
*/
}
function sort(rows){
   for(var i=1;i<rows.length;i++){
       for(var j=1;j<i;j++){
	   if(cmp(rows[i],rows[j])){
		swapNode(rows[i],rows[j]);
 	   }
       }

   }

}
 function swapNode(node1,node2)
        {
          var parent = node1.parentNode;//父节点
          var t1 = node1.nextSibling;//两节点的相对位置
          var t2 = node2.nextSibling;
$(node1).fadeToggle("slow");          
$(node2).fadeToggle("slow");          
          //如果是插入到最后就用appendChild
          if(t1) parent.insertBefore(node2,t1);
          else parent.appendChild(node2);
          if(t2) parent.insertBefore(node1,t2);
          else parent.appendChild(node1);
$(node1).fadeToggle("slow");          
$(node2).fadeToggle("slow");          
}    
function cmp(a,b){
   if(parseInt(a.cells[3].innerHTML)>parseInt(b.cells[3].innerHTML))
	return true;
   
   if(parseInt(a.cells[3].innerHTML)==parseInt(b.cells[3].innerHTML))
	return str2sec(a.cells[4].innerHTML)<str2sec(b.cells[4].innerHTML);
}
 function trim(str){ //删除左右两端的空格
　　     return str.replace(/(^\s*)|(\s*$)/g, "");
　　 }
function newrow(tab,solution){

  var row="<tr><td></td><td>"+solution['user_id']+"</td>";
  row+="<td>"+trim(solution['nick'])+"</td>";
  row+="<td>";
  var css="grey";
  var time=0;
  if(solution['result']==4){
	 row+="1";
	 time=solution['in_date'];
	 count=sec2str( time);
         css="well green";
  } else{
	 row+="0";
	 css="well red";
	 count=-1;
  }
  row+="</td>";
  var n=tab[0].rows[0].cells.length;
  row+="<td>"+sec2str(time)+"</td>";

  for(var i=5;i<n;i++) {
	if(i-5==solution['num'])
		row+="<td class='"+css+"'>"+count+"</td>"; 
	else
		row+="<td></td>"; 
  }
  row+="</tr>";
  return row;  
}
function findrow(tab,solution){
  var rows=tab[0].rows;
  for(var i=0;i<rows.length;i++){
     if(rows[i].cells[1].innerHTML==solution['user_id']) 
	return rows[i];

  }
  return null;
}
</script>
<style>
.well{
   background-image:none;
   padding:1px;
}
td{
   white-space:nowrap;

}
.red{
  background-color:#ffa0a0;
}
.green{
  background-color:#33ff33;
}

</style>
  </body>
</html>

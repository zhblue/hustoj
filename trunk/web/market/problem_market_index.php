<?php
require(dirname(__FILE__)."/../admin/admin-header.php");
if(isset($OJ_LANG)){
        require_once("../lang/$OJ_LANG.php");
}
require_once(dirname(__FILE__)."/../include/db_info.inc.php");
require_once(dirname(__FILE__)."/../include/set_get_key.php");
if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])
                ||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])
                )){
        echo "<a href='../loginpage.php'>Please Login First!</a>";
        exit(1);
}

if(isset($_GET['keyword']))
	$keyword=$_GET['keyword'];
else
	$keyword="";
require_once(dirname(__FILE__) ."/../market/market.inc.php");
$page=1;
$cnt=1;
$page_cnt=0;
if (isset($_GET['page'])){
    $page=intval($_GET['page']);
  }else $page=$cnt;
 $pstart=1000+$page_cnt*intval($page-1);
 $pend=$pstart+$page_cnt;
 echo "<div class='container'>";
 echo "<form action=problem_market_index.php>";
 if($keyword) {
	 $keyword="%$keyword%";
 }else{
	// $result=pdo_query($sql,$pstart,$pend);
}
?>
<nav id="page" class="center">
	<ul class="pagination">
		<li class="page-item"><a href="problem_market_index.php?page=1">&lt;&lt;</a></li>
		<?php  
		$pagecount = intval(@file_get_contents($OJ_MARKET_HOST.'/problem_market_json.php?getPageCount=1'));
		if($pagecount == 0)$pagecount=1;
		for ($i = 1; $i <= $pagecount; $i++) {
		?>
					<li class="page-item <?php if($page == $i){echo "active";} ?> "><a href="problem_market_index.php?page=<?php echo $i ?> ">
					<?php echo $i ?> 
					</a></li>
		<?php		
		}
		?>
		<li class="page-item"><a href="problem_market_index.php?page=<?php echo $pagecount ?>">&gt;&gt;</a></li>
	</ul>
</nav>

<?php
if(market_isLogin() == "noLogin")//isLogin noLogin
{
    echo market_login();
}
//market_login();
$data_url = $OJ_MARKET_HOST . '/problem_market_json.php?page='.$page;
$json = http_request($data_url);
$result = json_decode($json,true);
if(is_null($result))
	echo $json;
echo "<center><table class='table table-striped' width=98% border=1>";
echo "<tr><td colspan=8>";
//echo "<input type=checkbox onchange='$(\"input[type=checkbox]\").prop(\"checked\", this.checked)'>";
echo "题库网址：".$OJ_MARKET_HOST . " &nbsp;&nbsp;题库账号：".$OJ_MARKET_USERNAME;
echo "<tr><td>PID<td>Title<td>AC<td>submit<td>source<td>Date<td>pull";
if( !is_null($result))
foreach($result as $row){
        echo "<tr>";
        echo "<td>".$row['problem_id'];
       // echo "<input type=checkbox name='pid[]' value='".$row['problem_id']."'>";
        echo "<td><a target='_blank' href=".$row['host']."/problem.php?id=".$row['problem_id'].">".$row['title']."</a>";
        echo "<td>".$row['accepted'];
		echo "<td>".$row['submit'];
		echo "<td>".$row['source'];
        echo "<td>".$row['in_date'];
		echo "<td>";
		echo "<input type=button class='pullproblem' disabled value='pull' title_md5='".md5($row['title'])."' problem-id=".$row['problem_id'].">";
        echo "</tr>";
}

echo "<tr><td colspan=8>";
echo "</tr>";
echo "</table></center>";
?>
<nav id="page" class="center">
	<ul class="pagination">
		<li class="page-item"><a href="problem_market_index.php?page=1">&lt;&lt;</a></li>
		<?php  
		$pagecount = intval(@file_get_contents($OJ_MARKET_HOST.'/problem_market_json.php?getPageCount=1'));
		if($pagecount == 0)$pagecount=1;
		for ($i = 1; $i <= $pagecount; $i++) {
		?>
					<li class="page-item <?php if($page == $i){echo "active";} ?> "><a href="problem_market_index.php?page=<?php echo $i ?> ">
					<?php echo $i ?> 
					</a></li>
		<?php		
		}
		?>
		<li class="page-item"><a href="problem_market_index.php?page=<?php echo $pagecount ?>">&gt;&gt;</a></li>
	</ul>
</nav>
<script src='../template/bs3/jquery.min.js' ></script>
<script>
function iGetInnerText(testStr) {
	var resultStr = testStr.replace(/\ +/g, ""); //去掉空格
	resultStr = testStr.replace(/[ ]/g, "");    //去掉空格
	resultStr = testStr.replace(/[\r\n]/g, ""); //去掉回车换行
	return resultStr;
}
String.prototype.startWith=function(s){
    if(s==null||s==""||this.length==0||s.length>this.length)
        return false;
    if(this.substr(0,s.length)==s)
        return true;
    else
        return false;
    return true;
}
$(document).ready(function(){
  $(".pullproblem").click(function(){
        var problem_id=$(this).attr('problem-id');
		console.log(problem_id);
		var _self = $(this);
        $.post("../market/problem_import_xml_byId_ajax.php",
                {problem_id:problem_id},
                function(data,status){
					if(iGetInnerText(data) =='ok')
						_self.val('ok');
					else
						_self.val('no');
					_self.attr('title',data);
					//alert(data);
                }
        );
  });
    var marketHost = '<?php echo $OJ_MARKET_HOST ?>' ;
    $(".pullproblem").each(function(index){
        var url = location.href;
        if (url.startWith(marketHost)) {
            return;
        }
		var title_md5=$(this).attr('title_md5');
		var _self = $(this);
        $.post("../market/hasProblem_api.php",
                {title_md5:title_md5},
                function(data,status){
					if(data == 2)
						_self.attr('title',"可以拉取");
					else if(data == 1)
						_self.val("已存在");
					else
						_self.val("异常");
					if(data !=2)_self.attr("disabled",true);else _self.attr("disabled",false);
					console.log(data);

					//alert(data);
                }
        );
	});
});
</script>
</div>

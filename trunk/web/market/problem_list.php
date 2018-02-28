<?php

require(dirname(__FILE__) ."/../admin/admin-header.php");
        if(isset($OJ_LANG)){
                require_once("../lang/$OJ_LANG.php");
        }
require_once(dirname(__FILE__) ."/../include/set_get_key.php");
require_once(dirname(__FILE__) ."/market.inc.php");
if (!(isset($_SESSION[$OJ_NAME.'_'.'administrator'])
                ||isset($_SESSION[$OJ_NAME.'_'.'contest_creator'])
                ||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])
                )){
        echo "<a href='../loginpage.php'>Please Login First!</a>";
        exit(1);
}
if(market_isLogin() == "noLogin")//isLogin noLogin
{
    echo market_login();
}
if(isset($_GET['keyword']))
	$keyword=$_GET['keyword'];
else
	$keyword="";
$sql="SELECT max(`problem_id`) as upid FROM `problem`";
$page_cnt=100;
$result=pdo_query($sql);
$row=$result[0];
$cnt=intval($row['upid'])-1000;
$cnt=intval($cnt/$page_cnt)+(($cnt%$page_cnt)>0?1:0);
if (isset($_GET['page'])){
        $page=intval($_GET['page']);
}else $page=$cnt;
$pstart=1000+$page_cnt*intval($page-1);
$pend=$pstart+$page_cnt;
echo "<div class='container'>";
echo "<form action=problem_list.php>";
echo "<select class='input-mini' onchange=\"location.href='problem_list.php?page='+this.value;\">";
for ($i=1;$i<=$cnt;$i++){
        if ($i>1) echo '&nbsp;';
        if ($i==$page) echo "<option value='$i' selected>";
        else  echo "<option value='$i'>";
        echo $i+9;
        echo "**</option>";
}
echo "</select>";
$sql="";
if($keyword) {
	$keyword="%$keyword%";
	$sql="select `problem_id`,`title`,`accepted`,`in_date`,`defunct` FROM `problem` where title like ? or source like ?";
	$result=pdo_query($sql,$keyword,$keyword);
}else{
	$sql="select `problem_id`,`title`,`accepted`,`in_date`,`defunct` FROM `problem` where problem_id>=? and problem_id<=? order by `problem_id` desc";
	$result=pdo_query($sql,$pstart,$pend);
}
?>
<form action=problem_list.php>

<input name=keyword><input type=submit value="<?php echo $MSG_SEARCH?>" ></form>

<?php
echo "<center><table class='table table-striped' width=90% border=1>";
echo "<form method=post action=contest_add.php>";
echo "<tr><td colspan=9>";
echo "&nbsp;&nbsp;题库网址：".$OJ_MARKET_HOST . " &nbsp;&nbsp;账号：".$OJ_MARKET_USERNAME ;
echo "<tr><td>PID";
//echo "<input type=checkbox onchange='$(\"input[type=checkbox]\").prop(\"checked\", this.checked)'>";
echo "<td>Title<td>AC<td>Date";
if(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])){
		echo "<td>push</tr>";
}
foreach($result as $row){
        echo "<tr>";
        echo "<td>".$row['problem_id'];
        echo "<input type=checkbox name='pid[]' value='".$row['problem_id']."'>";
        echo "<td><a target='_blank' href='../problem.php?id=".$row['problem_id']."'>".$row['title']."</a>";
        echo "<td>".$row['accepted'];
        echo "<td>".$row['in_date'];
  if(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])){
				echo "<td>";
				echo "<input type=button class='pushproblem' value='push' disabled title_md5=".md5($row['title'])." problem-id=".$row['problem_id'].">";
        }
        echo "</tr>";
}
echo "<tr><td colspan=9><input type=submit name='problem2contest' value='CheckToNewContest'>";
echo "</tr></form>";
echo "</table></center>";
?>
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
  $("#Available").click(function(){
        var data='0';
        $.each($('input:checkbox:checked'),function(){
                if($(this).val()!='on')data += ',' + $(this).val();
        });
        
        $.post("problem_df_change_ajax.php",
                {act:'N',ids:data},
                function(data,status){
                        location.reload();
                        //console.log(data);
                }
        );
  });
  $("#Reserved").click(function(){
        var data='0';
        $.each($('input:checkbox:checked'),function(){
                if($(this).val()!='on') data += ',' + $(this).val();
        });
        
        $.post("problem_df_change_ajax.php",
                {act:'Y',ids:data},
                function(data,status){
                        location.reload();
                        //console.log(data);
                }
        );
  });
  $("#Delete").click(function(){
        var data='0';
        $.each($('input:checkbox:checked'),function(){
                if($(this).val()!='on') data += ',' + $(this).val();
        });
        
        $.post("problem_del_ajax.php",
                {ids:data},
                function(result){
					if(result == 'ok')
						location.reload();
                    else 
						alert(result);
					console.log(result == 'ok');
					console.log(result);
                }
        );
  });
var marketHost = '<?php echo $OJ_MARKET_HOST ?>' ;
	$(".pushproblem").each(function(index){
        var url = location.href;
     //   console.log(url);        console.log(marketHost);
        if (url.startWith(marketHost)) {
            return;
        }
		var title_md5=$(this).attr('title_md5');
		var _self = $(this);
        $.post("../market/hasProblem.php",
                {title_md5:title_md5},
                function(data,status){
					if(data == 2)
						_self.attr('title',"可以推送");
					else if(data == 1)
						_self.val("已存在");
					else
						_self.val("异常");
					if(iGetInnerText(data) !='2')_self.attr("disabled",true);	else _self.attr("disabled",false);
                    //_self.attr("disabled",false);
					console.log(data);
					//alert(data);
                }
        );
	});
    $(".pushproblem").click(function(){
        var problem_id=$(this).attr('problem-id');
		var _self = $(this);
        $.post("../market/problem_push_by_Id_ajax.php",
                {problem_id:problem_id},
                function(data,status){
					if(iGetInnerText(data) =='ok')
						_self.val('ok');
					else
						_self.val('no');
					_self.attr('title',data);
					console.log(data);
				//	alert(data);
                }
        );
  });
});
</script>
</div>

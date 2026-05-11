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

  <center>
<table class="table table-striped">
    <caption style="text-align: center;font-size: 20px">代码分享列表，<a href="./sharecodepage.php">立即分享</a></caption>
    <tr>
        <th>编号</th>
        <th>标题</th>
        <th>语言</th>
        <th>分享时间</th>
        <th>操作</th>
    </tr>
    <?php if($pageNum == 0){ ?>
        <tr><td colspan="4"><center>您暂未分享任何代码,<a href="./sharecodepage.php">立即分享</a></center></td></tr>
    <?php }?>
    <?php
    foreach($share_list as $share_info){
        echo "<tr>";
        echo "<td><a href='./sharecodepage.php?sid=".$share_info["share_id"]."'>".$share_info["share_id"]."</a></td>";
        echo "<td><a href='./sharecodepage.php?sid=".$share_info["share_id"]."'>".$share_info["title"]."</a></td>";
        echo "<td>".$share_info["language"]."</td>";
        echo "<td>".$share_info["share_time"]."</td>";
        echo "<td><div class=\"btn-group btn-group-sm\" role=\"group\" aria-label=\"Default button group\">
                <button type=\"button\" class=\"btn btn-default btn-info\" onclick='seeCode(".$share_info["share_id"].")'>查看</button>
                <button type=\"button\" class=\"btn btn-default btn-danger\" onclick='deleteCode(".$share_info["share_id"].")'>删除</button>
                </div></td>";
        echo "</tr>";
    }
    ?>
</table>
      <?php if($pageNum > 1){ ?>
      <nav aria-label="...">
          <ul class="pagination">
              <li><a href="./sharecodelist.php?page=1" aria-label="Previous"><span aria-hidden="true">«</span></a></li>
              <?php for($i=1;$i<=$pageNum;$i++){
                  echo "<li><a href='./sharecodelist.php?page=".$i."'>".$i."</a></li>";
              }?>
              <li><a href="./sharecodelist.php?page=<?php echo $pageNum?>" aria-label="Next"><span aria-hidden="true">»</span></a></li>
          </ul>
      </nav>
      <?php }?>
</center>
    </div>
    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php include("template/$OJ_TEMPLATE/js.php");?>
    <script>
        function deleteCode(sid) {
            if(isNaN(sid)){
                return;
            }
            if(!confirm("您确定要删除此代码？")){
               return;
            }
            $.ajax({
                type: "POST",
                url: "./sharecodepage.php",
                data: {
                    "delete": sid,
                },
                success: function(data){
                    console.log(data);
                    alert(data.msg);
                    if(data.status=="success"){
                        window.location.reload();
                    }
                }
            });
        }
        function seeCode(sid) {
            window.location.href="./sharecodepage.php?sid="+sid;
        }
    </script>
  </body>
</html>
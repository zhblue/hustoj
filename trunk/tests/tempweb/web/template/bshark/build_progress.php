<!DOCTYPE html>
<html xmlns="http:// www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>开发进度 - MasterOJ</title>
        <?php require("./template/bshark/header-files.php");?>
    </head>
    
    <body>
        <?php require("./template/bshark/nav.php");?>
        <div class="card" style="margin: 3% 10% 5% 10%">
  <div class="card-body">
      <h4><?php echo $OJ_NAME;?> With BShark</h4>
    <img src="./template/bshark/pics/logo.png">
    <p>基于Bootstrap4编写。采用模板shards。由Yemaster开发<br>
    Shards Demo:<a href="https://designrevision.com/demo/shards/">https://designrevision.com/demo/shards/</a></p>
    <h4>
        开发进度(实时更新,请持续关注)
    </h4>
    <table class="table table-hover">
        <tbody>
            <tr>
                <th width=20>前端剩余时间</th>
                <td width=80 id="PreTime">已经完成。</td>
                <th width=20>后端剩余时间</th>
                <td width=80 id="BackTime">已经完成</td>
            </tr>
        </tbody>
        
    </table>
</div>
    </div>
<?php require("./template/bshark/footer.php");?>
<?php require("./template/bshark/footer-files.php");?>
    </body>
</html>

<?php
require_once(dirname(__FILE__) ."/../include/db_info.inc.php");
if (!isset($_SESSION[$OJ_NAME.'_'.'administrator']))
{
    echo "<a href='../loginpage.php'>Please Login First!</a>";
    exit(1);
}

if(isset($_POST['config']))
{
    $filename = dirname(__FILE__)."/../config/system.conf.php";
    $config = file_get_contents($filename);
    $OJ_NAME_POST = htmlentities(stripslashes($_POST['OJ_NAME']), ENT_QUOTES);
    if($OJ_NAME_POST) {
        $pattern = "/OJ_NAME=\"(.*?)\"/";
        $replacement = "OJ_NAME=\"". $OJ_NAME_POST."\"";
        $config=preg_replace($pattern,$replacement,$config);
        file_put_contents($filename,$config);
        echo '保存成功';
    }
    else
        echo '保存失败';
    exit(1);
}
require("admin-header.php");
if(isset($OJ_LANG)){
    require_once("../lang/$OJ_LANG.php");
}
//$str = file_get_contents($filename);

echo "<title>System Config</title>";
echo "<center><h2>System Config</h2></center>";
?>
<div class="container-fluid">
    <div class="row-fluid">
        <div class="span12">
            <form id="myform" method="post" role="form" >
                <div class="form-group">
                    <label for="OJ_NAME">系统标题</label>
                    <input type="text" class="form-control" id="OJ_NAME" placeholder="请输入系统标题" value="<?php echo $OJ_NAME ?>">
                </div>
                <button id="submit" type="button" class="btn btn-default">Submit</button>
            </form>
        </div>
    </div>
</div>
<?php
require("../oj-footer.php");
?>
<script>
    $(function(){
        $('#submit').click(function(){
            $.post('config.php',{config:"ajax",OJ_NAME:$('#OJ_NAME').val()},function(result){
            //    alert(result);
                console.log(result);
            });
        })
    })
</script>


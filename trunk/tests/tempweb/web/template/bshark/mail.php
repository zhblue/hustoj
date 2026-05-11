<?php 
$user_id = $_SESSION[$OJ_NAME.'_'.'user_id'];
$sql = 'SELECT * FROM `mail` WHERE `from_user`=? OR `to_user`=? ORDER BY unix_timestamp(in_date) DESC';
$chats = pdo_query($sql, $user_id, $user_id);
$friend="请选择一个人聊天";
if ($_GET["friend"]) {
    $friend = $_GET['friend'];
    $is_friend = count(pdo_query('SELECT * FROM `users` WHERE `user_id`=?',$friend))>0?1:0;
    if ($is_friend) $friend = pdo_query('SELECT * FROM `users` WHERE `user_id`=?',$friend)[0]['user_id'];
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title><?php echo $MSG_MAIL;?> - <?php echo $OJ_NAME; ?></title>
        <?php require("./template/bshark/header-files.php");?>
            <style type="text/css">
        .item-right {
            background: #12B7F5;
            margin-right: 10px;
            width:200px;
            float: right;
            color: #fff;
            border-radius: 5px;
        }
        .item-left {
            background: #E5E5E5;
            margin-left: 10px;
            width:200px;
            float: left;
            color: #000;
            border-radius: 5px;
        }
        .userlist {
    list-style-type: none;
    margin: 0;
    padding: 0;
    width: 100%;
    background-color: #fcfcfc;
}
 
.userlist li a {
    display: block;
    color: #000;
    padding: 8px 16px;
    text-decoration: none;
}
 
.userlist li a:hover {
    background-color: #f1f1f1;
    color: #000;
}
 
.active {
    background-color: #f1f1f1;
    color: #000;
}
        .info-tip{
            background:rgba(250,60,0,1);
            position:absolute;
            width:10px;
            height:10px;
            top:0;
            right:0;
            -webkit-border-radius: 50%;
            -moz-border-radius: 50%;
            border-radius:50%;
        }
        .w-e-text-container{
    height: 160px !important;
    opacity: 1
}
a {
    color: black;
}
a:hover {
    color: #121212;
}
    </style>
    </head>
    
    <body>
        <?php require("./template/bshark/nav.php");?>
        <div class="ui container bsharkMain">
        <div class="ui stackable grid">
            <div class="eleven wide column">
                <div class="card">
                    <div class="card-body" style="padding: 0">
                        <div>
                            <?php 
                                if ($_POST["content"]) {
                                    $from_user = $user_id;
                                    $to_user = $_POST["friend"];
                                    $content = $_POST["content"];
                                    $content = str_replace("<p>","",$content);
                                    $content = str_replace("</p>","",$content);
                                    $title = '无标题!';
                                    $in_date = date('Y-m-d H:i:s');
                                    $new_mail = 1;
                                    $reply = 0;
                                    $defunct = 'N';
                                    pdo_query('INSERT INTO `mail`(`to_user`, `from_user`, `title`, `content`, `new_mail`, `reply`, `in_date`, `defunct`) VALUES (?,?,?,?,?,?,?,?)',$to_user,$from_user,$title,$content,$new_mail,$reply,$in_date,$defunct);
                                    $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ?"https://": "http://";
                                    $url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                                    header("Location:".$url);
                                }
                            ?>
                            <div style="width:100%;line-height:center;height:36px;background-color:#2185d0;display:inline-block"><p style="color:#fff;font-size:20px;margin: 0;line-height: 36px;" align=center><span><?php 
                            if ($friend) echo $friend;
                            else echo "暂无联系人";
                            ?></span></p></div>
                            <div style="width:100%;height:500px;">
                                <div style="width:36%;border:1px solid #aaa;float: left;height: 100%;">
                                    <ul class="userlist" style="height:50px;background-color:#fff">
                                        <li>
                                            <a>
                                                <form>
                                                    <div class="ui left icon mini input">
                                                        <input type="text" name="friend" placeholder="<?php echo "输入" . $MSG_USER_ID . "开始聊天";?>">
                                                        <i class="user icon"></i>
                                                    </div>
                                                </form>
                                            </a>
                                        </li>
                                    </ul>
                                    <ul class="userlist" style="overflow:auto;height:450px;">
                                        <?php 
                                            $kkk = Array();
                                        foreach ($chats as $row) {
                                            ?>
                                            <li><?php 
                                                if ($row['from_user']==$user_id) $this_f = $row['to_user'];
                                                else $this_f = $row['from_user'];
                                                if ($kkk[strtolower($this_f)]=='666') continue;
                                                else $kkk[strtolower($this_f)]='666';
                                                ?>
                                                <a href="?friend=<?php echo $this_f;?>" class="<?php if ($this_f==$friend) echo 'active';?>"><div style="position:relative;width:42px;height:42px;display:inline;"><img src="<?php $logopng = 'http://q.qlogo.cn/headimg_dl?dst_uin=1440169768&spec=160';echo $logopng;?>" width=42 height=42 style="border:1px solid #222;display:inline;border-radius:50%;float:left;">
                                                <?php 
                                                if (count(pdo_query('select * from `mail` where `from_user`=? and `to_user`=? and `new_mail`=1',$this_f, $user_id))>0) {?>
                                                <span class="info-tip"></span><?php
                                                }?>
                                                </div><?php
                                                echo '<div style="margin-left:55px"><b>'.$this_f.'</b><span style="float:right">'.''.'</span><br />';
                                                $this_info=pdo_query('SELECT * FROM `mail` WHERE (`from_user`=? AND `to_user`=?) OR (`to_user`=? AND `from_user`=?) ORDER BY unix_timestamp(in_date) DESC LIMIT 1',$user_id,$this_f,$user_id,$this_f)[0];
                                                $this_info['content'] =preg_replace('/\<img[\s\S]*\>/i','[图片]',$this_info['content']);
                                                $this_info['content'] =preg_replace('/\<pre[\s\S]*\>[\s\S]*\<\/pre\>/i','[代码]',$this_info['content']);
                                                $this_info['content'] =preg_replace('/\<a[\s\S]*\>[\s\S]*\<\/a\>/i','[链接]',$this_info['content']);
                                                $this_info['content'] =preg_replace('/\<table[\s\S]*\>[\s\S]*\<\/table\>/i','[表格]',$this_info['content']);
                                                $this_info['content'] = strip_tags($this_info['content']);  
                                                echo substr($this_info['content'],0,18).'...</div>';
                                                ?></a>
                                            </li>
                                            <?php 
                                        }
                                        ?>
                                    </ul>
                                </div>
                                <div style="width:64%;border:1px solid #ccc;position:relative;float:left;height: 100%;">
                                    <div style="width:98%;height:290px;overflow:auto" id='infos'>
                                        <?php 
                                            if ($friend && $is_friend) {
                                                pdo_query('UPDATE `mail` SET `new_mail`=0 WHERE `from_user`=? AND `to_user`=?',$friend,$user_id);
                                                $chats_i = pdo_query('select * from `mail` where (`to_user`=? and `from_user`=?) or (`from_user`=? and `to_user`=?) order by unix_timestamp(`in_date`)',$user_id,$friend,$user_id,$friend);
                                                foreach ($chats_i as $chat_id=>$chat_r) {
                                                    if ($chat_r['from_user']==$user_id) {
                                                        $say = 1;
                                                        $this_u = $user_id;
                                                    }
                                                    else {
                                                        $say = 0;
                                                        $this_u=$friend;
                                                    }
                                                    $logopng = 'http://q.qlogo.cn/headimg_dl?dst_uin=1440169768&spec=160';
                                                    //if ($chat_id != 0 && strtotime($chat_r['in_date'])-strtotime($chats_i[$chat_id-1]['in_date'])>30*60) {
                                                    ?>
                                                    <span style="display:block;text-align:center"><?php echo $chat_r['in_date'];?></span>
                                                    <?php // } ?><!--br-->
                                                    <div style="width:100%;float:none;display:block">
                                                    <a href="userinfo.php?user=<?php echo $this_u;?>"><img src="<?php echo $logopng;?>" width=32 height=32 style="border:1px solid #000;border-radius:50%;float:<?php if ($say==0) echo 'left';else echo 'right';?>"></a>
                                                    <div class="item-<?php if ($say==0) echo 'left';else echo 'right';?>"><p style="margin:6px"><?php echo $chat_r['content'];?></p></div>
                                                    </div>
                                                    
<div style="clear:both;"></div> 
                                                    <?php
                                                }
                                            }
                                            else {
                                                echo "<h2><b>暂无联系人或联系人不存在</b></h2>从列表中选择联系人，或者输入用户名后按回车直接聊天";
                                            }
                                        ?>
                                    </div>
                                    <div style="bottom:0px;right:0;position:absolute;width:100%;height:190px">
                                    <?php if ($friend && $is_friend) {
                                      ?><form action="" method="post" style="width:100%"><div id="editor" style="width:100%;height:100%;position:absolute;width:auto;right:0;bottom:0;left:0"></div><button class="btn btn-info btn-sm" style="position:absolute;width:auto;right:10px;bottom:10px" onclick='aa.value=editor.txt.html();' type=submit>发送</button>
                                      <input id="aa" name="content" type=hidden><input name="friend" type=hidden value="<?php echo $friend;?>"></form><?php  
                                    }
                                    ?>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php require("./template/bshark/footer.php");?>
<?php require("./template/bshark/footer-files.php");?>
    <script type="text/javascript">
        var E = window.wangEditor
        var editor = new E('#editor')
        
    editor.customConfig.zIndex = 0
    editor.customConfig.menus = [
        //'head',
        'bold',
        //'italic',
        //'underline',
        'link',
        //'list',
        //'quote',
        //'table',
        'image',
        'emoticon',
        'code'
    ]
        // 或者 var editor = new E( document.getElementById('editor') )
        editor.create()
    </script>
                <script type="text/javascript">
                    var div = document.getElementById('infos');
                    div.scrollTop = div.scrollHeight;
            </script>
    </body>
</html>

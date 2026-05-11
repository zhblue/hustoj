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
      <?php if($readOnly) {?>
          <br>
          <span class="label label-primary">author:</span> <a href="userinfo.php?user=<?php echo $author ?>"><?php echo $author ?></a>
          <span class="label label-primary">language:</span> <?php echo  $language ?>
          <span class="label label-primary">time:</span> <?php echo $share_time ?>
          <br>
      <?php } ?>

<?php if(!$readOnly){?>
<div><label for="#title">Title</label><input type="text" id="title" value="<?php echo $title?>"></div>
<span id="language_span">Language:
<select id="language" name="language" >
    <option value="c_cpp">C/C++</option>
    <option value="java">Java</option>
    <option value="python">Python</option>
</select>
<?php echo $MSG_VCODE?>:
<input id="vcode"  size=4 type=text><img id="vimg"alt="click to change" src="vcode.php" onclick="this.src='vcode.php?'+Math.random()">
<?php }?>

<br>
</span>

    <pre id="code" cols=180 rows=20 class="ace_editor" style="min-height:600px;width: 80%"><textarea id="source" class="ace_text-input"><?php echo htmlentities($view_src,ENT_QUOTES,"UTF-8")?></textarea></pre>
    <?php if(!$readOnly){?>
        <button class="btn btn-info" type="button" onclick="submitCode()"><?php echo $MSG_SUBMIT?></button>
    <?php } ?>
    <?php if($isOwner){?>
      <button  class="btn btn-danger" type="button" onclick="deleteCode(<?php echo $sid?>)">DELETE</button>
    <?php } ?>
</center>
    </div>
    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php include("template/$OJ_TEMPLATE/js.php");?>

    <script src="<?php echo $OJ_CDN_URL?>ace/ace.js"></script>
    <script src="<?php echo $OJ_CDN_URL?>ace/ext-language_tools.js"></script>
    <script>
        $("#language").on("change",changeLanguage);
        //初始化对象
        editor = ace.edit("code");

        //设置风格和语言（更多风格和语言，请到github上相应目录查看）
        theme = "clouds"
        language = "c_cpp"
        editor.setTheme("ace/theme/" + theme);
        editor.session.setMode("ace/mode/" + language);

        //字体大小
        editor.setFontSize(18);

        //设置只读（true时只读，用于展示代码）
        editor.setReadOnly(<?php echo $readOnly ?>);

        //自动换行,设置为off关闭
        editor.setOption("wrap", "free")

        //启用提示菜单
        ace.require("ace/ext/language_tools");
        editor.setOptions({
            enableBasicAutocompletion: true,
            enableSnippets: true,
            enableLiveAutocompletion: true
        });
        function changeLanguage(){
            var lan = $("#language").val();
            editor.session.setMode("ace/mode/" + lan);
        }

        <?php if(!$readOnly){?>
        function submitCode() {
            var code = editor.getValue();
            var title=$("#title").val().trim();
            var vcode = $("#vcode").val().trim();
            var language = $("#language").val();
            if(vcode.length===0){
                alert("请输入验证码！");
                return;
            }
            if(code.length===0){
                alert("请输入代码！");
                return;
            }
            console.log(code);
            $.ajax({
                type: "POST",
                url: "./sharecodepage.php",
                data: {
                    <?php if($isOwner){
                    echo '"sid": '.$sid.',';
                    }?>
                    "title":title,
                    "code": code,
                    "vcode": vcode,
                    "language": language
                },
                success: function(data){
                    console.log(data);
                    if(data.status=="success"){
                        window.location.href = "./sharecodepage.php?sid="+data.data.sid;
                    }else {
                        $("#vcode").val('')
                        $("#vimg").click();
                        alert(data.msg);
                    }
                }
            });
        }
        <?php }?>
        <?php if($isOwner){?>
        <?php //如果是作者本人浏览代码，会显示选择语言的selector 这个时候就要初始化一下selector了 ?>
        editor.session.setMode("ace/mode/<?php echo $language?>");
        $("#language").val("<?php echo $language?>");
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
                        window.location.href = "./sharecodelist.php";
                    }
                }
            });
        }
        <?php } ?>
    </script>

  </body>
</html>

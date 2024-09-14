<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $MSG_ERROR_INFO; ?> - <?php echo $OJ_NAME; ?></title>
    <?php require("./template/bshark/header-files.php"); ?>
</head>

<body>
    <?php require("./template/bshark/nav.php"); ?>
    <div class="ui container bsharkMain">
        <div class="card">
            <div class="card-body">
                <h2><?php echo $MSG_ERROR_INFO; ?></h2>
                <div class="card" style="box-shadow:0px 2px 4px #ddd;">
                    <div class="card-body">
                        <pre><code><?php echo $view_reinfo ?></code></pre>
                    </div>
                </div>
                <span style="display:none" id="errtxt"><?php echo $view_reinfo; ?></span><br>
                <button class="ui button" onclick="explain();this.style.display='none';">显示辅助信息</button>
                <div id='errexp'></div>
                <script>
                    var pats = new Array();
                    var exps = new Array();
                    pats[0] = /A Not allowed system call.* /;
                    exps[0] = "<?php echo $MSG_A_NOT_ALLOWED_SYSTEM_CALL ?>";
                    pats[1] = /Segmentation fault/;
                    exps[1] = "<?php echo $MSG_SEGMETATION_FAULT ?>";
                    pats[2] = /Floating point exception/;
                    exps[2] = "<?php echo $MSG_FLOATING_POINT_EXCEPTION ?>";
                    pats[3] = /buffer overflow detected/;
                    exps[3] = "<?php echo $MSG_BUFFER_OVERFLOW_DETECTED ?>";
                    pats[4] = /Killed/;
                    exps[4] = "<?php echo $MSG_PROCESS_KILLED ?>";
                    pats[5] = /Alarm clock/;
                    exps[5] = "<?php echo $MSG_ALARM_CLOCK ?>";
                    pats[6] = /CALLID:20/;
                    exps[6] = "<?php echo $MSG_CALLID_20 ?>";
                    pats[7] = /ArrayIndexOutOfBoundsException/;
                    exps[7] = "<?php echo $MSG_ARRAY_INDEX_OUT_OF_BOUNDS_EXCEPTION ?>";
                    pats[8] = /StringIndexOutOfBoundsException/;
                    exps[8] = "<?php echo $MSG_STRING_INDEX_OUT_OF_BOUNDS_EXCEPTION ?>";
                    function explain() {
                        var errmsg = $("#errtxt").text();
                        var expmsg = "";
                        for (var i = 0; i < pats.length; i++) {
                            var pat = pats[i];
                            var exp = exps[i];
                            var ret = pat.exec(errmsg);
                            if (ret) {
                                expmsg += ret + " : " + exp + "<br><div class='ui header'></div>";
                            }
                        }
                        document.getElementById("errexp").innerHTML = expmsg;
                    }

                    explain();
                </script>
<?php if(isset($OJ_MARKDOWN)&&$OJ_MARKDOWN){ ?>
          <script src="<?php echo $OJ_CDN_URL.$path_fix."template/bs3/"?>marked.min.js"></script>
        <script>
            $(document).ready(function(){
                        marked.use({
                          // 开启异步渲染
                          async: true,
                          pedantic: false,
                          gfm: true,
                          mangle: false,
                          headerIds: false
                        });
                        $("#errtxt").each(function(){
                                $(this).html(marked.parse($(this).html()));
                        });
                        // adding note for ```input1  ```output1 in description
                        for(let i=1;i<10;i++){
                                $(".language-input"+i).parent().before("<div><?php echo $MSG_Input?>"+i+":</div>");
                                $(".language-output"+i).parent().before("<div><?php echo $MSG_Output?>"+i+":</div>");
                        }
        
        
                $("#errtxt table").addClass("ui mini-table cell striped");
                $("#errtxt table tr:odd td").css({
                    "border": "1px solid grey",
                    "text-align": "center",
                    "width": "200px",
                    "background-color": "#8521d022",
                    "height": "30px"
                });
                $("#errtxt table tr:even td").css({
                    "border": "1px solid grey",
                    "text-align": "center",
                    "width": "200px",
                    "background-color": "#2185d022",
                    "height": "30px"
                });
                $("#errtxt table th").css({
                    "border": "1px solid grey",
                    "width": "200px",
                    "height": "30px",
                    "background-color": "#2185d088",
                    "text-align": "center"
                });
                <?php
                  if(isset($OJ_DOWNLOAD)&&$OJ_DOWNLOAD){
                    if(isset($OJ_DL_1ST_WA_ONLY) && $OJ_DL_1ST_WA_ONLY){
                ?>
                   let down=$($("#errtxt").find("h2")[0]);
                   let filename=down.text();
                   down.html("<a href='download.php?sid=<?php echo $id?>&name=" + filename+ "'>" + filename+ "</a>");
                <?php
                    }else{
                ?>
                   $("#errtxt").find("h2").each(function(){
                           let down=$(this);
                           let filename=down.text();
                           console.log(filename);
                           down.html("<a href='download.php?sid=<?php echo $id?>&name=" + filename+ "'>" + filename+ "</a>");
                   });
        
                <?php
                    }
                  }
                ?>
                $("th").each(function(){
                        let html=$(this).html();
                        html=html.replace("Expected","<?php echo $MSG_EXPECTED ?>");
                        html=html.replace("Yours","<?php echo $MSG_YOURS ?>");
                        html=html.replace("filename","<?php echo $MSG_FILENAME ?>");
                        html=html.replace("size","<?php echo $MSG_SIZE ?>");
                        html=html.replace("result","<?php echo $MSG_RESULT ?>");
                        html=html.replace("memory","<?php echo $MSG_MEMORY?>");
                        html=html.replace("time","<?php echo $MSG_TIME ?>");
                        $(this).html(html);
        
                });
        
            });
        
        </script>

<?php } ?>
            </div>
        </div>
    </div>
    <?php require("./template/bshark/footer.php"); ?>
    <?php require("./template/bshark/footer-files.php"); ?>
</body>

</html>

<?php if (__FILE__ === $_SERVER['SCRIPT_FILENAME']) {
    // 当前文件就是用户直接请求的入口脚本
    http_response_code(403);
    exit('Direct access is not allowed.');
}
$show_title=$id." - $MSG_ERROR_INFO - $OJ_NAME";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="">
  <meta name="author" content="">
  <link rel="icon" href="../../favicon.ico">

  <title><?php echo $show_title?></title>
  <?php include("template/$OJ_TEMPLATE/css.php");?>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="http://cdn.bootcss.com/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

  <!-- 数学公式: AI 答疑的回答里常带公式, 与 syzoj 一致加载 MathJax -->
  <script>
  MathJax = {
    startup : { typeset: false  } ,
    tex: {inlineMath: [['$', '$'], ['\\(', '\\)']]}
  };
  </script>
  <script id="MathJax-script" async src="template/<?php echo $OJ_TEMPLATE?>/tex-chtml.js"></script>
  <style>
  td > code {
      color:red;
      white-space:nowrap;
  }
  #errtxt{margin-top:0;margin-bottom:0;}
  </style>
</head>

<body>

  <div class="container">
    <?php include("template/$OJ_TEMPLATE/nav.php");?>
    <!-- Main component for a primary marketing message or call to action -->
    <div class="jumbotron">

      <p><strong><?php echo $MSG_ERROR_INFO?></strong></p>
      <pre id='errtxt' class="alert alert-error"><?php echo $view_reinfo?></pre>

      <p><strong><?php echo $MSG_INFO_EXPLAINATION?></strong></p>
      <div id='errexp'></div>

    </div>

  </div> <!-- /container -->

<!-- Bootstrap core JavaScript
  ================================================== -->
  <!-- Placed at the end of the document so the pages load faster -->
  <?php include("template/$OJ_TEMPLATE/js.php");?>
<?php if(!isset($OJ_MARKDOWN)||!$OJ_MARKDOWN){ // js.php 仅在开启 markdown 时加载 marked, 而 AI 答疑始终需要它 ?>
<script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE/"?>marked.min.js"></script>
<?php } ?>

  <script>
    var pats=new Array();
    var exps=new Array();
    pats[0]=/A Not allowed system call/;
    exps[0]="<?php echo $MSG_A_NOT_ALLOWED_SYSTEM_CALL ?>";
    pats[1]=/Segmentation fault/;
    exps[1]="<?php echo $MSG_SEGMETATION_FAULT ?>";
    pats[2]=/Floating point exception/;
    exps[2]="<?php echo $MSG_FLOATING_POINT_EXCEPTION ?>";
    pats[3]=/buffer overflow detected/;
    exps[3]="<?php echo $MSG_BUFFER_OVERFLOW_DETECTED ?>";
    pats[4]=/Killed/;
    exps[4]="<?php echo $MSG_PROCESS_KILLED ?>";
    pats[5]=/Alarm clock/;
    exps[5]="<?php echo $MSG_ALARM_CLOCK ?>";
    pats[6]=/CALLID:20/;
    exps[6]="<?php echo $MSG_CALLID_20 ?>";
    pats[7]=/ArrayIndexOutOfBoundsException/;
    exps[7]="<?php echo $MSG_ARRAY_INDEX_OUT_OF_BOUNDS_EXCEPTION ?>";
    pats[8]=/StringIndexOutOfBoundsException/;
    exps[8]="<?php echo $MSG_STRING_INDEX_OUT_OF_BOUNDS_EXCEPTION ?>";
    pats[9]=/Binary files/;
    exps[9]="<?php echo $MSG_WRONG_OUTPUT_TYPE_EXCEPTION ?>";
    pats[10]=/non-zero return/;
    exps[10]="<?php echo $MSG_NON_ZERO_RETURN ?>";

// MathJax 的配置在 <head> 里一次性给出, 这里不再重复赋值:
// tex-chtml.js 是 async 加载, 重复赋值会和它的读取时机产生竞态。
// 统一的 MathJax 排版函数：等待 MathJax 异步加载完成后再执行，避免竞态
function renderMathJax() {
  if (window.MathJax && MathJax.startup && MathJax.startup.promise) {
    // MathJax 已就绪，等其启动完成后排版
    MathJax.startup.promise.then(function() {
      try { MathJax.typeset(); } catch (e) { console.error('MathJax typeset error:', e); }
    });
  } else if (window.MathJax && typeof MathJax.typeset === 'function') {
    try { MathJax.typeset(); } catch (e) { console.error('MathJax typeset error:', e); }
  } else {
    // MathJax 仍在异步加载（async 脚本可能晚于此处执行），轮询重试直到就绪
    setTimeout(renderMathJax, 300);
  }
}
function fill_data(data){
    // AI 回答会整体替换 #errexp, 先把按钮摘出来, 渲染完再放回去,
    // 否则 pull_result 里的 $('#ai_bt') 会落空(syzoj 原版即如此)。
    var bt = document.getElementById('ai_bt');
    var btHtml = bt ? bt.outerHTML : '';
    $("#errexp").html(data);
    $("#errexp").html(marked.parse($("#errexp").text()));
    if (btHtml) $("#errexp").append(btHtml);
    // MathJax 是 async 加载, 统一走 renderMathJax() 等待就绪, 不直接调 typesetPromise
    setTimeout(renderMathJax, 200);
}
function pull_result(id){
	console.log(id);
    $.ajax({
	url: '../aiapi/ajax.php', 
	type: 'GET',
	data: { id: id },
	success: function(data) {
		if(data=='waiting'){
			window.setTimeout('pull_result('+id+')',2000);
		}else{
			fill_data(data);
		    $('#ai_bt').val('再来一次');
		    $('#ai_bt').prop('disabled', false);
		}
	},
	error: function() {
	    $('#ai_bt').val('获取数据失败');
	    $('#ai_bt').prop('disabled', false);
	}
    });
}
   function explain(){
      var errmsg = $("#errtxt").text();
      var expmsg = "";
      for(var i=0; i<pats.length; i++){
        var pat = pats[i];
        var exp = exps[i];
        var ret = pat.exec(errmsg);
        if(ret){
          expmsg += ret+" : "+exp+"<br><hr />";
        }
      }
       <?php if (!$isAC && isset($OJ_AI_API_URL)&&!empty($OJ_AI_API_URL) && $coin >0){ ?>
                   expmsg+="<input type=button id='ai_bt' class='btn btn-primary' onclick='ai_explain()' value='<?php echo $MSG_PAY.$OJ_AI_API_PRICE."💰".$MSG_COIN.$MSG_USE.$MSG_AI_HELP ?>'>";
        <?php } ?>
      document.getElementById("errexp").innerHTML=expmsg;
    }
    explain();
    function ai_explain(){

       <?php
                if (!$isAC && isset($OJ_AI_API_URL)&&!empty($OJ_AI_API_URL)){ ?>
                let expmsg=document.getElementById("errexp").innerHTML;
                expmsg+="AI 答疑 ...<img src='image/loader.gif'>";
                $("#errexp").html(expmsg);
                $('#ai_bt').prop('disabled', true);
                    $.ajax({
                        url: '<?php echo $OJ_AI_API_URL ?>?sid=<?php echo $id?>',
                        type: 'GET',
                        success: function(data) {
                                if(parseInt(data)>0)
                                        window.setTimeout('pull_result('+data+')',2000);
                                else{
                                        fill_data(data);
                                }
                        },
                        error: function() {
                            console.log('获取数据失败');
                        }
                    });
       <?php } ?>
    }
  </script>

<?php if(isset($OJ_MARKDOWN)&&$OJ_MARKDOWN){ ?>
  <script>
    $(document).ready(function(){
                marked.use({
                  // 开启异步渲染
                  async: false,
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
           // "text-align": "center",
            "width": "200px",
            "background-color": "#8521d022",
            "height": "30px"
        });
        $("#errtxt table tr:even td").css({
            "border": "1px solid grey",
           // "text-align": "center",
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
          if(isset($OJ_DOWNLOAD)&&$OJ_DOWNLOAD && $coin>0 ){
            if(isset($OJ_DL_1ST_WA_ONLY) && $OJ_DL_1ST_WA_ONLY){
        ?>
           let down=$($("#errtxt").find("h2")[0]);
           let filename=down.text();
           down.html("<a href='download.php?sid=<?php echo $id?>&name=" + filename+ "'>💰" + filename+ "</a>");
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

</body>
</html>

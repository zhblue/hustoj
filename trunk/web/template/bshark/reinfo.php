<?php
if (__FILE__ === $_SERVER['SCRIPT_FILENAME']) {
    // 当前文件就是用户直接请求的入口脚本
    http_response_code(403);
    exit('Direct access is not allowed.');
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo $MSG_ERROR_INFO; ?> - <?php echo $OJ_NAME; ?></title>
    <?php require("./template/bshark/header-files.php"); ?>
    <script>
  MathJax = {
    startup : { typeset: false  } ,
    tex: {inlineMath: [['$', '$'], ['\\(', '\\)']]}
  };
    </script>
    <script id="MathJax-script" async src="template/<?php echo $OJ_TEMPLATE?>/tex-chtml.js"></script>
    <?php if(!isset($OJ_MARKDOWN)||!$OJ_MARKDOWN){ // AI 答疑的 markdown 渲染始终需要 marked ?>
    <script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE/"?>marked.min.js"></script>
    <?php } ?>

</head>

<body>
    <?php require("./template/bshark/nav.php"); ?>
<style>
td > code {
    color:red;
}
</style>
    <div class="ui container bsharkMain">
        <div class="card">
            <div class="card-body">
                <h2><?php echo $MSG_ERROR_INFO; ?></h2>
                <div class="card" style="box-shadow:0px 2px 4px #ddd;">
                    <div class="card-body">
                        <div id='errtxt'><?php echo $view_reinfo ?></div>
                    </div>
                </div>

                <button class="ui button" onclick="explain();this.style.display='none';">显示辅助信息</button>
                <div id='errexp'></div>
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
    // AI 回答会整体替换 #errexp, 先把"再来一次"按钮摘出来, 渲染完再放回去,
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
                   expmsg+="<input type=button id='ai_bt' class='ui button primary' onclick='ai_explain()' value='<?php echo $MSG_PAY.$OJ_AI_API_PRICE."💰".$MSG_COIN.$MSG_USE.$MSG_AI_HELP ?>'>";
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
          <script src="<?php echo $OJ_CDN_URL.$path_fix."template/$OJ_TEMPLATE/"?>marked.min.js"></script>
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

            </div>
        </div>
    </div>
    <?php require("./template/bshark/footer.php"); ?>
    <?php require("./template/bshark/footer-files.php"); ?>
</body>

</html>

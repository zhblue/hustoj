<?php $show_title=$id." - $MSG_ERROR_INFO - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<script src="template/<?php echo $OJ_TEMPLATE?>/js/textFit.min.js"></script>
<style>
.single-subtask {
    box-shadow: none !important;
}

.single-subtask > .title {
    display: none;
}

.single-subtask > .content {
    padding: 0 !important;
}

.accordion > .content > .accordion {
    margin-top: 0;
    margin-bottom: 0;
}

.accordion > .content > .accordion > .content {
    margin-top: 0;
    margin-bottom: 14px;
}

.accordion > .content > .accordion > .content > :last-child {
    margin-bottom: -10px !important;
}
</style>
<div class="padding">
    <div style="margin-top: 0px; margin-bottom: 14px; " v-if="content != null && content !== ''">
    <p class="transition visible">
           <strong ><?php echo "$MSG_ERROR_INFO";?> </strong>
        </p>
        <div class="ui existing segment">
          <pre v-if="escape" style="margin-top: 0; margin-bottom: 0; " id="errtxt"><code><?php echo $view_reinfo?></code></pre>
        </div>
    </div>

    <div style="margin-top: 0px; margin-bottom: 14px; " v-if="content != null && content !== ''">
    <p class="transition visible">
           <strong ><?php echo "$MSG_INFO_EXPLAINATION";?></strong>
        </p>
        <div class="ui existing segment">
          <pre v-if="escape" style="margin-top: 0; margin-bottom: 0; "><code><div id='errexp'></div></code></pre>
        </div>
        </div>
    </div>
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
      document.getElementById("errexp").innerHTML=expmsg;
    }



 function showDownload2(){
      var errmsg = $("#errtxt").html();

      // 从错误信息文本框中提取出第一组数据名称，并将其作为参数添加到下载链接中
      var first_data_name = /========\[(.*?)\]=========/.exec(errmsg)[1];
      errmsg=errmsg.replace(/========\[(.*)\]=========/, "<a href='download.php?sid=<?php echo $id?>&name=" + first_data_name + "'>" + first_data_name + "</a>");

      $("#errtxt").html(errmsg);
 }
 function showDownload(){
      var errmsg = $("#errtxt").html();
      errmsg=errmsg.replace(/========\[(.*)\]=========/g,"<a href='download.php?sid=<?php echo $id?>&name=$1'>$1</a>");
      $("#errtxt").html(errmsg);
 }


    explain();
    <?php
  if(isset($OJ_DOWNLOAD)&&$OJ_DOWNLOAD){
    if(isset($OJ_DL_1ST_WA_ONLY) && $OJ_DL_1ST_WA_ONLY){
     echo  "showDownload2()";
    }else{
     echo  "showDownload();";
    }
  }
?>
</script>

        <?php if(isset($OJ_MARKDOWN)&&$OJ_MARKDOWN){ ?>
          <script src="<?php echo $OJ_CDN_URL.$path_fix."template/bs3/"?>marked.min.js"></script>
<script>
    marked.use({
        async: true,
        pedantic: false,
        gfm: true,
        mangle: false,
        headerIds: false
    });
    $("#errtxt").each(function(){
                        raw=raw.replaceAll("\n","|\n|");
                        raw=raw.replaceAll("&gt;","|");
                        raw=raw.replaceAll("&lt;","|");
                        raw=raw.replace( /[\\\/]/g  ,"|");
                        raw=raw.replaceAll("      |","|");
                        raw=raw.replaceAll("|==============================|","\n");
                        raw=raw.replaceAll("\n||\n","\n");
                        raw=raw.replaceAll("Expected","期望输出");
                        raw=raw.replaceAll("Yours|","你的输出|\n|:----|:----|");
                        raw=raw.replaceAll("time_space_table:","文件|大小|结果|内存|耗时|\n|----|----|----|----|----");
                        raw=raw.replaceAll(".in ","|");
                        raw=raw.replaceAll(" bytes :","|");
                        raw=raw.replaceAll(" mem=","|");
                        raw=raw.replaceAll(" time=","|");

                        <?php if ($jresult==5 ) {?>
                                raw=raw.replaceAll(" ","<span class=blue>▯</span>");
                        <?php } ?>
                        $(this).html((raw));
                        $(this).html(marked.parse(raw));

    });
    $("#errtxt table tr td").css({
        "border": "1px solid grey",
        "text-align": "left",
        "width": "200px",
        "height": "30px"
    });

    $("#errtxt table th").css({
        "border": "1px solid grey",
        "width": "200px",
        "height": "30px",
        "background-color": "#9e9e9ea1",
        "text-align": "center"
    });
    $("#errtxt p").css({
        "margin": "1em 0em -1em 0em"
    });
</script>

    <?php } ?>


<?php include("template/$OJ_TEMPLATE/footer.php");?>

 


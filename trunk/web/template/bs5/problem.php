<?php
if ( $pr_flag ) {
    $show_title = $row['title']." - $MSG_PROBLEM - $OJ_NAME";
} else {
    $show_title = "$MSG_PROBLEM ".$PID[$pid].": ".$row['title']." - $OJ_NAME";
}
?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-4">
    <div class="card-header">
        <center>
            <?php
            if ( $pr_flag ) {
                echo "<h3>$id: " . $row[ 'title' ] . "</h3>";
                echo "<div align='right'><sub>[$MSG_Creator : <span id='creator'></span>]</sub></div>";
            } else {
                $id = $row[ 'problem_id' ];
                echo "<h3>$MSG_PROBLEM " . $PID[ $pid ] . ": " . $row[ 'title' ] . "</h3>";
                echo "<div align='right'><sub>[$MSG_Creater : <span id='creator'></span>]</sub></div>";
            }
            echo "<span class='text-success'>$MSG_Time_Limit : </span><span fd='time_limit' pid='".$row['problem_id']."'>" . $row[ 'time_limit' ] . "</span> sec &nbsp;&nbsp;";
            echo "<span class='text-success'>$MSG_Memory_Limit : </span>" . $row[ 'memory_limit' ] . " MiB &nbsp;&nbsp;";
            echo ($row['spj']?"<span class='badge bg-warning text-dark'>$MSG_SPJ</span>":"");
            ?>
        </center>
        <center class="mt-2">
            <div class="btn-group" role="group">
            <?php if($pr_flag){ ?>
                <a id='submit' class="btn btn-primary btn-sm" href='submitpage.php?id=<?php echo $id?>'>
                    <i class="bi bi-upload"></i> <?php echo $MSG_SUBMIT?>
                </a>
            <?php } else { ?>
                <?php if($contest_is_over){ ?>
                <a id='submit' class="btn btn-primary btn-sm" href='submitpage.php?id=<?php echo $id?>'>
                    <i class="bi bi-upload"></i> <?php echo $MSG_SUBMIT?>
                </a>
                <?php } else { ?>
                <a id='submit' class="btn btn-primary btn-sm" href='submitpage.php?cid=<?php echo $cid?>&pid=<?php echo $pid?>&langmask=<?php echo $langmask?>'>
                    <i class="bi bi-upload"></i> <?php echo $MSG_SUBMIT?>
                </a>
                <?php } ?>
                <a class="btn btn-outline-primary btn-sm" href='contest.php?cid=<?php echo $cid?>'><?php echo $MSG_PROBLEM.$MSG_LIST?></a>
            <?php } ?>

            <?php if (isset($OJ_OI_MODE)&&!$OJ_OI_MODE) { ?>
                <a class="btn btn-outline-success btn-sm" href='status.php?problem_id=<?php echo $row['problem_id']?>&jresult=4'>
                    <?php echo $MSG_SOVLED?>: <?php echo $row['accepted']?>
                </a>
                <a class="btn btn-outline-info btn-sm" href='status.php?problem_id=<?php echo $row['problem_id']?>'>
                    <?php echo $MSG_SUBMIT_NUM?>: <?php echo $row['submit']?>
                </a>
            <?php } ?>
                <a class="btn btn-outline-secondary btn-sm" href='problemstatus.php?id=<?php echo $row['problem_id']?>'><?php echo $MSG_STATISTICS?></a>
                <a class="btn btn-outline-danger btn-sm" href='#' onclick='transform()'><?php echo $MSG_SHOW_OFF?></a>

            <?php if ( isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME.'_'."p".$row['problem_id']])  ) { ?>
                <?php require_once("include/set_get_key.php"); ?>
                <a class="btn btn-success btn-sm" href='admin/problem_edit.php?id=<?php echo $id?>&getkey=<?php echo htmlentities($_SESSION[$OJ_NAME.'_'.'getkey'], ENT_QUOTES, 'UTF-8')?>'>
                    <i class="bi bi-pencil"></i> EDIT
                </a>
                <a class="btn btn-success btn-sm" href='javascript:phpfm(<?php echo $row['problem_id']?>)'>
                    <i class="bi bi-folder2-open"></i> TESTDATA
                </a>
                <?php if( isset($used_in_contests) && count($used_in_contests)>0 ){ ?>
                    <div class='mt-2'>
                    <?php echo "$MSG_PROBLEM_USED_IN:";?>
                    <?php foreach($used_in_contests as $contests){ ?>
                        <a class='badge bg-warning text-dark' href='contest.php?cid=<?php echo $contests[0]?>'><?php echo $contests[1]?></a>
                    <?php } ?>
                    </div>
                <?php } ?>
            <?php } ?>
            </div>
        </center>
    </div>

    <div class="card-body">
        <?php echo "<!--StartMarkForVirtualJudge-->"; ?>

        <div class="card mb-3">
            <div class="card-header"><h5><i class="bi bi-info-circle"></i> <?php echo $MSG_Description?></h5></div>
            <div id="description" class="card-body content">
                <?php echo bbcode_to_html($row['description'])?>
            </div>
        </div>

        <?php if($row['input']){ ?>
        <div class="card mb-3">
            <div class="card-header"><h5><i class="bi bi-box-arrow-in-right"></i> <?php echo $MSG_Input?></h5></div>
            <div id="input" class="card-body content">
                <?php echo bbcode_to_html($row['input'])?>
            </div>
        </div>
        <?php } ?>

        <?php if($row['output']){ ?>
        <div class="card mb-3">
            <div class="card-header"><h5><i class="bi bi-box-arrow-out-right"></i> <?php echo $MSG_Output?></h5></div>
            <div id="output" class="card-body content">
                <?php echo bbcode_to_html($row['output'])?>
            </div>
        </div>
        <?php } ?>

        <?php
        $sinput=str_replace("<","&lt;",$row['sample_input']);
        $sinput=str_replace(">","&gt;",$sinput);
        $soutput=str_replace("<","&lt;",$row['sample_output']);
        $soutput=str_replace(">","&gt;",$soutput);
        ?>

        <?php if(strlen($sinput)){ ?>
        <div class="card mb-3">
            <div class="card-header">
                <h5><i class="bi bi-file-earmark-code"></i> <?php echo $MSG_Sample_Input?>
                    <button class="btn btn-sm btn-outline-secondary" onclick="CopyToClipboard($('#sampleinput').text())">
                        <i class="bi bi-clipboard"></i> Copy
                    </button>
                </h5>
            </div>
            <div class="card-body"><pre id="sinput" class="mb-0"><span id="sampleinput" class="sampledata"><?php echo $sinput?></span></pre></div>
        </div>
        <?php } ?>

        <?php if(strlen($soutput)){ ?>
        <div class="card mb-3">
            <div class="card-header">
                <h5><i class="bi bi-file-earmark-code"></i> <?php echo $MSG_Sample_Output?>
                    <button class="btn btn-sm btn-outline-secondary" onclick="CopyToClipboard($('#sampleoutput').text())">
                        <i class="bi bi-clipboard"></i> Copy
                    </button>
                </h5>
            </div>
            <div class="card-body"><pre id="soutput" class="mb-0"><span id='sampleoutput' class='sampledata'><?php echo $soutput?></span></pre></div>
        </div>
        <?php } ?>

        <?php if($row['hint']){ ?>
        <div class="card mb-3">
            <div class="card-header"><h5><i class="bi bi-lightbulb"></i> <?php echo $MSG_HINT?></h5></div>
            <div id="hint" class="card-body content hint">
                <?php echo bbcode_to_html($row['hint'])?>
            </div>
        </div>
        <?php } ?>

        <?php if($pr_flag){ ?>
        <div class="card mb-3">
            <div class="card-header"><h5><i class="bi bi-folder"></i> <?php echo $MSG_SOURCE?></h5></div>
            <div fd="source" style='word-wrap:break-word;' pid='<?php echo $row['problem_id']?>' class='card-body content'>
                <?php
                $cats=explode(" ",$row['source']);
                foreach($cats as $cat){
                    $hash_num=hexdec(substr(md5($cat),0,7));
                    $label_theme=$color_theme[$hash_num%count($color_theme)];
                    if($label_theme=="") $label_theme="primary";
                    echo "<a class='badge bg-".$label_theme."' style='display: inline-block;' href='problemset.php?search=".urlencode(htmlentities($cat,ENT_QUOTES,'utf-8'))."'>".htmlentities($cat,ENT_QUOTES,'utf-8')."</a>&nbsp;";
                }
                ?>
            </div>
        </div>
        <?php } ?>

        <?php echo "<!--EndMarkForVirtualJudge-->"; ?>

        <center>
            <div class="btn-group">
            <?php if($pr_flag){ ?>
                <a class="btn btn-primary" href='submitpage.php?id=<?php echo $id?>'><?php echo $MSG_SUBMIT?></a>
            <?php } else { ?>
                <a class="btn btn-primary" href='submitpage.php?cid=<?php echo $cid?>&pid=<?php echo $pid?>&langmask=<?php echo $langmask?>'><?php echo $MSG_SUBMIT?></a>
            <?php } ?>
            <?php if ($OJ_BBS) echo "<a class='btn btn-warning' href='bbs.php?pid=".$row['problem_id']."$ucid'>$MSG_BBS</a>"; ?>
            </div>
        </center>
    </div>
</div>

<script>
function phpfm(pid){
    $.post("admin/phpfm.php", {'frame': 3, 'pid': pid, 'pass': ''}, function(data, status){
        if (status == "success") {
            document.location.href = "admin/phpfm.php?frame=3&pid=" + pid;
        }
    });
}

function selectOne(num, answer){
    let editor = $("iframe")[0].contentWindow.$("#source");
    let old = editor.text();
    let key = num+".*";
    let rep = old.replace(new RegExp(key), num+" "+answer);
    editor.text(rep);
}

function selectMulti(num, answer){
    let editor = $("iframe")[0].contentWindow.$("#source");
    let old = editor.text();
    let key = num+".*";
    let rep = old.replace(new RegExp(key), num+" "+answer);
    editor.text(rep);
}

$(document).ready(function(){
    $("#creator").load("problem-ajax.php?pid=<?php echo $id?>");

    <?php if(isset($OJ_MARKDOWN)&&$OJ_MARKDOWN){ ?>
    $(".md").each(function(){
        $(this).html(marked.parse($(this).html()));
    });
    <?php } ?>

    $('span[class=auto_select]').each(function(){
        let i=1;
        let start=0;
        let raw=$(this).html();
        let options=['A','B','C','D'];
        while(start>=0){
            start=raw.indexOf(i+".",start);
            if(start<0) break;
            let end=start;
            let type="radio";
            for(let j=0;j<4;j++){
                let option=options[j];
                end=raw.indexOf(option+".",start);
                if(j==0&&raw.substring(start,end).indexOf("多选")>0) type="checkbox";
                if (end<0) break;
                let disp="<input type=\""+type+"\" name=\""+i+"\" value=\""+option+"\" />"+option+".";
                raw=raw.substring(0,end-1)+disp+raw.substring(end+2);
                start+=disp.length;
            }
            start=end+1;
            i++;
        }
        $(this).html(raw);
    });

    $('input[type="radio"]').click(function(){
        if ($(this).is(':checked')) {
            selectOne($(this).attr("name"),$(this).val());
        }
    });

    $('input[type="checkbox"]').click(function(){
        let num=$(this).attr("name");
        let answer="";
        $("input[type=checkbox][name="+num+"]").each(function(){
            if ($(this).is(':checked')) answer+=$(this).val();
        });
        selectMulti(num,answer);
    });

    <?php if ($row['spj']>1 || isset($_GET['sid']) || (isset($OJ_AUTO_SHOW_OFF)&&$OJ_AUTO_SHOW_OFF)){ ?>
    transform();
    <?php }?>
});

function CopyToClipboard(input){
    var textToClipboard = input;
    var success = true;
    if (window.clipboardData) {
        window.clipboardData.setData("Text", textToClipboard);
    } else {
        var forExecElement = document.createElement("pre");
        forExecElement.style.position = "absolute";
        forExecElement.style.left = "-10000px";
        forExecElement.style.top = "-10000px";
        forExecElement.textContent = textToClipboard;
        document.body.appendChild(forExecElement);
        forExecElement.contentEditable = true;
        var rangeToSelect = document.createRange();
        rangeToSelect.selectNodeContents(forExecElement);
        var selection = window.getSelection();
        selection.removeAllRanges();
        selection.addRange(rangeToSelect);
        try {
            success = document.execCommand("copy", false, null);
        } catch (e) {
            success = false;
        }
        document.body.removeChild(forExecElement);
    }
    if (success) {
        alert("Copied!");
    } else {
        alert("Your browser doesn't allow clipboard access!");
    }
}

function transform(){
    let height=document.body.clientHeight;
    let width=parseInt(document.body.clientWidth*0.6);
    let width2=parseInt(document.body.clientWidth*0.4);
    let submitURL=$("#submit")[0].href;
    <?php if(isset($_GET['sid'])) echo "submitURL+='&sid=".intval($_GET['sid'])."';"; ?>
    let main=$("#main");
    let problem=main.html();

    if (window.screen.width < 500){
        main.parent().append("<div id='submitPage' class='container' style='opacity:0.8;z-index:88;top:49px;'></div>");
        $("#submitPage").html("<iframe id='ansFrame' src='"+submitURL+"&spa' width='100%' height='"+window.innerHeight+"px' ></iframe>");
        window.setTimeout('$("#ansFrame")[0].scrollIntoView()',1000);
    } else {
        main.css("width",width2);
        main.css("margin-left","0px");
        main.parent().append("<div id='submitPage' class='container' style='opacity:0.8;position:fixed;z-index:1000;top:49px;right:-"+width2+"px'></div>");
        $("#submitPage").html("<iframe src='"+submitURL+"&spa' width='"+(width*0.96)+"px' height='"+(window.innerHeight*0.9)+"px' ></iframe>");
    }
    $("#submit").remove();
    <?php if ($row['spj']>1){ ?>
    window.setTimeout('$("iframe")[0].contentWindow.$("#TestRun").remove();',1000);
    <?php }?>
}
</script>

<?php include("template/$OJ_TEMPLATE/footer.php");?>

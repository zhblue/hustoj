<?php
if ( $pr_flag ) {
    $show_title = $row['title']." - $MSG_PROBLEM - $OJ_NAME";
} else {
    $show_title = "$MSG_PROBLEM ".$PID[$pid].": ".$row['title']." - $OJ_NAME";
}
?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<!-- Toast container for copy feedback -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999">
    <div id="copyToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body"><i class="bi bi-check-circle me-1"></i> Copied!</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<div class="card mb-4">
    <!-- Header -->
    <div class="card-header">
        <div class="d-flex flex-wrap gap-2 align-items-center justify-content-between mb-2">
            <div>
                <?php
                if ($pr_flag) {
                    echo "<h4 class='mb-0'>$id: " . htmlentities($row['title'], ENT_QUOTES, 'utf-8') . "</h4>";
                } else {
                    $id = $row['problem_id'];
                    echo "<h4 class='mb-0'>$MSG_PROBLEM " . $PID[$pid] . ": " . htmlentities($row['title'], ENT_QUOTES, 'utf-8') . "</h4>";
                }
                ?>
                <small class="text-body-secondary"><?php echo $MSG_Creator?>: <span id="creator"></span></small>
            </div>
            <div class="d-flex flex-wrap gap-1 align-items-center">
                <span class="badge bg-secondary"><?php echo isset($MSG_Time_Limit) ? $MSG_Time_Limit : 'Time' ?>: <?php echo $row['time_limit']?>s</span>
                <span class="badge bg-secondary"><?php echo isset($MSG_Memory_Limit) ? $MSG_Memory_Limit : 'Memory' ?>: <?php echo $row['memory_limit']?> MiB</span>
                <?php if($row['spj']) echo "<span class='badge bg-warning text-dark'>$MSG_SPJ</span>"; ?>
            </div>
        </div>

        <div class="btn-group flex-wrap" role="group">
            <?php if($pr_flag){ ?>
                <a id="submit" class="btn btn-primary btn-sm" href="submitpage.php?id=<?php echo $id?>">
                    <i class="bi bi-upload"></i> <?php echo $MSG_SUBMIT?>
                </a>
            <?php } else { ?>
                <?php if($contest_is_over){ ?>
                <a id="submit" class="btn btn-primary btn-sm" href="submitpage.php?id=<?php echo $id?>">
                    <i class="bi bi-upload"></i> <?php echo $MSG_SUBMIT?>
                </a>
                <?php } else { ?>
                <a id="submit" class="btn btn-primary btn-sm" href="submitpage.php?cid=<?php echo $cid?>&pid=<?php echo $pid?>&langmask=<?php echo $langmask?>">
                    <i class="bi bi-upload"></i> <?php echo $MSG_SUBMIT?>
                </a>
                <?php } ?>
                <a class="btn btn-outline-primary btn-sm" href="contest.php?cid=<?php echo $cid?>"><?php echo $MSG_PROBLEM.$MSG_LIST?></a>
            <?php } ?>

            <?php if(isset($OJ_OI_MODE) && !$OJ_OI_MODE){ ?>
                <a class="btn btn-outline-success btn-sm" href="status.php?problem_id=<?php echo $row['problem_id']?>&jresult=4">
                    <i class="bi bi-check-circle"></i> <?php echo $MSG_SOVLED?>: <?php echo $row['accepted']?>
                </a>
                <a class="btn btn-outline-info btn-sm" href="status.php?problem_id=<?php echo $row['problem_id']?>">
                    <i class="bi bi-paperclip"></i> <?php echo $MSG_SUBMIT_NUM?>: <?php echo $row['submit']?>
                </a>
            <?php } ?>
            <a class="btn btn-outline-secondary btn-sm" href="problemstatus.php?id=<?php echo $row['problem_id']?>">
                <i class="bi bi-bar-chart"></i> <?php echo $MSG_STATISTICS?>
            </a>
            <a class="btn btn-outline-danger btn-sm" href="#" onclick="transform();return false">
                <i class="bi bi-arrows-fullscreen"></i> <?php echo $MSG_SHOW_OFF?>
            </a>

            <?php if(isset($_SESSION[$OJ_NAME.'_'.'administrator']) || isset($_SESSION[$OJ_NAME."p".$row['problem_id']])){ ?>
                <?php require_once("include/set_get_key.php"); ?>
                <a class="btn btn-success btn-sm" href="admin/problem_edit.php?id=<?php echo $id?>&getkey=<?php echo htmlentities($_SESSION[$OJ_NAME.'_'.'getkey'], ENT_QUOTES, 'UTF-8')?>">
                    <i class="bi bi-pencil"></i> EDIT
                </a>
                <a class="btn btn-success btn-sm" href="javascript:phpfm(<?php echo $row['problem_id']?>)">
                    <i class="bi bi-folder2-open"></i> TESTDATA
                </a>
                <?php if(isset($used_in_contests) && count($used_in_contests) > 0){ ?>
                    <?php foreach($used_in_contests as $contests){ ?>
                        <a class="badge bg-warning text-dark text-decoration-none" href="contest.php?cid=<?php echo $contests[0]?>"><?php echo htmlentities($contests[1])?></a>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
        </div>
    </div>

    <!-- Body: Accordion sections -->
    <div class="card-body p-0">
        <?php echo "<!--StartMarkForVirtualJudge-->"; ?>

        <div class="accordion" id="problemAccordion">

            <!-- Description -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#desc">
                        <i class="bi bi-info-circle me-2"></i><?php echo $MSG_Description?>
                    </button>
                </h2>
                <div id="desc" class="accordion-collapse collapse show" data-bs-parent="#problemAccordion">
                    <div class="accordion-body content"><?php echo bbcode_to_html($row['description'])?></div>
                </div>
            </div>

            <?php if($row['input']){ ?>
            <!-- Input -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#input-sec">
                        <i class="bi bi-box-arrow-in-right me-2"></i><?php echo $MSG_Input?>
                    </button>
                </h2>
                <div id="input-sec" class="accordion-collapse collapse" data-bs-parent="#problemAccordion">
                    <div class="accordion-body content"><?php echo bbcode_to_html($row['input'])?></div>
                </div>
            </div>
            <?php } ?>

            <?php if($row['output']){ ?>
            <!-- Output -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#output-sec">
                        <i class="bi bi-box-arrow-out-right me-2"></i><?php echo $MSG_Output?>
                    </button>
                </h2>
                <div id="output-sec" class="accordion-collapse collapse" data-bs-parent="#problemAccordion">
                    <div class="accordion-body content"><?php echo bbcode_to_html($row['output'])?></div>
                </div>
            </div>
            <?php } ?>

            <?php
            $sinput = str_replace(["<", ">"], ["&lt;", "&gt;"], $row['sample_input']);
            $soutput = str_replace(["<", ">"], ["&lt;", "&gt;"], $row['sample_output']);
            ?>

            <?php if(strlen($sinput)){ ?>
            <!-- Sample Input -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sample-input">
                        <i class="bi bi-file-earmark-code me-2"></i><?php echo $MSG_Sample_Input?>
                        <button class="btn btn-sm btn-outline-secondary ms-auto me-2" onclick="event.stopPropagation();copyToClipboard($('#sampleinput').text())">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </button>
                </h2>
                <div id="sample-input" class="accordion-collapse collapse" data-bs-parent="#problemAccordion">
                    <div class="accordion-body p-0">
                        <pre class="bg-dark text-light p-3 mb-0 rounded-0" style="overflow-x:auto"><span id="sampleinput" class="sampledata"><?php echo $sinput?></span></pre>
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if(strlen($soutput)){ ?>
            <!-- Sample Output -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sample-output">
                        <i class="bi bi-file-earmark-code me-2"></i><?php echo $MSG_Sample_Output?>
                        <button class="btn btn-sm btn-outline-secondary ms-auto me-2" onclick="event.stopPropagation();copyToClipboard($('#sampleoutput').text())">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </button>
                </h2>
                <div id="sample-output" class="accordion-collapse collapse" data-bs-parent="#problemAccordion">
                    <div class="accordion-body p-0">
                        <pre class="bg-dark text-light p-3 mb-0 rounded-0" style="overflow-x:auto"><span id="sampleoutput" class="sampledata"><?php echo $soutput?></span></pre>
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if($row['hint']){ ?>
            <!-- Hint -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#hint-sec">
                        <i class="bi bi-lightbulb me-2"></i><?php echo $MSG_HINT?>
                    </button>
                </h2>
                <div id="hint-sec" class="accordion-collapse collapse" data-bs-parent="#problemAccordion">
                    <div class="accordion-body content hint"><?php echo bbcode_to_html($row['hint'])?></div>
                </div>
            </div>
            <?php } ?>

            <?php if($pr_flag && $row['source']){ ?>
            <!-- Source -->
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#source-sec">
                        <i class="bi bi-folder me-2"></i><?php echo $MSG_SOURCE?>
                    </button>
                </h2>
                <div id="source-sec" class="accordion-collapse collapse" data-bs-parent="#problemAccordion">
                    <div class="accordion-body" style="word-wrap:break-word">
                        <?php
                        $cats = explode(" ", $row['source']);
                        foreach($cats as $cat){
                            if(!trim($cat)) continue;
                            $hash_num = hexdec(substr(md5($cat), 0, 7));
                            $label_theme = $color_theme[$hash_num % count($color_theme)] ?: 'primary';
                            echo "<a class='badge bg-$label_theme text-decoration-none me-1 mb-1' href='problemset.php?search=".urlencode(htmlentities($cat, ENT_QUOTES, 'utf-8'))."'>".htmlentities($cat, ENT_QUOTES, 'utf-8')."</a>";
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php } ?>

        </div><!-- /accordion -->

        <?php echo "<!--EndMarkForVirtualJudge-->"; ?>

        <div class="p-3 text-center border-top">
            <div class="btn-group" role="group">
            <?php if($pr_flag){ ?>
                <a class="btn btn-primary" href="submitpage.php?id=<?php echo $id?>"><i class="bi bi-upload"></i> <?php echo $MSG_SUBMIT?></a>
            <?php } else { ?>
                <a class="btn btn-primary" href="submitpage.php?cid=<?php echo $cid?>&pid=<?php echo $pid?>&langmask=<?php echo $langmask?>"><i class="bi bi-upload"></i> <?php echo $MSG_SUBMIT?></a>
            <?php } ?>
            <?php if(isset($OJ_BBS) && $OJ_BBS) echo "<a class='btn btn-warning' href='bbs.php?pid=".$row['problem_id']."$ucid'>$MSG_BBS</a>"; ?>
            </div>
        </div>
    </div>
</div>

<script>
function phpfm(pid){
    $.post("admin/phpfm.php", {'frame': 3, 'pid': pid, 'pass': ''}, function(data, status){
        if (status == "success") document.location.href = "admin/phpfm.php?frame=3&pid=" + pid;
    });
}

function copyToClipboard(text){
    if(!text) return;
    if(window.clipboardData){
        window.clipboardData.setData("Text", text);
    } else {
        var el = document.createElement('pre');
        el.style.cssText = "position:absolute;left:-9999px";
        el.textContent = text;
        document.body.appendChild(el);
        el.contentEditable = true;
        var range = document.createRange();
        range.selectNodeContents(el);
        var sel = window.getSelection();
        sel.removeAllRanges();
        sel.addRange(range);
        document.execCommand("copy");
        document.body.removeChild(el);
    }
    var toast = new bootstrap.Toast(document.getElementById('copyToast'));
    toast.show();
}

function selectOne(num, answer){
    let editor = $("iframe")[0].contentWindow.$("#source");
    let old = editor.text();
    let rep = old.replace(new RegExp(num+".*"), num+" "+answer);
    editor.text(rep);
}

function selectMulti(num, answer){
    let editor = $("iframe")[0].contentWindow.$("#source");
    let old = editor.text();
    let rep = old.replace(new RegExp(num+".*"), num+" "+answer);
    editor.text(rep);
}

$(document).ready(function(){
    $("#creator").load("problem-ajax.php?pid=<?php echo $id?>");

    <?php if(isset($OJ_MARKDOWN) && $OJ_MARKDOWN){ ?>
    $(".md").each(function(){ $(this).html(marked.parse($(this).html())); });
    <?php } ?>

    $('span.auto_select').each(function(){
        let i = 1, start = 0, raw = $(this).html();
        let options = ['A','B','C','D'];
        while(start >= 0){
            start = raw.indexOf(i+".", start);
            if(start < 0) break;
            let end = start, type = "radio";
            for(let j = 0; j < 4; j++){
                let option = options[j];
                end = raw.indexOf(option+".", start);
                if(j == 0 && raw.substring(start, end).indexOf("多选") > 0) type = "checkbox";
                if(end < 0) break;
                let disp = "<input type='"+type+"' name='"+i+"' value='"+option+"' />"+option+".";
                raw = raw.substring(0, end-1) + disp + raw.substring(end+2);
                start += disp.length;
            }
            start = end + 1;
            i++;
        }
        $(this).html(raw);
    });

    $('input[type="radio"]').click(function(){
        if($(this).is(':checked')) selectOne($(this).attr("name"), $(this).val());
    });

    $('input[type="checkbox"]').click(function(){
        let num = $(this).attr("name"), answer = "";
        $("input[type=checkbox][name="+num+"]").each(function(){
            if($(this).is(':checked')) answer += $(this).val();
        });
        selectMulti(num, answer);
    });

    <?php if($row['spj'] > 1 || isset($_GET['sid']) || (isset($OJ_AUTO_SHOW_OFF) && $OJ_AUTO_SHOW_OFF)){ ?>
    transform();
    <?php }?>
});

function transform(){
    let submitURL = $("#submit")[0].href;
    <?php if(isset($_GET['sid'])) echo "submitURL += '&sid=".intval($_GET['sid'])."';"; ?>
    let main = $("#main");
    let w = parseInt(document.body.clientWidth * 0.6);
    let w2 = parseInt(document.body.clientWidth * 0.4);

    if(window.screen.width < 500){
        main.parent().append("<div id='submitPage' class='container' style='opacity:0.8;z-index:88;top:49px;'></div>");
        $("#submitPage").html("<iframe id='ansFrame' src='"+submitURL+"&spa' width='100%' height='"+window.innerHeight+"px'></iframe>");
        window.setTimeout("$('#ansFrame')[0].scrollIntoView()", 1000);
    } else {
        main.css("width", w2).css("margin-left", "0px");
        main.parent().append("<div id='submitPage' class='container' style='opacity:0.8;position:fixed;z-index:1000;top:49px;right:-"+w2+"px'></div>");
        $("#submitPage").html("<iframe src='"+submitURL+"&spa' width='"+(w*0.96)+"px' height='"+(window.innerHeight*0.9)+"px'></iframe>");
    }
    $("#submit").remove();
    <?php if($row['spj'] > 1){ ?>
    window.setTimeout('$("iframe")[0].contentWindow.$("#TestRun").remove()', 1000);
    <?php } ?>
}
</script>

<?php include("template/$OJ_TEMPLATE/footer.php");?>

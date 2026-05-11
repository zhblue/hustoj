<?php $show_title=isset($id) ? "$id - $MSG_ERROR_INFO - $OJ_NAME" : "Runtime Error - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<style>
#errtxt pre {
    background: #1e1e1e;
    color: #d4d4d4;
    padding: 1rem;
    border-radius: 0.5rem;
    overflow-x: auto;
    font-size: 0.875rem;
    white-space: pre-wrap;
    word-break: break-all;
}
#errtxt code {
    color: #f44747;
    white-space: pre-wrap;
}
#errexp {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 0.5rem;
}
#errexp h1,#errexp h2,#errexp h3 {
    margin-top: 0.5rem;
}
</style>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-bug"></i> <?php echo isset($MSG_ERROR_INFO) ? $MSG_ERROR_INFO : 'Runtime Error Info'?></h4>
    </div>
    <div class="card-body">
        <?php if(isset($view_reinfo) && $view_reinfo){ ?>
        <div class="mb-3">
            <h5><i class="bi bi-terminal"></i> <?php echo isset($MSG_ERROR_INFO) ? $MSG_ERROR_INFO : 'Error Info'?></h5>
            <pre id="errtxt" class="bg-dark text-light"><?php echo isset($view_reinfo) ? $view_reinfo : ''?></pre>
        </div>
        <?php } ?>

        <?php if(isset($view_mail_link)){ ?>
        <div class="mb-3">
            <?php echo isset($view_mail_link) ? $view_mail_link : ''?>
        </div>
        <?php } ?>

        <div>
            <h5><i class="bi bi-lightbulb"></i> <?php echo isset($MSG_INFO_EXPLAINATION) ? $MSG_INFO_EXPLAINATION : 'Explanation'?></h5>
            <div id="errexp" class="bg-light p-3 rounded"></div>
        </div>
    </div>
</div>

<script>
var pats = new Array();
var exps = new Array();
pats[0] = /A Not allowed system call/;
exps[0] = "<?php echo isset($MSG_A_NOT_ALLOWED_SYSTEM_CALL) ? $MSG_A_NOT_ALLOWED_SYSTEM_CALL : 'Not allowed system call' ?>";
pats[1] = /Segmentation fault/;
exps[1] = "<?php echo isset($MSG_SEGMETATION_FAULT) ? $MSG_SEGMETATION_FAULT : 'Segmentation fault' ?>";
pats[2] = /Floating point exception/;
exps[2] = "<?php echo isset($MSG_FLOATING_POINT_EXCEPTION) ? $MSG_FLOATING_POINT_EXCEPTION : 'Floating point exception' ?>";
pats[3] = /buffer overflow detected/;
exps[3] = "<?php echo isset($MSG_BUFFER_OVERFLOW_DETECTED) ? $MSG_BUFFER_OVERFLOW_DETECTED : 'Buffer overflow detected' ?>";
pats[4] = /Killed/;
exps[4] = "<?php echo isset($MSG_PROCESS_KILLED) ? $MSG_PROCESS_KILLED : 'Process killed' ?>";
pats[5] = /Alarm clock/;
exps[5] = "<?php echo isset($MSG_ALARM_CLOCK) ? $MSG_ALARM_CLOCK : 'Alarm clock' ?>";
pats[6] = /CALLID:20/;
exps[6] = "<?php echo isset($MSG_CALLID_20) ? $MSG_CALLID_20 : 'CALLID:20' ?>";
pats[7] = /ArrayIndexOutOfBoundsException/;
exps[7] = "<?php echo isset($MSG_ARRAY_INDEX_OUT_OF_BOUNDS_EXCEPTION) ? $MSG_ARRAY_INDEX_OUT_OF_BOUNDS_EXCEPTION : 'Array index out of bounds' ?>";
pats[8] = /StringIndexOutOfBoundsException/;
exps[8] = "<?php echo isset($MSG_STRING_INDEX_OUT_OF_BOUNDS_EXCEPTION) ? $MSG_STRING_INDEX_OUT_OF_BOUNDS_EXCEPTION : 'String index out of bounds' ?>";
pats[9] = /Binary files/;
exps[9] = "<?php echo isset($MSG_WRONG_OUTPUT_TYPE_EXCEPTION) ? $MSG_WRONG_OUTPUT_TYPE_EXCEPTION : 'Wrong output type' ?>";
pats[10] = /non-zero return/;
exps[10] = "<?php echo isset($MSG_NON_ZERO_RETURN) ? $MSG_NON_ZERO_RETURN : 'Non-zero return' ?>";

function fill_data(data){
    $("#errexp").html(data);
}

function pull_result(id){
    $.ajax({
        url: 'aiapi/ajax.php',
        type: 'GET',
        data: { id: id },
        success: function(data) {
            if(data=='waiting'){
                setTimeout('pull_result('+id+')', 2000);
            } else {
                fill_data(data);
            }
        },
        error: function() {
            $("#errexp").html('<span class="text-danger">Failed to fetch data</span>');
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
            expmsg += ret + " : " + exp + "<hr/>";
        }
    }
    document.getElementById("errexp").innerHTML = expmsg;

    <?php if (!$isAC && isset($OJ_AI_API_URL) && !empty($OJ_AI_API_URL)){ ?>
    expmsg += "AI <?php echo isset($MSG_EXPLAIN) ? $MSG_EXPLAIN : 'Explaining'?> ... <img src='image/loader.gif' alt='loading'>";
    $("#errexp").html(expmsg);
    $.ajax({
        url: '<?php echo $OJ_AI_API_URL ?>?sid=<?php echo isset($id) ? $id : ''?>',
        type: 'GET',
        success: function(data) {
            if(parseInt(data) > 0){
                setTimeout('pull_result('+data+')', 2000);
            } else {
                fill_data(data);
            }
        },
        error: function() {
            console.log('Failed to fetch AI explanation');
        }
    });
    <?php } ?>
}

$(document).ready(function(){
    explain();
});
</script>

<?php include("template/$OJ_TEMPLATE/footer.php");?>

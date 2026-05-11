<?php
$brush = isset($language_name, $slanguage) ? strtolower($language_name[$slanguage]) : '';
if ($brush == 'pascal') $brush = 'delphi';
elseif (in_array($brush, ['obj-c', 'clang'])) $brush = 'c';
elseif (in_array($brush, ['python3', 'cangjie'])) $brush = 'python';
elseif ($brush == 'clang++') $brush = 'c++';
elseif ($brush == 'freebasic') $brush = 'vb';
elseif ($brush == 'swift') $brush = 'csharp';
?>
<?php $show_title=isset($sproblem_id) ? "$sproblem_id - Source Code - $OJ_NAME" : "Source Code - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<link href='<?php echo isset($OJ_CDN_URL) ? $OJ_CDN_URL : ''?>highlight/styles/shCore.css' rel='stylesheet' type='text/css'/>
<link href='<?php echo isset($OJ_CDN_URL) ? $OJ_CDN_URL : ''?>highlight/styles/shThemeDefault.css' rel='stylesheet' type='text/css'/>

<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="mb-0">
            <?php if(isset($suser_id)){ ?>
            <i class="bi bi-code-square"></i> <?php echo htmlentities($suser_id, ENT_QUOTES, 'utf-8')?> -
            <?php } ?>
            <?php if(isset($sproblem_id)){ ?>
            Problem <?php echo $sproblem_id?>
            <?php } ?>
        </h4>
        <div>
            <button class="btn btn-sm btn-outline-primary me-1" onclick="window.print();">
                <i class="bi bi-printer"></i> <?php echo isset($MSG_PRINTER) ? $MSG_PRINTER : 'Print'?>
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if(isset($ok) && $ok && isset($view_source)){ ?>
        <pre class="p-3 bg-dark text-light rounded m-3" style="font-size:0.85rem;overflow-x:auto;"><?php
            $auth = "/***************************************************************\n";
            $auth .= "\tProblem: ".(isset($sproblem_id) ? $sproblem_id : '')."\n";
            $auth .= "\tUser: ".(isset($suser_id) ? $suser_id : '')." [".(isset($nick) ? $nick : '')."\n";
            $auth .= "\tLanguage: ".(isset($language_name, $slanguage) ? $language_name[$slanguage] : '')."\n";
            $auth .= "\tResult: ".(isset($judge_result, $sresult) ? $judge_result[$sresult] : '')."\n";
            if(isset($sresult) && $sresult == 4){
                $auth .= "\tTime: ".(isset($stime) ? $stime : '')." ms\n";
                $auth .= "\tMemory: ".(isset($smemory) ? $smemory : '')." kb\n";
            }
            $auth .= "****************************************************************/\n\n";
            echo htmlentities(str_replace("\n\r","\n",$view_source),ENT_QUOTES,"utf-8")."\n".$auth;
        ?></pre>
        <?php } else { ?>
        <div class="alert alert-danger m-3">You could not view this code!</div>
        <?php } ?>
    </div>
</div>

<script src='<?php echo isset($OJ_CDN_URL) ? $OJ_CDN_URL : ''?>highlight/scripts/shCore.js' type='text/javascript'></script>
<script src='<?php echo isset($OJ_CDN_URL) ? $OJ_CDN_URL : ''?>highlight/scripts/shBrushCpp.js' type='text/javascript'></script>
<script>
function draw(){
    SyntaxHighlighter.config.bloggerMode = false;
    SyntaxHighlighter.config.clipboardSwf = '<?php echo isset($OJ_CDN_URL) ? $OJ_CDN_URL : ''?>highlight/scripts/clipboard.swf';
    SyntaxHighlighter.highlight();
}
$(document).ready(draw);
</script>

<?php include("template/$OJ_TEMPLATE/footer.php");?>

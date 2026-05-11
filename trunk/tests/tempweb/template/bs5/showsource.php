<?php $show_title=isset($id) ? "$id - Source Code - $OJ_NAME" : "Source Code - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<link href='<?php echo isset($OJ_CDN_URL) ? $OJ_CDN_URL : ""?>template/<?php echo $OJ_TEMPLATE?>/js/syntaxhighlighter/styles/shCore.css' rel='stylesheet' type='text/css'/>
<link href='<?php echo isset($OJ_CDN_URL) ? $OJ_CDN_URL : ""?>template/<?php echo $OJ_TEMPLATE?>/js/syntaxhighlighter/styles/shThemeDefault.css' rel='stylesheet' type='text/css'/>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0">
            <i class="bi bi-file-code"></i>
            <?php echo isset($MSG_SOURCE) ? $MSG_SOURCE : 'Source Code'?>
            <?php if(isset($suser_id)){ ?>
            — <a href="userinfo.php?user=<?php echo urlencode($suser_id)?>"><?php echo htmlentities($suser_id, ENT_QUOTES, 'utf-8')?></a>
            <?php } ?>
        </h4>
    </div>
    <div class="card-body">
        <?php if(isset($ok) && $ok && isset($view_source)){ ?>
        <div class="mb-3">
            <span class="badge bg-info"><?php echo isset($MSG_PROBLEM) ? $MSG_PROBLEM : 'Problem'?>: <?php echo isset($sproblem_id) ? $sproblem_id : (isset($pid) ? $pid : '')?></span>
            <span class="badge bg-secondary"><?php echo isset($MSG_LANG) ? $MSG_LANG : 'Lang'?>: <?php echo isset($language_name, $slanguage) ? $language_name[$slanguage] : ''?></span>
            <span class="badge <?php echo isset($sresult) && $sresult == 4 ? 'bg-success' : 'bg-danger'?>">
                <?php echo isset($MSG_RESULT) ? $MSG_RESULT : 'Result'?>: <?php echo isset($judge_result, $sresult) ? $judge_result[$sresult] : ''?>
            </span>
            <?php if(isset($stime)){ ?>
            <span class="badge bg-dark"><?php echo $stime?> ms</span>
            <?php } ?>
            <?php if(isset($smemory)){ ?>
            <span class="badge bg-dark"><?php echo $smemory?> KB</span>
            <?php } ?>
        </div>

        <?php
        // Build brush name for syntax highlighting
        $brush = isset($language_name, $slanguage) ? strtolower($language_name[$slanguage]) : '';
        if ($brush == 'pascal') $brush = 'delphi';
        elseif (in_array($brush, ['obj-c', 'clang'])) $brush = 'c';
        elseif (in_array($brush, ['python3', 'cangjie'])) $brush = 'python';
        elseif ($brush == 'clang++') $brush = 'c++';
        elseif ($brush == 'freebasic') $brush = 'vb';
        elseif ($brush == 'swift') $brush = 'csharp';
        ?>

        <pre class="bg-dark text-light p-3 rounded" style="overflow-x:auto;font-size:14px;"><code id="source-code"><?php
            $auth = "/**************************************************************\n";
            $auth .= "  Problem: ".(isset($sproblem_id) ? $sproblem_id : '')."\n";
            $auth .= "  User: ".(isset($suser_id) ? $suser_id : '')." [".(isset($nick) ? $nick : '')."]\n";
            $auth .= "  Language: ".(isset($language_name, $slanguage) ? $language_name[$slanguage] : '')."\n";
            $auth .= "  Result: ".(isset($judge_result, $sresult) ? $judge_result[$sresult] : '')."\n";
            if(isset($sresult) && $sresult == 4){
                $auth .= "  Time: ".(isset($stime) ? $stime : '')." ms\n";
                $auth .= "  Memory: ".(isset($smemory) ? $smemory : '')." kb\n";
            }
            $auth .= "****************************************************************/\n\n";
            echo htmlentities(str_replace("\n\r","\n",$view_source),ENT_QUOTES,"utf-8")."\n".$auth;
        ?></code></pre>
        <?php } else { ?>
        <div class="alert alert-danger">You could not view this code!</div>
        <?php } ?>
    </div>
    <div class="card-footer text-center">
        <a class="btn btn-outline-primary" href="javascript:history.back()">
            <i class="bi bi-arrow-left"></i> <?php echo isset($MSG_BACK) ? $MSG_BACK : 'Back'?>
        </a>
        <?php if(isset($id)){ ?>
        <a class="btn btn-outline-secondary" href="comparesource.php?left=<?php echo $id?>&right=<?php echo $id?>">
            <i class="bi bi-arrow-left-right"></i> <?php echo isset($MSG_COMPARE) ? $MSG_COMPARE : 'Compare'?>
        </a>
        <?php } ?>
    </div>
</div>

<script src='<?php echo isset($OJ_CDN_URL) ? $OJ_CDN_URL : ""?>template/<?php echo $OJ_TEMPLATE?>/js/syntaxhighlighter/scripts/shCore.js' type='text/javascript'></script>
<script src='<?php echo isset($OJ_CDN_URL) ? $OJ_CDN_URL : ""?>template/<?php echo $OJ_TEMPLATE?>/js/syntaxhighlighter/scripts/shBrushCpp.js' type='text/javascript'></script>
<script>
$(document).ready(function(){
    SyntaxHighlighter.config.bloggerMode = false;
    SyntaxHighlighter.highlight();
});
</script>

<?php include("template/$OJ_TEMPLATE/footer.php");?>

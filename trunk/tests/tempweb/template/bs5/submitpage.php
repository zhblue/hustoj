<?php $show_title="$MSG_SUBMIT - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<?php
// Build language options from $language_name (defined in const.inc.php)
// Respect $langmask if set (from backend), default show all
if (!isset($langmask)) $langmask = -1;
$lang_count = isset($language_name) ? count($language_name) : 0;
?>

<div class="card">
    <div class="card-header">
        <h4><i class="bi bi-upload"></i> <?php echo isset($MSG_SUBMIT) ? $MSG_SUBMIT : 'Submit'?></h4>
    </div>
    <div class="card-body">
        <form id="submitForm" method="post" action="submit.php" class="mb-3">
            <div class="mb-3">
                <label class="form-label"><?php echo isset($MSG_PROBLEM_ID) ? $MSG_PROBLEM_ID : 'Problem ID'?>:</label>
                <input class="form-control" type="text" name="id" value="<?php echo isset($_GET['id'])?intval($_GET['id']):''?>" <?php echo isset($_GET['id'])?"readonly":""?>>
            </div>
            <div class="mb-3">
                <label class="form-label"><?php echo isset($MSG_LANG) ? $MSG_LANG : 'Language'?>:</label>
                <select class="form-select" name="language" id="language">
                    <?php
                    if ($lang_count > 0) {
                        // Determine which languages are allowed by langmask
                        $allowed = array();
                        for ($i = 0; $i < $lang_count; $i++) {
                            // Skip if langmask explicitly excludes this language (bit not set)
                            if (($langmask >= 0) && !($langmask & (1 << $i))) continue;
                            $selected = (isset($lastlang) && $lastlang == $i) ? "selected" : "";
                            $lang_name = isset($language_name[$i]) ? $language_name[$i] : "Lang$i";
                            echo "<option value='$i' $selected>$lang_name</option>";
                        }
                    } else {
                        // Fallback: common languages
                        $common = array('C','C++','Java','Python3');
                        foreach ($common as $i => $name) {
                            echo "<option value='$i'>$name</option>";
                        }
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label"><?php echo isset($MSG_SOURCE) ? $MSG_SOURCE : 'Source'?>:</label>
                <textarea id="source" class="form-control font-monospace" name="source" rows="15" required></textarea>
            </div>
            <div class="mb-3">
                <input type="hidden" name="spa" value="1">
                <button type="submit" class="btn btn-primary"><i class="bi bi-upload"></i> <?php echo isset($MSG_SUBMIT) ? $MSG_SUBMIT : 'Submit'?></button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function(){
    <?php if(isset($_GET['cid']) && isset($_GET['pid'])){ ?>
    $("input[name=id]").val("<?php echo intval($_GET['pid'])?>").attr("readonly", true);
    <?php } ?>
    // Restore last used language
    var lastlang = document.cookie.match(/lastlang=(\d+)/);
    if(lastlang && !$("#language")[0].hasAttribute('selected')){
        $("#language").val(lastlang[1]);
    }
});
</script>

<?php include("template/$OJ_TEMPLATE/footer.php");?>

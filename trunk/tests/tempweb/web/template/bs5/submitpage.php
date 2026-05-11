<?php $show_title="$MSG_SUBMIT - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card">
    <div class="card-header">
        <h4><i class="bi bi-upload"></i> <?php echo $MSG_SUBMIT?></h4>
    </div>
    <div class="card-body">
        <form id="submitForm" method="post" action="submit.php" class="mb-3">
            <div class="mb-3">
                <label class="form-label"><?php echo $MSG_PROBLEM_ID?>:</label>
                <input class="form-control" type="text" name="id" value="<?php echo isset($_GET['id'])?intval($_GET['id']):''?>" <?php echo isset($_GET['id'])?"readonly":""?>>
            </div>
            <div class="mb-3">
                <label class="form-label"><?php echo $MSG_LANG?>:</label>
                <select class="form-select" name="language">
                    <?php
                    $langs = explode("|", $OJ_LANG_SET);
                    foreach($langs as $i=>$lang){
                        $selected = ($i==0) ? "selected" : "";
                        echo "<option value='$i' $selected>$lang</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label"><?php echo $MSG_SOURCE?>:</label>
                <textarea id="source" class="form-control font-monospace" name="source" rows="15" required></textarea>
            </div>
            <div class="mb-3">
                <input type="hidden" name="spa" value="1">
                <button type="submit" class="btn btn-primary"><i class="bi bi-upload"></i> <?php echo $MSG_SUBMIT?></button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function(){
    <?php if(isset($_GET['cid']) && isset($_GET['pid'])){ ?>
    $("input[name=id]").val("<?php echo $_GET['pid']?>").attr("readonly", true);
    <?php } ?>
});
</script>

<?php include("template/$OJ_TEMPLATE/footer.php");?>

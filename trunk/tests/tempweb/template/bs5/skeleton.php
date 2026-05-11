<?php $show_title=isset($view_title) ? "$view_title - $OJ_NAME" : "Skeleton - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><?php echo isset($view_title) ? htmlentities($view_title, ENT_QUOTES, 'utf-8') : ''?></h4>
    </div>
    <div class="card-body">
        <?php if(isset($view_content)){ ?>
        <div class="md">
            <?php echo isset($view_content) ? $view_content : ''?>
        </div>
        <?php } ?>
    </div>
</div>

<script src="<?php echo isset($OJ_CDN_URL) ? $OJ_CDN_URL : ''?>template/<?php echo isset($OJ_TEMPLATE) ? $OJ_TEMPLATE : 'bs5'?>/js/marked.min.js"></script>
<script>
$(document).ready(function(){
    $(".md").each(function(){
        $(this).html(marked.parse($(this).html()));
    });
});
</script>

<?php include("template/$OJ_TEMPLATE/footer.php");?>

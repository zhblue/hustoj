<?php $show_title="$MSG_FAQ - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>

<div class="card mb-3">
    <div class="card-header">
        <h4 class="mb-0"><i class="bi bi-question-circle"></i> <?php echo $MSG_FAQ?></h4>
    </div>
    <div class="card-body">
        <div class="accordion" id="faqAccordion">
            <?php
            $i = 0;
            foreach($view_faq as $row){
                $i++;
            ?>
            <div class="accordion-item">
                <h2 class="accordion-header" id="heading<?php echo $i?>">
                    <button class="accordion-button <?php echo $i>1?'collapsed':''?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $i?>" aria-expanded="<?php echo $i==1?'true':'false'?>" aria-controls="collapse<?php echo $i?>">
                        <?php echo htmlentities($row['title'],ENT_QUOTES,'utf-8')?>
                    </button>
                </h2>
                <div id="collapse<?php echo $i?>" class="accordion-collapse collapse <?php echo $i==1?'show':''?>" aria-labelledby="heading<?php echo $i?>" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <?php echo bbcode_to_html($row['content'])?>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>

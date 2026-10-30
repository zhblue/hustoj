<?php
$msg_path = realpath(dirname(__FILE__)."/../../admin/msg/$domain.txt");
if (file_exists($msg_path))
    $view_marquee_msg = file_get_contents($msg_path);
else
    $view_marquee_msg = "";

$lg_js_dir = basename(getcwd());
if ($OJ_CDN_URL == "" && ($lg_js_dir == "discuss3" || $lg_js_dir == "admin" || $lg_js_dir == "include")) $lg_js_fix = "../";
else $lg_js_fix = "";
?>
<script>
$(function () {
    <?php if ($view_marquee_msg != "") { ?>
    var msg = "<div class='lg-notice'><span class='lg-notice-tag'>公告</span><marquee direction='left' scrollamount='3' scrolldelay='50' onmouseover='this.stop()' onmouseout='this.start()'><?php echo json_encode($view_marquee_msg); ?></marquee></div>";
    $("#main").prepend(msg);
    <?php } ?>

    $("form").append("<div id='csrf' />");
    $("#csrf").load("<?php echo $lg_js_fix ?>csrf.php");

<?php if (isset($_SESSION[$OJ_NAME."_administrator"]) || isset($_SESSION[$OJ_NAME."_problem_editor"]) || isset($_SESSION[$OJ_NAME."_tag_adder"])) { ?>
    $("div[fd=source]").each(function () {
        var pid = $(this).attr('pid');
        $(this).append("<span><span class='label label-success' pid='" + pid + "' onclick='problem_add_source(this," + pid + ");'>+</span></span>");
    });
<?php } ?>

    $(".hint pre").each(function () {
        var content = $(this);
        $(this).before("<button type='button' class='lg-hint-toggle'>展开提示</button>");
        $(this).prev().on('click', function () {
            content.toggle();
            $(this).text(content.is(':visible') ? '收起提示' : '展开提示');
        });
    });

    console.log("HUSTOJ · Luogu-style theme loaded.");
});

function problem_add_source(sp, pid) {
    var p = $(sp).parent();
    p.html("<form onsubmit='return false;'><input type='hidden' name='m' value='problem_add_source'><input type='hidden' name='pid' value='" + pid + "'><input type='text' class='input input-large' name='ns'></form>");
    p.find("input").focus();
    p.find("input").change(function () {
        var ns = p.find("input[name=ns]").val();
        $.post("admin/ajax.php", p.find("form").serialize());
        p.parent().append("<span class='label label-success'>" + ns + "</span>");
        p.html("<span class='label label-success' pid='" + pid + "' onclick='problem_add_source(this," + pid + ");'>+</span>");
    });
}
</script>

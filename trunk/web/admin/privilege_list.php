<?php
require("admin-header.php");
require_once("../include/set_post_key.php");

if (!isset($_SESSION[$OJ_NAME . '_administrator'])) {
    exit(1);
}

if (isset($OJ_LANG)) {
    require_once("../lang/$OJ_LANG.php");
}

$escape = static function ($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};

$privileges = "'administrator','source_browser','contest_creator','user_adder'," .
    "'http_judge','problem_editor','tag_adder','problem_importer'," .
    "'problem_verifiter','password_setter','printer','balloon','vip'," .
    "'problem_start','problem_end','service_port'";

$keyword = trim((string)($_GET['keyword'] ?? ''));
$page = max(1, intval($_GET['page'] ?? 1));
$idsperpage = 25;

if ($keyword !== '') {
    $like = "%$keyword%";
    $result = pdo_query(
        "SELECT * FROM privilege WHERE user_id LIKE ? OR rightstr LIKE ? ORDER BY user_id, rightstr",
        $like, $like
    );
    $ids = count($result);
    $pages = 1;
} else {
    $ids = intval(pdo_query(
        "SELECT COUNT(*) AS ids FROM privilege WHERE rightstr IN ($privileges)"
    )[0]['ids'] ?? 0);
    $pages = max(1, intval(ceil($ids / $idsperpage)));
    $page = min($page, $pages);
    $sid = ($page - 1) * $idsperpage;
    $result = pdo_query(
        "SELECT * FROM privilege WHERE rightstr IN ($privileges) ORDER BY user_id, rightstr LIMIT $sid, $idsperpage"
    );
}

$pagesperframe = 5;
$frame = intval(ceil($page / $pagesperframe));
$spage = ($frame - 1) * $pagesperframe + 1;
$epage = min($spage + $pagesperframe - 1, $pages);
?>

<title>Privilege List</title>
<hr>
<center><h3><?php echo $escape($MSG_USER . "-" . $MSG_PRIVILEGE . "-" . $MSG_LIST); ?></h3></center>

<div class="padding">
<center>
<form action="privilege_list.php" class="form-search form-inline" method="get">
  <input type="text" name="keyword" class="form-control search-query"
         value="<?php echo $escape($keyword); ?>"
         placeholder="<?php echo $escape($MSG_USER_ID . ', ' . $MSG_PRIVILEGE); ?>">
  <button type="submit" class="form-control"><?php echo $escape($MSG_SEARCH); ?></button>
</form>
</center>

<center>
  <table width="100%" border="1" style="text-align:center;">
    <tr><td>ID</td><td>PRIVILEGE</td><td>REMOVE</td></tr>
<?php foreach ($result as $row):
    $userId = (string)($row['user_id'] ?? '');
    $rightstr = (string)($row['rightstr'] ?? '');
    $valuestr = (string)($row['valuestr'] ?? 'true');
    $canDelete = !($rightstr === 'administrator' &&
        $userId === (string)$_SESSION[$OJ_NAME . '_user_id']);
?>
    <tr>
      <td><?php echo $escape($userId); ?></td>
      <td><?php echo $escape($rightstr); ?><?php
        if ($valuestr !== 'true') echo ':' . $escape($valuestr);
      ?></td>
      <td>
<?php if ($canDelete): ?>
        <button type="button" class="privilege-delete btn btn-link"
                data-uid="<?php echo $escape($userId); ?>"
                data-rightstr="<?php echo $escape($rightstr); ?>">Delete</button>
<?php else: ?>
        Can't suicide
<?php endif; ?>
      </td>
    </tr>
<?php endforeach; ?>
  </table>
</center>

<?php if ($keyword === ''): ?>
<div style="display:inline;"><nav class="center"><ul class="pagination pagination-sm">
  <li class="page-item"><a href="privilege_list.php?page=1">&lt;&lt;</a></li>
  <li class="page-item"><a href="privilege_list.php?page=<?php echo $page === 1 ? 1 : $page - 1; ?>">&lt;</a></li>
<?php for ($i = $spage; $i <= $epage; $i++): ?>
  <li class="<?php echo $page === $i ? 'active ' : ''; ?>page-item">
    <a title="go to page" href="privilege_list.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
  </li>
<?php endfor; ?>
  <li class="page-item"><a href="privilege_list.php?page=<?php echo $page === $pages ? $page : $page + 1; ?>">&gt;</a></li>
  <li class="page-item"><a href="privilege_list.php?page=<?php echo $pages; ?>">&gt;&gt;</a></li>
</ul></nav></div>
<?php endif; ?>
</div>

<script>
$(function () {
    $('.privilege-delete').on('click', function () {
        var button = $(this);
        if (!window.confirm('Delete this privilege?')) return;

        $.ajax({
            url: 'privilege_delete.php',
            type: 'POST',
            data: {
                uid: button.attr('data-uid'),
                rightstr: button.attr('data-rightstr'),
                postkey: <?php echo json_encode($_SESSION[$OJ_NAME . '_postkey'] ?? ''); ?>
            },
            dataType: 'text'
        }).done(function () {
            window.location.reload();
        }).fail(function (xhr) {
            window.alert(xhr.responseText || 'Delete failed');
        });
    });
});
</script>

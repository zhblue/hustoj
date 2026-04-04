<?php $page_title = "Mail - $OJ_NAME"; ?>
<?php include("_includes/head.php"); ?>
</head>

<body class="mdui-drawer-body-left mdui-theme-primary-indigo mdui-theme-accent-indigo mdui-appbar-with-toolbar">
    <?php include("_includes/header.php"); ?>
    <?php include("_includes/sidebar.php"); ?>
    <div class="mdui-container">
        <div class="mdui-card">
            <div class="mdui-card-primary" style="text-align: center;">
                <div class="mdui-card-primary-title"><?php echo $MSG_MAIL?></div>
            </div>
            <div class="mdui-card-content">
<?php
if($view_content){
    echo "<center>
<table class='mdui-table'>
<tr>
<td class='mdui-text-color-black-secondary'>".htmlentities($from_user,ENT_QUOTES,"UTF-8")." -&gt; ".htmlentities($to_user,ENT_QUOTES,"UTF-8")."</td>
</tr>
<tr>
<td class='mdui-text-color-black-secondary'>Subject: ".htmlentities(str_replace("\n\r","\n",$view_title),ENT_QUOTES,"UTF-8")."</td>
</tr>
<tr><td><pre>". htmlentities(str_replace("\n\r","\n",$view_content),ENT_QUOTES,"UTF-8")."</pre>
</td></tr>
</table></center>";
}
?>
<table class="mdui-table">
<form method="post" action="mail.php">
    <tr>
        <td style="padding: 8px;">From: <?php echo htmlentities($from_user,ENT_QUOTES,"UTF-8");?></td>
    </tr>
    <tr>
        <td style="padding: 8px;">
            To: <input name="to_user" class="mdui-textfield-input" size=10 value="<?php if ($from_user==$_SESSION[$OJ_NAME.'_user_id']||$from_user=="") echo htmlentities($to_user) ;else echo htmlentities($from_user);?>">
        </td>
    </tr>
    <tr>
        <td style="padding: 8px;">
            Title: <input name="title" class="mdui-textfield-input" size=20 value="<?php echo htmlentities($title)?>">
            <input type="submit" class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-primary" value="<?php echo $MSG_SUBMIT?>">
        </td>
    </tr>
    <tr>
        <td style="padding: 8px;">
            <textarea name="content" rows=10 cols=80 class="mdui-textfield-input"></textarea>
        </td>
    </tr>
</form>
</table>
<table class="mdui-table mdui-table-selected">
<thead>
<tr><td>Mail ID</td><td>From:Title</td><td>Date</td></tr>
</thead>
<tbody>
<?php
$cnt=0;
foreach($view_mail as $row){
if ($cnt)
echo "<tr class='oddrow'>";
else
echo "<tr class='evenrow'>";
foreach($row as $table_cell){
echo "<td>";
echo "\t".$table_cell;
echo "</td>";
}
echo "</tr>";
$cnt=1-$cnt;
}
?>
</tbody>
</table>
            </div>
        </div>
    </div>
    <?php include("_includes/footer.php"); ?>
</body>
</html>

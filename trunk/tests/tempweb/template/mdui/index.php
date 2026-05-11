<!DOCTYPE html>
<html lang="cn">

<head>
    <?php include('_includes/head.php'); ?>
</head>

<body class="mdui-drawer-body-left mdui-theme-primary-indigo mdui-theme-accent-indigo mdui-appbar-with-toolbar">
    <?php include('_includes/header.php'); ?>
    <?php include('_includes/sidebar.php'); ?>
    <div class="mdui-container">
        <!-- News List -->
        <?php
            $sql_news = "select * FROM `news` WHERE `defunct`!='Y' AND `title`!='faqs.cn' ORDER BY `importance` ASC,`time` DESC LIMIT 10";
            $result_news = mysql_query_cache( $sql_news );
            if ( $result_news ) {
                echo '<div class="mdui-panel" mdui-panel>';
                echo '<h1>公告</h1>';
                foreach ( $result_news as $row ) {
                    echo '<div class="mdui-panel-item">'
                        .'<div class="mdui-panel-item-header">'
                        .'<div class="mdui-panel-item-title">'.$row["title"].'</div>'
                        .'<div class="mdui-panel-item-summary">'.$row["time"].'</div>'
                        .'<i class="mdui-panel-item-arrow mdui-icon material-icons">keyboard_arrow_down</i>'
                        .'</div>'
                        .'<div class="mdui-panel-item-body">'
                        .bbcode_to_html($row["content"])
                        .'</div>'
                        .'</div>';
                }
                echo '</div>';
            }
        ?>

        <!-- User rank -->
        <h1>排名</h1>
        <div class="mdui-table-fluid">
            <table class="mdui-table">
                <thead>
                    <th>#</th>
                    <th>用户名</th>
                    <th>个性签名</th>
                </thead>
                <tbody>
                    <?php
                        $sql_users = "select * FROM `users` ORDER BY `solved` DESC LIMIT 15";
                        $result_users = mysql_query_cache( $sql_users );
                        if ( $result_users ) {
                            $i = 1;
                            foreach ( $result_users as $row ) {
                                echo '<tr>'
                                    .'<td>'.$i++.'</td>'
                                    .'<td>'
                                    ."<a href=\"userinfo.php?user=".$row["user_id"]."\" class=\"mdui-text-color-theme-accent\">".$row["user_id"]."</a>"
                                    .'</td>'
                                    ."<td>${row["school"]}</td>"
                                    .'</tr>';
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>

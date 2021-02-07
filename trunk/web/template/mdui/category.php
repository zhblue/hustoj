<!DOCTYPE html>
<html lang="cn">

<head>
    <?php include('_includes/head.php'); ?>
    <style>
    .oj-category .oj-category-item {
        margin-bottom: 12px;   
    }
    </style>
</head>

<body class="mdui-drawer-body-left mdui-theme-primary-indigo mdui-theme-accent-indigo mdui-appbar-with-toolbar">
    <?php include('_includes/header.php'); ?>
    <?php include('_includes/sidebar.php'); ?>
    <div class="mdui-container">
        <h1>分类</h1>
        <!-- 覆盖重写样式 分类下a标签多行时 相邻两行之间没有间距 -->
        <div class="oj-category">
            <?php if(!$result) { ?>
                <div style="font-size: 175%;">暂无分类</div>
            <?php } else { ?>
                <?php
                    $colors = [
                        "red",
                        "pink",
                        "purple",
                        "orange",
                        "light-blue",
                        "green",
                        "teal"
                        ];
                    foreach($result as $row) { 
                        $hash_num = hexdec(substr(md5($row["source"]),0,7));
                        $label_color = $colors[$hash_num%count($colors)];
                        $label_color = $label_color ? $label_color : "theme";
                        // source 不为空 时显示 避免出现空白标签
                        if (!empty($row["source"])) {
                            echo '<a class="oj-category-item mdui-btn mdui-btn-dense mdui-color-'.$label_color.'-accent mdui-ripple mdui-m-x-2" href="problemset.php?search='.urlencode($row["source"]).'">'.$row["source"].'</a>';
                        }
                        
                    }
                ?>
            <?php } ?>
        </div>
    </div>
</body>

</html>
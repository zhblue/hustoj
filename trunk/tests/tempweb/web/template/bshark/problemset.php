<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>
		<?php echo $MSG_PROBLEMS; ?> - <?php echo $OJ_NAME; ?>
	</title>
	<?php require("./template/bshark/header-files.php"); ?>
</head>

<body>
	<?php require("./template/bshark/nav.php"); ?>
	<div class="ui container bsharkMain">
		<div class="ui stackable grid">
			<div class="eleven wide column">
				<div class='card'>
					<div class="card-body">
						<table class="ui single line fluid unstackable compact table">
							<thead>
								<tr class='toprow'>
									<th style="width: 1px"></th>
									<th class='one wide'>
										id
									</th>
									<th>
										<?php echo $MSG_TITLE ?>
									</th>
									<th class='one wide'>
										<?php echo $MSG_SOURCE ?>
									</th>
									<th class='one wide' style="cursor:hand">
										<?php echo $MSG_AC ?>
									</th>
									<th class='one wide' style="cursor:hand">
										<?php echo $MSG_SUBMIT ?>
									</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$cnt = 0;
								foreach ($view_problemset as $row) {
									echo "<tr>";
									$i = 0;
									foreach ($row as $table_cell) {
										if ($i == 1 || $i == 3)
											echo "<td class='hidden-xs'";
										else
											echo "<td";
										if (trim($table_cell) == "<div class='label label-danger'>N</div>")
											echo " class='left red marked'";
										if (trim($table_cell) == "<div class='label label-success'>Y</div>")
											echo " class='left green marked'";
										echo ">";
										if (
											trim($table_cell) != "<div class='label label-danger'>N</div>" &&
											trim($table_cell) != "<div class='label label-success'>Y</div>"
										)
											echo "\t" . $table_cell;
										echo "</td>";
										$i++;
									}
									echo "</tr>";
									$cnt = 1 - $cnt;
								}
								?>
							</tbody>
						</table>
						<?php if (!$_GET["search"]) { ?>
							<div class="ui container center aligned">
								<div class="ui borderless tiny menu pagination">
									<a class="icon item" href="problemset.php?page=1"><i class="angle double left icon"></i></a>
									<?php
									if (!isset($page))
										$page = 1;
									$page = intval($page);
									$section = 8;
									$start = $page > $section ? $page - $section : 1;
									$end = $page + $section > $view_total_page ? $view_total_page : $page + $section;
									for ($i = $start; $i <= $end; $i++) {
										echo "<a class='" . ($page == $i ? "active " : "") . "item' href='problemset.php?page=" . $i . "'>" . $i . "</a>";
									}
									?>
									<a class="icon item" href="problemset.php?page=<?php echo $view_total_page ?>"><i class="angle double right icon"></i></a>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
			<div class="five wide column">
				<div class="card">
					<div class="card-body">
						<h2 class="ui header">
							<?php echo $MSG_SEARCH; ?>
						</h2>
						<div class="row">
							<div colspan='1' class="col-md-12">
								<form action='problemset.php'>
									<div class="ui left icon fluid input">
										<input type="text" name="search" placeholder='keyword'>
										<i class="search icon"></i>
									</div>
								</form>
							</div>
						</div>
						<div class="row">
							<div colspan='1' class="col-md-12">
								<form action='problem.php' >
									<div class="ui left icon fluid input">
										<input type="text" name="id" placeholder='ID'>
										<i class="search icon"></i>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<?php if ($THEME_SHOW_CATEGORY == true) { ?>
					<br>
					<div class="card">
						<div class="card-body">
							<h2 class="ui header"><?php echo $MSG_SOURCE; ?></h2>
							<?php $view_category = "";
							$sql = "select distinct source "
								. "FROM `problem` where defunct='N'"
								. "LIMIT 500";
							$result = pdo_query($sql);
							$category = array();
							foreach ($result as $row) {
								$cate = explode(" ", $row['source']);
								foreach ($cate as $cat) {
									array_push($category, trim($cat));
								}
							}
							$category = array_unique($category);
							if (!$result) {
								$view_category = "No Category Now!";
							} else {
								foreach ($category as $cat) {
									if (trim($cat) == "")
										continue;
									$hash_num = hexdec(substr(md5($cat), 0, 7));
									$label_theme = $color_theme[$hash_num % count($color_theme)];
									if ($label_theme == "")
										$label_theme = "default";
									$view_category .= "<a class='badge badge-$label_theme' href='problemset.php?search=" . urlencode(htmlentities($cat, ENT_QUOTES, 'UTF-8')) . "'>" . $cat . "</a>&nbsp;";
								}
							}
							echo $view_category;
							?>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<?php require("./template/bshark/footer.php"); ?>
	<?php require("./template/bshark/footer-files.php"); ?>
</body>

</html>

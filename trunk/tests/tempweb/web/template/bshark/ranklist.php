<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>
		<?php echo $MSG_RANKLIST; ?> - <?php echo $OJ_NAME; ?>
	</title>
	<?php require("./template/bshark/header-files.php"); ?>
	<?php
	function deleteFkingOriginStyle($e)
	{
		return substr($e, 18, -6);
	}
	?>
</head>

<body>
	<?php require("./template/bshark/nav.php"); ?>
	<div class="ui container bsharkMain">
		<div class="card">
			<div class="card-body">
				<h2>
					<?php echo $MSG_RANKLIST; ?>
				</h2>
				<div class="ui borderless tiny menu pagination">
					<a href=ranklist.php?scope=d class="item">Day</a>
					<a href=ranklist.php?scope=w class="item">Week</a>
					<a href=ranklist.php?scope=m class="item">Month</a>
					<a href=ranklist.php?scope=y class="item">Year</a>
				</div>
				<form class="ui form" action="ranklist.php">
					<div class="inline field">
						<div class="ui action input">
							<input type="text" name="prefix"
								value="<?php echo htmlentities(isset($_GET['prefix']) ? $_GET['prefix'] : "", ENT_QUOTES, "utf-8") ?>"
								placeholder="<?php echo $MSG_USER ?>">
							<button class="ui button">
								<?php echo $MSG_SEARCH; ?>
							</button>
						</div>
					</div>
				</form>
				<div class="ui container center aligned">
					<div class="ui borderless tiny menu pagination">
						<?php
						$nowStart = 0;
						if (isset($_GET["start"]))
							$nowStart = intval($_GET["start"]);
						$qs = "";
						if (isset($_GET['prefix'])) {
							$qs .= "&prefix=" . htmlentities($_GET['prefix'], ENT_QUOTES, "utf-8");
						}
						if (isset($scope)) {
							$qs .= "&scope=" . htmlentities($scope, ENT_QUOTES, "utf-8");
						}

						for ($i = 0; $i < $view_total; $i += $page_size) {
							echo "<a class='item";
							if (strval($i) == $nowStart)
								echo " active";
							echo "' href='./ranklist.php?start=" . strval($i) . $qs . "'>";
							echo strval($i + 1);
							echo "-";
							echo strval($i + $page_size);
							echo "</a>";
						}
						?>
					</div>
				</div>
			</div>
		</div>
		<div class="ui stackable three column grid">
			<?php
			foreach ($view_rank as $row) {
				?>
				<div class="column">
					<div class="card card-rank">
						<div class="card-body">
							<h1 class="rank-title">
								<?php echo $row[1]; ?>
							</h1>
							<div class="rank-school">
								<?php echo $row[2]; ?>
							</div>
							<span class="ui green label rank-stat">
								<i class="icon check"></i>
								<?php echo deleteFkingOriginStyle($row[3]); ?>
								<?php echo $MSG_SOVLED; ?>
							</span>&nbsp;
							<span class="ui yellow label rank-stat">
								<i class="icon paper plane"></i>
								<?php echo deleteFkingOriginStyle($row[4]); ?>
								<?php echo $MSG_SUBMIT; ?>
							</span>
						</div>
						<span class="rank">#<?php echo $row[0]; ?></span>
					</div>
				</div>
			<?php
			}
			?>
		</div>
	</div>
	<?php require("./template/bshark/footer.php"); ?>
	<?php require("./template/bshark/footer-files.php"); ?>
</body>

</html>

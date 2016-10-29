<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false" aria-controls="navbar">
       <span class="sr-only">Toggle navigation</span>
       <span class="icon-bar"></span>
       <span class="icon-bar"></span>
       <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="contest.php">Contest</a>
    </div>
    <div class="collapse navbar-collapse" id="navbar-collapse">
      <ul class="nav navbar-nav">
        <li <?php if ($navigation_tab == "problem") echo "class='active'"?>><a href="contest.php?cid=<?php echo $cid?>"><?php echo $MSG_PROBLEMS?></a></li>
        <li <?php if ($navigation_tab == "ranklist") echo "class='active'"?>><a href="contestrank.php?cid=<?php echo $cid?>"><?php echo $MSG_STANDING?></a></li>
        <li <?php if ($navigation_tab == "status") echo "class='active'"?>><a href="status.php?cid=<?php echo $cid?>"><?php echo $MSG_STATUS?></a></li>
        <li <?php if ($navigation_tab == "statistics") echo "class='active'"?>><a href="conteststatistics.php?cid=<?php echo $cid;?>"><?php echo $MSG_STATISTICS?></a></li>
      </ul>
      <?php require_once("./template/".$OJ_TEMPLATE."/user-header.php"); ?>
    </div>
  </div>
</nav>


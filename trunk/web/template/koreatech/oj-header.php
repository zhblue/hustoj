<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
  <div class="container">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
       <span class="sr-only">Toggle navigation</span>
       <span class="icon-bar"></span>
       <span class="icon-bar"></span>
       <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="<?php echo $OJ_HOME?>">Judge</a>
    </div>
    <div class="collapse navbar-collapse" id="navbar">
      <ul class="nav navbar-nav">
        <li <?php if ($navigation_tab == "problem") echo "class='active'"?>><a href="problemset.php"><?php echo $MSG_PROBLEMS?></a></li>
        <li <?php if ($navigation_tab == "status") echo "class='active'"?>><a href="status.php"><?php echo $MSG_STATUS?></a></li>
        <li <?php if ($navigation_tab == "ranklist") echo "class='active'"?>><a href="ranklist.php"><?php echo $MSG_RANKLIST?></a></li>
        <li <?php if ($navigation_tab == "contest") echo "class='active'"?>><a href="contest.php"><?php echo $MSG_CONTEST?></a></li>
        <li <?php if ($navigation_tab == "faqs") echo "class='active'"?>><a href="faqs.php"><?php echo $MSG_FAQ?></a></li>
      </ul>
<?php require_once("./template/".$OJ_TEMPLATE."/user-header.php"); ?>
    </div>
  </div>
</nav>


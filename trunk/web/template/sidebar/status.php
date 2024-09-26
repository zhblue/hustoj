<?php $show_title="$MSG_STATUS - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>
<script src="template/<?php echo $OJ_TEMPLATE?>/js/textFit.min.js"></script>
<div class="padding" >

  <!-- <form action="" class="ui mini form" method="get" role="form" id="form"> -->
  <form id=simform class="ui mini form" action="status.php" method="get">
    <div class="inline fields" style="margin-bottom: 25px; white-space: nowrap; ">
      <label style="font-size: 1.2em; margin-right: 1px; "><?php echo $MSG_PROBLEM_ID?>：</label>
      <div class="field"><input name="problem_id" style="width: 50px; " type="text" value="<?php echo isset($problem_id)?htmlspecialchars($problem_id, ENT_QUOTES):"" ?>"></div>
        <label style="font-size: 1.2em; margin-right: 1px; "><?php echo $MSG_USER?>：</label>
        <div class="field"><input name="user_id" style="width: 50px; " type="text" value="<?php echo  isset($user_id)?htmlspecialchars($user_id, ENT_QUOTES):"" ?>"></div>

        <label style="font-size: 1.2em; margin-right: 1px; "><?php echo $MSG_SCHOOL?>：</label>
        <div class="field"><input name="school" style="width: 50px; " type="text" value="<?php echo isset($school)?htmlspecialchars($school, ENT_QUOTES):"" ?>"></div>
        <label style="font-size: 1.2em; margin-right: 1px; "><?php echo $MSG_GROUP_NAME?>：</label>
        <div class="field"><input name="group_name" style="width: 50px; " type="text" value="<?php echo isset($group_name)?htmlspecialchars($group_name, ENT_QUOTES):"" ?>"></div>
        <label style="font-size: 1.2em; margin-right: 1px; "><?php echo $MSG_LANG?>：</label>
        <select class="form-control" size="1" name="language" style="width: 110px;font-size: 1em ">
          <option value="-1">All</option>
          <?php
          if(isset($_GET['language'])){
            $selectedLang=intval($_GET['language']);
          }else{
            $selectedLang=-1;
          }
          $lang_count=count($language_ext);
          $langmask=$OJ_LANGMASK;
          $lang=(~((int)$langmask))&((1<<($lang_count))-1);
          for($i=0;$i<$lang_count;$i++){
            if($lang&(1<<$i))
            echo"<option value=$i ".( $selectedLang==$i?"selected":"").">
            ".$language_name[$i]."
            </option>";
          }
          ?>
        </select>
        <label style="font-size: 1.2em; margin-right: 1px;margin-left: 10px; ">状态：</label>
        <select class="form-control" size="1" name="jresult" style="width: 110px;">
          <?php if (isset($_GET['jresult'])) $jresult_get=intval($_GET['jresult']);
          else $jresult_get=-1;
          if ($jresult_get>=12||$jresult_get<0) $jresult_get=-1;
          if ($jresult_get==-1) echo "<option value='-1' selected>All</option>";
          else echo "<option value='-1'>All</option>";
          for ($j=0;$j<12;$j++){
          $i=($j+4)%12;
          if ($i==$jresult_get) echo "<option value='".strval($jresult_get)."' selected>".$jresult[$i]."</option>";
          else echo "<option value='".strval($i)."'>".$jresult[$i]."</option>";
          }
          echo "</select>";
          ?>
          <?php if(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'source_browser'])){
            if(isset($_GET['showsim']))
            $showsim=intval($_GET['showsim']);
            else
            $showsim=0;
            echo "<label style=\"font-size: 1.2em; margin-right: 1px;margin-left: 10px; \">相似度：</label>";
          echo "
          <select id=\"appendedInputButton\" class=\"form-control\" name=showsim onchange=\"document.getElementById('simform').submit();\" style=\"width: 110px;\">
          <option value=0 ".($showsim==0?'selected':'').">All</option>
          <option value=80 ".($showsim==80?'selected':'').">80</option>
          <option value=85 ".($showsim==85?'selected':'').">85</option>
          <option value=90 ".($showsim==90?'selected':'').">90</option>
          <option value=95 ".($showsim==95?'selected':'').">95</option>
          <option value=100 ".($showsim==100?'selected':'').">100</option>
          </select>";
          }
          ?>
      <button class="ui labeled icon mini green button" type="submit" style="margin-left: 20px;">
        <i class="search icon"></i>
       <?php echo $MSG_SEARCH;?>
      </button>
                <span class='ui mini grey button'>AWT:<?php echo round($avg_delay,2)?>s </span>
                 <script>var AWT=<?php echo round($avg_delay*500,0) ?>;</script>
    </div>
  </form>


  <table id="result-tab" class="ui very basic center aligned table" style="white-space: nowrap; " id="table">
    <thead>
      <tr>
        <th class='desktop-only item'><?php echo $MSG_RUNID?></th>
        <th><?php echo $MSG_USER?></th>
        <th><?php echo $MSG_NICK?></th>
        <th><?php echo $MSG_PROBLEM_ID?></th>
        <th><?php echo $MSG_RESULT?></th>
        <th><?php echo $MSG_MEMORY?></th>
        <th><?php echo $MSG_TIME?></th>
        <th ><?php echo $MSG_LANG?></th>
        <th  class='desktop-only item'><?php echo $MSG_CODE_LENGTH?></th>
        <th ><?php echo $MSG_SUBMIT_TIME?></th>
       <?php    if (isset($_SESSION[$OJ_NAME.'_'.'administrator'])) {
                                                        echo "<th class='desktop-only item'>";
                                                                echo $MSG_JUDGER;
                                                        echo "</th>";
                                                } ?>
      </tr>
    </thead>
    <tbody  style='font-weight:700' >
      <!-- <tr v-for="item in items" :config="displayConfig" :show-rejudge="false" :data="item" is='submission-item'>
          </tr> -->
    <?php
    foreach($view_status as $row){
    $i=0;
    echo "<tr>";
    foreach($row as $table_cell){
      if ($i==4)
        echo "<td class='td_result'>";
      else if($i==0 || $i>7 && $i!=9)
        echo "<td class='desktop-only item '>";
      else
        echo "<td>";
      echo $table_cell;
      echo "</td>";
      $i++;
    }
    echo "</tr>\n";
    }
    ?>

    </tbody>
  </table>

  <div style="margin-bottom: 30px; ">

  <div style="text-align: center; ">
        <div class="ui pagination menu" style="box-shadow: none; ">
          <a class="icon item" href="<?php echo "status.php?".$str2;?>" id="page_prev">
    Top
          </a>
          <?php
      if (isset($_GET['prevtop']))
      echo "<a class=\"item\" href=\"status.php?".$str2."&top=".intval($_GET['prevtop'])."\">Prev</a>";
      else
      echo "<a class=\"item\" href=\"status.php?".$str2."&top=".($top+20)."\">Prev</a>";

      ?>

          <a class="icon item" href="<?php echo "status.php?".$str2."&top=".$bottom."&prevtop=$top"; ?>" id="page_next">
            Next
          </a>
        </div>
  </div>
</div>

<script>
        var i = 0;
        var judge_result = [<?php
        foreach ($judge_result as $result) {
                echo "'$result',";
        } ?>
        ''];

        var judge_color = [<?php
        foreach ($judge_color as $result) {
                echo "'$result',";
        } ?>
        ''];
   var oj_mark='<?php echo $OJ_MARK ?>'; 
   var user_id="<?php if (isset($_SESSION[$OJ_NAME."_user_id"]) && $OJ_FANCY_RESULT ) echo $_SESSION[$OJ_NAME."_user_id"]; ?>";
   var fancy_mp3="<?php if (isset($_SESSION[$OJ_NAME."_user_id"]) && $OJ_FANCY_RESULT ) echo $OJ_FANCY_MP3; ?>";
  
</script>
        <script src="template/<?php echo $OJ_TEMPLATE?>/auto_refresh.js?v=0.522" ></script>

<?php include("template/$OJ_TEMPLATE/footer.php");

<?php $show_title="$MSG_CONTEST - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php");?>
<div class="padding">
<div class="ui grid" style="margin-bottom: 10px; ">
    <div class="row" style="white-space: nowrap; ">
      <div class="seven wide column">
          <form method=post action=contest.php >
            <div class="ui search" style="width: 280px; height: 28px; margin-top: -5.3px; ">
              <div class="ui left icon input" style="width: 100%; ">
                <input class="prompt" style="width: 100%; " type="text" value="" placeholder=" <?php echo $MSG_CONTEST_NAME ?> …" name="keyword">
                <i class="search icon"></i>
                <a  class="ui button"  href="contest.php" ><?php echo $MSG_VIEW_ALL_CONTESTS ?></a>
              </div>
              <div class="results" style="width: 100%; "></div>
            </div>
          </form>

      </div>

      <div class="nine wide right aligned column">

      </div>
    </div>
  </div>

      <div style="margin-bottom: 30px; ">
    
    <?php
      if(!isset($page)) $page=1;
      $page=intval($page);
      $section=8;
      $start=$page>$section?$page-$section:1;
      $end=$page+$section>$view_total_page?$view_total_page:$page+$section;
      $MY=isset($_GET['my'])?"&my":"";
    ?>
<div style="text-align: center; ">
  <div class="ui pagination menu" style="box-shadow: none; ">
    <a class="<?php if($page==1) echo "disabled "; ?>icon item" href="<?php if($page<>1) echo "contest.php?page=".strval($page-1).$MY ?>" id="page_prev">
      <i class="left chevron icon"></i>
    </a>
    <?php
      for ($i=$start;$i<=$end;$i++){
        echo "<a class=\"".($page==$i?"active ":"")."item\" href=\"contest.php?page=".$i.$MY."\">".$i."</a>";
      }
    ?>
    <a class="<?php if($page==$view_total_page) echo "disabled "; ?> icon item" href="<?php if($page<>$view_total_page) echo "contest.php?page=".strval($page+1).$MY; ?>" id="page_next">
    <i class="right chevron icon"></i>
    </a>
  </div>
</div>

</div>
    <table class="ui very basic center aligned table">
      <thead>
        <tr>
          <th><?php echo $MSG_CONTEST_ID?></th>
          <th><?php echo $MSG_CONTEST_NAME?></th>
          <th><?php echo $MSG_TIME?></th>
          <th><?php echo $MSG_CONTEST_OPEN?></th>
          <th><?php echo $MSG_CONTEST_CREATOR?></th>
        </tr>
      </thead>
      <tbody>
          <?php
            foreach($view_contest as $row){
              echo "<tr>";
              foreach($row as $table_cell){
                echo "<td>";
                echo "\t".$table_cell;
                echo "</td>";
              }
              echo "</tr>";
            }
          ?>
          
          

          <!-- <td><a href="<%= syzoj.utils.makeUrl(['contest', contest.id]) %>"><%= contest.title %> <%- tag %></a></td>
          <td><%= syzoj.utils.formatDate(contest.start_time) %></td>
          <td><%= syzoj.utils.formatDate(contest.end_time) %></td>
          <td class="font-content"><%- contest.subtitle %></td> -->
      </tbody>
    </table>
</div>

<?php include("template/$OJ_TEMPLATE/footer.php");?>

<?php $show_title = "题单 - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php"); ?>
<?php
require_once("problem_list_data.php");
$user_id = "";
$submit_arr = [];
$accepted_arr = [];
if (isset($_SESSION[$OJ_NAME . '_' . 'user_id'])) {
  $user_id = $_SESSION[$OJ_NAME . '_' . 'user_id'];
  $sql = "SELECT problem_id FROM solution WHERE user_id = ? GROUP BY problem_id";
  $ret = @pdo_query($sql, $user_id);
  foreach ($ret as $row) {
    $submit_arr[$row[0]] = true;
  }

  $sql = "SELECT problem_id FROM solution WHERE user_id = ?  AND result = 4 GROUP BY problem_id";
  $ret = @pdo_query($sql, $user_id);
  foreach ($ret as $row) {
    $accepted_arr[$row[0]] = true;
  }
}

$all_problems = [];
foreach($list as $set) {
  foreach($set['sublist'] as $subset) {
    $p = $subset['problems'];
    array_push($all_problems, ...$p);
  }
}
$ins = str_repeat("?,", count($all_problems) - 1) . "?";
$sql = "SELECT problem_id, title, accepted, submit FROM problem WHERE problem_id in ($ins)";


$ret = @pdo_query($sql, ...$all_problems);
$problemsMap = [];
foreach($ret as $problem) {
  $problemsMap[$problem['problem_id']] = $problem;
}

// print_r($problemsMap);


foreach ($list as $i => &$set) {
  foreach ($set['sublist'] as $j => &$subset) {
    $problems = $subset['problems'];

    $subset['number'] = count($problems);
    $subset['acc_number'] = 0;

    $ret = [];
    foreach($problems as $problem) {
      array_push($ret, $problemsMap[$problem]);
    }
    
    foreach ($ret as $k => &$problem) {
      $problem_id = $problem['problem_id'];
      if (array_key_exists($roblem_id, $submit_arr)) {
        if (array_key_exists($problem_id, $accepted_arr)) {
          $problem['status'] = 1;
          $subset['acc_number']++;
        } else {
          $problem['status'] = -1;
        }
      }
      $problem['rate'] = $problem['submit'] == 0 ? "0.0%" : sprintf("%.2f%%", $problem['accepted'] / $problem['submit'] * 100);
    }
    $subset['problems'] = $ret;
  }
}
?>
<style>
  #list {
    padding: 20px;
  }

  .section {
    margin-top: 20px !important;
  }

  .accepted {
    color: forestgreen;
  }

  .wrong_answer {
    color: red;
  }
</style>
<div id="context1">
  <div class="ui secondary menu">
    <a class="item active" data-tab="first">语言入门</a>
    <a class="item" data-tab="second">算法入门</a>
    <a class="item" data-tab="third">比赛</a>
  </div>
  <?php
  $type_name = [null, 'first', 'second', 'third'];
  for ($type = 1; $type <= 3; $type++) {
  ?>
    <div class="ui tab green segment <?php if ($type == 1) echo "active" ?>" data-tab="<?php echo $type_name[$type] ?>">
      <div class="ui grid">
        <div class="four wide column">
          <div class="ui secondary vertical  pointing menu" style="position:sticky; top:30px">
            <?php
            $flag = true;
            foreach ($list as $i => $set) {
              if ($set['type'] != $type) continue;
              if ($flag) {
                $flag = false;
                echo "<a class='item active' data-tab='$i'>" . $set['title'] . "</a>";
              } else {
                echo "<a class='item' data-tab='$i'>" . $set['title'] . "</a>";
              }
            }
            ?>
          </div>
        </div>
        <div class="twelve wide column" style="margin-left: -30px;">
          <?php
          $flag = true;
          foreach ($list as $i => $set) {
            if ($set['type'] != $type) continue;
          ?>
            <div class="ui  tab segment
          <?php if ($flag) {
              echo "active";
              $flag = false;
            } ?>
        " data-tab="<?php echo $i; ?>">

              <div class="ui fluid accordion">
                <?php foreach ($set['sublist'] as $j => $subset) {
                  $title = ($j + 1) . '. ' . $subset['title'];
                ?>
                  <div class="title active">
                    <i class="dropdown icon"></i>
                    <?php echo $title ?>
                    <div style="float:right">
                      <div class="ui label" style="width: 35px;"><?php echo $subset['number'] ?></div>
                      <div class="ui green label" style="width: 35px;"><?php echo $subset['acc_number'] ?></div>
                    </div>
                  </div>
                  <div class="content active">
                    <table class="ui single line selectable table center aligned">
                      <thead>
                        <tr>
                          <th class="one wide">状态</th>
                          <th class="two wide">编号</th>
                          <th class="left aligned ">题目名称</th>
                          <th class="one wide">通过</th>
                          <th class="one wide">提交</th>
                          <th class="one wide">通过率</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($subset['problems'] as $k => $item) { ?>
                          <tr>
                            <?php if ($item['status'] == 1) { ?>
                              <td>
                                <span class="status accepted">
                                  <i class="icon checkmark"></i>
                                </span>
                              </td>
                            <?php } else if ($item['status'] == -1) { ?>
                              <td>
                                <span class="status wrong_answer">
                                  <i class="icon remove"></i>
                                </span>
                              </td>
                            <?php } else { ?>
                              <td>
                                <span class="status">
                                </span>
                              </td>
                            <?php } ?>
                            <td><b><?php echo $item['problem_id'] ?></b></td>
                            <td class="left aligned"><a href="problem.php?id=<?php echo $item['problem_id'] ?>">
                                <?php echo $item['title'] ?>
                              </a></td>
                            <td><a href="status.php?problem_id=<?php echo $item['problem_id'] ?>&jresult=4"><?php echo $item['accepted'] ?></a></td>
                            <td><a href="status.php?problem_id=<?php echo $item['problem_id'] ?>"><?php echo $item['submit'] ?></a></td>
                            <td><?php echo $item['rate'] ?></td>
                          </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                <?php } ?>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
    </div>
  <?php
  }
  ?>
</div>



<script type="text/javascript">
  $(function() {
    $('.ui .accordion')
      .accordion({
        exclusive: false
      });
    $('.menu .item')
      .tab();
    $('#context1 .menu .item')
      .tab({
        context: $('#context1')
      });
    // $('[data-make-sticky-to]')
    //   .sticky({
    //     context: $('[data-make-sticky-to]').data('makeStickyTo'),
    //     setSize: true,
    //   });
  });
</script>

<?php include("template/$OJ_TEMPLATE/footer.php"); ?>
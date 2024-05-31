<?php $show_title = "Contest RankList - " . $title . " - $OJ_NAME"; ?>
<?php include("template/$OJ_TEMPLATE/header.php"); ?>
<style>
	.submit_time {
		font-size: 0.8em;
		margin-top: 5px;
		color: #000;
	}
</style>
<div style="margin-bottom:40px; ">
	<h1 style="text-align:left;">Contest<?php echo $cid ?> -- <?php echo $title ?>
	<a class='ui small blue button' href="contestrank-oi.php?spa&down&cid=<?php echo $cid ?>" >Download</a> 
	<a class='ui small green button' href="contestrank5.php?cid=<?php echo $cid?>" ><?php echo $MSG_REVIEW_CONTESTRANK ?></a>
 	<button  class='ui small yellow button' onclick='$("tr[class!=active]").toggle();'>Show/Hide</button>
	</h1>
</div>
<div id="rankdiv" class="padding" onclick="$(this).css('margin-left',(548-window.innerWidth/2)+'px').css('margin-right',(548-window.innerWidth/2)+'px')" style="margin-left:-200px;margin-right:-200px;overflow:auto" >
	<?php if ($user_cnt > 0) { ?>
		<table   <?php if (isset($_GET['down'])) echo "border=1" ?>
                                 class="ui very basic center aligned table" style="margin:30px" >
			<thead>
				<tr class='active'>
					    <td class="{sorter:'false'} text-center"><?php echo $MSG_STANDING?></td>
				            <td class='text-center'><?php echo $MSG_USER?></td>
				            <td class='text-center'><?php echo $MSG_NICK?></td>
				            <td class='text-center'><?php echo $MSG_SOVLED?></td>
				            <td class='text-center'><?php echo $MSG_CONTEST_PENALTY?></td>
						<td class='text-center'><?php echo $MSG_MARK ?></td>
					<?php
					for ($i = 0; $i < $pid_cnt; $i++)
						echo "<th><a href=problem.php?cid=$cid&pid=$i>$PID[$i]</a></th>";
					?>
					
				</tr>
			</thead>
			<tbody>
				<?php
				$rank = 1;
				for ($i = 0; $i < $user_cnt; $i++) {
					$uuid = $U[$i]->user_id;
					$nick = $U[$i]->nick;
					$usolved = $U[$i]->solved;
					echo "<tr onclick='$(this).attr(\"class\",\"active\");'  ondblclick='$(this).attr(\"class\",\"\");' >";
					echo "<td>";
					if ($nick[0] != "*") {
						if ($rank == 1)
							echo "<div class=\"ui yellow ribbon label\">";
						else if ($rank == 2)
							echo "<div class=\"ui ribbon label\">";
						else if ($rank == 3)
							echo "<div class=\"ui brown ribbon label\" style=\"background-color: #C47222 !important;\">";
						else
							echo "<div>";
						echo $rank++;
						echo "</div>";
					} else
						echo "*";
					echo "</td>";

					if (isset($_GET['user_id']) && $uuid == $_GET['user_id'])
						echo "<td bgcolor=#ffff77>";
					else
						echo "<td>";
					echo "<a name=\"$uuid\" href=userinfo.php?user=$uuid>$uuid</a>";
					echo "</td>";

					echo "<td>";
					echo  htmlentities($U[$i]->nick, ENT_QUOTES, "UTF-8");
					echo "</td>";
					
 					echo "<td>";
                                        echo  htmlentities($U[$i]->solved, ENT_QUOTES, "UTF-8");
                                        echo "</td>";

					echo "<td>";
					echo sec2str($U[$i]->time);
					echo "</td>";

					echo "<td class='ui small header'>";
					echo "<a href=status.php?user_id=$uuid&cid=$cid>" . ($U[$i]->total) . "</a>";
					echo "</td>";
					if(isset($_GET['down']))
                                                $tag="b";
                                        else
                                                $tag="div";

					for ($j = 0; $j < $pid_cnt; $j++) {
						if (isset($U[$i])) {
							if (isset($U[$i]->p_ac_sec[$j]) && $U[$i]->p_ac_sec[$j] > 0) {
								if ($uuid == $first_blood[$j]) {
									echo "<td style=\"background: rgb(" . (150 + 12 * $U[$i]->p_wa_num[$j]) . ", 255, " . min(230, 150 + 8 * $U[$i]->p_wa_num[$j]) . "); position:relative;\">";
									echo "<$tag style=\"position:absolute;width:30%;margin-top: 5%;margin-right: 5%;height:30%;right:0px;top:0px;\"> <font color='red'>※1st</font></$tag>";
								} else {
									echo "<td style=\"background: rgb(" . (150 + 12 * $U[$i]->p_wa_num[$j]) . ", 255, " . min(230, 150 + 8 * $U[$i]->p_wa_num[$j]) . "); \">";
								}
								if (isset($U[$i]->p_wa_num[$j]) && $U[$i]->p_wa_num[$j] > 0) {
									echo "<span class=\"score score_10\">";
									echo $U[$i]->p_pass_rate[$j]*100;
									echo "</span>";
								} else {
									echo "<span class=\"score score_10\">";
									echo $U[$i]->p_pass_rate[$j]*100;
									echo "</span>";
								}
                                                                echo " <$tag class=\"submit_time\">";
                                                                echo sec2str($U[$i]->p_ac_sec[$j]);
                                                                echo "</$tag>";

							} else if (isset($U[$i]->p_wa_num[$j]) && $U[$i]->p_wa_num[$j] > 0) {
								echo "<td style=\"background: rgb(255, " . max(130, 240 - 9 * $U[$i]->p_wa_num[$j]) . ", " . max(130, 240 - 9 * $U[$i]->p_wa_num[$j]) . "); \">";
								echo "<span class=\"score score_0\">";
								echo $U[$i]->p_pass_rate[$j] * 100;
								echo "</span>";
							} else {
								echo "<td>";
							}
						} else {
							echo "<td>";
						}

						echo "</td>";
					}
					

					echo "</tr>";
				}
				?>

			</tbody>
		</table>

	<?php } else { ?>
		<div style="background-color: #fff; height: 18px; margin-top: -18px; "></div>
		<div class="ui placeholder segment" style="margin-top: 0px; ">
			<div class="ui icon header">
				<i class="ui file icon" style="margin-bottom: 20px; "></i>
				暂无选手提交
			</div>
		</div>
	<?php } ?>

</div>
<?php include("template/$OJ_TEMPLATE/footer.php"); ?>

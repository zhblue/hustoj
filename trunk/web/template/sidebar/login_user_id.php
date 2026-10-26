<?php 
ini_set("display_errors", "On");  //set this to "On" for debugging  ,especially when no reason blank shows up.
	require(dirname(dirname(dirname(__FILE__)))."/include/db_info.inc.php");
        if(isset($_SESSION[$OJ_NAME.'_'.'user_id'])) {
                   
                ?>
                <a href="<?php echo $path_fix?>/userinfo.php?user=<?php echo htmlentities($_SESSION[$OJ_NAME.'_'.'user_id'])?>"
                    style="color: inherit; ">
                    <div class="ui simple dropdown item">
                        <?php echo htmlentities($_SESSION[$OJ_NAME.'_'.'user_id']); 
                              if(!empty($_SESSION[$OJ_NAME.'_nick'])) echo "(".htmlentities($_SESSION[$OJ_NAME.'_nick']).")";
                              if(!empty($_SESSION[$OJ_NAME.'_group_name'])) echo "[".htmlentities($_SESSION[$OJ_NAME.'_group_name'])."]";
                              if($coin>0) echo "💰️$coin";        
                        ?>
                        <i class="dropdown icon"></i>
                        <div class="menu">
                                <a class="item" href="modifypage.php"><i class="edit icon"></i><?php echo $MSG_REG_INFO;?></a>
                                <a class="item" href="portal.php"><i class="tasks icon"></i><?php echo $MSG_TODO;?></a>
                                <?php if ($OJ_SaaS_ENABLE){ ?>
                                <?php if($_SERVER['HTTP_HOST']==$DOMAIN)
                                        echo  "<a class='item' href='http://".htmlentities($_SESSION[$OJ_NAME.'_'.'user_id']).".$DOMAIN'><i class='globe icon' ></i>MyOJ</a>";?>
                                <?php } ?>
                            <?php if(isset($_SESSION[$OJ_NAME.'_'.'administrator'])||isset($_SESSION[$OJ_NAME.'_'.'contest_creator'])||isset($_SESSION[$OJ_NAME.'_'.'user_adder'])||isset($_SESSION[$OJ_NAME.'_'.'password_setter'])||isset($_SESSION[$OJ_NAME.'_'.'problem_editor'])){ ?>
                            <a class="item" href="admin/"><i class="settings icon"></i><?php echo $MSG_ADMIN;?></a>
                            <?php }
if(isset($_SESSION[$OJ_NAME.'_'.'balloon'])){
  echo "<a class=item href='balloon.php'><i class='golf ball icon'></i>$MSG_BALLOON</a>";
}
                              if((isset($OJ_EXAM_CONTEST_ID)&&$OJ_EXAM_CONTEST_ID>0)||
                                     (isset($OJ_ON_SITE_CONTEST_ID)&&$OJ_ON_SITE_CONTEST_ID>0)||
                                     (isset($OJ_MAIL)&&!$OJ_MAIL)){
                                      // mail can not use in contest or mail is turned off
                              }else{
                                    $mail=checkmail();
                                    if($mail) echo "<a class='item mail' href=".$path_fix."mail.php><i class='mail icon'></i>$MSG_MAIL$mail</a>";
                              }




                            ?>
        <?php
        if(isset($OJ_PRINTER) && $OJ_PRINTER)
        {
        ?>
          <a  class="item"  href="printer.php">
            <i class="print icon"></i> <?php echo $MSG_PRINTER?>
          </a>
        <?php
        }
        ?>
                            <a class="item" href="logout.php"><i class="power icon"></i><?php echo $MSG_LOGOUT;?></a>
                        </div>
                    </div>
                </a>
                <?php } else { ?>


                <div class="item">
                    <a class="ui button" style="margin-right: 0.5em; " href="loginpage.php">
                       <?php echo $MSG_LOGIN?>
                    </a>
                    <?php if(isset($OJ_REGISTER)&&$OJ_REGISTER ){ ?>
                    <a class="ui primary button" href="registerpage.php">
                       <?php echo $MSG_REGISTER?>
                    </a>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>

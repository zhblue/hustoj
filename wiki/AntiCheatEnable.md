#summary How to enable anti-cheat with SIM

= Introduction =

if you want to enable AntiCheat on hustoj, follow the steps


= Details =
  # update web and core using svn or the [AutoUpdateShellScript]
  # add in judge.conf

OJ_SIM_ENABLE=1 

  # add in db_info.inc.php

  $OJ_SIM=true;

  # create table in mysql

CREATE TABLE `sim` (



  `s_id` int(11) NOT NULL,

  `sim_s_id` int(11) NULL,

  `sim` int(11) NULL,



  PRIMARY KEY  (`s_id`)



) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;




 * THIS FUNCTION REQUEST A LARGE MOUNT OF FILE STORAGE IN LONG TERM RUNNING *

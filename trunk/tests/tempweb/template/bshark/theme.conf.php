<?php
$THEME_SHOW_CATEGORY = false; //Whether to display a list of category on the Problemset page
$THEME_NEWS_MOD = "list"; // list or show
$THEME_MOD = "light"; //light or dark
$THEME_BANNER = "hidden"; //show or hidden
$THEME_HOME_STATISTICS = "hidden"; //show or hidden
$THEME_LOGIN_BG = "./template/bshark/statics/bg.jpg";
$THEME_AUTO_GET_LATEST_INFO = "no"; // yes or no.Will use ajax to get information. Here is the code.
/*
$.ajax({url:"https://vt-dev-team.github.io/bshark/version",async:false}).responseText
*/

/* ================================
You cannot modify the following configuration, otherwise unknown bugs may occur
*/
$THEME_BSHARK_VERSION = "V21.8.15";
// You can visit https://vt-dev-team.github.io/bshark/version.html to check the lastest version

/* The followings is not finished */

$THEME_CAPTCHA = "none"; // Img CAPTCHA is too ugly, you can use any item in ["none", "easy", "medium", "hard", "hard++"]
/* CAPTCHA example
1. none: None captcha
2. easy: 1+2=?, 5-1=?
3. medium: +, -, *
4. hard: +, -, *, log, ln
5. hard++: ∫ and more
*/
?>
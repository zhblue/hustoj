<?php
if (strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) < 9) {
    require_once("include/db_info.inc.php");
    require_once("include/my_func.inc.php");
    $token = getToken();
    if (!isset($_SESSION[$OJ_NAME . '_' . 'csrf_keys'])) {
        $_SESSION[$OJ_NAME . '_' . 'csrf_keys'] = array();
    }
    array_push($_SESSION[$OJ_NAME . '_' . 'csrf_keys'], $token);
    while (count($_SESSION[$OJ_NAME . '_' . 'csrf_keys']) > 10)
        array_shift($_SESSION[$OJ_NAME . '_' . 'csrf_keys']);

    ?><input type="hidden" name="csrf" value="<?php echo $token ?>"
             class="<?php echo in_array($token, $_SESSION[$OJ_NAME . '_' . 'csrf_keys']) ?>">
    <?php

}

<?php if(isset($OJ_LANG)){
		require_once(dirname(__FILE__)."/../lang/$OJ_LANG.php");
		if(file_exists("./faqs.$OJ_LANG.php")){
			$OJ_FAQ_LINK="./faqs.$OJ_LANG.php";
		}
	}else{
		require_once("./lang/en.php");
	}
	?>

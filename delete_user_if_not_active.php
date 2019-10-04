<?php
include 'class/class.BUSINESSLOGIC.php';
include 'class/class.utilities.php';
include 'class/class.SINGLETON_MODEL.php';
date_default_timezone_set('Asia/Ho_Chi_Minh');
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(0);

$utl = SINGLETON_MODEL::getInstance("UTILITIES");
$dbf = new BUSINESSLOGIC();
// This code is use for delete user after x time if not active
$result = $dbf->getDynamic("member","SUBDATE(CURRENT_DATE(), INTERVAL 24 HOUR) >= datecreated AND crf_token_login <> ''","");
	if ($dbf->totalRows($result) > 0) {
		 while ($row = $dbf->nextData($result)) {
			 
			$dbf->deleteDynamic("member", "id='" . $row['id'] . "'");
		}			
	 }
?>
		
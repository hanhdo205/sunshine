<?php
session_start();
error_reporting(0);
include ('../../class/defineConst.php');
include ('../../class/class.BUSINESSLOGIC.php');
include ('../../class/class.SINGLETON_MODEL.php');
$dbf = SINGLETON_MODEL::getInstance("BUSINESSLOGIC");


// Include Language file
		if(isset($_SESSION['language'])){
		 include "../languages/lang_".$_SESSION['language'].".php";
		}else{
		 include "../languages/lang_en.php";
		}
		
$id = $_POST['id'];

$dbf->deleteDynamic("events", "id=" . $id);	

?>
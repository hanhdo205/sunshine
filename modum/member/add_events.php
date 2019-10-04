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
		
$result = array("status"=>0,"data"=>"","msg"=>"No access");
if ($_REQUEST["title"]!="") 
{
		
	$title      = $_REQUEST["title"];
	$start      = $_REQUEST["start"];
	$end       = $_REQUEST["end"];
	$desc       = $_REQUEST["desc"];
	$allday       = $_REQUEST["allday"];
	
	if ($title != "") 
	{
		$result["status"]=1;
		if ( 0 < $_FILES['file']['error'] ) {
			echo 'Error: ' . $_FILES['file']['error'] . '<br>';
		}
		else {
			move_uploaded_file($_FILES['file']['tmp_name'], '../../upload/' . $_FILES['file']['name']);
		}
		$str_deposit=_EVENTCREATED;
		$array_events = array("title"=>$title,"content"=>$desc,"start"=>$start,"end"=>$end,"allday"=>$allday,"pic"=>$_FILES['file']['name']);
		$dbf->insertTable("events", $array_events);	
	} 
	
	if($result["status"]==0) 
	{
	   $str_deposit=_NOSAVE;
	}
    	
	$result["data"]=$str_deposit;
	
}

header("Content-type:application/json"); 
echo json_encode($result);
// IMPORTANT: don't forget to "exit"
die();
?>
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
		
$connect = new PDO('mysql:host='.HOSTADDRESS.';dbname='.DBNAME.'', DBACCOUNT, DBPASSWORD);
$result = array("status"=>0,"data"=>"","msg"=>"No access");
if(isset($_REQUEST["id"]))
{
	$result["status"]=1;
	
	if(isset($_FILES['file']) && @$_FILES['file']['name'] != ""){
		if ( 0 < $_FILES['file']['error'] ) {
			echo 'Error: ' . $_FILES['file']['error'] . '<br>';
		}
		else {
			move_uploaded_file($_FILES['file']['tmp_name'], '../../upload/' . $_FILES['file']['name']);
		}
		$query = "UPDATE events SET title=:title, start=:start_event, end=:end_event, content=:desc, allday=:allday, pic=:pic WHERE id=:id";
			
		 $statement = $connect->prepare($query);
		 $statement->execute(
		  array(
		   ':title'  => $_REQUEST['title'],
		   ':start_event' => $_REQUEST['start'],
		   ':end_event' => $_REQUEST['end'],
		   ':desc' => $_REQUEST['desc'],
		   ':id'   => $_REQUEST['id'],
		   ':allday' => $_REQUEST['allday'],
		   ':pic' => $_FILES['file']['name']
		  )
		 );
	} else {
		$query = "UPDATE events SET title=:title, start=:start_event, end=:end_event, content=:desc, allday=:allday WHERE id=:id";
			
		 $statement = $connect->prepare($query);
		 $statement->execute(
		  array(
		   ':title'  => $_REQUEST['title'],
		   ':start_event' => $_REQUEST['start'],
		   ':end_event' => $_REQUEST['end'],
		   ':desc' => $_REQUEST['desc'],
		   ':id'   => $_REQUEST['id'],
		   ':allday' => $_REQUEST['allday']
		  )
		 );
	}
	$str_deposit=_DATAINSERTED;
}
if($result["status"]==0) 
	{
	   $str_deposit=_NOSAVE;
	}
    	
	$result["data"]=$str_deposit;
	
header("Content-type:application/json"); 
echo json_encode($result);
// IMPORTANT: don't forget to "exit"
die();
?>
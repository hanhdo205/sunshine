<?php
session_start();
error_reporting(0);
include ('../../class/defineConst.php');
include ('../../class/class.BUSINESSLOGIC.php');
include ('../../class/class.SINGLETON_MODEL.php');
$dbf = SINGLETON_MODEL::getInstance("BUSINESSLOGIC");


// Include Language file
		if(isset($_SESSION['lang'])){
		 include "../languages/lang_".$_SESSION['lang'].".php";
		}else{
		 include "../languages/lang_en.php";
		}
$connect = new PDO('mysql:host='.HOSTADDRESS.';dbname='.DBNAME.'', DBACCOUNT, DBPASSWORD);
$data = array();
$query = "SELECT * FROM events ORDER BY id";

$statement = $connect->prepare($query);

$statement->execute();

$result = $statement->fetchAll();

foreach($result as $row)
{
 $data[] = array(
  'id'   => $row["id"],
  'title'   => $row["title"],
  'description' => $row["content"],
  'start'   => $row["start"],
  'end'   => $row["end"],
  'all_day' => $row["allday"],
  'pic' => $row["pic"]
 );
}

echo json_encode($data);
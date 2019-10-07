<?php
session_start();
error_reporting(0);
include ('../../class/defineConst.php');
include ('../../class/class.BUSINESSLOGIC.php');
include ('../../class/class.SINGLETON_MODEL.php');
$dbf = SINGLETON_MODEL::getInstance("BUSINESSLOGIC");

$data = array();

$result = $dbf->getDynamic("events","","");
if ($dbf->totalRows($result) > 0) {
	 while ($row = $dbf->nextData($result)) {
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
}

echo json_encode($data);
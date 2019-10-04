<?php
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
$result = array("status"=>0,"data"=>"","msg"=>"No access");
if ($_REQUEST["member_id"]!="") 
{
	
	require('coinpayments.inc.php');
	$member_id      = $_REQUEST["member_id"];
	$orderdate      = $_REQUEST["orderdate"];
	$paiddate      = $_REQUEST["paiddate"];

	$totalsale        = $_REQUEST["totalsale"];
	$totalamount 	= $_REQUEST["totalamount"];
    $deliverycomment      = $_REQUEST["deliverycomment"];

	$salepaid        = $_REQUEST["salepaid"];
	$amountpaid = $_REQUEST["amountpaid"];
    $paidcomment      = $_REQUEST["paidcomment"];

	if ($totalsale != "") 
	{
		$result["status"]=1;
		
		$str_deposit=_DATAINSERTED;
		$array_sales = array("member_id"=>$member_id,"price"=>$totalamount,"quantity"=>$totalsale,"datecreated"=>strtotime($orderdate),"dateupdated"=>time(),"status"=>0,"comment"=>$deliverycomment);									
		$dbf->insertTable("history_sales", $array_sales);	
	} 
	if ($salepaid != "") 
	{
		$result["status"]=1;
		$str_deposit=_DATAINSERTED;
		$array_paids = array("member_id"=>$member_id,"price"=>$amountpaid,"quantity"=>$salepaid,"datecreated"=>strtotime($paiddate),"dateupdated"=>time(),"status"=>0,"comment"=>$paidcomment);									
		$dbf->insertTable("history_payment", $array_paids);	
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
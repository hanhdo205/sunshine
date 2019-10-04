<?php
session_start();
include str_replace('\\','/',dirname(__FILE__)).'/class/class.DEFINE.php';
include str_replace('\\','/',dirname(__FILE__)).'/class/class.HTML.php';
include str_replace('\\','/',dirname(__FILE__)).'/class/class.JAVASCRIPT.php';
include str_replace('\\','/',dirname(__FILE__)).'/class/class.UTILITIES.php';
include str_replace('\\','/',dirname(__FILE__)).'/class/class.CSS.php';
include str_replace('\\','/',dirname(__FILE__)).'/class/class.SINGLETON_MODEL.php';
include str_replace('\\','/',dirname(__FILE__)).'/class/simple_html_dom.php';
include str_replace('\\','/',dirname(__FILE__)).'/class/class.BUSINESSLOGIC.php';


$dbf = SINGLETON_MODEL::getInstance("BUSINESSLOGIC");

$result = array("status"=>0,"data"=>"Please check ETH Address or Balance");
if (isset($_REQUEST["amount"]) && isset($_REQUEST["payment_id"]) && $_REQUEST["address"]!="" && $_REQUEST["address"]!="") 
{
	
	require('coinpayments.inc.php');
	$currency        = "ETH";
	$amount =0;
	$amount 		 = $_REQUEST["amount"];
    $payment_id      = $_REQUEST["payment_id"];
	$address 		 = $_REQUEST["address"];
	
	$cps = new CoinPaymentsAPI();
	$private_key = 'ecfD2193e8E224Ae03b4A576771bBDe58Ed4f4B115f62f9d6e5F4Aa0B91a68ae';
	$public_key  = '477c907df0f3921465c906464e24f5ced2ed5309557d97ce4428b1ae6a8009e8';
	$cps->Setup($private_key, $public_key);
	$ipn_url = "https://www.diamondcapital.co/ipn_handler_payment.php";	
	
	$result_payment = $cps->CreateWithdrawal($amount, $currency, $address, FALSE, $ipn_url);	
	if ($result_payment['error'] == 'ok') 
	{
		$value = array("confirm_payment"=>1);
        $where = "id = '".$payment_id."'";
        $affect=$dbf->updateTable("btc_tranfer",$value,$where);		
		$result["status"]=1;
		$total_deposit   = $result_payment['result']["amount"]; 		
		$array_col = array("member_id"=>$payment_id,"coin"=>$coin,"amount"=>$result_payment['result']["amount"],"amount2"=>$result_payment['result']["amount2"],"txn_id"=>$result_payment['result']["txn_id"],"address"=>$result_payment['result']["txn_id"],"confirms_needed"=>$result_payment['result']["confirms_needed"],"confirms_needed"=>$result_payment['result']["confirms_needed"],"status_url"=>$result_payment['result']["status_url"],"qrcode_url"=>$result_payment['result']["qrcode_url"],"datecreated"=>time(),"status"=>0);									
		$dbf->insertTable("transaction_payment", $array_col);	
	} 
	else 
	{
	   $str_deposit='Error: '.$result_payment['error']."\n";
	}
    	
	$result["data"]=$str_deposit;
	
}

header("Content-type:application/json"); 
echo json_encode($result);
?>
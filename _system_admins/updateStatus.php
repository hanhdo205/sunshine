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

$dbf2 = SINGLETON_MODEL::getInstance("BUSINESSLOGIC");


    $packages_id	= $_GET["packages_id"];
	$id     		= $_GET["id"];
    $status 		= $_GET["status"];
    if((int)$id !=0 && $packages_id!="")
    {
        $info_member 		= $dbf2->getInfoColum("member",$id);
		$infonguoibaotro    = $dbf2->getInfoColum("member",$rowgetInfo["parentid"]);
        $info_packages      = $dbf2->getInfoColum("packages",$packages_id);
		
		$arrayPackeges = array();
		$result = $dbf2->getDynamic("packages","status=1","id asc");
		while( $row = $dbf2->nextData($result))
		{
		   $arrayPackeges[$row["id"]] = $row;
		}	
		$max_out = ($info_packages["price"]*250)/100;
		$value = array("packages_id"=>$packages_id,"max_out"=>$max_out,"active_register"=>1,"status"=>$status,"datecreated"=>time(),"dateupdated"=>time());
        $where = "id = '".$id."'";
        $affect=$dbf2->updateTable("member",$value,$where);
        if($affect)
        {
          echo 1;
		  if($status==1)
		  {
			   $info_balance_wallet = array();
               $info_balance_wallet = $dbf->getInfoColumBalance("balance_wallet",$id);
														
			   $price_udate_member = ($info_balance_wallet["price"] - ($info_packages["price"]+10)); 
			   $array_col = array("price"=>$price_udate_member);
			   $dbf->updateTable("balance_wallet", $array_col, "member_id='" . $id . "'");

				/*insert CoinDMCC >>*/												
				$info_coinDCMM= $dbf->checkCoinDCMM($info_member["id"]);
				if(!$info_coinDCMM)												
				{
				   $coinDCMM = $info_coinDCMM["price"] + $arrayPackeges[$packages_id]["coin_dmcc"];                                                   											   
				   $array_col = array("price"=> $coinDCMM);
				   $affect2 = $dbf->updateTable("coin_wallet", $array_col, "member_id='" . $id . "'");												                    
					
				}else
				{
					$coinDCMM = $arrayPackeges[$packages_id]["coin_dmcc"];		
					$array_col = array("member_id"=>$id,"price"=>$coinDCMM,"status"=>1);
					$affect2 = $dbf->insertTable("coin_wallet", $array_col);
				}
				 /*  << */												
			  
			  /***************************/
				$result_eth  = json_decode(file_get_contents(HOST."response_eth.json"));
				$usd_eth     = $result_eth->data->amount;
				$quantity    = $info_packages["price"]/$usd_eth;
				
				$array_col_investment = array("member_id"=>$id,"quantity"=>$quantity,"quantity_usd"=>$info_packages["price"],"address_eth"=>$info_member["eth_address"],"hash"=>"","blockNumber"=>"","datecreated"=>time(),"status"=>1);									
				$dbf->insertTable("member_invest",$array_col_investment);
			   
				/*printf("<pre>%s</pre>",print_r($info_member,true)); */
				if($info_member["sponser_id"]!=0)
				{
					$info_sponser = $dbf->getInfoColum("member",$info_member["sponser_id"]);
					/*printf("<pre>%s</pre>",print_r($info_sponser,true)); */
					
					if($info_sponser["status"]==1)
					{
						/*check maxout and 250% */														
						$datenow                = date("d-m-Y",time());
						$totalMaxout            = $dbf->CheckMaxoutIncome($info_member["sponser_id"],strtotime($datenow));
						$totalMaxout250 		= $dbf->CheckMaxoutIncome250($info_member["sponser_id"]);														
						/*$totalMaxout250_member  = ($arrayPackeges[$info_sponser["packages_id"]]["price"]*250)/100;*/
						
						if((int)$info_sponser["max_out"]==0)
						{
							$totalMaxout250_member  = ($arrayPackeges[$info_sponser["packages_id"]]["price"]*250)/100;
						}else
						{
							$totalMaxout250_member = $info_sponser["max_out"];
						}
						
						$price_direct = ($arrayPackeges[$info_sponser["packages_id"]]["f1"]*$info_packages["price"])/100;									
						
						
						/*check maxout 250% */
						if($totalMaxout250<$totalMaxout250_member)
						{
							if(($totalMaxout250+$price_direct) > $totalMaxout250_member)
							{
								$price_direct = $totalMaxout250_member - $totalMaxout250;
							}
							
							/* check maxout by day */
							if($totalMaxout < $arrayPackeges[$info_sponser["packages_id"]]["price"])
							{
								if(($totalMaxout+$price_direct) > $arrayPackeges[$info_sponser["packages_id"]]["price"])
								{
									$price_direct = $arrayPackeges[$info_sponser["packages_id"]]["price"] - $totalMaxout;
								}
								
								$array_col_direct = array("ponser_id"=>$info_member["sponser_id"],"member_id"=>$info_member["id"],"price"=>$price_direct,"datecreated"=>strtotime(date("Y-m-d",time())),"status"=>1);									
								$dbf->insertTable("incomedirect", $array_col_direct);
							}
							
							
						}
						/*f2->f8*/														
					}
					
					if($info_sponser["sponser_id"])
					{															
						$dbf->IncomeIndirectMember_F2_F8($info_sponser["sponser_id"],$info_member,$packages_id,$arrayPackeges,$info_packages,2);
					}
					
				}										  
			  
			  /***************************/											  
			  if($info_member["email"])
				{
				$arraySMTPSERVER = array("host" => "capitalvs.net", "user" => "info@capitalvs.net", "password" => "m{Q]EI+qNZmD", "from" => "Diamond Capital");
				$subject ="Diamond Capital Investment Successfull";

				$headers.= "From: info@capitalvs.net\n";
				$headers.= "MIME-Version: 1.0\n";
				$headers.= "Content-Type: text/html; charset=\"UTF-8\"\n";
				
				$message='<table border="1" cellpadding="0" cellspacing="0" style="border:solid #e7e8ef 3.0pt;font-size:10pt;font-family:Calibri" width="600">
					<tbody><tr style="border:#e7e8ef;padding:0 0 0 0">
						<td style="padding-left:15pt" colspan="2">
							<br>
							<img alt="Diamond Capital" src="'.HOSTS.'themes/diamond_capital/images/logo.png" class="CToWUd"><br>
							<br>
						</td>
					</tr>
					<tr>
						<td width="25" style="border:white">
							&nbsp;
						</td>
						<td style="border:white">
							<br>
							<h1><span style="font-size:19.0pt;font-family:Verdana;color:black">'.$subject.'</span></h1>
							<br>
						</td>
					</tr>
					<tr>
						<td width="25" style="border:white">
							&nbsp;
						</td>
						<td style="border:white">
							<div style="color:#818181;font-size:10.5pt;font-family:Verdana"><span class="im">
								Dear <a href="mailto:'.$info_member["email"].'" target="_blank">'.$info_member["email"].'</a>,<br>
								
								<br></span>	

								<p style="text-align:left">
								  <strong>Package Investment: '.$info_packages["title"].'</trong>
								</p>																
								
								<p style="text-align:left">
								  <strong>Amount Investment: $'.number_format($info_packages["price"]).'</trong>
								</p>
							   <p style="text-align:left">
								  <strong>ID: '.$info_member["ma_id"].'</trong>
							   </p>
							   <p style="text-align:left">
								  <strong>Username: '.$info_member["tendangnhap"].'</trong>
							   </p>															  
							  
								
								<br>
								<br>
								<br>
								Best regards,<br>
								Diamond Capital<br>
							</span></div>									
						</td>
					</tr>
					<tr>
						<td width="25" style="border:white">
							&nbsp;
						</td>
						<td style="border:white">
							<div style="color:#818181;font-size:9pt;font-family:Verdana">
								<br>
								<br>
							</div>
						</td>
					</tr>
					<tr>
						<td colspan="2" style="height:30pt;background-color:#e7e8ef;border:none">
							<center>You are receiving this email because you registered on <a href="'.HOST.'" style="color:#5b9bd5" target="_blank">'.HOST.'</a><br></center>
						</td>
					</tr>
				</tbody></table>';
				
					require("modum/class.phpmailer.php");
					$mail = new PHPMailer();
					$SMTP_Host = $arraySMTPSERVER["host"];
					$SMTP_Port = 25;
					$SMTP_UserName = $arraySMTPSERVER["user"];
					$SMTP_Password = $arraySMTPSERVER["password"];
					$from = $SMTP_UserName;
					$fromName = "DIAMOND CAPITAL";
					$to = $info_member["email"];
					$mail->IsSMTP();
					$mail->Host = $SMTP_Host;
					$mail->Port = $SMTP_Port;
					$mail->SMTPSecure = 'tls';
					$mail->SMTPAuth = true;
					$mail->Username = $SMTP_UserName;
					$mail->Password = $SMTP_Password;
					$mail->From = $from;
					$mail->FromName = $fromName;
					$mail->Sender=$info['email'];
					$mail->AddAddress($to);
					$mail->AddReplyTo($info['email'], $fromName);
					$mail->SMTPDebug  = 1;
					$mail->IsHTML(true);
					$mail->Subject = $subject;
					$mail->Body = $message;
					$mail->AltBody = "This is the body in plain text for non-HTML mail clients";
					$mail->Send();
				}

		  
		  }else
		  {
			//delete
            $sql1 = "DELETE FROM Incomeindirect WHERE member_id=".$id."";  			
			$sql2 = "DELETE FROM incomedirect WHERE member_id=".$id."";
			$sql3 = "DELETE FROM all_withdrawals WHERE member_id=".$id."";
			$sql4 = "DELETE FROM member_invest WHERE member_id=".$id."";
			
			try {
			
			   $dbf2->doSQL($sql1);
			   $dbf2->doSQL($sql2);	
			   $dbf2->doSQL($sql3);	
			   $dbf2->doSQL($sql4);	
			   
			}catch (Exception $ex)
			{
			  
			}
		  
		  }
		  
        }else
        {
          echo 0;
        }
    }else
    {
      echo 0;
    }
?>
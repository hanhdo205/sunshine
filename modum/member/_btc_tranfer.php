<?php

if( $_SESSION["Free"]==1)
{
  $html->redirectURL("/system");
}else
{
  /*
  if(!isset($_SESSION["password2"]) || $_SESSION["password2"]=="")
  {
      $html->redirectURL("/confirm_by_password.aspx?redirect_page=withdraw");
      exit();
  }
  */
  
    require ("recaptcha-master/src/autoload.php");
	// Register API keys at https://www.google.com/recaptcha/admin
	$siteKey = '6LeDQykTAAAAAPam2aleQ1qhT_erWJUarWWCWBlU';
	$secret = '6LeDQykTAAAAAPqOXMc5ECBd45zTF_SCQgZLddPx';
	// reCAPTCHA supported 40+ languages listed here: https://developers.google.com/recaptcha/docs/language
	$lang = 'en';

  $total_price_withdraw = $dbf->TotalPriceMember("all_withdrawals",$_SESSION["member_id"]);
  $total_price_tranfer = $dbf->TotalPriceMember("btc_tranfer",$_SESSION["member_id"]);
  $total_price_have = $total_price_withdraw - $total_price_tranfer;
    
  if(count($info_balance_wallet))
  {
	$TotalPIN = $info_balance_wallet["price"];
	$total_price_have+=$TotalPIN;
  }

?>

<link href="/css/system/template/css/main.css" rel="stylesheet">

<section id="main">
	<!-- WRAP -->
	<div class="wrap">
	 <section id="content">
            <div id="main-container">
                  <div id="page-content" style="min-height: 318px;">
                      <div class="block">
                           <div class="block-title">
                              <h2>Withdraw</h2>
                           </div>
                           <?php
						   
						    $day = date("d");
							$isValue = true;
							if((int)$day!=8 && (int)$day!=18 && (int)$day!=28)							
							{
								$isValue = false;
								 echo '<div class="alert alert-danger alert-dismissable">
								   <h4><strong>Notice</strong></h4>
								   <p>Withdrawal only on 08th and 18th and 28th of each month. You come back later, please !</p>
								</div>';
								
							}
						   
						   
                            if (isset($_POST["submit_transfer"])) 
							{

                              foreach ($_POST as $key => $value) {
                                $$key = $value;
                              }
                              $recaptcha = new \ReCaptcha\ReCaptcha($secret);
							  $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
							  if ($resp->isSuccess()) 
							  {
                              
                                  //echo $pass3;
								  $pass3 = md5($pass3);
								  $rstcheck = $dbf->getDynamic("member", "ma_id ='" . $rowgetInfo["ma_id"] . "' and password3='".$pass3."'", "");
								  if ($dbf->totalRows($rstcheck) > 0) {

										      if($total_price_have > 49 && $total_price_have>=$Comm_Money )
											  {
        										 try {
                                                        if($Comm_Money>49)
                                                        {
            										        if(trim($rowgetInfo["eth_address"])!="")
														    {		
																$array_col = array("member_id"=>$_SESSION["member_id"],"price"=>$Comm_Money,"btc"=>$btc,"status"=>0,"datecreated"=>time());
																$affect = $dbf->insertTable("btc_tranfer", $array_col);
																echo '<div class="alert alert-danger alert-dismissable alert-success">
																   <h4><strong>Notice</strong></h4>
																   <p>Transfer is successfull !!!</p>
																</div>';

																  $total_price_withdraw = $dbf->TotalPriceMember("all_withdrawals",$_SESSION["member_id"]);
																  $total_price_tranfer = $dbf->TotalPriceMember("btc_tranfer",$_SESSION["member_id"]);
																  $total_price_have = $total_price_withdraw - $total_price_tranfer;
																  if(count($info_balance_wallet))
																  {
																	$TotalPIN = $info_balance_wallet["price"];
																	$total_price_have+=$TotalPIN;
																  }

																foreach ($_POST as $key => $value) {
																	$$key = "";
																}
																unset($_POST);
															}else
															{
															 echo '<div class="alert alert-danger alert-dismissable">
            												   <h4><strong>Notice</strong></h4>
            												   <p>Transfer is error !!! Please check Eth address current</p>
            												</div>';
															}																
                                                        }else
                                                        {
															if($Comm_Money<49)
															{
																echo '<div class="alert alert-danger alert-dismissable">
            												   <h4><strong>Notice</strong></h4>
            												   <p>Transfer is error !!! Please check amount min >= $50</p>
            												</div>';
															}else
															{
																echo '<div class="alert alert-danger alert-dismissable">
            												   <h4><strong>Notice</strong></h4>
            												   <p>Transfer is error !!! Please check amount bitcoin current</p>
            												</div>';
															}	
                                                            
                                                        }

        										 }catch (Exception $e) {
        												echo '<div class="alert alert-danger alert-dismissable">
        												   <h4><strong>Notice</strong></h4>
        												   <p>'.$e.'</p>
        												</div>';
        										}

											  }else
											  {
												   echo '<div class="alert alert-danger alert-dismissable">
													   <h4><strong>Notice</strong></h4>
													   <p>Transfer is wrong !!! Because the amount transferred is greater than the money in your wallet</p>
													</div>';
											  }



								  } else
								  {
									   echo '<div class="alert alert-danger alert-dismissable">
										   <h4><strong>Notice</strong></h4>
										   <p>Transfer is wrong !!! Because password 2 is wrong</p>
										</div>';
								  }

                              }else
                              {
                                  echo '<div class="alert alert-danger alert-dismissable">
                                       <h4><strong>Notice</strong></h4>
                                       <p>Security Code is wrong !!! </p>
                                    </div>';
                              }
                            }

							if($isValue) {
                           ?>
                           <form action="" method="post" class="form-horizontal form-bordered">
                              <div class="form-group">
                                 <div class="row">
                                    <label class="col-md-3 control-label" for="Comm_Type">Total income <span class="text-danger">*</span></label>
                                    <input type="hidden" name="total_price_income_have" id="total_price_income_have" value="<?php echo $total_price_have;?>">
                                    <div class="col-md-6">
                                       <select id="Comm_Type" name="Comm_Type" class="form-control">
                                          <option value="1" selected="">Balance-wallet ($<?php echo number_format($total_price_have,2);?>)</option>
                                       </select>
                                    </div>
                                 </div>

                                 <div class="row">
                                    <label class="col-md-3 control-label" for="Comm_Money">Money (USD) <span class="text-danger">*</span></label>
                                    <div class="col-md-6">
                                        <input type="number" id="Comm_Money" name="Comm_Money" class="form-control" value="<?=$Comm_Money?>" required>
                                    </div>
                                 </div>
                                 
                                   <script>
                                          $("#Comm_Money").change(function() {
                                            var Comm_Money = parseFloat($("#Comm_Money").val());
                                            var total_price_income_have = parseFloat($("#total_price_income_have").val());
                                              if(Comm_Money <= total_price_income_have && Comm_Money > 49 && total_price_income_have > 49)
                                              {
                                                   $.ajax({url: "tranfer_btc.php?price="+Comm_Money, success: function(result){
                                                   var res = result.split("<!>");
                                                   if(res[0]==1){
                                                      $("#Comm_Money").css({"background-color":"blue","color":"#fff"});
                                                      $("#btc").val(res[1]);
                                                      //alert("An user's ID code invalid");
                                                   }else
                                                   {
                                                      $("#btc").css({"background-color":"red","color":"#fff"});
                                                      alert("Please Enter USD other !");
                                                   }
                                                }});
                                              }
                                              else
                                              {
                                                   if(total_price_income_have==0)
                                                   {
                                                     alert("Errot ! Don't convert USD to BTC with $"+total_price_income_have+"");
                                                   }else
                                                   {
                                                      if(Comm_Money < 50)
												      {
														alert("Error ! Please Enter USD other min  $50");  
													  }else{
														alert("Error ! Please Enter USD other small < $"+total_price_income_have+"");  
													  }		  
													  
                                                   }

                                              }

                                          });
                                   </script>

                                 <div class="row">
                                    <label class="col-md-3 control-label" for="Comm_Money">Security Code<span class="text-danger">*</span></label>
                                    <div class="col-md-3">
											<div class="g-recaptcha" data-sitekey="<?php echo $siteKey; ?>"></div>
											<script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=<?php echo $lang; ?>"></script>
                                    </div>
                                 </div>
                              </div>
							  
							 
							  
                              <div class="form-group form-actions">
							    <div class="row">
                                 <label class="col-md-3 control-label" for="User_RePassword2">&nbsp;</label>
                                 <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col-md-offset-6">
                                    <div class="input-group"> <input type="password" name="pass3" value="" class="form-control" placeholder="Password 2" required>
                                    <span class="input-group-btn"> <button type="submit" name="submit_transfer" class="btn btn-effect-ripple btn-primary" style="overflow: hidden; position: relative;">External transfer</button> </span> </div>
                                 </div>
								 </div>
                              </div>
							  
                           </form>
						   
						   <?php } ?>
						   
                        </div>


               <div class="block">
                  <div class="block-title">
                     <h2>Withdraw history</h2>
                  </div>
                   <div class="table-responsive">
                         <div class="dataTables_wrapper form-inline no-footer">
                            <table class="table table-striped table-bordered table-vcenter table-hover dataTable no-footer" role="grid" aria-describedby="example-datatable_info">
                               <thead>
                                  <tr role="row">
                                     <th class="text-center sorting"><span style="color:#2c3e50">No.</span></th>
									 <th class="text-center sorting"><span style="color:#2c3e50">Date</span></th>
                                     <th class="text-center sorting"><span style="color:#2c3e50">USD</span></th>
                                     <th class="text-center sorting"><span style="color:#2c3e50">Status</span></th>
                                     
                                  </tr>
                               </thead>

                               <tbody>
                                  <?php


                                           $result2 = $dbf->getDynamic("btc_tranfer", "member_id =" . $_SESSION["member_id"] . "", "id desc");
                                           $total2 = $dbf->totalRows($result2);
                                           if ($total2 > 0) {
                                           $i=1;
                                           while ($row2 = $dbf->nextData($result2)) {
											   $price_pecent = (3*$row2["price"])/100;
                                           echo'<tr role="row">
                                                 <td class="text-center">' . $i . '</td>
												 <td class="text-center">' . date("Y-m-d H:i:s",$row2['datecreated']) . '</td>
                                                 <td class="text-left"> $' . number_format(($row2["price"]-$price_pecent),1).'</td>
                                                 <td class="text-left">'.(($row2["status"]==1)?"Success":"Pending").'</td>
                                                 
                                              </tr>';
                                            $i++;
                                            }
                                            }

                                  ?>

                               </tbody>
                            </table>

                         </div>
                      </div>

                  </div>
               </div>
            </div>

<div class="clearfix"></div>
     </section>
</div>
<div class="clearfix"></div>
</section>
<div class="clearfix"></div>
<?php include("inc/footer.php");?>

<?php
}
?>
<?php
	session_start();
	$current_member_id = $_SESSION["contact"]["customer_name"];
	$infor_account_edit = $dbf->getInfoColum("member",$current_member_id);
	$show_price = $infor_account_edit["show_price"];
	
	if(!isset($_SESSION["contact"]) || empty($_SESSION["contact"]))
	{
		echo "<script>window.location.href='booking.aspx';</script>";
		Header( "Location: booking.aspx" ); 
	}
	
	if($_SESSION["contact"]["token"] != $_SESSION['token'])
	{
		echo "<script>window.location.href='booking.aspx';</script>";
		Header( "Location: booking.aspx" );
	}
	
	
		
	if(isset($_POST['submit_send']))
	{			
				$addmore_clicked = false;
				if(isset($_POST['addmore']) &&  $_POST['addmore'] == 1) {
					$addmore_clicked = true;
				}
				// do insert database here
				$member_id = $rowgetInfo["id"];
				$company = $rowgetInfo["company_alphabet"];
				$contact_person = $rowgetInfo["company"];
				$payment_method   =  $rowgetInfo["payment_method"];
				if(isset($_SESSION["contact"]["customer_name"]) && ($rowgetInfo["roles_id"]<6) ) {
					$member_id = $_SESSION["contact"]["customer_name"];
					$infor_account_edit = $dbf->getInfoColum("member",$member_id);
					$contact_person   =  $infor_account_edit["company"];
					$company   =  $infor_account_edit["company_alphabet"];
					$payment_method   =  $infor_account_edit["payment_method"];
				}
				$no_patient = count($_SESSION["contact"]["name"]);
				$total = $_POST['total'];
				$quantity = $_POST['quantity'];
				$subtotal = $_POST['subtotal'];
				$tax = $_POST['subvat'];
				$basic_price = $_POST['basic_price'];
				$order_number = $_SESSION["contact"]["order_number"];
				$order_date = time();
				$dateupdated = time();
				$status = 0;
				$comment = '';
				
				$array_orders = array("member_id"=>$member_id,"company"=>$company,"contact_person"=>$contact_person,"no_patient"=>$no_patient,"total"=>$total,"quantity"=>$quantity,"subtotal"=>$subtotal,"tax"=>$tax,"basic_price"=>$basic_price,"order_number"=>$order_number,"order_date"=>$order_date,"dateupdated"=>$dateupdated,"status"=>$status,"payment_method"=>$payment_method,"comment"=>$comment);									
				$orders_arr = $dbf->insertTable_2("orders", $array_orders);
				if(is_array($orders_arr)) { // if inserted OK
					$id = $orders_arr[2];
					$nearest_date = array();
					$detail_txt = '';
					$count = 1;
					$patient_list = '';
					$production_list = '';
					foreach($_SESSION["contact"]["name"] as $key=>$value) {
						$gender = $_SESSION["contact"]["sex"][$key][0];
						$sex = strtolower($gender);
						$age = $_SESSION["contact"]["age"][$key];
						
						$production = $_SESSION["contact"]["work_tool"][$key][0];
						if($production=='cad')
						{
							$production = $_SESSION["contact"]["cad_select"][$key][0];
						}
						
						$shade = $_SESSION["contact"]["shade"][$key][0];
						$position = serialize($_SESSION["contact"]["check"][$key]);
						$sub_quantity = $_SESSION["contact"]["quantity"][$key];
						$total_fee = $_SESSION["contact"]["subtotal"][$key];
						$price_novat = $_SESSION["contact"]["price_novat"][$key];
						$singlevat = $_SESSION["contact"]["singlevat"][$key];
						$delivery_time = $_SESSION["contact"]["delivery_time"][$key][0];
						$patient_list .= $value . ',';
						$production_list .= $production . ',';
						if($delivery_time=='urgent' || $delivery_time=='same') {
							$desired_date = date("Y/m/d");
						} else {
							$desired_date = $_SESSION["contact"]["desireddate"][$key];
						}
						$nearest_date[] = strtotime($desired_date);
						$remarks = $_SESSION["contact"]["note"][$key];
						$process_file = $_SESSION["contact"]["stlfile"][$key];
						$array_detail = array("order_id"=>$id,"patient_name"=>$value,"gender"=>$gender,"age"=>$age,"production"=>$production,"shade"=>$shade,"position"=>$position,"detail_quantity"=>$sub_quantity,"detail_total_fee"=>$total_fee,"price_novat"=>$price_novat,"single_vat"=>$singlevat,"desired_date"=>$desired_date,"delivery_time"=>$delivery_time,"remarks"=>$remarks,"process_file"=>$process_file);
						$dbf->insertTable_2("order_detail", $array_detail);
						
						$is_patient_exist = $dbf->getDynamic("member", "hovaten='$value' AND gender='$sex' AND age=$age AND parentid=$member_id", "");
						if ($dbf->totalRows($is_patient_exist) <= 0) {
							$ma_id = $dbf->general_ma_id();
							$my_passwords = $utl->randomPassword(10,1,"lower_case,upper_case,numbers,special_symbols");
							$User_Password = $my_passwords[0];
							$array_patient = array("ma_id" => $ma_id,"roles_id" => 16,"hovaten" => $value,"parentid" => $member_id,"tendangnhap" => $ma_id,"gender" => $sex,"age" => $age,"password" => md5($User_Password),"password2" => md5($User_Password),"password3" => md5($User_Password),"datecreated"=>time(),"dateupdated"=>time(),"member_re"=>1,"status"=>0,"active_register"=>0);
														
							 $affect = $dbf->insertTable_2("member", $array_patient);
						}
												
						$detail_txt .= '<p style="text-align:left">'.$count.' - 制作物: '.$product_item[$production].' ／ VITAシェードガイド: '.$shade.' ／ 制作部位: '.implode(',',unserialize($position)).' ／ 本数: '.$sub_quantity.' 本</p>';
						$count ++;
						
					}
					$array_col["nearest_date"] = min($nearest_date);
					$array_col["shipping_date"] = min($nearest_date);
					$array_col["patient_list"] = $patient_list;
					$array_col["production_list"] = $production_list;
					$dbf->updateTable("orders", $array_col, "id='" . $id . "'");
				}

				unset($_POST);
				unset($_SESSION["contact"]);
				
				$token             = md5(uniqid(rand(), TRUE));
			    $_SESSION['token'] = $token;
				
				if($addmore_clicked) {
					echo "<script>window.location.href='booking.aspx';</script>";
					Header( "Location: booking.aspx/" ); 
					exit;
				} else {
					echo "<script>window.location.href='booking-complete.aspx/?item=".$no_patient."';</script>";
					Header( "Location: booking-complete.aspx/?item=" .$no_patient ); 
					exit;
				}
		

	}
	

$quantity = 0;
$subtotal = 0;
$subvat = 0;
$urgent_subtotal = 0;
$urgent_subvat = 0;
$total = 0;
$urgent_total = 0;
$get_price = $_SESSION["contact"]["get_price"];
$get_vat = $_SESSION["contact"]["get_vat"];
$urgent_get_price = $_SESSION["contact"]["urgent_get_price"];

if($infor_account_edit["price_novat"] > 0 || $infor_account_edit["urgent_price_novat"] > 0) {
	$get_price = $infor_account_edit["price_novat"];
	$urgent_get_price = $infor_account_edit["urgent_price_novat"];
}

$price_inc_vat = $get_price + ($get_vat*$get_price/100);
$urgent_price = ($get_price*$urgent_get_price)/100;
$urgent_get_vat = ($get_vat*$urgent_price)/100;
$urgent_price_inc_vat = ($get_price*$urgent_get_price)/100;
?>

<script src="js/custom/booking.js"></script>

<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item">
		<a href="booking.aspx"><?php echo T_('Booking');?></a>
	  </li>
	  <li class="breadcrumb-item active"><?php echo T_('Booking Confirm');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">
		<div class="row">
		  <div class="col-md-12">
		  
			<div class="card">
			  <div class="card-header"><?php echo T_('Order form');?></div>
			  <form id="frmorder" action="" method="post" enctype="multipart/form-data">
			  <div class="card-body">
			  <?php //printf("<pre>%s</pre>",print_r($_SESSION["contact"],true));?>
				<div id="bookingconfirmAccordion" data-children=".item">
					<?php foreach($_SESSION["contact"]["name"] as $key=>$value) {
						$quantity += $_SESSION["contact"]["quantity"][$key];
						$subtotal += $_SESSION["contact"]["quantity"][$key] * $get_price;
						$urgent_subtotal += ($_SESSION["contact"]["urgentsubtotal"][$key]>0) ? $_SESSION["contact"]["quantity"][$key] * $urgent_price : 0;
						$subvat += $_SESSION["contact"]["quantity"][$key] * $get_vat * $get_price / 100;
						$urgent_subvat += ($_SESSION["contact"]["urgentsubtotal"][$key]>0) ? $_SESSION["contact"]["quantity"][$key] * $urgent_get_vat : 0;
						$total += $_SESSION["contact"]["subtotal"][$key];
						$urgent_total += $_SESSION["contact"]["urgentsubtotal"][$key];
						//echo serialize($_SESSION["contact"]["check"][$key]);
					?>
					<div class="item">
					  <a data-toggle="collapse" data-parent="#bookingconfirmAccordion" href="#patientAccordion<?php echo $key;?>" aria-expanded="true" aria-controls="patientAccordion<?php echo $key;?>" class="custom-accordion mb-3"><i class="icon-note"></i> <?php echo T_('Patient name') . ' ' . $_SESSION["contact"]["name"][$key];?><i class="accordion-icon customer-acc  icon-arrow-up"></i></a>
					  
					  <div class="collapse pl-4" id="patientAccordion<?php echo $key;?>" role="tabpanel">
						<!--<div class="form-group row">
							<label class="col-md-2 col-form-label"><?php echo T_('Patient Name');?></label>						  
							<div class="col-md-5">
								<label class="col-form-label"><?php echo $_SESSION["contact"]["name"][$key];?></label>
							</div>
						</div>-->

						
						<div class="form-group row">
							<label class="col-md-2 col-form-label"><?php echo T_('Gender');?><span class="text-danger">*</span></label>						  
							<div class="col-md-5">
								<label class="col-form-label"><?php echo T_($_SESSION["contact"]["sex"][$key][0]);?></label>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-md-2 col-form-label"><?php echo T_('Age');?><span class="text-danger">*</span></label>						  
							<div class="col-md-5">
								<label class="col-form-label"><?php $age = (int) $_SESSION["contact"]["age"][$key];
								echo ($age>1) ? sprintf( T_("%d years old"), $age) : printf( T_("%d year old"), $age);?></label>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-md-2 col-form-label"><?php echo T_('Item');?><span class="text-danger">*</span></label>	
							<div class="col-md-5">						
								<label class="col-form-label">
									<?php 
										if($_SESSION["contact"]["work_tool"][$key][0]=='cad')
											
										{
											echo $product_item[$_SESSION["contact"]["cad_select"][$key][0]];
											
										}else{
											echo $product_item[$_SESSION["contact"]["work_tool"][$key][0]];
										}
									?>
									</label>
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-md-2 col-form-label"><?php echo T_('VITA Shade');?><span class="text-danger">*</span></label>						  
							<div class="col-md-5">
								<label class="col-form-label"><?php echo T_($_SESSION["contact"]["shade"][$key][0]);?></label>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-md-2 col-form-label"><?php echo T_('Position');?><span class="text-danger">*</span></label>						  
							<div class="col-md-5">
								<label class="col-form-label"><?php echo implode(',',$_SESSION["contact"]["check"][$key]);?></label>
							   <!--<div class="input-group">
								<div class="col-md-12 col-form-label row">
									<div class="col-sm-6">
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxrt8" type="checkbox" value="18" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'18')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxrt8">18</label>
										</div>
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxrt7" type="checkbox"  value="17"  name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'17')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxrt7">17</label>
										</div>
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxrt6" type="checkbox"  value="16" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'16')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxrt6">16</label>
										</div>
									
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxrt5" type="checkbox"  value="15" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'15')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxrt5">15</label>
										</div>
									
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxrt4" type="checkbox"  value="14" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'14')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxrt4">14</label>
										</div>
									
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxrt3" type="checkbox"  value="13" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'13')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxrt3" >13</label>
										</div>
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxrt2" type="checkbox"  value="12" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'12')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxrt2"  >12</label>
										</div>
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxrt1" type="checkbox"  value="11" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'11')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxrt1"  >11</label>
										</div>
									</div>
									
									<div class="col-sm-6">
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxlt8" type="checkbox" value="21" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'21')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxlt8">21</label>
										</div>
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxlt7" type="checkbox"  value="22"  name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'22')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxlt7">22</label>
										</div>
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxlt6" type="checkbox"  value="23" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'23')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxlt6">23</label>
										</div>
									
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxlt5" type="checkbox"  value="24" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'24')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxlt5">24</label>
										</div>
									
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxlt4" type="checkbox"  value="25" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'25')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxlt4">25</label>
										</div>
									
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxlt3" type="checkbox"  value="26" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'26')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxlt3">26</label>
										</div>
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxlt2" type="checkbox"  value="27" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'27')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxlt2">27</label>
										</div>
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxlt1" type="checkbox"  value="28" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'28')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxlt1">28</label>
										</div>
									</div>
								
									
								</div>					
							</div>
							<div class="col-md-11  border-shade">
								<div class=" border-shade-top" ></div>
							</div>
							 
							<div class="col-md-12 col-form-label row">
								<div class="col-sm-6">
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxrb8" type="checkbox" value="48" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'48')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxrb8">48</label>
										</div>
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxrb7" type="checkbox"  value="47"  name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'47')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxrb7">47</label>
										</div>
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxrb6" type="checkbox"  value="46" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'46')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxrb6">46</label>
										</div>
									
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxrb5" type="checkbox"  value="45" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'45')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxrb5">45</label>
										</div>
									
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxrb4" type="checkbox"  value="44" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'44')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxrb4">44</label>
										</div>
									
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxrb3" type="checkbox"  value="43" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'43')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxrb3">43</label>
										</div>
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxrb2" type="checkbox"  value="42" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'42')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxrb2">42</label>
										</div>
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxrb1" type="checkbox"  value="41" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'41')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxrb1">41</label>
										</div>
								</div>
									
								<div class="col-sm-6">
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxlb8" type="checkbox" value="31" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'31')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxlb8">31</label>
										</div>
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxlb7" type="checkbox"  value="32"  name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'32')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxlb7">32</label>
										</div>
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxlb6" type="checkbox"  value="33" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'33')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxlb6">33</label>
										</div>
									
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxlb5" type="checkbox"  value="34" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'34')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxlb5">34</label>
										</div>
									
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxlb4" type="checkbox"  value="35" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'35')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxlb4">35</label>
										</div>
									
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxlb3" type="checkbox"  value="36" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'36')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxlb3">36</label>
										</div>
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxlb2" type="checkbox"  value="37" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'37')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxlb2">37</label>
										</div>
										<div class="form-check form-check-inline mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $key;?>-checkboxlb1" type="checkbox"  value="38" name="check[<?php echo $key;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$key],'38')) ? 'checked' : '';?> disabled>
											<label class="form-check-label" for="inline-<?php echo $key;?>-checkboxlb1">38</label>
										</div>
								</div>

							</div>-->
							
							</div>
						</div> <!-- form-group -->	
						<?php if($show_price=='yes') { ?>
						<div class="form-group row">
							<label class="col-md-2 col-form-label"><?php echo T_('Quantity');?></label>						  
							<div class="col-md-5">
								<label class="col-form-label"><?php $sub_quantity = (int) $_SESSION["contact"]["quantity"][$key];
								echo ($sub_quantity>1) ? sprintf( T_("%d teeths"), $sub_quantity) : sprintf( T_("%d tooth"), $sub_quantity);?><label class="ml-5"><?php echo sprintf( T_('Amount per teeth ￥%s (tax included)'),$price_inc_vat);?></label></label>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-md-2 col-form-label"><?php echo T_('Total');?></label>
							<div class="col-md-5">						
								<label class="col-form-label"><?php echo sprintf( T_('%s JPY'),number_format($_SESSION["contact"]["subtotal"][$key] + $_SESSION["contact"]["urgentsubtotal"][$key],0));?>
								<label class="ml-5"><?php echo T_('(tax included)');?></label></label>
							</div>
						</div>
						<?php } ?>
						<div class="form-group row">
							<label class="col-md-2 col-form-label"><?php echo T_('Desired delivery date');?><span class="text-danger">*</span></label>						  
							<div class="col-md-5">
								<label class="col-form-label"><?php 
								$dt_arr = array('normal'=>'','urgent'=>T_('Urgent'),'sameday'=>'');
								$delivery_time = $_SESSION["contact"]["delivery_time"][$key][0]; echo $dt_arr[$delivery_time];?></label>
								<?php if(isset($_SESSION['language']) && $_SESSION['language'] == 'ja_JP') { 
								$session_desireddate = explode('/',$_SESSION["contact"]["desireddate"][$key]);
								$desireddate_y = $session_desireddate[0];
								$desireddate_m = $session_desireddate[1];
								$desireddate_d = $session_desireddate[2];
								?>
								<label class="col-form-label"><?php echo ($delivery_time=='urgent' || $delivery_time=='same') ? date("Y年m月d日") : $desireddate_y.'年'.$desireddate_m.'月'.$desireddate_d.'日';?></label>
								<?php } else { ?>
								<label class="col-form-label"><?php echo ($delivery_time=='urgent' || $delivery_time=='same') ? date("Y/m/d") : $_SESSION["contact"]["desireddate"][$key];?></label>
								<?php } ?>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-md-2 col-form-label"><?php echo T_('Remarks');?></label>						  
							<div class="col-md-5">
								<label class="col-form-label"><?php echo $_SESSION["contact"]["note"][$key];?></label>
							</div>
						</div>

						<div class="form-group row">
							<label class="col-md-2 col-form-label"><?php echo T_('Processing data attachment');?><span class="text-danger">*</span></label>						  
							<div class="col-md-5">
								<label class="col-form-label"><i class="icon-paper-clip"></i> <?php echo urldecode ($_SESSION["contact"]["stlfile"][$key]);?></label>
							</div>
						</div>

					  </div>
					</div>
					<?php } ?>
				  </div>
				  <input type="hidden" name="quantity" value="<?php echo $quantity;?>">
				  <input type="hidden" name="subtotal" value="<?php echo $urgent_total ? $urgent_subtotal + $subtotal : $subtotal;?>">
				  <input type="hidden" name="subvat" value="<?php echo $urgent_total ? $urgent_subvat + $subvat : $subvat;?>">
				  <input type="hidden" name="urgent_subtotal" value="<?php echo $urgent_subtotal;?>">
				  <input type="hidden" name="urgent_subvat" value="<?php echo $urgent_subvat;?>">
				  <input type="hidden" name="total" value="<?php echo $total + $urgent_total;?>">
				  <?php if($show_price=='yes') { ?>
					<hr/>
					<div class="row">
						<div class="col-lg-4 col-sm-4"></div>
						<div class="col-lg-4 col-sm-4">
							<table class="table table-clear">
								<tbody>
									<tr>
										<td class="left">
											<strong><?php echo T_('Total quantity');?></strong>
										</td>
										<td class="right"><?php echo ($quantity>1) ? sprintf( T_("%d teeths"), $quantity) : sprintf( T_("%d tooth"), $quantity);?>
													</td>
									</tr>
									<tr>
										<td class="left">
											<strong><?php echo T_('Subtotal');?></strong>
										</td>
										<td class="right"><?php echo sprintf( T_('%s JPY'),number_format($urgent_total ? $urgent_subtotal + $subtotal : $subtotal,0));?>
													</td>
									</tr>
									<tr>
										<td class="left">
											<strong><?php echo T_('VAT');?></strong>
										</td>
										<td class="right"><?php echo sprintf( T_('%s JPY'),number_format($urgent_total ? $urgent_subvat + $subvat : $subvat,0));?>
													</td>
									</tr>
									<tr>
										<td class="left">
											<strong><?php echo T_('Total');?>	</strong>
										</td>
										<td class="right">
											<strong><span class="money-red"><?php echo sprintf( T_('%s JPY'),number_format($total+$urgent_total,0));?></span>
													</strong>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				  <?php } ?>	
					<!--<div class="form-group row">
						<div class="col-sm-7">
						 		<hr/>	  
						</div>	
					</div>

					<div class="form-group row">
					    <fieldset class="col-sm-2 form-group">
						 					  
						</fieldset>
					
						<fieldset class="col-sm-3 form-group">						 
						  <div class="input-group row">
						    <div class="col-sm-6">
								 <button class="btn btn-warning btn-book" type="button">送信</button>
							</div>
						  </div>
						</fieldset>									
					</div>-->
					<input type="hidden" name="basic_price" value="<?php echo $price_inc_vat;?>">
					</div> <!-- card-body -->	
				   	<div class="card-footer">
						<button class="btn btn-link btn-lg active" type="button" onclick="window.location.href='./booking.aspx';">
							<i class="fa fa-angle-double-left" aria-hidden="true"></i> <?php echo T_('Back to form');?>
						</button>
					  <button id="addmore" class="btn btn-primary" type="button">
						<?php echo T_('Order more'); ?></button>
						<input type="hidden" class="addmore_clicked" name="addmore" value="0">
					  <button id="submitButton" class="btn btn-warning btn-warn" type="submit" value="submit_send" name="submit_send">
						<!-- <i class="fa fa-dot-circle-o"></i> --> <?php echo T_('Send');?></button>
					</div>
			</form>
			</div> <!-- card -->
		  </div> <!-- col-md-12 -->
		  <!-- /.col-->
		  </div>
		  <!-- /.row-->
	  </div>
	</div>
  </main>

<script type="text/javascript">
	$(document).ready(function(){
			$('.item:first-child > .collapse').addClass( "show" );
			$('#addmore').click( function () {
				$('.addmore_clicked').val('1');
				$('#submitButton').trigger('click');
			});				
	})
</script>

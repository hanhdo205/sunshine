<?php
	session_start();
	$current_member_id = isset($_SESSION["contact"]["customer_name"]) ? $_SESSION["contact"]["customer_name"] : $_SESSION['member_id'];
	$infor_account_edit = $dbf->getInfoColum("member",$current_member_id);
	//$order_number = date("ymdHis").substr(microtime(FALSE), 2, 3);
	$order_number = date("ymdHis");
	if(isset($_SESSION["contact"]["order_number"])) {
		$order_number = $_SESSION["contact"]["order_number"];
	}
	$show_price = $infor_account_edit["show_price"];
	if (!isset($_SESSION['token'])) {
		$token             = md5(uniqid(rand(), TRUE));
		$_SESSION['token'] = $token;
	}else
	{
		$token = $_SESSION['token'];
	}
	
	if(isset($_SESSION["contact"]["still_error"]) && $_SESSION["contact"]["still_error"] == true)
		$error = T_('Please complete your previous order');

	if(isset($_POST['submit']))
    {
        unset($_SESSION["contact"]);
		
		$_SESSION["contact"]["order_number"] = $order_number;
		
        foreach($_POST as $key => $value){
			if(!is_array($value)){
			   $_SESSION["contact"][$key] = $dbf->filter($value);	
			}else{
			   $_SESSION["contact"][$key] = $value;
			}
        }

		if($_SESSION["contact"]["token"] == $_SESSION['token'])
		{
		
				 $isvalue = true;
					$error_detail = '';
				 $_SESSION["contact"]["still_error"] == false;
				 if(isset($_FILES['fileinput'])) {
						//printf("<pre>%s</pre>",print_r($_FILES['fileinput'],true));die();
							$uploaddirectory = "upload/orders/";
							foreach($_FILES['fileinput']['name'] as $i => $name)
							{
								if(@$_FILES["fileinput"]["name"][$i] != "") {
									// now $name holds the original file name
									$tmp_name = $_FILES['fileinput']['tmp_name'][$i];
									
									$fileinput = $utl->encode_filename($_FILES["fileinput"]["name"][$i]);
									
									$FileName = $_SESSION["contact"]["order_number"] . '_' . $fileinput;
									
									$error = $_FILES['fileinput']['error'][$i];
									$size = $_FILES['fileinput']['size'][$i];
									$type = $_FILES['fileinput']['type'][$i];
									$imagename	= explode(".",$name);
									$extfile 	= strtolower($imagename[1]);
									$allowedExts1 = array("zip", "rar", "stl");
									$allowed_filetypes = array("application/stl", "application/x-rar-compressed", "application/zip", "application/x-zip", "application/octet-stream", "application/x-zip-compressed");
									if(!in_array($extfile,$allowed_filetypes) && !in_array($extfile, $allowedExts1)) {
										$error = T_("Opps, something went wrong!");
										$isvalue = false;
										$_SESSION["contact"]["fileerror"][$i] = __("File type is invalid");
									}
									if($error === UPLOAD_ERR_OK )
									{
										
											// No errors
											 // You'll probably want something unique for each file.
											move_uploaded_file($tmp_name, $uploaddirectory.$FileName);
											
									}
									$_SESSION["contact"]["stlfile"][$i] = $FileName;
								
								} else {
									if($_SESSION["contact"]["stlfile"][$i] == "") {
										$isvalue = false;
										$_SESSION["contact"]["fileerror"][$i] = __("Please chose a file");
										$_SESSION["contact"]["stlfile"][$i] = '';
									}
								}
								
							}

					}
				foreach($_SESSION["contact"]["name"] as $k => $v) {
					
					if(ctype_alpha(str_replace(' ', '', $v)) === false) {
						$isvalue = false;
						$_SESSION["contact"]["patient_error"][$k] = T_("Please enter alphabet only");
						$_SESSION["contact"]["still_error"] = true;
					}
					
					if($utl->checked($_SESSION["contact"]["delivery_time"][$k],'normal')) {
						if($_SESSION["contact"]["desireddate"][$k]=='') {
							$isvalue = false;
							$_SESSION["contact"]["desireddate_error"][$k] = T_("Please pick a date");
							$_SESSION["contact"]["still_error"] = true;
						}
					}
					if($_SESSION["contact"]["delivery_time_out"][$k]==1) {
						$isvalue = false;
						$error_detail = T_("The option you have just been selected is out of time");
						$_SESSION["contact"]["desireddate_error"][$k] = T_("The option you have just been selected is out of time");
						$_SESSION["contact"]["still_error"] = true;
					}
					if($_SESSION["contact"]["delivery_time_urgent"][$k]==1) {
						$isvalue = false;
						$error_detail = T_("The option you have just been selected is out of serve today");
						$_SESSION["contact"]["desireddate_urgent_error"][$k] = T_("The option you have just been selected is out of serve today");
						$_SESSION["contact"]["still_error"] = true;
					}
					if($_SESSION["contact"]["name"][$k]=="") {
						$isvalue = false;
						$_SESSION["contact"]["patient_error"][$k] = T_("Please enter patient name");
						$_SESSION["contact"]["still_error"] = true;
					}
					
					if($_SESSION["contact"]["age"][$k]=="") {
						$isvalue = false;
						$_SESSION["contact"]["age_error"][$k] = T_("Please enter patient age");
					}
					if(empty($_SESSION["contact"]["check"][$k])) {
						$isvalue = false;
						$_SESSION["contact"]["check_error"][$k] = T_("Please check the position");
						$_SESSION["contact"]["still_error"] = true;
					}
				}
				
				if($_SESSION["contact"]["customer_name"]=="") {
						$isvalue = false;
						$_SESSION["contact"]["customer_error"] = T_("Please select customer name");
						$_SESSION["contact"]["still_error"] = true;
					}
					 
				 if($isvalue)
				 {
					
					 echo "<script>window.location.href='booking-confirm.aspx';</script>";
					 Header( "Location: booking-confirm.aspx" );
					 exit;
					
				 } else {
					$error = ($error_detail !='') ? $error_detail : T_("Opps, something went wrong!");
				 }
	
		}else
			{
				$error.= T_('Invalid token');
			}
		
    }
	
?>
<?php

$arrayMemberCurrent= array();
$arrayMemberCurrent = $dbf->getPatientListArray($rowgetInfo["id"],$rowgetInfo,$arrayMemberCurrent);
$arrayMemberCurrent = $dbf->array_sort_by_column($arrayMemberCurrent,"datecreated");

$js_options[] = '<option value="">'.T_('Choose patient').'</option>';
foreach($arrayMemberCurrent as $row)
	{
		if($row["is_del"]!=1)
		{
			$js_options[] = '<option value="'.$row['id'].'">'.$row['hovaten'].'</option>';
		
		}
	}
	
$arrayCustomer= array();
$arrayCustomer = $dbf->getCustomerListArray($arrayCustomer);
$arrayCustomer = $dbf->array_sort_by_column($arrayCustomer,"datecreated");

?>


<?php $getprice_info = $dbf->getInfoColum("setting",22);
$get_price = $getprice_info['value'];
?>

<?php $getvat_info = $dbf->getInfoColum("setting",23);
$get_vat = $getvat_info['value'];?>

<?php $urgent_getprice_info = $dbf->getInfoColum("setting",25);
$urgent_get_price = $urgent_getprice_info['value'];?>

<?php
if($rowgetInfo["roles_id"] == 15 && ($infor_account_edit["price_novat"] > 0 || $infor_account_edit["urgent_price_novat"] > 0)) {
	$get_price = $infor_account_edit["price_novat"];
	$urgent_get_price = $infor_account_edit["urgent_price_novat"];
}
?>

<?php $price_inc_vat = $get_price + ($get_vat*$get_price/100);?>

<?php $urgent_getvat_info = $dbf->getInfoColum("setting",26);
$urgent_get_vat = $urgent_getvat_info['value'];?>

<?php //$urgent_price_inc_vat = $urgent_get_price + ($urgent_get_vat*$urgent_get_price/100);?>

<?php $urgent_price_inc_vat = ($price_inc_vat*$urgent_get_price)/100;?>

<script src="vendors/moment/js/moment.min.js"></script>
<script src="js/custom/moment-timezone-with-data.min.js"></script>

<link href="vendors/select2/css/select2.min.css" rel="stylesheet" />
<script src="vendors/select2/js/select2.min.js"></script>

<link rel="stylesheet" href="js/jconfirm/jquery-confirm.css">
<script src="js/jconfirm/jquery-confirm.js"></script>

<link rel="stylesheet" href="css/custom/jquery-ui.css">
<script src="js/custom/jquery-ui.js"></script>
<?php if(isset($_SESSION['language']) && $_SESSION['language'] != 'en_US') { ?>
<script src="js/custom/datepicker-<?php echo $datepicker_lang[$_SESSION['language']];?>.js"></script>
<?php } ?>

<script type="text/javascript">
        var translate = {
			patient_name:"<?php echo T_('Patient Name');?>",
			gender:"<?php echo T_('Gender');?>",
			invalid:"<?php echo T_('Please fill out this field.');?>",
			lastname:"<?php echo T_('Last name');?>",
			firstname:"<?php echo T_('First name');?>",
			male:"<?php echo T_('Male');?>",
			female:"<?php echo T_('Female');?>",
			common:"<?php echo T_('Common');?>",
			urgent:"<?php echo T_('Expedited Shipping(until 19:00 jst)');?>",
			age:"<?php echo T_('Age');?>",
			choose_file:"<?php echo T_('Choose file');?>",
			no_file:"<?php echo T_('No file chosen');?>",
			desired_date:"<?php echo T_('Desired delivery date');?>",
			production:"<?php echo T_('Item');?>",
			zirconia:"<?php echo T_('Fullzirconia');?>",
			cad_cam:"<?php echo T_('CAD Design(Data Only)');?>",
			model:"<?php echo T_('3D Printer Model');?>",
			please_select_option:"<?php echo T_('Please select an option.');?>",
			please_select:"<?php echo T_('Please select');?>",
			none:"<?php echo T_('None');?>",
			other:"<?php echo T_('Other');?>",
			shade:"<?php echo T_('VITA Shade');?>",
			note_multi:"<?php echo T_('※If you wish multiple, Please write down in the remarks column bottom of this page');?>",
			extra_charge:"<?php echo sprintf(T_('※ Extra charge: %s%% extra charge of the total amount'),$urgent_get_price);?>",
			attachement:"<?php echo T_('Attach data for processing');?>",
			filetype:"<?php echo T_('※Data format .stl / .zip<br>※Please send combined 1 file included \"Working Model\", \"Dental　Antagonist\",\"The model bit between Working Model and Dental Antagonist\"<br>※Maximum upload file size: 20MB');?>",
			remarks:"<?php echo T_('Remarks');?>",
			total:"<?php echo T_('Total');?>",
			quantity:"<?php echo T_('Quantity');?>",
			position:"<?php echo T_('Position');?>",
			jpy:"<?php echo T_('JPY');?>",
			teeth:"<?php echo T_('teeth');?>",
			choose_patient:"<?php echo T_('Choose patient');?>",
			enter_patient:"<?php echo T_('Enter patient name');?>",
			no_patient_found:"<?php echo T_('No patient found');?>",
			add_new:"<?php echo T_('Add new');?>",
			enter_customer:"<?php echo T_('Choose customer');?>",
			out_of_urgent:"<?php echo T_('The option you have just been selected is out of serve today');?>",
			out_of_time:"<?php echo T_('The option you have just been selected is out of time');?>",
			btn_close_alert:"<?php echo T_('Close');?>",
		};
		
		var options = '<?php echo implode('',$js_options);?>';
		<?php //if($rowgetInfo["roles_id"]<6) { ?>
			var allow_tag = true;
		<?php //} elseif($rowgetInfo["roles_id"]==15) { ?>
			//var allow_tag = false;
		<?php //} ?>
		<?php if($show_price=='yes') { ?>
			var show_price = true;
		<?php } else { ?>
			var show_price = false;
		<?php } ?>
		
		var get_price = 0;
		var get_vat = 0;
		var price_inc_vat = 0;
		
		var urgent_get_price = 0;
		var urgent_get_vat = 0;
		var urgent_price_inc_vat = 0;
</script>
<script src="js/custom/booking.js"></script>
<script src="js/custom/jquery.form.js"></script>
<script src="js/custom/upload_progress.js"></script>

<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item active"><?php echo T_('Order');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">
		<div class="row">
		  <div class="col-md-12 mb-5">
		  
			<div class="card">
			  <div class="card-header"><?php echo T_('Order');?></div>
			  <form action="" method="post" id="fbooking" enctype="multipart/form-data" novalidate>
			  <div class="card-body">
			  <?php  /*printf("<pre>%s</pre>",print_r($_SESSION["contact"],true)); */
			  if($error) 
					{
						echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">'.T_($error).'<button class="close hide_error" type="button" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></DIV>';						
					}
				?>
				<input type="hidden" name="patient_count" class="patient_count" value="<?php echo (isset($_SESSION["contact"]["patient_count"]) && $_SESSION["contact"]["patient_count"] > 0) ? $_SESSION["contact"]["patient_count"] : 1;?>">
				<?php if($rowgetInfo["roles_id"]<6) { ?>
				<div class="form-group row customer_row">
					    <label class="col-md-2 col-form-label"><?php echo T_('Customer company');?> <span class="text-danger">*</span></label>
						<?php 
							$customer_error = "";
							if(isset($_SESSION["contact"]["customer_error"])) {
								$customer_error = "has_error";
							} ?>						  
						<div class="col-md-5 <?php echo $customer_error;?>">
							<select id="select_customer" class="form-control select2-single select2 <?php echo $customer_error;?>" name="customer_name" data-number="<?php echo $i;?>" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" onchange="setCustomValidity('')">
								<?php
								$customer_options = array();
								$customer_options[] = '<option value="">'.T_('Choose customer').'</option>';
									foreach($arrayCustomer as $customer)
										{
											if($customer["is_del"]!=1)
											{	
												$company_ja_JP = $customer['company'];
												$company_non_ja_JP = $customer['company_alphabet'];
												$hovaten_ja_JP = $customer['hovaten'];
												$hovaten_non_ja_JP = $customer['hovaten_alphabet'];
												$company = (isset($_SESSION['language']) && $_SESSION['language']=='ja_JP') ? $company_ja_JP : $company_non_ja_JP;
												$hovaten = (isset($_SESSION['language']) && $_SESSION['language']=='ja_JP') ? $hovaten_ja_JP : $hovaten_non_ja_JP;
												$customer_options[] = '<option value="'.$customer['id'].'" '.$utl->selected($customer['id'],$_SESSION["contact"]["customer_name"]).'>'.$company . ' - ' . $hovaten .'</option>';
											
											}
										}

								foreach($customer_options as $c_option) {
									echo $c_option;
								}?>
							</select>
							<small class="<?php echo $customer_error;?> field_message text-danger"><?php echo T_($_SESSION["contact"]["customer_error"]);?></small>

						</div>
					</div>
				<?php } else { ?>
					<input type="hidden" name="customer_name" value="<?php echo $rowgetInfo["id"];?>">
				<?php } ?>
				<?php 
				$count = (isset($_SESSION["contact"]["patient_count"])  && $_SESSION["contact"]["patient_count"] > 0 ) ? $_SESSION["contact"]["patient_count"] : 1;
				/* 
					for($i=0;$i<$count;$i++) {  */ 
					$i=0;
				?>
				<?php if($i>0) { ?>
					<fieldset class="new_patient"><hr/>
						<a href="javascript:void(0);" class="remove"><i class="icon-close" aria-hidden="true"></i></a>
				<?php } else { ?>
					<fieldset>
				<?php } ?>
					<div class="form-group row patient_row">
					    <label class="col-md-2 col-form-label"><?php echo T_('Patient Name');?><span class="text-danger">*</span></label>
						<?php 
							$patient_error = "";
							if(isset($_SESSION["contact"]["patient_error"][$i])) {
								$patient_error = "has_error";
							} ?>						  
						<div class="col-md-5 <?php echo $patient_error;?>">
							<select id="select_<?php echo $i;?>" class="form-control select2-single patient_select select2 <?php echo $patient_error;?>" name="patient_name[<?php echo $i;?>][]" data-number="<?php echo $i;?>" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" onchange="setCustomValidity('')">
								<?php
								$options = array();
								$options[] = '<option value="">'.T_('Choose patient').'</option>';
								foreach($arrayMemberCurrent as $row)
									{
										if($row["is_del"]!=1)
										{
											$options[] = '<option value="'.$row['id'].'" '.$utl->selected($row['id'],$_SESSION["contact"]["patient_name"][$i][0]).'>'.$row['hovaten'].' ('.sprintf(T_('%s, %d'),$gender[$row['gender']],(int) $row['age']).')</option>';
										
										}
									}

								foreach($options as $option) {
									echo $option;
								}?>
							</select>
							<small class="<?php echo $patient_error;?> field_message text-danger"><?php echo T_($_SESSION["contact"]["patient_error"][$i]);?></small>
							<input class="form-control patient_name_<?php echo $i;?>" name="name[]" type="hidden" value="<?php echo $_SESSION["contact"]["name"][$i];?>" />
						</div>
						<div class="col-sm-4 col-form-label"><label><?php echo T_('Example: Taro Yamada');?></label></div>
					</div>
					<div class="form-group row">
						<label class="col-md-2 col-form-label"><?php echo T_('Gender');?><span class="text-danger">*</span></label>
						<div class="col-md-5 col-form-label">
							<div class="form-check form-check-inline mr-1">
							<input class="form-check-input" id="male<?php echo $i;?>" type="radio" value="Male" name="sex[<?php echo $i;?>][]" <?php 
							$is_male = $utl->checked($_SESSION["contact"]["sex"][$i],'Male');
							$is_female = $utl->checked($_SESSION["contact"]["sex"][$i],'Female');
							echo ($is_male) ? 'checked' : ($is_female ? '' : 'checked');?>>
							<label class="form-check-label" for="male<?php echo $i;?>"><?php echo T_('Male');?></label>
							</div>
							<div class="form-check form-check-inline mr-1">
							<input class="form-check-input" id="female<?php echo $i;?>" type="radio" value="Female" name="sex[<?php echo $i;?>][]" <?php echo ($is_female) ? 'checked' : '';?>>
							<label class="form-check-label" for="female<?php echo $i;?>"><?php echo T_('Female');?></label>
							</div>
							
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-md-2 col-form-label"><?php echo T_('Age');?><span class="text-danger">*</span></label>	
						<?php 
							$age_error = "";
							if(isset($_SESSION["contact"]["age_error"][$i])) {
								$age_error = "has_error";
							} ?>							
						<div class="col-md-5 <?php echo $age_error;?>">
							<input type="text" class="form-control patient_age_<?php echo $i;?> <?php echo $age_error;?>" name="age[]"  value="<?php echo $_SESSION["contact"]["age"][$i];?>" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')">
							<small class="<?php echo $age_error;?> field_message text-danger"><?php echo T_($_SESSION["contact"]["age_error"][$i]);?></small>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-md-2 col-form-label"><?php echo T_('Item');?><span class="text-danger">*</span></label>
						<div class="col-md-10 col-form-label">
							<div class="form-check form-check-inline cus mr-1">
								<input class="form-check-input work_tool" id="CAM<?php echo $i;?>" type="radio" value="cad" name="work_tool[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["work_tool"][$i],'cad')) ? 'checked' : 'checked';?>>
								<label class="form-check-label customer-clss" for="CAM<?php echo $i;?>">
								        <?php echo T_('CAD Design(Data Only)');?>
								      
								    <span class="form-check-label work_tool_select"> 
								   		 <select class="form-control" id="select1"       name="cad_select[<?php echo $i;?>][]">
										<option value="crown"><?php echo T_('Crown (single crown)');?></option>
										<option value="bridge"><?php echo T_('Bridge');?></option>
										<option value="clasp"><?php echo T_('Clasp');?></option>
										<option value="denture"><?php echo T_('Denture Frame');?></option>
						   		   		 </select>
						   		 </span>
								</label>
							</div>
							<div class="form-check form-check-inline mr-1">
								<input class="form-check-input work_tool" id="zirconia<?php echo $i;?>" type="radio" value="zirconia" name="work_tool[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["work_tool"][$i],'zirconia')) ? 'checked' : '';?>>
								<label class="form-check-label" for="zirconia<?php echo $i;?>"><?php echo T_('Fullzirconia');?></label>
							</div>
							<div class="form-check form-check-inline mr-1">
								<input class="form-check-input work_tool" id="demo<?php echo $i;?>" type="radio" value="3d" name="work_tool[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["work_tool"][$i],'3d')) ? 'checked' : '';?>>
								<label class="form-check-label" for="demo<?php echo $i;?>"><?php echo T_('3D Printer Model');?></label>
							</div> 
							<div class="form-check form-check-inline mr-1">
								<input class="form-check-input work_tool" id="frame<?php echo $i;?>" type="radio" value="frame" name="work_tool[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["work_tool"][$i],'3d')) ? 'checked' : '';?>>
								<label class="form-check-label" for="frame<?php echo $i;?>"><?php echo T_('Frame Design');?></label>
							</div> 
						</div>
					</div>
					<br>
					<div class="form-group row">
						<label class="col-md-2 col-form-label"><?php echo T_('VITA Shade');?><span class="text-danger">*</span></label>
						<?php 
							$shade_error = "";
							if(isset($_SESSION["contact"]["shade_error"][$i])) {
								$shade_error = "has_error";
							} ?>
						<div class="col-md-5 col-form-label">
							<select class="form-control <?php echo $shade_error;?>" name="shade[<?php echo $i;?>][]" required oninvalid="this.setCustomValidity('<?php echo T_('Please select an option.');?>')" oninput="setCustomValidity('')">
								<option value="None" <?php echo $utl->selected('None',$_SESSION["contact"]["shade"][$i][0]);?>><?php echo T_('None');?></option>
								<option value="A1" <?php echo $utl->selected('A1',$_SESSION["contact"]["shade"][$i][0]);?>><?php echo T_('A1');?></option>
								<option value="A2" <?php echo $utl->selected('A2',$_SESSION["contact"]["shade"][$i][0]);?>><?php echo T_('A2');?></option>
								<option value="A3" <?php echo $utl->selected('A3',$_SESSION["contact"]["shade"][$i][0]);?>><?php echo T_('A3');?></option>
								<option value="A3.5" <?php echo $utl->selected('A3.5',$_SESSION["contact"]["shade"][$i][0]);?>><?php echo T_('A3.5');?></option>
								<option value="A4" <?php echo $utl->selected('A4',$_SESSION["contact"]["shade"][$i][0]);?>><?php echo T_('A4');?></option>
								<option value="B1" <?php echo $utl->selected('B1',$_SESSION["contact"]["shade"][$i][0]);?>><?php echo T_('B1');?></option>
								<option value="B2" <?php echo $utl->selected('B2',$_SESSION["contact"]["shade"][$i][0]);?>><?php echo T_('B2');?></option>
								<option value="B3" <?php echo $utl->selected('B3',$_SESSION["contact"]["shade"][$i][0]);?>><?php echo T_('B3');?></option>
								<option value="B4" <?php echo $utl->selected('B4',$_SESSION["contact"]["shade"][$i][0]);?>><?php echo T_('B4');?></option>
								<option value="C1" <?php echo $utl->selected('C1',$_SESSION["contact"]["shade"][$i][0]);?>><?php echo T_('C1');?></option>
								<option value="C2" <?php echo $utl->selected('C2',$_SESSION["contact"]["shade"][$i][0]);?>><?php echo T_('C2');?></option>
								<option value="C3" <?php echo $utl->selected('C3',$_SESSION["contact"]["shade"][$i][0]);?>><?php echo T_('C3');?></option>
								<option value="C4" <?php echo $utl->selected('C4',$_SESSION["contact"]["shade"][$i][0]);?>><?php echo T_('C4');?></option>
								<option value="D2" <?php echo $utl->selected('D2',$_SESSION["contact"]["shade"][$i][0]);?>><?php echo T_('D2');?></option>
								<option value="D3" <?php echo $utl->selected('D3',$_SESSION["contact"]["shade"][$i][0]);?>><?php echo T_('D3');?></option>
								<option value="D4" <?php echo $utl->selected('D4',$_SESSION["contact"]["shade"][$i][0]);?>><?php echo T_('D4');?></option>
								<option value="Other" <?php echo $utl->selected('Other',$_SESSION["contact"]["shade"][$i][0]);?>><?php echo T_('Other');?></option>
							 </select>
							<small class="<?php echo $shade_error;?> field_message text-danger"><?php echo T_($_SESSION["contact"]["shade_error"][$i]);?></small>
						</div>
					</div>
					<div class="row custom-txt">
						<div class="col-sm-2"></div>
						<div class="col-sm-10">
							<div class="help-block"> <?php echo T_('※If you wish multiple, Please write down in the remarks column bottom of this page');?></div>
						</div>
					</div>
					<div class="form-group row">
							<label class="col-md-2 col-form-label"><?php echo T_('Position');?> <span class="text-danger">*</span></label>	<?php 
							$check_error = "";
							if(isset($_SESSION["contact"]["check_error"][$i])) {
								$check_error = "has_error";
							} ?>					  
						<div class="col-md-10 position_check">
							<div class="input-group">
								<div class="col-form-label row top_position <?php echo $check_error;?>">
									<div class="col-sm-6 col-md-6 col-lg-6">
										<div class="form-check form-check-inline cus-form-check mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxrt8" type="checkbox" value="18" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'18')) ? 'checked' : '';?>>
											<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxrt8">18</label>
										</div>
										<div class="form-check form-check-inline cus-form-check mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxrt7" type="checkbox"  value="17"  name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'17')) ? 'checked' : '';?>>
											<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxrt7">17</label>
										</div>
										<div class="form-check form-check-inline cus-form-check mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxrt6" type="checkbox"  value="16" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'16')) ? 'checked' : '';?>>
											<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxrt6">16</label>
										</div>
									
										<div class="form-check form-check-inline cus-form-check mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxrt5" type="checkbox"  value="15" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'15')) ? 'checked' : '';?>>
											<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxrt5">15</label>
										</div>
									
										<div class="form-check form-check-inline cus-form-check mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxrt4" type="checkbox"  value="14" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'14')) ? 'checked' : '';?>>
											<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxrt4">14</label>
										</div>
									
										<div class="form-check form-check-inline cus-form-check mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxrt3" type="checkbox"  value="13" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'13')) ? 'checked' : '';?>>
											<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxrt3">13</label>
										</div>
										<div class="form-check form-check-inline cus-form-check mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxrt2" type="checkbox"  value="12" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'12')) ? 'checked' : '';?>>
											<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxrt2">12</label>
										</div>
										<div class="form-check form-check-inline cus-form-check mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxrt1" type="checkbox"  value="11" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'11')) ? 'checked' : '';?>>
											<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxrt1">11</label>
										</div>
									</div>
									
									<div class="col-sm-6 col-md-6 col-lg-6">
										<div class="form-check form-check-inline cus-form-check mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxlt8" type="checkbox" value="21" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'21')) ? 'checked' : '';?>>
											<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxlt8">21</label>
										</div>
										<div class="form-check form-check-inline cus-form-check mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxlt7" type="checkbox"  value="22"  name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'22')) ? 'checked' : '';?>>
											<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxlt7">22</label>
										</div>
										<div class="form-check form-check-inline cus-form-check mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxlt6" type="checkbox"  value="23" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'23')) ? 'checked' : '';?>>
											<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxlt6">23</label>
										</div>
									
										<div class="form-check form-check-inline cus-form-check mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxlt5" type="checkbox"  value="24" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'24')) ? 'checked' : '';?>>
											<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxlt5">24</label>
										</div>
									
										<div class="form-check form-check-inline cus-form-check mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxlt4" type="checkbox"  value="25" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'25')) ? 'checked' : '';?>>
											<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxlt4">25</label>
										</div>
									
										<div class="form-check form-check-inline cus-form-check mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxlt3" type="checkbox"  value="26" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'26')) ? 'checked' : '';?>>
											<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxlt3">26</label>
										</div>
										<div class="form-check form-check-inline cus-form-check mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxlt2" type="checkbox"  value="27" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'27')) ? 'checked' : '';?>>
											<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxlt2">27</label>
										</div>
										<div class="form-check form-check-inline cus-form-check mr-1">
											<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxlt1" type="checkbox"  value="28" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'28')) ? 'checked' : '';?>>
											<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxlt1">28</label>
										</div>
									</div>	
									<div class="col-sm-12 col-md-12 col-lg-12  border-shade">
										<div class=" border-shade-top" ></div>
									</div>				
								</div>
								
							 
								<div class="col-form-label row bottom_position <?php echo $check_error;?>">
									<div class=" col-sm-6 col-md-6 col-lg-6">
											<div class="form-check form-check-inline cus-form-check mr-1">
												<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxrb8" type="checkbox" value="48" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'48')) ? 'checked' : '';?>>
												<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxrb8">48</label>
											</div>
											<div class="form-check form-check-inline cus-form-check mr-1">
												<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxrb7" type="checkbox"  value="47"  name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'47')) ? 'checked' : '';?>>
												<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxrb7">47</label>
											</div>
											<div class="form-check form-check-inline cus-form-check mr-1">
												<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxrb6" type="checkbox"  value="46" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'46')) ? 'checked' : '';?>>
												<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxrb6">46</label>
											</div>
										
											<div class="form-check form-check-inline cus-form-check mr-1">
												<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxrb5" type="checkbox"  value="45" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'45')) ? 'checked' : '';?>>
												<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxrb5">45</label>
											</div>
										
											<div class="form-check form-check-inline cus-form-check mr-1">
												<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxrb4" type="checkbox"  value="44" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'44')) ? 'checked' : '';?>>
												<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxrb4">44</label>
											</div>
										
											<div class="form-check form-check-inline cus-form-check mr-1">
												<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxrb3" type="checkbox"  value="43" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'43')) ? 'checked' : '';?>>
												<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxrb3">43</label>
											</div>
											<div class="form-check form-check-inline cus-form-check mr-1">
												<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxrb2" type="checkbox"  value="42" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'42')) ? 'checked' : '';?>>
												<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxrb2">42</label>
											</div>
											<div class="form-check form-check-inline cus-form-check mr-1">
												<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxrb1" type="checkbox"  value="41" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'41')) ? 'checked' : '';?>>
												<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxrb1">41</label>
											</div>
									</div>
										
									<div class="col-sm-6 col-md-6 col-lg-6">
											<div class="form-check form-check-inline cus-form-check mr-1">
												<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxlb8" type="checkbox" value="31" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'31')) ? 'checked' : '';?>>
												<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxlb8">31</label>
											</div>
											<div class="form-check form-check-inline cus-form-check mr-1">
												<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxlb7" type="checkbox"  value="32"  name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'32')) ? 'checked' : '';?>>
												<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxlb7">32</label>
											</div>
											<div class="form-check form-check-inline cus-form-check mr-1">
												<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxlb6" type="checkbox"  value="33" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'33')) ? 'checked' : '';?>>
												<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxlb6">33</label>
											</div>
										
											<div class="form-check form-check-inline cus-form-check mr-1">
												<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxlb5" type="checkbox"  value="34" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'34')) ? 'checked' : '';?>>
												<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxlb5">34</label>
											</div>
										
											<div class="form-check form-check-inline cus-form-check mr-1">
												<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxlb4" type="checkbox"  value="35" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'35')) ? 'checked' : '';?>>
												<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxlb4">35</label>
											</div>
										
											<div class="form-check form-check-inline cus-form-check mr-1">
												<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxlb3" type="checkbox"  value="36" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'36')) ? 'checked' : '';?>>
												<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxlb3">36</label>
											</div>
											<div class="form-check form-check-inline cus-form-check mr-1">
												<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxlb2" type="checkbox"  value="37" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'37')) ? 'checked' : '';?>>
												<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxlb2">37</label>
											</div>
											<div class="form-check form-check-inline cus-form-check mr-1">
												<input class="form-check-input check_position_1" id="inline-<?php echo $i;?>-checkboxlb1" type="checkbox"  value="38" name="check[<?php echo $i;?>][]" <?php echo ($utl->checked($_SESSION["contact"]["check"][$i],'38')) ? 'checked' : '';?>>
												<label class="form-check-label" for="inline-<?php echo $i;?>-checkboxlb1">38</label>
											</div>
									</div>
									<div class="col-sm-12 col-md-12 col-lg-12"> </div>
								</div>
								<div class="col-sm-12 col-md-12 col-lg-12  <?php echo $check_error;?>"> </div>
								<small class="<?php echo $check_error;?> field_message text-danger"><?php echo T_($_SESSION["contact"]["check_error"][$i]);?></small>
							</div>
						</div>
						
					</div> <!-- form-group -->
					
					<input type="hidden" class="count_check_<?php echo $i;?>" name="quantity[]" value="<?php echo $_SESSION["contact"]["quantity"][$i];?>">
					<input type="hidden" class="input_subtotal_<?php echo $i;?>" name="subtotal[]" value="<?php echo $_SESSION["contact"]["subtotal"][$i];?>">
					<input type="hidden" class="input_vat_<?php echo $i;?>" name="singlevat[]" value="<?php echo $_SESSION["contact"]["singlevat"][$i];?>">
					<input type="hidden" class="input_price_novat_<?php echo $i;?>" name="price_novat[]" value="<?php echo $_SESSION["contact"]["price_novat"][$i];?>">
					
					<input type="hidden" class="urgent_subtotal_<?php echo $i;?>" name="urgentsubtotal[]" value="<?php echo $_SESSION["contact"]["urgentsubtotal"][$i];?>">

					<input type="hidden" class="urgent_price_<?php echo $i;?>" name="urgent_price[]" value="<?php echo $_SESSION["contact"]["urgent_price"][$i];?>">
					
					<?php if($show_price=='yes') { ?>

					<div class="form-group row">
					    <label class="col-md-2 col-form-label"><?php echo T_('Quantity');?></label>						  
						<div class="col-md-5">
							<label class="quantity_<?php echo $i;?>">
							<?php $sub_quantity = ($_SESSION["contact"]["quantity"][$i]) ? (int) $_SESSION["contact"]["quantity"][$i] : (int) 0;
							echo ($sub_quantity>1) ? sprintf( T_("%d teeths"), $sub_quantity) : sprintf( T_("%d tooth"), $sub_quantity);?>
							</label>
							
						</div>
					</div>

					<div class="form-group row">
					    <label class="col-md-2 col-form-label"><?php echo T_('Total');?></label>						  
						<div class="col-md-5">
							<label class="subtotal_<?php echo $i;?>">
							<?php echo ($_SESSION["contact"]["subtotal"][$i]) ? number_format($_SESSION["contact"]["subtotal"][$i],0) : 0;?>
							</label> <?php echo T_('JPY');?>
							
						</div>
					</div>
					
					<?php } ?>

					<div class="form-group row">
					    <label class="col-md-2 col-form-label"><?php echo T_('Desired delivery date');?> <span class="text-danger">*</span></label>
											  
						<div class="col-form-label col-md-6">
							<?php 
								$desireddate_error = "";
									if(isset($_SESSION["contact"]["desireddate_error"][$i])) {
										$desireddate_error = "has_error";
									} 
							?>
							<?php 
								$desireddate_urgent_error = "";
									if(isset($_SESSION["contact"]["desireddate_urgent_error"][$i])) {
										$desireddate_urgent_error = "has_error";
									} 
							?>
							<?php
									$is_normal = $utl->checked($_SESSION["contact"]["delivery_time"][$i],'normal');
									$is_urgent = $utl->checked($_SESSION["contact"]["delivery_time"][$i],'urgent');
									$is_sameday = ($utl->checked($_SESSION["contact"]["delivery_time"][$i],'same')) ? $utl->checked($_SESSION["contact"]["delivery_time"][$i],'same') : 1;
								?>
							<div class="form-check mb-2">
								<input class="form-check-input delivery_time<?php echo $i;?>" id="sameday<?php echo $i;?>" type="radio" value="same" name="delivery_time[<?php echo $i;?>][]" <?php echo ($is_sameday) ? 'checked' : '';?>>
								<label class="form-check-label ml-1 <?php echo $desireddate_error;?>" for="sameday<?php echo $i;?>"><?php echo T_('Standard (Order by 15:00 JST and we will deliver it within today)');?></label>
								<small class="<?php echo $desireddate_error;?> field_message text-danger"><?php echo T_($_SESSION["contact"]["desireddate_error"][$i]);?></small>
							</div>
							<div class="form-check mb-2">
								<input class="form-check-input delivery_time<?php echo $i;?>" id="urgent<?php echo $i;?>" type="radio" value="urgent" name="delivery_time[<?php echo $i;?>][]" <?php echo ($is_urgent) ? 'checked' : '';?>>
								<label class="form-check-label customcheck ml-1 <?php echo $desireddate_urgent_error;?>" for="urgent<?php echo $i;?>"><?php echo T_('Expedited Shipping (If you wish to deliver in today after 15:00 JST. The order accept by 18:00 JST in 3 hours)');?></label>
								<!--<div class="help-block urgent_block customer-help urgent<?php echo $i;?> box<?php echo $i;?>"> <?php echo sprintf(T_('※ Extra charge: %s%% extra charge of the total amount'),$urgent_get_price);?>-->
								<div class="help-block urgent_block customer-help"> <?php echo sprintf(T_('※ Extra charge: %s%% extra charge of the total amount'),$urgent_get_price);?>
								</div>
								<small class="<?php echo $desireddate_urgent_error;?> field_message text-danger"><?php echo T_($_SESSION["contact"]["desireddate_urgent_error"][$i]);?></small>
							</div>
							<div class="form-check mb-2">
								
								<input class="form-check-input delivery_time<?php echo $i;?>" id="normal<?php echo $i;?>" type="radio" value="normal" name="delivery_time[<?php echo $i;?>][]" <?php echo ($is_normal) ? 'checked' : '';?>>
								<label class="form-check-label" for="normal<?php echo $i;?>"><?php echo T_('Scheduled Delivery');?></label>
								
							</div>
							<div class="calendar-wrap"><div id="desireddate<?php echo $i;?>" class="normal<?php echo $i;?> box<?php echo $i;?>"></div></div>
							
							<div class="input-group mt-2 mb-2 normal<?php echo $i;?> box<?php echo $i;?>">
									<span class="input-group-prepend">
									  <span class="input-group-text">
										<i class="fa fa-calendar"></i>
									  </span>
									</span>
									<input id="input_desireddate<?php echo $i;?>" class="form-control date" type="text" name="desireddate[]" value="<?php echo $_SESSION["contact"]["desireddate"][$i];?>" autocomplete="off" />
									
							</div>
							
						</div>
						<!--<div class="col-md-5">
							<div class="input-group">
								<span class="input-group-prepend">
								  <span class="input-group-text">
									<i class="fa fa-calendar"></i>
								  </span>
								</span>
								<input id="desireddate<?php echo $i;?>" class="form-control date" type="text" name="desireddate[]" value="<?php echo $_SESSION["contact"]["desireddate"][$i];?>" autocomplete="off"/>
							</div>
						</div>-->								
					</div>
					<div class="form-group row">
					    <label class="col-md-2 col-form-label"><?php echo T_('Remarks');?></label>						  
						<div class="col-md-5">
							<textarea class="form-control" rows="7" name="note[]"><?php echo $_SESSION["contact"]["note"][$i];?></textarea>
						</div>
					</div>

					<div class="form-group row">
					    <label class="col-md-2 col-form-label"><?php echo T_('Attach data for processing');?> <span class="text-danger">*</span></label>						  
						<div class="col-md-5">
							<?php $stlfile_error = '';
							if(isset($_SESSION["contact"]["stlfile"][$i])) { ?>
								<?php if(isset($_SESSION["contact"]["fileerror"][$i]) && $_SESSION["contact"]["fileerror"][$i] != '0') {
									$stlfile_error = "has_error";
								} 
							} ?>
							
							<span class="input-group div-select-stlinput_<?php echo $i;?>"><input type="text" name="stlfile[<?php echo $i;?>]" class="stl_input_<?php echo $i;?> input full upload form-control <?php echo $stlfile_error;?>" placeholder="<?php echo T_('No file chosen');?>" value="<?php echo (isset($_SESSION["contact"]["stlfile"][$i])) ? $_SESSION["contact"]["stlfile"][$i] : '';?>" autocomplete="off" style="padding: 3px !important;background: #fff;">
								<span class="input-group-append">
									<label for="stlinput_<?php echo $i;?>" class="btn btn-primary"><?php echo T_('Choose file');?></label></span>
								</span>
								<div class='progress_div' id="progress_div">
									<div class='bar' id='bar'></div>
									<div class='percent' id='percent'>0%</div>
								</div>
								<small class="<?php echo $stlfile_error;?> field_message text-danger"><?php echo T_($_SESSION["contact"]["fileerror"][$i]);?></small>
								<span class="help-block"> <?php echo T_('※Data format .stl / .zip<br>※Please send combined 1 file included "Working Model", "Dental　Antagonist","The model bit between Working Model and Dental Antagonist"<br>※Maximum upload file size: 20MB');?></span>
								<input id="stlinput_<?php echo $i;?>" type="file" name="fileinput[]" style="visibility:hidden;">
						</div>
					</div>
					<input type="hidden" class="delivery_time_out" name="delivery_time_out[]">
					<input type="hidden" class="delivery_time_urgent" name="delivery_time_urgent[]">
					</fieldset>
					<script type="text/javascript">
					$(document).ready(function () {
						if(allow_tag==true) {
							$('.patient_select').select2({
								  theme: 'bootstrap',
								  placeholder: '<?php echo T_('Enter patient name');?>',
								  tags: true,
								  createTag: function (params) {
									return {
									  id: params.term,
									  text: params.term,
									  newOption: true
									}
								  },
								  language: {
									   "noResults": function(){
										   return "<?php echo T_('Enter patient name');?>";
									   }
								   },
									escapeMarkup: function (markup) {
										return markup;
									}
								});
						} else {
							$('.patient_select').select2({
							  theme: 'bootstrap',
							  placeholder: '<?php echo T_('Choose patient');?>',
							  language: {
									   "noResults": function(){
										   return "<?php echo T_('No patient found');?>&nbsp;<a href='new-patient.aspx' class='btn btn-sm btn-danger'><?php echo T_('Add new');?></a>";
									   }
								   },
									escapeMarkup: function (markup) {
										return markup;
									}
							});
						}
						$('#select_customer').select2({
							  theme: 'bootstrap',
							  placeholder: '<?php echo T_('Choose customer');?>'
						});
							//var d = new Date();
							var d = moment().tz('Asia/Ho_Chi_Minh');
							$('#desireddate<?php echo $i;?>').datepicker({
								inline: true,
								altField: '#input_desireddate<?php echo $i;?>',
								//changeMonth: true,
								//changeYear: true,
								dateFormat: "yy/mm/dd",
								//minDate: d.getHours() >= 15 ? 1 : 0
								minDate: 1
							});
							$('#input_desireddate<?php echo $i;?>').change(function(){
								$('#desireddate<?php echo $i;?>').datepicker('setDate', $(this).val());
							});
							<?php if(isset($_SESSION["contact"]["desireddate"][$i])) { ?>
								$('#desireddate<?php echo $i;?>').datepicker('setDate', '<?php echo $_SESSION["contact"]["desireddate"][$i];?>');
							<?php } ?>
							/*var targetTimeOffset  = +7*60;
							d.setMinutes(d.getMinutes() - d.getTimezoneOffset() - targetTimeOffset );
							console.log(d.getMinutes() - d.getTimezoneOffset() - targetTimeOffset );
							$('#desireddate<?php echo $i;?>').datepicker('option','minDate', d);*/
							//$('#desireddate<?php echo $i;?>').datepicker().datepicker("setDate", d);
							get_price = parseInt($('.get_price').val());
							get_vat = parseInt($('.get_vat').val());
							price_inc_vat = get_price + (get_vat*get_price/100);
							
							urgent_get_price = parseInt($('.urgent_get_price').val());
							urgent_get_vat = parseInt($('.urgent_get_vat').val());
							urgent_price_inc_vat = (price_inc_vat*urgent_get_price)/100;
							
							$(document).on('change', '[id^=inline-<?php echo $i;?>-checkbox]', function() {
									var numberOfChecked = $('[id^=inline-<?php echo $i;?>-checkbox]').filter(':checked').length;
									var totalCheckboxes = $('input:checkbox').length;
									var numberNotChecked = $('input:checkbox:not(":checked")').length;
									
									var price = h(numberOfChecked*price_inc_vat);
									$('.subtotal_<?php echo $i;?>').text(price);
									$('.input_price_novat_<?php echo $i;?>').val(numberOfChecked*get_price);
									$('.input_vat_<?php echo $i;?>').val(numberOfChecked*get_vat*get_price/100);
									$('.input_subtotal_<?php echo $i;?>').val(numberOfChecked*price_inc_vat);
									$('.quantity_<?php echo $i;?>').text(numberOfChecked + ' <?php echo T_('teeth');?>');
									$('.count_check_<?php echo $i;?>').val(numberOfChecked);
								
							});
							var fileSelectEle = document.getElementById('stlinput_<?php echo $i;?>');
							fileSelectEle.onchange = function ()
							{
								//upload_image();
								if(fileSelectEle.value.length == 0) {
									$('.stl_input_<?php echo $i;?>').val('');
								} else {
									$('.stl_input_<?php echo $i;?>').val(fileSelectEle.files[0].name);
									$('.file_field_error_<?php echo $i;?>').remove();
								}
							}
							
							if(d.format('H') > 12) {
								$('#normal<?php echo $i;?>').attr('checked', 'checked');
								$('.normal<?php echo $i;?>').show();
							} else {
								$('.normal<?php echo $i;?>').hide();
							}

							
							if ($('.delivery_time<?php echo $i;?>').is(":checked")) {
								var delivery_time = $(".delivery_time<?php echo $i;?>:checked").val();
								if(delivery_time=='normal') {
									$('.normal<?php echo $i;?>').show();
								}
							}
							
							$('.delivery_time<?php echo $i;?>').click(function(){
								var inputValue = $(this).attr('value');
								var targetBox = $('.' + inputValue + '<?php echo $i;?>');
								$('.box<?php echo $i;?>').not(targetBox).hide();
								$(targetBox).show();
								if(inputValue=='urgent') {
									if(d.format('H') > 15) {
										$('.delivery_time_urgent').val(1);
										$('.delivery_time_out').val('');
										$.alert({
											title: false,
											content: translate.out_of_urgent,
											buttons: {
												yes: {
													text: translate.btn_close_alert,
												}
											}
										});
									} else {
										$('.delivery_time_urgent').val('');
										var numberOfChecked = $('.count_check_<?php echo $i;?>').val();
										console.log(numberOfChecked);
										$('.urgent_subtotal_<?php echo $i;?>').val(numberOfChecked*urgent_price_inc_vat);
										$('.urgent_price_<?php echo $i;?>').val(numberOfChecked*urgent_price_inc_vat);
									}
								} else if(inputValue=='same') {
									$('.delivery_time_out').val('');
									$('.urgent_subtotal_<?php echo $i;?>').val(0);
									$('.urgent_price_<?php echo $i;?>').val(0);
									if(d.format('H') > 12) {
										$('.delivery_time_out').val(1);
										$('.delivery_time_urgent').val('');
										$.alert({
											title: false,
											content: translate.out_of_time,
											buttons: {
												yes: {
													text: translate.btn_close_alert,
												}
											}
										});
									}
								} else {
									$('.urgent_subtotal_<?php echo $i;?>').val(0);
									$('.urgent_price_<?php echo $i;?>').val(0);
								}
							});

					});
                    </script>
                   
					<script>										
						$(document.body).on('change',".select2-search__field",function (e) {
						   //doStuff
						   if($('#select2-select_0-results').length > 0) 
						   {
							   var optVal= $(".select2-results").text();
							   if(optVal!="")
							   {
								   //select2-select_customer-container
								   //select2-select_0-container
								   $("#select2-select_0-container").text(optVal);
								   $("input.patient_name_0").val(optVal);
							   }
						   }   
						});	
						
					$("input.work_tool").change(function() {
						if (this.value == 'cad') {
							$('#select1').show();
						}
						else {
							$('#select1').hide();
						}
					});						
					</script>
					
					
				    <?php
					
						if(isset($_SESSION["contact"]["name"][$i]) && $_SESSION["contact"]["name"][$i]!="") 
						{
					
						   echo '<script>
									setTimeout(function(){
                                         var  optVal_back = "'.$_SESSION["contact"]["name"][$i].'"; 										
								         $("#select2-select_0-container").text(optVal_back);
						                 $("input.patient_name_0").val(optVal_back);										
									}, 50);
						   </script>';
									
						}
					?>
					
					
				<?php /*} */ // end foreach ?>
					</div> <!-- card-body -->	
					<input type="hidden" name="token" value="<?php echo $token;?>">
					<input type="hidden" name="get_price" class="get_price" value="<?php echo $get_price;?>">
					<input type="hidden" name="get_vat" class="get_vat" value="<?php echo $get_vat;?>">
					<input type="hidden" name="urgent_get_price" class="urgent_get_price" value="<?php echo $urgent_get_price;?>">
					<input type="hidden" name="urgent_price_inc_vat" class="urgent_price_inc_vat" value="<?php echo $urgent_price_inc_vat;?>">
				   	<div class="card-footer">
					  <!--<button id="addMore" class="btn btn-primary" type="button">
						<?php echo T_('Add more'); ?></button>-->
					  <button class="btn btn-warning btn-warn" type="submit" name="submit" onclick='upload_image();'><?php echo T_('Confirm'); ?></button>
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
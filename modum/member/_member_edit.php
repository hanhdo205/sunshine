<?php

if(isset($_GET["id"]) && $_GET["id"] != '')
{
	$id = $_GET["id"];
	$infor_account_edit = $dbf->getInfoColum("member",$id);
	
	$ma_id   =  $infor_account_edit["ma_id"];
	
	$_SESSION["member"]["company_name"] =  $infor_account_edit["company"];
	$_SESSION["member"]["First_Name"] = $infor_account_edit["first_name"];
	$_SESSION["member"]["Last_Name"] = $infor_account_edit["last_name"];
	$_SESSION["member"]["responsible_person"] = $infor_account_edit["responsible_person"];
	$_SESSION["member"]["gender"] = $infor_account_edit["gender"];
	$_SESSION["member"]["birthdate"] = $infor_account_edit["date_ngaysinh"];
	
	$_SESSION["member"]["prefecture"]  = $infor_account_edit["prefecture"];
	$_SESSION["member"]["city"] = $infor_account_edit["city"];
	$_SESSION["member"]["address"] = $infor_account_edit["address"];
	
	$_SESSION["member"]["User_Email"] = $infor_account_edit["email"];
	$_SESSION["member"]['User_Phone'] = $infor_account_edit["phone_number"];
	$_SESSION["member"]['description'] = $infor_account_edit["description"];
	$_SESSION["member"]['profile_img'] = $infor_account_edit["profile_img"];
	$_SESSION["member"]['card_img'] = $infor_account_edit["card_img"];
	$_SESSION["member"]['tuition_fee'] = $infor_account_edit["tuition_fee"];
	$_SESSION["member"]['meals_fee'] = $infor_account_edit["meals_fee"];
	$_SESSION["member"]['tools_fee'] = $infor_account_edit["tools_fee"];
	$_SESSION["member"]['reg_date'] = date('Y/m/d',$infor_account_edit["reg_date"]);
	$_SESSION["member"]['reminder_date'] = $infor_account_edit["reminder_date"];

}
//printf("<pre>%s</pre>",print_r($_SESSION["member"],true));								
?>
<script>
	var readURLs = function(input,div) {
		$('#' + div).empty();   
		var number = 0;
		$.each(input.files, function(value) {
			var reader = new FileReader();
			
			reader.onload = function (e) {
				var id = (new Date).getTime();
				
				$('#' + div).prepend('<a href="'+e.target.result+'" data-fancybox="images"><img id='+id+' class="thumb remove_img_preview" src='+e.target.result+' data-index='+number+' /></a>');
				number++;
			};
			reader.readAsDataURL(input.files[value]);
		});
	}
	var translate = {
		choose_csv_label:"<?php echo T_('Choose CSV file');?>",
		select_file_label:"<?php echo T_('Select file');?>",
		toast_title:"<?php echo T_('Notice');?>",
		csv_imported:"<?php echo T_('CSV imported!');?>",
		cancel_btn:"<?php echo T_('Cancel');?>",
		yes_btn:"<?php echo T_('Yes');?>",
		submit_btn:"<?php echo T_('Submit');?>",
		form_payment_update_title:"<?php echo T_('Payment update');?>",
		invalid:"<?php echo T_('Invalid File. Upload : <b>csv</b> Files.');?>",
		close_btn:"<?php echo T_('Close');?>",
		member_updated:"<?php echo T_('Member updated');?>",
	};
</script>

<link href="vendors/select2/css/select2.min.css" rel="stylesheet" />
<!-- Plugins and scripts required by this view-->
<script src="vendors/select2/js/select2.min.js"></script>
<link rel="stylesheet" type="text/css" href="/js/fancybox/jquery.fancybox.min.css" media="screen" />
<script type="text/javascript" src="/js/fancybox/jquery.fancybox.min.js"></script>
<link rel="stylesheet" href="css/custom/jquery-ui.css">
<script src="js/custom/jquery-ui.js"></script>
<?php if(isset($_SESSION['language']) && $_SESSION['language'] != 'en_US') { ?>
<script src="js/custom/datepicker-<?php echo $datepicker_lang[$_SESSION['language']];?>.js"></script>
<?php } ?>
<link rel="stylesheet" href="js/jconfirm/jquery-confirm.css">
<script src="js/jconfirm/jquery-confirm.js"></script>
<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item active"><?php echo T_('Member edit');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">
		<div class="row">
		  <div class="col-md-12">
		  
			<div class="card">
			  <div class="card-header"><?php echo T_('Member edit');?>
			  <!--<div class="card-header-actions">
						<button class="btn btn-sm btn-warning btn-warn csv_member_import"><i class="icon-cloud-upload"></i> <?php echo T_('CSV import');?></button>
					  </div>-->
			  </div>
			  
			  <form action="" method="post" enctype="multipart/form-data" novalidate>
			  <div class="card-body">
                    <?php
							$profile_img=$_SESSION["member"]['profile_img'];
							$card_img=$_SESSION["member"]['card_img'];
						    $isValue = true;
                            if (isset($_POST["edit_member"])) {
							  for($i=1;$i<=2;$i++)
								{		
								$listFile = "file".$i;
								
								if($_FILES[$listFile]["tmp_name"])
									{
								
									 $filename = $_FILES[$listFile]['name'];
									 $ext = pathinfo(strtolower($filename), PATHINFO_EXTENSION);
									 $allowed_filetypes = array('jpg','gif','png','jpeg');
									 $max_filesize = 2 * (1024 * 1024); // 2MB
									if(!in_array($ext,$allowed_filetypes))
									{				 
										 $result["status"] = 0;  
										 $result["msg"][] = "Only upload file .JPG, .GIF, .PNG";
										  echo '<div class="alert alert-danger alert-dismissable">'.T_('Only upload file .JPG, .GIF, .PNG').'</div>';
											$isValue = false;
										 
									}else
									{
										
											if(filesize($_FILES[$listFile]['tmp_name']) > $max_filesize)
											{						
												 $result["status"] = 0;  
												 $result["msg"][] = "File image very large.>4Mb";
												 echo '<div class="alert alert-danger alert-dismissable">'.T_('File zise too large.').'</div>';
												 $isValue = false;
											}else
											{	if($i==1) $picture = "profile_img"; else $picture = "card_img";
												$newName = $ma_id.'_'.$picture;
												$path = "upload/member/".$newName.".".$ext;
												move_uploaded_file($_FILES[$listFile]['tmp_name'], $path);
												$$picture = $path;
												$_SESSION["member"][$picture] = $path;
											}
									}
									
									
								  }
								} /* end for */
								
								foreach ($_POST as $key => $value) {
									
										if(!is_array($value)){
										   $_SESSION["member"][$key] = $dbf->filter($value);	
										}else{
										   $_SESSION["member"][$key] = $value;
										}
								}
								
								$rstcheck2 = $dbf->getDynamic("member", "email ='" . $_SESSION["member"]['User_Email'] . "' AND ma_id != '" . $ma_id ."'", "");
								
								// validate
								
								if ($_SESSION["member"]['First_Name']==""){
									$first_name_require = true;
									$isValue = false;
								}
								if ($_SESSION["member"]['Last_Name']==""){
									$last_name_require = true;
									$isValue = false;
								}
								
								if ($_SESSION["member"]['prefecture']==""){
									$prefecture_require = true;
									$isValue = false;
								}
								if ($_SESSION["member"]['city']==""){
									$city_require = true;
									$isValue = false;
								}
								if ($_SESSION["member"]['address']==""){
									$address_require = true;
									$isValue = false;
								}
								if ((int) $dbf->totalRows($rstcheck2) != 0 ){
									$email_exist = true;
									$isValue = false;
								} /*elseif ( !filter_var($_SESSION["member"]['User_Email'], FILTER_VALIDATE_EMAIL) && $_SESSION["member"]['User_Email'] != "" ){
									$email_invalid = true;
									$isValue = false;
								}*/ elseif ($_SESSION["member"]['User_Email']==""){
									$email_require = true;
									$isValue = false;
								}
								
								
							   if($isValue) {
		 
									 $_SESSION["member"]['profile_img'] = $profile_img;
									 $_SESSION["member"]['card_img'] = $card_img;
									 $array_col = array("ma_id" => $_SESSION["member"]["User_ID"],"roles_id" => 15,"first_name" => $_SESSION["member"]["First_Name"],"last_name" => $_SESSION["member"]["Last_Name"],"company" => $_SESSION["member"]["company_name"],"responsible_person" => $_SESSION["member"]["responsible_person"],"gender" => $_SESSION["member"]["gender"],"date_ngaysinh" => $_SESSION["member"]["birthdate"],"prefecture" => $_SESSION["member"]["prefecture"],"city" => $_SESSION["member"]["city"],"address" => $_SESSION["member"]["address"],"parentid" => $_SESSION["member_id"],"tendangnhap" => $_SESSION["member"]["username"],"password" => md5($_SESSION["member"]['User_Password']),"password2" => md5($_SESSION["member"]["User_Password"]),"password3" => md5($_SESSION["member"]["User_Password"]),"email" => $_SESSION["member"]["User_Email"],"phone_number" => $_SESSION["member"]['User_Phone'],"description" => $_SESSION["member"]["description"],"profile_img" => $_SESSION["member"]["profile_img"],"card_img" => $_SESSION["member"]["card_img"],"reg_date" => strtotime($_SESSION["member"]["reg_date"]),"reminder_date" => $_SESSION["member"]["reminder_date"],"tuition_fee" => $_SESSION["member"]["tuition_fee"],"meals_fee" => $_SESSION["member"]["meals_fee"],"tools_fee" => $_SESSION["member"]["tools_fee"],"dateupdated"=>time(),"member_re"=>1,"status"=>1,"active_register"=>1,"is_read"=>"yes");
										
									 $affect = $dbf->updateTable("member", $array_col, "id='" . $_GET["id"] . "'");
									 if ($affect > 0)
									 {
										  //echo '<div class="alert alert-dismissable alert-success">'.T_('New member created successfully !!!').'</div>';
										  
											foreach ($_POST as $key => $value) {
												$$key = "";
											  }	
											//unset($_POST);
											//unset($_SESSION["member"]);
											//echo "<script>window.location.href='member-edit.aspx/?id='".$id.";</script>";
											//Header( "Location: member-edit.aspx/?id=".$id );
											//exit;
									 }
										  
							}
							
						}
						//printf("<pre>%s</pre>",print_r($_SESSION["member"],true));
                          ?>
					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Member ID');?></label>						  
						<div class="col-md-5">
							<input type="text" tabindex="-1" readonly="" placeholder="Automatically generated" value="<?php echo $ma_id;?>" class="form-control" name="User_ID" id="User_ID" readonly>
						</div>
					</div>
					
					
					<div class="form-group row" style="display:none">
						<label class="col-md-3 col-form-label"><?php echo T_('Username');?> <span class="text-danger">*</span></label>						  
						<div class="col-md-5">
							<input type="hidden" tabindex="-1" value="<?php echo $ma_id;?>" class="form-control" name="username" id="username" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')">
						</div>
					</div>

					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Parent name');?></label>						  
						<div class="col-md-5">
							<input type="text" tabindex="-1" value="<?php echo $_SESSION["member"]["company_name"];?>" class="form-control" name="company_name" id="company_name" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')">
						</div>
					</div>
				
					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Student name');?> <span class="text-danger">*</span></label>
						<div class="col-md-5 form-inline">
							
							<div class="form-group">
								<label class="mr-1" for="First_Name"><?php echo T_('Fullname');?></label>
								<input type="text" value="<?php echo $_SESSION["member"]["First_Name"];?>" class="form-control <?php echo ($first_name_require) ? 'has-error':'';?>" name="First_Name" id="First_Name" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')" size="40">
							</div>
							<div class="form-group">
								<label class="mx-1" for="Last_Name"><?php echo T_('Nickname');?></label>
								<input type="text" value="<?php echo $_SESSION["member"]["Last_Name"];?>" class="form-control <?php echo ($last_name_require) ? 'has-error':'';?>" name="Last_Name" id="Last_Name" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')">
							</div>
							<?php if($first_name_require || $last_name_require) { ?><small class="has_error field_message text-danger ml-2"><?php echo T_('Please fill out person name');?></small><?php } ?>
						</div>			
					</div>
					
					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Gender');?> <span class="text-danger">*</span></label>
						<div class="col-md-5 col-form-label">
							<div class="form-check form-check-inline mr-1">
							<?php
								$is_male = $utl->checked(array($_SESSION["member"]["gender"]),'male');
								$is_female = $utl->checked(array($_SESSION["member"]["gender"]),'female');
							?>
							<input class="form-check-input" id="man" type="radio" value="male" name="gender" <?php echo ($is_male) ? 'checked' : ($is_female ? '' : 'checked');?>>
							<label class="form-check-label" for="man"><?php echo T_('Male');?></label>
							</div>
							<div class="form-check form-check-inline mr-1">
							<input class="form-check-input" id="female" type="radio" value="female" name="gender" <?php echo ($is_female) ? 'checked' : '';?>>
							<label class="form-check-label" for="female"><?php echo T_('Female');?></label>
							</div>
							
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Email/Facebook');?></label>
						<div class="col-md-5">
							<input type="email" value="<?php echo $_SESSION["member"]["User_Email"];?>" class="form-control <?php echo ($email_exist || $email_require || $email_invalid) ? 'has-error':'';?>" name="User_Email" id="User_Email" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')">
							<?php if($email_exist) { ?><small class="has_error field_message text-danger"><?php echo T_('Email is already exits. Please try again');?></small><?php } ?>
							<?php if($email_require) { ?><small class="has_error field_message text-danger"><?php echo T_('Please provide an email');?></small><?php } ?>
							<?php if($email_invalid) { ?><small class="has_error field_message text-danger"><?php echo T_('Please provide a valid email');?></small><?php } ?>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Birthdate');?></label>						  
						<div class="col-md-5">
							<input type="text" tabindex="-1" value="<?php echo $_SESSION["member"]["birthdate"];?>" class="form-control" name="birthdate" id="birthdate" autocomplete="off">
						</div>
					</div>

					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Phone Number');?></label>						  
						<div class="col-md-5">
							<input type="number" class="form-control" name="User_Phone" id="User_Phone" value="<?php echo $_SESSION["member"]["User_Phone"];?>" >
						</div>
					</div>					
					
					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Address');?> <span class="text-danger">*</span></label>						  
						<fieldset class="col-md-5 form-group">						 
						  <div class="input-group row">
							<div class="col-sm-12 mb-3">
								<input class="form-control <?php echo ($address_require) ? 'has-error':'';?>" id="" type="text" name="address" placeholder="<?php echo T_('Street, building, apartment, apartment name');?>" value="<?php echo $_SESSION["member"]["address"];?>"/>
							</div>
						    <div class="col-sm-12 mb-3">
								<input class="form-control <?php echo ($prefecture_require) ? 'has-error':'';?>" id="" type="text" name="prefecture" placeholder="<?php echo T_('District');?>" value="<?php echo $_SESSION["member"]["prefecture"];?>"/>
							</div>
							<div class="col-sm-12 mb-3">
								<input class="form-control <?php echo ($city_require) ? 'has-error':'';?>" id="" type="text" name="city" placeholder="<?php echo T_('City');?>" value="<?php echo $_SESSION["member"]["city"];?>"/>
							</div>
							
							<div class="col-sm-12">
								<?php if($postal_code_require || $prefecture_require || $city_require || $address_require) { ?><div class="text-danger"><?php echo T_('Please enter all address fields');?></div><?php } ?>
							</div>
						  </div>
						</fieldset>									
					</div>

					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Responsible person');?></label>						  
						<div class="col-md-5">
								<select id="select_person" class="form-control select_person select2" name="responsible_person">
									<option value=""><?php echo T_('---');?></option>
									<?php $responsible_person = $dbf->getDynamic("responsible_person", "", "id ASC");
										if($dbf->totalRows($responsible_person)>0) {
											while( $person = $dbf->nextData($responsible_person)){
												echo '<option value="'.$person['id'].'" '.$utl->selected($person['id'],$_SESSION["member"]["responsible_person"]).'>' . $person['first_name'] . $person['last_name'] . '</option>';
											}
										}
									?>
								</select>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Profile picture');?></label>
						<div class="d-flex flex-row">
							<div class="p-2">
								<div id ="up_profile"><?php
									if(file_exists($_SESSION["member"]["profile_img"])) { ?>
										   <a href="<?php echo $profile_img;?>" data-fancybox="images"><img class="d-block select-image mb-2" src="<?php echo $_SESSION["member"]["profile_img"];?>" width="100px"/></a>
									   <?php } else {
										   echo T_('No file select');
										}
									?>
								</div>
							</div>
							<div class="p-2">
								<label for="profile_img" class="btn btn-primary"><?php echo T_('Select file');?></label>
							</div>
						</div>
						
					</div>
					
					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Birth certificate');?></label>						  
						<div class="d-flex flex-row">
							<div class="p-2">
								<div id ="up_card"><?php
									if(file_exists($_SESSION["member"]["card_img"])) { ?>
										   <a href="<?php echo $card_img;?>" data-fancybox="images"><img class="d-block select-image mb-2" src="<?php echo $_SESSION["member"]["card_img"];?>" width="100px"/></a>
									   <?php } else {
										   echo T_('No file select');
										}
									?>
								</div>
							</div>
							<div class="p-2">
								<label for="card_img" class="btn btn-primary"><?php echo T_('Select file');?></label>
							</div>
						</div>
						
					</div>
					
					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Registration date');?></label>						  
						<div class="col-md-5">
							<input type="text" tabindex="-1" value="<?php echo $_SESSION["member"]['reg_date'];?>" class="form-control" name="reg_date" id="reg_date" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')" autocomplete="off">
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Reminder date');?></label>						  
						<div class="col-md-5">
							<input type="text" tabindex="-1" value="<?php echo $_SESSION["member"]["reminder_date"];?>" class="form-control" name="reminder_date" id="reminder_date" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')" autocomplete="off">
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Term (month)');?></label>
						<div class="col-md-5 form-inline">
							<div class="form-group">
								<label class="mr-1" for="tuition_fee"><?php echo T_('Tuition');?></label>
								<input type="text" value="<?php echo $_SESSION["member"]["tuition_fee"];?>" class="form-control <?php echo ($tuition_fee_require) ? 'has-error':'';?>" name="tuition_fee" id="tuition_fee" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')" size="2">
							</div>
							<div class="form-group">
								<label class="mx-1" for="meals_fee"><?php echo T_('Meals');?></label>
								<input type="text" value="<?php echo $_SESSION["member"]["meals_fee"];?>" class="form-control <?php echo ($meals_fee_require) ? 'has-error':'';?>" name="meals_fee" id="meals_fee" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')" size="2">
							</div>
							<div class="form-group">
								<label class="mx-1" for="tools_fee"><?php echo T_('Tools');?></label>
								<input type="text" value="<?php echo $_SESSION["member"]["tools_fee"];?>" class="form-control <?php echo ($tools_fee_require) ? 'has-error':'';?>" name="tools_fee" id="tools_fee" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')" size="2">
							</div>
							<?php if($tuition_fee_require || $meals_fee_require || $tools_fee_require) { ?><small class="has_error field_message text-danger ml-2"><?php echo T_('Please fill out terms fee');?></small><?php } ?>
						</div>			
					</div>
					
					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Note');?></label>						  
						<div class="col-md-5">
							<textarea name="description" class="form-control" rows="7"><?php echo $_SESSION["member"]["description"];?></textarea>
						</div>
					</div>

					<input id="profile_img" name="file1" type="file" style="visibility:hidden;" onchange="readURLs(this,'up_profile');">
					<input id="card_img" name="file2" type="file" style="visibility:hidden;" onchange="readURLs(this,'up_card');">
					</div> <!-- card-body -->	
				   	<div class="card-footer">
					<a href="javascript:void(0);" onclick="goBack(-1)" id="goback" class="btn btn-link btn-lg active" role="button" aria-pressed="true"><i class="fa fa-angle-double-left" aria-hidden="true"></i> <?php echo T_('Go back to Member List');?></a>
					  <button class="btn btn-warning btn-warn" type="submit" name="edit_member">
						 <?php echo T_('Save');?></button>
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
<script type="text/javascript" src="vendors/toastr/js/toastr.js" class="view-script"></script>
<link rel="stylesheet" href="vendors/toastr/css/toastr.css">
<script src="js/custom/toastr.js"></script>
<script src="js/custom/member_create.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('#select_person').select2({
		  theme: 'bootstrap'
	});
	
	$('#birthdate,#reg_date,#reminder_date').datepicker({
		changeYear: true,
		changeMonth: true,
		dateFormat: "yy/mm/dd"
	});
	
	$('[data-fancybox="images"]').fancybox({
		afterLoad : function(instance, current) {
			var pixelRatio = window.devicePixelRatio || 1;

			if ( pixelRatio > 1.5 ) {
				current.width  = current.width  / pixelRatio;
				current.height = current.height / pixelRatio;
			}
		},
		closeClick  : true,
		openEffect  : 'fade',
		closeEffect : 'fade',
		scrolling   : false,
		padding     : 0,
		autoScale: true,
		smallBtn : true,
		toolbar  : false
	});
	
});
<?php if(isset($_POST['edit_member'])) { 
	if(!($_SESSION['count'])){
		$_SESSION['count'] = 2;
	}else{
		$count = $_SESSION['count'] + 1;
		$_SESSION['count'] = $count;
	}?>
	 $('#goback').attr('onclick','goBack(-<?php echo $_SESSION["count"];?>)');
	 var $toast = toastr["success"](translate.member_updated,translate.toast_title);
<?php } ?>
</script>

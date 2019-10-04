<?php
	session_start();

	if (!isset($_SESSION['token'])) {
		$token             = md5(uniqid(rand(), TRUE));
		$_SESSION['token'] = $token;
	}else
	{
		$token = $_SESSION['token'];
	}
	if(isset($_SESSION['invalid_token'])) $error= '<p class="error_messe" style="color: red;">メールの送信エラー！もう一度送信してください。</p>';

	if(isset($_POST['confirm']))
    {
        unset($_SESSION["contact"]); 
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
				 $username_exist = false;
				 $email_exist = false;
				 $account_dupplicate = false;
				 $company_require = false;
				 $company_alpha = false;
				 $user_fullname_require = false;
				 $user_fullname_alpha_require = false;
				 $user_fullname_alpha = false;
				 $password_require = false;
				 
				 //$rstcheck = $dbf->getDynamic("member", "tendangnhap ='" . $_SESSION["contact"]['username'] . "'", "");
				 $rstcheck2 = $dbf->getDynamic("member", "email ='" . $_SESSION["contact"]['User_Email'] . "'", "");
				 $rstcheck3 = $dbf->getDynamic("member", "ma_id ='" . $_SESSION["contact"]['User_ID'] . "'", "");
				  
				 $aValid = array('-', '_', '.', ',', '#', '>', '<', ';', ':',' ');
					 // case ((int) $dbf->totalRows($rstcheck) != 0 ):
						// $username_exist = true;
						// $isvalue = false;
						// break;
					// case ($_SESSION["contact"]['username']=="" ):
						// $username_require = true;
						// $isvalue = false;
						// break;
					if ($_SESSION["contact"]['company_name']==""){
						$company_require = true;
						$isvalue = false;
					}
					if ($_SESSION["contact"]['company_name_alphabet']==""){
						$company_name_alpha_require = true;
						$isvalue = false;
					}
					if (ctype_alpha(str_replace($aValid, '', $_SESSION["contact"]['company_name_alphabet'])) === false && $_SESSION["contact"]['company_name_alphabet']!=""){
						$isvalue = false;
						$company_alpha = true;
					}
					if ($_SESSION["contact"]['User_Name']==""){
						$user_fullname_require = true;
						$isvalue = false;
					}
					if ($_SESSION["contact"]['User_Name_alphabet']==""){
						$user_fullname_alpha_require = true;
						$isvalue = false;
					}
					if (ctype_alpha(str_replace(' ', '', $_SESSION["contact"]['User_Name_alphabet'])) === false && $_SESSION["contact"]['User_Name_alphabet']!=""){
						$isvalue = false;
						$user_fullname_alpha = true;
					}
					if ((int) $dbf->totalRows($rstcheck2) != 0 ){
						$email_exist = true;
						$isvalue = false;
					}
					if ((int) $dbf->totalRows($rstcheck2) != 0 ){
						$email_exist = true;
						$isvalue = false;
					}
					if ($_SESSION["contact"]['postal_code']==""){
						$postal_code_require = true;
						$isvalue = false;
					}
					if ($_SESSION["contact"]['prefecture']==""){
						$prefecture_require = true;
						$isvalue = false;
					}
					if ($_SESSION["contact"]['city']==""){
						$city_require = true;
						$isvalue = false;
					}
					if ($_SESSION["contact"]['address']==""){
						$address_require = true;
						$isvalue = false;
					}
					if ($_SESSION["contact"]['User_Email']==""){
						$email_require = true;
						$isvalue = false;
					}
					if ( !filter_var($_SESSION["contact"]['User_Email'], FILTER_VALIDATE_EMAIL) && $_SESSION["contact"]['User_Email'] != "" ){
						$email_require = true;
						$isvalue = false;
					}
					if ($_SESSION["contact"]['User_Phone']==""){
						$phone_require = true;
						$isvalue = false;
					}
					
					if ($_SESSION["contact"]['User_Password']==""){
						$password_require = true;
						$isvalue = false;
					}
					
				 
				 if($isvalue)
				 {
					
					 echo "<script>window.location.href='register-confirm.aspx';</script>";
					 Header( "Location: register-confirm.aspx" );
					 exit;
					
				 } /*else
				 {
					$error = $error;
				 }*/
	
		}else
			{
				$error.= '<p class="error_messe" style="color: red;">メールの送信エラー！もう一度送信してください。</p>';
			}
		
    }
	
?>
<?php
if(isset($_GET['lang']) && !empty($_GET['lang'])){
 $_SESSION['language'] = $_GET['lang'];
}

$locale = (isset($_SESSION['language']))? $_SESSION['language'] : DEFAULT_LOCALE;
//$locale = (isset($_SESSION['language']))? $_SESSION['language'] : $rowgetInfo["language"];
//$locale = ($rowgetInfo["language"])? $rowgetInfo["language"] : DEFAULT_LOCALE;

// gettext setup
T_setlocale(LC_MESSAGES, $locale);
// Set the text domain
$domain = $locale;
T_bindtextdomain($domain, LOCALE_DIR);
T_bind_textdomain_codeset($domain, $encoding);
T_textdomain($domain);

header("Content-type: text/html; charset=$encoding");

$ma_id = $dbf->general_ma_id();
 
$my_passwords = $utl->randomPassword(10,1,"lower_case,upper_case,numbers,special_symbols");
$User_Password = $my_passwords[0];

?>
<?php $setting_policy = $dbf->getInfoColum("setting",27);
$policy = unserialize($setting_policy['value']);
$trans = $lang[$_SESSION['language']];

$logo_exists = false;

$getlogo_info = $dbf->getInfoColum("setting",24);
$set_logo = $getlogo_info['value'];
if(file_exists($set_logo)) {
	   $logo_exists = true;
}
?>
<!DOCTYPE html>
<!--
* CoreUI Pro - Bootstrap Admin Template
* @version v2.1.9
* @link https://coreui.io/pro/
* Copyright (c) 2018 creativeLabs Łukasz Holeczek
* License (https://coreui.io/pro/license)
-->
<html lang="en">
<head>
  <base href="./">
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
  <meta name="description" content="Rang Su Viet Quoc Group">
  <meta name="author" content="Łukasz Holeczek">
  <meta name="keyword" content="Rang Su, Viet Quoc Group">
  <title>Register</title>
  <!-- Icons-->
  <link href="vendors/@coreui/icons/css/coreui-icons.min.css" rel="stylesheet">
  <link href="vendors/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">
  <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
  <link href="vendors/simple-line-icons/css/simple-line-icons.css" rel="stylesheet">
  <!-- Main styles for this application-->
   <link href="css/coreui/style.css" rel="stylesheet">
   <link href="css/custom/register.css" rel="stylesheet">
  <link href="vendors/pace-progress/css/pace.min.css" rel="stylesheet">
  <script>
		var ajax_url = "<?php echo url() . '/modum/member/do_ajax.php';?>"
</script>
</head>
<body class="app custom-app">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="customer-padding card mx-4">
          <div class="card-body p-3">
          	<div class="text-center mb-3">
                <img class="navbar-brand-full" src="<?php echo $set_logo;?>" width="200" height="50" alt="Vietquoc Logo">
              </div>
            <h1 class="text-center"><?php echo T_('Sign up');?></h1>
            <p class="text-muted"><?php echo T_('Create your account');?></p>
			
            <form id="register" action="" method="post" novalidate>
					<input type="hidden" tabindex="-1" readonly="" placeholder="Automatically generated" value="<?php echo $ma_id;?>" class="form-control" name="User_ID" id="User_ID">
					<!-- <div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Username');?> <span class="text-danger">*</span></label>						  
						<div class="col-md-9">
							<input type="text"  class="form-control <?php echo ($username_exist || $account_dupplicate || $username_require) ? 'has-error':'';?>" name="username" id="username" value="<?php echo $_SESSION["contact"]["username"];?>" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')">
							<?php if($username_exist) { ?><div class="text-danger"><?php echo T_('Username is already exits. Please try again');?></div><?php } ?>
							<?php if($account_dupplicate) { ?><div class="text-danger"><?php echo T_('Create member error !!! Account is duplicate');?></div><?php } ?>
							<?php if($username_require) { ?><div class="text-danger"><?php echo T_('Please enter username');?></div><?php } ?>
						</div>
					</div> -->

					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Company name');?><span class="text-danger">*</span></label>			  
						<div class="col-md-9">
							<input type="text" class="form-control <?php echo ($company_require) ? 'has-error':'';?>" name="company_name" id="company_name"  placeholder="<?php echo T_('Please enter your company name');?>" value="<?php echo $_SESSION["contact"]["company_name"];?>" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')">
							<?php if($company_require) { ?><div class="text-danger"><?php echo T_('Please enter company name');?></div><?php } ?>
							
						</div>
					</div>

					<div class="form-group row">
						
						<label class="col-md-3 col-form-label"><?php echo T_('Company name <br/> (Roman characters)');?><span class="text-danger">*</span></label>						  
						<div class="col-md-9">
							<input type="text" class="form-control <?php echo ($company_name_alpha_require || $company_alpha) ? 'has-error':'';?>" name="company_name_alphabet" id="company_name_alphabet" placeholder="<?php echo T_('Please enter your company name in Roman characters');?>" value="<?php echo $_SESSION["contact"]["company_name_alphabet"];?>">
							<?php if($company_name_alpha_require) { ?><div class="text-danger"><?php echo T_('Please enter company name');?></div><?php } ?>
							<?php if($company_alpha) { ?><div class="text-danger"><?php echo T_('Please enter alphabet only');?></div><?php } ?>
							
						</div>
					</div>

					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Contact person');?><span class="text-danger">*</span></label>
						<div class="col-md-9">
							<input type="text"  class="form-control <?php echo ($user_fullname_require) ? 'has-error':'';?>" name="User_Name" id="User_Name" placeholder="<?php echo T_('Please enter your contact person name');?>" value="<?php echo $_SESSION["contact"]["User_Name"];?>" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')">
							<?php if($user_fullname_require) { ?><div class="text-danger"><?php echo T_('Please enter contact person');?></div><?php } ?>
						</div>						  				
					</div>

					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Contact person <br/> (Roman characters)');?> <span class="text-danger">*</span></label>
						<div class="col-md-9">
							<input type="text"  class="form-control <?php echo ($user_fullname_alpha_require || $user_fullname_alpha) ? 'has-error':'';?>" name="User_Name_alphabet" id="User_Name_alphabet" placeholder="<?php echo T_('Please enter your contact person name in Roman characters');?>" value="<?php echo $_SESSION["contact"]["User_Name_alphabet"];?>" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')">
							<?php if($user_fullname_alpha_require) { ?><div class="text-danger"><?php echo T_('Please enter contact person ');?></div><?php } ?>
							<?php if($user_fullname_alpha) { ?><div class="text-danger"><?php echo T_('Please enter alphabet only');?></div><?php } ?>
						</div>						  				
					</div>

					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Gender');?><span class="text-danger">*</span></label>
						<div class="col-md-5 col-form-label">
							<div class="form-check form-check-inline mr-1">
								<input class="form-check-input" id="man" type="radio" value="male" name="gender" <?php 
								$is_male = $utl->checked(array($_SESSION["contact"]["gender"]),'male');
								$is_female = $utl->checked(array($_SESSION["contact"]["gender"]),'female');
								echo ($is_male) ? 'checked' : ($is_female ? '' : 'checked');?>>
								<label class="form-check-label" for="man"><?php echo T_('Male');?></label>
							</div>
							<div class="form-check form-check-inline mr-1">
								<input class="form-check-input" id="female" type="radio" value="female" name="gender" <?php echo ($is_female) ? 'checked' : '';?>>
								<label class="form-check-label" for="female"><?php echo T_('Female');?></label>
							</div>
							
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Address');?><span class="text-danger">*</span></label>						  
						<fieldset class="col-md-9 form-group">						 
						  <div class="row">
							<div class="col-sm-12 mb-3">
								<input class="form-control <?php echo ($address_require) ? 'has-error':'';?>" id="" type="text" name="address" value="<?php echo $_SESSION["contact"]["address"];?>" placeholder="<?php echo T_('Street, Building, apartment, suite, floor, etc.');?>" />
							</div>
						    <div class="col-sm-12 mb-3">
								<input class="form-control <?php echo ($prefecture_require) ? 'has-error':'';?>" id="" type="text" name="prefecture" value="<?php echo $_SESSION["contact"]["prefecture"];?>" placeholder="<?php echo T_('Prefecture, province, state, region, etc.');?>" />
							</div>
							<div class="col-sm-12 mb-3">
								<input class="form-control <?php echo ($city_require) ? 'has-error':'';?>" id="" type="text" name="city" value="<?php echo $_SESSION["contact"]["city"];?>" placeholder="<?php echo T_('City');?>" />
							</div>
							<div class="col-sm-12">
								<input class="form-control <?php echo ($postal_code_require) ? 'has-error':'';?>" id="" type="text" name="postal_code" value="<?php echo $_SESSION["contact"]["postal_code"];?>" placeholder="<?php echo T_('Zip code');?>" />
							</div>
							<div class="col-sm-12">
								<?php if($postal_code_require || $prefecture_require || $city_require || $address_require) { ?><div class="text-danger"><?php echo T_('Please enter all address fields');?></div><?php } ?>
							</div>
							
						  </div>
						</fieldset>									
					</div>

					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Email');?><span class="text-danger">*</span></label>						  
						<div class="col-md-9">
							<input type="email" class="form-control <?php echo ($email_exist || $email_require) ? 'has-error':'';?>" name="User_Email" id="User_Email" value="<?php echo $_SESSION["contact"]["User_Email"];?>" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')">
							<?php if($email_exist) { ?><div class="text-danger"><?php echo T_('Email is already exits. Please try again');?></div><?php } ?>
							<?php if($email_require) { ?><div class="text-danger"><?php echo T_('Please enter a valid email');?></div><?php } ?>
						</div>
													
					</div>
					
					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Phone number');?><span class="text-danger">*</span></label>						  
						<div class="col-md-9">
							<div class="row">
								<div class="col-md-4">
									<select name="country_code" class="form-control select2">
									<?php foreach($country_code as $key=>$value) {
										if($value['dial_code'] !='') {
											echo '<option value="' . $value['dial_code'] .'" data-name="'.$key.'" '.$utl->selected($value['dial_code'],$_SESSION["contact"]["country_code"]).'>'. $value['dial_code'] .'</option>';
										}
									}?>
									</select>
								</div>
								<div class="col-md-8">
									<input type="number" class="form-control <?php echo ($phone_require) ? 'has-error':'';?>" name="User_Phone" id="User_Phone" value="<?php echo $_SESSION["contact"]["User_Phone"];?>" >
									
								</div>
								<div class="col-md-4"></div>
								<div class="col-md-8">
									<?php if($phone_require) { ?><div class="text-danger"><?php echo T_('Please enter phone number');?></div><?php } ?>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Payment method');?></label>
						<div class="col-md-9 col-form-label">
							<div class="form-check form-check-inline mr-1">
								<input class="form-check-input" id="invoice" type="radio" value="invoice" name="paymentmethod" <?php echo ($utl->checked(array($_SESSION["contact"]["paymentmethod"]),'invoice')) ? 'checked' : '';?>>
								<label class="form-check-label" for="invoice"><?php echo T_('Payment on invoice');?></label>
							</div>
							<div class="form-check form-check-inline mr-1">
								<input class="form-check-input" id="deposit" type="radio" value="deposit" name="paymentmethod" <?php echo ($utl->checked(array($_SESSION["contact"]["paymentmethod"]),'deposit')) ? 'checked' : '';?>>
								<label class="form-check-label" for="deposit"><?php echo T_('Deposit');?></label>
							</div>
							<div class="form-check form-check-inline mr-1">
								<input class="form-check-input" id="credit" type="radio" value="credit" name="paymentmethod" <?php echo ($utl->checked(array($_SESSION["contact"]["paymentmethod"]),'credit')) ? 'checked' : '';?>>
								<label class="form-check-label" for="credit"><?php echo T_('Credit Card');?></label>
							</div>
							
						</div>
					</div>

					<div class="form-group row">
					    <label class="col-md-3 col-form-label"><?php echo T_('Password');?><span class="text-danger">*</span></label>
						<div class="col-md-7">
							<div class="input-group" id="show_hide_password" >
								<input type="text" class="form-control <?php echo ($password_require) ? 'has-error':'';?>" id="User_Password" name="User_Password" value="<?php echo $User_Password;?>" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')">
								<div class="input-group-append">
									<a href="" class="input-group-text"><i class="fa fa-eye" aria-hidden="true"></i></a>
								</div>
							</div>
							<?php if($password_require) { ?><div class="text-danger"><?php echo T_('Please type strong password');?></div><?php } ?>
						</div>
						<div class="col-md-1"><a href="javascript:void(0)" class="btn btn-light refresh"><i class="fa fa-refresh rotate" aria-hidden="true"></i> <?php echo T_('Refresh');?></a>
						</div>
					</div>
					<div class="form-group">
						<p><?php echo T_('Please read the following terms and conditions carefully and tick check box of the "I agree with the terms and conditions" if you agree. And then, click the "Send" button to proceed.');?></p>
					</div>
					<div class="form-group">
						<div class="section-policy">
						<div class="privacy-box ">
							<?php echo $policy[$trans];?>
						</div>
						<div class="form-group text-center">
							<label>
								<input id="agree" type="checkbox" name="agree" value="agree"> <?php echo T_('I agree with the terms and conditions');?>
							</label>
						</div>
					</div>
					</div>
					<input type="hidden" name="token" value="<?php echo $token;?>">
					<div class="form-group text-center">
						<p class="btn-item">
							<button class="btn btn-primary" type="submit" id="submitBtn" name="confirm" disabled><?php echo T_('Send');?></button>
						</p>
						
					</div>
					<div class="form-group">
						<p class="btn-black">
							 <a href="#" class="btn btn-danger" ><?php echo T_('Back');?>
							</a>
						</p>
					</div>
					
			</form>
          </div>
        </div>
      </div>
    </div>

  </div>
<footer class="footer-bottom2">
	<div class="footer-text">
		©2019 Rang Su Viet Quoc Group. All Rights Reserved.
	</div>
</footer>
 
  <!-- Bootstrap and necessary plugins-->
  <script src="vendors/jquery/js/jquery.min.js"></script>
  <script src="vendors/popper.js/js/popper.min.js"></script>
  <script src="vendors/bootstrap/js/bootstrap.min.js"></script>
  <script src="vendors/pace-progress/js/pace.min.js"></script>
  <script src="vendors/perfect-scrollbar/js/perfect-scrollbar.min.js"></script>
  <script src="vendors/@coreui/coreui-pro/js/coreui.min.js"></script>
  <script src="js/custom/custom.js"></script>
  <link href="vendors/select2/css/select2.min.css" rel="stylesheet" />
  <script src="vendors/select2/js/select2.min.js"></script>
  <script>
  function formaticon (icon) {
	  if (!icon.id) {
		return icon.text;
	  }
	  var name = icon.element.dataset.name.toLowerCase();
	  var $icon = $(
		'<span><i class="flag-icon flag-icon-'+name+'" id="'+name+'"></i> ' + icon.text + '</span>'
	  );
	  return $icon;
	};
	$('.select2').select2({
		  theme: 'bootstrap',
		  templateResult: formaticon
		});
    $('#ui-view').ajaxLoad();
    $(document).ajaxComplete(function() {
      Pace.restart()
    });

    $('input[name="agree"]').on('change', function() {
	    if ($(this).is(":checked")) {
	        $('#submitBtn').removeAttr('disabled');
	    } else {
	        $('#submitBtn').prop('disabled', true);
	    }
	});
    
  </script>

</body>
</html>
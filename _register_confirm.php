<?php
session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');
	
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

	
	include_once 'modum/class.template.php';
	
	if(!isset($_SESSION["contact"]) || empty($_SESSION["contact"]))
	{
		echo "<script>window.location.href='register.aspx';</script>";
		Header( "Location: register.aspx" ); 
	}
	
	if($_SESSION["contact"]["token"] != $_SESSION['token'])
	{
		echo "<script>window.location.href='register.aspx';</script>";
		Header( "Location: register.aspx" );
	}
	
	$gender_arr = array('male'=>T_('Male'),'female'=>T_('Female'));
	$payment_arr = array('credit'=>T_('Credit Card'),'invoice'=>T_('Payment on invoice'),'deposit'=>T_('Deposit'));
	
	if(isset($_POST['submit_send']))
	{


				// do insert database here
				$User_ID = $_SESSION["contact"]['User_ID'];
				$User_Name = $_SESSION["contact"]['User_Name'];
				$User_Name_alphabet = $_SESSION["contact"]['User_Name_alphabet'];
				$company_name = $_SESSION["contact"]['company_name'];
				$company_name_alphabet = $_SESSION["contact"]['company_name_alphabet'];
				$gender = $_SESSION["contact"]['gender'];
				$prefecture = $_SESSION["contact"]['prefecture'];
				$city = $_SESSION["contact"]['city'];
				$address = $_SESSION["contact"]['address'];
				$postal_code = $_SESSION["contact"]['postal_code'];
				//$username = $_SESSION["contact"]['username'];
				$User_ID = $_SESSION["contact"]['User_ID'];
				$User_Password = $_SESSION["contact"]['User_Password'];
				$User_Email = $_SESSION["contact"]['User_Email'];
				$User_Phone = $_SESSION["contact"]["country_code"] . $_SESSION["contact"]['User_Phone'];
				$payment_method = $_SESSION["contact"]["paymentmethod"];
				
				$default_admin_email_info = $dbf->getInfoColum("setting",24);
				$admin_email = $default_admin_email_info['value'];
				

				$customer_subject = T_('Welcome [VietQuocLab]');
				
				$thankyou = T_('Dear customer');
				$username_text = T_('Username');
				$company_text = T_('Company');
				$userid_text = T_('User ID');
				$password_text = T_('Password');
				$password_text = T_('Password');
				$active_text = T_('Activate Account');
				$email_text = T_('Email');
				$pin = $dbf->generate_pin_active();
				
				$username = sprintf(T_('Mrs/Mr %s'),$User_Name);
				$userid = $User_ID;
				$password = $User_Password;
				$signature_text = $signature;
				$body_message = T_('Your new VietquocLab Account has been created. Welcome to the VietquocLab community!<br>Please click on <strong>Activate Account</strong> button to verify your account:');
				$mail_footer_text = $mail_footer;

				$customer_message = Template::get_contents("modum/mail_template/new_register.tpl", array('logo' => HOST.'images/logo.jpg', 'thankyou' => $thankyou, 'pin'=>$pin, 'lang'=>$locale, 'username'=>$username, 'userid'=>$userid, 'userid_text'=>$userid_text, 'password'=>$password, 'password_text'=>$password_text,'username_text'=>$username_text,'active_text'=>$active_text,'email'=>$User_Email,'email_text'=>$email_text,'body_message'=>$body_message,'signature_text'=>$signature_text,'mail_footer_text'=>$mail_footer_text, 'company_name' => $company_name,'company_text' => $company_text,'url' => HOST));
				
				$from = $arraySMTPSERVER["user"];
				$fromName = $arraySMTPSERVER["from"];
				
				$customer_param = array('EmailFrom'=>$from,'FromName'=>$fromName,'ReplyTo'=>$admin_email,'ReplyName'=>'VietQuocLab','EmailTo'=>$User_Email,'ToName'=>$User_Name,'Subject'=>$customer_subject,'Content'=>$customer_message);
				
				require("modum/class.phpmailer.php");
				$mail = new PHPMailer();
				
				$customer_mail = $dbf->sendmail($customer_param,$mail );
				
				if ($customer_mail) 
				{
					
					$array_col = array("ma_id" => $User_ID,"crf_token_login"=>$pin,"roles_id" => 15,"hovaten" => $User_Name,"hovaten_alphabet" => $User_Name_alphabet,"company" => $company_name,"company_alphabet" => $company_name_alphabet,"gender" => $gender,"language" => $_SESSION['language'],"prefecture" => $prefecture,"city" => $city,"address" => $address,"postal_code" => $postal_code,"parentid" => 1,"tendangnhap" => $username,"password" => md5($User_Password),"password2" => md5($User_Password),"password3" => $User_Password,"email" => $User_Email,"phone_number" => $User_Phone,"payment_method" => $payment_method,"datecreated"=>time(),"dateupdated"=>time(),"member_re"=>1,"status"=>0,"active_register"=>0);
					$affect = $dbf->insertTable_2("member", $array_col);
					unset($_POST);
					unset($_SESSION["contact"]);
					
					$token             = md5(uniqid(rand(), TRUE));
					$_SESSION['token'] = $token;
					
					echo "<script>window.location.href='register-complete.aspx';</script>";
					Header( "Location: register-complete.aspx" ); 
					exit; 
					
				}	  

 }
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
          <div class="card-body p-4">
          	<div class="text-center mb-3">
                <img class="navbar-brand-full" src="<?php echo $set_logo;?>" width="200" height="50" alt="Vietquoc Logo">
              </div>
            <h1 class="text-center"><?php echo T_('Confirm');?></h1>
            <p class="text-muted"><?php echo T_('Confirm your input');?></p>
			<?php if($error) echo "<div class='txt-contact'>".$error."</div>"; ?>
            <form action="" method="post">
					
					<!-- <div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Username');?> <span class="text-danger">*</span></label>						  
						<div class="col-md-9">
							<label class="col-form-label"><?php echo $_SESSION["contact"]["username"];?></label>
						</div>
					</div> -->

					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Company Name');?><span class="text-danger">*</span></label>						  
						<div class="col-md-9">
							<label class="col-form-label"><?php echo $_SESSION["contact"]["company_name"];?></label>
						</div>
					</div>

					<div class="form-group row">
						
						<label class="col-md-3 col-form-label"><?php echo T_('Company Name <br/> (Alphabet)');?><span class="text-danger">*</span> </label>						  
						<div class="col-md-9">
							<label class="col-form-label"><?php echo $_SESSION["contact"]["company_name_alphabet"];?></label>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Responsibility Person');?><span class="text-danger">*</span></label>
						<div class="col-md-9">
							<label class="col-form-label"><?php echo $_SESSION["contact"]["User_Name"];?></label>
						</div>						  				
					</div>

					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Responsibility Person <br/> (Alphabet)');?> <span class="text-danger">*</span></label>
						<div class="col-md-9">
							<label class="col-form-label"><?php echo $_SESSION["contact"]["User_Name_alphabet"];?></label>
						</div>						  				
					</div>

					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Gender');?><span class="text-danger">*</span></label>
						<div class="col-md-5">
							<label class="col-form-label"><?php 
							echo $gender_arr[$_SESSION["contact"]["gender"]];?></label>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Address');?><span class="text-danger">*</span></label>						  
						<fieldset class="col-md-9 form-group">						 
						  <div class="row">
						    <div class="col-sm-12 mb-3">
								<label class="col-form-label"><?php echo $_SESSION["contact"]["prefecture"];?></label>
							</div>
							<div class="col-sm-12 mb-3">
								<label class="col-form-label"><?php echo $_SESSION["contact"]["city"];?></label>
							</div>
							<div class="col-sm-12 mb-3">
								<label class="col-form-label"><?php echo $_SESSION["contact"]["address"];?></label>
							</div>
							<div class="col-sm-12">
								<label class="col-form-label"><?php echo $_SESSION["contact"]["postal_code"];?></label>
							</div>
						  </div>
						</fieldset>									
					</div>

					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Email');?><span class="text-danger">*</span></label>						  
						<div class="col-md-9">
							<label class="col-form-label"><?php echo $_SESSION["contact"]["User_Email"];?></label>
						</div>
													
					</div>
					
					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Phone Number');?><span class="text-danger">*</span></label>						  
						<div class="col-md-9">
							<label class="col-form-label"><?php echo $_SESSION["contact"]["country_code"];?><?php echo $_SESSION["contact"]["User_Phone"];?></label>
						</div>
													
					</div>
					
					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Payment Method');?></label>						  
						<div class="col-md-9">
							<label class="col-form-label"><?php echo $payment_arr[$_SESSION["contact"]["paymentmethod"]];?></label>
						</div>
													
					</div>
					

					<div class="form-group row">
					    <label class="col-md-3 col-form-label"><?php echo T_('Password');?><span class="text-danger">*</span></label>
						<div class="col-md-7">
							<label class="col-form-label"><?php echo $_SESSION["contact"]["User_Password"];?></label>
						</div>
					</div>
					
					<hr>
					<div class="mb-5"></div>

					<div class="form-group text-center">
						<p class="btn-item">
							<button class="btn btn-primary" type="submit" id="submitBtn" name="submit_send"><?php echo T_('Create Account');?></button>
						</p>
						
					</div>
					<div class="form-group">
						<p class="btn-black">
							 <button class="btn-back btn-form hover" type="button" onclick="window.location.href='register.aspx';"><?php echo T_('Back to form');?></button>
						</p>
					</div>
					
			</form>
          </div>
        </div>
      </div>
    </div>

  </div>
<footer class="footer-bottom1">
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
  <script>
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
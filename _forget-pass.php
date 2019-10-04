<?php
if(isset($_GET['lang']) && !empty($_GET['lang'])){
 $_SESSION['language'] = $_GET['lang'];
}

include_once 'modum/class.template.php';

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

$flag_status = 1;
$success_change = false;
if (isset($_POST["forgetpass"])) {  
	   
	  $Username  = $dbf->filter($_POST["Username"]);
	  $query = $dbf->getDynamic("member", "(tendangnhap='" . $Username . "' or email ='" . $Username . "') and is_del=0 ", "");
	  $total = $dbf->totalRows($query);
	  if ($total > 0) 
	  {
		
		$member = $dbf->nextData($query);
		$ma_id = $member["ma_id"];
		$username = $member["tendangnhap"];
		$eth_address = $member["eth_address"];
		$email = $member["email"];
		$pin = $dbf->generate_pin_active();
		$signature_text = $signature;
		$mail_footer_text = $mail_footer;
		$username_text = T_('Username');
		$reset_password_text = T_('Reset Password');
		$dear_text = sprintf(T_('Dear %s'),$email) ;
		$message_body_text = T_('We have received a request to reset your password. If you did not make the request, just ignore this email. Otherwise, you can reset your password using this link:');
		$valid_24h = T_('※ The link will be valid for 24 hours. If you did not make this request, you can just ignore this email.');
      
        /* Get email address
        *****************************/
        /* Get template
        *****************************/
       
		$subject = T_("Vietquoc reset password");
		$header = T_("Please set password");

		$default_admin_email_info = $dbf->getInfoColum("setting",24);
		$admin_email = $default_admin_email_info['value'];

		require("modum/class.phpmailer.php");
		$mail = new PHPMailer();
		$from = $arraySMTPSERVER["user"];
		$fromName = $arraySMTPSERVER["user"];
		  
		$message = Template::get_contents("modum/mail_template/forget_password.tpl", array('logo' => HOST.'images/logo.jpg', 'email' => $email, 'subject' => $header, 'link' => HOST.'change-password.aspx?code='.$pin .'&lang='.$_SESSION['language'], 'ma_id' => $ma_id, 'username' => $username,'signature_text' => $signature_text,'mail_footer_text' => $mail_footer_text,'username_text' => $username_text,'reset_password_text' => $reset_password_text,'message_body_text' => $message_body_text,'valid_24h' => $valid_24h,'dear_text' => $dear_text, 'url' => HOST));
		
		$customer_param = array('EmailFrom'=>$from,'FromName'=>$fromName,'ReplyTo'=>$admin_email,'ReplyName'=>'VietQuocLab','EmailTo'=>$email,'ToName'=>$email,'Subject'=>$subject,'Content'=>$message);
		
		$customer_mail = $dbf->sendmail($customer_param,$mail );

						if ($customer_mail) 
						{
							$array_col = array("token_forget_password"=>$pin);
							$affect = $dbf->updateTable("member", $array_col, "id='" . $member["id"] . "'");
							if($affect > 0)
							{
								$flag_status = 1;
								$success_change = true;
								$msg = T_("A message was send to your email address containing a link to reset your password.");
							}	  
							
						}	  
						else
						{          
						  $flag_status = 0;
						  $msg = T_("Unable to reset your password. Please try again");
						}
			
		
	  }
	  else {
		  
		  $flag_status = 0;
		  $msg = T_("User doesn't exits");
	  }
}
$logo_exists = false;

$getlogo_info = $dbf->getInfoColum("setting",24);
$set_logo = $getlogo_info['value'];
if(file_exists($set_logo)) {
	   $logo_exists = true;
}
?>
<html lang="en">
<head>
  <base href="./">
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
  <meta name="description" content="Rang Su Viet Quoc Group">
  <meta name="author" content="Łukasz Holeczek">
  <meta name="keyword" content="Rang Su, Viet Quoc Group">
  <title><?php echo T_('Forget Password');?></title>
  <!-- Icons-->
  <link href="vendors/@coreui/icons/css/coreui-icons.min.css" rel="stylesheet">
  <link href="vendors/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">
  <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
  <link href="vendors/simple-line-icons/css/simple-line-icons.css" rel="stylesheet">
  <!-- Main styles for this application-->
  <link href="css/coreui/style.css" rel="stylesheet">
  <link href="css/custom/register.css" rel="stylesheet">
 
  
</head>
<body class="app customer-app">
  <div class="container">
      	<div class="customer-bg">
	  <?php 
			if($msg!="")
			{
		?>
			  <div class="alert <?php echo (($flag_status==1)?"alert-success":"alert-warning");?> alert-dismissible fade show" role="alert">
				<strong><?php echo T_($msg);?>
				<button class="close" type="button" data-dismiss="alert" aria-label="Close">
				  <span aria-hidden="true">×</span>
				</button>
			  </div>
			
		<?php							
			}
		?>
		<?php if(!$success_change) { ?>
			<div class="clearfix">
				<div class="col-sm-12 text-center mb-3">
                <img class="navbar-brand-full" src="<?php echo $set_logo;?>" width="200" height="50" alt="Vietquoc Logo">
              </div>
			  <h4 class="pt-3"><?php echo T_('Did you forget your password?');?></h4>
			  <p class="text-muted"><?php echo T_('Provide your email that you used to register. We will send you information on how to reset your password.');?></p>
			</div>
			<form action="" method="post">
			<div class="input-prepend input-group">
			  <div class="input-group-prepend">
				<span class="input-group-text">
				  <i class="fa fa-send"></i>
				</span>
			  </div>
			  <input class="form-control" id="prependedInput" size="16" name="Username" type="text" placeholder="<?php echo T_('Your email');?>">
			  <span class="input-group-append">
				<button class="btn btn-primary" type="submit" name="forgetpass"><?php echo T_('Send');?></button>
			  </span>
			</div>
			</form>
		<?php } ?>
		<div class="text-center">
			<p>
				<?php echo T_('Already have account?');?>
				<?php $page = (isset($_SESSION['is_role']) && $_SESSION['is_role'] == "admin") ? "admin.aspx" : "system.aspx";?>
				<a href="/<?php echo $page;?>"><?php echo T_('Sign In');?></a>
			</p>
		</div>
		</div> 
  </div>
  <footer class="footer-bottom" >
	  <div class="footer-text">
	    ©2019 Rang Su Viet Quoc Group. All Rights Reserved.
	  </div>
</footer>
  <!-- Bootstrap and necessary plugins-->
  <script src="vendors/jquery/js/jquery.min.js"></script>
  <script src="vendors/popper.js/js/popper.min.js"></script>
  <script src="vendors/bootstrap/js/bootstrap.min.js"></script>
  <script src="vendors/perfect-scrollbar/js/perfect-scrollbar.min.js"></script>
  <script src="vendors/@coreui/coreui-pro/js/coreui.min.js"></script>
  <script>
    $('#ui-view').ajaxLoad();
    $(document).ajaxComplete(function() {
      Pace.restart()
    });
  </script>

</body>
</html>
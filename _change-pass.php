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

$code = $dbf->filter($_REQUEST["code"]);
$flag_status = 1;
if (isset ($_REQUEST["code"]) && $code!="")
{
    $query = $dbf->getDynamic("member", "token_forget_password='" . $code . "'", "");
    $total = $dbf->totalRows($query);
	if ($total > 0) 
	{
		$member 	 = $dbf->nextData($query);		
		$ma_id  	 = $member["ma_id"];
		$username 	 = $member["tendangnhap"];
		$email 		 = $member["email"];
		$signature_text = $signature;
		$mail_footer_text = $mail_footer;	
		$dear_text = sprintf(T_('Dear %s'),$email);		
		$username_text = T_('Username');		
		$new_password_text = T_('New password');		
		if (isset($_POST["change_pass"])) 
		{  
	   
	      $new_password  = $dbf->filter($_POST["new_password"]);
	  
      
        /* Get email address
        *****************************/
        /* Get template
        *****************************/       
		
		$subject =T_("Vietquoc change password");
		$header =T_("Password changed");
		$default_admin_email_info = $dbf->getInfoColum("setting",24);
		$admin_email = $default_admin_email_info['value'];

		require("modum/class.phpmailer.php");
		$mail = new PHPMailer();
		$from = $arraySMTPSERVER["user"];
		$fromName = $arraySMTPSERVER["user"];
		  
		$message = Template::get_contents("modum/mail_template/reset_password.tpl", array('logo' => HOST.'images/logo.jpg', 'email' => $email, 'subject' => $header, 'link' => HOST.'change-password.aspx?code='.$pin, 'ma_id' => $ma_id, 'username' => $username,'signature_text' => $signature_text,'mail_footer_text' => $mail_footer_text,'username_text' => $username_text,'new_password_text' => $new_password_text,'new_password' => $new_password,'dear_text' => $dear_text, 'url' => HOST));
		
		$customer_param = array('EmailFrom'=>$from,'FromName'=>$fromName,'ReplyTo'=>$admin_email,'ReplyName'=>'VietQuocLab','EmailTo'=>$email,'ToName'=>$email,'Subject'=>$subject,'Content'=>$message);
		
		$customer_mail = $dbf->sendmail($customer_param,$mail );

			if ($customer_mail) 
				{
					$array_col = array("token_forget_password"=>"","password"=>md5($new_password),"password2"=>md5($new_password),"password3"=>md5($new_password));
					$affect = $dbf->updateTable("member", $array_col, "id='" . $member["id"] . "'");
					if($affect > 0)
					{
						$flag_status = 1;
						$msg = T_("Change password successfull. Please check your mail for information");
					}	  
					
				}	  
				else
				{          
				  $flag_status = 0;
				  $msg = T_("Change your password failure. Please again");
				}
			
		
	  }
		
		
	}else
	{
		$html->redirectURL("/system.aspx");
	}
}
else
{
 $html->redirectURL("/system.aspx");
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
  <title><?php echo T_('Change Password');?></title>
  <!-- Icons-->
  <link href="vendors/@coreui/icons/css/coreui-icons.min.css" rel="stylesheet">
  <link href="vendors/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">
  <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
  <link href="vendors/simple-line-icons/css/simple-line-icons.css" rel="stylesheet">
  <!-- Main styles for this application-->
  <link href="css/coreui/style.css" rel="stylesheet">
  <link href="vendors/pace-progress/css/pace.min.css" rel="stylesheet">
  
</head>

<body class="app flex-row align-items-center">
<div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card-group">
          <div class="card p-4">
		  <?php 
				if($msg!="")
				{
			?>
					
						  <div class="alert alert-success alert-dismissible fade show" role="alert">
							<?php //echo T_("<strong>Success</strong>") . " " . $msg;?>
							<?php echo T_($msg);?>
							<button class="close" type="button" data-dismiss="alert" aria-label="Close">
							  <span aria-hidden="true">×</span>
							</button>
						  </div>
				
			<?php							
				}
			?>
		  <form action="" method="post">
            <div class="card-body">
              <h1><?php echo T_('Change Password');?></h1>
              <p class="text-muted"><?php echo T_('News password');?></p>
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">
                    <i class="icon-lock"></i>
                  </span>
                </div>
                <input class="form-control" type="password" name="new_password" value="<?php echo $password;?>" >
              </div>
				
			  <div class="row">
                <div class="col-6">
                  <button class="btn btn-primary px-4" type="submit" name="change_pass"><?php echo T_('Submit');?></button>
                </div>
                 <div class="text-center">
					<p>
						<?php echo T_('Already have account?');?>
						<a href="/system.aspx"><?php echo T_('Sign In');?></a>
					</p>
				</div> 
              </div>
            </div>
			</form>
          </div>
          <!--<div class="card text-white bg-primary py-5 d-md-down-none" style="width:44%">
            <div class="card-body text-center">
              <div>
                <h2>Sign up</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                <button class="btn btn-primary active mt-3" type="button">Register Now!</button>
              </div>
            </div>
          </div>-->
        </div>
      </div>
    </div>
  </div>
  
	<!-- Bootstrap and necessary plugins-->
  <script src="vendors/jquery/js/jquery.min.js"></script>
  <script src="vendors/popper.js/js/popper.min.js"></script>
  <script src="vendors/bootstrap/js/bootstrap.min.js"></script>
  <script src="vendors/pace-progress/js/pace.min.js"></script>
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
<!-- end document-->
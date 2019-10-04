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

$code = $_REQUEST["code"];
$flag_status = 1;
$page = (isset($_SESSION['is_role']) && $_SESSION['is_role'] == "admin") ? "admin.aspx" : "system.aspx";



if (isset ($_REQUEST["code"]) && $code!="")
{
    $query = $dbf->getDynamic("member", "crf_token_login='" . $code . "'", "");
    $total = $dbf->totalRows($query);
	if ($total > 0) 
	{
		$member = $dbf->nextData($query);
		$array_col = array("crf_token_login"=>'',"active_register"=>1,"status"=>1);
		$affect = $dbf->updateTable("member", $array_col, "id='" . $member["id"] . "'");
		if($affect > 0)
		{
			$flag_status = 1;
			$msg = T_("Your account has been activated successfully. You can now login.");
			$login_text = sprintf(T_('Login <a href="/%s">here</a>'),$page);
			
			//mail
			include_once 'modum/class.template.php';
			require("modum/class.phpmailer.php");
			$mail = new PHPMailer();
			
			$default_admin_email_info = $dbf->getInfoColum("setting",24);
			$admin_email = $default_admin_email_info['value'];
			
			$from = $arraySMTPSERVER["user"];
			$fromName = $arraySMTPSERVER["from"];

			$admin_subject ="［VietQuocLab］新しい顧客が登録されました";
			$admin_header ="新しい顧客が登録されました";
			
			$signature_text = $signature;
			$mail_footer_text = $mail_footer;
			$body_message = T_('Your account has been activated successfully. You can now login.');
			$thankyou = T_('Your account activated');
			$active_text = T_('Login');
			$password = $member["password3"];
			$password_text = T_('Password');
			$email_text = T_('Email');
			$user_email = $member["email"];
			$username_text = T_('Username');
			$username = $member["hovaten"];
			$userid_text = T_('User ID');
			$userid = $member["ma_id"];
			$company_name = $member["company"];
			$customer_subject = T_('Account activated');
			
			$customer_message = Template::get_contents("modum/mail_template/activated.tpl", array('logo' => HOST.'images/logo.jpg', 'thankyou' => $thankyou,'username'=>$username, 'userid'=>$userid, 'userid_text'=>$userid_text, 'password'=>$password, 'password_text'=>$password_text,'username_text'=>$username_text,'email'=>$user_email,'email_text'=>$email_text,'active_text'=>$active_text,'body_message'=>$body_message,'signature_text'=>$signature_text,'mail_footer_text'=>$mail_footer_text, 'url' => HOST));

			$admin_message = Template::get_contents("modum/mail_template/admin_new_register.tpl", array('logo' => HOST.'images/logo.jpg', 'username'=>$username, 'userid'=>$userid, 'admin_subject' => $admin_header, 'company_name' => $company_name,'email'=>$user_email, 'url' => HOST));
			
			$customer_param = array('EmailFrom'=>$from,'FromName'=>$fromName,'ReplyTo'=>$admin_email,'ReplyName'=>'VietQuocLab','EmailTo'=>$user_email,'ToName'=>$username,'Subject'=>$customer_subject,'Content'=>$customer_message);

			$admin_param = array('EmailFrom'=>$from,'FromName'=>$fromName,'ReplyTo'=>$admin_email,'ReplyName'=>'VietQuocLab','EmailTo'=>$admin_email,'ToName'=>'VietquocLab','Subject'=>$admin_subject,'Content'=>$admin_message);
			
			$customer_mail = $dbf->sendmail($customer_param,$mail );
			$admin_mail = $dbf->sendmail($admin_param,$mail );
		}
		
	}else
	{
		$flag_status = 0;
		$msg = T_("For security reasons, when you receive a confirm registration email, you must click on the button/link and take action within 1 day");
		$login_text = sprintf(T_('Back to <a href="/%s">login page</a>'),$page);
	}
}
else
{
 $html->redirectURL("/system.aspx");
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
  <title><?php echo T_('Active');?></title>
  <!-- Icons-->
  <link href="vendors/@coreui/icons/css/coreui-icons.min.css" rel="stylesheet">
  <link href="vendors/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">
  <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
  <link href="vendors/simple-line-icons/css/simple-line-icons.css" rel="stylesheet">
  <!-- Main styles for this application-->
  <link href="css/coreui/style.css" rel="stylesheet">
  <link href="css/custom/register.css" rel="stylesheet">
 
  
</head>
<body class="app">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-7">
      	<div class="customer-bg">
	  <?php 
			if($msg!="")
			{
		?>
				
				  <div class="clearfix">
						<div class="col-sm-12 text-center mb-3">
						<img class="navbar-brand-full" src="<?php echo $set_logo;?>" width="200" height="50" alt="Vietquoc Logo">
					  </div>
					  <h4 class="pt-3"><?php echo ($flag_status==1)? T_("Congratulations"):T_("Your token has expired");?></h4>
					  <p class="text-muted"><?php echo T_($msg);?></p>
					</div>

		<?php							
			}
		?>

		<div class="text-center">
			<p>
				<?php echo $login_text;?>
			</p>
		</div>
		</div>
      </div>
    </div>
  </div>
  <footer class="footer-bottom">
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
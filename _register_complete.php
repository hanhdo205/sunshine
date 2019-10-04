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
<body class="app" style="overflow:hidden;">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="customer-padding card mx-4">
          <div class="card-body p-4">
          	<div class="text-center mb-3">
                <img class="navbar-brand-full" src="<?php echo $set_logo;?>" width="200" height="50" alt="Vietquoc Logo">
              </div>
            <h1 class="text-center"><?php echo T_('THANK YOU FOR Sign-up');?></h1>
			<p><?php echo T_('Thank you for signing up to the VietQuocLab Order management system.
You will receive a confirmation email shortly with your user ID and password.');?></p>
			<p><?php echo T_('Click on Activate Account to verify your account.');?></p>
			<hr>
			<div class="text-center mt-4">
			<p><a href="/system.aspx"><?php echo T_('Back to Home');?></a></p>
		</div>
          </div>
        </div>
      </div>
    </div>

  </div>
<footer>
	<div class="footer-bottom">
	  <div class="footer-text">
	    ©2019 Rang Su Viet Quoc Group. All Rights Reserved.
	  </div>
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
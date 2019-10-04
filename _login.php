<?php

if(isset($_GET['lang']) && !empty($_GET['lang'])){
 $_SESSION['language'] = $_GET['lang'];
}
$_SESSION['is_role'] = 'admin';

$locale = (isset($_SESSION['language']))? $_SESSION['language'] : 'ja_JP';
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

if(isset($_SESSION["member_id"]) && $_SESSION["member_id"]!="")
{
  $html->redirectURL("default.aspx");
}
function is_lang($lang) {
  if($lang == $_SESSION['language'] || $lang == $_POST["lang"]) {
    return true;
  }
  return false;
}

$objCookie = new SC_Cookie();
$username  = $objCookie->getCookie("login_email");
$password  = $objCookie->getCookie("login_password");
$remember  = $objCookie->getCookie("login_remember");

if (isset ($_POST["submitlogin"])) {
/*
  $recaptcha = new \ReCaptcha\ReCaptcha($secret);
  $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
  if ($resp->isSuccess()) 
  {
*/    
  $username = $dbf->filter($_POST["username"]);
  $password = $dbf->filter($_POST["password"]);
  $username = $dbf->escapeStr($username);
  $password = $dbf->escapeStr($password);
  $remember = (int)$_POST["remember"]; 
  //$_SESSION["language"]  = $_POST["lang"];
  unset($_SESSION["language"]);
  unset($_SESSION["contact"]);
  
  $query = $dbf->getDynamic("member", "(tendangnhap='" . $username . "' or ma_id='" . $username . "' or email='" . $username . "') and password='" . md5($password) . "' and is_del=0 and active_register=1 ", "");
  $total = $dbf->totalRows($query); 
  if ($total > 0) {
    if ($total >= 2) {
      $msg = T_('Username ID or duplicate. Please contact Admin');
    }
    else {
      $row = $dbf->nextData($query);
      $_SESSION["member_id"]    = stripslashes($row["id"]);
	  $_SESSION["roles_id"]     = stripslashes($row["roles_id"]);
      $_SESSION["member_email"]   = $row["email"];
      $_SESSION["member_hovaten"]   = stripslashes($row["hovaten"]);
      $_SESSION["Free"] = 0;
      $_SESSION["currentmember"]  = 1;
    
    
    if($row["status"]==1)
    {
    $_SESSION["member_active"] = 1;  
    }else
    {
    $_SESSION["member_active"] = 0;    
    }
    
    if($_POST["remember"]==1)
    {
      $objCookie->setCookie('login_remember', 1);
      $objCookie->setCookie('login_email', $username);
      $objCookie->setCookie('login_password', $_POST["password"]);
    }else
    {
      $objCookie->delCookie('login_remember');
      $objCookie->delCookie('login_email');
      $objCookie->delCookie('login_password'); 
    }   
    
      $html->redirectURL("default.aspx");
    $array_log = array("member_id"=>$_SESSION["member_id"],"name_member"=>$_SESSION["member_hovaten"],"content_log"=>"at " . date("Y-m-d H:i:s"),"type_query"=>"logged in","table_name"=>"member","sqlquery"=>"Logged in with user name & password","datecreated"=>time());
    if($_SESSION["member_id"]!=1)
    $dbf->insertTable_2("history_logs", $array_log);
    }
  }
  else {
    $array_log = array("member_id"=>$_SESSION["member_id"],"name_member"=>$username,"content_log"=>"at " . date("Y-m-d H:i:s"),"type_query"=>"login failed","table_name"=>"member","sqlquery"=>"Login failed for user","datecreated"=>time());
    $dbf->insertTable_2("history_logs", $array_log);
    $msg = __('Acount ID or password wrong. Please try again !');
  }
  
  /*
  }else
  {
      $msg = "<p><strong>Note:</strong> Error code <tt>missing-input-response</tt> may mean the user just didn't complete the reCAPTCHA.</p>";
  }
  */ 
  
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
  <meta name="description" content="CoreUI - Open Source Bootstrap Admin Template">
  <meta name="author" content="Łukasz Holeczek">
  <meta name="keyword" content="Bootstrap,Admin,Template,Open,Source,jQuery,CSS,HTML,RWD,Dashboard">
  <title>Login</title>
  <!-- Icons-->
  <link href="vendors/@coreui/icons/css/coreui-icons.min.css" rel="stylesheet">
  <link href="vendors/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">
  <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
  <link href="vendors/simple-line-icons/css/simple-line-icons.css" rel="stylesheet">
  <!-- Main styles for this application-->
  <link href="css/coreui/style.css" rel="stylesheet">
   <link href="css/custom/login.css" rel="stylesheet">
  <link href="vendors/pace-progress/css/pace.min.css" rel="stylesheet">
  
</head>

<body class="app customer-app">
<div class="container flex-row align-items-center">
    <div class="row justify-content-center">
      <div class="customer-width col-md-5">
	  <form action="" method="post">
        <div class="card-group">
		
          <div class="card p-4">
      <?php 
        if($msg!="")
        {
      ?>

              <div class="alert alert-warning alert-dismissible fade show" role="alert">
              <?php echo '<strong>'. T_('Error').'</strong>' . " " . T_($msg);?>
              <button class="close" type="button" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
              </button>
              </div>
        
      <?php             
        }
      ?>
      
            <div class="card-body">
			<?php if($logo_exists) { ?>
              <div class="col-sm-12 text-center mb-3">
                <img class="navbar-brand-full" src="<?php echo $set_logo;?>" height="50" alt="logo">
              </div>
			  <?php } ?>
              <p class="text-logo sign-in"><?php echo T_('Sign in');?></p>
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">
                    <i class="icon-user"></i>
                  </span>
                </div>
                <input class="form-control input-user-name" type="text" name="username" value="<?php echo $username;?>" placeholder="<?php echo T_('Username');?>">
              </div>
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">
                    <i class="icon-lock"></i>
                  </span>
                </div>
                <input class="form-control input-pasword" type="password" name="password" value="<?php echo $password;?>" placeholder="<?php echo T_('Password');?>">
              </div>
        <div class="form-check mb-3">
          <input type="checkbox" id="remember" class="form-check-input" name="remember"  value="1" <?php echo (($remember==1)?"checked='checked'":"")?>><label for="remember" class="remember-me form-check-label" ><?php echo T_('Remember me?');?></label>
        </div>
        <div class="row">
                <div class="col-6">
                  <button class="sign-in btn btn-primary px-4" type="submit" name="submitlogin"><?php echo T_('Sign in');?></button>
                </div>
                <div class="col-6 nopadding">
          <a href="/forgot-password.aspx" class="forgotten-password btn btn-link px-0"><?php echo T_('Forgot password?');?></a>
                </div>
              </div>
            </div>
      
          </div>

        </div>
         
		</form>
      </div>
    </div>
   
  </div>
  
<footer class="app-footer footer-bottom">
  <div class="footer-text">
    ©2019 CRM. All Rights Reserved.
  </div>
</footer>
  
 
  <!-- Bootstrap and necessary plugins-->
  <script src="vendors/jquery/js/jquery.min.js"></script>
  <script src="vendors/popper.js/js/popper.min.js"></script>
  <script src="vendors/bootstrap/js/bootstrap.min.js"></script>
  <script src="vendors/pace-progress/js/pace.min.js"></script>
  <script src="vendors/perfect-scrollbar/js/perfect-scrollbar.min.js"></script>
  <script src="vendors/@coreui/coreui-pro/js/coreui.min.js"></script>
  <script type="text/javascript">
//<![CDATA[
    $(document).ready(function() {
		
		if ($("input[name='lang']:checked").val()=='ja_JP') {
				$('.input-user-name').attr('placeholder','ユーザー名、またはメールアドレス');
				$('.input-pasword').attr('placeholder','パスワード');
				$('.remember-me').text('パスワードを記録する');
				$('.forgotten-password').text('パスワードをお忘れですか？');

				$('.sign-in').text('サインイン');
				$(".forgotten-password").attr("href", "/forgot-password.aspx?lang=ja_JP");
				$(".sign-up").attr("href", "/register.aspx?lang=ja_JP");
				$('body').addClass('lang-ja');
			} else if ($("input[name='lang']:checked").val()=='vi_VN') {
				$('.input-user-name').attr('placeholder','ID đăng nhập');
				$('.input-pasword').attr('placeholder','Mật khẩu');
				$('.remember-me').text('Lưu thông tin đăng nhập?');
				$('.forgotten-password').text('Quên mật khẩu?');

				$('.sign-in').text('Đăng nhập');
				$(".forgotten-password").attr("href", "/forgot-password.aspx?lang=vi_VN");
				$(".sign-up").attr("href", "/register.aspx?lang=vi_VN");
				$('body').removeClass('lang-ja');
			}

        $('input[type=radio][name=lang]').change(function() {
			if (this.value == 'ja_JP') {
				$('.input-user-name').attr('placeholder','ユーザー名、またはメールアドレス');
				$('.input-pasword').attr('placeholder','パスワード');
				$('.remember-me').text('パスワードを記録する');
				$('.forgotten-password').text('パスワードをお忘れですか？');

				$('.sign-in').text('サインイン');
				$(".forgotten-password").attr("href", "/forgot-password.aspx?lang=ja_JP");
				$(".sign-up").attr("href", "/register.aspx?lang=ja_JP");
				$('body').addClass('lang-ja');
			}
			else if (this.value == 'vi_VN') {
				$('.input-user-name').attr('placeholder','ID đăng nhập');
				$('.input-pasword').attr('placeholder','Mật khẩu');
				$('.remember-me').text('Lưu thông tin đăng nhập?');
				$('.forgotten-password').text('Quên mật khẩu?');

				$('.sign-in').text('Đăng nhập');
				$(".forgotten-password").attr("href", "/forgot-password.aspx?lang=vi_VN");
				$(".sign-up").attr("href", "/register.aspx?lang=vi_VN");
				$('body').removeClass('lang-ja');
			}
			else {
				$('.input-user-name').attr('placeholder','Account ID');
				$('.input-pasword').attr('placeholder','Password');
				$('.remember-me').text('Remember me?');
				$('.forgotten-password').text('Forgot your password?');

				$('.sign-in').text('Sign in');
				$(".forgotten-password").attr("href", "/forgot-password.aspx?lang=en_US");
				$(".sign-up").attr("href", "/register.aspx?lang=en_US");
				$('body').removeClass('lang-ja');
			}
		});

    });
 //]]>
</script>

</body>

</html>
<!-- end document-->
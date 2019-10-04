<?php
session_start();
/*
if(isset($_GET["debug"]))
{
	$_SESSION["debug"] = 1;
}

if(!isset($_SESSION["debug"]))
{
	die("Not access. Please contact Administrator");
}
*/
//error_reporting(E_ALL | E_STRICT);
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(0);

function url() {
  return sprintf(
    "%s://%s%s",
    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
    $_SERVER['SERVER_NAME'],
    dirname($_SERVER['PHP_SELF'])
  );
}

// Set Language variable
// define constants
define('PROJECT_DIR', realpath('./'));
define('LOCALE_DIR', PROJECT_DIR .'/locales');
require_once(PROJECT_DIR . '/phpgettext/gettext.inc');

if(isset($_GET['lang']) && !empty($_GET['lang'])){
	$_SESSION['language'] = $_GET['lang'];

	if(isset($_SESSION['language']) && $_SESSION['language'] != $_GET['lang']){
		echo "<script type='text/javascript'> location.reload(); </script>";
	}
}

if (empty($_SESSION["login"]))
{
    $_SESSION["login"] = session_id();
    $_SESSION["Free"] = 1;
}

date_default_timezone_set('Asia/Bangkok');
include str_replace('\\', '/', dirname(__FILE__)) . '/class/defineConst.php';
include str_replace('\\', '/', dirname(__FILE__)) . '/class/class_cookie.php';
include str_replace('\\', '/', dirname(__FILE__)) . '/class/class.BUSINESSLOGIC.php';
include str_replace('\\', '/', dirname(__FILE__)) . '/class/class.CSS.php';
include str_replace('\\', '/', dirname(__FILE__)) . '/class/class.JAVASCRIPT.php';
include str_replace('\\', '/', dirname(__FILE__)) . '/class/class.HTML.php';
include str_replace('\\', '/', dirname(__FILE__)) . '/class/class.utilities.php';
include str_replace('\\', '/', dirname(__FILE__)) . '/class/class.SINGLETON_MODEL.php';
include str_replace('\\', '/', dirname(__FILE__)) . '/class/comments.class.php';

$dbf = SINGLETON_MODEL::getInstance("BUSINESSLOGIC");
$html = SINGLETON_MODEL::getInstance("HTML");
$css = SINGLETON_MODEL::getInstance("CSS");
$js = SINGLETON_MODEL::getInstance("JAVASCRIPT");
$utl = SINGLETON_MODEL::getInstance("UTILITIES");
//$cmnt = SINGLETON_MODEL::getInstance("Comments_System");
$info = $dbf->getConfig();

$rowgetInfo = $dbf->getInfoColum("member",$_SESSION["member_id"]);
//$rowgetActual = $dbf->getActualColum("actual_sales",$_SESSION["member_id"]);

/*	
if($page!="active-member" && $page!="network-tree" && $page!="referrals" && $page!="account_update_info" && $page!="deposit" && $page!="verify-account" && $page!="investment" && $page!="account_change_password")
{
	if($rowgetInfo["status"]==0)
	{			
		$html->redirectURL("/investment.aspx");
		exit();
	}
}
*/ 


$supported_locales = array('ja_JP');
$encoding = 'UTF-8';

//$locale = (isset($_SESSION['language']))? $_SESSION['language'] : DEFAULT_LOCALE;
$locale = (isset($_SESSION['language']))? $_SESSION['language'] : $rowgetInfo["language"];
if(!isset($_SESSION['language'])) {
	$_SESSION['language'] = $rowgetInfo["language"];
}
//$locale = ($rowgetInfo["language"])? $rowgetInfo["language"] : $_SESSION['language'];

// gettext setup
T_setlocale(LC_MESSAGES, $locale);
// Set the text domain
$domain = $locale;
T_bindtextdomain($domain, LOCALE_DIR);
T_bind_textdomain_codeset($domain, $encoding);
T_textdomain($domain);

header("Content-type: text/html; charset=$encoding");
$lang = array(
		'en_US' => 'us',
		'vi_VN' => 'vn',
		//'ja_JP' => 'jp',
	);
$summernote = array(
		'en_US' => 'en-US',
		'vi_VN' => 'vi-VN',
		//'ja_JP' => 'ja-JP',
	);
$editor_input = array(
		'en_US' => 'us',
		'vi_VN' => 'vn',
		//'ja_JP' => 'jp',
);

$datepicker_lang = array(
		'en_US' => 'en',
		'vi_VN' => 'vi',
		//'ja_JP' => 'ja',
	);
$lang_text = array(
		'en_US' => T_('English'),
		'vi_VN' => T_('Tiếng Việt'),
		//'ja_JP' => T_('日本語'),
	);
	//日本語
$datatable = array(
		'en_US' => 'English',
		'vi_VN' => 'Vietnamese',
		//'ja_JP' => 'Japanese',
	);
	
$teeth_text = sprintf( T_('%d teeths'),2);
$year_old_text = sprintf( T_('%d years old'),2);
	
$gender = array('male'=>T_('Male'),'female'=>T_('Female'));
$publish = array(
	'1' => T_('Publish'),
	'0' => T_('Pending'),
);
$product_item = array('cad'=>T_('CAD Design(Data Only)'),'zirconia'=>T_('Fullzirconia'),'3d'=>T_('3D Printer Model'));
$delivery_text = array('normal'=>T_('Normal'),'urgent'=>T_('Urgent'));

$signature = T_('We look forward to your continued support for us.<br>Contact us<br><a style="color:#5b9bd5" href="mailto:info@vql.jp">info@vql.jp</a></font><br><br>');
$mail_footer = T_('Website URL: ');

	define('DEFAULT_LOCALE', 'vi_VN');
	$lang = array(
			'en_US' => 'us',
			'vi_VN' => 'vn',
			//'ja_JP' => 'jp',
		);
	


switch($page) {
	case ($page=="home" || $page=="system"):
		include("_login.php");
		break;
	case ($page=="admin"):
		include("_logon.php");
		break;
	case ($page=="register"):
		include("_register.php");
		break;
	case ($page=="register-confirm"):
		include("_register_confirm.php");
		break;
	case ($page=="register-complete"):
		include("_register_complete.php");
		break;
	case ($page=="forgot-password"):
		include("_forget-pass.php");
		break;
	case ($page=="active-member"):
		include("_active_register.php");
		break;
	case ($page=="change-password"):
		include("_change-pass.php");
		break;
	default:
?>

		<!DOCTYPE html>
		<html lang="en">

		<head>
			<!-- Required meta tags-->
			<meta charset="UTF-8">
			<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">   
		<?php
			
			echo "<base href='" . HOST . "' />";
			if (empty($_SESSION["login"]))
			{
				$_SESSION["login"] = session_id();
				$_SESSION["Free"] = 1;
			}
				if ($URL[0] == md5("signout" . date("dmH")))
				$dbf->signout();
		?>
		<?php    

			$infonguoibaotro    = $dbf->getInfoColum("member",$rowgetInfo["parentid"]);	
			if($rowgetInfo["country_id"])
			{
				 $info_country       = $dbf->getInfoColum("countries",$rowgetInfo["country_id"]);
			}
			
			// This code is use for delete order's uploaded files after x time
			 $path = "upload/orders/";
				  if ($handle = opendir($path)) {

					while (false !== ($file = readdir($handle))) {
						if ((time()-filectime($path.'/'.$file)) > 7776000) {  // 7776000 = 60*60*24*90
							echo time() . '<br>'.filectime($path.'/'.$file);
								unlink($path.'/'.$file);
						}
					}
				  }

		?>
		<!-- Title Page-->
		<title>
			<?php
			echo $info['title'];
			?>
		</title>
		<link rel="shortcut icon" href="<?php echo HOST?>favicon.png" />

		<!-- Fontfaces CSS-->
		<link href="vendors/@coreui/icons/css/coreui-icons.min.css" rel="stylesheet">
		<link href="vendors/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">
		<link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
		<link href="vendors/simple-line-icons/css/simple-line-icons.css" rel="stylesheet">

		<link href="css/custom/style.css" rel="stylesheet">
		<?php if($rowgetInfo["roles_id"]==15) {
		  echo '<link href="css/custom/member.css" rel="stylesheet">';
		}
		?>
		<link href="css/coreui/style.css" rel="stylesheet">
		<link href="vendors/pace-progress/css/pace.min.css" rel="stylesheet">


		<script src="vendors/jquery/js/jquery.min.js"></script>
		<!--<script src="js/custom/notice.js"></script>-->
		<script>
		function goBack(param) {
		  window.history.go(param);
		}
		</script>
		</head>

		<body class=" app header-fixed sidebar-fixed aside-menu-fixed sidebar-lg-show">
			
					<?php
						include ("_header_member.php");
					?> 
					<!-- MAIN CONTENT-->
					<div class="app-body body-padding">
						<!-- SIDE-BAR CONTENT-->
						<?php
							include ("side-bar.php");
						?>
						<!-- END SIDE-BAR CONTENT-->
						
						<?php
							include ("modum/bodymain.php");
						?>
						<!-- END MAIN CONTENT-->
						
						<!-- ASIDE CONTENT-->
						<?php if($rowgetInfo["roles_id"]<=7)
							include ("aside.php");
						?>
						<!-- END ASIDE CONTENT-->
					</div> <!-- .app-body-->
			<script type="text/javascript">
				$(document).ready(function(){
					$('.icon-bar').on('click', function(){
						$('.page-wrapper').toggleClass('minimize');
					})
									
				})
			</script>
			<?php if($rowgetInfo["roles_id"]==51) { ?>
			<footer class="custome-body">
			  <div class="footer-text">
			   <!-- <div class="txt-left">
				  <a href="http://www..com/">Viet Quoc Lab</a>
				  <span>© 2019 all right reserved</span>
				</div>
				<div class="txt-right">
				  <span>Powered by</span>
				  <a href="https://globee.asia/">globee</a>
				</div> -->
				<!--<div><!--©2019 Rang Su Viet Quoc Group. All Rights Reserved.--></div>-->
					
			  </div>
			</footer>
			<?php } else { ?>
			
			<?php } ?>
			<!-- Bootstrap and necessary plugins-->
		  
		  <script src="vendors/popper.js/js/popper.min.js"></script>
		  <script src="vendors/bootstrap/js/bootstrap.min.js"></script>
		  <script src="vendors/pace-progress/js/pace.min.js"></script>
		  <script src="vendors/perfect-scrollbar/js/perfect-scrollbar.min.js"></script>
		  <script src="vendors/@coreui/coreui-pro/js/coreui.min.js"></script>
		  
		</body>
		</html>
		<!-- end document-->

	<?php 
	break;
	} ?>
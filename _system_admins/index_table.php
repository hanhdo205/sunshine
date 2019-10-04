<?php
date_default_timezone_set('Asia/Bangkok');
error_reporting(1);
session_start();
if (empty ($_SESSION["user_login"])) {
  unset($_SESSION["user_login"]);
  echo "<script>window.location.href='login.php'</script>";
  exit;
}
include str_replace('\\', '/', dirname(__FILE__)) . '/content_spaw/spaw.inc.php';
include str_replace('\\', '/', dirname(__FILE__)) . '/class/class.DEFINE.php';
include str_replace('\\', '/', dirname(__FILE__)) . '/class/class.HTML.php';
include str_replace('\\', '/', dirname(__FILE__)) . '/class/class.JAVASCRIPT.php';
include str_replace('\\', '/', dirname(__FILE__)) . '/class/class.UTILITIES.php';
include str_replace('\\', '/', dirname(__FILE__)) . '/class/class.CSS.php';
include str_replace('\\', '/', dirname(__FILE__)) . '/class/class.SINGLETON_MODEL.php';
include str_replace('\\', '/', dirname(__FILE__)) . '/class/simple_html_dom.php';
include str_replace('\\', '/', dirname(__FILE__)) . '/class/class.BUSINESSLOGIC.php';
include str_replace('\\', '/', dirname(__FILE__)) . '/class/template.php';
include str_replace('\\', '/', dirname(__FILE__)) . '/Cache_Lite/Lite/Function.php';
$html = SINGLETON_MODEL :: getInstance("HTML");
$js = SINGLETON_MODEL :: getInstance("JAVASCRIPT");
$css = SINGLETON_MODEL :: getInstance("CSS");
$utl = SINGLETON_MODEL :: getInstance("UTILITIES");
$dbf = SINGLETON_MODEL :: getInstance("BUSINESSLOGIC");
$html->docType();
$CONFIG = $dbf->loadSetting();
if ($_GET['table_name'])
  $_SESSION['table_name'] = $_GET['table_name'];
if ($_SESSION['table_name'] != '') {
  $arrayTitle = $dbf->getArray("sys_table", "table_name='" . $_SESSION['table_name'] . "'", "", "stdObject");
  if (is_array($arrayTitle))
    foreach ($arrayTitle as $value) {
      $arrayHeader["Page"] = stripslashes($value->title);
      $arrayHeader["title"] = str_replace("{PAGE_NAME}", $utl->stripUnicode($arrayHeader["Page"]), $arrayHeader["title"]);
      $arrayHeader["title"] = str_replace("{SESSION_USER}", $utl->stripUnicode($_SESSION['user_login']['fullname']), $arrayHeader["title"]);
  }
}
$options = array('cacheDir' => 'tmp/', 'lifeTime' => 3600);
$cache = new Cache_Lite_Function($options);
$html->openHTML();
$html->openHead();
?>
<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">



<?php
$arrayHeader["icon"]= "";
$html->displayHead($arrayHeader["utf8"], $arrayHeader["refresh"], $arrayHeader["title"], $arrayHeader["keywords"], $arrayHeader["description"], $arrayHeader["copyright"], $arrayHeader["icon"]);
$arrayCSS = array("css/bootstrap.min.css","css/font-awesome.min.css",pathTheme . "/style/style.pack.css", "js/jquery-ui/jquery-ui.css", "style/jquery.Slidemenu.css","style/jquery-customselect-1.9.1.css");
foreach ($arrayCSS as $value)
  $css->linkCSS($value);
$html->closeHead();
$html->openBody(array("div" => "divSwap"));
$arrayJS = array("js/jquery-1.9.1.js", "js/jquery.validate.js", "js/adminLib.js", "js/jquery-ui/jquery-ui.js", "js/jquery.Slidemenu.js", "js/jquery.query.js", "js/jquery-customselect-1.9.1.js");
foreach ($arrayJS as $value)
  $js->linkJS($value);
$html->normalForm("frm", array("action" => "", "method" => "post"));
$dbf->showAdminHome($CURRENT_PAGE);
$dbf->huy($CURRENT_PAGE . "?" . QUERY_STRING);
?>
<?php
session_start();
error_reporting(0);
include ('../../class/defineConst.php');
include ('../../class/class.BUSINESSLOGIC.php');
include '../../class/class.utilities.php';
include ('../../class/class.SINGLETON_MODEL.php');

$utl = SINGLETON_MODEL::getInstance("UTILITIES");
$dbf = new BUSINESSLOGIC();

// define constants
define('PROJECT_DIR', realpath('../../'));
define('LOCALE_DIR', PROJECT_DIR .'/locales');
define('DEFAULT_LOCALE', 'en_US');

include '../../phpgettext/gettext.inc';
	
$rowgetInfo = $dbf->getInfoColum("member",$_SESSION["member_id"]);
	$supported_locales = array('ja_JP', 'vi_VN');
	$encoding = 'UTF-8';

	//$locale = (isset($_SESSION['language']))? $_SESSION['language'] : DEFAULT_LOCALE;
	$locale = (isset($_SESSION['language']))? $_SESSION['language'] : $rowgetInfo["language"];
	//$locale = ($rowgetInfo["language"])? $rowgetInfo["language"] : DEFAULT_LOCALE;

	// gettext setup
	T_setlocale(LC_MESSAGES, $locale);
	// Set the text domain
	$domain = $locale;
	T_bindtextdomain($domain, LOCALE_DIR);
	T_bind_textdomain_codeset($domain, $encoding);
	T_textdomain($domain);

	header("Content-type: text/html; charset=$encoding");

$data = array();

$students = $dbf->getDynamic("member", "roles_id=15", "");
if ($dbf->totalRows($students) > 0) {
	 while ($s_row = $dbf->nextData($students)) {
		$birthday_reminder_date = $utl->get_next_reminder_date($s_row["date_ngaysinh"], 12);
		if (($birthday_reminder = strtotime($birthday_reminder_date)) !== false) {
			$date=date_create($birthday_reminder_date);
			$brithdate = date_format($date,"Y-m-d");
			$data[] = array(
			'id'   => $s_row["id"],
			'title'   => sprintf(T_("%s's Birthday"),$s_row["last_name"]),
			'description' => $s_row["first_name"] . ' - ' . $s_row["date_ngaysinh"],
			'start'   => $brithdate . ' 08:00:00',
			'end'   => $brithdate . ' 17:00:00',
			'all_day' => '',
			'pic' => $s_row["profile_img"],
			'src' => 'birthday'
			);
		}
	 }
}

echo json_encode($data);
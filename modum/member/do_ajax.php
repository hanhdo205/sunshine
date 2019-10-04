<?php
include '../../class/comments.class.php';
include '../../class/class.BUSINESSLOGIC.php';
include '../../class/class.utilities.php';
include '../../class/class.SINGLETON_MODEL.php';
date_default_timezone_set('Asia/Ho_Chi_Minh');
error_reporting(E_ERROR | E_WARNING | E_PARSE);
//error_reporting(0);

// define constants
define('PROJECT_DIR', realpath('../../'));
define('LOCALE_DIR', PROJECT_DIR .'/locales');
define('DEFAULT_LOCALE', 'en_US');

include '../../phpgettext/gettext.inc';
	
$utl = SINGLETON_MODEL::getInstance("UTILITIES");
$dbf_cmnt = new BUSINESSLOGIC();
$rowgetInfo = $dbf_cmnt->getInfoColum("member",$_SESSION["member_id"]);
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
	
if(isset($_SESSION['language']) && $_SESSION['language']=='ja_JP') {
	$date_format = "Y/m/d H:i";
	$short_date_format = "Y/m/d";
} else {
	$date_format = "d/m/Y H:i";
	$short_date_format = "d/m/Y";
}

$status_search = array('1'=>T_('Paid'),'2'=>T_('Unpaid'),'3'=>T_('Partial'));

// ajax on refresh password
if (isset($_REQUEST['password'])) { // ajax on refresh password
	$response = array("status"=>0,"data"=>"");
	$password = $_REQUEST['password'];
	
	$my_password = $utl->randomPassword(10,1,"lower_case,upper_case,numbers,special_symbols");
	
	if($my_password[0]) {
		$response["status"]=1;
		$response["data"]=$my_password[0];
	}
		
	header("Content-type:application/json"); 
	echo json_encode($response);
	// IMPORTANT: don't forget to "exit"
	die();
}

// Upload file in summernote editor
if (isset($_POST['action']) && $_POST['action']=='summernote_upload_file') {
	if (isset($_FILES['file']) && ($_FILES['file']['name'])) {
		$response = array("status"=>0,"data"=>"");
		if (!$_FILES['file']['error']) {
			$name = md5(rand(100, 200));
			$ext = explode('.', $_FILES['file']['name']);
			$file_type = $_FILES['file']['type'];
			$allowed = array("image/jpeg", "image/gif", "image/png");
			if(!in_array($file_type, $allowed)) {
				$response["status"]=0;
				$response["data"]='Only jpg, gif, and png files are allowed.';
				header("Content-type:application/json"); 
				echo json_encode($response);
				// IMPORTANT: don't forget to "exit"
				die();
			}
			$filename = $name . '.' . $ext[1];
			$destination = '../../upload/post/' . $filename; //change this directory
			$location = $_FILES["file"]["tmp_name"];
			move_uploaded_file($location, $destination);
				$response["status"]=1;
				$response["data"]=HOST . 'upload/post/' . $filename;
		}
		else
		{
			$response["status"]=0;
			$response["data"]='Ooops!  Your upload triggered the following error:  '.$_FILES['file']['error'];
		}
		header("Content-type:application/json"); 
		echo json_encode($response);
		// IMPORTANT: don't forget to "exit"
		die();
	}
}

// Delete file in summernote editor 
if (isset($_POST['action']) && $_POST['action']=='summernote_delete_file') { // ajax on remove post image
	$src = $_POST['src'];
	$file_name = str_replace(HOST . 'upload/post/', '../../upload/post/', $src); // striping host to get relative path
	
        if(unlink($file_name))
        {
            echo 'File Delete Successfully';
        }
}

// DataTable Member List
if (isset($_POST['action']) && $_POST['action']=='member_list') {
	$search = '';
	$order_array = array();
	//printf("<pre>%s</pre>",print_r($_POST,true));
	$start_date = strtotime($_POST['start_date']);
	$end_date = strtotime($_POST['end_date']);
	
	$columns = array( 
		0 =>'ma_id', 
		1 => 'company',
		2 => 'first_name',
		3 => 'last_name',
		4 => 'responsible_person',
		5 => 'tuition_fee',
		6 => 'meals_fee',
		7 => 'tools_fee',
		/*8 => 'order_amount',
		9 => 'paid_amount'*/
	);
	$order_by = 'tb1.id DESC';
	if($_POST["date_search_type"] == "update") {
		$order_by = 'tb1.dateupdated DESC, tb1.datecreated DESC';
	}
	$order_sort = '';
	$limit = '';
		
	if(isset($_POST["order"])){
		$order_by = $columns[$_POST['order'][0]['column']] . " " . $_POST['order'][0]['dir'];
	}
	if($_POST["length"] != -1){
	 $limit = $_POST['start'] . ', ' . $_POST['length'];
	}
	
	if(!empty($_POST["search"])){
		$search.=" AND ( tb1.ma_id LIKE '%".$_POST['search']."%' "; 
		$search.=" OR tb1.first_name LIKE '%".$_POST['search']."%' ";
		$search.=" OR tb1.last_name LIKE '%".$_POST['search']."%' ";
		$search.=" OR tb1.company LIKE '%".$_POST['search']."%' )";
	}
	if(isset($_POST["person"]) && $_POST["person"] !=''){
		$search.=" AND tb1.responsible_person = ".$_POST['person']."";
	}

	$totalData = $dbf_cmnt->totalRows($dbf_cmnt->getDynamic("member", "roles_id=15", ""));

	if($_POST["date_search_type"] == "no") {
		
		$sql = $dbf_cmnt->inner3joinDynamic("tb1.id as id,tb1.ma_id,tb1.first_name,tb1.last_name,tb1.company,tb1.reg_date,tb1.dateupdated,tb1.responsible_person,tb1.tuition_fee,tb1.meals_fee,tb1.tools_fee,tb2.member_id,COUNT(DISTINCT tb2.dateupdated) as order_item,SUM(tb2.price)*COUNT(DISTINCT  tb2.dateupdated)/COUNT(*) as order_amount,COUNT(DISTINCT tb3.dateupdated) as paid_item,SUM(tb3.price)*COUNT(DISTINCT  tb3.dateupdated)/COUNT(*) as paid_amount","member","history_sales","history_payment","tb1.id = tb2.member_id","tb2.member_id = tb3.member_id","tb1.roles_id=15 $search","tb1.id",$order_by,$limit);
		
		$totalFiltered = $dbf_cmnt->totalRows($dbf_cmnt->count3joinDynamic("member","history_sales","history_payment","tb1.id = tb2.member_id","tb2.member_id = tb3.member_id","tb1.roles_id=15 $search","tb1.id",$order_by,""));

		if($dbf_cmnt->totalRows($sql) > 0) {
			
			while( $row = $dbf_cmnt->nextData($sql)){
				//printf("<pre>%s</pre>",print_r($row,true));
				$responsible_person = '';
				$info_person = $dbf_cmnt->getInfoColum("responsible_person",$row['responsible_person']);
				if($info_person) {
					$responsible_person   =  $info_person["first_name"].$info_person["last_name"];
				}
				$order_array[] = array(
						"ma_id"=>$row['ma_id'],
						"company"=>$row['company'],
						"first_name"=>$row['first_name'],
						"last_name"=>$row['last_name'],
						"responsible_person"=>$responsible_person,
						"reg_date"=>date($short_date_format,$row['reg_date']),
						"tuition_fee"=>$row['tuition_fee'],
						"meals_fee"=>$row['meals_fee'],
						"tools_fee"=>$row['tools_fee'],
						"order_amount"=>number_format($row["order_amount"],0),
						"paid_amount"=>number_format($row["paid_amount"],0),
						"add_sales"=>'<button type="button" class="btn btn-neo mb-1 add_sales" data-id="'.$row['id'].'" data-sales="'.$row['order_item'].'" data-paid="'.$row['paid_item'].'"><i class="fa fa-plus" aria-hidden="true"></i> '.T_('Add').'</button>',
						"update"=>'<a href="member-edit/?id='.$row['id'].'" data-id="'.$row['id'].'" data-action="edit_member" class="responsible_action btn btn-effect-ripple btn-xs btn-secondary mr-1" data-toggle="tooltip" title="" style="overflow: hidden; position: relative;" data-original-title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a><a data-id="'.$row['id'].'" data-action="delete_user" class="delete_user_from_list text-white btn btn-effect-ripple btn-xs btn-danger" data-toggle="tooltip" title="" style="overflow: hidden; position: relative;" data-original-title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>',
				);
			}
		}
	}
	
	if($_POST["date_search_type"] == "update") {
				
		$sql = $dbf_cmnt->inner3joinDynamic("tb1.id as id,tb1.ma_id,tb1.first_name,tb1.last_name,tb1.company,tb1.reg_date,tb1.dateupdated,tb1.responsible_person,tb1.tuition_fee,tb1.meals_fee,tb1.tools_fee,tb2.member_id,COUNT(DISTINCT tb2.dateupdated) as order_item,SUM(tb2.price)*COUNT(DISTINCT  tb2.dateupdated)/COUNT(*) as order_amount,COUNT(DISTINCT tb3.dateupdated) as paid_item,SUM(tb3.price)*COUNT(DISTINCT  tb3.dateupdated)/COUNT(*) as paid_amount","member","history_sales","history_payment","tb1.id = tb2.member_id","tb2.member_id = tb3.member_id","roles_id=15 AND tb1.dateupdated BETWEEN $start_date AND $end_date $search","tb1.id",$order_by,$limit);
		
		$totalFiltered = $dbf_cmnt->totalRows($dbf_cmnt->count3joinDynamic("member","history_sales","history_payment","tb1.id = tb2.member_id","tb2.member_id = tb3.member_id","tb1.roles_id=15 AND tb1.dateupdated BETWEEN $start_date AND $end_date $search","tb1.id",$order_by,""));

		if($dbf_cmnt->totalRows($sql) > 0) {
			
			while( $row = $dbf_cmnt->nextData($sql)){
				//printf("<pre>%s</pre>",print_r($row,true));
				$responsible_person = '';
				$info_person = $dbf_cmnt->getInfoColum("responsible_person",$row['responsible_person']);
				if($info_person) {
					$responsible_person   =  $info_person["first_name"].$info_person["last_name"];
				}
				$order_array[] = array(
						"ma_id"=>$row['ma_id'],
						"company"=>$row['company'],
						"first_name"=>$row['first_name'],
						"last_name"=>$row['last_name'],
						"responsible_person"=>$responsible_person,
						"reg_date"=>date($short_date_format,$row['reg_date']),
						"tuition_fee"=>$row['tuition_fee'],
						"meals_fee"=>$row['meals_fee'],
						"tools_fee"=>$row['tools_fee'],
						"order_amount"=>number_format($row["order_amount"],0),
						"paid_amount"=>number_format($row["paid_amount"],0),
						"add_sales"=>'<button type="button" class="btn btn-neo mb-1 add_sales" data-id="'.$row['id'].'" data-sales="'.$row['order_item'].'" data-paid="'.$row['paid_item'].'"><i class="fa fa-plus" aria-hidden="true"></i> '.T_('Add').'</button>',
						"update"=>'<a href="member-edit/?id='.$row['id'].'" data-id="'.$row['id'].'" data-action="edit_member" class="responsible_action btn btn-effect-ripple btn-xs btn-secondary mr-1" data-toggle="tooltip" title="" style="overflow: hidden; position: relative;" data-original-title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a><a data-id="'.$row['id'].'" data-action="delete_user" class="delete_user_from_list text-white btn btn-effect-ripple btn-xs btn-danger" data-toggle="tooltip" title="" style="overflow: hidden; position: relative;" data-original-title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>',
				);
			}
		}
	}
	
	if($_POST["date_search_type"] == "regdate") {
				
		$sql = $dbf_cmnt->inner3joinDynamic("tb1.id as id,tb1.ma_id,tb1.first_name,tb1.last_name,tb1.company,tb1.reg_date,tb1.dateupdated,tb1.responsible_person,tb1.tuition_fee,tb1.meals_fee,tb1.tools_fee,tb2.member_id,COUNT(DISTINCT tb2.dateupdated) as order_item,SUM(tb2.price)*COUNT(DISTINCT  tb2.dateupdated)/COUNT(*) as order_amount,COUNT(DISTINCT tb3.dateupdated) as paid_item,SUM(tb3.price)*COUNT(DISTINCT  tb3.dateupdated)/COUNT(*) as paid_amount","member","history_sales","history_payment","tb1.id = tb2.member_id","tb2.member_id = tb3.member_id","tb1.roles_id=15 AND tb1.reg_date BETWEEN $start_date AND $end_date $search","tb1.id",$order_by,$limit);
		
		$totalFiltered = $dbf_cmnt->totalRows($dbf_cmnt->count3joinDynamic("member","history_sales","history_payment","tb1.id = tb2.member_id","tb2.member_id = tb3.member_id","tb1.roles_id=15 AND tb1.reg_date BETWEEN $start_date AND $end_date $search","tb1.id",$order_by,""));
		
		if($dbf_cmnt->totalRows($sql) > 0) {
			
			while( $row = $dbf_cmnt->nextData($sql)){
				//printf("<pre>%s</pre>",print_r($row,true));
				$responsible_person = '';
				$info_person = $dbf_cmnt->getInfoColum("responsible_person",$row['responsible_person']);
				if($info_person) {
					$responsible_person   =  $info_person["first_name"].$info_person["last_name"];
				}
				$order_array[] = array(
						"ma_id"=>$row['ma_id'],
						"first_name"=>$row['first_name'],
						"last_name"=>$row['last_name'],
						"company"=>$row['company'],
						"responsible_person"=>$responsible_person,
						"reg_date"=>date($short_date_format,$row['reg_date']),
						"tuition_fee"=>$row['tuition_fee'],
						"meals_fee"=>$row['meals_fee'],
						"tools_fee"=>$row['tools_fee'],
						"order_amount"=>number_format($row["order_amount"],0),
						"paid_amount"=>number_format($row["paid_amount"],0),
						"add_sales"=>'<button type="button" class="btn btn-neo mb-1 add_sales" data-id="'.$row['id'].'" data-sales="'.$row['order_item'].'" data-paid="'.$row['paid_item'].'"><i class="fa fa-plus" aria-hidden="true"></i> '.T_('Add').'</button>',
						"update"=>'<a href="member-edit/?id='.$row['id'].'" data-id="'.$row['id'].'" data-action="edit_member" class="responsible_action btn btn-effect-ripple btn-xs btn-secondary mr-1" data-toggle="tooltip" title="" style="overflow: hidden; position: relative;" data-original-title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a><a data-id="'.$row['id'].'" data-action="delete_user" class="delete_user_from_list text-white btn btn-effect-ripple btn-xs btn-danger" data-toggle="tooltip" title="" style="overflow: hidden; position: relative;" data-original-title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>',
				);
			}
		}
	}
	
	$data = array();
	//printf("<pre>%s</pre>",print_r($order_array,true));
	foreach($order_array as $key=>$value) {
		$sub_array = [
			$value['ma_id'],
			$value['first_name'],
			$value['last_name'],
			$value['company'],
			$value['responsible_person'],
			$value['reg_date'],
			$value['tuition_fee'],
			$value['meals_fee'],
			$value['tools_fee'],
			/*$value['order_amount'],
			$value['paid_amount'],
			$value['add_sales'],*/
			$value['update']
		];
		
		$data[] = $sub_array;
		
	}
	
	$json_data = array(
	 "draw"    => intval($_POST["draw"]),
	 "recordsTotal"  =>  $totalData,
	 "recordsFiltered" => $totalFiltered,
	 "data"    => $data
	);

	echo json_encode($json_data);
	//die();
}

// CSV Member List
if (isset($_POST['action']) && $_POST['action']=='csv_member_download') {
	ob_start();	
	$id = $_SESSION["member_id"];
	$start_date = strtotime($_POST['start_date']);
	$end_date = strtotime($_POST['end_date']);

	$search = '';
	$status_text = '';

	$header_row = [
        T_('Member ID'),
        T_('Student name'),
        T_('Nickname'),
        T_('Company name'),
        T_('Responsible person'),
        T_('Registration date'),
        T_('Tuition'),
        T_('Meals'),
        T_('Tools'),
       /* T_('Amount'),
        T_('Paid')*/
    ];

	if(!empty($_POST["search"])){
		$search.=" AND ( tb1.ma_id LIKE '%".$_POST['search']."%' "; 
		$search.=" OR tb1.first_name LIKE '%".$_POST['search']."%' ";
		$search.=" OR tb1.last_name LIKE '%".$_POST['search']."%' ";
		$search.=" OR tb1.company LIKE '%".$_POST['search']."%' )";
		$status_text .= ' and keyword is ' . $_POST['search'];
	}
	if(isset($_POST["person"]) && $_POST["person"] !=''){
		$search.=" AND tb1.responsible_person = ".$_POST['person']."";
		$search_person = $dbf_cmnt->getInfoColum("responsible_person",$row['responsible_person']);
			if($search_person) {
				$person   =  $search_person["first_name"].$search_person["last_name"];
			}
		$status_text .= ' and responsible_person is ' . $person;
	}

	$f = fopen('php://temp', "r+");
	if($_POST["date_search_type"] == "update") {
		$title_row = [sprintf(T_('Member list filter by update from %s to %s %s'),$_POST['start_date'],$_POST['end_date'],$status_text)];
		fputcsv($f, $title_row, ',' , '"');
	}
	else {
		$title_row = [sprintf(T_('Member list filter by registration date from %s to %s %s'),$_POST['start_date'],$_POST['end_date'],$status_text)];
		fputcsv($f, $title_row, ',' , '"');
	}
    fputcsv($f, $header_row, ',' , '"');

    if (!$f) {
        echo 'error';
        exit;
    } 
	
	$sql = $dbf_cmnt->inner3joinDynamic("tb1.id as id,tb1.ma_id,tb1.first_name,tb1.last_name,tb1.company,tb1.reg_date,tb1.dateupdated,tb1.responsible_person,tb1.tuition_fee,tb1.meals_fee,tb1.tools_fee,tb2.member_id,COUNT(DISTINCT tb2.dateupdated) as order_item,SUM(tb2.price)*COUNT(DISTINCT  tb2.dateupdated)/COUNT(*) as order_amount,COUNT(DISTINCT tb3.dateupdated) as paid_item,SUM(tb3.price)*COUNT(DISTINCT  tb3.dateupdated)/COUNT(*) as paid_amount","member","history_sales","history_payment","tb1.id = tb2.member_id","tb2.member_id = tb3.member_id","tb1.roles_id=15 AND $search","tb1.id",$order_by,$limit);
	
	$totalFiltered = $dbf_cmnt->totalRows($dbf_cmnt->count3joinDynamic("member","history_sales","history_payment","tb1.id = tb2.member_id","tb1.id = tb3.member_id","tb1.roles_id=15 AND $search","tb1.id",$order_by,""));
	
	$totalData = $dbf_cmnt->totalRows($dbf_cmnt->getDynamic("member", "1=1", ""));
	
	if($dbf_cmnt->totalRows($sql) > 0) {
		$order_array = array();
		while( $row = $dbf_cmnt->nextData($sql)){
			//printf("<pre>%s</pre>",print_r($row,true));
			$responsible_person = '';
			$info_person = $dbf_cmnt->getInfoColum("responsible_person",$row['responsible_person']);
			if($info_person) {
				$responsible_person   =  $info_person["first_name"].$info_person["last_name"];
			}
			$order_array[] = array(
					"ma_id"=>$row['ma_id'],
					"company"=>$row['company'],
					"first_name"=>$row['first_name'],
					"last_name"=>$row['last_name'],
					"responsible_person"=>$responsible_person,
					"reg_date"=>$row['reg_date'],
					"tuition_fee"=>$row['tuition_fee'],
					"meals_fee"=>$row['meals_fee'],
					"tools_fee"=>$row['tools_fee'],
					"order_amount"=>$row['order_amount'],
					"paid_amount"=>$row['paid_amount']
			);
		}
	}
	
	if($_POST["date_search_type"] == "update") {
				
		$sql = $dbf_cmnt->inner3joinDynamic("tb1.id as id,tb1.ma_id,tb1.first_name,tb1.last_name,tb1.company,tb1.reg_date,tb1.dateupdated,tb1.responsible_person,tb1.tuition_fee,tb1.meals_fee,tb1.tools_fee,tb2.member_id,COUNT(DISTINCT tb2.dateupdated) as order_item,SUM(tb2.price)*COUNT(DISTINCT  tb2.dateupdated)/COUNT(*) as order_amount,COUNT(DISTINCT tb3.dateupdated) as paid_item,SUM(tb3.price)*COUNT(DISTINCT  tb3.dateupdated)/COUNT(*) as paid_amount","member","history_sales","history_payment","tb1.id = tb2.member_id","tb2.member_id = tb3.member_id","roles_id=15 AND tb1.dateupdated BETWEEN $start_date AND $end_date $search","tb1.id",$order_by,$limit);
		
		$totalFiltered = $dbf_cmnt->totalRows($dbf_cmnt->count3joinDynamic("member","history_sales","history_payment","tb1.id = tb2.member_id","tb2.member_id = tb3.member_id","tb1.roles_id=15 AND tb1.dateupdated BETWEEN $start_date AND $end_date $search","tb1.id",$order_by,""));
		
		if($dbf_cmnt->totalRows($sql) > 0) {
			$order_array = array();
			while( $row = $dbf_cmnt->nextData($sql)){
				//printf("<pre>%s</pre>",print_r($row,true));
				$responsible_person = '';
				$info_person = $dbf_cmnt->getInfoColum("responsible_person",$row['responsible_person']);
				if($info_person) {
					$responsible_person   =  $info_person["first_name"].$info_person["last_name"];
				}
				$order_array[] = array(
						"ma_id"=>$row['ma_id'],
						"company"=>$row['company'],
						"first_name"=>$row['first_name'],
						"last_name"=>$row['last_name'],
						"responsible_person"=>$responsible_person,
						"reg_date"=>$row['reg_date'],
						"tuition_fee"=>$row['tuition_fee'],
						"meals_fee"=>$row['meals_fee'],
						"tools_fee"=>$row['tools_fee'],
						"order_amount"=>$row['order_amount'],
						"paid_amount"=>$row['paid_amount']
				);
			}
		}
	}
	
	if($_POST["date_search_type"] == "regdate") {
				
		$sql = $dbf_cmnt->inner3joinDynamic("tb1.id as id,tb1.ma_id,tb1.first_name,tb1.last_name,tb1.company,tb1.reg_date,tb1.dateupdated,tb1.responsible_person,tb1.tuition_fee,tb1.meals_fee,tb1.tools_fee,tb2.member_id,COUNT(DISTINCT tb2.dateupdated) as order_item,SUM(tb2.price)*COUNT(DISTINCT  tb2.dateupdated)/COUNT(*) as order_amount,COUNT(DISTINCT tb3.dateupdated) as paid_item,SUM(tb3.price)*COUNT(DISTINCT  tb3.dateupdated)/COUNT(*) as paid_amount","member","history_sales","history_payment","tb1.id = tb2.member_id","tb2.member_id = tb3.member_id","tb1.roles_id=15 AND tb1.reg_date BETWEEN $start_date AND $end_date $search","tb1.id",$order_by,$limit);
		
		$totalFiltered = $dbf_cmnt->totalRows($dbf_cmnt->count3joinDynamic("member","history_sales","history_payment","tb1.id = tb2.member_id","tb2.member_id = tb3.member_id","tb1.roles_id=15 AND tb1.reg_date BETWEEN $start_date AND $end_date $search","tb1.id",$order_by,""));
		
		if($dbf_cmnt->totalRows($sql) > 0) {
			$order_array = array();
			while( $row = $dbf_cmnt->nextData($sql)){
				//printf("<pre>%s</pre>",print_r($row,true));
				$responsible_person = '';
				$info_person = $dbf_cmnt->getInfoColum("responsible_person",$row['responsible_person']);
				if($info_person) {
					$responsible_person   =  $info_person["first_name"].$info_person["last_name"];
				}
				$order_array[] = array(
						"ma_id"=>$row['ma_id'],
						"company"=>$row['company'],
						"first_name"=>$row['first_name'],
						"last_name"=>$row['last_name'],
						"responsible_person"=>$responsible_person,
						"reg_date"=>$row['reg_date'],
						"tuition_fee"=>$row['tuition_fee'],
						"meals_fee"=>$row['meals_fee'],
						"tools_fee"=>$row['tools_fee'],
						"order_amount"=>$row['order_amount'],
						"paid_amount"=>$row['paid_amount']
				);
			}
		}
	}
	
	$data = array();

	foreach($order_array as $key=>$value) {
		$sub_array = [
			$value['ma_id'],
			$value['first_name'],
			$value['last_name'],
			$value['company'],
			$value['responsible_person'],
			$value['reg_date'],
			$value['tuition_fee'],
			$value['meals_fee'],
			$value['tools_fee'],
			/*$value['order_amount'],
			$value['paid_amount'],*/
		];
		
		fputcsv($f, $sub_array, ',', '"');
		
	}

	
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=member_list.csv');
	
	rewind($f);
	if(isset($_SESSION['language']) && $_SESSION['language'] == 'ja_JP') {	
		while (($buf = fgets($f)) !== false) :
			echo mb_convert_encoding($buf, 'SJIS-win', mb_internal_encoding());
		endwhile;
	} else {
		while (($buf = fgets($f)) !== false) :
			//echo mb_convert_encoding($buf, 'Windows-1258', mb_internal_encoding());
			// $converted =  iconv("UTF-8//IGNORE", "WINDOWS-1252//IGNORE", $buf);
			// $converted =  iconv("WINDOWS-1252//IGNORE", "UTF-8//IGNORE", $converted);
			echo $buf;
		endwhile;
	}
	
    fclose($f);
	
	//echo json_encode($json_data);
	//die();
	ob_flush();
}

// DataTable revenue - expenditure list
if (isset($_POST['action']) && $_POST['action']=='revenue_expenditure') {
	$search = '';
	$order_array = array();
	//printf("<pre>%s</pre>",print_r($_POST,true));
	$start_date = strtotime($_POST['start_date']);
	$end_date = strtotime($_POST['end_date']);
	$payment_status = $_POST['payment_status'];
	
	switch ($payment_status) {
		case 'all':
			$search .= '';
			break;
		default :
			$status_query = explode(',',$payment_status);
			if(count($status_query) == 1) {
				$status_item = $status_query[0];
				$search .= " AND tb1.payment_status = $status_item";
			} else {
				$search .= " AND (";
				foreach ($status_query as $status_item) {
					$tb2_status[] = "tb1.payment_status = $status_item";
				}
				$search .= implode(' OR ',$tb2_status);
				$search .= ")";
			}
			break;
	}
	
	$columns = array( 
		0 =>'ma_id', 
		1 => 'company',
		2 => 'dateupdated',
		3 => 'total_amount',
		4 => 'unreceived',
		5 => 'responsible_person',
		6 => 'payment_status'
	);
	$order_by = 'tb2.dateupdated DESC';
	$order_sort = '';
	$limit = '';
		
	if(isset($_POST["order"])){
		$order_by = $columns[$_POST['order'][0]['column']] . " " . $_POST['order'][0]['dir'];
	}
	if($_POST["length"] != -1){
	 $limit = $_POST['start'] . ', ' . $_POST['length'];
	}
	
	if(!empty($_POST["search"])){
		$search.=" AND ( tb1.ma_id LIKE '%".$_POST['search']."%' "; 
		$search.=" OR tb1.company LIKE '%".$_POST['search']."%' )";
	}
	if(isset($_POST["person"]) && $_POST["person"] !=''){
		$search.=" AND tb1.responsible_person = ".$_POST['person']."";
	}

	$totalData = $dbf_cmnt->totalRows($dbf_cmnt->getDynamic("member", "roles_id=15", ""));
				
	$sql = $dbf_cmnt->inner3joinDynamic("tb1.id as id,tb1.ma_id,tb1.company,tb1.payment_status as payment_status,tb2.dateupdated,tb1.responsible_person,tb1.rank,tb1.member_status,tb2.member_id,COUNT(DISTINCT tb2.dateupdated) as order_item,SUM(tb2.price)*COUNT(DISTINCT  tb2.dateupdated)/COUNT(*) as order_amount,COUNT(DISTINCT tb3.dateupdated) as paid_item,SUM(tb3.price)*COUNT(DISTINCT  tb3.dateupdated)/COUNT(*) as paid_amount","member","history_sales","history_payment","tb1.id = tb2.member_id","tb2.member_id = tb3.member_id","roles_id=15 AND tb2.dateupdated BETWEEN $start_date AND $end_date $search","tb1.id",$order_by,$limit);
	
	$totalFiltered = $dbf_cmnt->totalRows($dbf_cmnt->count3joinDynamic("member","history_sales","history_payment","tb1.id = tb2.member_id","tb2.member_id = tb3.member_id","tb1.roles_id=15 AND tb2.dateupdated BETWEEN $start_date AND $end_date $search","tb1.id",$order_by,""));

	if($dbf_cmnt->totalRows($sql) > 0) {
		
		while( $row = $dbf_cmnt->nextData($sql)){
			//printf("<pre>%s</pre>",print_r($row,true));
			$responsible_person = '';
			$info_person = $dbf_cmnt->getInfoColum("responsible_person",$row['responsible_person']);
			if($info_person) {
				$responsible_person   =  $info_person["first_name"].$info_person["last_name"];
			}
			$unreceived = $row["order_amount"] - $row["paid_amount"];
			$payment_val = $row["payment_status"];
			switch (true) {
				case $payment_val == 1:
					$payment_status = T_('Paid');
					break;
				case $payment_val == 2:
					$payment_status = T_('Unpaid');
					break;
				default:
					$payment_status = T_('Partial');
					break;
			}
			$order_array[] = array(
					"ma_id"=>$row['ma_id'],
					"company"=>$row['company'],
					"dateupdated"=>date($short_date_format,$row['dateupdated']),
					"total_amount"=>sprintf( T_('￥%s'),number_format($row["order_amount"],0)),
					"unreceived"=>sprintf( T_('￥%s'),number_format($unreceived,0)),
					"responsible_person"=>$responsible_person,
					"payment_status"=>$payment_status,
					"add_paid"=>'<button type="button" class="btn btn-neo mb-1 update_payment" data-id="'.$row['id'].'" data-paid="'.$row['paid_item'].'" data-unpaid="'.$unreceived.'">'. T_('Update') .'</button>'
			);
		}
	}
	

	$data = array();
	//printf("<pre>%s</pre>",print_r($order_array,true));
	foreach($order_array as $key=>$value) {
		$sub_array = [
			$value['ma_id'],
			$value['company'],
			$value['dateupdated'],
			$value['total_amount'],
			$value['unreceived'],
			$value['responsible_person'],
			$value['payment_status'],
			$value['add_paid']
		];
		
		$data[] = $sub_array;
		
	}
	
	$json_data = array(
	 "draw"    => intval($_POST["draw"]),
	 "recordsTotal"  =>  $totalData,
	 "recordsFiltered" => $totalFiltered,
	 "data"    => $data
	);

	echo json_encode($json_data);
	//die();
}

// CSV revenue - expenditure list
if (isset($_POST['action']) && $_POST['action']=='csv_revenue_expenditure') {
	ob_start();	
	$id = $_SESSION["member_id"];
	$start_date = strtotime($_POST['start_date']);
	$end_date = strtotime($_POST['end_date']);

	$search = '';
	$status_text = '';

	$header_row = [
        T_('Member ID'),
        T_('Company name'),
		T_('Order date'),
        T_('Order amount'),
        T_('Unreceived'),
        T_('Responsible person'),
        T_('Payment status')
    ];
	
	$payment_status = $_POST['payment_status'];
	
	switch ($payment_status) {
		case 'all':
			$search .= '';
			break;
		default :
			$status_query = explode(',',$payment_status);
			if(count($status_query) == 1) {
				$status_item = $status_query[0];
				$search .= " AND tb1.payment_status = $status_item";
				$status_text = ' and payment status is ' . $status_search[$status_item];
			} else {
				$status_text_arr = array();
				$search .= " AND (";
				foreach ($status_query as $status_item) {
					$tb2_status[] = "tb1.payment_status = $status_item";
					$status_text_arr[] = $status_search[$status_item];
				}
				$search .= implode(' OR ',$tb2_status);
				$search .= ")";
				$status_text_string = implode(',',$status_text_arr);
				$status_text = sprintf(T_(' and payment status are %s'),$status_text_string);
			}
			break;
	}

	if(!empty($_POST["search"])){
		$search.=" AND ( tb1.ma_id LIKE '%".$_POST['search']."%' ";
		$search.=" OR tb1.company LIKE '%".$_POST['search']."%' )";
		$status_text .= ' and keyword is ' . $_POST['search'];
	}
	if(isset($_POST["person"]) && $_POST["person"] !=''){
		$search.=" AND tb1.responsible_person = ".$_POST['person']."";
		$search_person = $dbf_cmnt->getInfoColum("responsible_person",$_POST['person']);
			if($search_person) {
				$person   =  $search_person["first_name"].$search_person["last_name"];
			}
		$status_text .= ' and responsible person is ' . $person;
	}

	$f = fopen('php://temp', "r+");
	if($_POST["date_search_type"] == "update") {
		$title_row = [sprintf(T_('Revenue and expenditure list filter by update from %s to %s %s'),$_POST['start_date'],$_POST['end_date'],$status_text)];
		fputcsv($f, $title_row, ',' , '"');
	}
	else {
		$title_row = [sprintf(T_('Revenue and expenditure list filter by registration date from %s to %s %s'),$_POST['start_date'],$_POST['end_date'],$status_text)];
		fputcsv($f, $title_row, ',' , '"');
	}
    fputcsv($f, $header_row, ',' , '"');

    if (!$f) {
        echo 'error';
        exit;
    } 
	
	$totalData = $dbf_cmnt->totalRows($dbf_cmnt->getDynamic("member", "roles_id=15", ""));
				
	$sql = $dbf_cmnt->inner3joinDynamic("tb1.id as id,tb1.ma_id,tb1.company,tb1.payment_status as payment_status,tb2.dateupdated,tb1.responsible_person,tb1.rank,tb1.member_status,tb2.member_id,COUNT(DISTINCT tb2.dateupdated) as order_item,SUM(tb2.price)*COUNT(DISTINCT  tb2.dateupdated)/COUNT(*) as order_amount,COUNT(DISTINCT tb3.dateupdated) as paid_item,SUM(tb3.price)*COUNT(DISTINCT  tb3.dateupdated)/COUNT(*) as paid_amount","member","history_sales","history_payment","tb1.id = tb2.member_id","tb2.member_id = tb3.member_id","roles_id=15 AND tb2.dateupdated BETWEEN $start_date AND $end_date $search","tb1.id",$order_by,$limit);
	
	$totalFiltered = $dbf_cmnt->totalRows($dbf_cmnt->count3joinDynamic("member","history_sales","history_payment","tb1.id = tb2.member_id","tb2.member_id = tb3.member_id","tb1.roles_id=15 AND tb2.dateupdated BETWEEN $start_date AND $end_date $search","tb1.id",$order_by,""));
		
	if($dbf_cmnt->totalRows($sql) > 0) {
		
		while( $row = $dbf_cmnt->nextData($sql)){
			//printf("<pre>%s</pre>",print_r($row,true));
			$responsible_person = '';
			$info_person = $dbf_cmnt->getInfoColum("responsible_person",$row['responsible_person']);
			if($info_person) {
				$responsible_person   =  $info_person["first_name"].$info_person["last_name"];
			}
			$unreceived = $row["order_amount"] - $row["paid_amount"];
			$payment_val = $row["payment_status"];
			switch (true) {
				case $payment_val == 1:
					$payment_status = T_('Paid');
					break;
				case $payment_val == 2:
					$payment_status = T_('Unpaid');
					break;
				default:
					$payment_status = T_('Partial');
					break;
			}
			$order_array[] = array(
					"ma_id"=>$row['ma_id'],
					"company"=>$row['company'],
					"dateupdated"=>date($short_date_format,$row['dateupdated']),
					"total_amount"=>sprintf( T_('￥%s'),number_format($row["order_amount"],0)),
					"unreceived"=>sprintf( T_('￥%s'),number_format($unreceived,0)),
					"responsible_person"=>$responsible_person,
					"payment_status"=>$payment_status
			);
		}
	}
	
	$data = array();

	foreach($order_array as $key=>$value) {
		$sub_array = [
			$value['ma_id'],
			$value['company'],
			$value['dateupdated'],
			$value['total_amount'],
			$value['unreceived'],
			$value['responsible_person'],
			$value['payment_status']
		];
		
		fputcsv($f, $sub_array, ',', '"');
		
	}

	
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=revenue_expenditure_list.csv');
	
	rewind($f);
	if(isset($_SESSION['language']) && $_SESSION['language'] == 'ja_JP') {	
		while (($buf = fgets($f)) !== false) :
			echo mb_convert_encoding($buf, 'SJIS-win', mb_internal_encoding());
		endwhile;
	} else {
		while (($buf = fgets($f)) !== false) :
			//echo mb_convert_encoding($buf, 'Windows-1258', mb_internal_encoding());
			// $converted =  iconv("UTF-8//IGNORE", "WINDOWS-1252//IGNORE", $buf);
			// $converted =  iconv("WINDOWS-1252//IGNORE", "UTF-8//IGNORE", $converted);
			echo $buf;
		endwhile;
	}
	
    fclose($f);
	
	//echo json_encode($json_data);
	//die();
	ob_flush();
}

// Sales update
if (isset($_POST['action']) && $_POST['action']=='add_sales') {
	$member_id = $_POST['id'];
	$amount = $_POST['amount'];
	$vat = $_POST['vat'];
	$price = $amount + $vat;
	$dateinput = strtotime($_POST['dateinput']);
	$payment_status = $_POST['payment_status'];
	$comment = $_POST['comment'];
	$partial = $_POST['partial'];
	$response = array('status'=>0);
	$array_col = array("member_id"=>$member_id,"price"=>$price,"datecreated"=>$dateinput,"dateupdated"=>time(),"status"=>$payment_status,"comment"=>$comment);
	$insert = $dbf_cmnt->insertTable_2("history_sales", $array_col);
	if ($insert > 0){
		$update_arr = array("dateupdated"=>time());
		$update = $dbf_cmnt->updateTable("member", $update_arr, "id='" . $member_id . "'");
		switch(true) {
			case $payment_status == 1:
				$array_paid = array("member_id"=>$member_id,"price"=>$price,"datecreated"=>$dateinput,"dateupdated"=>time(),"status"=>$payment_status,"comment"=>$comment);
				$paid_insert = $dbf_cmnt->insertTable_2("history_payment", $array_paid);
				
			break;
			case $payment_status == 3:
				$array_paid = array("member_id"=>$member_id,"price"=>$partial,"datecreated"=>$dateinput,"dateupdated"=>time(),"status"=>$payment_status,"comment"=>$comment);
				$paid_insert = $dbf_cmnt->insertTable_2("history_payment", $array_paid);
			break;
			default:
				// do nothing
			break;
		}
		
		$difference = $dbf_cmnt->getSubtracting("history_sales","history_payment","price","price","member_id=$member_id","member_id=$member_id AND id IS NOT NULL");
		if($dbf_cmnt->totalRows($difference) > 0) {
			while( $row_diff = $dbf_cmnt->nextData($difference)){
				switch (true) {
					case $row_diff['difference'] == NULL:
						$status_arr = array("payment_status"=>2);
						break;
					case $row_diff['difference'] == 0:
						$status_arr = array("payment_status"=>1);
						break;
					case $row_diff['difference'] == $row_diff['amount']:
						$status_arr = array("payment_status"=>2);
						break;
					default:
						$status_arr = array("payment_status"=>3);
						break;
				}
				$status_update = $dbf_cmnt->updateTable("member", $status_arr, "id='" . $member_id . "'");
			}
		}
		
		$response["status"]=1;
		
	}
	
	header("Content-type:application/json"); 
	echo json_encode($response);
	die();
}

// Update payment
if (isset($_POST['action']) && $_POST['action']=='add_payment') {
	$member_id = $_POST['id'];
	$due = $_POST['due'];
	$liquidate = $_POST['liquidate'];
	$dateinput = strtotime($_POST['dateinput']);
	$payment_status = ($due == $liquidate) ? 1 : 3;
	$comment = $_POST['comment'];
	$response = array('status'=>0);
	$array_col = array("member_id"=>$member_id,"price"=>$liquidate,"datecreated"=>$dateinput,"dateupdated"=>time(),"status"=>$payment_status,"comment"=>$comment);
	$insert = $dbf_cmnt->insertTable_2("history_payment", $array_col);
	if ($insert > 0){
		$update_arr = array("dateupdated"=>time());
		$update = $dbf_cmnt->updateTable("member", $update_arr, "id='" . $member_id . "'");
		
		$difference = $dbf_cmnt->getSubtracting("history_sales","history_payment","price","price","member_id=$member_id","member_id=$member_id");
		if($dbf_cmnt->totalRows($difference) > 0) {
			while( $row_diff = $dbf_cmnt->nextData($difference)){
				switch (true) {
					case $row_diff['difference'] == 0:
						$status_arr = array("payment_status"=>1);
						$response["status"]='paid';
						break;
					case $row_diff['difference'] == $row_diff['amount']:
						$status_arr = array("payment_status"=>2);
						$response["status"]='unpaid';
						break;
					default:
						$status_arr = array("payment_status"=>3);
						$response["status"]='partial';
						break;
				}
				$status_update = $dbf_cmnt->updateTable("member", $status_arr, "id='" . $member_id . "'");
			}
		}
		
		
		
	}
	
	header("Content-type:application/json"); 
	echo json_encode($response);
	die();
}

// CSV import new user
if (isset($_POST['action']) && $_POST['action']=='csv_import') {
	$response = array('status'=>0,'message'=>'');
	$fileName = $_FILES["file"]["tmp_name"];
	    
    if ($_FILES["file"]["size"] > 0) {
        
        $file = fopen($fileName, "r");
        
        while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
           
			$ma_id = $dbf_cmnt->general_ma_id();
			$my_passwords = $utl->randomPassword(10,1,"lower_case,upper_case,numbers,special_symbols");
			$User_Password = $my_passwords[0];
			
			$profile_img = basename($column[13]);
			$card_img = basename($column[14]);
			$profile_uploaded = '';
			$card_uploaded = '';
			list($txt, $ext) = explode(".", $profile_img);
			$profile_img = $ma_id.'_'.$txt;
			$profile_img = $profile_img.".".$ext;
			
			list($c_txt, $c_ext) = explode(".", $card_img);
			$card_img = $ma_id.'_'.$c_txt;
			$card_img = $card_img.".".$c_ext;
			
			//check if the files are only image / document
			if($ext == "jpg" or $ext == "png" or $ext == "gif"){
				//here is the actual code to get the file from the url and save it to the uploads folder
				//get the file from the url using file_get_contents and put it into the folder using file_put_contents
				$upload = file_put_contents(PROJECT_DIR."/upload/member/$profile_img",file_get_contents($column[13]));
				//check success
				if($upload) $profile_uploaded = $profile_img;
			}
			
			//check if the files are only image / document
			if($c_ext == "jpg" or $c_ext == "png" or $c_ext == "gif"){
				//here is the actual code to get the file from the url and save it to the uploads folder
				//get the file from the url using file_get_contents and put it into the folder using file_put_contents
				$c_upload = file_put_contents(PROJECT_DIR."/upload/member/$card_img",file_get_contents($column[14]));
				//check success
				if($c_upload) $card_uploaded = $card_img;
			}
						
			$array_col = array("ma_id" => $ma_id,"roles_id" => 15,"company" => $column[0],"first_name" => $column[1],"last_name" => $column[2],"gender" => $column[3],"email" => $column[4],"date_ngaysinh" => $column[5],"phone_number" => $column[6],"postal_code" => $column[7],"prefecture" => $column[8],"city" => $column[9],"address" => $column[10],"responsible_person" => $column[11],"member_status" => $column[12],"datecreated"=>time(),"description" => $column[15],"parentid" => $_SESSION["member_id"],"tendangnhap" => $ma_id,"password" => md5($User_Password),"password2" => md5($User_Password),"password3" => md5($User_Password),"profile_img" => "upload/member/".$profile_uploaded,"card_img" => "upload/member/".$card_uploaded,"dateupdated"=>time(),"member_re"=>1,"status"=>1,"active_register"=>1,"is_read"=>"yes");
			$affect = $dbf_cmnt->insertTable_2("member", $array_col);
            
            if ($affect > 0) {
                $type = "success";
				$response["status"]=1;
                $response["message"] = T_('CSV Data Imported into the Database');
			
            } else {
                $type = "error";
				$response["status"]=0;
                $response["message"] = T_('Problem in Importing CSV Data');
            }
        }
    }
	
	header("Content-type:application/json"); 
	echo json_encode($response);
	die();
}

// Get reminder
if (isset($_POST['action']) && $_POST['action']=='reminder') {
	$this_year = $_POST['year'];
	$this_month = $_POST['month'];
	$selected_time = $this_year.'/'.$this_month.'/01';
	
	$list = array();
	
	$columns = array( 
		0 =>'ma_id', 
		1 => 'first_name',
		2 => 'last_name',
		3 => 'item'
	);
	$order_by = 'id DESC';
	
	if(isset($_POST["order"])){
		$order_by = $columns[$_POST['order'][0]['column']] . " " . $_POST['order'][0]['dir'];
	}
	if($_POST["length"] != -1){
		$limit = $_POST['start'] . ', ' . $_POST['length'];
	}
	
	$result = $dbf_cmnt->getDynamic("member","roles_id=15",$order_by,$limit);
	
	if ($dbf_cmnt->totalRows($result) > 0) {
		 while ($row = $dbf_cmnt->nextData($result)) {
			$item = array();
			$tuition_fee = $row['tuition_fee'];
			$meals_fee = $row['meals_fee'];
			$tools_fee = $row['tools_fee'];
			$reminder_start = $row['reminder_date'];
			$tuition_reminder_date = $utl->get_next_reminder_date($reminder_start, $tuition_fee, $selected_time);
			$meals_reminder_date = $utl->get_next_reminder_date($reminder_start, $meals_fee, $selected_time);
			$tools_reminder_date = $utl->get_next_reminder_date($reminder_start, $tools_fee, $selected_time);
			
			if (($tuition_reminder = strtotime($tuition_reminder_date)) !== false)
			{
			  $tuition_month = date("n", $tuition_reminder);
			  if($this_month == $tuition_month)
				$item[] = T_('Tuition');
			}
			if (($meals_reminder = strtotime($meals_reminder_date)) !== false)
			{
			  $meals_month = date("n", $meals_reminder);
			  if($this_month == $meals_month)
				$item[] = T_('Meals');
			}
			if (($tools_reminder = strtotime($tools_reminder_date)) !== false)
			{
			  $tools_month = date("n", $tools_reminder);
			  if($this_month == $tools_month)
				$item[] = T_('Tools');
			}
			if(!empty($item))
			$list[] = array(
				"ma_id"=>'<a href="member-edit/?id='.$row['id'].'" data-id="'.$row['id'].'" data-action="edit_member" data-toggle="tooltip" title="" style="overflow: hidden; position: relative;" data-original-title="Edit">'.$row['ma_id'].'</a>',
				"student_name"=>$row['first_name'],
				"nick_name"=>$row['last_name'],
				"item"=>implode(', ',$item)
			);
		}
		$totalFiltered = count($list);
		$totalData = $dbf_cmnt->totalRows($result);
	 }

	$data = array();
	foreach($list as $key=>$value) {
		$sub_array = [
			$value['ma_id'],
			$value['student_name'],
			$value['nick_name'],
			$value['item']
		];
		
		$data[] = $sub_array;
		
	}
	
	$json_data = array(
	 "draw"    => intval($_POST["draw"]),
	 "recordsTotal"  =>  $totalData,
	 "recordsFiltered" => $totalFiltered,
	 "data"    => $data
	);

	echo json_encode($json_data);
	die();
}

// Get revenue number
if (isset($_POST['action']) && $_POST['action']=='revenue_number') {
	$this_year = $_POST['year'];
	$this_month = $_POST['month'];
	$response = array('status'=>0,'row_one'=>0,'row_two'=>0);
	$affect = $dbf_cmnt->getSum("history_sales","COUNT(id) AS OID","price", "YEAR(FROM_UNIXTIME(datecreated)) = $this_year AND MONTH(FROM_UNIXTIME(datecreated)) = $this_month","");
	if ($affect > 0){
		$response["status"]=1;
		while( $row = $dbf_cmnt->nextData($affect)){
			$response["row_one"] = sprintf(T_('%s JPY'),number_format($row['value_sum']));
			$response["row_two"] = $row['OID']>1 ? sprintf(T_('%s items'),number_format($row['OID'])) : sprintf(T_('%s item'),number_format($row['OID']));
		}
	}
	
	header("Content-type:application/json"); 
	echo json_encode($response);
	die();
}

// Get goals number
if (isset($_POST['action']) && $_POST['action']=='goals_number') {
	$this_year = $_POST['year'];
	$this_month = $_POST['month'];
	$goal_month = $_POST['month'] - 1;
	$response = array('status'=>0,'row_one'=>0,'row_two'=>0);
	$revenue_sum = 0;
	$count_item = 0;
	$result = $dbf_cmnt->getDynamic("goal_setting","year=$this_year AND month=$goal_month","");
	
	$affect = $dbf_cmnt->getSum("history_sales","COUNT(id) AS OID","price", "YEAR(FROM_UNIXTIME(datecreated)) = $this_year AND MONTH(FROM_UNIXTIME(datecreated)) = $this_month","");
	if ($affect > 0){
		while( $a_row = $dbf_cmnt->nextData($affect)){
			$revenue_sum = $a_row['value_sum'];
		}
	}
	
	$count = $dbf_cmnt->getCount("member","id","YEAR(FROM_UNIXTIME(datecreated)) = $this_year AND MONTH(FROM_UNIXTIME(datecreated)) = $this_month","");
	if ($dbf_cmnt->totalRows($count) > 0) {
		$total = $dbf_cmnt->nextData($count);
		$count_item = $total['value_count'];
	}
	
	if ($dbf_cmnt->totalRows($result) > 0) {
		 while ($row = $dbf_cmnt->nextData($result)) {
			$response["row_one"] = $row['customer']>1 ? sprintf(T_('%s persons'),number_format($row['customer'])) : sprintf(T_('%s person'),number_format($row['customer']));
			$response["row_two"] = sprintf(T_('%s JPY'),number_format($row['revenue']));
			$response["customer_percent"] = number_format(($count_item * 100) / $row['customer'],2) . '%';
			$response["revenue_percent"] = number_format(($revenue_sum * 100) / $row['revenue'],2) . '%';
		}			
	 }
	
	header("Content-type:application/json"); 
	echo json_encode($response);
	die();
}

// Delete user
if (isset($_POST['action']) && $_POST['action']=='delete_user') {
	$id = $_POST['id'];
	$response = array('status'=>0);
	$affect = $dbf_cmnt->deleteDynamic("member", "id='" . $id . "'");
	if ($affect > 0){
		$response["status"]=1;
	}
	
	header("Content-type:application/json"); 
	echo json_encode($response);
	die();
}

// Add new responsible person
if (isset($_POST['action']) && $_POST['action']=='add_new_responsible_person') {
	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$response = array('id'=>0);

	$array_new = array('first_name'=>$first_name,'last_name'=>$last_name);
	$affect = $dbf_cmnt->insertTable_2("responsible_person", $array_new);

	if ($affect > 0){
		$response["id"]=$affect[2];
	}
	
	header("Content-type:application/json"); 
	echo json_encode($response);
	die();
}

// Update responsible person
if (isset($_POST['action']) && $_POST['action']=='edit_responsible_person') {
	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$id = $_POST['id'];
	$response = array('id'=>0);

	$result = $dbf_cmnt->getDynamic("responsible_person","id=".$id,"");
	if ($dbf_cmnt->totalRows($result) > 0) {
		 while ($row = $dbf_cmnt->nextData($result)) {
			$array_update = array('first_name'=>$first_name,'last_name'=>$last_name);
			$update = $dbf_cmnt->updateTable("responsible_person", $array_update, "id='" . $id . "'","NOSAVELOG");
			$response["id"]=$id;
		}			
	 }
	
	header("Content-type:application/json"); 
	echo json_encode($response);
	die();
}

// Delete responsible person
if (isset($_POST['action']) && $_POST['action']=='delete_responsible_person') {
	$id = $_POST['id'];
	$response = array('status'=>0);
	$affect = $dbf_cmnt->deleteDynamic("responsible_person", "id='" . $id . "'");
	if ($affect > 0){
		$response["status"]=1;
	}
	
	header("Content-type:application/json"); 
	echo json_encode($response);
	die();
}

// DataTable Payment List update payment status
if (isset($_POST['action']) && $_POST['action']=='update_payment_status') {
	$order_id = $_POST['order_id'];
	$paid_status = $_POST["paid_status"];
	$paid_total = $_POST["paid_total"];
	if($paid_status==1) {
		$result = $dbf_cmnt->getDynamic("orders", "id=$order_id", "");
		if ($dbf_cmnt->totalRows($result) > 0) {
			  while ($row = $dbf_cmnt->nextData($result)) {
					$paid_total = $row['total'];
			  }
		}
	}

	$paid_date = date('Y/m/d', time());

	$response = array('status'=>0);
	
	$array_orders = array("paid_date"=>$paid_date,"payment_status"=>$paid_status,"paid"=>$paid_total);
	$orders_arr = $dbf_cmnt->updateTable("orders", $array_orders, "id='" . $order_id . "'","UPDATE",$order_id);
	
	if ($orders_arr) {
		  $response["status"]=1;
	}
	
	header("Content-type:application/json"); 
	echo json_encode($response);
	die();
}

// Get goal setting value
if (isset($_POST['action']) && $_POST['action']=='goal_setting_value') {
	$selected_year = $_POST['year'];
	$saved_goal = $dbf_cmnt->getDynamic("goal_setting", "year=$selected_year", "CAST(month as unsigned) ASC");
	if($dbf_cmnt->totalRows($saved_goal)>0) {
		$key = 0;
		$response = array();
		while( $update = $dbf_cmnt->nextData($saved_goal))
		{
			$response[] = array('revenue'=>number_format($update['revenue']),'customer'=>number_format($update['customer']));
			$key++;
		}
	}
	
	header("Content-type:application/json"); 
	echo json_encode($response);
	die();
}

function download($filePath) 
{     
    if(!empty($filePath)) 
    { 
        $fileInfo = pathinfo($filePath); 
        $fileName  = $fileInfo['basename'];
		//$fileName = str_replace(' ', '_', $fileName);
		//$fileName = str_replace('#', '_', $fileName);
        $fileExtnesion   = $fileInfo['extension']; 
        $default_contentType = "application/octet-stream"; 
        $content_types_list = mimeTypes(); 
        // to find and use specific content type, check out this IANA page : http://www.iana.org/assignments/media-types/media-types.xhtml 
        if (array_key_exists($fileExtnesion, $content_types_list))  
        { 
            $contentType = $content_types_list[$fileExtnesion]; 
        } 
        else 
        { 
            $contentType =  $default_contentType; 
        } 
        if(file_exists($filePath)) 
        { 
            $size = filesize($filePath); 
            $offset = 0; 
            $length = $size; 
            //HEADERS FOR PARTIAL DOWNLOAD FACILITY BEGINS 
            if(isset($_SERVER['HTTP_RANGE'])) 
            { 
                preg_match('/bytes=(\d+)-(\d+)?/', $_SERVER['HTTP_RANGE'], $matches); 
                $offset = intval($matches[1]); 
                $length = intval($matches[2]) - $offset; 
                $fhandle = fopen($filePath, 'r'); 
                fseek($fhandle, $offset); // seek to the requested offset, this is 0 if it's not a partial content request 
                $data = fread($fhandle, $length); 
                fclose($fhandle); 
                header('HTTP/1.1 206 Partial Content'); 
                header('Content-Range: bytes ' . $offset . '-' . ($offset + $length) . '/' . $size); 
            }//HEADERS FOR PARTIAL DOWNLOAD FACILITY BEGINS 
            //USUAL HEADERS FOR DOWNLOAD 
            //header("Content-Disposition: attachment;filename*=us-ascii'en-us'".urlencode($fileName)); 
            header("Content-Disposition: attachment;filename=".$fileName); 
            header('Content-Type: '.$contentType); 
            header("Accept-Ranges: bytes"); 
            header("Pragma: public"); 
            header("Expires: -1"); 
            header("Cache-Control: no-cache"); 
            header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0"); 
            header("Content-Length: ".filesize($filePath)); 
            $chunksize = 20 * (1024 * 1024); //20B (highest possible fread length) 
            if ($size > $chunksize) 
            { 
              $handle = fopen($_FILES["file"]["tmp_name"], 'rb'); 
              $buffer = ''; 
              while (!feof($handle) && (connection_status() === CONNECTION_NORMAL))  
              { 
                $buffer = fread($handle, $chunksize); 
                print $buffer; 
                ob_flush(); 
                flush(); 
              } 
              if(connection_status() !== CONNECTION_NORMAL) 
              { 
                echo "Connection aborted"; 
              } 
              fclose($handle); 
            } 
            else  
            { 
              ob_clean(); 
              flush(); 
              readfile($filePath); 
            } 
         } 
         else 
         { 
           echo 'File does not exist!'; 
         } 
    } 
    else 
    { 
        echo 'There is no file to download!'; 
    } 
} 

/* Function to get correct MIME type for download */ 
function mimeTypes() 
{ 
/* Just add any required MIME type if you are going to download something not listed here.*/ 
$mime_types = array("323" => "text/h323", 
                    "acx" => "application/internet-property-stream", 
                    "ai" => "application/postscript", 
                    "aif" => "audio/x-aiff", 
                    "aifc" => "audio/x-aiff", 
                    "aiff" => "audio/x-aiff", 
                    "asf" => "video/x-ms-asf", 
                    "asr" => "video/x-ms-asf", 
                    "asx" => "video/x-ms-asf", 
                    "au" => "audio/basic", 
                    "avi" => "video/x-msvideo", 
                    "axs" => "application/olescript", 
                    "bas" => "text/plain", 
                    "bcpio" => "application/x-bcpio", 
                    "bin" => "application/octet-stream", 
                    "bmp" => "image/bmp", 
                    "c" => "text/plain", 
                    "cat" => "application/vnd.ms-pkiseccat", 
                    "cdf" => "application/x-cdf", 
                    "cer" => "application/x-x509-ca-cert", 
                    "class" => "application/octet-stream", 
                    "clp" => "application/x-msclip", 
                    "cmx" => "image/x-cmx", 
                    "cod" => "image/cis-cod", 
                    "cpio" => "application/x-cpio", 
                    "crd" => "application/x-mscardfile", 
                    "crl" => "application/pkix-crl", 
                    "crt" => "application/x-x509-ca-cert", 
                    "csh" => "application/x-csh", 
                    "css" => "text/css", 
                    "dcr" => "application/x-director", 
                    "der" => "application/x-x509-ca-cert", 
                    "dir" => "application/x-director", 
                    "dll" => "application/x-msdownload", 
                    "dms" => "application/octet-stream", 
                    "doc" => "application/msword", 
                    "dot" => "application/msword", 
                    "dvi" => "application/x-dvi", 
                    "dxr" => "application/x-director", 
                    "eps" => "application/postscript", 
                    "etx" => "text/x-setext", 
                    "evy" => "application/envoy", 
                    "exe" => "application/octet-stream", 
                    "fif" => "application/fractals", 
                    "flr" => "x-world/x-vrml", 
                    "gif" => "image/gif", 
                    "gtar" => "application/x-gtar", 
                    "gz" => "application/x-gzip", 
                    "h" => "text/plain", 
                    "hdf" => "application/x-hdf", 
                    "hlp" => "application/winhlp", 
                    "hqx" => "application/mac-binhex40", 
                    "hta" => "application/hta", 
                    "htc" => "text/x-component", 
                    "htm" => "text/html", 
                    "html" => "text/html", 
                    "htt" => "text/webviewhtml", 
                    "ico" => "image/x-icon", 
                    "ief" => "image/ief", 
                    "iii" => "application/x-iphone", 
                    "ins" => "application/x-internet-signup", 
                    "isp" => "application/x-internet-signup", 
                    "jfif" => "image/pipeg", 
                    "jpe" => "image/jpeg", 
                    "jpeg" => "image/jpeg", 
                    "jpg" => "image/jpeg", 
                    "js" => "application/x-javascript", 
                    "latex" => "application/x-latex", 
                    "lha" => "application/octet-stream", 
                    "lsf" => "video/x-la-asf", 
                    "lsx" => "video/x-la-asf", 
                    "lzh" => "application/octet-stream", 
                    "m13" => "application/x-msmediaview", 
                    "m14" => "application/x-msmediaview", 
                    "m3u" => "audio/x-mpegurl", 
                    "man" => "application/x-troff-man", 
                    "mdb" => "application/x-msaccess", 
                    "me" => "application/x-troff-me", 
                    "mht" => "message/rfc822", 
                    "mhtml" => "message/rfc822", 
                    "mid" => "audio/mid", 
                    "mny" => "application/x-msmoney", 
                    "mov" => "video/quicktime", 
                    "movie" => "video/x-sgi-movie", 
                    "mp2" => "video/mpeg", 
                    "mp3" => "audio/mpeg", 
                    "mpa" => "video/mpeg", 
                    "mpe" => "video/mpeg", 
                    "mpeg" => "video/mpeg", 
                    "mpg" => "video/mpeg", 
                    "mpp" => "application/vnd.ms-project", 
                    "mpv2" => "video/mpeg", 
                    "ms" => "application/x-troff-ms", 
                    "mvb" => "application/x-msmediaview", 
                    "nws" => "message/rfc822", 
                    "oda" => "application/oda", 
                    "p10" => "application/pkcs10", 
                    "p12" => "application/x-pkcs12", 
                    "p7b" => "application/x-pkcs7-certificates", 
                    "p7c" => "application/x-pkcs7-mime", 
                    "p7m" => "application/x-pkcs7-mime", 
                    "p7r" => "application/x-pkcs7-certreqresp", 
                    "p7s" => "application/x-pkcs7-signature", 
                    "pbm" => "image/x-portable-bitmap", 
                    "pdf" => "application/pdf", 
                    "pfx" => "application/x-pkcs12", 
                    "pgm" => "image/x-portable-graymap", 
                    "pko" => "application/ynd.ms-pkipko", 
                    "pma" => "application/x-perfmon", 
                    "pmc" => "application/x-perfmon", 
                    "pml" => "application/x-perfmon", 
                    "pmr" => "application/x-perfmon", 
                    "pmw" => "application/x-perfmon", 
                    "pnm" => "image/x-portable-anymap", 
                    "pot" => "application/vnd.ms-powerpoint", 
                    "ppm" => "image/x-portable-pixmap", 
                    "pps" => "application/vnd.ms-powerpoint", 
                    "ppt" => "application/vnd.ms-powerpoint", 
                    "prf" => "application/pics-rules", 
                    "ps" => "application/postscript", 
                    "pub" => "application/x-mspublisher", 
                    "qt" => "video/quicktime", 
                    "ra" => "audio/x-pn-realaudio", 
                    "ram" => "audio/x-pn-realaudio", 
                    "ras" => "image/x-cmu-raster", 
                    "rgb" => "image/x-rgb", 
                    "rmi" => "audio/mid", 
                    "roff" => "application/x-troff", 
                    "rtf" => "application/rtf", 
                    "rtx" => "text/richtext", 
                    "scd" => "application/x-msschedule", 
                    "sct" => "text/scriptlet", 
                    "setpay" => "application/set-payment-initiation", 
                    "setreg" => "application/set-registration-initiation", 
                    "sh" => "application/x-sh", 
                    "shar" => "application/x-shar", 
                    "sit" => "application/x-stuffit", 
                    "snd" => "audio/basic", 
                    "spc" => "application/x-pkcs7-certificates", 
                    "spl" => "application/futuresplash", 
                    "src" => "application/x-wais-source", 
                    "sst" => "application/vnd.ms-pkicertstore", 
                    "stl" => "application/vnd.ms-pkistl", 
                    "stm" => "text/html", 
                    "svg" => "image/svg+xml", 
                    "sv4cpio" => "application/x-sv4cpio", 
                    "sv4crc" => "application/x-sv4crc", 
                    "t" => "application/x-troff", 
                    "tar" => "application/x-tar", 
                    "tcl" => "application/x-tcl", 
                    "tex" => "application/x-tex", 
                    "texi" => "application/x-texinfo", 
                    "texinfo" => "application/x-texinfo", 
                    "tgz" => "application/x-compressed", 
                    "tif" => "image/tiff", 
                    "tiff" => "image/tiff", 
                    "tr" => "application/x-troff", 
                    "trm" => "application/x-msterminal", 
                    "tsv" => "text/tab-separated-values", 
                    "txt" => "text/plain", 
                    "uls" => "text/iuls", 
                    "ustar" => "application/x-ustar", 
                    "vcf" => "text/x-vcard", 
                    "vrml" => "x-world/x-vrml", 
                    "wav" => "audio/x-wav", 
                    "wcm" => "application/vnd.ms-works", 
                    "wdb" => "application/vnd.ms-works", 
                    "wks" => "application/vnd.ms-works", 
                    "wmf" => "application/x-msmetafile", 
                    "wps" => "application/vnd.ms-works", 
                    "wri" => "application/x-mswrite", 
                    "wrl" => "x-world/x-vrml", 
                    "wrz" => "x-world/x-vrml", 
                    "xaf" => "x-world/x-vrml", 
                    "xbm" => "image/x-xbitmap", 
                    "xla" => "application/vnd.ms-excel", 
                    "xlc" => "application/vnd.ms-excel", 
                    "xlm" => "application/vnd.ms-excel", 
                    "xls" => "application/vnd.ms-excel", 
                    "xlt" => "application/vnd.ms-excel", 
                    "xlw" => "application/vnd.ms-excel", 
                    "xof" => "x-world/x-vrml", 
                    "xpm" => "image/x-xpixmap", 
                    "xwd" => "image/x-xwindowdump", 
                    "z" => "application/x-compress", 
                    "rar" => "application/x-rar-compressed", 
                    "zip" => "application/zip"); 
return $mime_types;                     
}
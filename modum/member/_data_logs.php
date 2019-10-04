<?php
session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');
include ('../../class/defineConst.php');
include ('../../class/class.BUSINESSLOGIC.php');

// define constants
	define('PROJECT_DIR', realpath('../../'));
	define('LOCALE_DIR', PROJECT_DIR .'/locales');
	define('DEFAULT_LOCALE', 'en_US');
	
	include '../../phpgettext/gettext.inc';

	$supported_locales = array('ja_JP', 'vi_VN');
	$encoding = 'UTF-8';

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

if($_SESSION["roles_id"]==15)
{
	echo T_('You are not authorized to access this page');
	die();
}

$dbf = new BUSINESSLOGIC();
$datatable = array(
			'en_US' => 'English',
			'vi_VN' => 'Vietnamese',
			'ja_JP' => 'Japanese',
		);
	
$order_id = $_SESSION["order_id"];
?>
<style>input {
    border: 1px solid #e2e2e2 !important;
    padding: 2px !important;
}
.data_logs iframe {display:none}</style>
<meta http-equiv="content-type" content="text/html;charset=UTF-8"/>

<!-- Fontfaces CSS-->
<link href="/vendors/@coreui/icons/css/coreui-icons.min.css" rel="stylesheet">
<link href="/vendors/flag-icon-css/css/flag-icon.min.css" rel="stylesheet">
<link href="/vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<link href="/vendors/simple-line-icons/css/simple-line-icons.css" rel="stylesheet">

<link href="/css/custom/style.css" rel="stylesheet">

<link href="/css/coreui/style.css" rel="stylesheet">
<link href="/vendors/pace-progress/css/pace.min.css" rel="stylesheet">


<script src="/vendors/jquery/js/jquery.min.js"></script>
<!--<script src="js/custom/notice.js"></script>-->
<link href="/vendors/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet" />
<!-- Plugins and scripts required by this view-->
<script src="/vendors/datatables.net/js/jquery.dataTables.js"></script>
<script src="/vendors/datatables.net-bs4/js/dataTables.bootstrap4.js"></script>
<div class="block dataTables_wrapper">
<div class="card">
<div class="card-header"><?php echo T_('History Update');?>
</div>
<div class="card-body">
    <table id="mainTable" class="table table-striped table-bordered table-vcenter table-hover dataTable no-footer" role="grid" aria-describedby="example-datatable_info" cellpadding="1" cellspacing="1">
       <thead>
          <tr role="row">
             <th class="titleBottom"><span style="color:red"><?php echo T_('No.');?></span></th>
             <th class="titleBottom"><span style="color:red"><?php echo T_('Date');?></span></th>
             <th class="titleBottom"><span style="color:red"><?php echo T_('Log');?></span></th>
          </tr>
       </thead>
       <tbody>
       <?php
            //$result = $dbf->getDynamic("member", "parentid =". (int)$rowgetInfo["id"] ." and status=1", "id asc");
            $result = $dbf->getDynamic("history_logs", "order_id=$order_id AND (table_name LIKE '%orders%' OR table_name LIKE '%order_detail%')", "id DESC");
            if ($dbf->totalRows($result) > 0) {
                  $no = 1;
                  $f1 = 0;
				  $log_arr = array();
                  while ($row = $dbf->nextData($result)) {
						if(isset($_SESSION['language']) && $_SESSION['language']=='ja_JP') {
							$datecreated = date("Y/m/d H:i:s",$row["datecreated"]);
						} else {
							$datecreated = date("d/m/Y H:i:s",$row["datecreated"]);
						}
                       $user_name = $row["name_member"];
					   if(empty($user_name)) $user_name = 'User with ID=' . $row["member_id"];
                       $content_log = $row["content_log"];
                       $action = 'was ' . $row["type_query"];
                       $table = $row["table_name"];
					   
					   $log_arr[$datecreated][] = array(
							'datecreated'=>$datecreated,
							'data_logs'=>$user_name . ' ' . $action . ' ' . $content_log
						);

                       
                  }
				  foreach($log_arr as $key=>$value) {
					  echo '<tr class="cell2">
								<td class="itemCenter" style="width:20px; color:#3F5F7F">'.$no.'</td>
								<td class="itemText">'.$value[0]['datecreated'].'</td>
								<td class="itemText data_logs">';
					  foreach($value as $k=>$v) {
						  echo $v['data_logs'] . '<br>';
					  }
					  echo '</td></tr>';
					  $no++;
				  }
            }
       ?>
       </tbody>
    </table>
</div>
</div>
</div>
<!-- Bootstrap and necessary plugins-->
  
  <script src="/vendors/popper.js/js/popper.min.js"></script>
  <script src="/vendors/bootstrap/js/bootstrap.min.js"></script>
  <script src="/vendors/pace-progress/js/pace.min.js"></script>
  <script src="/vendors/perfect-scrollbar/js/perfect-scrollbar.min.js"></script>
  <script src="/vendors/@coreui/coreui-pro/js/coreui.min.js"></script>
  <script type="text/javascript">
//<![CDATA[
    $(document).ready(function() {
		
		$('#mainTable').DataTable( {
			"sPaginationType": "simple_numbers",
			"bFilter": false,
			"language":
				{
					 "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/<?php echo $datatable[$locale];?>.json"
				},
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			} ]
		} );
		
    });
 //]]>
</script>
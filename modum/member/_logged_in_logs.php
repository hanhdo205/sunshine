<?php
session_start();
date_default_timezone_set('Asia/Bangkok');
include ('../../class/defineConst.php');
include ('../../class/class.BUSINESSLOGIC.php');

if($_SESSION["roles_id"]==15)
{
	echo "<p>Don't permission access.</p>";
	die();
}

$dbf = new BUSINESSLOGIC();
$lang = array(
		'en' => 'English',
		'vi' => 'Vietnamese',
		'ja' => 'Japanese',
	);
$member_id = $_GET["id"];	
// Include Language file
		if(isset($_SESSION['language'])){
		 include "../languages/lang_".$_SESSION['language'].".php";
		}else{
		 include "../languages/lang_en.php";
		}
?>
<style>input {
    border: 1px solid #e2e2e2 !important;
    padding: 2px !important;
}
.data_logs iframe {display:none}</style>
<meta http-equiv="content-type" content="text/html;charset=UTF-8"/>
<link rel="stylesheet"  type="text/css" href="/css/style.pack.css"/>

<link href="/css/system/template/css/main.css" rel="stylesheet">

<!-- Fontfaces CSS-->
<link href="/css/font-face.css" rel="stylesheet" media="all">
<link href="/vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
<link href="/vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
<link href="/vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

<!-- Bootstrap CSS-->
<link href="/vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

<!-- Vendor CSS-->
<link href="/vendor/animsition/animsition.min.css" rel="stylesheet" media="all">

<!-- Main CSS-->
<link href="/css/theme.css" rel="stylesheet" media="all">
<link href="/css/theme_diamond.css" rel="stylesheet" media="all">

<!-- Jquery JS-->
<script src="//code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="/css/system/template/js/vendor/modernizr-2.8.3.min.js"></script>


<link rel="stylesheet"  type="text/css" href="/css/jquery.dataTables.min.css"/>
<script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

 <div class="block dataTables_wrapper">
    <table id="mainTable" class="table table-striped table-bordered table-vcenter table-hover dataTable no-footer" role="grid" aria-describedby="example-datatable_info" cellpadding="1" cellspacing="1">
       <thead>
          <tr role="row">
             <th class="titleBottom"><span style="color:red"><?php echo _NO;?></span></th>
             <th class="titleBottom"><span style="color:red"><?php echo _DATE;?></span></th>
             <th class="titleBottom"><span style="color:red"><?php echo _LOG;?></span></th>
          </tr>
       </thead>
       <tbody>
       <?php
            //$result = $dbf->getDynamic("member", "parentid =". (int)$rowgetInfo["id"] ." and status=1", "id asc");
            $result = $dbf->getDynamic("history_logs", "member_id=" . $member_id . " AND type_query LIKE '%logged in%'", "id DESC");
            if ($dbf->totalRows($result) > 0) {
                  $no = 1;
                  $f1 = 0;
                  while ($row = $dbf->nextData($result)) {
                       $datecreated = date("d-m-Y H:i:s",$row["datecreated"]);
                       $user_name = $row["name_member"];
					   if(empty($user_name)) $user_name = 'User with ID=' . $row["member_id"];
                       $content_log = $row["content_log"];
                       $action = 'was ' . $row["type_query"];
                       $table = $row["table_name"];
                       
                       echo '<tr class="cell2">
                            <td class="itemCenter" style="width:20px; color:#3F5F7F">'.$no.'</td>
                            <td class="itemText">'.$datecreated.'</td>
                            <td class="itemText data_logs">' . $user_name . ' ' . $action . ' ' . $content_log .'</td>
                       </tr>';
                       $no++;
                  }
            }
       ?>
       </tbody>
    </table>
</div>
<!-- Bootstrap JS-->
    <script src="/vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="/vendor/bootstrap-4.1/bootstrap.min.js"></script>
<script type="text/javascript">
//<![CDATA[
    jQuery(document).ready(function($) {
		
		var table = $('#mainTable').DataTable( {
			"pagingType": "full_numbers",
			"bFilter": true,
			"language": {
				 "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/<?php echo $lang[$_SESSION['language']];?>.json"
				}
		} );

    });

 //]]>
</script>
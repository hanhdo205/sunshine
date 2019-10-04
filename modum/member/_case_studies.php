<?php

if( $_SESSION["Free"]==1)
{
  $html->redirectURL("/system");
}else
{
	 if($rowgetInfo["roles_id"]==15)                                                       
       {
          $html->redirectURL("/ranking");
          exit();
       }
	 $lang = array(
		'en' => 'English',
		'vi' => 'Vietnamese',
		'ja' => 'Japanese',
	);  
	$publish = array(
		'1' => 'Publish',
		'0' => 'Pending',
	);

?>


<script src="/css/system/template/js/vendor/modernizr-2.8.3.min.js"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>

<link rel="stylesheet" type="text/css" href="/js/fancybox/jquery.fancybox.min.css" media="screen" />
<script type="text/javascript" src="/js/fancybox/jquery.fancybox.min.js"></script>

<link rel="stylesheet"  type="text/css" href="/css/jquery.dataTables.min.css"/>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<style>
table.dataTable tbody th,
table.dataTable tbody td {
	padding: 5px;
}
.fancybox-close-small {
  background-image: url('https://cdn.jsdelivr.net/fancybox/1.3.4/fancybox.png');
  background-position: -40px 0px;
  width: 30px;
  height: 30px;
  top: -15px !important;
  right: -15px !important;
  text-indent: -9999px;
}
.jconfirm-buttons{
/*display: none*/
}
.btn-neo.transaction-update {
	margin-right: 20px;
}
input {
    border: 1px solid #e2e2e2 !important;
    padding: 2px !important;
}
</style>
<section id="main">
	<!-- WRAP -->
	<div class="wrap">    
		<!-- CONTENT -->
	    <section id="content">
            <div id="main-container">
                <div id="page-content" style="min-height: 973px;">
				<div class="mb-3"><a href="<?php echo HOST?>case-study.aspx" class="btn btn-effect-ripple btn-neo" style="overflow: hidden; position: relative;"><i class="fa fa-plus" aria-hidden="true"></i> <?php echo _ADDNEW;?></a></div>
                   <div class="block full">
                      <div class="block-title">
                         <h2><?php echo _CASESTUDIES;?></h2>
                      </div>
                      <?php
                        if(isset($_GET["id_del"]) && isset($_GET["delete"]) && (int)$_GET["id_del"]!=0 )
                        {
                            $id_del = (int)$_GET["id_del"];
                            
                                 $affect = $dbf->deleteDynamic("case_studies", "id='" . $id_del . "'");
                                 if ($affect > 0)
                                 {
                                    echo '<div class="alert alert-success alert-dismissable">
                                       <h4><strong>Notice</strong></h4>
                                       <p>Delete post successfull !!!</p>
                                    </div>';

                                 } else
                                 {
                                      echo '<div class="alert alert-danger alert-dismissable">
                                       <h4><strong>Notice</strong></h4>
                                       <p>Delete post wrong !!!</p>
                                    </div>';
                                 }

                            
                        }
                      ?>

                       <div class="block dataTables_wrapper">
					   
							<table id="mainTable" class="table table-striped table-bordered table-vcenter table-hover dataTable no-footer" role="grid" aria-describedby="example-datatable_info" cellpadding="1" cellspacing="1">
							   <thead>
								  <tr role="row">
									 <th class="titleBottom"><?php echo _NO;?></th>
									 <th class="titleBottom"><?php echo _DATE;?></th>
									 <th class="titleBottom"><?php echo _TITLE;?></th>
									 <th class="titleBottom"><?php echo _POSTSTATUS;?></th>
									 <th class="titleBottom no-sort"></th>
								  </tr>
							   </thead>
							   <tbody>
							   <?php
									
									$result = $dbf->getDynamic("case_studies", "", "id DESC");
									if ($dbf->totalRows($result) > 0) {
										  $no = 1;
										  $f1 = 0;
										  while ($row = $dbf->nextData($result)) {
											   $datecreated = date("d-m-Y H:i:s",$row["datecreated"]);
											   $title = $row["title"];
											   $status = $row["status"];
											   
											   echo '<tr class="cell2">
													<td class="itemCenter" style="width:20px; color:#3F5F7F">'.$no.'</td>
													<td class="itemText">'.$datecreated.'</td>
													<td class="itemText">' . $title . '</td>
													<td class="itemText">' . $publish[$status] . '</td>
													<td class="itemText">';
                                                                
                                                                echo '<a href="' .HOST. 'edit-case-study.aspx?id=' . $row['id'] . '" class="btn btn-effect-ripple btn-xs btn-secondary" data-toggle="tooltip" title="" style="overflow: hidden; position: relative;" data-original-title="'._EDIT.'"><i class="fa fa-pencil" aria-hidden="true"></i></a>
																<a href="' .HOST. 'case-studies.aspx?id_del=' . $row['id'] . '&delete=true" class="btn btn-effect-ripple btn-xs btn-danger" data-toggle="tooltip" title="" onclick="return confirm(\'Are you really want to delete?\');" style="overflow: hidden; position: relative;" data-original-title="'._DELETE.'"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
                                                                
                                                             echo'</td>
											   </tr>';
											   $no++;
										  }
									}
							   ?>
							   </tbody>
							</table>
						</div>
                   </div>
                </div>
            </div>

<div class="clearfix"></div>
     </section>
</div>
<div class="clearfix"></div>
</section>
<div class="clearfix"></div>
<?php
}
?>
<script type="text/javascript">
//<![CDATA[
    jQuery(document).ready(function($) {
		
		$('#mainTable').DataTable( {
			"pagingType": "full_numbers",
			"bFilter": true,
			"language": {
				 "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/<?php echo $lang[$_SESSION['lang']];?>.json"
				},
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			} ]
		} );
		
    });
 //]]>
</script>

<style>
.fancybox-slide--iframe .fancybox-content {
    max-width  : 80%;
    max-height : 80%;
    margin: 0;
}
</style>
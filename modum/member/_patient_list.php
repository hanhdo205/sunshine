<link href="vendors/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet" />
<!-- Plugins and scripts required by this view-->
<script src="vendors/datatables.net/js/jquery.dataTables.js"></script>
<script src="vendors/datatables.net-bs4/js/dataTables.bootstrap4.js"></script>
<link rel="stylesheet" href="js/jconfirm/jquery-confirm.css">
<script src="js/jconfirm/jquery-confirm.js"></script>
<script type="text/javascript">
	var translate = {
		member_deleted:"<?php echo T_('Delete patient successfull !!!');?>",
		close_btn:"<?php echo T_('Close');?>",
		sure:"<?php echo T_('Are you really want to delete?');?>",
		submit_btn:"<?php echo T_('Submit');?>",
		cancel_btn:"<?php echo T_('Cancel');?>",
		yes_btn:"<?php echo T_('Yes');?>",
		toast_title:"<?php echo T_('Delete Patient');?>",
	};
	var table;
  </script>
<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item active"><?php echo T_('Patient List');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">

		<div class="row">
		  <div class="col-md-12">
		  <div class="card">
			<div class="card-header">
			  <i class="icon-magnifier"></i> <?php echo T_('Search Patient');?>
			</div>
			<div class="card-body">
				
				<div class="form-group row">
						<div class="col-6">
							<input class="form-control" type="text" id="mySearchText" placeholder="<?php echo T_('Patient Code, Patient Name');?>" />
						</div>
						<div class="form-actions">
						  <button class="btn btn-primary" id="mySearchButton"><?php echo T_('Search');?></button>
						</div>
					</div>

			</div>
		  </div>
			<div class="card">
			  <div class="card-header"><?php echo T_('Patient List');?>
				 <div class="card-header-actions">
					<a href="new-patient.aspx" class="btn btn-sm btn-warning text-white"> <?php echo T_('Add new');?></a>
				  </div> 
			  </div>
			  <div class="card-body">
				<?php
					if(isset($_GET["id_del"]) && isset($_GET["delete"]) && (int)$_GET["id_del"]!=0 )
					{
						$id_del = (int)$_GET["id_del"];
						if($dbf->checkEditMember($rowgetInfo["id"],$id_del))
						{
							 //$array_col = array("status" =>0,"is_del"=>1);
							 //$affect = $dbf->updateTable("member", $array_col, "id='" . $id_del . "'");
							 $affect = $dbf->deleteDynamic("member", "id='" . $id_del . "'");
							 if ($affect > 0)
							 {
								echo '<div class="alert alert-success alert-dismissable">
								   '.T_('Delete member successfull !!!').'
								   <button class="close" type="button" data-dismiss="alert" aria-label="Close">
										<span aria-hidden="true">×</span>
									  </button>
								</div>';

							 } else
							 {
								  echo '<div class="alert alert-danger alert-dismissable">
								   '.T_('Delete member wrong !!!').
								   '<button class="close" type="button" data-dismiss="alert" aria-label="Close">
									<span aria-hidden="true">×</span>
								  </button>
								</div>';
							 }

						}else
						{
							 echo '<div class="alert alert-danger alert-dismissable">
								   '.T_('You can not delete this member !!!').'
								   <button class="close" type="button" data-dismiss="alert" aria-label="Close">
									<span aria-hidden="true">×</span>
								  </button>
								</div>';
						}
					}
					?>
				<table class="table table-striped table-bordered datatable table-vcenter">
					<thead>
					  <tr>
						<th><?php echo T_('Patient Code');?></th>
						<th><?php echo T_('Patient Name');?></th>
					    <th><?php echo T_('Age');?></th>
					    <th><?php echo T_('Gender');?></th>
						<th class="no-sort"></th>
					  </tr>
					</thead>
					<tbody>
					<?php

							$arrayMemberCurrent= array();
							$arrayMemberCurrent = $dbf->getPatientListArray($rowgetInfo["id"],$rowgetInfo,$arrayMemberCurrent);
							$arrayMemberCurrent = $dbf->array_sort_by_column($arrayMemberCurrent,"datecreated");

								foreach($arrayMemberCurrent as $row)
								{
									if($row["is_del"]!=1)
									{
									echo '<tr role="row" class="row_member '.$row['ma_id'].'">
												 <td>' . $row['ma_id'] . '</td>
												 <td>' . $row['hovaten'] . '</td>
												 <td>' . $row['age'] . '</td>
												 <td>' . $gender[$row['gender']].'</td>
												 <td>';
												
												 echo  '
												<a data-id="'.$row['id'].'" class="delete_user_from_list text-white btn btn-effect-ripple btn-xs btn-danger" data-toggle="tooltip" title="" style="overflow: hidden; position: relative;" data-original-title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
												
											 echo'</td>
										  </tr>';
									}
								}
							
						  ?>
					  
					</tbody>
				  </table>
				  
			  </div>
			</div>
		  </div>
		  <!-- /.col-->
		  </div>
		  <!-- /.row-->
	  </div>
	</div>
  </main>
  <script type="text/javascript" src="vendors/toastr/js/toastr.js" class="view-script"></script>
  <link rel="stylesheet" href="vendors/toastr/css/toastr.css">
  <script src="js/custom/delete_user.js"></script>
  <script type="text/javascript">
//<![CDATA[
    $(document).ready(function() {
		
		table = $('.datatable').DataTable( {
			"dom": '<"top"i>rt<"bottom"><"clear">',
			"pagingType": "full_numbers",
			//"bFilter": false,
			"language":
				{
					 "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/<?php echo $datatable[$locale];?>.json",
					 //"searchPlaceholder": "<?php echo T_('Search by Customer number, Company name, Contact person, Email address, Telephone number.');?>",
					 //"search": "",
				},
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			} ]
		} );
		
		$('#mySearchButton').on( 'keyup click', function () {
			table.search($('#mySearchText').val()).draw();
		  } );
		
    });
 //]]>
</script>
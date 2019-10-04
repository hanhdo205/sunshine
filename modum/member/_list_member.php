<link href="vendors/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet" />
<!-- Plugins and scripts required by this view-->
<script src="vendors/datatables.net/js/jquery.dataTables.js"></script>
<script src="vendors/datatables.net-bs4/js/dataTables.bootstrap4.js"></script>
<link rel="stylesheet" href="js/jconfirm/jquery-confirm.css">
<script src="js/jconfirm/jquery-confirm.js"></script>
<script type="text/javascript">
	var translate = {
		member_deleted:"<?php echo T_('Delete member successfull !!!');?>",
		close_btn:"<?php echo T_('Close');?>",
		sure:"<?php echo T_('Are you really want to delete?');?>",
		submit_btn:"<?php echo T_('Submit');?>",
		cancel_btn:"<?php echo T_('Cancel');?>",
		yes_btn:"<?php echo T_('Yes');?>",
		toast_title:"<?php echo T_('Delete User');?>",
	};
	var table;
  </script>
<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item active"><?php echo T_('User List');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">

		<div class="row">
		  <div class="col-md-12">
			<div class="card">
			  <div class="card-header"><?php echo T_('User List');?>
				  
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
						<th class="no-sort"><?php echo T_('User Name');?></th>
						<th class="no-sort"><?php echo T_('Name');?></th>
						<th class="no-sort"><?php echo T_('Role');?></th>
						<th class="no-sort"></th>
					  </tr>
					</thead>
					<tbody>
						<?php
							$role_arr = array(
								4=>T_('Master'),
								5=>T_('Administrator'),
								6=>T_('Manager'),
								7=>T_('Operator'),
								8=>T_('Accountant'),
							);
							

							$arrayMemberCurrent= array();
							$arrayMemberCurrent = $dbf->getMemberListArray($rowgetInfo["id"],$rowgetInfo,$arrayMemberCurrent);
							$arrayMemberCurrent = $dbf->array_sort_by_column($arrayMemberCurrent,"datecreated");


								foreach($arrayMemberCurrent as $row)
								{
									if($row["is_del"]!=1)
									{
									$picture = $row['picture'] ? $row['picture'] : HOST . '/style/images/packages/user.png';
									if($row['date_end']) {
										$date = new DateTime(date('Y-m-d H:i:s',$row['date_end']));
										$now = new DateTime();
									}
									//$rowgetActual = $dbf->getActualColum("actual_sales",$row['id'],"quantity");
									$tax = $row['tax'] ? $row['tax'] : 0;
									$is_active = ($row['date_end'] && $date < $now) ? '<i class="fa fa-user-times text-danger" aria-hidden="true"></i>' : '<i class="fa fa-user text-success" aria-hidden="true"></i>';
									echo '<tr role="row" class="row_member '.$row['ma_id'].'">
												 
												 <td><a href="' .HOST. 'edit-member.aspx?id=' . $row['id'] . '">' . $row['tendangnhap'] . '</a></td>
												 <td>' . $row['hovaten'] . '</td>
												 <td>' . T_($role_arr[$row['roles_id']]) . '</td>
												 <td>';
												if($rowgetInfo["roles_id"]!=15)
												{
												 echo '
												<a data-id="'.$row['id'].'" class="delete_user_from_list text-white btn btn-effect-ripple btn-xs btn-danger" data-toggle="tooltip" title="" style="overflow: hidden; position: relative;" data-original-title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
												}
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
    $(document).ready(function($) {
		
		table = $('.datatable').DataTable( {
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
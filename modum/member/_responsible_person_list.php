<link href="vendors/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet" />
<!-- Plugins and scripts required by this view-->
<script src="vendors/datatables.net/js/jquery.dataTables.js"></script>
<script src="vendors/datatables.net-bs4/js/dataTables.bootstrap4.js"></script>
<link rel="stylesheet" href="js/jconfirm/jquery-confirm.css">
<script src="js/jconfirm/jquery-confirm.js"></script>
<script type="text/javascript">
	var translate = {
		member_deleted:"<?php echo T_('Responsible person deleted!');?>",
		member_created:"<?php echo T_('Responsible person inserted!');?>",
		member_updated:"<?php echo T_('Responsible person updated!');?>",
		close_btn:"<?php echo T_('Close');?>",
		sure:"<?php echo T_('Are you really want to delete?');?>",
		submit_btn:"<?php echo T_('Submit');?>",
		cancel_btn:"<?php echo T_('Cancel');?>",
		yes_btn:"<?php echo T_('Yes');?>",
		toast_title:"<?php echo T_('Notice');?>",
		add_new_title:"<?php echo T_('Add new responsible person');?>",
		update_title:"<?php echo T_('Update responsible person');?>",
		first_name:"<?php echo T_('First name');?>",
		last_name:"<?php echo T_('Last name');?>",
		invalid:"<?php echo T_('Please fill all fields');?>",
	};
	var table;
  </script>
<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item active"><?php echo T_('Responsible Person');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">

		<div class="row">
		  <div class="col-md-12">
		  
			<div class="card">
			  <div class="card-header"><?php echo T_('Responsible Person');?>
				 <div class="card-header-actions">
					<a href="javascript:void(0)" class="btn btn-sm btn-warning text-white responsible_action" data-action="add_new_responsible_person"> <?php echo T_('Add new');?></a>
				  </div> 
			  </div>
			  <div class="card-body">
				
				<table class="table table-striped table-bordered datatable table-vcenter">
					<thead>
					  <tr>
						<th><?php echo T_('ID');?></th>
						<th width="80%"><?php echo T_('Name');?></th>
						<th class="no-sort"></th>
					  </tr>
					</thead>
					<tbody>
					<?php

							$responsible_person = $dbf->getDynamic("responsible_person", "", "id ASC");
							if($dbf->totalRows($responsible_person)>0) {
								while( $person = $dbf->nextData($responsible_person)){
									echo '<tr role="row" class="row_member '.$person['id'].'">';
									echo '<td>' . $person['id'] . '</td>';
									echo '<td>' . $person['first_name'] . ' ' . $person['last_name'] . '</td>';
									echo  '<td><a data-id="'.$person['id'].'" data-action="edit_responsible_person" data-first="' . $person['first_name'] . '" data-last="' . $person['last_name'] . '" class="responsible_action btn btn-effect-ripple btn-xs btn-secondary mr-2" data-toggle="tooltip" title="" style="overflow: hidden; position: relative;" data-original-title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a><a data-id="'.$person['id'].'" data-action="delete_responsible_person" class="delete_user_from_list text-white btn btn-effect-ripple btn-xs btn-danger" data-toggle="tooltip" title="" style="overflow: hidden; position: relative;" data-original-title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a></td>';
									echo'</tr>';
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
  <script src="js/custom/toastr.js"></script>
  <script src="js/custom/responsible_person.js"></script>
  <script src="js/custom/delete_user.js"></script>
  <script type="text/javascript">
//<![CDATA[
    $(document).ready(function() {
		
		table = $('.datatable').DataTable( {
			"pagingType": "simple_numbers",
			"bFilter": false,
			"language":
				{
					 "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/<?php echo $datatable[$locale];?>.json",
				},
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			} ]
		} );

		
    });
 //]]>
</script>
<link href="vendors/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet" />
<!-- Plugins and scripts required by this view-->
<script src="vendors/datatables.net/js/jquery.dataTables.js"></script>
<script src="vendors/datatables.net-bs4/js/dataTables.bootstrap4.js"></script>
<link rel="stylesheet" href="js/jconfirm/jquery-confirm.css">
<script src="js/jconfirm/jquery-confirm.js"></script>
<script type="text/javascript">
	var translate = {
		post_deleted:"<?php echo T_('Category deleted successfully!');?>",
		post_created:"<?php echo T_('Category created successfully!');?>",
		post_updated:"<?php echo T_('Category edited successfully!');?>",
		close_btn:"<?php echo T_('Close');?>",
		sure:"<?php echo T_('Are you really want to delete?');?>",
		submit_btn:"<?php echo T_('Submit');?>",
		cancel_btn:"<?php echo T_('Cancel');?>",
		yes_btn:"<?php echo T_('Yes');?>",
		toast_title:"<?php echo T_('FAQ Category');?>",
	};
	var table;
  </script>

		<div class="row">
		  <div class="col-md-12">
			<div class="card">
			  
			  <div class="card-body">	
				<table class="table table-striped table-bordered datatable table-vcenter">
					<thead>
					  <tr>
						<th class="no-sort"><?php echo T_('No.');?></th>
						<th><?php echo T_('Title');?></th>
						<th><?php echo T_('Status');?></th>
						<th class="no-sort"></th>
					  </tr>
					</thead>
					<tbody>
						<?php
									$trans = $lang[$_SESSION['language']];
									$result = $dbf->getDynamic("category_questions", "1=1", "id DESC");
									if ($dbf->totalRows($result) > 0) {
										  $no = 1;
										  $f1 = 0;
										  while ($row = $dbf->nextData($result)) {											  
											   $datecreated = date("d-m-Y H:i:s",$row["datecreated"]);
											   $title = unserialize($row["title"]);
											   $status = $row["status"];
											   
											   echo '<tr class="cell2">
													<td class="itemCenter" style="width:20px; color:#3F5F7F">'.$no.'</td>
													<td class="itemText">' . $title[$trans] . '</td>
													<td class="itemText">' . T_($publish[$status]) . '</td>
													<td class="itemText">';
                                                                
                                                                echo '<a href="' .HOST. 'created-category-faq.aspx?id=' . $row['id'] . '" class="btn btn-effect-ripple btn-xs btn-secondary" data-toggle="tooltip" title="" style="overflow: hidden; position: relative;" data-original-title="Edit"><i class="fa fa-pencil" aria-hidden="true"></i></a>
																<a data-id="'.$row['id'].'" class="delete_category_faq_from_list text-white btn btn-effect-ripple btn-xs btn-danger" data-toggle="tooltip" title="" style="overflow: hidden; position: relative;" data-original-title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
                                                                
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
		  <!-- /.col-->
		  </div>
		  <!-- /.row-->

  
  <script type="text/javascript" src="vendors/toastr/js/toastr.js" class="view-script"></script>
  <link rel="stylesheet" href="vendors/toastr/css/toastr.css">
  <script src="js/custom/delete_post.js"></script>
  <script type="text/javascript">
//<![CDATA[
    $(document).ready(function() {
		
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
<?php
$year = (isset($_GET['year'])) ? $_GET['year'] : date("Y");
$month = (isset($_GET['month'])) ? $_GET['month'] : date("m");
$id = $_SESSION["member_id"];
$amountbilled = $dbf->getAmountBilled("orders","member_id=$id AND MONTH(FROM_UNIXTIME(order_date)) = $month AND YEAR(FROM_UNIXTIME(order_date)) = $year");
if ($dbf->totalRows($amountbilled) > 0) {
	while ($row = $dbf->nextData($amountbilled)) {
		$sum = $row['sum_total'];
	}
}
?>
<link href="vendors/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet" />
<!-- Plugins and scripts required by this view-->
<script src="vendors/datatables.net/js/jquery.dataTables.js"></script>
<script src="vendors/datatables.net-bs4/js/dataTables.bootstrap4.js"></script>

<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item active"><?php echo T_('Revenue Detail');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">

		<div class="row">
		  <div class="col-md-12">
		  <div class="card">
			<div class="card-body">
				<div class="row">
					<div class="form-group custom-form-group col-md-6">
						
						<div class="row mb-2">
							<div class="col-sm-4 form-group ">
								  <label><?php echo T_('Billing Month');?></label>						  
							</div>
							<div class="col-md-8">
								<input disabled="" type="text" value="<?php echo sprintf(T_('%s/%s'),$year,$month);?>" class="form-control month_to_show" >
							</div>
						</div>
							
						<div class="row mb-2">
							<div class="col-sm-4 form-group ">
								  <label><?php echo T_('Amount Billed');?></label>						  
							</div>
							<div class="col-md-8">
								<input disabled="" type="text" value="<?php echo sprintf(T_('%s JPY'),number_format($sum,0));?>" class="form-control" >
							</div>
						</div>
					</div>
				</div>
			</div>
		
		
			 <div class="card-header custom-card-header"><?php echo T_('Sales Billing Management Billing Monthly Details');?>
				  <div class="card-header-actions">
					<button class="btn btn-sm btn-warning btn-warn revenue_download"><i class="icon-cloud-download"></i> CSV</button>
				  </div>
			  </div>
			  <div class="card-body custom-card-body">
				
				<table id="datatable" class="table table-striped table-bordered datatable table-vcenter">
					<thead>
					  <tr>
						<th><?php echo T_('Order Date');?></th>
						<th><?php echo T_('Order number');?></th>
						<th><?php echo T_('Number of patient');?></th>
						<th><?php echo T_('Number of requests');?></th>
						<th><?php echo T_('Total Fee(VAT)');?></th>
						<th><?php echo T_('Status');?></th>
					  </tr>
					</thead>
					<tbody>
					<?php
						
						$status_arr = array('0'=>'<span class="badge badge-danger">'. T_('Pending') . '</span>','1'=>'<span class="badge badge-warning text-white">' . T_('Assigning') . '</span>','2'=>'<span class="badge badge-warning text-white">' . T_('Assigning') . '</span>','3'=>'<span class="badge badge-primary">' . T_('Processing') . '</span>','4'=>'<span class="badge badge-info text-white">' . T_('Completed') . '</span>','5'=>'<span class="badge badge-success">' . T_('Delivered') . '</span>');
						$result = $dbf->getDynamic("orders", "member_id=$id AND MONTH(FROM_UNIXTIME(order_date)) = $month AND YEAR(FROM_UNIXTIME(order_date)) = $year", "");
						if ($dbf->totalRows($result) > 0) {
							  while ($row = $dbf->nextData($result)) {
								  echo '<tr role="row"><td>'.date("Y/m/d H:i",$row['order_date']).'</td><td>'.$row['order_number'].'</td><td>'.$row['no_patient'].'</td><td>'.$row['quantity'].'</td><td>'.sprintf(T_('%s JPY'),number_format($row['total'],0)).'</td><td>'.T_($status_arr[$row['status']]).'</td></tr>';
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
  <script src="js/custom/jquery.fileDownload.js"></script>
  <script src="js/custom/custom.js"></script>
  <script type="text/javascript">
//<![CDATA[
    $(document).ready(function() {
		
		var table = $('.datatable').DataTable( {
			"sPaginationType": "simple_numbers",
			"bFilter": false,
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
		
		
    });
 //]]>
</script>
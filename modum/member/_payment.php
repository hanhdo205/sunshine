<?php
$start_date = date("Y/m/d", strtotime( date( 'Y-m-d' )));
$end_date = date("Y/m/d", strtotime( date( 'Y-m-d' )));
?>
<link href="vendors/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet" />
<!-- Plugins and scripts required by this view-->
<script src="vendors/datatables.net/js/jquery.dataTables.js"></script>
<script src="vendors/datatables.net-bs4/js/dataTables.bootstrap4.js"></script>

<script src="js/coreui/datatables.js"></script>

<!--<link href="vendors/bootstrap-daterangepicker/css/daterangepicker.min.css" rel="stylesheet" />-->
<link href="css/custom/daterangepicker.css" rel="stylesheet" />
<link href="css/custom/orders.css" rel="stylesheet" />
<link href="vendors/select2/css/select2.min.css" rel="stylesheet" />
<!-- Plugins and scripts required by this view-->
<script src="vendors/jquery.maskedinput/js/jquery.maskedinput.js"></script>
<script src="vendors/moment/js/moment.min.js"></script>
<script src="vendors/select2/js/select2.min.js"></script>
<script src="vendors/bootstrap-daterangepicker/js/daterangepicker.js"></script>

<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item active"><?php echo T_('Accountant-payment');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">

		<div class="row">
		  <div class="col-md-12">
		  <div class="card">
			<div class="card-header">
			  <i class="icon-magnifier"></i> <?php echo T_('Search Form');?>
			</div>
			<div class="card-body">
				<div class="form-group row">
					<label class="col-md-2 col-form-label"><?php echo T_('Payment Status');?></label>
					<div id="statusgroup" class="col-10 col-sm-10 col-md-10 col-lg-10 col-xl-9 col-form-label">
						<div class="form-check form-check-inline mr-1">
							<input class="form-check-input" id="status-checkbox" type="checkbox" value="all" name="payment_status" checked>
							<label class="form-check-label" for="status-checkbox"><?php echo T_('All');?></label>
						</div>
						<div class="form-check form-check-inline mr-1">
							<input class="form-check-input status-has-value" id="payment-radio2" type="checkbox" value="paid" name="payment_status">
							<label class="form-check-label" for="payment-radio2"><?php echo T_('Paid');?></label>
						</div>
						<div class="form-check form-check-inline mr-1">
							<input class="form-check-input status-has-value" id="payment-radio3" type="checkbox" value="unpaid" name="payment_status">
							<label class="form-check-label" for="payment-radio3"><?php echo T_('Unpaid payment');?></label>
						</div>
						<div class="form-check form-check-inline mr-1">
							<input class="form-check-input status-has-value" id="payment-radio4" type="checkbox" value="partial" name="payment_status">
							<label class="form-check-label" for="payment-radio4"><?php echo T_('Partial payment');?></label>
						</div>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-md-2 col-form-label"><?php echo T_('Keyword');?></label>
					<div class="col-10 col-sm-10 col-md-10 col-lg-10 col-xl-6 col-form-label">
						<input class="form-control" id="mySearchText" type="text" placeholder="<?php echo T_('Search by Order number, Company name etc.');?>" />
					</div>
				</div>
				<div class="form-group row">
					<label class="col-md-2 col-form-label"><?php echo T_('Shipping Date');?></label>
					<div class="col-10 col-sm-10 col-md-10 col-lg-10 col-xl-6 col-form-label">
						<div class="input-group">
							<span class="input-group-prepend">
								<span class="input-group-text">
									<i class="fa fa-calendar"></i>
								</span>
								</span>
							<input class="form-control" id="daterange" name="daterange" type="text" />
						</div>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-md-2 col-form-label"></label>
					<div class="col-10 col-sm-10 col-md-10 col-lg-10 col-xl-6 col-form-label">
						<div class="form-actions">
							<button class="btn btn-primary" id="mySearchButton" type="submit"> <?php echo T_('Search');?></button>
						</div>
					</div>
				</div>
				<input type="hidden" value="<?php echo $start_date;?> - <?php echo $end_date;?>" class="today_date"/>
			</div>
		  </div>
			<div class="card">
			  <div class="card-header"><?php echo T_('Order List');?> <span class="filter_text"></span>
				  <div class="card-header-actions">
					<button class="btn btn-sm btn-warning btn-warn csv_order_download"><!-- <i class="icon-cloud-download"></i> --> CSV</button>
				  </div>
			  </div>
			  <div class="card-body">
								<table id="datatable" class="table table-striped table-bordered datatable table-vcenter">
								<thead>
								  <tr>
								  	<th><?php echo T_('Shipping Date');?></th>
									<th><?php echo T_('Customer name');?></th>
									<th><?php echo T_('Order ID');?></th>
									<th><?php echo T_('Total Amount');?></th>
									<th><?php echo T_('Operator');?></th>
									<th><?php echo T_('Payment Status');?></th>
									<th class="no-sort"></th>
								  </tr>
								</thead>
								<tfoot>
									<tr>
										<th></th>
										<th></th>
										<th><?php echo T_('Totals');?></th>
										<th class="payment_totals"></th>
										<th></th>
										<th></th>
										<th></th>
									</tr>
								</tfoot>
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
  <script type="text/javascript">
	var translate = {
		filter_by_shipping_from:"<?php echo T_('Filter by shipping date from ');?>",
		filter_to:"<?php echo T_(' to ');?>",
		filter_by_order_from:"<?php echo T_('Filter by order date from ');?>",
		filter_by_keyword:"<?php echo T_('Filter by keyword: ');?>",
		filter_all:"<?php echo T_('(View all)');?>",
		status_text:"<?php echo T_(' and status are pending');?>",
		status_all:"<?php echo T_('(status are pending)');?>",
		toast_title:"<?php echo T_('Updated');?>",
		number_empty:"<?php echo T_('Please enter number');?>",
		number_invalid:"<?php echo T_('Number too big');?>",
		payment_updated:"<?php echo T_('Payment Status updated!');?>",
		unpaid: "<?php echo '<button type=\"button\" class=\"btn badge badge-warning customer-badge dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">' . T_('Unpaid') . '</button>';?>",
		paid: "<?php echo '<button type=\"button\" class=\"btn badge badge-success customer-badge dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">' . T_('Paid') . '</button>';?>",
		partial: "<?php echo '<button type=\"button\" class=\"btn badge badge-info customer-badge dropdown-toggle\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">' . T_('Partial payment') . '</button>';?>",
	};
  </script>

  <script src="js/custom/jquery.fileDownload.js"></script>
  <script type="text/javascript" src="vendors/toastr/js/toastr.js" class="view-script"></script>
  <link rel="stylesheet" href="vendors/toastr/css/toastr.css">
  <script src="js/custom/payment_list.js"></script>
  
  <script type="text/javascript">
	$(document).on('click', '.dropdown-menu>form', function(e) {
		e.stopPropagation();
	});
	$('input[name="daterange"]').daterangepicker({
		  autoApply: true,
		  opens: 'right',
		  /*ranges: {
			'<?php echo T_('Today');?>': [moment(), moment()],
			'<?php echo T_('Yesterday');?>': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			'<?php echo T_('Last 7 Days');?>': [moment().subtract(6, 'days'), moment()],
			'<?php echo T_('Last 30 Days');?>': [moment().subtract(29, 'days'), moment()],
			'<?php echo T_('This Month');?>': [moment().startOf('month'), moment().endOf('month')],
			'<?php echo T_('Last Month');?>': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
		  },*/
		  locale: {
				"format": "YYYY/MM/DD",
				"separator": " - ",
				"applyLabel": "<?php echo T_('Apply');?>",
				"cancelLabel": "<?php echo T_('Cancel');?>",
				"fromLabel": "<?php echo T_('From');?>",
				"toLabel": "<?php echo T_('To');?>",
				"customRangeLabel": "<?php echo T_('Custom Range');?>",
				"daysOfWeek": [
					"<?php echo T_('Su');?>",
					"<?php echo T_('Mo');?>",
					"<?php echo T_('Tu');?>",
					"<?php echo T_('We');?>",
					"<?php echo T_('Th');?>",
					"<?php echo T_('Fr');?>",
					"<?php echo T_('Sa');?>"
				],
				"monthNames": [
					"<?php echo T_('January');?>",
					"<?php echo T_('February');?>",
					"<?php echo T_('March');?>",
					"<?php echo T_('April');?>",
					"<?php echo T_('May');?>",
					"<?php echo T_('June');?>",
					"<?php echo T_('July');?>",
					"<?php echo T_('August');?>",
					"<?php echo T_('September');?>",
					"<?php echo T_('October');?>",
					"<?php echo T_('November');?>",
					"<?php echo T_('December');?>"
				],
				"firstDay": 1
			}
			 
		});
  </script>
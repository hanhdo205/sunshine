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
	  <li class="breadcrumb-item active"><?php echo T_('Orders');?></li>
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
						<div class="col-8">
							<input class="form-control" id="mySearchText" type="text" placeholder="<?php echo T_('Search by Order number, Company name, Contact person, Patient etc.');?>" />
						</div>
						<div class="form-actions">
						  <button class="btn btn-primary" id="mySearchButton" type="submit"> <?php echo T_('Search');?></button>
						</div>
					</div>
				<div class="row">
					<div class="form-group col-md-6">
						<label><?php echo T_('Order Date');?></label>
							<div class="form-group row">
								<div class="col-8">
									
										<div class="input-group">
										  <span class="input-group-prepend">
											<span class="input-group-text">
											  <i class="fa fa-calendar"></i>
											</span>
										  </span>
										  <input class="form-control" id="orderrange" name="daterange" type="text" />
										</div>
								</div>
								<div class="form-actions">
								  <button class="btn btn-primary" id="orderButton" type="submit"> <?php echo T_('Search');?></button>
								</div>
							</div>
					</div>
					<div class="form-group col-md-6">
						<label><?php echo T_('Shipping Date');?></label>
							<div class="form-group row">
								<div class="col-8">
									
										<div class="input-group">
										  <span class="input-group-prepend">
											<span class="input-group-text">
											  <i class="fa fa-calendar"></i>
											</span>
										  </span>
										  <input class="form-control" id="shippingrange" name="daterange" type="text" />
										</div>
								</div>
								<div class="form-actions">
								  <button class="btn btn-primary" id="shippingButton" type="submit"></i> <?php echo T_('Search');?></button>
								</div>
							</div>
					</div>
				</div>
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
						<th><?php echo T_('Order Date');?></th>
						<th><?php echo T_('Customer Name');?></th>
						<th><?php echo T_('Priority');?></th>
						<th><?php echo T_('Total Qty');?></th>
						<th><?php echo T_('Total Amount');?></th>
						<th><?php echo T_('Shipping Date');?></th>
						<th><?php echo T_('Operating Status');?></th>
						<th><?php echo T_('Operator');?></th>
						<th><?php echo T_('Payment Status');?></th>
						<th><?php echo T_('Last Update');?></th>
						<th class="no-sort"></th>
					  </tr>
					</thead>
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
		status_priority:"<?php echo T_(' and priority is high');?>",
		status_all:"<?php echo T_('(status are pending)');?>",
		status_all_priority:"<?php echo T_('(priority is high)');?>",
	};
  </script>
  <script src="js/custom/jquery.fileDownload.js"></script>
  <script src="js/custom/order_list.js"></script>
  
  <script type="text/javascript">

	$('input[name="daterange"]').daterangepicker({
		  autoApply: true,
		  opens: 'left',
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
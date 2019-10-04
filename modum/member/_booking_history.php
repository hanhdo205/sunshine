<link href="vendors/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet" />
<!-- Plugins and scripts required by this view-->
<script src="vendors/datatables.net/js/jquery.dataTables.js"></script>
<script src="vendors/datatables.net-bs4/js/dataTables.bootstrap4.js"></script>

<script src="js/coreui/datatables.js"></script>

<!-- Plugins and scripts required by this view-->
<script src="vendors/jquery.maskedinput/js/jquery.maskedinput.js"></script>
<!--<link href="vendors/bootstrap-daterangepicker/css/daterangepicker.min.css" rel="stylesheet" />-->
<link href="css/custom/daterangepicker.css" rel="stylesheet" />
<link href="css/custom/orders.css" rel="stylesheet" />
<link href="vendors/select2/css/select2.min.css" rel="stylesheet" />
<script src="vendors/moment/js/moment.min.js"></script>
<script src="vendors/select2/js/select2.min.js"></script>
<script src="vendors/bootstrap-daterangepicker/js/daterangepicker.js"></script>

<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item active"><?php echo T_('Order list');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">
		<?php //if($rowgetInfo["roles_id"]==15) { ?>
		<div class="row">
		  <div class="col-md-12 mb-5">
			<div class="card">
			<div class="card-body">
				<div class="form-group row">
					<label class="col-md-2 col-form-label"><?php echo T_('Filter by');?></label>
					<div class="col-10 col-sm-10 col-md-10 col-lg-10 col-xl-6 col-form-label">
							<div class="form-group row">
								<div class="col-8">
									<input class="form-control" type="text" id="mySearchText" placeholder="<?php echo T_('Order number or  Patient name');?>" />
								</div>
								<div class="form-actions">
								  <button class="btn btn-primary" id="mySearchButton"> <?php echo T_('Search');?></button>
								</div>
							</div>
					</div>
				</div>
				<div class="row"><div class="col-md-7 text-center mb-4 fancy"><span><?php echo T_('OR');?></span></div><div class="col-md-5"></div></div>
				<div class="row">
					<label class="col-md-2 col-form-label"><?php echo T_('Date range filter');?></label>
					<div class="col-10 col-sm-10 col-md-10 col-lg-10 col-xl-6 col-form-label">
						<div class="form-check form-check-inline mr-1">
							<input class="form-check-input" id="dateranger-radio1" type="radio" value="order" name="search_type" <?php echo (isset($_GET['status'])) ? '' : 'checked';?>>
							<label class="form-check-label" for="dateranger-radio1"><?php echo T_('Order Date');?></label>
						</div>
						<div class="form-check form-check-inline mr-1">
							<input class="form-check-input" id="dateranger-radio2" type="radio" value="shipping" name="search_type" <?php echo (isset($_GET['status'])) ? 'checked' : '';?>>
							<label class="form-check-label" for="dateranger-radio2"><?php echo T_('Shipping Date');?></label>
						</div>
					</div>
				</div>
				<div class="row">
					<label class="col-md-2 col-form-label"></label>
					<div class="col-10 col-sm-10 col-md-10 col-lg-10 col-xl-6 col-form-label">
						<div class="form-group row">
							<div class="col-8">
								<div class="input-group">
									<span class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-calendar"></i>
										</span>
										</span>
									<input class="form-control" id="daterange" name="daterange" type="text" />
								</div>
							</div>
							<div class="form-actions">
							  <button class="btn btn-primary" id="daterangeButton"> <?php echo T_('Search');?></button>
							</div>
						</div>
					</div>
				</div>
			</div>	
			  <div class="card-header custom-card-header"><?php echo T_('Order history');?></div>
			  <div class="card-body custom-card-body">
				
				<table id="datatable" class="table table-striped table-bordered datatable table-vcenter customer-table">
					<thead>
					  <tr>
						<th><?php echo T_('Order Date');?></th>
						<th><?php echo T_('Order number');?></th>
						<th><?php echo T_('Patient name');?></th>
						<th class="no-sort"><?php echo T_('Item');?></th>
						<th><?php echo T_('Expedited Shipping');?></th>
						<th><?php echo T_('Shipping Date');?></th>
						<th><?php echo T_('Status');?></th>
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
		  
		<?php //} ?>
		
	  </div>
	</div>
  </main>
  <!--<script src="js/coreui/advanced-forms.js"></script>-->
  <script src="js/custom/booking.js"></script>
  <script type="text/javascript">
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
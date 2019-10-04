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

<link rel="stylesheet" href="js/jconfirm/jquery-confirm.css">
<script src="js/jconfirm/jquery-confirm.js"></script>
<link rel="stylesheet" href="css/custom/jquery-ui.css">
<script src="js/custom/jquery-ui.js"></script>
<?php if(isset($_SESSION['language']) && $_SESSION['language'] != 'en_US') { ?>
<script src="js/custom/datepicker-<?php echo $datepicker_lang[$_SESSION['language']];?>.js"></script>
<?php } ?>

<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item active"><?php echo T_('Revenue and expenditure management');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">

		<div class="row">
		  <div class="col-md-12">
		  <div class="card">
			<div class="card-header">
			  <i class="icon-magnifier"></i> <?php echo T_('Revenue and expenditure search form');?>
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
							<input class="form-check-input status-has-value" id="payment-radio2" type="checkbox" value="1" name="payment_status">
							<label class="form-check-label" for="payment-radio2"><?php echo T_('Paid');?></label>
						</div>
						<div class="form-check form-check-inline mr-1">
							<input class="form-check-input status-has-value" id="payment-radio3" type="checkbox" value="2" name="payment_status">
							<label class="form-check-label" for="payment-radio3"><?php echo T_('Unpaid');?></label>
						</div>
						<div class="form-check form-check-inline mr-1">
							<input class="form-check-input status-has-value" id="payment-radio4" type="checkbox" value="3" name="payment_status">
							<label class="form-check-label" for="payment-radio4"><?php echo T_('Partial');?></label>
						</div>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-md-2 col-form-label"><?php echo T_('Keyword');?></label>
					<div class="col-10 col-sm-10 col-md-10 col-lg-10 col-xl-6 col-form-label">
						<input class="form-control" id="mySearchText" type="text" placeholder="<?php echo T_('Search by member ID, Company name');?>" />
					</div>
				</div>
				<div class="form-group row">
					<label class="col-md-2 col-form-label"><?php echo T_('Responsible person');?></label>
					<div class="col-10 col-sm-10 col-md-10 col-lg-10 col-xl-6 col-form-label">
						<select id="select_person" class="form-control select_person select2">
							<option value=""><?php echo T_('---');?></option>
							<?php $responsible_person = $dbf->getDynamic("responsible_person", "", "id ASC");
								if($dbf->totalRows($responsible_person)>0) {
									while( $person = $dbf->nextData($responsible_person)){
										echo '<option value="'.$person['id'].'">' . $person['first_name'] . $person['last_name'] . '</option>';
									}
								}
							?>
						</select>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-md-2 col-form-label"><?php echo T_('Daterange');?></label>
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
			  <div class="card-header"><?php echo T_('Revenue and expenditure list');?> <span class="filter_text"></span>
				  <div class="card-header-actions">
					<button class="btn btn-sm btn-warning btn-warn csv_payment_download"><!-- <i class="icon-cloud-download"></i> --> CSV</button>
				  </div>
			  </div>
			  <div class="card-body">
								<table id="datatable" class="table table-striped table-bordered datatable table-vcenter">
								<thead>
								  <tr>
								  	<th><?php echo T_('Member ID');?></th>
									<th><?php echo T_('Company name');?></th>
									<th><?php echo T_('Order date');?></th>
									<th><?php echo T_('Total Amount');?></th>
									<th><?php echo T_('Unreceived');?></th>
									<th><?php echo T_('Responsible person');?></th>
									<th><?php echo T_('Payment status');?></th>
									<th class="no-sort"><?php echo T_('Payment update');?></th>
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
		date_label:"<?php echo T_('Date');?>",
		due_label:"<?php echo T_('Due');?>",
		liquidate_label:"<?php echo T_('Liquidate');?>",
		note_label:"<?php echo T_('Note');?>",
		toast_title:"<?php echo T_('Notice');?>",
		cancel_btn:"<?php echo T_('Cancel');?>",
		yes_btn:"<?php echo T_('Yes');?>",
		submit_btn:"<?php echo T_('Submit');?>",
		form_payment_update_title:"<?php echo T_('Payment update');?>",
		invalid:"<?php echo T_('Please fill out liquidate field');?>",
		close_btn:"<?php echo T_('Close');?>",
		payment_updated:"<?php echo T_('Payment updated');?>",
		jpy:"<?php echo T_('JPY');?>",
		paid:"<?php echo T_('Paid');?>",
		unpaid:"<?php echo T_('Unpaid');?>",
		partial:"<?php echo T_('Partial');?>",
	};
	var table;
  </script>

  <script src="js/custom/jquery.fileDownload.js"></script>
  <script type="text/javascript" src="vendors/toastr/js/toastr.js" class="view-script"></script>
  <link rel="stylesheet" href="vendors/toastr/css/toastr.css">
  <script src="js/custom/toastr.js"></script>
  <script src="js/custom/revenue-expenditure.js"></script>
  
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
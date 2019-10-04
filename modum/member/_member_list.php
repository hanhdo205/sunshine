<?php
$start_date = date("Y/m/d", strtotime( date( 'Y-m-d' )));
$end_date = date("Y/m/d", strtotime( date( 'Y-m-d' )));
unset($_SESSION['count']);
$getvat_info = $dbf->getInfoColum("setting",25);
$set_vat = $getvat_info['value'];
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
	  <li class="breadcrumb-item active"><?php echo T_('Member list');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">

		<div class="row">
		  <div class="col-md-12">
		  <div class="card">
			<div class="card-header">
			  <i class="icon-magnifier"></i> <?php echo T_('Member search');?>
			</div>
			<div class="card-body">

						<div class="form-group row">
							<label class="col-md-2 col-form-label"><?php echo T_('Keyword');?></label>
							<div class="col-10 col-sm-10 col-md-10 col-lg-10 col-xl-6 col-form-label">
								<input class="form-control" id="mySearchText" type="text" placeholder="<?php echo T_('Search by Student name, Parent name');?>" />
							</div>
						</div>
						
						<div class="form-group row">
							<label class="col-md-2 col-form-label"><?php echo T_('Responsible person');?></label>
							<div class="col-10 col-sm-10 col-md-10 col-lg-10 col-xl-6 col-form-label">
								<select id="select_person" class="form-control select_person select2" name="select_person">
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
						
						<div class="row">
							<label class="col-md-2 col-form-label"><?php echo T_('Date range filter');?></label>
							<div class="col-10 col-sm-10 col-md-10 col-lg-10 col-xl-6 col-form-label">
								<div class="form-check form-check-inline mr-1">
									<input class="form-check-input" id="dateranger-radio" type="radio" value="no" name="search_type" checked>
									<label class="form-check-label" for="dateranger-radio"><?php echo T_('All');?></label>
								</div>
								<div class="form-check form-check-inline mr-1">
									<input class="form-check-input" id="dateranger-radio1" type="radio" value="update" name="search_type">
									<label class="form-check-label" for="dateranger-radio1"><?php echo T_('Last update');?></label>
								</div>
								<div class="form-check form-check-inline mr-1">
									<input class="form-check-input" id="dateranger-radio2" type="radio" value="regdate" name="search_type">
									<label class="form-check-label" for="dateranger-radio2"><?php echo T_('Registration date');?></label>
								</div>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-2 col-form-label"></label>
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
			  <div class="card-header"><?php echo T_('Member list');?> <span class="filter_text"></span>
					<?php if($rowgetInfo["roles_id"]==4) { ?>
					  <div class="card-header-actions">
						<a href="member-create.aspx" class="btn btn-sm btn-primary btn-warn"><i class="icon-user-follow"></i> <?php echo T_('New member');?></a>
						<!--<button class="btn btn-sm btn-warning btn-warn csv_member_download"><i class="icon-folder-alt"></i> CSV</button>-->
					  </div>
					<?php } ?>
			  </div>
			  <div class="card-body">
				
				<table id="datatable" class="table table-striped table-bordered datatable">
					<thead>
					  <tr>
						<th class="text-center" rowspan=2><?php echo T_('Member ID');?></th>
						 <th class="text-center sorting" rowspan=2><?php echo T_('Student name');?></th>
						 <th class="text-center sorting" rowspan=2><?php echo T_('Nickname');?></th>
						 <th class="text-center sorting" rowspan=2><?php echo T_('Parent name');?></th>
						 <th class="text-center sorting" rowspan=2><?php echo T_('Responsible person');?></th>
						 <th class="text-center sorting" rowspan=2><?php echo T_('Registration date');?></th>
						 <th class="text-center sorting_disabled no-sort" colspan=3><?php echo T_('Term (month)');?></th>
						 <!--<th class="text-center sorting_disabled no-sort" colspan=2><?php echo T_('Revenue - Expenditure (VND)');?></th>
						 <th class="text-center sorting_disabled no-sort" rowspan=2><?php echo T_('Update');?></th>-->
						 
						 <th class="text-center sorting_disabled no-sort" style="min-width:80px;" rowspan=2><span>&nbsp;</span></th>
						  
						</tr>
						<tr>									  
							 <th class="text-center sorting"><?php echo T_('Tuition');?></th>
							 <th class="text-center sorting"><?php echo T_('Meals');?></th>
							 <th class="text-center sorting border-right"><?php echo T_('Tools');?></th>
							 <!--<th class="text-center sorting"><?php echo T_('Amount');?></th>
							 <th class="text-center sorting border-right"><?php echo T_('Paid');?></th>-->
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
		amount_label:"<?php echo T_('Amount (exclude vat)');?>",
		vat_label:"<?php echo T_('VAT');?>",
		received_label:"<?php echo T_('Received');?>",
		paid_label:"<?php echo T_('Paid');?>",
		unpaid_label:"<?php echo T_('Unpaid');?>",
		partial_label:"<?php echo T_('Partial payment');?>",
		note_label:"<?php echo T_('Note');?>",
		toast_title:"<?php echo T_('Notice');?>",
		cancel_btn:"<?php echo T_('Cancel');?>",
		yes_btn:"<?php echo T_('Yes');?>",
		submit_btn:"<?php echo T_('Submit');?>",
		form_payment_update_title:"<?php echo T_('Sales update');?>",
		invalid:"<?php echo T_('Please fill out amount field');?>",
		close_btn:"<?php echo T_('Close');?>",
		sales_updated:"<?php echo T_('Sales updated');?>",
		jpy:"<?php echo T_('JPY');?>",
	};
	var table;
	var vat = <?php echo $set_vat;?>;
  </script>
  <script src="js/custom/jquery.fileDownload.js"></script>
  <script type="text/javascript" src="vendors/toastr/js/toastr.js" class="view-script"></script>
  <link rel="stylesheet" href="vendors/toastr/css/toastr.css">
  <script src="js/custom/toastr.js"></script>
  <script src="js/custom/member_list.js"></script>
  <script src="js/custom/delete_user.js"></script>
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
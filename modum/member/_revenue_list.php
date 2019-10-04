<link href="vendors/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet" />
<!-- Plugins and scripts required by this view-->
<script src="vendors/datatables.net/js/jquery.dataTables.js"></script>
<script src="vendors/datatables.net-bs4/js/dataTables.bootstrap4.js"></script>

<script src="js/coreui/datatables.js"></script>

<!-- Plugins and scripts required by this view-->
<link rel="stylesheet" href="css/custom/revenue.css">
<link rel="stylesheet" href="css/custom/jquery-ui.css">
<script src="js/custom/jquery-ui.js"></script>
<?php if(isset($_SESSION['language']) && $_SESSION['language'] != 'en_US') { ?>
<script src="js/custom/datepicker-<?php echo $datepicker_lang[$_SESSION['language']];?>.js"></script>
<?php } ?>

<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item active"><?php echo T_('Revenue List');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">
		<?php //if($rowgetInfo["roles_id"]==15) { ?>
		<div class="row">
		  <div class="col-md-12 mb-5">
			<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="form-group custom-form-group col-md-6">
						
						<div class="row mb-2">
							<div class="col-sm-4 form-group ">
								  <label><?php echo $rowgetInfo["ma_id"];?></label>						  
							</div>
							<div class="col-md-8">
								<input disabled="" type="text" value="<?php echo $rowgetInfo["company"];;?>" class="form-control" >
							</div>
						</div>
						<label><?php echo T_('Billing month');?></label>
							<div class="form-group row">
								<div class="col-9">
										<div class="input-group">
										  <span class="input-group-prepend">
											<span class="input-group-text">
											  <i class="fa fa-calendar"></i>
											</span>
										  </span>
										  <input class="form-control" id="monthselect" name="daterange" type="text" autocomplete="off" />

										</div>
								</div>
								<div class="form-actions">
								  <button class="btn btn-primary" id="daterangeButton"><i class="fa fa-dot-circle-o"></i> <?php echo T_('Search');?></button>
								</div>
							</div>
					</div>
				</div>
			</div>
			<hr>
			  <div class="card-header custom-card-header"><?php echo T_('Sales Billing Management Billing List');?></div>
			  <div class="card-body custom-card-body">
				
				<table id="datatable" class="table table-striped table-bordered datatable table-vcenter">
					<thead>
					  <tr>
						<th><?php echo T_('Use month');?></th>
						<th><?php echo T_('Amount');?></th>
						<th class="no-sort"></th>
					  </tr>
					</thead>
					<tbody>
					
					</tbody>
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
  <script src="js/custom/revenue.js"></script>
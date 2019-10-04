<?php
$id = $_SESSION["member_id"];
$last_month = date('m', strtotime('-1 month', time()));
$this_month = date('m');
$this_month_short = date('n');
$this_year = date('Y');
$last_year = date('Y', strtotime('-2 year'));
$count_item = 0;
$count_done = 0;
$priority_item = 0;
$start_date = date("Y/m/d", strtotime( date( 'Y-m-01' )));
$end_date = date("Y/m/d", strtotime( date( 'Y-m-d' )));
?>
<link href="vendors/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet" />
<!-- Plugins and scripts required by this view-->
<script src="vendors/datatables.net/js/jquery.dataTables.js"></script>
<script src="vendors/datatables.net-bs4/js/dataTables.bootstrap4.js"></script>

<script src="js/coreui/datatables.js"></script>

<link rel="stylesheet" href="css/custom/jquery-ui.css">
<script src="js/custom/jquery-ui.js"></script>
<?php if(isset($_SESSION['language']) && $_SESSION['language'] != 'en_US') { ?>
<script src="js/custom/datepicker-<?php echo $datepicker_lang[$_SESSION['language']];?>.js"></script>
<?php } ?>
<script type="text/javascript">
	var table;
  </script>
<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><?php echo T_('Home');?></a></li>
	  <!-- Breadcrumb Menu-->
	  <!--<li class="breadcrumb-menu d-md-down-none">
		<div class="btn-group" role="group" aria-label="Button group">
		  <a class="btn" href="#">
			<i class="icon-speech"></i>
		  </a>
		  <a class="btn" href="./">
			<i class="icon-graph"></i>  Dashboard</a>
		  <a class="btn" href="#">
			<i class="icon-settings"></i>  Settings</a>
		</div>
	  </li>-->
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">
			<div class="row">
			
				<div class="col-md-12">
					<div class="card">
						<div class="card-header">
							<i class="icon-wallet"></i> <?php echo T_('Revenues');?>
							<div class="card-header-actions"><div id="revenue_date_picker" class="revenue_number" data-id="reminder"></div></div>
						</div>
						<div id="reminder_show" class="card-body">
							<table id="datatable" class="table table-striped table-bordered datatable">
								<thead>
									<tr>
										<th class="text-center"><?php echo T_('Member ID');?></th>
										<th class="text-center sorting"><?php echo T_('Student name');?></th>
										<th class="text-center sorting"><?php echo T_('Nickname');?></th>
										<th class="text-center no-sort"><?php echo T_('Revenues');?></th>
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
<script src="js/custom/revenue_number.js"></script>
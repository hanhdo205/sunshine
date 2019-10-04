<link rel="stylesheet" href="css/custom/jquery-ui.css">
<script src="js/custom/jquery-ui.js"></script>
<?php if(isset($_SESSION['language']) && $_SESSION['language'] != 'en_US') { ?>
<script src="js/custom/datepicker-<?php echo $datepicker_lang[$_SESSION['language']];?>.js"></script>
<?php } ?>
<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item active"><?php echo T_('Goal Setting');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">
		<div class="row">
		
		  <div class="col-sm-12">
			<div class="card">
			  <div class="card-header"><?php echo T_('Goal Setting');?></div>
				<form action="" method="post">
					<div class="card-body">
					<?php if (isset($_POST["setgoal"])) {
						foreach ($_POST as $key => $value) {
							if(!is_array($value)){
							   $$key = $dbf->filter($value);
							} else {
							   $$key = $value;
							}
						  }
						$saved_goal = $dbf->getDynamic("goal_setting", "year=$selected_year", "CAST(month as unsigned) ASC");
						if($dbf->totalRows($saved_goal)>0) {
							$key = 0;
							while( $update = $dbf->nextData($saved_goal))
							{
							   $array_update = array('year'=>$selected_year,'month'=>$update["month"],'revenue'=>preg_replace('/\D/', '', $revenue[$key]),'customer'=>preg_replace('/\D/', '', $customer[$key]));
							   $key++;
							   $dbf->updateTable("goal_setting", $array_update, "id='" . $update['id'] . "'");
										
							}
						} else {
							for($i=0;$i<12;$i++) {
								$array_new = array('year'=>$selected_year,'month'=>$i,'revenue'=>preg_replace('/\D/', '', $revenue[$i]),'customer'=>preg_replace('/\D/', '', $customer[$i]));
								$dbf->insertTable_2("goal_setting", $array_new);
							}
						}
					} ?>
						<div class="form-group row">
							<div class="col-md-12 pb-3">
								<div class="d-flex">
								  <div class="p-2">
									<input type="hidden" class="selected_year" name="selected_year" value="<?php echo (isset($_POST['selected_year'])) ? $_POST['selected_year'] : '' ;?>">
									<div id="goals_date_picker" class="goal_setting" data-id="customer_datepicker"></div>
								  </div>
								  <div class="ml-auto p-2"><a href="javascript:void(0)" id="editable" class="btn btn-sm btn-secondary"><?php echo T_('Edit');?></a></div>
								</div>
								
							</div>
							<div class="col-md-12">
								<table class="table datatable table-responsive-sm table-bordered table-vcenter">
									<thead>
									  <tr>
										<th></th>
										<th><?php echo T_('Revenue');?></th>
										<th><?php echo T_('Customer');?></th>
									  </tr>
									</thead>
									<tbody>
										<?php for($i=0;$i<12;$i++) {
											echo '<tr>
												<td>' . $month_arr[$i] . '</td>
												<td><div class="input-group"><input class="form-control goal-setting-input revenue_'.$i.'" id="appendedInput" name="revenue[]" size="16" type="text" disabled><div class="input-group-append"><span class="input-group-text">'.T_('JPY').'</span></div></div></td>
												<td><div class="input-group"><input class="form-control goal-setting-input customer_'.$i.'" id="appendedPerson" name="customer[]" size="16" type="text" disabled><div class="input-group-append"><span class="input-group-text">'.T_('Person').'</span></div></div></td>
											  </tr>';
										} ?>
									</tbody>
								</table>
							</div>
						</div>
					</div> <!-- card-body -->	
					<div class="card-footer">
						  <button class="btn btn-warning btn-warn goal-setting-button" type="submit" name="setgoal" disabled>
							 <?php echo T_('Save');?></button>
					</div>
				</form>
			</div> <!-- card -->
		  </div> <!-- col-sm-4 -->
		  <!-- /.col-->
		  </div>
		  <!-- /.row-->
	  </div>
	</div>
  </main>
  <script src="js/custom/custom.js"></script>
  <script src="js/custom/revenue_datepicker.js"></script>
<?php
$id = $_SESSION["member_id"];
$last_month = date('m', strtotime('-1 month', time()));
$this_month = date('m');
$this_month_short = date('n');
$this_year = date('Y');
$last_year = date('Y', strtotime('-2 year'));
?><!-- Plugins and scripts required by this view-->
<script src="vendors/chart.js/js/Chart.min.js"></script>
<script src="vendors/@coreui/coreui-plugin-chartjs-custom-tooltips/js/custom-tooltips.min.js"></script>
<script type="text/javascript">
	var translate = {
		jpy:"<?php echo T_('JPY');?>"
	}
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
		
		<?php if($rowgetInfo["roles_id"]==4) { //admin role ?>
		<div class="row">
		<div class="col-md-6">
				<div class="card">
					<div class="card-header"><i class="fa fa-bar-chart"></i> <?php echo T_('Patients and Quantity in last 12 months');?></div>
						<div class="card-body">
						<?php
							$labels = array();
							$data_patient = array();
							$data_quantity = array();
							$order_by = 'year ASC, month ASC';
							$limit = '0,12';
							$sql = $dbf->getMonthlyRevenue('orders','', 'MONTH(FROM_UNIXTIME(order_date)),YEAR(FROM_UNIXTIME(order_date))',$order_by,$limit);
							while( $row = $dbf->nextData($sql)){
								//printf("<pre>%s</pre>",print_r($row,true));
								$labels[] = '"'.sprintf(T_('%s/%s'),$row['year'],$row['month']).'"';
								$data_patient[] = $row['sum_patient'];
								$data_quantity[] = $row['sum_quantity'];
							}
							$labels = implode(',',$labels);
							$data_patient = implode(',',$data_patient);
							$data_quantity = implode(',',$data_quantity);
						?>
							<canvas id="patient_quantity" class="chartjs"></canvas>
						</div>
				</div>
			</div>
			<!-- /.col-->
		<div class="col-md-6">
			<div class="card">
				<div class="card-header"><i class="fa fa-bar-chart"></i> <?php echo T_('Revenue chart');?></div>
				<div class="card-body">
					<div class="text-center mb-5">
						<div class="btn-group revenue_button" role="group" aria-label="Revenue chart">
							<button type="button" id="seven_days_chart" class="btn btn-light"><?php echo T_('7 days chart');?></button>
							<button type="button" id="thirty_days_chart" class="btn btn-light"><?php echo T_('30 days chart');?></button>
							<button type="button" id="twelve_months_chart" class="btn btn-light"><?php echo T_('12 months chart');?></button>
						</div>
					</div>
					<div id="js_chart_seven_days_chart" class="js_chart_seven_days_chart">
						<canvas id="revenue_seven_days_chart" class="chartjs"></canvas>
					</div>
					<div id="js_chart_thirty_days_chart">
						<canvas id="revenue_thirty_days_chart" class="chartjs"></canvas>
					</div>
					<div id="js_chart_twelve_months_chart">
						<canvas id="revenue_twelve_months_chart" class="chartjs"></canvas>
					</div>
				</div>
			</div>
		</div>
			<div class="col-md-12">
				<div class="card">
					<div class="card-header"><i class="fa fa-line-chart"></i> <?php echo T_('Operator last 6 months line chart');?></div>
						<div class="card-body">
						<?php
							$operator_arr = array();
							$operator_tmp = array();
							$sql_quantity = $dbf->getSum("orders", "operator,operator_id,MONTH(FROM_UNIXTIME(order_date)) AS month,YEAR(FROM_UNIXTIME(order_date)) AS year","quantity", "operator_id<>0 AND YEAR(FROM_UNIXTIME(order_date)) = $this_year", "year DESC, month DESC", "MONTH(FROM_UNIXTIME(order_date)),YEAR(FROM_UNIXTIME(order_date)),operator_id");
							while( $quantity_row = $dbf->nextData($sql_quantity)){
								//printf("<pre>%s</pre>",print_r($quantity_row,true));
								$operator_tmp[$quantity_row['operator_id']][$quantity_row['year'].'_'.$quantity_row['month']] = $quantity_row['year'].'/'.$quantity_row['month'];
								$operator_arr[$quantity_row['operator_id']][$quantity_row['year'].'_'.$quantity_row['month']] = array(
									'name'=>$quantity_row['operator'],
									'sum'=>$quantity_row['value_sum'],
									'yearmonth'=>$quantity_row['year'].'/'.$quantity_row['month']
								);
							}
							//$operator_tmp[125][] = 3;
							//printf("<pre>%s</pre>",print_r($operator_arr,true));
							$key_tmp = 0;
							for ($i = 0; $i < 6; $i++) 
								{
									$m = date("Y_n", strtotime( date( 'Y-m-01' )." -$i months"));
									$months[$m] = date("Y/n", strtotime( date( 'Y-m-01' )." -$i months"));
									foreach($operator_tmp as $key=>$tmp) {
										if($key_tmp != $key) {
											$infor_account_edit = $dbf->getInfoColum("member",$key);
											$name=$infor_account_edit["hovaten"];;
										}
										if(!isset($tmp[$m])) {
											$operator_arr[$key][$m] = array(
													'name'=>$name,
													'sum'=>0,
													'yearmonth'=>$months[$m]
												);
										}
										$key_tmp = $key;
									}
																		
								}
								$months = array_reverse($months);
								
								foreach($operator_arr as $key=>$tmp) {
									//$tmp = array_reverse($tmp);
									ksort($tmp);
									$sum_data = '';
									$name_data = '';
									foreach($tmp as $k=>$v) {
											$sum_data .= $v['sum'] . ',';
											$name_data = $v['name'];
										
									}
									$color = $utl->random_color();
									$data[] =	"{
											backgroundColor: '#".$color."',
											borderColor: '#".$color."',
											data: [".$sum_data."],
											//hidden: true,
											label: '".$name_data."',
											fill: '-1'
										}";
								}
							
							
						?>
							<canvas id="quantity_chart" class="chartjs"></canvas>
						</div>
				</div>
			</div>
			<!-- /.col-->
			
			<div class="col-md-12">
				<div class="card">
					<div class="card-header"><i class="fa fa-line-chart"></i> <?php echo T_('Customer last 6 months line chart');?></div>
						<div class="card-body">
						<?php
							$operator_arr = array();
							$operator_tmp = array();
							$sql_quantity = $dbf->getSum("orders", "company,member_id,MONTH(FROM_UNIXTIME(order_date)) AS month,YEAR(FROM_UNIXTIME(order_date)) AS year","quantity", "YEAR(FROM_UNIXTIME(order_date)) = $this_year", "year DESC, month DESC", "MONTH(FROM_UNIXTIME(order_date)),YEAR(FROM_UNIXTIME(order_date)),member_id");
							while( $quantity_row = $dbf->nextData($sql_quantity)){
								//printf("<pre>%s</pre>",print_r($quantity_row,true));
								$operator_tmp[$quantity_row['member_id']][$quantity_row['year'].'_'.$quantity_row['month']] = $quantity_row['year'].'/'.$quantity_row['month'];
								$operator_arr[$quantity_row['member_id']][$quantity_row['year'].'_'.$quantity_row['month']] = array(
									'name'=>$quantity_row['company'],
									'sum'=>$quantity_row['value_sum'],
									'yearmonth'=>$quantity_row['year'].'/'.$quantity_row['month']
								);
							}
							//$operator_tmp[125][] = 3;
							//printf("<pre>%s</pre>",print_r($operator_tmp,true));
							$key_tmp = 0;
							for ($i = 0; $i < 6; $i++) 
								{
									$m = date("Y_n", strtotime( date( 'Y-m-01' )." -$i months"));
									$customer_months[$m] = date("Y/n", strtotime( date( 'Y-m-01' )." -$i months"));
									
									foreach($operator_tmp as $key=>$tmp) {
										if($key_tmp != $key) {
											$infor_account_edit = $dbf->getInfoColum("member",$key);
											$name=$infor_account_edit["company"];;
										}
										//echo $key_tmp . ' <> ' . $key . '-' . $m . ' = ' . $name . '<br>';
										if(!isset($tmp[$m])) {
											$operator_arr[$key][$m] = array(
													'name'=>$name,
													'sum'=>0,
													'yearmonth'=>$months[$m]
												);
										}
										$key_tmp = $key;
									}
																		
								}
								$customer_months = array_reverse($customer_months);
								
								foreach($operator_arr as $key=>$tmp) {
									ksort($tmp);
									//$tmp = array_reverse($tmp);
									
									//printf("<pre>%s</pre>",print_r($tmp,true));
									$sum_data = '';
									$name_data = '';
									foreach($tmp as $k=>$v) {
											$sum_data .= $v['sum'] . ',';
											$name_data = $v['name'];
										
									}
									$color = $utl->random_color();
									$customer_data[] =	"{
											backgroundColor: '#".$color."',
											borderColor: '#".$color."',
											data: [".$sum_data."],
											//hidden: true,
											label: '".$name_data."',
											fill: '-1'
										}";
								}
							
							
						?>
							<canvas id="customer_quantity_chart" class="chartjs"></canvas>
						</div>
				</div>
			</div>
			<!-- /.col-->
			
		</div>
		<!-- /.row-->
		<div class="row">
			<div class="col-md-6">
				<div class="card">
					<div class="card-header"><i class="fa fa-pie-chart"></i> <?php echo T_('Revenue previous month');?></div>
						<div class="card-body">
							<?php
								$pie_sql = $dbf->getMonthlyRevenue('orders','MONTH(FROM_UNIXTIME(order_date))  = ' . $last_month, 'MONTH(FROM_UNIXTIME(order_date)),YEAR(FROM_UNIXTIME(order_date))',$order_by);
								while( $pie_row = $dbf->nextData($pie_sql)){
									//printf("<pre>%s</pre>",print_r($pie_row,true));
									$sum_total = $pie_row['sum_total'];
									$sum_paid = $pie_row['sum_paid'];
								}
								if ($dbf->totalRows($pie_sql) > 0) {
							?>
									<canvas id="last_month" class="chartjs"></canvas>
								<?php } else { echo T_('No data');} ?>
						</div>
				</div>
			</div>
			<!-- /.col-->
			
			<div class="col-md-6">
				<div class="card">
					<div class="card-header"><i class="fa fa-pie-chart"></i> <?php echo T_('Revenue this month');?></div>
						<div class="card-body">
							<?php
								
								$current_pie_sql = $dbf->getMonthlyRevenue('orders','MONTH(FROM_UNIXTIME(order_date))  = ' . $this_month, 'MONTH(FROM_UNIXTIME(order_date)),YEAR(FROM_UNIXTIME(order_date))',$order_by);
								while( $current_row = $dbf->nextData($current_pie_sql)){
									//printf("<pre>%s</pre>",print_r($current_row,true));
									$current_total = $current_row['sum_total'];
									$current_paid = $current_row['sum_paid'];
								}
								if ($dbf->totalRows($current_pie_sql) > 0) {
							?>
									<canvas id="this_month" class="chartjs"></canvas>
								<?php } else { echo T_('No data');} ?>
						</div>
				</div>
			</div>
			<!-- /.col-->
		</div>
		<!-- /.row-->
		<script>
		<?php foreach($months as $month) {
			$m_y = explode('/',$month);
			$m = $m_y[1];
			$y = $m_y[0];
			$new_months[] = sprintf(T_('%s/%s'),$y,$m);
		}?>
		<?php foreach($customer_months as $c_month) {
			$c_m_y = explode('/',$c_month);
			$c_m = $c_m_y[1];
			$c_y = $c_m_y[0];
			$c_new_months[] = sprintf(T_('%s/%s'),$c_y,$c_m);
		}?>
		var data = {
			labels: ['<?php echo implode("','",$new_months);?>'],
			datasets: [<?php echo implode(",",$data);?>]
		};
		
		var customer_data = {
			labels: ['<?php echo implode("','",$c_new_months);?>'],
			datasets: [<?php echo implode(",",$customer_data);?>]
		};

		var options = {
			maintainAspectRatio: false,
			spanGaps: false,
			elements: {
				line: {
					tension: 0.000001
				}
			},
			scales: {
				yAxes: [{
					stacked: true
				}]
			},
			plugins: {
				filler: {
					propagate: false
				},
				'samples-filler-analyser': {
					target: 'chart-analyser'
				}
			}
		};
			var line_chart = document.getElementById("quantity_chart").getContext('2d');
			var chart = new Chart(line_chart, {
				type: 'line',
				data: data,
				options: options
			});
			
			var customer_line_chart = document.getElementById("customer_quantity_chart").getContext('2d');
			var chart = new Chart(customer_line_chart, {
				type: 'line',
				data: customer_data,
				options: options
			});
			var ctx1 = document.getElementById("patient_quantity").getContext('2d');
			var lineChart = new Chart(ctx1, {
				type: 'bar',
				data: {
					labels: [<?php echo $labels;?>],
					datasets: [{
							label: '<?php echo T_('Number of Patient');?>',
							backgroundColor: '#013976',
							data: [<?php echo $data_patient;?>] 
						}, {
							label: '<?php echo T_('Quantity');?>',
							backgroundColor: '#EBEFF3',
							data: [<?php echo $data_quantity;?>]
						}]
						
				},
				options: {
					legend: {
						display: false
					},
					tooltips: {
						mode: 'index',
						intersect: false
					},
					responsive: true,
					scales: {
						xAxes: [{
							stacked: true,
						}],
						yAxes: [{
							stacked: true
						}]
					}
				}
			});
		</script>
		
		<?php } elseif($rowgetInfo["roles_id"]==6 || $rowgetInfo["roles_id"]==7 ) { // manager or operator role ?>
		
		<?php
			if($rowgetInfo["roles_id"]==6) $where = 'manager_id=' . $id;
			else $where = 'operator_id=' . $id;
			// assigned
			$result = $dbf->getCount("orders", "id", $where, "id DESC");
			if ($dbf->totalRows($result) > 0) {
				$row = $dbf->nextData($result);
			}
			$total_order = $dbf->getCount("orders", "id", "", "id DESC");
			if ($dbf->totalRows($total_order) > 0) {
				$total_row = $dbf->nextData($total_order);
			}
			$assigned = $row['value_count'];
			$total_order = $total_row['value_count'];
			$order_percent = $assigned * 100 / $total_order;
			
			// order per day
			$now = time();
			$date_sql = $dbf->getDynamic("orders", $where, "ABS(UNIX_TIMESTAMP(order_date) - UNIX_TIMESTAMP(NOW())) ASC LIMIT 1");
			if ($dbf->totalRows($date_sql) > 0) {
				  while ($date_row = $dbf->nextData($date_sql)) {
					  $first_order_date = $date_row['order_date'];
				  }
			}
			
			$total_date_sql = $dbf->getDynamic("orders", "", "ABS(UNIX_TIMESTAMP(order_date) - UNIX_TIMESTAMP(NOW())) ASC LIMIT 1");
			if ($dbf->totalRows($total_date_sql) > 0) {
				  while ($total_date_row = $dbf->nextData($total_date_sql)) {
					  $total_first_order_date = $total_date_row['order_date'];
				  }
			}
			$total_datediff = $now - $total_first_order_date;
			$total_days = round($total_datediff / (60 * 60 * 24));
			$total_order_per_day = $total_order/$total_days;
			
			$datediff = $now - $first_order_date;
			$days = round($datediff / (60 * 60 * 24));
			$order_per_day = $assigned/$days;
			
			$percent_order_perday = $order_per_day * 100 / $total_order_per_day;
			
			// revenue
			$revenue = $dbf->getSum("orders", "id","total", $where, "id DESC");
			if ($dbf->totalRows($revenue) > 0) {
				$revenue_row = $dbf->nextData($revenue);
			}
			
			$total_revenue = $dbf->getSum("orders", "id","total", "", "id DESC");
			if ($dbf->totalRows($total_revenue) > 0) {
				$total_revenue_row = $dbf->nextData($total_revenue);
			}
			
			$self_revenue = $revenue_row['value_sum'];
			$all_revenue = $total_revenue_row['value_sum'];
			
			$percent_revenue = $self_revenue * 100 / $all_revenue;
			
			// patient
			$patient = $dbf->getSum("orders", "id","no_patient", $where, "id DESC");
			if ($dbf->totalRows($patient) > 0) {
				$patient_row = $dbf->nextData($patient);
			}
			
			$total_patient = $dbf->getSum("orders", "id","no_patient", "", "id DESC");
			if ($dbf->totalRows($total_patient) > 0) {
				$total_patient_row = $dbf->nextData($total_patient);
			}
			
			$self_patient = $patient_row['value_sum'];
			$all_patient = $total_patient_row['value_sum'];
			
			$percent_patient = $self_patient * 100 / $all_patient;
			
			// teeth
			$teeth = $dbf->getSum("orders", "id","quantity", $where, "id DESC");
			if ($dbf->totalRows($teeth) > 0) {
				$teeth_row = $dbf->nextData($teeth);
			}
			
			$total_teeth = $dbf->getSum("orders", "id","quantity", "", "id DESC");
			if ($dbf->totalRows($total_teeth) > 0) {
				$total_teeth_row = $dbf->nextData($total_teeth);
			}
			
			$self_teeth = $teeth_row['value_sum'];
			$all_teeth = $total_teeth_row['value_sum'];
			
			$percent_teeth = $self_teeth * 100 / $all_teeth;

		?>
		
		<div class="card-group mb-4">
			<div class="card">
			  <div class="card-body">
				<div class="h1 text-muted text-right mb-4">
				  <i class="icon-pie-chart"></i>
				</div>
				<div class="text-value"><?php echo number_format($assigned,0);?></div>
				<small class="text-muted text-uppercase font-weight-bold"><?php echo T_('Assigned');?></small>
				<div class="progress progress-xs mt-3 mb-0">
				  <div class="progress-bar bg-info" role="progressbar" style="width: <?php echo $order_percent;?>%" aria-valuenow="<?php echo $assigned;?>" aria-valuemin="0" aria-valuemax="<?php echo $total_order;?>"></div>
				</div>
			  </div>
			</div>
			<div class="card">
			  <div class="card-body">
				<div class="h1 text-muted text-right mb-4">
				  <i class="icon-clock"></i>
				</div>
				<div class="text-value"><?php echo number_format($order_per_day,0);?></div>
				<small class="text-muted text-uppercase font-weight-bold"><?php echo T_('Order per day');?></small>
				<div class="progress progress-xs mt-3 mb-0">
				  <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $percent_order_perday;?>%" aria-valuenow="<?php echo $order_per_day;?>" aria-valuemin="0" aria-valuemax="<?php echo $total_order_per_day;?>"></div>
				</div>
			  </div>
			</div>
			<div class="card">
			  <div class="card-body">
				<div class="h1 text-muted text-right mb-4">
				  <i class="icon-calculator"></i>
				</div>
				<div class="text-value"><?php echo sprintf(T_('%s JPY'),number_format($self_revenue,0));?></div>
				<small class="text-muted text-uppercase font-weight-bold"><?php echo T_('Revenue');?></small>
				<div class="progress progress-xs mt-3 mb-0">
				  <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $percent_revenue;?>%" aria-valuenow="<?php echo $self_revenue;?>" aria-valuemin="0" aria-valuemax="<?php echo $all_revenue;?>"></div>
				</div>
			  </div>
			</div>
			<div class="card">
			  <div class="card-body">
				<div class="h1 text-muted text-right mb-4">
				  <i class="icon-people"></i>
				</div>
				<div class="text-value"><?php echo number_format($self_patient,0);?></div>
				<small class="text-muted text-uppercase font-weight-bold"><?php echo T_('Number of Patient');?></small>
				<div class="progress progress-xs mt-3 mb-0">
				  <div class="progress-bar" role="progressbar" style="width: <?php echo $percent_patient;?>%" aria-valuenow="<?php echo $self_patient;?>" aria-valuemin="0" aria-valuemax="<?php echo $all_patient;?>"></div>
				</div>
			  </div>
			</div>
			<div class="card">
			  <div class="card-body">
				<div class="h1 text-muted text-right mb-4">
				  <i class="icon-diamond"></i>
				</div>
				<div class="text-value"><?php echo number_format($self_teeth,0);?></div>
				<small class="text-muted text-uppercase font-weight-bold"><?php echo $self_teeth > 1 ? T_('teeths') : T_('teeth');?></small>
				<div class="progress progress-xs mt-3 mb-0">
				  <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo $percent_teeth;?>%" aria-valuenow="<?php echo $self_teeth;?>" aria-valuemin="0" aria-valuemax="<?php echo $all_teeth;?>"></div>
				</div>
			  </div>
			</div>
		  </div>
		  
		  <div class="row">
			<div class="col-md-6">
				<div class="card">
					<div class="card-header"><i class="fa fa-pie-chart"></i> <?php echo T_('Revenue previous month');?></div>
						<div class="card-body">
							<?php
								
								$pie_sql = $dbf->getMonthlyRevenue('orders','MONTH(FROM_UNIXTIME(order_date))  = ' . $last_month  . ' AND ' . $where, 'MONTH(FROM_UNIXTIME(order_date)),YEAR(FROM_UNIXTIME(order_date))',$order_by);
								while( $pie_row = $dbf->nextData($pie_sql)){
									//printf("<pre>%s</pre>",print_r($pie_row,true));
									$sum_total = $pie_row['sum_total'];
									$sum_paid = $pie_row['sum_paid'];
								}
								if ($dbf->totalRows($pie_sql) > 0) {
							?>
									<canvas id="last_month" class="chartjs"></canvas>
								<?php } else { echo T_('No data');} ?>
						</div>
				</div>
			</div>
			<!-- /.col-->
			
			<div class="col-md-6">
				<div class="card">
					<div class="card-header"><i class="fa fa-pie-chart"></i> <?php echo T_('Revenue this month');?></div>
						<div class="card-body">
							<?php
								$current_pie_sql = $dbf->getMonthlyRevenue('orders','MONTH(FROM_UNIXTIME(order_date))  = ' . $this_month  . ' AND ' . $where, 'MONTH(FROM_UNIXTIME(order_date)),YEAR(FROM_UNIXTIME(order_date))',$order_by);
								while( $current_row = $dbf->nextData($current_pie_sql)){
									//printf("<pre>%s</pre>",print_r($current_row,true));
									$current_total = $current_row['sum_total'];
									$current_paid = $current_row['sum_paid'];
								}
								if ($dbf->totalRows($current_pie_sql) > 0) {
							?>
									<canvas id="this_month" class="chartjs"></canvas>
								<?php } else { echo T_('No data');} ?>
						</div>
				</div>
			</div>
			<!-- /.col-->
		</div>
		<!-- /.row-->
		
		<?php } elseif($rowgetInfo["roles_id"]==15) { // customer role ?>
		<div class="row">
		  <div class="col-md-12">
			<div class="card">
			  <div class="card-header"><?php echo T_('Recent Your Orders');?></div>
			  <div class="card-body">
				
				<table class="table table-responsive-sm table-bordered table-vcenter">
					<thead>
					  <tr>
						<th><?php echo T_('Order Date');?></th>
						<th><?php echo T_('Order #');?><br><?php echo T_('Patients');?></th>
						<th><?php echo T_('Item');?></th>
						<th><?php echo T_('Status');?></th>
						<th></th>
					  </tr>
					</thead>
					<tbody>
					<?php
						$order_array = array();
						$result = $dbf->getDynamic("orders", "member_id=$id", "id DESC LIMIT 3");
						if ($dbf->totalRows($result) > 0) {
								$list = array();
							  while ($row = $dbf->nextData($result)) {
								  $list[] = $row["id"];
							  }
						}
						$id_list = implode(',',$list);
						$limit = '';
						$orders_query = $dbf->getjoinDynamic("orders","order_detail","tb1.id = tb2.order_id","tb1.id IN ($id_list) ","","FIELD(tb1.id, $id_list)",$limit);
						while( $orders = $dbf->nextData($orders_query)){
							//printf("<pre>%s</pre>",print_r($orders,true));
							$order_array[$orders["order_id"]][] = array(
									"order_date"=>$orders["order_number"],
									"patient_name"=>$orders["patient_name"],
									"production"=>$orders["production"],
									"status"=>$orders["status"],
							);
						}
						
						$status_arr = array('0'=>T_('<span class="badge badge-danger">Pending</span>'),'1'=>T_('<span class="badge badge-warning text-white">Assigning</span>'),'2'=>T_('<span class="badge badge-warning text-white">Assigning</span>'),'3'=>T_('<span class="badge badge-primary">Processing</span>'),'4'=>T_('<span class="badge badge-info text-white">Completed</span>'),'5'=>T_('<span class="badge badge-success">Delivered</span>'));
						foreach($order_array as $key=>$value) {
							if($key==0) {
								foreach($value as $k=>$v) {
									echo '<tr>
										<td>' . date("Y/m/d H:i",$v['order_date']) . '</td>
										<td>' . $v['order_date'] . '<br>' . $v['patient_name'] . '</td>
										<td>' . $v['production'] . '</td>
										<td>
										  <span class="badge badge-warning">' . T_($status_arr[$v['status']]) . '</span>
										</td>
										<td><a href="booking-detail.aspx/?order_id='.$v['order_date'].'">' . T_('Details'). '</a></td>
									  </tr>';
								}
							} else {
								$patient_name=array();
								$production=array();
								//$status=array();
								
								$status=$status_arr[$value[0]['status']];
								foreach($value as $k=>$v) {
									$patient_name[]=$v['patient_name'];
									$production[]=$product_item[$v['production']];
									//$status[]=$v['status'];
								}
								echo '<tr>
										<td>' . date("Y/m/d H:i",$value[0]['order_date']) . '</td>
										<td>' . $value[0]['order_date'] . '<br>' . implode('<br>',$patient_name) . '</td>
										<td>' . implode('<br>',$production) . '</td>
										<td>' . $status . '</td>
										<td><a href="booking-detail.aspx/?order_id='.$value[0]['order_date'].'">' . T_('Details'). '</a></td>
									  </tr>';
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
		  <div class="row">
		  	
			<div class="col-md-12">
				<div class="card">
						<div class="card-header"><?php echo T_('VietQuocLab News');?></div>
						<div class="card-body row">
										
				<?php 
						$sql = "SELECT * FROM informations WHERE status=1";
						$currentPage = 1;
						if(isset($_GET['pageNumber'])){
							$currentPage = $_GET['pageNumber'];
						}
						$startPage = ($currentPage-1)*PERPAGE_LIMIT;
						if($startPage < 0) $startPage = 0;
						$href = "default.aspx?";
						
						$result = $dbf->getLastNews("informations","status=1","id desc",$startPage . "," . PERPAGE_LIMIT);
						$informations = $dbf->totalRows($result);
						
						if($informations>0)
						{
							$str="";
							$i=1;
							$trans = $lang[$_SESSION['language']];
							while( $row = $dbf->nextData($result))
							{
								$questions[] = $row;
								$get_the_date = $utl->time_ago($row["datecreated"]);
								$title = unserialize($row["title"]);
								$content = unserialize($row["content"]);
								preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $content, $image);
								$featured_image = $image['src'];
							?>
								<?php if($title[$trans]!='') { ?>
									<div class="col-md-2"></div>
									<div class="col-md-8 custom-border-bottom">
									
											<a href="news-detail.aspx?id=<?php echo $row["id"];?>">
											<div class="media">
												<?php echo $featured_image ? '<img class="mr-3" src="'.$featured_image.'" alt="'.$title[$trans].'" width="100px">' : '';?>
													<div class="media-body">
														<h5 class="mt-0"><?php echo $title[$trans];?></h5>
														<?php echo $utl->shorten_text($content[$trans],200,'...',true);?>
													</div>
											</div>
											<p class="card-text text-right"><small class="text-muted"><?php echo $get_the_date;?></small></p>
											</a>
									
									</div>
									<div class="col-md-2"></div>
								<?php } ?>	
							<?php								
								
							}
							if(is_array($questions)){
								$questions["page_links"] = $dbf->paginateResults($sql,$href);
							}
						}
					?>
						
					
				  </div>
				  <div class="card-footer text-center">
						<nav aria-label="...">
							<ul class="pagination d-flex justify-content-end">
								<?php echo $questions["page_links"]; ?>
							</ul>
						</nav>
						</div>
				</div>
				
			</div>
		
			<!-- /.col-->
		</div>
		<!-- /.row-->
		<?php } ?>
	  </div>
	</div>
  </main>
  
  <?php if($rowgetInfo["roles_id"]<15) { ?>
  <script src="js/custom/custom.js"></script>
  <script>
	<?php if ($dbf->totalRows($pie_sql) > 0) { ?>
		var pieChart = new Chart($('#last_month'), {
		  type: 'pie',
		  data: {
			labels: ['<?php echo T_('Revenue');?>', '<?php echo T_('Received');?>'],
			datasets: [{
			  data: [<?php echo $sum_total;?>,<?php echo $sum_paid;?>],
			  backgroundColor: ['#FF6384', '#36A2EB'],
			  hoverBackgroundColor: ['#FF6384', '#36A2EB']
			}]
		  },
		  options: {
			responsive: true
		  }
		});
	<?php } ?>
	<?php if ($dbf->totalRows($current_pie_sql) > 0) { ?>
		var currentpieChart = new Chart($('#this_month'), {
		  type: 'pie',
		  data: {
			labels: ['<?php echo T_('Revenue');?>', '<?php echo T_('Received');?>'],
			datasets: [{
			  data: [<?php echo $current_total;?>,<?php echo $current_paid;?>],
			  backgroundColor: ['#FF6384', '#36A2EB'],
			  hoverBackgroundColor: ['#FF6384', '#36A2EB']
			}]
		  },
		  options: {
			responsive: true
		  }
		});
	<?php } ?>
	</script>
	<?php } ?>
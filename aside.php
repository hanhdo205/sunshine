<aside class="aside-menu">
	<ul class="nav nav-tabs" role="tablist">
	  <li class="nav-item">
		<a class="nav-link active" data-toggle="tab" href="#timeline" role="tab">
		  <i class="icon-list"></i>
		</a>
	  </li>
	  <?php if($rowgetInfo["roles_id"]<6) { ?>
	  <!--
	  <li class="nav-item">
		<a class="nav-link" data-toggle="tab" href="#messages" role="tab">
		  <i class="icon-speech"></i>
		</a>
	  </li>
	  !-->
	  <?php } ?>
	</ul>
	<!-- Tab panes-->
	<div class="tab-content">
	  <div class="tab-pane active" id="timeline" role="tabpanel">
		<div class="list-group list-group-accent">
		  <div class="list-group-item list-group-item-accent-secondary bg-light text-center font-weight-bold text-muted text-uppercase small"><?php echo T_('Today tasks');?></div>
		  <?php
			$totalTask = 0;
			$current_member_id=$rowgetInfo["id"];
				$begin = date('Y/m/d 00:00', time());
				$end = date('Y/m/d 23:59', time());
				$start_date = strtotime($begin);
				$end_date = strtotime($end);
				switch(true) {
					case $rowgetInfo["roles_id"] < 6 :
						$new_task = $dbf->getDynamic("orders", "dateupdated BETWEEN $start_date AND $end_date", "dateupdated DESC");
						$totalTask = $dbf->totalRows($new_task);
						break;
					case $rowgetInfo["roles_id"] == 6 :
						$new_task = $dbf->getDynamic("orders", "(manager_id=$current_member_id OR manager_id=0) AND (dateupdated BETWEEN $start_date AND $end_date)", "dateupdated DESC");
						$totalTask = $dbf->totalRows($new_task);
						break;
					case $rowgetInfo["roles_id"] == 7 :
						$new_task = $dbf->getDynamic("orders", "(dateupdated BETWEEN $start_date AND $end_date) AND operator_id=$current_member_id", "dateupdated DESC");
						$totalTask = $dbf->totalRows($new_task);
						break;
					default:
						break;
					
				}
				
				if($totalTask>0){
					$tasks=array();
					
					while( $task = $dbf->nextData($new_task))
						{ 
							$task_status = '-warning';
							$task_icon = "";
							switch(true) {
								case $rowgetInfo["roles_id"] < 6 :
									if($task['status']!=0) {
										$task_status = '-success';
										$task_icon = "-user-following";
									} else {
										$task_icon = "-user";
									}
								break;
								case $rowgetInfo["roles_id"] == 6 :
									if($task['status']!=1) {
										$task_status = '-success';
										$task_icon = "-user-following";
									} else {
										$task_icon = "-user";
									}
								break;
								case $rowgetInfo["roles_id"] == 7 :
									if($task['status']!=2) {
										$task_status = '-success';
										$task_icon = "-arrow-up-circle";
									} else {
										$task_icon = "-arrow-down-circle";
									}
								break;
							}
					?>
							<div class="list-group-item list-group-item-accent<?php echo $task_status;?> list-group-item-divider">
								<a href="order-detail.aspx/?order_id=<?php echo $task['id'];?>" class="aside-link">
									<div class="avatar float-right pt-2">
									  <i class="icon<?php echo $task_icon;?> icons font-2xl"></i>
									</div>
									<div>#<?php echo $task['order_number'];?>
									</div>
									<small class="text-muted mr-3">
									  <i class="icon-calendar"></i>  <?php echo $utl->time_ago($task['dateupdated']);?></small>
								</a>
							  </div>
						   
						<?php }
				}
		  ?>
		 
		</div>
	  </div>
	 <?php if($rowgetInfo["roles_id"]<6) { ?>
	 <div class="tab-pane" id="messages" role="tabpanel">
		<div class="list-group list-group-accent">
		  <div class="list-group-item list-group-item-accent-secondary bg-light text-center font-weight-bold text-muted text-uppercase small"><?php echo T_('Today messages');?></div>
		  <?php
			$totalMessage = 0;
				switch(true) {
					case $rowgetInfo["roles_id"] < 6 :
						$new_message = $dbf->getDynamic("contact_form", "datecreated BETWEEN $start_date AND $end_date", "id DESC");
						$totalMessage = $dbf->totalRows($new_message);
						break;
					default:
						break;
					
				}
				
				if($totalMessage>0){
					$messages=array();
					
					while( $messages = $dbf->nextData($new_message))
						{ 
							switch($messages['is_read']){
								case 'yes':
									$mess_status = "-success";
									$mess_icon = "-open";
									break;
								default:
									$mess_status = "-warning";
									$mess_icon = "";
									break;
							}
							?>
							<div class="list-group-item list-group-item-accent<?php echo $mess_status;?> list-group-item-divider">
								<a href="message-detail.aspx/?mess_id=<?php echo $messages['id'];?>" class="aside-link">
									<div class="pt-3 mr-3 float-left">
										<div class="envelope-icon">
											<i class="icon-envelope<?php echo $mess_icon;?> font-2xl mr-2"></i>
										</div>
									</div>
									<div>
										<small class="text-muted"><?php echo $messages['contact_name'];?></small>
										<small class="text-muted float-right mt-1"><?php echo $utl->time_ago($messages['datecreated']);?></small>
									</div>
									<small class="text-muted"><?php echo $utl->shorten_text($messages['message'],100,'...',true);?></small>
								</a>
							</div>
						<?php }
				}
		  ?>
		 
		</div>
	  </div>
	 <?php } ?>
	</div>
</aside>
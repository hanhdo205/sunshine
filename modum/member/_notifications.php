<?php
date_default_timezone_set('Asia/Bangkok');
if($_SESSION["Free"]==1)
{
  $html->redirectURL("/system");
}else
{
?>
<div class="row">
		<div class="col-md-12">

			<div class="card">
				<div class="card-header">
					<strong class="card-title"><?php echo _NOTIFICATIONS;?></strong>
				</div>
				<div class="card-body">
				    <?php 
						$result = $dbf->getDynamic("notifications","(is_general =1 or member_id = '".$rowgetInfo["id"]."') and status=1","is_read desc,id desc");
						$totalNotifi = $dbf->totalRows($result);
						
						if($totalNotifi>0)
						{
							$dbf->updateNotofication("notifications","(`member_id`=".$rowgetInfo["id"]." OR `member_id`=0)");
							$str="";
							$i=1;
							while( $row = $dbf->nextData($result))
							{
								$alerts = array(
									'yes' => 'secondary',
									'no' => 'primary'
								);
							?>
							
							<div class="sufee-alert alert with-close alert-<?php echo $alerts[$row["is_read"]];?> alert-dismissible fade show">
									<span class="badge badge-pill badge-primary"><?php echo $row["title"];?></span>
									<?php echo $row["content"];?>
									<button type="button" class="close" data-dismiss="alert" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
							   
							<?php								
								
							}
						}
					?>
				</div>
			</div>
		</div>
	</div>
						
<?php 
}
include("inc/footer.php");
?>
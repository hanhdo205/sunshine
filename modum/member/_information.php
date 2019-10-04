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
					<strong class="card-title">Informations</strong>
				</div>
				<div class="card-body">
				    <?php 
						$result = $dbf->getDynamic("informations","status=1","id desc");
						$informations = $dbf->totalRows($result);
						
						if($informations>0)
						{
							$str="";
							$i=1;
							while( $row = $dbf->nextData($result))
							{
							?>
							
							   <div class="sufee-alert alert with-close alert-primary alert-dismissible fade show">
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
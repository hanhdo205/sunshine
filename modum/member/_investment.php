<style>
 .btn-primary 
 {
    color: #fff;
    background-color: #121734;
 }
 
 .btn-warning 
 {
    color: #212529;
    background-color: #DDAF3F;
    border-color: #ffc107;
 }
</style>

<div class="card">
	<div class="card-header">
		<strong class="card-title">INVESTMENT</strong>
	</div>
	
	<div class="card-body">	 
	<div class="row">
	
	<?php
		
		$totalMaxout250 		= $dbf->CheckMaxoutIncome250($rowgetInfo["sponser_id"]);
		if((int)$rowgetInfo["max_out"]==0)
		{
			$totalMaxout250_member  = ($arrayPackeges[$rowgetInfo["packages_id"]]["price"]*250)/100;
		}else
		{
			$totalMaxout250_member = $rowgetInfo["max_out"];
		}	
	
	    foreach($arrayPackeges as $valuePackeges)
		{
			if($valuePackeges["status"]==1)
			{             	
	?>	        
		
			<div class="col-md-4 item-buy-1" style="cursor: pointer;">
				<!-- Widget: user widget style 1 -->
				<div class="card">
				<div class="box box-widget widget-user">
					<!-- Add the bg color to the header using any of the bg-* classes -->
					<div class="widget-user-header">
						<h3 class="widget-user-username"><span style="float:left; padding: 10px; color:<?php echo $valuePackeges["color"]; ?>"><?php echo $valuePackeges["title"];?></span><span style="float:right;padding: 10px;color:<?php echo $valuePackeges["color"]; ?>">$<?php echo number_format(($valuePackeges["price"]+10));?></span><br style="clear:both"/></h3>						
					</div>
					<div class="widget-user-image">
						<img class="img-circle" src="<?php echo $valuePackeges["picture"];?>" alt="User Avatar">
					</div>
					<div class="box-footer">
						<div class="row">
							<div class="col-sm-4">
								<div class="description-block">									
									<h5 class="description-header">&nbsp;</h5>									
								</div>
								<!-- /.description-block -->
							</div>
							<!-- /.col -->
							<div class="col-sm-4">
								<div class="description-block">
									<h5 class="description-header">&nbsp;</h5>
									<span class="description-text">&nbsp;</span>
								</div>
								<!-- /.description-block -->
							</div>
							<!-- /.col -->
							<div class="col-sm-4">
								<div class="description-block">
									<h5 class="description-header">&nbsp;</h5>
								</div>
								<!-- /.description-block -->
							</div>
							<!-- /.col -->
						</div>
						<!-- /.row -->
					</div>					
					<br>
					
					<div class="text-center" style="margin-top: 50px;margin-bottom:30px;">
					    <?php 
						if(($rowgetInfo["status"]==0 && (int)$rowgetInfo["packages_id"]==0) || ($totalMaxout250>=$totalMaxout250_member))
						{
					    ?>
					   <button type="button" class="btn btn-primary" onclick="window.location.href='/active-by-request.aspx?package=<?php echo $valuePackeges["id"];?>'">Invesment Now</button>
						<?php } ?> 
					</div>
					
				</div>
				</div>
				<!-- /.widget-user -->
			</div>
		
		<?php	
			}
			}
		?>
 </div>	
 </div>
</div>
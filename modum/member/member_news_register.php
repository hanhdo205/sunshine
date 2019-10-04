<style>
        .marquee {
            width: 100%;
            overflow: hidden;
            border: 1px solid #ccc;
            color: rgb(0, 0, 0);
        }

        ul.marquee li {
            display: inline-block; 
            padding: 10px 20px; 
        }
        
       @-webkit-keyframes marqueeAnimation-3416432  { 100%  {margin-left:-2815px}}
</style>

<div class="row">					
		<div class="card-body col-lg-12" style="padding:0px;">
			<div class="sufee-alert alert with-close alert-info alert-dismissible fade show" style="padding:15px 2px 15px;">
				<span class="badge badge-pill badge-primary"><i class="fa fa-user"></i>&nbsp;&nbsp;New members</span>
				<div class="box-body">
					<ul class="marquee">
                            <?php 
								$result_ml = $dbf->getDynamic("member","1=1","id desc");
						        $total_ml = $dbf->totalRows($result_ml);
						
								if($total_ml>0)
								{									
									
									while( $row = $dbf->nextData($result_ml))
									{
										if($row["country_id"]!=0){
											
										  $info_contry = $dbf->getInfoColum("countries",$row["country_id"]);
										 
								?> 					
									<li class="item-new-mem"><img src="http://flagpedia.net/data/flags/h80/<?php echo strtolower($info_contry["countries_iso_code_2"]);?>.png" width="20px">&nbsp;&nbsp;<?php echo $row["tendangnhap"];?></li>
								<?php
									}
								}
								}
								?>		
					</ul>
                 </div>								
				<button type="button" class="close" data-dismiss="alert" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</div>
		</div>
	</div>


<script src="js/jquery.marquee.min.js"></script>
<script>
$(function () {
$('.marquee').marquee({
	duration: 15000,
	duplicate: true,
	//pauseOnHover: true
});
});
</script>

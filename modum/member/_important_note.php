<?php if($info["on_off"]=="on") { ?>
<div class="row">					
	<div class="card-body col-lg-12" style="padding:0px;">
		<div class="sufee-alert alert with-close alert-primary alert-primary_note alert-dismissible fade show">
			
				<div class="row row-flex-carousel">
				<div class="js-notification-content col-md-11">
				<div class="row row-flex-carousel">
				<div class="col-md-3 text-center mb-10">
				<i class="icon-danger-sign fa-5x" aria-hidden="true"></i>
				</div>
				<div class="col-md-9">
				<strong class="text-uppercase">IMPORTANT NOTE!</strong>
				<?php echo $info["IMPORTANT_NOTE"];?>
				</div>
				</div>
				</div>
				</div>
			
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">×</span>
			</button>
		</div>
	</div>
</div>
<?php } ?>



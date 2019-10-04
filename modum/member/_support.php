<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item active"><?php echo T_('Support');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">
		<div class="row">
		  <div class="col-md-12">
		  
			<div class="card">
			  <div class="card-header"><?php echo T_('Support');?></div>
				<div class="card-body">
					<p><?php echo T_('If you have any questions , please feel free to get in touch via');?></p>
					<?php echo T_('Email');?>: <a href="mailto:<?php echo $info["EMAIL_SUPPORT"]?>"> <?php echo $info["EMAIL_SUPPORT"]?> </a>
				</div> <!-- card-body -->
			</div> <!-- card -->
		  </div> <!-- col-md-12 -->
		  <!-- /.col-->
		  </div>
		  <!-- /.row-->
	  </div>
	</div>
  </main>
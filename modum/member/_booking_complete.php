<?php $item = $_GET['item'];?>
<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item">
		<a href="booking.aspx"><?php echo T_('Order');?></a>
	  </li>
	  <li class="breadcrumb-item active"><?php echo T_('Order complete');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">
		<div class="row">
		  <div class="col-md-12">
		  
			<div class="card">
			  <div class="card-header"><?php echo T_('Order complete');?></div>
			  <div class="card-body">
				  <div class="jumbotron jumbotron-fluid customer-jum">
					<div class="container text-center">
						<!-- <p><?php 
						if($item > 1) {
							echo sprintf(T_('Thank you for order "%d" items.'),(int) $item);
						} else {
							echo sprintf(T_('Thank you for order "%d" item.'),(int) $item);
						}?></p> -->
						<div class="text-center mb-3">
               <!--  <img class="navbar-brand-full" src="images/logo.jpg" width="200" height="50" alt="Vietquoc Logo"> -->
              </div>
            <h1 class="text-center"><?php echo T_('Thank you for your order');?></h1>
			<!--<p><?php echo T_('We have sent the order confirmation email to your registered email address.');?></p>-->
			<hr>
			<div class="text-center mt-4">
			<p><a href="/system.aspx"><?php echo T_('Back to Home');?></a></p>
					</div>
				  </div>

					</div> <!-- card-body -->	
				   
			</div> <!-- card -->
		  </div> <!-- col-md-12 -->
		  <!-- /.col-->
		  </div>
		  <!-- /.row-->
	  </div>
	</div>
  </main>
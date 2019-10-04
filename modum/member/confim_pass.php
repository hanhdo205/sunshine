<?php

if( $_SESSION["Free"]==1)
{
  $html->redirectURL("/system");
}else
{


?>
<link href="/css/system/template/css/bootstrap.min.css" rel="stylesheet">
<link href="/css/system/template/css/main.css" rel="stylesheet">
<script src="/css/system/template/js/vendor/modernizr-2.8.3.min.js"></script>
<?php include("inc/header.php");?>

<section id="main">
	<!-- WRAP -->
	<div class="wrap">

     <!-- USERMENU -->
		<aside id="user-menu">
            <?php include("inc/sidebar.php");?>
		</aside>
		<!-- /USERMENU -->
		<!-- CONTENT -->
	 <section id="content">
            <div id="main-container">
                  <div id="page-content" style="min-height: 318px;">
                      <div class="block">
                           <div class="block-title">
                              <h2>Confirm by password</h2>
                           </div>
                           <form action="" method="post" class="form-horizontal form-bordered">
                              <div class="form-group">
                                 <div class="row">
                                    <label class="col-md-3 control-label" for="pass2">Password 2 <span class="text-danger">*</span></label>
                                    <div class="col-md-6"> <input type="password" id="pass2" name="pass2" class="form-control"> </div>
                                 </div>
                              </div>
                              <div class="form-group form-actions">
                                 <div class="col-md-9 col-md-offset-3"> <button type="submit" name="submit" class="btn btn-effect-ripple btn-primary" style="overflow: hidden; position: relative;">Confirm</button> </div>
                              </div>
                           </form>
                        </div>
                  </div>
            </div>
            <div class="clearfix"></div>
     </section>
</div>
<div class="clearfix"></div>
</section>
<div class="clearfix"></div>
<?php include("inc/footer.php");?>
<?php
}
?>


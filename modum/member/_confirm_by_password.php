<?php
if( $_SESSION["Free"]==1)
{
  $html->redirectURL("/system");
}else
{
?>
<link href="/css/system/template/css/main.css" rel="stylesheet">

<section id="main">
	<!-- WRAP -->
	<div class="wrap">
	 <!-- CONTENT -->
	 <section id="content">

            <div id="main-container">
                  <div id="page-content" style="min-height: 318px;">
                      <div class="block">
                           <div class="block-title">
                              <h2>Confirm by password</h2>
                           </div>
                           <?php
                                if (isset($_POST["confirm_pass_submit"])) {
                                  foreach ($_POST as $key => $value) {
                                    $$key = $value;
                                  }
                              $pass2 = md5($pass2);
                              $rstcheck = $dbf->getDynamic("member", "ma_id ='" . $rowgetInfo["ma_id"] . "' and password2='".$pass2."'", "");
                              if ($dbf->totalRows($rstcheck) > 0) {
                                $_SESSION["currentmember"]  = 1;
                                $_SESSION["password2"] = $pass2;
                                if(isset($_GET["redirect_page"]))
                                {
                                  $html->redirectURL($_GET["redirect_page"]);
                                }else
                                {
                                  $html->redirectURL("/acount.html");
                                }
                              } else
                                  {
                                       echo '<div class="alert alert-danger alert-dismissable">
                                           <h4><strong>Notice</strong></h4>
                                           <p>Password 2 is wrong</p>
                                        </div>';
                                  }
                              }
                           ?>
                           <form action="" method="post" class="form-horizontal form-bordered">
                              <div class="form-group">
                                 <div class="row">
                                    <label class="col-md-3 control-label" for="pass2">Password 2 <span class="text-danger">*</span></label>
                                    <div class="col-md-6"> <input placeholder="Password 2" type="password" id="pass2" name="pass2" class="form-control" required> </div>
                                 </div>
                              </div>
                              <div class="form-group form-actions">
                                 <div class="col-md-9 col-md-offset-3"> <button type="submit" name="confirm_pass_submit" class="btn btn-effect-ripple btn-primary" style="overflow: hidden; position: relative;">Confirm</button> </div>
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

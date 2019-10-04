<?php
if( $_SESSION["Free"]==1)
{
  $html->redirectURL("/system");
}else
{
      if($_SESSION["currentmember"]==0)
     {
         $html->redirectURL("/confirm_by_password.aspx?redirect_page=account_change_password");
         exit();
     }
?>

<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item active"><?php echo T_('Change password');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">
		<div class="row">
		  <div class="col-md-12">
		  
			<div class="card">
			  <div class="card-header">
	  				<?php echo T_('Change password');?>
			  </div>
			  <div class="card-body">
			  <?php
					if (isset($_POST["submit_change_pass"])) {
					  foreach ($_POST as $key => $value) {
						$$key = $value;
					  }
					  $pass2 = md5($pass2);
					  $rstcheck = $dbf->getDynamic("member", "ma_id ='" . $rowgetInfo["ma_id"] . "' and password2='".$pass2."'", "");
					  if ($dbf->totalRows($rstcheck) > 0) {
							 $array_col = array("password" => md5($User_Password), "password2" => md5($User_Password), "password3" => $User_Password);
							 $affect = $dbf->updateTable("member", $array_col, "id='" . $_SESSION['member_id'] . "'");
							 if ($affect > 0)
							 {
								echo '<div class="alert alert-success alert-dismissable">'.T_('Password updated successfully!!!').'</div>';
									$rowgetInfo         = $dbf->getInfoColum("member",$_SESSION["member_id"]);
									$info_country       = $dbf->getInfoColum("countries",$rowgetInfo["country_id"]);
							 }
					  } else
					  {
						   echo '<div class="alert alert-danger alert-dismissable">'.T_('Old password is wrong').'</div>';
					  }
					}
				  ?>
						  
						  <form action="" method="post" class="form-horizontal form-bordered">
							<div class="form-group form-actions">
							  <div class="row">
								   <label class="col-md-3 control-label" for="User_RePassword2">&nbsp;</label>
								<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 col-md-offset-6">
								   <div class="input-group"> <input type="password" name="pass2" class="form-control" placeholder="<?php echo T_('Current password');?>" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')">  </div>
								</div>
								 </div>
							 </div>
								<div class="form-group row">
									<label class="col-md-3 col-form-label" for="User_Password"><?php echo T_('New password');?></label>
									<div class="col-md-6">
										<input type="password" id="User_Password" name="User_Password" class="form-control" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')">
									</div>
								</div>
								<div class="form-group row">
									<label class="col-md-3 col-form-label" for="User_RePassword"><?php echo T_('Retype new password');?></label>
									<div class="col-md-6">
										<input type="password" id="User_RePassword" name="User_RePassword" class="form-control" required oninvalid="this.setCustomValidity('<?php echo T_('Confirm password does not match');?>')" oninput="setCustomValidity('')" data-equalto="User_Password">
									</div>
								</div>
							 
							 <div class="form-group">
								<div class="text-center">
							 <button type="submit" name="submit_change_pass" class="btn btn-effect-ripple btn-primary" ><?php echo T_('Save Changes');?></button>
							 </div>
							 </div>
						  </form>
				  </div> <!-- card body -->
			</div> <!-- card -->
		  </div> <!-- col-md-12 -->
		  <!-- /.col-->
		  </div>
		  <!-- /.row-->
	  </div>
	</div>
  </main>
  
  <script>
            var password           = document.getElementById("User_Password");
            var confirm_password   = document.getElementById("User_RePassword");
            function validatePassword(){
              if(password.value != confirm_password.value) {
                confirm_password.setCustomValidity("<?php echo T_('Confirm password does not match');?>");
              } else {
                confirm_password.setCustomValidity('');
              }
            }
            password.onchange = validatePassword;
            confirm_password.onkeyup = validatePassword;
      </script>
<?php
}
?>

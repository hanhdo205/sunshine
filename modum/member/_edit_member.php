<?php
$my_passwords = $utl->randomPassword(10,1,"lower_case,upper_case,numbers,special_symbols");
$edit_id = (int)$_GET["id"];
?>

<link href="vendors/bootstrap-daterangepicker/css/daterangepicker.min.css" rel="stylesheet" />
<!--<link href="vendors/bootstrap-datetimepicker/css/bootstrap-datetimepicker.css" rel="stylesheet" />-->
<link href="vendors/select2/css/select2.min.css" rel="stylesheet" />
<!-- Plugins and scripts required by this view-->
<script src="vendors/jquery.maskedinput/js/jquery.maskedinput.js"></script>
<script src="vendors/moment/js/moment.min.js"></script>
<script src="vendors/select2/js/select2.min.js"></script>
<script src="vendors/bootstrap-daterangepicker/js/daterangepicker.js"></script>
<!--<script src="vendors/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>-->
<link rel="stylesheet" href="js/jconfirm/jquery-confirm.css">
<script src="js/jconfirm/jquery-confirm.js"></script>
<script type="text/javascript">
	var translate = {
		member_deleted:"<?php echo T_('Delete member successfull !!!');?>",
		close_btn:"<?php echo T_('Close');?>",
		sure:"<?php echo T_('Are you really want to delete?');?>",
		submit_btn:"<?php echo T_('Submit');?>",
		cancel_btn:"<?php echo T_('Cancel');?>",
		yes_btn:"<?php echo T_('Yes');?>",
	};
  </script>
<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item">
		<a href="member-list.aspx"><?php echo T_('User Management');?></a>
	  </li>
	  <li class="breadcrumb-item active"><?php echo T_('Edit User');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">
		<div class="row">
		  <div class="col-md-12">
		  
			<div class="card">
			  <div class="card-header">
	  				<?php echo T_('Edit User');?>
					<div class="card-header-actions">
						<button class="btn btn-danger delete_user" data-id="<?php echo $edit_id;?>"><?php echo T_('Delete User');?></button>
			  		</div>	
			  </div>
					
						  <div class="card-body">
						  <?php
                            
                            //check quuyen edit account
                            if($dbf->checkEditMember($rowgetInfo["id"],$edit_id))
                            {

                            $infor_account_edit = $dbf->getInfoColum("member",$edit_id);
                            $User_ID   =  $infor_account_edit["ma_id"];
                            $User_Login   =  $infor_account_edit["tendangnhap"];
                            //$User_Password   =  $infor_account_edit["password3"];
                            $User_Name = $infor_account_edit["hovaten"];
                            $User_Email = $infor_account_edit["email"];
                            $User_Lang = $infor_account_edit["language"];
                            $User_Role = $infor_account_edit["roles_id"];
							$User_date_end = "";
							

                            if (isset($_POST["edit_member"])) 
							{
                              foreach ($_POST as $key => $value) {
                                $$key = $dbf->filter($value);
                              }
							 
                              //$pass3 = md5($pass3);
                              //$rstcheck = $dbf->getDynamic("member", "ma_id ='" . $rowgetInfo["ma_id"] . "' and password3='".$pass3."'", "");
                              //if ($dbf->totalRows($rstcheck) > 0) {
                                 $array_col = array("hovaten" => $User_Name,"roles_id" => $User_Role,"email" => $User_Email,"language" => $User_Lang, "dateupdated"=>time());
								 
								 if(isset($User_Password) && $User_Password != '') {
									 $array_col['password'] = md5($User_Password);
									 $array_col['password2'] = md5($User_Password);
									 $array_col['password3'] = $User_Password;
								 }
								 
                                 $affect = $dbf->updateTable("member", $array_col, "id='" . $edit_id . "'");
                                 if ($affect > 0)
                                 {
									echo '<div class="alert alert-success alert-dismissable">'.T_('Edit member successfull !!!').'</div>';

                                 } else
                                 {
                                      echo '<div class="alert alert-danger alert-dismissable">'.T_('Edit member wrong !!!').'</div>';
                                 }

                            }

                          ?>
						  <form action="" method="post">
						  
						  
								<!--<div class="form-group row">
									<label class="col-md-3 col-form-label"><?php echo T_('User ID');?></label>						  
									<div class="col-md-5">
										<input type="text" tabindex="-1" readonly="" placeholder="Automatically generated" value="<?php echo $User_ID;?>" class="form-control" name="User_ID" id="User_ID">
									</div>
								</div> -->

								<div class="form-group row">
									<label class="col-md-3 col-form-label"><?php echo T_('Username');?></label>						  
									<div class="col-md-5">
										<input disabled="" type="text" value="<?php echo $User_Login;?>" class="form-control" name="username" id="username" required>
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-md-3 col-form-label"><?php echo T_('New password');?></label>						  
									<div class="col-md-5">
										<input type="password" class="form-control" name="User_Password" id="User_Password">
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-md-3 col-form-label"><?php echo T_('Retype new password');?></label>						  
									<div class="col-md-5">
										<input type="password" class="form-control" name="User_RePassword" id="User_RePassword" oninvalid="this.setCustomValidity('<?php echo T_('Confirm password does not match');?>')" oninput="setCustomValidity('')" data-equalto="User_Password">
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-md-3 col-form-label"><?php echo T_('Name');?></label>						  
									<div class="col-md-5">
										<input type="text" value="<?php echo $User_Name;?>" class="form-control" name="User_Name" id="User_Name" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')">
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-md-3 col-form-label"><?php echo T_('Email');?></label>						  
									<div class="col-md-5">
										<input type="email" value="<?php echo $User_Email;?>" class="form-control" name="User_Email" id="User_Email" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')">
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-md-3 col-form-label"><?php echo T_('Languages');?></label>						  
									<div class="col-md-5">
										<select class="form-control" name="User_Lang">
											<option value="">---</option>
											<?php foreach($lang as $key=>$value) {
												echo '<option value="'.$key.'" '.$utl->selected($key,$User_Lang).'>'.$lang_text[$key].'</option>';
											};?>
										 </select>
									</div>
								</div>
								
								<div class="form-group row">
									<label class="col-md-3 col-form-label"><?php echo T_('Role');?></label>						  
									<div class="col-md-5">
										<select class="form-control" name="User_Role">
											<option value="">---</option>
											<!--<option value="15" <?php echo $utl->selected(15,$User_Role);?>><?php echo T_('Customer');?></option>-->
											<!--<option value="5" <?php echo $utl->selected(5,$User_Role);?>><?php echo T_('Administrator');?></option>-->
											<option value="6" <?php echo $utl->selected(6,$User_Role);?>><?php echo T_('Manager');?></option>
											<option value="7" <?php echo $utl->selected(7,$User_Role);?>><?php echo T_('Operator');?></option>
											<option value="8" <?php echo $utl->selected(8,$User_Role);?>><?php echo T_('Accountant');?></option>
										 </select>
									</div>
								</div>

								
								<hr/>
								<div class="form-actions">
									<button class="btn btn-primary" type="reset">
										 <?php echo T_('Cancel');?></button>
										
									<button class="btn btn-warning btn-warn" name="edit_member" type="submit" >
									 <?php echo T_('Save');?></button>
								</div>

							
					</form>
			<?php } else {
					echo '<div class="alert alert-danger alert-dismissable">'.T_('You can not edit this member !!!').'</div>';
				}
				?>
			</div> <!-- card-body -->	
				
			</div> <!-- card -->
		  </div> <!-- col-md-12 -->
		  <!-- /.col-->
		  </div>
		  <!-- /.row-->
	  </div>
	</div>
  </main>
  <script src="js/custom/delete_user.js"></script>
  <script src="js/coreui/advanced-forms.js"></script>
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
<?php
$ma_id = $dbf->general_ma_id();
 
$my_passwords = $utl->randomPassword(10,1,"lower_case,upper_case,numbers,special_symbols");
$User_Password = $my_passwords[0];
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

<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item active"><?php echo T_('Member');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">
		<div class="row">
		  <div class="col-md-12">
		  
			<div class="card">
			  <div class="card-header"><?php echo T_('Member create');?></div>
			   
			  <form action="" method="post" novalidate>
			  <div class="card-body">
					<?php
                            if (isset($_POST["created_member"])) {
                              foreach ($_POST as $key => $value) {
                                $$key = $dbf->filter($value);
                              }
							  $isvalue = true;
							  $username_error = "";
							  $fullname_error = "";
							  $email_error = "";
							  $role_error = "";
							  $password_error = "";
							  switch(true) {
									case($username == ""):
										$isvalue = false;
										$username_error = T_('Username can not be blank');
									break;
									case($User_Name == ""):
									  $isvalue = false;
									  $fullname_error = T_('Fullname can not be blank');
									break;
									case($User_Email == ""):
										$isvalue = false;
										$email_error = T_('Email can not be blank');
									break;
									case($User_Role == ""):
										$isvalue = false;
										$role_error = T_('Please select role');
									break;
									case($User_Password == ""):
										$isvalue = false;
										$password_error = T_('Password can not be blank');
									break;
									default:
									// do nothing
									break;
							  }
								if($isvalue) {  
                               $rstcheck = $dbf->getDynamic("member", "tendangnhap ='" . $username . "'", "");
                               if ($dbf->totalRows($rstcheck) == 0) {
                                      
                                      $rstcheck2 = $dbf->getDynamic("member", "email ='" . $User_Email . "'", "");
                                      if ((int) $dbf->totalRows($rstcheck2) == 0) 
									  {
                                             
												 $User_RegisteredDatetime = date('Y-m-d H:i:s',strtotime($User_RegisteredDatetime));
												
                                                 $rstcheck3 = $dbf->getDynamic("member", "ma_id ='" . $ma_id . "'", "");
                                                 if ((int) $dbf->totalRows($rstcheck3) <=0) {		if($User_Price=='')$User_Price = 0;												
                                                     $array_col = array("ma_id" => $ma_id,"roles_id" => $User_Role,"hovaten" => $User_Name,"parentid" => $_SESSION["member_id"],"tendangnhap" => $username,"password" => md5($User_Password),"password2" => md5($User_Password),"password3" => md5($User_Password),"email" => $User_Email,"language" => $User_Lang,"datecreated"=>strtotime($User_RegisteredDatetime),"dateupdated"=>time(),"member_re"=>1,"status"=>1,"active_register"=>1);
														
													 //var_dump($array_col);die();
                                                     $affect = $dbf->insertTable_2("member", $array_col);
                                                     if ($affect > 0)
                                                     {
                                                          echo '<div class="alert alert-dismissable alert-success">'.T_('Create member success !!!').'</div>';

                                                          foreach ($_POST as $key => $value) {
                                                            $$key = "";
														  }	
                                                     }
                                                 }else
                                                     {
                                                        echo '<div class="alert alert-danger alert-dismissable">'.T_('Create member error !!! Account is duplicate').'</div>';
                                                     }

                                      }else
                                      {
										$isvalue = false;
                                         $email_error = T_('Email is already exits. Please try again');
                                      }
                              } else
                              {
									$isvalue = false;
									$username_error = T_('Username is already exits. Please try again');
                              }
							} 
							
						
                         if(!$isvalue) 
							{
								$error = T_("Opps, something went wrong!");
								echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">'.T_($error).'<button class="close hide_error" type="button" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></DIV>';						
							} 
					}	  
					?>
						<!--  
					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('User ID');?></label>						  
						<div class="col-md-5">
							<input type="text" tabindex="-1" readonly="" placeholder="Automatically generated" value="<?php echo $ma_id;?>" class="form-control" name="User_ID" id="User_ID" required>
						</div>
					</div> -->
					
					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Username');?> <span class="text-danger">*</span></label>
						<?php 
							if($username_error) {
								$username_error_class = "has_error";
							} ?>						
						<div class="col-md-5 <?php echo $username_error_class;?>">
										
							<input type="text" tabindex="-1" value="<?php echo $username;?>" class="form-control" name="username" id="username" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')">
							<small class="<?php echo $username_error_class;?> field_message text-danger"><?php echo T_($username_error);?></small>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Full name');?>  <span class="text-danger">*</span></label>
							<?php 
								if($fullname_error) {
								$fullname_error_class = "has_error";
							} ?>						
						<div class="col-md-5 <?php echo $fullname_error_class;?>">
							<input type="text" value="<?php echo $User_Name;?>" class="form-control" name="User_Name" id="User_Name" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')">
							<small class="<?php echo $fullname_error_class;?> field_message text-danger"><?php echo T_($fullname_error);?></small>
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Email');?> <span class="text-danger">*</span></label>	
							<?php 
								if($email_error) {
								$email_error_class = "has_error";
							} ?>						
						<div class="col-md-5 <?php echo $email_error_class;?>">
							<input type="email" value="<?php echo $User_Email;?>" class="form-control" name="User_Email" id="User_Email" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')">
							<small class="<?php echo $email_error_class;?> field_message text-danger"><?php echo T_($email_error);?></small>
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
					    <label class="col-md-3 col-form-label"><?php echo T_('Role');?> <span class="text-danger">*</span></label>	
							<?php 
								if($role_error) {
								$role_error_class = "has_error";
							} ?>	
						<div class="col-md-5 <?php echo $role_error_class;?>">
							<select class="form-control" name="User_Role">
						        <option value="">---</option>
							    <!--<option value="5"><?php echo T_('Administrator');?></option>-->
						    	<option value="6" <?php echo $utl->selected(6,$User_Role);?>><?php echo T_('Manager');?></option>
						    	<option value="7" <?php echo $utl->selected(7,$User_Role);?>><?php echo T_('Operator');?></option>
						    	<option value="8" <?php echo $utl->selected(8,$User_Role);?>><?php echo T_('Accountant');?></option>
						     </select>
							 <small class="<?php echo $role_error_class;?> field_message text-danger"><?php echo T_($role_error);?></small>
						</div>
					</div>

					<div class="form-group row">
					    <label class="col-md-3 col-form-label"><?php echo T_('Password');?> <span class="text-danger">*</span></label>
							<?php 
								if($password_error) {
								$password_error_class = "has_error";
							} ?>
						
						<div class="col-md-4 <?php echo $password_error_class;?>">
							<div class="input-group" id="show_hide_password" >
								<input type="text" class="form-control" id="User_Password" name="User_Password" value="<?php echo $User_Password;?>">
								<div class="input-group-append">
									<a href="" class="input-group-text"><i class="fa fa-eye" aria-hidden="true"></i></a>
								</div>
							</div>
							<small class="<?php echo $password_error_class;?> field_message text-danger"><?php echo T_($password_error);?></small>
						</div>
						<div class="col-md-1"><a class="btn btn-light refresh"><i class="fa fa-refresh rotate" aria-hidden="true"></i> <?php echo T_('Refresh');?></a></div>
					</div>

				</div> <!-- card-body -->	
				<div class="card-footer">
				  <button class="btn btn-warning btn-warn" type="submit" name="created_member" >
					 <?php echo T_('Add member');?></button>
				</div>
			</form>
			</div> <!-- card -->
		  </div> <!-- col-md-12 -->
		  <!-- /.col-->
		  </div>
		  <!-- /.row-->
	  </div>
	</div>
  </main>
  <script src="js/coreui/advanced-forms.js"></script>
  <script src="js/custom/custom.js"></script>
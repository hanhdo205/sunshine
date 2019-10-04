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
	  <li class="breadcrumb-item active"><?php echo T_('Patient Registration');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">
		<div class="row">
		  <div class="col-md-12">
		  
			<div class="card">
			  <div class="card-header"><?php echo T_('Patient Registration');?></div>
			   
			  <form action="" method="post" novalidate>
			  <div class="card-body">
							<?php
					if (isset($_POST["created_member"])) {
					  foreach ($_POST as $key => $value) {
						$$key = $dbf->filter($value);
					  }
					  $fullname_error = "";
					  $age_error = "";
					  $isvalue = true;
					  switch(true) {
							
							case($User_Name == ""):
							  $isvalue = false;
							  $fullname_error = T_('Fullname can not be blank');
							break;
							
							case(ctype_alpha(str_replace(' ', '', $User_Name)) === false):
								$isvalue = false;
								$fullname_error = T_("Please enter alphabet only");
							break;
							
							case($User_Age == ""):
							  $isvalue = false;
							  $age_error = T_('Please fill out this field.');
							break;
							
							case($User_Age <= 0):
							  $isvalue = false;
							  $age_error = T_('Please enter valid age.');
							break;
							
							default:
							// do nothing
							break;
					  }
					  
					if($isvalue) { 
						 $rstcheck3 = $dbf->getDynamic("member", "ma_id ='" . $User_ID . "'", "");
						 if ((int) $dbf->totalRows($rstcheck3) <=0) {												
							 $array_col = array("ma_id" => $User_ID,"roles_id" => 16,"hovaten" => $User_Name,"parentid" => $_SESSION["member_id"],"tendangnhap" => $username,"gender" => $gender,"age" => $User_Age,"password" => md5($User_Password),"password2" => md5($User_Password),"password3" => md5($User_Password),"datecreated"=>time(),"dateupdated"=>time(),"member_re"=>1,"status"=>0,"active_register"=>0);
								
							 //var_dump($array_col);die();
							 $affect = $dbf->insertTable_2("member", $array_col);
							 if ($affect > 0)
							 {
								  echo '<div class="alert alert-dismissable alert-success">'.T_('New patient created successfully !!!').'</div>';

								  foreach ($_POST as $key => $value) {
									$$key = "";
								  }	
							 }
						 }else
							 {
								echo '<div class="alert alert-danger alert-dismissable">'.T_('Create member error !!! Account is duplicate').'</div>';
							 }

					}
				
				if(!$isvalue) 
					{
						$error = T_("Opps, something went wrong!");
						echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">'.T_($error).'<button class="close hide_error" type="button" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button></DIV>';						
				} 
			}
			  ?>
							<input type="hidden" tabindex="-1" placeholder="Automatically generated" value="<?php echo $ma_id;?>" name="User_ID" id="User_ID" >

							<input type="hidden" tabindex="-1" value="<?php echo $ma_id;?>" name="username" id="username">

					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Patient name');?>  <span class="text-danger">*</span></label>	
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
						<label class="col-md-3 col-form-label"><?php echo T_('Gender');?>  <span class="text-danger">*</span></label>
						<div class="col-md-5 col-form-label">
							<div class="form-check form-check-inline mr-1">
							<input class="form-check-input" id="male" type="radio" value="male" name="gender" <?php 
							$is_male = $utl->checked(array($gender),'male');
							$is_female = $utl->checked(array($gender),'female');
							echo ($is_male) ? 'checked' : ($is_female ? '' : 'checked');?>>
							<label class="form-check-label" for="male"><?php echo T_('Male');?></label>
							</div>
							<div class="form-check form-check-inline mr-1">
							<input class="form-check-input" id="female" type="radio" value="female" name="gender" <?php echo ($is_female) ? 'checked' : '';?>>
							<label class="form-check-label" for="female"><?php echo T_('Female');?></label>
							</div>
							
						</div>
					</div>
					
					<div class="form-group row">
						<label class="col-md-3 col-form-label"><?php echo T_('Age');?> <span class="text-danger">*</span></label>
							<?php 
								if($age_error) {
								$age_error_class = "has_error";
							} ?>						
						<div class="col-md-5 <?php echo $age_error_class;?>">
							<input type="number" value="<?php echo $User_Age;?>" class="form-control" name="User_Age" id="User_Age" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')">
							<small class="<?php echo $age_error_class;?> field_message text-danger"><?php echo T_($age_error);?></small>
						</div>
					</div>

				</div> <!-- card-body -->	
				<div class="card-footer">
				  <button class="btn btn-warning btn-warn" type="submit" name="created_member" >
					 <?php echo T_('Add Patient');?></button>
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
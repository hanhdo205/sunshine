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
	  <li class="breadcrumb-item">
		<a href="patient-list.aspx"><?php echo T_('Patient');?></a>
	  </li>
	  <li class="breadcrumb-item active"><?php echo T_('Patient Detail');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">
		<div class="row">
		  <div class="col-md-12">
		  
			<div class="card">
			  <div class="card-header"><?php echo T_('Patient Detail');?></div>
			  
			  <div class="card-body">
			  <?php
                            $edit_id = (int)$_GET["id"];
                            //check quuyen edit account
                            if($dbf->checkEditMember($rowgetInfo["id"],$edit_id))
                            {
								$infor_account_edit = $dbf->getInfoColum("member",$edit_id);
								$User_ID   =  $infor_account_edit["ma_id"];
								$User_Name = $infor_account_edit["hovaten"];
								$User_Gender = $gender[$infor_account_edit["gender"]];
								$User_Age = $infor_account_edit["age"];
							}
                          ?>
					<form action="" method="post">
						<div class="form-group row">
							<fieldset class="col-sm-2 form-group">
							  <label><?php echo T_('Patient Number');?></label>						  
							</fieldset>
						
							<div class="col-sm-5">
								<input disabled="" type="text" value="<?php echo $User_ID;?>" class="form-control" >	
							</div>
													
						</div>

						<div class="form-group row">
							<fieldset class="col-sm-2 form-group">
							  <label><?php echo T_('Patient Name');?></label>						  
							</fieldset>
						
							<div class="col-md-5">
								<input disabled="" type="text" value="<?php echo $User_Name;?>" class="form-control" >
							</div>
														
						</div>
						

						<div class="form-group row">
							<fieldset class="col-2 form-group">
							  <label><?php echo T_('Gender');?></label>						  
							</fieldset>
							
							<div class="col-md-5">
								<input disabled="" type="text" value="<?php echo $User_Gender;?>" class="form-control" >
							</div>
							
						</div>

					</form>
				</div> <!-- card-body -->	
				   	
			
			</div> <!-- card -->
		  </div> <!-- col-md-12 -->
		  <!-- /.col-->
		  </div>
		  <!-- /.row-->
	  </div>
	</div>
  </main>
  <script src="js/coreui/advanced-forms.js"></script>
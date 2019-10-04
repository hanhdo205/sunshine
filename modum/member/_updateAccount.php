<script src="js/custom/update_profile.js"></script>
<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item active"><?php echo T_('Profile');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">
		<div class="row">
		  <div class="col-md-12">
		  
			<div class="card">
			  <div class="card-header">
	  				<?php echo T_('User profile');?>
			  </div>
			  <div class="card-body">
			  <?php
							$avatar='';
						    $isValue = true;
                            if (isset($_POST["submit_update_info"])) {
								
								//for($i=1;$i<=2;$i++)
								//{		
								$listFile = "listFile1";
								
								if($_FILES[$listFile]["tmp_name"])
									{
								
									 $filename = $_FILES[$listFile]['name'];
									 /*$ext = strtolower(substr($filename, strpos($filename,'.'), strlen($filename)-1));*/
									 $ext = pathinfo(strtolower($filename), PATHINFO_EXTENSION);
									 $allowed_filetypes = array('jpg','gif','png','jpeg');
									 $max_filesize = 2 * (1024 * 1024); // 2MB
									if(!in_array($ext,$allowed_filetypes))
									{				 
										 $result["status"] = 0;  
										 $result["msg"][] = "Only upload file .JPG, .GIF, .PNG";
										  echo '<div class="alert alert-danger alert-dismissable">'.T_('Only upload file .JPG, .GIF, .PNG').'</div>';
											$isValue = false;
										 
									}else
									{
										
											if(filesize($_FILES[$listFile]['tmp_name']) > $max_filesize)
											{						
												 $result["status"] = 0;  
												 $result["msg"][] = "File image very large.>4Mb";
												 echo '<div class="alert alert-danger alert-dismissable">'.T_('File zise too large.').'</div>';
												 $isValue = false;
											}else
											{	$picture = "avatar";
												$newName = $rowgetInfo["ma_id"].'_'.$picture.'_'.session_id();
												$path = "upload/".$newName.".".$ext;
												move_uploaded_file($_FILES[$listFile]['tmp_name'], $path);
												$$picture = $path;
											}
									}
									
									
								  }
								//} /* end for */
								  foreach ($_POST as $key => $value) {
									$$key = $dbf->filter($value);
								  }
								  
								  if($isValue)
								  {
                              
                              //$rstcheck = $dbf->getDynamic("member", "ma_id ='" . $rowgetInfo["ma_id"] . "' and password3='".$pass3."'", "");
                              //if ($dbf->totalRows($rstcheck) > 0) {
                                     //$array_col = array("date_ngaysinh" => strtotime($User_Birthday), "email" => $User_Email, "gioitinh" => $User_Gender, "cmnd" => $cmnd, "diachi" => $User_Address, "country_id" => $User_Country, "didong" => $User_Mobile, "bit_coin_address"=>$bit_coin_address, "sotaikhoan" => $User_BankAccountNumber, "nganhang" => $User_BankName);
									 
										$array_col = array("hovaten" => $User_Name,"email" => $User_Email,"language" => $User_Lang);
										
									 
										if($avatar != '') {
											$array_col["picture"] = $avatar;
											}
									
                                     //$array_col = array("date_ngaysinh" => strtotime($User_Birthday), "email" => $User_Email, "gioitinh" => $User_Gender, "cmnd" => $cmnd, "diachi" => $User_Address, "country_id" => $User_Country, "didong" => $User_Mobile);
                                     $affect = $dbf->updateTable("member", $array_col, "id='" . $_SESSION['member_id'] . "'");
									 
									 /*$array_sales = array("member_id"=>$_SESSION['member_id'],"quantity"=>$User_actual,"datecreated"=>time());
									 
									 if($dbf->totalRows($actual) > 0)
										 $actual_sales = $dbf->updateTable("actual_sales", $array_sales, "member_id='" . $_SESSION['member_id'] . "'");
									 else $actual_sales = $dbf->insertTable("actual_sales", $array_sales);*/
									 //printf("<pre>%s</pre>",print_r($array_col,true));	die();	
                                     if ($affect > 0)
                                     {
                                        echo '<div class="alert alert-success" role="alert">'.T_('Profile updated successfully!').'</div>';
                                            $rowgetInfo         = $dbf->getInfoColum("member",$_SESSION["member_id"]);
											//$rowgetActual = $dbf->getActualColum("actual_sales",$_SESSION["member_id"]);
                                            $info_country       = $dbf->getInfoColum("countries",$rowgetInfo["country_id"]);
                                     } 
                              /*} else
                              {
                                   echo '<div class="alert alert-danger alert-dismissable">
                                       <h4><strong>Notice</strong></h4>
                                       <p>Password 2 is wrong</p>
                                    </div>';
                              }*/
							  
								  }
                            }
                          ?>
						  
						  <form id="update_information" class="form-horizontal form-bordered" method="post" action="" enctype="multipart/form-data">
							<div class="row">
							
								<div class="col-4">
									<div class="form-group">
										<div class="row">
											<div class="col-md-3">
											</div>
											<div class="col-md-6">
												<input id="files" name="listFile1" style="display:none" type="file" onchange="readURL(this);" data-buttonText="<?php echo T_('Select file');?>">
												<div class="div-avatar circle"><img id="avatar-img" class="rounded-circle mx-auto d-block select-image" src="<?php echo $rowgetInfo["picture"] ? $rowgetInfo["picture"] : HOST . '/style/images/packages/user.png';?>" onload="fixAspect(this);" />
												<div data-id="avatar_image_id" data-src="avatar-img" class="camera-icon avatar-image"><i class="fa fa-camera" aria-hidden="true"></i></div>
												</div>
											</div>
											<div class="col-md-3">
											</div>
											<div class="col-md-12">
												<h4 class="mt-2 text-center"><?php echo $rowgetInfo["hovaten"]?></h4>
												<!--<strong><?php echo T_('Description');?></strong>
												<textarea rows="3" class="form-control editor" name="User_Description"><?php echo $rowgetInfo["description"];?></textarea>-->
											</div>
										</div>
									</div>
								</div>
								<div class="col-8">
								    
									<div class="row">
										<div class="form-group col-sm-6">
										  <label for="User_ID"><?php echo T_('User ID');?></label>
										  <input class="form-control" id="User_ID" name="User_ID" type="text" tabindex="-1" readonly="" placeholder="Automatically generated" value="<?php echo $rowgetInfo["ma_id"];?>"/>
										</div>
										<!--<div class="form-group col-sm-6">
										  <label for="username"><?php echo T_('Username');?> <span class="text-danger">*</span></label>
										  <input class="form-control" id="username" value="<?php echo $rowgetInfo["tendangnhap"];?>" disabled="" type="text" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')" />
										</div>-->
									</div>
									
									<!-- /.row-->
									<div class="row">
										<div class="form-group col-sm-6">
											<label for="User_Name"><?php echo T_('Fullname');?> <span class="text-danger">*</span></label>
											<input type="text" value="<?php echo $rowgetInfo["hovaten"];?>" class="form-control" name="User_Name" id="User_Name">
										</div>
									</div>
									<!--<div class="row">
										<div class="form-group col-sm-6">
										  <label for="User_Age"><?php echo T_('Age');?></label>
										  <input type="text" value="<?php echo $rowgetInfo["age"];?>" class="form-control" name="User_Age" id="User_Age">
										</div>
										<div class="form-group col-sm-6">
											<label for="username"><?php echo T_('Gender');?></label>
											<div>
											  <div class="form-check form-check-inline">
											  
												   <input class="form-check-input" type="radio" name="User_Sex" id="male" value="male" <?php
												$is_male = $utl->checked($rowgetInfo["gender"], 'male');
												$is_female = $utl->checked($rowgetInfo["gender"], 'female');
												echo ($is_male) ? 'checked' : ($is_female ? '' : 'checked');?> /><label class="form-check-label" for="male"><?php echo T_('Male');?></label>
												</div>
												<div class="form-check form-check-inline">
													<input class="form-check-input" type="radio" name="User_Sex" id="female" value="female" <?php echo ($is_female) ? 'checked' : '';?> /><label class="form-check-label" for="female"><?php echo T_('Female');?></label>
												</div>
											</div>
										</div>
									</div>-->
									<!-- /.row-->
									
									<div class="row">
										<div class="form-group col-sm-6">
										  <label for="User_Email"><?php echo T_('Email');?> <span class="text-danger">*</span></label>
										  <input type="email" value="<?php echo $rowgetInfo["email"];?>" class="form-control" name="User_Email" id="User_Email" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')">
										</div>
										<!-- <div class="form-group col-sm-6">
										  <label for="User_Mobile"><?php echo T_('Phone number');?> <span class="text-danger">*</span></label>
										  <input type="tel" value="<?php echo $rowgetInfo["phone_number"];?>" class="form-control" name="User_Mobile" id="User_Mobile" required oninvalid="this.setCustomValidity('<?php echo T_('Please fill out this field.');?>')" oninput="setCustomValidity('')">
										</div>-->
									</div>
									<!-- /.row-->
									
									<!-- <div class="form-group">
										<label for="User_Address"><?php echo T_('Address');?> <span class="text-danger">*</span></label>
										<input type="text" value="<?php echo $rowgetInfo["address"];?>" class="form-control" name="User_Address" id="User_Address">
									</div>-->
									<div class="row">
										<div class="form-group col-sm-6">
											<label for="select_lang"><?php echo T_('Main Language');?></label>
												<select class="form-control" name="User_Lang">
													<option value="">---</option>
													
													<?php 
													foreach($lang_text as $key=>$value) {
														echo '<option value="'.$key.'" '.$utl->selected($key,$rowgetInfo["language"]).'>'.$value.'</option>';
													};?>
												 </select>
												 <span class="help-block"><?php echo T_('※ This language setting is used for display of website, notification, and E-mail content. If you change language, log-out and lgoin again after select your prefer language.');?></span>
										</div>
									</div>

								  <div class="form-group form-actions">
								  <div class="row">
									<div class="col">
									   <div class="input-group"> <!--<input type="password" name="pass3" class="form-control" placeholder="Current password 2" >--> <span class="input-group-btn"> 
									   <button class="btn btn-effect-ripple btn-primary" name="submit_update_info" type="submit" style="overflow: hidden; position: relative;"><?php echo T_('Save Changes');?></button>
									   </span> </div>
									</div>
									 </div>
								 </div>

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
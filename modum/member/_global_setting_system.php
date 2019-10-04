<script>
	var readURLs = function(input,div) {
		$('#' + div).empty();   
		var number = 0;
		$.each(input.files, function(value) {
			var reader = new FileReader();
			
			reader.onload = function (e) {
				var id = (new Date).getTime();
				
				$('#' + div).prepend('<span class="slider_img"><img id='+id+' class="thumb remove_img_preview" src='+e.target.result+' data-index='+number+' /></span>');
				$('.navbar-brand').html('<img class="navbar-brand-full" src="'+e.target.result+'" height="40" alt="logo">');
				//$('.navbar-brand').html('');
				number++;
			};
			reader.readAsDataURL(input.files[value]);
		});
	}
</script>
<script src="js/custom/custom.js"></script>
<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item active"><?php echo T_('Setting');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">
		<div class="row">
		<?php if($rowgetInfo["roles_id"]<=5) { ?>
		  <div class="col-sm-6 ui-sortable">
			<div class="card">
			  <div class="card-header drag ui-sortable-handle"><?php echo T_('System Setting');?></div>
				<form action="" method="post" enctype="multipart/form-data">
					<div class="card-body">
						<?php
						$gettitle_info = $dbf->getInfoColum("setting",1);
						$set_title = $gettitle_info['value'];
						$getemail_info = $dbf->getInfoColum("setting",5);
						$set_email = $getemail_info['value'];
						$getlogo_info = $dbf->getInfoColum("setting",24);
						$set_logo = $getlogo_info['value'];
						$getvat_info = $dbf->getInfoColum("setting",25);
						$set_vat = $getvat_info['value'];
						$getreminder_info = $dbf->getInfoColum("setting",26);
						$set_reminder = $getreminder_info['value'];
						$isValue = true;
						$logo_exists = false;
						if (isset($_POST["setting"])) {
							$listFile = "logo";
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
										 $result["msg"][] = "Only allowed filetype .JPG, .GIF, .PNG";
										  echo '<div class="alert alert-danger alert-dismissable">'.T_('Only allowed filetype .JPG, .GIF, .PNG').'</div>';
											$isValue = false;
										 
									}else
									{
										
											if(filesize($_FILES[$listFile]['tmp_name']) > $max_filesize)
											{						
												 $result["status"] = 0;  
												 $result["msg"][] = "File size too large";
												 echo '<div class="alert alert-danger alert-dismissable">'.T_('File size too large').'</div>';
												 $isValue = false;
											}else
											{	$picture = "set_logo";
												$newName = $picture;
												$path = "upload/".$newName.".".$ext;
												move_uploaded_file($_FILES[$listFile]['tmp_name'], $path);
												$$picture = $path;
											}
									}
									
									
								  }
							  foreach ($_POST as $key => $value) {
								$$key = $dbf->filter($value);
							  }
							  

							$title_setting = array("value" => $set_title);
							$mail_setting = array("value" => $set_email);
							$vat_setting = array("value" => $set_vat);
							$reminder_setting = array("value" => $set_reminder);

							$title_affect = $dbf->updateTable("setting", $title_setting, "id='1'");
							$mail_affect = $dbf->updateTable("setting", $mail_setting, "id='5'");
							$vat_affect = $dbf->updateTable("setting", $vat_setting, "id='25'");
							$reminder_affect = $dbf->updateTable("setting", $reminder_setting, "id='26'");
							if($isValue)
								$logo_affect = $dbf->updateTable("setting", array("value" => $set_logo), "id='24'");

							if ($mail_affect > 0 || $title_affect > 0 || $vat_affect > 0 || $reminder_affect > 0 || $isValue)
							{
								echo '<div class="alert alert-success" role="alert">'.T_('System setting was set successfully!').'</div>';
							} 
						  
						}
						?>
						<div class="form-group row">
							<div class="col-md-3"><input id="logo_img" name="logo" type="file" style="visibility:hidden;" onchange="readURLs(this,'up_logo');"></div>
							<div class="col-md-9 mb-2"><div id ="up_logo"></div><div id="area_id"></div></div>
							<label class="col-md-3 col-form-label" for="select_mail"><?php echo T_('Site logo');?></label>
								<div class="col-md-9">
																
									<?php
									if(file_exists($set_logo)) {
										   $logo_exists = true;?>
										   <img class="logo-photo d-block select-image mb-2" src="<?php echo $set_logo;?>" width="100px"/>
									   <?php } else {
										   // printf("<pre>%s</pre>",print_r($set_logo,true));
										}
									?>
									<span class="input-group div-select-logo">
										<input type="text" name="logo_file" class="logo-input input full upload form-control" placeholder="<?php echo T_('No file select');?>" value="<?php echo ($logo_exists) ? $set_logo : '';?>" autocomplete="off" style="padding: 3px !important;background: #fff;">
										<span class="input-group-append">
											<label for="logo_img" class="btn btn-primary"><?php echo T_('Select file');?></label>
										</span>
									</span>
								</div>
						</div>
						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="site_title"><?php echo T_('Site title');?></label><div class="col-md-9">						  
								<input type="text" id="site_title" class="form-control title" name="set_title" value="<?php echo $set_title;?>" size="100%">
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="select_mail"><?php echo T_('Email address');?></label><div class="col-md-9">						  
								<input type="email" id="select_mail" class="form-control mail" name="set_email" value="<?php echo $set_email;?>" size="100%">
							</div>
						</div>
						<!--<div class="form-group row">
							<label class="col-md-3 col-form-label" for="set_vat"><?php echo T_('Tax');?></label>
							<div class="col-md-9">
								<div class="input-group"><input class="form-control vat" id="set_vat" name="set_vat" size="16" type="text" value="<?php echo $set_vat;?>"><div class="input-group-append"><span class="input-group-text">%</span></div></div>	
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-3 col-form-label" for="set_reminder"><?php echo T_('Reminder');?></label>
							<div class="col-md-9">
								<div class="input-group"><input class="form-control reminder" id="set_reminder" name="set_reminder" size="16" type="text" value="<?php echo $set_reminder;?>"><div class="input-group-append"><span class="input-group-text"><?php echo T_('month');?></span></div></div>					  
								
							</div>
						</div>-->
						
					</div> <!-- card-body -->	
					<div class="card-footer">
						  <button class="btn btn-warning btn-warn" type="submit" name="setting">
							 <?php echo T_('Save');?></button>
					</div>
				</form>
			</div> <!-- card -->
		  </div> <!-- col-sm-4 -->
		<?php } ?>
		  <!-- /.col-->
		  </div>
		  <!-- /.row-->
	  </div>
	</div>
  </main>
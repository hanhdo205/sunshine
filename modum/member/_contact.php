<?php
session_start();

if (!isset($_SESSION['token'])) {
	$token             = md5(uniqid(rand(), TRUE));
	$_SESSION['token'] = $token;
}else
{
	$token = $_SESSION['token'];
}
if(isset($_SESSION['invalid_token'])) $error= '<p class="error_messe" style="color: red;">'.__('Opps. Something wrong').'</p>';

if(isset($_POST['submit']))
{
	unset($_SESSION["contact"]);  
	foreach($_POST as $key => $value){
		if(!is_array($value)){
		   $_SESSION["contact"][$key] = $dbf->filter($value);	
		}else{
		   $_SESSION["contact"][$key] = $value;	
		}
		
	}
	if($_SESSION["contact"]["token"] == $_SESSION['token'])
	{
	$_POST = $_SESSION["contact"];   
	$require = array(
			'message'=>__('Message field')
		);
		$error = '';
			 $isvalue = true;
			 if ( !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) && $_POST["email"] != '' ) {
					$emailErr.= '<p class="error_messe" style="color: red;">'.__('Email is invalid').'</p>'; 
					$isvalue = false;
			 }
				 
			 if($isvalue)
			 {
				
				 echo "<script>window.location.href='contact-confirm.aspx';</script>";
				 Header( "Location: contact-confirm.aspx" );
				 exit;
				
			 }else
			 {
				$error = $emailErr;
			 }

	}else
		{
			$error.= '<p class="error_messe" style="color: red;">'.__('Opps. Something wrong').'</p>';
		}
	
}
	
$info_account = $dbf->getInfoColum("member",$rowgetInfo["id"]);
$id   = $rowgetInfo["id"];
$User_ID   = $info_account["ma_id"];
$User_Name = $info_account["hovaten"];
$Company = $info_account["company"];
$User_Email = $info_account["email"];
?>
<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item active"><?php echo T_('Contact');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">
		<div class="row">
		  <div class="col-md-12 mb-5">
		  
			<div class="card">
			  <div class="card-header"><?php echo T_('Contact');?></div>
			  <form id="fcontact" name="fcontact" action="" method="post" onsubmit="return CheckValidator('fcontact')" novalidate>
			  <?php if($error) 
					{
						echo "<div class='txt-contact'>".$error."</div>";									
					}
				?>
			  <div class="card-body">
					<div class="form-group row">
					    <label class="col-md-3 col-form-label"><?php echo T_('User ID');?></label>						  
						<div class="col-md-5">
							<label><?php echo $User_ID;?></label>
							<input type="hidden" name="contact_id" value="<?php echo $User_ID;?>">
							<input type="hidden" name="contact_user_id" value="<?php echo $id;?>">
						</div>
					</div>

					
					<div class="form-group row">
					    <label class="col-md-3 col-form-label"><?php echo T_('Company name');?></label>						  
						<div class="col-md-5">
							<label><?php echo $Company;?></label>
							<input type="hidden" name="contact_company" value="<?php echo $Company;?>">
						</div>
					</div>
					
					<div class="form-group row">
					    <label class="col-md-3 col-form-label"><?php echo T_('Contact person');?></label>						  
						<div class="col-md-5">
							<label><?php echo $User_Name;?></label>
							<input type="hidden" name="contact_name" class="contact_name form-control" value="<?php echo $User_Name;?>">
						</div>
					</div>
					
					<div class="form-group row">
					    <label class="col-md-3 col-form-label"><?php echo T_('Message');?></label>						  
						<div class="col-md-5">								
							<textarea name="message" class="form-control" rows="7"><?php echo $_SESSION["contact"]["message"]; ?></textarea>
						</div>
					</div>

					
					</div> <!-- card-body -->	
				   	<div class="card-footer">
						<input type="hidden" name="token" value="<?php echo $token;?>">
						<input type="hidden" name="email" value="<?php echo $User_Email;?>">
						<?php if(isset($_POST['order_id'])) { ?>
							<input type="hidden" name="order_id" value="<?php echo $_POST['order_id'];?>">
						<?php } ?>
						<button class="btn btn-warning btn-warn" name="submit" type="submit">
						<?php echo T_('Confirm');?></button>
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
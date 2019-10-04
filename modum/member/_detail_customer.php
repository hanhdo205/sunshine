<?php
if(isset($_GET['id'])) {
	$_SESSION["customer_id"] = $_GET['id'];
}
if(isset($_SESSION["customer_id"])) {
	$edit_id = (int) $_SESSION["customer_id"];
}

$getprice_info = $dbf->getInfoColum("setting",22);					
$set_price = $getprice_info['value'];
$urgent_getprice_info = $dbf->getInfoColum("setting",25);
$urgent_set_price = $urgent_getprice_info['value'];						
?>
<link href="vendors/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet" />
<!-- Plugins and scripts required by this view-->
<script src="vendors/datatables.net/js/jquery.dataTables.js"></script>
<script src="vendors/datatables.net-bs4/js/dataTables.bootstrap4.js"></script>
<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item">
		<a href="customer-list.aspx"><?php echo T_('Customer List');?></a>
	  </li>
	  <li class="breadcrumb-item active"><?php echo T_('Customer Detail');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">
	  
	    <?php
		 if(isset($_POST["edit_member"])) 
		 {
			  foreach ($_POST as $key => $value) {
				$$key = $dbf->filter($value);
			  }
				
				   if(!isset($User_Gender)) 
				   {
					  $User_Gender = ""; 
				   }
				   if(!isset($payment_method)) 
				   {
					  $payment_method = ""; 
				   }
				  				
				  
				   /*$array_col = array("price_novat" => $set_price,"urgent_price_novat" => $urgent_set_price,"dateupdated"=>time());*/
				   $array_col = array("hovaten" => $User_Name,"hovaten_alphabet" => $User_Name_alphabet,"company" => $company_name,"company_alphabet" => $company_alphabet,"gender" =>$User_Gender,"prefecture" => $prefecture,"city" => $city,"address" => $address, "email" => $User_Email,"phone_number" => $User_Phone,"payment_method" => $payment_method,"price_novat" => $set_price,"urgent_price_novat" => $urgent_set_price,"dateupdated"=>time());
				   
				   $price_affect = $dbf->updateTable("member", $array_col, "id='" . $edit_id . "'");
					if ($price_affect > 0)
					 {
						echo '<div class="alert alert-success alert-dismissable">'.T_('Setting saved!').'</div>';

					 } else
					 {
						  echo '<div class="alert alert-danger alert-dismissable">'.T_('Setting failed!').'</div>';
					 }
		}
		
		
		/*check quuyen edit account*/
		if($dbf->getEditMember($edit_id))
		{
			$infor_account_edit = $dbf->getInfoColum("member",$edit_id);
			$User_ID   =  $infor_account_edit["ma_id"];
			$User_Company   =  $infor_account_edit["company"];
			$company_alphabet = $infor_account_edit["company_alphabet"];
			$User_Login   =  $infor_account_edit["tendangnhap"];
			$User_Password   =  $infor_account_edit["password3"];
			$User_Name = $infor_account_edit["hovaten"];
			$hovaten_alphabet = $infor_account_edit["hovaten_alphabet"];
			$User_Gender 	= $infor_account_edit["gender"];
			$User_Address = $infor_account_edit["address"];
			
			$postal_code = $infor_account_edit["postal_code"];
			$prefecture  = $infor_account_edit["prefecture"];
			$city 		 = $infor_account_edit["city"];
			$address     = $infor_account_edit["address"];
			
			$User_Email = $infor_account_edit["email"];
			$User_Phone = $infor_account_edit["phone_number"];
			$User_Lang = $infor_account_edit["language"];
			$User_Role = $infor_account_edit["roles_id"];
			$is_read = $infor_account_edit["is_read"];
			$show_price = $infor_account_edit["show_price"];
			$get_price = $infor_account_edit["price_novat"];
			$urgent_get_price = $infor_account_edit["urgent_price_novat"];
			$payment_method = $infor_account_edit["payment_method"];
			$checked = ($utl->checked(array($show_price),'yes')) ? 'checked' : '';
		}
		
		if(($rowgetInfo["roles_id"] < 6) && $is_read !="yes") {
			$array_col = array("is_read" =>"yes");
			$affect = $dbf->updateTable("member", $array_col, "id='" . $edit_id . "'");
		}
			
		?>
	  
		<form action="" method="post">
		<div class="row">
		
		  <div class="col-md-7">		  
			<div class="card">
			  <div class="card-header"><?php echo T_('Customer Detail');?></div>
			  
			  <div class="card-body">
			  
					    
						<div class="form-group row">
							<fieldset class="col-sm-4 form-group">
							  <label><?php echo T_('Customer Number');?></label>						  
							</fieldset>
						
							<div class="col-sm-8">
								<input disabled="" type="text" value="<?php echo $User_ID;?>" class="form-control"  >

							</div>							
						</div>
						

						<div class="form-group row">
							<fieldset class="col-sm-4 form-group">
							  <label><?php echo T_('Company Name');?></label>						  
							</fieldset>
							<div class="col-md-8">
								<input type="text" name="company_name" value="<?php echo $User_Company;?>" class="form-control" >	
							</div>								
						</div>
						
						<div class="form-group row">
							<fieldset class="col-sm-4 form-group">
							  <label><?php echo T_('Company Name <br/> (Alphabet)');?></label>						  
							</fieldset>		  
							<div class="col-md-8">
								<input type="text" class="form-control <?php echo ($company_alpha) ? 'has-error':'';?>" name="company_alphabet" id="company_alphabet" value="<?php echo $company_alphabet;?>">
								
							</div>
						</div>
						
						<div class="form-group row">
							<fieldset class="col-sm-4 form-group">
							  <label><?php echo T_('Responsibility Person');?></label>					  
							</fieldset>
							<div class="col-md-8">
								<input type="text" name="User_Name" value="<?php echo $User_Name;?>" class="form-control" >
							</div>								
						</div>
						
						<div class="form-group row">
							<fieldset class="col-sm-4 form-group">
							  <label><?php echo T_('Responsibility Person <br/> (Alphabet)');?></label>					  
							</fieldset>
							<div class="col-md-8">
								<input type="text" name="User_Name_alphabet" value="<?php echo $hovaten_alphabet;?>" class="form-control" >
							</div>								
						</div>
						
						<div class="form-group row">							
							<fieldset class="col-4 form-group">
							  <label><?php echo T_('Gender');?></label>						  
							</fieldset>
							
							<div class="col-md-8">
								<div class="form-check form-check-inline mr-1">
								<input class="form-check-input" id="man" type="radio" value="male" name="User_Gender" <?php echo (($User_Gender=="male")?"checked":""); ?> >
								<label class="form-check-label" for="man"><?php echo T_('Male');?></label>
								</div>
								<div class="form-check form-check-inline mr-1">
								<input class="form-check-input" id="female" type="radio" value="female" name="User_Gender"  <?php echo (($User_Gender=="female")?"checked":""); ?>>
								<label class="form-check-label" for="female"><?php echo T_('Female');?></label>
								</div>
								
							</div>
						</div>
						

						<div class="form-group row">
							<fieldset class="col-4 form-group">
							  <label><?php echo T_('Address');?></label>						  
							</fieldset>
							
							<div class="col-md-8">								
								
								<div class="mb-3">
								<input class="form-control" id="" type="text" name="postal_code" placeholder="<?php echo T_('Postal code');?>" value="<?php echo $postal_code;?>"/>
								</div>
								<div class="mb-3">
									<input class="form-control" id="" type="text" name="prefecture" placeholder="<?php echo T_('Prefectures');?>" value="<?php echo $prefecture;?>"/>
								</div>
								<div class="mb-3">
									<input class="form-control" id="" type="text" name="city" placeholder="<?php echo T_('City');?>" value="<?php echo $city;?>"/>
								</div>
								<div class="mb-3">
									<input class="form-control" id="" type="text" name="address" placeholder="<?php echo T_('Street, building, apartment, apartment name');?>" value="<?php echo $address;?>"/>
								</div>								
								
							</div>
							
						</div>

						<div class="form-group row">
							<fieldset class="col-4 form-group">
							  <label><?php echo T_('E-mail Address');?></label>						  
							</fieldset>
							<div class="col-md-8">
								<input type="text" value="<?php echo $User_Email;?>" name="User_Email" class="form-control" >
							</div>
							
						</div>

						<div class="form-group row">
							<fieldset class="col-4 form-group">
							  <label><?php echo T_('Telephone Number');?></label>						  
							</fieldset>
							<div class="col-md-8">
								<input type="text" name="User_Phone" value="<?php echo $User_Phone;?>" class="form-control" >
							</div>
							
						</div>
						
						<div class="form-group row">
							<fieldset class="col-4 form-group">
							  <label><?php echo T_('Payment Method');?></label>						  
							</fieldset>
							<div class="col-md-8 col-form-label">
								
								<div class="form-check form-check-inline mr-1">
									<input class="form-check-input" id="invoice" type="radio" value="invoice" name="payment_method" <?php echo ($utl->checked(array($payment_method),'invoice')) ? 'checked' : '';?>>
									<label class="form-check-label" for="invoice"><?php echo T_('Payment on invoice');?></label>
								</div>
								<div class="form-check form-check-inline mr-1">
									<input class="form-check-input" id="deposit" type="radio" value="deposit" name="payment_method" <?php echo ($utl->checked(array($payment_method),'deposit')) ? 'checked' : '';?>>
									<label class="form-check-label" for="deposit"><?php echo T_('Deposit');?></label>
								</div>
								<div class="form-check form-check-inline mr-1">
									<input class="form-check-input" id="credit" type="radio" value="credit" name="payment_method" <?php echo ($utl->checked(array($payment_method),'credit')) ? 'checked' : '';?>>
									<label class="form-check-label" for="credit"><?php echo T_('Credit Card');?></label>
								</div>		
							</div>	
						</div>
						<div class="form-actions">
							<button class="btn btn-warning btn-warn" name="edit_member" type="submit" >
							 <?php echo T_('Save');?></button>
						</div>
				</div> <!-- card-body -->	
			</div> <!-- card -->
		</div>
		<div class="col-md-5">
			<div class="card">
				  <div class="card-header"><?php echo T_('Settings');?></div>
				  <div class="card-body">
							<div class="form-group row">
								<fieldset class="col-4 form-group">
								  <label><?php echo T_('Show price?');?></label>						  
								</fieldset>
								<div class="col-md-8">
									<label class="switch switch-label switch-pill switch-outline-primary show_price" style="margin-bottom: 0" data-id="<?php echo $edit_id;?>"><input class="switch-input" type="checkbox" <?php echo $checked ;?> /><span class="switch-slider" data-checked="✓" data-unchecked="✕"></span></label>
								</div>
								
							</div>
							<div class="form-group row">
								<label class="col-md-4 col-form-label"><?php echo T_('Price Setting');?></label>						  
								<div class="col-md-8 input-group">
									<input type="number" class="form-control price" name="set_price" value="<?php echo $get_price ? $get_price : $set_price;?>">
									<div class="input-group-append">
										<span class="input-group-text"><?php echo T_('JPY');?></span>
									</div>
								</div>
							</div>
							
							<div class="form-group row">
							<label class="col-md-4 col-form-label"><?php echo T_('Urgent Price Setting');?></label>						  
							<div class="col-md-8 input-group">
								<input type="number" class="form-control price" name="urgent_set_price" value="<?php echo $urgent_get_price ? $urgent_get_price : $urgent_set_price;?>">
								<div class="input-group-append">
									<span class="input-group-text"><?php echo T_('%');?></span>
								</div>
							</div>
							</div>
							<div class="form-actions">
									<button class="btn btn-warning btn-warn" name="edit_member" type="submit" >
									 <?php echo T_('Save');?></button>
								</div>
						
				  </div>
			</div>
			
		</div>
		
		<div class="col-md-12">
			<div class="card">
			  <div class="card-header"><?php echo T_('Patient List');?>
	
			  </div>
			  <div class="card-body">
				
				<table class="table table-striped table-bordered datatable table-vcenter">
					<thead>
					  <tr>
						<th><?php echo T_('Patient Code');?></th>
						<th><?php echo T_('Patient Name');?></th>
						<th><?php echo T_('Age');?></th>
						<th><?php echo T_('Gender');?></th>
					  </tr>
					</thead>
					<tbody>
					<?php

							$arrayMemberCurrent= array();
							$arrayMemberCurrent = $dbf->getPatientListArray($edit_id,$rowgetInfo,$arrayMemberCurrent);
							$arrayMemberCurrent = $dbf->array_sort_by_column($arrayMemberCurrent,"datecreated");

								foreach($arrayMemberCurrent as $row)
								{
									if($row["is_del"]!=1)
									{
									echo '<tr role="row" class="row_member '.$row['ma_id'].'">
												 <td>' . $row['ma_id'] . '</td>
												 <td>' . $row['hovaten'] . '</td>
												 <td>' . $row['age'] . '</td>
												 <td>' . $gender[$row['gender']] . '</td>';
											 echo'</tr>';
									}
								}
							
						  ?>
					  
					</tbody>
				  </table>
				  
			  </div>
			</div>
		  </div> <!-- col-md-12 -->
		  <!-- /.col-->
		  </form>
		  </div>
		  <!-- /.row-->
	  </div>
	</div>
  </main>
  <script src="js/custom/custom.js"></script>
   <script type="text/javascript">
//<![CDATA[
    $(document).ready(function() {
		
		var table = $('.datatable').DataTable( {
			"pagingType": "full_numbers",
			"bFilter": false,
			"language":
				{
					 "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/<?php echo $datatable[$locale];?>.json",
					 //"searchPlaceholder": "<?php echo T_('Search by Customer number, Company name, Contact person, Email address, Telephone number.');?>",
					 //"search": "",
				},
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			} ]
		} );

    });
 //]]>
</script>
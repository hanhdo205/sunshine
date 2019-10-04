<?php
    session_start();
	if($_SESSION["Free"]==1)
	{
	  $html->redirectURL("/system");
	  die();
	}
   
	$result = array("status"=>1,"msg"=>array());
    if(isset($_POST["verify-account"])) 
    {        
		foreach ($_POST as $key => $value) {
			$$key = $value;			
		}
		
		  $pass3 = md5($SecurityPassword2);
		  $rstcheck = $dbf->getDynamic("member", "id ='" . $_SESSION["member_id"] . "' and password2='".$pass3."'", "");
		  if ($dbf->totalRows($rstcheck) > 0) 
		  {
		
		$isValue= true;
		for($i=1;$i<=3;$i++)
		{		
			$listFile = "listFile".$i;
			if($_FILES[$listFile]["tmp_name"])
		    {
			 $filename = $_FILES[$listFile]['name'];
			 $ext = strtolower(substr($filename, strpos($filename,'.'), strlen($filename)-1));
			 $allowed_filetypes = array('.jpg','.gif','.png','.jpeg');
			 $max_filesize = 4200000;
			if(!in_array($ext,$allowed_filetypes))
			{				 
				 $result["status"] = 0;  
			     $result["msg"][] = "Only upload file .JPG, .GIF, .PNG";
				  echo '<div class="alert alert-danger alert-dismissable">
						   <h4><strong>Notice</strong></h4>
						   <p>Only upload file .JPG, .GIF, .PNG</p>
						</div>';
				 break;
				 
			}else
			{
				
					if(filesize($_FILES[$listFile]['tmp_name']) > $max_filesize)
					{						
						 $result["status"] = 0;  
						 $result["msg"][] = "File image very large.>4Mb";
						 echo '<div class="alert alert-danger alert-dismissable">
						   <h4><strong>Notice</strong></h4>
						   <p>File image very large.>4Mb</p>
						</div>';
						 break;
					}else
					{
						$newName = $ph_gh_member_id.'_img_'.session_id();
						$path = "upload/$newName$ext";
						copy($_FILES[$listFile]['tmp_name'], $path);
						$picture = "picture".$i;
						$$picture = $path;						
					}
			}
			
			
	      }else 
		  {
			 $result["status"] = 0;  
			 $result["msg"][] = "Please upload file"; 
		  }
		
        } /* end for */
		
		if($result["status"]==1)
		{
			
			 $info_Verify_member  = $dbf->getInfoVerifyMember("member_vetify",$_SESSION["member_id"]);
			
			 if($info_Verify_member)
			 {				 
				 $arrayVerify = array("DocumentType"=>$DocumentType,"IdNumber"=>$IdNumber,"IdDate"=>$IdDate,"User_Country"=>$User_Country,"GenderId"=>$GenderId,"Address"=>$Address,"CityTown"=>$CityTown,"ZipCode"=>$ZipCode,"StateRegion"=>$StateRegion,"Phone"=>$Phone,"picture1"=>$picture1,"picture2"=>$picture2,"picture3"=>$picture3,"datecreated"=>time());
				 $affect2 = $dbf->updateTable("member_vetify", $arrayVerify, "member_id='" . $_SESSION["member_id"]. "'");												                    
															 
			 }else
			 {
				  $arrayVerify = array("member_id"=>$_SESSION["member_id"],"DocumentType"=>$DocumentType,"IdNumber"=>$IdNumber,"IdDate"=>$IdDate,"User_Country"=>$User_Country,"GenderId"=>$GenderId,"Address"=>$Address,"CityTown"=>$CityTown,"ZipCode"=>$ZipCode,"StateRegion"=>$StateRegion,"Phone"=>$Phone,"picture1"=>$picture1,"picture2"=>$picture2,"picture3"=>$picture3,"status"=>0,"datecreated"=>time());
				  $affect2 = $dbf->insertTable("member_vetify", $arrayVerify); 
			 }
			 
			 if($affect2)
			 {
				
				echo '<div class="alert alert-danger alert-dismissable alert-success">
                       <h4><strong>Notice</strong></h4>
                       <p>Please waiting verify Administrator process in 24h !!!</p>
                    </div>';
			 }
		}
		
		}else
          {
               echo '<div class="alert alert-danger alert-dismissable">
                   <h4><strong>Notice</strong></h4>
                   <p>Password 2 is wrong</p>
                </div>';
          }
	
	}
?>
<link href="/css/system/template/css/main.css" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
function getHora() {
   date = new Date();   
   return " "+date.getHours()+':'+date.getMinutes()+':'+date.getSeconds();
};

$( function() {
   $( "#models_IdDate" ).datepicker(
   {	
        changeMonth: true,
		changeYear: true,
		dateFormat: 'dd-mm-yy' + getHora(),
	});
  });
</script>

<section id="main">
	<!-- WRAP -->
	<div class="wrap">
	 <section id="content">
            <div id="main-container">
                  <div id="page-content" style="min-height: 318px;">
                      <div class="block">
                           <div class="block-title">
                              <h2>Verify Account</h2>
                           </div>
         
		 
		 <div class="row"> 
			<div class="col-md-12">
						<form action="" enctype="multipart/form-data" method="post">
						
						<div class="box box-default">                       
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Select document</label><span class="notify">* <span class="field-validation-valid" data-valmsg-for="DocumentType" data-valmsg-replace="true"></span></span>
                                        <select name="DocumentType" id="DocumentType" class="form-control" required="">
                                               <option value="1">Passport</option>
                                               <option value="2">ID</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Passport/ID/DL number</label> <span class="notify">* <span class="field-validation-valid" data-valmsg-for="IdNumber" data-valmsg-replace="true"></span></span>
                                        <input class="form-control" data-val="true" data-val-required="Please complete Id Number" id="models_IdNumber" name="IdNumber" required="required" value="" type="text">
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Id Date</label> <span class="notify">* <span class="field-validation-valid" data-valmsg-for="IdDate" data-valmsg-replace="true"></span></span>
                                        <input class="form-control" data-val="true" data-val-date="The field IdDate must be a date." data-val-required="Please complete Id Date" id="models_IdDate" name="IdDate" required="required" value="" type="text">
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Country</label> <span class="notify"><span class="field-validation-valid" data-valmsg-for="CountryId" data-valmsg-replace="true"></span></span>
                                        <select class="form-control" name="User_Country" id="User_Country" required>
											 <option value="">-- Select Country --</option>	
											<?php
											   $rstcountries = $dbf->getDynamic("countries", "status=1", "countries_name asc");
											   while ($row = $dbf->nextData($rstcountries)) {
												  echo '<option ' . (($User_Country == $row['id']) ? "selected=''" : "") . ' value="' . $row['id'] . '">' . $row['countries_name'] . '</option>';
												}
											 ?>
										</select>
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Gender</label> <span class="notify"><span class="field-validation-valid" data-valmsg-for="GenderId" data-valmsg-replace="true"></span></span>
                                        <select name="GenderId" id="GenderId" class="form-control" required="">
                                                <option value="1">Male</option>
                                                <option value="2">Female</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Address</label> <span class="notify">* <span class="field-validation-valid" data-valmsg-for="Address" data-valmsg-replace="true"></span></span>
                                        <input class="form-control" data-val="true" data-val-required="Please complete Address" id="models_Address" name="Address" required="required" value="" type="text">
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>City/Town</label> <span class="notify">* <span class="field-validation-valid" data-valmsg-for="CityTown" data-valmsg-replace="true"></span></span>
                                        <input class="form-control" data-val="true" data-val-required="Please complete City/Town" id="models_CityTown" name="CityTown" required="required" value="" type="text">
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>ZipCode</label> <span class="notify">* <span class="field-validation-valid" data-valmsg-for="ZipCode" data-valmsg-replace="true"></span></span>
                                        <input class="form-control" data-val="true" data-val-required="Please complete ZipCode" id="models_ZipCode" name="ZipCode" required="required" value="" type="text">
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>State/Region</label> <span class="notify">* <span class="field-validation-valid" data-valmsg-for="StateRegion" data-valmsg-replace="true"></span></span>
                                        <input class="form-control" data-val="true" data-val-required="Please complete State/Region" id="models_StateRegion" name="StateRegion" required="required" value="" type="text">
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Phone</label> <span class="notify">* <span class="field-validation-valid" data-valmsg-for="Phone" data-valmsg-replace="true"></span></span>
                                        <input class="form-control" data-val="true" data-val-regex="Invalid phone number" data-val-regex-pattern="^(\(([0-9]{2,3})\))?([0-9]{8,11})$" data-val-required="Please enter your phone number" id="models_Phone" name="Phone" required="required" value="" type="text">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Passport/ID/DL image (Fontside)</label> <span class="notify">*</span>
                                        <input name="listFile1" required="" type="file">
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Passport/ID/DL image (Backside)</label> <span class="notify">*</span>
                                        <input name="listFile2" required="" type="file">
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Proof of Addresses</label> <span class="notify">*</span>
                                        <input name="listFile3" required="" type="file">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Security Password</label> <span class="notify">* <span class="field-validation-valid" data-valmsg-for="SecurityPassword" data-valmsg-replace="true"></span></span>
                                        <input class="form-control" data-val="true" data-val-length="The Security Password must be at least 6 characters long." data-val-length-max="100" data-val-length-min="6" data-val-required="Please complete Security Password" id="models_SecurityPassword" name="SecurityPassword2" placeholder="Your account Security Password" required="required" type="password">
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="break"></div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary btn-lg btn-flat" name="verify-account">Submit</button>
                        </div>
                    </div>
			</form>            
			</div>
        </div>		
		</div>
		
		<div class="block">
			  <div class="block-title">
				 <h2>Verify Account Status</h2>
			  </div>

				<div class="box box-default">       
					<div class="box-body">
						<div id="no-more-tables">
							<table class="table table-striped table-bordered table-vcenter table-hover dataTable no-footer" role="grid" aria-describedby="example-datatable_info">
                                <thead>
									<tr>
										<th>Images</th>
										<th>ID Number</th>
										<th>Admin Note</th>
										<th>Create Date</th>									
										<th>Status</th>
									</tr>
								</thead>
								<tbody>
								<?php 
									 $info_Verify_member  = $dbf->getInfoVerifyMember("member_vetify",$_SESSION["member_id"]);			
									 /*print_r($info_Verify_member);*/
									 if($info_Verify_member)
									 {
										?>
									    <tr>
										<th>
											<?php 
											for($i=1;$i<=3;$i++){
												echo '<a target="_blank" href="'.$info_Verify_member["picture".$i].'"><img src="'.$info_Verify_member["picture".$i].'" align="absmidle" width="80" height="80" style="margin-right: 10px;"></a>';
											}
											?>
										</th>
										<th><?php echo $info_Verify_member["IdNumber"]?></th>
										<th><?php echo $info_Verify_member["note"]?></th>
										<th><?php echo date("Y-m-d",$info_Verify_member["datecreated"])?></th>										
										<th><?php echo (($info_Verify_member["status"]==1)?"Verify":"Not verify")?></th>
									</tr>
										<?php 
									 }
								?>
								
								</tbody>
							</table>
						</div>
					   
					</div>
				</div>
		</div>
			
</div>
</div>

<div class="clearfix"></div>
     </section>
</div>
<div class="clearfix"></div>
</section>
<div class="clearfix"></div>
	
	
	

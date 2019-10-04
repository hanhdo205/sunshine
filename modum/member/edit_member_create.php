<?php
if( $_SESSION["Free"]==1)
{
  $html->redirectURL("/system");
}else
{
	/*
     if($_SESSION["currentmember"]==0)
     {
         $html->redirectURL("/confirm_by_password.aspx");
         exit();
     }
	 */
	 
	  if($rowgetInfo["roles_id"]==15)                                                       
       {
          $html->redirectURL("/no_member_create");
          exit();
       }
function checked($value,$pattern) {
	if($value==$pattern) return 'checked';
	return '';
}
?>
<script>
function fixAspect(img) {
  var $img = $(img),
    width = $img.width(),
    height = $img.height(),
    tallAndNarrow = width / height < 1;
  if (tallAndNarrow) {
    $img.addClass('tallAndNarrow');
  }
  $img.addClass('loaded');
}
jQuery(document).ready(function ($) {
	$("#cmnd").change(function() {
	  	
	  		var filename = this.files[0].name;
		  	//$('.file-input').val(filename);
			var file_count = $('#userform input[type=file]').get(0).files.length
		  	$('.file-input').val(file_count + ' <?php echo _FILESELECTED;?>');
			$('.cmnd-photo').removeClass('d-block');	
			$('.cmnd-photo').addClass('d-none');	
	  	
	});	
	$("#gpkd").change(function() {
	  	
	  		var filename = this.files[0].name;
		  	$('.pdf-input').val(filename);
				
	  	
	});	
});
var readURLs = function(input,div) {
	$('#' + div).empty();   
	var number = 0;
	$.each(input.files, function(value) {
		var reader = new FileReader();
		
		reader.onload = function (e) {
			var id = (new Date).getTime();
			
			$('#' + div).prepend('<span class="slider_img"><img id='+id+' class="thumb remove_img_preview" src='+e.target.result+' data-index='+number+' /></span>');
			//$('#up_images').prepend('<img id='+id+' src='+e.target.result+' width="100px" height="100px" style="margin-right:10px;" data-index='+number+' onclick="removePreviewImage('+id+','+number+')"/>');
			number++;
		};
		reader.readAsDataURL(input.files[value]);
	});
}
</script>
<style>

.fancybox-close-small {
  background-image: url('https://cdn.jsdelivr.net/fancybox/1.3.4/fancybox.png');
  background-position: -40px 0px;
  width: 30px;
  height: 30px;
  top: -15px !important;
  right: -15px !important;
  text-indent: -9999px;
}
</style>
<script src="/css/system/template/js/vendor/modernizr-2.8.3.min.js"></script>
<link rel="stylesheet"  type="text/css" href="/css/jquery.dataTables.min.css"/>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" type="text/css" href="/js/fancybox/jquery.fancybox.min.css" media="screen" />
<script type="text/javascript" src="/js/fancybox/jquery.fancybox.min.js"></script>
<script>
function getHora() {
   date = new Date();   
   return " "+date.getHours()+':'+date.getMinutes()+':'+date.getSeconds();
};

$( function() {
   $( "#User_date_end" ).datepicker(
   {	
        changeMonth: true,
		changeYear: true,
		dateFormat: 'dd-mm-yy'+ getHora(),
	});
 });
	
</script>
<section id="main">
	<!-- WRAP -->
	<div class="wrap">

     <!-- USERMENU -->
		
		<!-- /USERMENU -->
		<!-- CONTENT -->
	 <section id="content">
            <div id="main-container">
                  <div id="page-content" style="min-height: 318px;">
                        <div class="block">
                            <div class="block-title">
                                <h2><?php echo _ACCOUNT;?></h2>
                            </div>

                            <?php
                            $edit_id = (int)$_GET["id"];
                            //check quuyen edit account
                            if($dbf->checkEditMember($rowgetInfo["id"],$edit_id))
                            {

                            $infor_account_edit = $dbf->getInfoColum("member",$edit_id);
                            $User_ID   =  $infor_account_edit["ma_id"];
                            $User_Des   =  $infor_account_edit["description"];
                            $User_Avatar   =  $infor_account_edit["picture"];
                            $User_Login   =  $infor_account_edit["tendangnhap"];
                            $User_Password   =  $infor_account_edit["password3"];
                            $User_Name = $infor_account_edit["hovaten"];
                            $User_Gender = $infor_account_edit["gioitinh"];
                            $cmnd = $infor_account_edit["cmnd"];
                            $gpkd = $infor_account_edit["gpkd"];
                            $User_Age = $infor_account_edit["age"];
                            $User_Email = $infor_account_edit["email"];
                            $User_Mobile = $infor_account_edit["didong"];
                            $User_Address = $infor_account_edit["diachi"];
                            $User_fb = $infor_account_edit["fb"];
							$User_Price = $infor_account_edit["price"];
							$User_Tax = $infor_account_edit["tax"];
                            $User_RegisteredDatetime = date("d-m-Y H:i:s",$infor_account_edit["datecreated"]);
							$User_date_end = "";
							if($infor_account_edit["date_end"])
							{
							   $User_date_end = date("d-m-Y h:s:i",$infor_account_edit["date_end"]);	
							}
							
                            $User_UserGroup = $infor_account_edit["packages_id"];

                            if (isset($_POST["edit_member"])) 
							{
                              foreach ($_POST as $key => $value) {
                                $$key = $dbf->filter($value);
                              }
							  if($User_Price=="")
							  {
								  $User_Price = 0;
							  }
								
								$listFile = "listFile";
								if($_FILES[$listFile]["tmp_name"][0])
									{
									$slide = array();
									foreach($_FILES[$listFile]["tmp_name"] as $i => $name) {
										 $filename = $_FILES[$listFile]['name'][$i];
										 /*$ext = strtolower(substr($filename, strpos($filename,'.'), strlen($filename)-1));*/
										 $ext = pathinfo(strtolower($filename), PATHINFO_EXTENSION);
										 $allowed_filetypes = array('jpg','gif','png','jpeg');
										 $max_filesize = 4200000;
										if(!in_array($ext,$allowed_filetypes))
										{				 
											 $result["status"] = 0;  
											 $result["msg"][] = "Only upload file .JPG, .GIF, .PNG";
											  echo '<div class="alert alert-danger alert-dismissable">
													   <h4><strong>Notice</strong></h4>
													   <p>Only upload file .JPG, .GIF, .PNG</p>
													</div>';
												$isValue = false;
											 
										}else
										{
											
												if(filesize($_FILES[$listFile]['tmp_name'][$i]) > $max_filesize)
												{						
													 $result["status"] = 0;  
													 $result["msg"][] = "File image very large.>4Mb";
													 echo '<div class="alert alert-danger alert-dismissable">
													   <h4><strong>Notice</strong></h4>
													   <p>File image very large.>4Mb</p>
													</div>';
													 $isValue = false;
												}else
												{	$picture = "piccmnd";
													$newName = $infor_account_edit["ma_id"].'_'.$picture.'_'.$filename;
													$path = "upload/".$newName;
													move_uploaded_file($_FILES[$listFile]['tmp_name'][$i], $path);
													$slide[] = $path;
												}
										}
									}
									if(isset($picture)) $$picture = implode(',',$slide);
								  }
								  
								  // gpkd upload
								  if($_FILES["gpkd"]["tmp_name"])
									{
											 $gpkdfilename = $_FILES["gpkd"]['name'];
											 /*$ext = strtolower(substr($filename, strpos($filename,'.'), strlen($filename)-1));*/
											 $ext = pathinfo(strtolower($gpkdfilename), PATHINFO_EXTENSION);
											 $allowed_filetypes = array('pdf');
											 $max_filesize = 4200000;
											if(!in_array($ext,$allowed_filetypes))
											{				 
												 //$result["status"] = 0;  
												 //$result["msg"][] = "Only upload file .PDF";
												  echo '<div class="alert alert-danger alert-dismissable">
														   <h4><strong>Notice</strong></h4>
														   <p>Only upload file .PDF</p>
														</div>';
													$isValue = false;
												 
											}else
											{
												
													if(filesize($_FILES["gpkd"]['tmp_name']) > $max_filesize)
													{						
														 //$result["status"] = 0;  
														 //$result["msg"][] = "File image very large.>4Mb";
														 echo '<div class="alert alert-danger alert-dismissable">
														   <h4><strong>Notice</strong></h4>
														   <p>File image very large.>4Mb</p>
														</div>';
														 $isValue = false;
													}else
													{	$gpkdpic = "gpkd";
														$newgpkdName = $infor_account_edit["ma_id"].'_'.$gpkdpic.'_'.$gpkdfilename;
														$gpkdpath = "upload/".$newgpkdName;
														move_uploaded_file($_FILES["gpkd"]['tmp_name'], $gpkdpath);
														$$gpkdpic = $gpkdpath;
													}
											}
											
										
									} // end gpkd upload

                              //$pass3 = md5($pass3);
                              //$rstcheck = $dbf->getDynamic("member", "ma_id ='" . $rowgetInfo["ma_id"] . "' and password3='".$pass3."'", "");
                              //if ($dbf->totalRows($rstcheck) > 0) {
                                 $array_col = array("hovaten" => $User_Name,"age" => $User_Age,"gioitinh" => $User_Sex,"fb" => $User_Facebook,"email" => $User_Email, "diachi" => $User_Address, "didong" => $User_Mobile,"price"=>$User_Price,"tax"=>$User_Tax,"dateupdated"=>time());
								 if($piccmnd != '') {
									$array_col["cmnd"] = $piccmnd;
								 } elseif($file_name != '') {
									 $array_col["cmnd"] = $file_name;
								 } else {
									$array_col["cmnd"] = $cmnd;
								 }
								 if($gpkd != '') {
										$array_col["gpkd"] = $gpkd;
									}
								 if($User_date_end)
								 {
									 $array_col["date_end"] = strtotime($User_date_end); 
								 }
                                 $affect = $dbf->updateTable("member", $array_col, "id='" . $edit_id . "'");
                                 if ($affect > 0)
                                 {
									?>
									<script>window.location = window.location.href;</script>
                                    <?php echo '<div class="alert alert-success alert-dismissable">
                                       <h4><strong>Notice</strong></h4>
                                       <p>Edit member successfull !!!</p>
                                    </div>';

                                 } else
                                 {
                                      echo '<div class="alert alert-danger alert-dismissable">
                                       <h4><strong>Notice</strong></h4>
                                       <p>Edit member wrong !!!</p>
                                    </div>';
                                 }


                              /*} else
                              {
                                   echo '<div class="alert alert-danger alert-dismissable">
                                       <h4><strong>Notice</strong></h4>
                                       <p>Password 2 is wrong</p>
                                    </div>';
                              }*/
                            }

                          ?>
                            <form id="userform" class="form-horizontal form-bordered" method="post" action="" enctype="multipart/form-data">
								<div class="form-group">
									<div class="row">
										<div class="col-md-2">
											
											<div class="div-avatar circle"><img id="avatar-img" class="rounded-circle mx-auto d-block select-image" src="<?php echo $User_Avatar ? $User_Avatar : HOST . '/style/images/packages/user.png';?>" onload="fixAspect(this);" />
											
											</div>
										</div>
										<div class="col-md-6">
											<h3><?php echo $User_Name;?></h3>
											<?php $ban_nick = $dbf->getDynamic("user_banned", "ip=$edit_id", "");
											$isban = $dbf->totalRows($ban_nick);
											if($isban) {
											while( $banrow = $dbf->nextData($ban_nick))
												{
													echo '<button type="button" class="btn btn-primary btn-sm unban_this" data-id="'.$edit_id.'">'._IPBAN.'</button>';
												}
											}
											?>
											<?php echo $User_Des;?>
										</div>
										<div class="col-md-4">
											<div class="alert alert-success" role="alert">
												  <h4 class="alert-heading text-sm-center"><?php echo _USERRANKING;?></h4>
												  <?php echo $dbf->getMemberRanking('history_sales',$edit_id);?>
													<p class="text-sm-center"><a href="<?=HOST?>ranking.aspx"><?php echo _SEEOTHER;?></a></p>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
									   <label for="User_ID" class="col-md-3 control-label"><?php echo _ID;?></label>
									   <div class="col-md-9"> <input type="text" tabindex="-1" readonly="" placeholder="Automatically generated" value="<?php echo $User_ID;?>" class="form-control" name="User_ID" id="User_ID"> </div>
									</div>
									<div class="row">
									   <label for="User_Email" class="col-md-3 control-label"><?php echo _USERNAME;?> <span class="text-danger">*</span></label>
									   <div class="col-md-9"> <input disabled="" type="text" value="<?php echo $User_Login;?>" class="form-control" name="username" id="username" required> </div>
									</div>
									<div class="row">
									   <label for="User_Password" class="col-md-3 control-label"><?php echo _PWD;?> <span class="text-danger">*</span></label>
									   <div class="col-md-9"> <input disabled="" type="text" value="<?php echo $User_Password;?>" class="form-control" name="userpassword" id="userpassword" required> </div>
									</div>

								 </div>                             
							 
							  
							 
                             <div class="form-group">
								<div class="row">
                                   <label for="User_Name" class="col-md-3 control-label"><?php echo _FULLNAME;?> <span class="text-danger">*</span></label>
                                   <div class="col-md-9"> <input type="text" value="<?php echo $User_Name;?>" class="form-control" name="User_Name" id="User_Name" required oninvalid="this.setCustomValidity('<?php echo _INVALID;?>')" oninput="setCustomValidity('')"> </div>
                                </div>
                                
								<div class="row">
                                   <label for="User_ID_card" class="col-md-3 control-label"><?php echo _IDINVN;?></label>
                                   <div class="col-md-9 User_ID_card cmnd-img">
								   <div id ="up_slides"></div><div id="area_id"></div>
								   <?php 
								   $file_exists = false;
								   $slide_arr = explode(',',$cmnd);
								   foreach($slide_arr as $photo) {
									   if(file_exists($photo)) {
										   $file_exists = true;?>
										   <a href="<?php echo $photo;?>" data-fancybox="images" data-width="2048" data-height="1365"><img class="cmnd-photo mx-auto d-block select-image" src="<?php echo $photo;?>" width="100px"/></a>
									   <?php } else {
										   //echo $cmnd;
										}								   
									 
								   } ?>
								   <span class="div-select-cmnd"><input type="text" name="file_name" class="file-input input full upload form-control" placeholder="<?php echo _NOFILESELECT;?>" value="<?php echo ($file_exists) ? '' : $cmnd;?>" autocomplete="off" style="padding: 3px !important;background: #fff;">
									<label for="cmnd" class="select-cmnd btn btn-primary btn-sm"><?php echo _SELECTFILE;?></label></span>
									<input multiple="1" id="cmnd" name="listFile[]" type="file" style="visibility:hidden;" onchange="readURLs(this,'up_slides');" >
								   
									</div>
                                </div>
								
								<div class="row">
                                   <label for="User_GPKD" class="col-md-3 control-label"><?php echo _CERTIFICATE;?></label>
                                   <div class="col-md-9 User_ID_card cmnd-img">
								   <?php
								   if(file_exists($gpkd)) {
										   $gpkd_exists = true;?>
										   <a href="<?php echo $gpkd;?>" class="fancybox" data-fancybox-type="iframe"><?php echo '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>&nbsp;' . $gpkd;?></a>
									   <?php } else {
										   //echo $gpkd;
										}
								   ?>
								   <span class="div-select-gpkd"><input type="text" name="gpkd_file" class="pdf-input input full upload form-control" placeholder="<?php echo _NOFILESELECT;?>" value="<?php echo ($gpkd_exists) ? '' : $gpkd;?>" autocomplete="off" style="padding: 3px !important;background: #fff;">
									<label for="gpkd" class="select-cmnd btn btn-primary btn-sm"><?php echo _SELECTFILE;?></label></span>
									<input id="gpkd" name="gpkd" type="file" style="visibility:hidden;"> 
								   
									</div>
                                </div>
								<div class="row">
                                   <label for="User_Age" class="col-md-3 control-label"><?php echo _AGE;?></label>
                                   <div class="col-md-9"> <input type="text" value="<?php echo $User_Age;?>" class="form-control" name="User_Age" id="User_Age"> </div>
                                </div>
								<div class="row">
									<label for="User_Sex" class="col-md-3 control-label"><?php echo _SEX;?></label>
									<div class="col-md-9">
										<div class="form-check form-check-inline">
										   <input class="form-check-input" type="radio" name="User_Sex" id="male" value="male" <?php echo checked( $User_Gender, 'male' ); ?> /><label class="form-check-label" for="male"><?php echo _MALE;?></label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="radio" name="User_Sex" id="female" value="female" <?php echo checked( $User_Gender, 'female' ); ?> /><label class="form-check-label" for="female"><?php echo _FEMALE;?></label>
										</div>
									</div>
                                </div>
								
                                <div class="row">
                                   <label for="User_Email" class="col-md-3 control-label"><?php echo _EMAIL;?> <span class="text-danger">*</span></label>
                                   <div class="col-md-9"> <input type="email" value="<?php echo $User_Email;?>" class="form-control" name="User_Email" id="User_Email" required oninvalid="this.setCustomValidity('<?php echo _INVALID;?>')" oninput="setCustomValidity('')"> </div>
                                </div>
                                <div class="row">
                                   <label for="User_Mobile" class="col-md-3 control-label"><?php echo _TEL;?> <span class="text-danger">*</span></label>
                                   <div class="col-md-9">
                                      <input type="tel" value="<?php echo $User_Mobile;?>" class="form-control" name="User_Mobile" id="User_Mobile" required oninvalid="this.setCustomValidity('<?php echo _INVALID;?>')" oninput="setCustomValidity('')">
                                   </div>
                                </div>
                                <div class="row">
                                   <label for="User_Address" class="col-md-3 control-label"><?php echo _ADDRESS;?></label>
                                   <div class="col-md-9"> <input type="text" value="<?php echo $User_Address;?>" class="form-control" name="User_Address" id="User_Address"> </div>
                                </div>
								<div class="row">
                                   <label for="User_Facebook" class="col-md-3 control-label"><?php echo _FACEBOOK;?></label>
                                   <div class="col-md-9"> <input type="text" value="<?php echo $User_fb;?>" class="form-control" name="User_Facebook" id="User_Facebook"> </div>
                                </div>
                             </div>
                                <div class="form-group">
								    <div class="row">
									   <label for="User_Price" class="col-md-3 control-label"><?php echo _WHOLESALE;?>(VND) <span class="text-danger">*</span></label>
									   <div class="col-md-9"> <input type="text" value="<?php echo $User_Price;?>" class="form-control" name="User_Price" id="User_Price" required oninvalid="this.setCustomValidity('<?php echo _INVALID;?>')" oninput="setCustomValidity('')"> </div>
									</div>
									
									<div class="row">
									   <label for="User_Tax" class="col-md-3 control-label"><?php echo _TAX;?> <span class="text-danger">*</span></label>
									   <div class="col-md-9"> <input type="text" value="<?php echo $User_Tax;?>" class="form-control" name="User_Tax" id="User_Tax" required oninvalid="this.setCustomValidity('<?php echo _INVALID;?>')" oninput="setCustomValidity('')"> </div>
									</div>
								
                                    <div class="row">
                                        <label for="User_RegisteredDatetime" class="col-md-3 control-label"><?php echo _DATEENROLL;?></label>
                                        <div class="col-md-6"> <input type="text" disabled="" value="<?php echo $User_RegisteredDatetime; ?>" data-date-format="dd-mm-YYYY" class="form-control input-datepicker" name="User_RegisteredDatetime" id="User_RegisteredDatetime"> </div>
                                    </div>								
									
									
									
									<div class="row">
										<label for="User_date_end" class="col-md-3 control-label"><?php echo _DATEWITHDRAWAL;?></label>
										<div class="col-md-6"> <input type="text" value="<?php echo $User_date_end; ?>" class="form-control" name="User_date_end" id="User_date_end"> </div>
									</div>
									
									
									<div class="row">
									   <label for="User_quantity" class="col-md-3 control-label"><?php echo _TOTALSHIPMENTQTY;?></label>
									   <div class="col-md-6"> <input type="text" disabled="" class="form-control" value="<?php echo number_format($dbf->getMemberDelivery("history_sales",$edit_id,"quantity"),0);?>" > </div>
									</div>
								<div class="row">
                                   <label for="User_history_payment" class="col-md-3 control-label"><?php echo _TOTALPAIDAMOUNT;?>(VND)</label>
                                   <div class="col-md-6"> <input type="text" disabled="" class="form-control" value="<?php echo number_format($dbf->getMemberDelivery("history_payment",$edit_id,"price"),0);?>" > </div>
                                </div>
								<div class="row">
                                   <label for="User_actual_sale" class="col-md-3 control-label"><?php echo _ACTUALSALE;?></label>
                                   <div class="col-md-6"> <input type="text" disabled="" class="form-control" value="<?php echo number_format($dbf->getActualColum("actual_sales",$edit_id,"quantity"),0);?>" > </div>
                                </div>
                                </div>
                                <div class="form-group form-actions"><div class="row">
                                    <div class="col-lg-4 col-md-5 col-sm-5 col-xs-8 col-md-offset-4">
                                        <div class="input-group"> <!--<input type="password" placeholder="Password 2" class="form-control" name="pass3" required>--> <span class="input-group-btn">
                                        <button class="btn btn-effect-ripple btn-info" name="back_member_list" type="button" onclick="window.location.href='<?=HOST?>member-list.aspx'" style="overflow: hidden; position: relative;"><?php echo _BACK;?></button> </span>
                                        </div>

                                    </div>
                                    <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4 col-md-offset-1">
                                        <span class="input-group-btn">
                                         <button class="btn btn-effect-ripple btn-primary" name="edit_member" type="submit" style="overflow: hidden; position: relative;"><?php echo _SAVE;?></button></span>
                                    </div>

                                </div>
                                </div>
                            </form>

                            <?php }else
                                {
                                    echo '<div class="alert alert-danger alert-dismissable">
                                       <h4><strong>Notice</strong></h4>
                                       <p>You can not edit this member !!!</p>
                                    </div>';
                                }
                                ?>

                        </div>
                    </div>
					<!-- history sale !-->
						<?php $member_id = $edit_id;
						include("history_sales_table.php");?>
					<!-- history sale !-->
            </div>
    <div class="clearfix"></div>
     </section>
</div>
<div class="clearfix"></div>
</section>
<div class="clearfix"></div>
<?php
}
?>
<script type="text/javascript">
//<![CDATA[
    jQuery(document).ready(function($) {
        $('[data-fancybox="images"]').fancybox({
			afterLoad : function(instance, current) {
				var pixelRatio = window.devicePixelRatio || 1;

				if ( pixelRatio > 1.5 ) {
					current.width  = current.width  / pixelRatio;
					current.height = current.height / pixelRatio;
				}
			},
			closeClick  : true,
			openEffect  : 'fade',
			closeEffect : 'fade',
			scrolling   : false,
			padding     : 0,
			autoScale: false,
			smallBtn : true,
			toolbar  : false
        });
		
		$(".fancybox").fancybox({
			afterLoad : function(instance, current) {
				var pixelRatio = window.devicePixelRatio || 1;

				if ( pixelRatio > 1.5 ) {
					current.width  = current.width  / pixelRatio;
					current.height = current.height / pixelRatio;
				}
			},
			closeClick  : true,
			openEffect  : 'fade',
			closeEffect : 'fade',
			scrolling   : false,
			padding     : 0,
			autoScale: false,
			smallBtn : true,
			toolbar  : false
		});
    });
 //]]>
</script>
<script type="text/javascript">
                $(document).ready(function($) {
					// when the user clicks on unban_this
					$('.unban_this').on('click', function(){
						var unbanid = $(this).data('id');

						$.ajax({
							url: ajax_url,
							type: 'post',
							data: {
								'unbanid': unbanid
							},
							success: function(response){
								$('.unban_this').remove();
							}
						});
					});
                })
            </script>
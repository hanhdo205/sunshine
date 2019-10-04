<?php
session_start();
date_default_timezone_set('Asia/Bangkok');
include ('../../class/defineConst.php');
include ('../../class/class.BUSINESSLOGIC.php');

if($_SESSION["roles_id"]==15)
{
	echo "<p>Don't permission access.</p>";
	die();
}

$dbf = new BUSINESSLOGIC();
$member_id = $_GET["id"];
if((int)$member_id)
{
    $rowgetInfo         = $dbf->getInfoColum("member",$member_id);
	if($rowgetInfo["parentid"]){
	  $infonguoibaotro    = $dbf->getInfoColum("member",$rowgetInfo["parentid"]);	
	}    
}

//print_r($rowgetInfo);

// Include Language file
		if(isset($_SESSION['language'])){
		 include "../languages/lang_".$_SESSION['language'].".php";
		}else{
		 include "../languages/lang_en.php";
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
.fancybox-image {
    width: auto !important;
}
</style>
<meta http-equiv="content-type" content="text/html;charset=UTF-8"/>
<link rel="stylesheet"  type="text/css" href="/css/style.pack.css"/>

<link href="/css/system/template/css/main.css" rel="stylesheet">

<!-- Fontfaces CSS-->
<link href="/css/font-face.css" rel="stylesheet" media="all">
<link href="/vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
<link href="/vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
<link href="/vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

<!-- Bootstrap CSS-->
<link href="/vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

<!-- Vendor CSS-->
<link href="/vendor/animsition/animsition.min.css" rel="stylesheet" media="all">

<!-- Main CSS-->
<link href="/css/theme.css" rel="stylesheet" media="all">
<link href="/css/theme_diamond.css" rel="stylesheet" media="all">



<style>
body {background: #fff;}
.form-group .row>div.col-md-8 {    border: 1px solid #e2e2e2;}
</style>

<!-- Jquery JS-->
<script src="//code.jquery.com/jquery-3.3.1.min.js"></script>
<link rel="stylesheet" type="text/css" href="/js/fancybox/jquery.fancybox.min.css" media="screen" />
<script type="text/javascript" src="/js/fancybox/jquery.fancybox.min.js"></script>
<link rel="stylesheet"  type="text/css" href="/css/jquery.dataTables.min.css"/>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

 <div class="block">
    <div class="form-group">
		<div class="row">
			<div class="col-md-2">
			</div>
			<div class="col-md-2">
				<input id="files" name="avatar" style="display:none" type="file" onchange="readURL(this);">
				<div class="div-avatar circle"><img id="avatar-img" class="rounded-circle mx-auto d-block select-image" src="<?php echo $rowgetInfo["picture"] ? HOST . $rowgetInfo["picture"] : HOST . '/style/images/packages/user.png';?>" onload="fixAspect(this);" />
				
				</div>
				<h3 class="text-sm-center"><?php echo $rowgetInfo["hovaten"]?></h3>
			</div>
			<div class="col-md-6">
				<div class="alert alert-success" role="alert">
					  <h4 class="alert-heading text-sm-center"><?php echo _USERRANKING;?></h4>
					  <?php echo $dbf->getMemberRanking('history_sales',$rowgetInfo["id"]);?>
				</div>
				
				<div><?php echo $rowgetInfo["description"];?></div>
				<div><a style="color:#fff;" class="btn btn-effect-ripple btn-secondary member_logs"  href="/modum/member/_logged_in_logs.php?id=<?php echo $rowgetInfo["id"];?>"><i class="fa fa-file-text-o" aria-hidden="true"></i> <?php echo _LOGS;?></a></div>
			</div>
			<div class="col-md-2">
			
			</div>
		</div>
	</div>
	 <div class="form-group mt-5">
		<div class="row">
		   <label for="User_ID" class="col-md-4 control-label text-sm-right"><?php echo _ID;?></label>
		   <div class="col-md-8"> <?php echo $rowgetInfo["ma_id"];?> </div>
		</div>
		<div class="row">
		   <label for="User_Email" class="col-md-4 control-label text-sm-right"><?php echo _USERNAME;?> </label>
		   <div class="col-md-8"> <?php echo $rowgetInfo["tendangnhap"];?> </div>
		</div>
		
	 </div>                             
	 
	 <div class="form-group">
		<div class="row">
		   <label for="User_Name" class="col-md-4 control-label text-sm-right"><?php echo _FULLNAME;?></label>
		   <div class="col-md-8"> <?php echo $rowgetInfo["hovaten"];?> </div>
		</div>
		
		<div class="row">
		   <label for="User_ID_card" class="col-md-4 control-label text-sm-right"><?php echo _IDINVN;?></label>
		    <div class="col-md-8 User_ID_card cmnd-img">
								   <div id ="up_slides"></div><div id="area_id"></div>
								   <?php 
								   $file_exists = false;
								   $slide_arr = explode(',',$rowgetInfo["cmnd"]);
								   foreach($slide_arr as $photo) {
										$file_headers = @get_headers(HOST . $photo);
										if($file_headers[0] == 'HTTP/1.0 404 Not Found'){
											  //echo "The file $filename does not exist";
												echo $rowgetInfo["cmnd"];
										} else if ($file_headers[0] == 'HTTP/1.0 302 Found' && $file_headers[7] == 'HTTP/1.0 404 Not Found'){
											//echo "The file $filename does not exist, and I got redirected to a custom 404 page..";
											echo $rowgetInfo["cmnd"];
										} else {
											$file_exists = true;?>
										   <a href="<?php echo HOST . $photo;?>" data-fancybox="images" data-width="2048" data-height="1365"><img class="cmnd-photo mx-auto d-block select-image" src="<?php echo HOST . $photo;?>" width="100px"/></a>
									   <?php
										}
									   
								   } 
								   if(!$file_exists)
									echo $rowgetInfo["cmnd"];?>
								   
									</div>
		</div>
		
		<div class="row">
		   <label for="User_GPKD" class="col-md-4 control-label text-sm-right"><?php echo _CERTIFICATE;?></label>
		   <div class="col-md-8"> 
				<?php 
					$file_headers = @get_headers(HOST . $rowgetInfo["gpkd"]);
					if($file_headers[0] == 'HTTP/1.0 404 Not Found'){
						  echo $rowgetInfo["gpkd"];
					} else if ($file_headers[0] == 'HTTP/1.0 302 Found' && $file_headers[7] == 'HTTP/1.0 404 Not Found'){
						echo $rowgetInfo["gpkd"];
					} else { ?>
						<a href="<?php echo HOST . $rowgetInfo["gpkd"];?>" class="fancybox" data-fancybox-type="iframe"><?php echo '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>&nbsp;' . $rowgetInfo["gpkd"];?></a>
			   <?php } ?>
		   
			</div>
		</div>
		<div class="row">
		   <label for="User_Age" class="col-md-4 control-label text-sm-right"><?php echo _AGE;?></label>
		   <div class="col-md-8"> <?php echo $rowgetInfo["age"];?> </div>
		</div>
		<div class="row">
			<label for="User_Sex" class="col-md-4 control-label text-sm-right"><?php echo _SEX;?></label>
			<div class="col-md-8">
				<?php echo $rowgetInfo["gioitinh"];?>
			</div>
		</div>
		
		<div class="row">
		   <label for="User_Email" class="col-md-4 control-label text-sm-right"><?php echo _EMAIL;?></label>
		   <div class="col-md-8"> <?php echo $rowgetInfo["email"];?> </div>
		</div>
		<div class="row">
		   <label for="User_Mobile" class="col-md-4 control-label text-sm-right"><?php echo _TEL;?></label>
		   <div class="col-md-8"><?php echo $rowgetInfo["didong"];?></div>
		</div>
		<div class="row">
		   <label for="User_Address" class="col-md-4 control-label text-sm-right"><?php echo _ADDRESS;?></label>
		   <div class="col-md-8"> <?php echo $rowgetInfo["diachi"];?> </div>
		</div>
		<div class="row">
		   <label for="User_Facebook" class="col-md-4 control-label text-sm-right"><?php echo _FACEBOOK;?></label>
		   <div class="col-md-8"> <?php echo $rowgetInfo["fb"];?> </div>
		</div>
		<div class="row">
		   <label for="User_Facebook" class="col-md-4 control-label text-sm-right"><?php echo _WHOLESALE;?>(VND)</label>
		   <div class="col-md-8"> <?php echo number_format($rowgetInfo["price"]);?> </div>
		</div>
		
		<div class="row">
		   <label for="User_RegisteredDatetime" class="col-md-4 control-label text-sm-right"><?php echo _DATEENROLL;?></label>
		   <div class="col-md-8"> <?php echo date("d-m-Y",$rowgetInfo["datecreated"]);?> </div>
		</div>

		<div class="row">
		   <label for="User_RegisteredDatetime" class="col-md-4 control-label text-sm-right"><?php echo _DATEWITHDRAWAL;?></label>
		   <div class="col-md-8"> <?php echo ($rowgetInfo["date_end"]) ? date("d-m-Y",$rowgetInfo["date_end"]) : '';?> </div>
		</div>
		<div class="row">
		   <label for="User_RegisteredDatetime" class="col-md-4 control-label text-sm-right"><?php echo _TOTALSHIPMENTQTY;?></label>
		   <div class="col-md-8"> <?php echo number_format($dbf->getMemberDelivery("history_sales",$rowgetInfo['id'],"quantity"),0);?> </div>
		</div>
		<div class="row">
		   <label for="User_RegisteredDatetime" class="col-md-4 control-label text-sm-right"><?php echo _TOTALPAIDAMOUNT;?>(VND)</label>
		   <div class="col-md-8"> <?php echo number_format($dbf->getMemberDelivery("history_payment",$rowgetInfo['id'],"price"),0);?></div>
		</div>
	 </div>
    <!-- history sale !-->
		<?php $member_id = $rowgetInfo['id'];
		include("history_sales_table.php");?>
	<!-- history sale !-->                       
                            
							  
</div>
<!-- Bootstrap JS-->
    <script src="/vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="/vendor/bootstrap-4.1/bootstrap.min.js"></script>
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
		$(".member_logs").fancybox({
			closeClick  : true,
			openEffect  : 'fade',
			closeEffect : 'fade',
			scrolling   : false,
			padding     : 0,
			type		: 'iframe',		
			autoScale: false,
			smallBtn : true,
			toolbar  : false
        });
    });
 //]]>
</script>
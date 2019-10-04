<?php
session_start();

include_once 'modum/class.template.php';

if(isset($_GET['order_id'])) {
	$_SESSION["order_id"] = $_GET['order_id'];
}
if(isset($_SESSION["order_id"])) {
	$order_id = $_SESSION["order_id"];
	$orders_query = $dbf->getjoinDynamic("orders","order_detail","tb1.id = tb2.order_id","tb1.id=$order_id","","tb2.id desc");
}
$order_array = array();
$msg = '';
$manager_class = '';
$operator_class = '';
$file_class = '';
$current_member_id=$rowgetInfo["id"];

while( $orders = $dbf->nextData($orders_query)){
	
	$order_number=$orders["order_number"];
	$order_date=$orders["order_date"];
	$dateupdated=$orders["dateupdated"];
	$shipping_date=$orders["shipping_date"];
	$quantity=$orders["quantity"];
	$tax=$orders["tax"];
	$total=$orders["total"];
	$ma_id=$orders["member_id"];
	$subtotal=$orders["subtotal"];
	$basic_price=$orders["basic_price"];
	$status=$orders["status"];
	$paid_date=$orders["paid_date"];
	$payment_method=$orders["payment_method"];
	$paid=$orders["paid"];
	$manager=$orders["manager"];
	$manager_id=$orders["manager_id"];
	$operator=$orders["operator"];
	$operator_id=$orders["operator_id"];
	$checker=$orders["checker"];
	$checker_id=$orders["checker_id"];
	$read_list = $orders['read_list'];
	$payment_status = $orders['payment_status'];
	$downloadable = $orders['downloadable'];
	//$response_person   =  $orders["contact_person"];
	//$company   =  $orders["company"];
	$order_array[] = array(
		'detail_id'=>$orders["id"],
		'detail_updated'=>$orders["detail_updated"],
		'patient_name'=>$orders["patient_name"],
		'gender'=>$orders["gender"],
		'production'=>$orders["production"],
		'shade'=>$orders["shade"],
		'position'=>unserialize($orders["position"]),
		'detail_total_fee'=>$orders["detail_total_fee"],
		'detail_quantity'=>$orders["detail_quantity"],
		'desired_date'=>$orders["desired_date"],
		'delivery_time'=>$orders["delivery_time"],
		'remarks'=>$orders["remarks"],
		'price_novat'=>$orders["price_novat"],
		'single_vat'=>$orders["single_vat"],
		'process_file'=>$orders["process_file"],
		'ship_file'=>$orders["ship_file"],
	);
	
	switch(true) { // in case pending task clicked
		case ($rowgetInfo["roles_id"] < 6) && ($status == 0) :
			$manager_class = ' has-error';
			break;
		case ($rowgetInfo["roles_id"] == 6) && ($status == 1 || $status == 0) :
			$operator_class = ' has-error';
			break;
		case ($rowgetInfo["roles_id"] == 7) && ($status == 2) :
			$download_file_class = ' has-error';
			break;
		case ($rowgetInfo["roles_id"] == 7) && ($status == 3) :
			$file_class = ' has-error';
			break;
		default:
			//do nothing
			break;
	}
	
	switch(true) { // in case notification clicked
		case ($status) :
			if($read_list=='') {
				$read_list = $current_member_id;
			} else {
				$read_list = $read_list . ',' . $current_member_id;
				$read_array = explode(',',$read_list);
				$read_array = array_unique($read_array);
				$read_list = implode(',',$read_array);
			}
			$array_read = array("read_list"=>$read_list);
			$dbf->updateTable("orders", $array_read, "id='" . $_SESSION['order_id'] . "'","NOSAVELOG",$_SESSION['order_id']);
			break;
		default:
			if($read_list=='') {
				$read_list = $current_member_id;
			} else {
				$read_list = $read_list . ',' . $current_member_id;
				$read_array = explode(',',$read_list);
				$read_array = array_unique($read_array);
				$read_list = implode(',',$read_array);
			}
			$array_read = array("read_list"=>$read_list);
			$dbf->updateTable("orders", $array_read, "id='" . $_SESSION['order_id'] . "'","NOSAVELOG",$_SESSION['order_id']);
			break;
	}
	
}

//$status_arr = array('0'=>__('Pending'),'1'=>__('Assigning'),'2'=>__('Assigning'),'3'=>__('Processing'),'4'=>__('Delivered'));
//$status=$status_arr[$order_status];

$infor_account_edit = $dbf->getInfoColum("member",$ma_id);
if($infor_account_edit) {
	$member_id   =  $infor_account_edit["ma_id"];
	$response_person   =  (isset($_SESSION['language']) && $_SESSION['language']=='ja_JP') ? $infor_account_edit["hovaten"] : $infor_account_edit["hovaten_alphabet"];
	$company   =  (isset($_SESSION['language']) && $_SESSION['language']=='ja_JP') ? $infor_account_edit["company"] : $infor_account_edit["company_alphabet"];
	$customer_email   =  $infor_account_edit["email"];
	$customer_language   =  $infor_account_edit["language"];
	$payment_method = $payment_method ? $payment_method : $infor_account_edit["payment_method"];
}
			
$base_url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http' ) . '://' .  $_SERVER['HTTP_HOST'];
$url = $base_url . $_SERVER["REQUEST_URI"];

$array_orders = array();
$$array_detail = array();
$orders_arr = 0;

if(isset($_POST['submit_send']))
{
	
	 unset($_SESSION["order"]); 
	 foreach($_POST as $key => $value){
			if(!is_array($value)){
			   $_SESSION["order"][$key] = $dbf->filter($value);
			   $$key = $dbf->filter($value);
			}else{
			   $_SESSION["order"][$key] = $value;
			}
        }
		
	$dateupdated = time();
	$shipping_date = time();
	
	$infor_account_manager = $dbf->getInfoColum("member",$manager_id);
	$manager_name = $infor_account_manager["hovaten"];

	$infor_account_operator = $dbf->getInfoColum("member",$operator_id);
	$operator_name = $infor_account_operator["hovaten"];

	$infor_account_checker = $dbf->getInfoColum("member",$checker_id);
	$checker_name = $infor_account_checker["hovaten"];
	
	$isvalue = false;
	if(isset($_FILES['ship_file'])) {
		//printf("<pre>%s</pre>",print_r($_FILES['ship_file'],true));die();
			$uploaddirectory = "upload/orders/";
			foreach($_FILES['ship_file']['name'] as $i => $name)
			{
				if(@$_FILES["ship_file"]["name"][$i] != "") {
					$isvalue = true;
					// now $name holds the original file name
					$tmp_name = $_FILES['ship_file']['tmp_name'][$i];
					$FileName = time() . '_' . $_FILES["ship_file"]["name"][$i];
					$error = $_FILES['ship_file']['error'][$i];
					$_SESSION["order"]["fileerror"][$i] = $error;
					$size = $_FILES['ship_file']['size'][$i];
					$type = $_FILES['ship_file']['type'][$i];
					$imagename	= explode(".",$name);
					$extfile 	= strtolower($imagename[1]);
					$allowedExts1 = array("zip", "rar", "stl");
					$allowed_filetypes = array("application/stl", "application/x-rar-compressed", "application/zip", "application/x-zip", "application/octet-stream", "application/x-zip-compressed");
					if(!in_array($extfile,$allowed_filetypes) && !in_array($extfile, $allowedExts1)) {
						$error = "Opps, something went wrong!";
						$isvalue = false;
						$_SESSION["order"]["fileerror"][$i] = T_("File type is invalid");
					}
					if($error === UPLOAD_ERR_OK )
					{
							// No errors
							 // You'll probably want something unique for each file.
							move_uploaded_file($tmp_name, $uploaddirectory.$FileName);
							
					}
					$_SESSION["order"]["stlfile"][$i] = $FileName;
				
				} else {
					if($_SESSION["order"]["stlfile"][$i] == "") {
						$error = "Opps, something went wrong!";
						$isvalue = false;
						$_SESSION["order"]["fileerror"][$i] = T_("Please chose a file");
						$_SESSION["order"]["stlfile"][$i] = '';
					}
				}
			}

	}
	
	$downloadable = (isset($_POST["downloadable"])) ? $_POST["downloadable"] : 0;
	
	if($paid && $paid >= $total) {
		$payment_status = 1;
	}
	
	switch(true) {
		case ($rowgetInfo["roles_id"]<=5) : // admin role only
			if(isset($_POST['manager_id']) && $_POST['manager_id']!='') {
				if($status==0) $status = 1;
				$array_orders = array("total"=>$total,"quantity"=>$quantity,"subtotal"=>$subtotal,"tax"=>$tax,"dateupdated"=>$dateupdated,"status"=>$status,"manager"=>$manager_name,"manager_id"=>$manager_id,"paid_date"=>$paid_date,"payment_method"=>$payment_method,"payment_status"=>$payment_status,"downloadable"=>$downloadable,"paid"=>$paid);
			}
			if(isset($_POST['operator_id']) && $_POST['operator_id']!='') {
				if($status < 2) $status = 2;
				$array_orders['status'] = $status;
				$array_orders['operator'] = $operator_name;
				$array_orders['operator_id'] = $operator_id;
			}
			if($downloadable && $status==4) {
				$array_orders['status'] = 5;
				$array_orders['read_list'] = "";
			}
		break;
		case ($rowgetInfo["roles_id"]<=6) : // from manager role
			if(isset($_POST['operator_id']) && $_POST['operator_id']!='') {
				if($status<2) $status = 2;
				// default is if($status==1) $status = 2;
				//default is $array_orders = array("dateupdated"=>$dateupdated,"status"=>$status,"operator"=>$operator_name,"operator_id"=>$operator_id);
				$array_orders = array("dateupdated"=>$dateupdated,"status"=>$status,"manager"=>$manager_name,"manager_id"=>$manager_id,"operator"=>$operator_name,"operator_id"=>$operator_id);
			}
		break;
		case ($rowgetInfo["roles_id"]<=7 && $isvalue) : //from operator role and shippable date uploaded
			if($status==3 && $downloadable == 0) $status = 4;
			elseif($status==3 && $downloadable == 1) $status = 5;
				$array_orders = array("read_list"=>"","dateupdated"=>$dateupdated,"status"=>$status,"shipping_date"=>$shipping_date);
			break;
	}
	
	if(!empty($array_orders)) {
		$orders_arr = $dbf->updateTable("orders", $array_orders, "id='" . $_SESSION['order_id'] . "'","UPDATE",$_SESSION['order_id']);
	}
	
	$msg = false;
	
	if($orders_arr > 0) { // if updated OK
		$msg_detail ='';
		$customer_msg_detail ='';
		foreach($_SESSION["order"]["detail_id"] as $i=>$v) {
			$gender = $_SESSION["order"]["patient_gender"][$i];
			$patient_name = $_SESSION["order"]["patient_name"][$i];
			//$production = $_SESSION["order"]["production"][$i];
			$production = $_SESSION['production'][$i];
			$shade = $_SESSION["order"]["shade"][$i];
			$position = serialize(explode(',',$_SESSION["order"]["position"][$i]));
			$detail_quantity = $_SESSION["order"]["detail_quantity"][$i];
			$desired_date = $_SESSION["order"]["desired_date"][$i];
			$delivery_time = $_SESSION["order"]["delivery_time"][$i];
			$remarks = $_SESSION["order"]["remarks"][$i];
			$process_file = $_SESSION["order"]["process_file"][$i];

			$stlfile = $_SESSION["order"]["stlfile"][$i];
			
			if($rowgetInfo["roles_id"]<6) {
				$array_detail = array("shade"=>$shade,"position"=>$position,"detail_quantity"=>$detail_quantity,"desired_date"=>$desired_date,"remarks"=>$remarks);
			}
			if($rowgetInfo["roles_id"]<8) {
				if(isset($_SESSION["order"]["stlfile"][$i]) && $stlfile != '') {
					$shipping_password = $utl->generateRandomString(10);
					$array_detail = array("ship_file"=>$stlfile,"shipping_password"=>$shipping_password);
					//$msg_detail .= sprintf(T_('Patient: %s - Password to download: %s <br>'),$patient_name,$shipping_password);
					$msg_detail .= sprintf(T_('Patient: %s <br>'),$patient_name);
					switch($customer_language) {
						case 'ja_JP' :
							//$customer_msg_detail .= '患者様名: '.$patient_name.' ダウンロード用パスワード: '.$shipping_password.' <br>';
							$customer_msg_detail .= '患者様名: '.$patient_name.'<br>';
							break;
						case 'vi_VN' :
							//$customer_msg_detail .= 'Tên bệnh nhân: '.$patient_name.' - Mật khẩu để tải file: '.$shipping_password.' <br>';
							$customer_msg_detail .= 'Tên bệnh nhân: '.$patient_name.'<br>';
							break;
						default :
							//$customer_msg_detail .= 'Patient: '.$patient_name.' - Password to download: '.$shipping_password.' <br>';
							$customer_msg_detail .= 'Patient: '.$patient_name.' <br>';
							break;
					}
				}
				
			}
			if(!empty($array_detail)) {
				$detail = $dbf->updateTable("order_detail", $array_detail, "id='" . $v . "'","UPDATE",$_SESSION['order_id']);
			}
			if ($detail > 0 && $isvalue)
				 {
					$msg = true;
				 }
				 
				 $order_array[$i] = array(
						'production'=>$production,
						'shade'=>$shade,
						'position'=>unserialize($position),
						'detail_total_fee'=>$orders["detail_total_fee"],
						'detail_quantity'=>$detail_quantity,
						'desired_date'=>$desired_date,
						'delivery_time'=>$delivery_time,
						'remarks'=>$remarks,
						'process_file'=>$process_file,
						'ship_file'=>$stlfile,
						'gender'=>$gender,
						'patient_name'=>$patient_name,
					);
			
		}
		unset($_SESSION["order"]["stlfile"]);
		unset($_SESSION["order"]["fileerror"]);
	}
	if($msg) {
		require("modum/class.phpmailer.php");
		$mail = new PHPMailer();
				
		$default_admin_email_info = $dbf->getInfoColum("setting",24);
		$admin_email = $default_admin_email_info['value'];
		
		$subject = "注文しましたデータを納品致します";
		
		$admin_order_link = HOST . 'order-detail.aspx/?order_id=' . $_SESSION['order_id'];
		$user_order_link = HOST . 'booking-detail.aspx/?order_id=' . $order_number;
		$order_detail = T_('Order Detail');
		
		switch($customer_language) {
			case 'ja_JP' :
				$customer_subject = 'データの準備が完了しました';
				$customer_header = '[VietQuocLab]データの準備が完了しました';
				$customer_order_detail = '注文詳細';
				$signature_text = '今後ともVietQuocLabをよろしくお願いいたします<br><br>お問い合わせ<br><a style="color:#5b9bd5" href="mailto:info@vql.jp">info@vql.jp</a></font><br><br>';
				$mail_footer_text = 'URL: ';
				$customer_text = '下記のリンクをクリックして、ログインの上、データをダウンロードしてください';
				break;
			case 'vi_VN' :
				$customer_subject = 'Dữ liệu  tải về';
				$customer_header = '[VietQuocLab] đã chuẩn bị xong dữ liệu tải về';
				$customer_order_detail = 'Chi tiết đặt hàng';
				$signature_text = 'Chúng tôi rất trân trọng những đóng góp của bạn dành cho VietQuocLab.<br>Hãy liên hệ <br><a style="color:#5b9bd5" href="mailto:info@vql.jp">info@vql.jp</a></font><br><br>';
				$mail_footer_text = 'Địa chỉ trang web: ';
				$customer_text = 'Bấm vào Link bên dưới, File sẽ được tải về';
				break;
			default :
				$customer_subject = 'Data is ready for download';
				$customer_header = '[VietQuocLab]Data is ready for download';
				$customer_order_detail = 'Order Detail';
				$signature_text = 'We look forward to your continued support for VietQuocLab.<br>Contact us<br><a style="color:#5b9bd5" href="mailto:info@vql.jp">info@vql.jp</a></font><br><br>';
				$mail_footer_text = 'Website URL: ';
				$customer_text = 'Please click the link below to login and download data';
				break;
		}
		
		$customer_message = Template::get_contents("modum/mail_template/shipable_upload.tpl", array('logo' => HOST.'images/logo.jpg', 'subject' => $customer_subject,'order_detail' => $customer_order_detail,'msg_detail' => $customer_msg_detail,'link' => $user_order_link,'signature_text'=>$signature_text,'customer_text'=>$customer_text,'mail_footer_text'=>$mail_footer_text, 'url' => HOST));
		
		$admin_message = Template::get_contents("modum/mail_template/shipable_upload.tpl", array('logo' => HOST.'images/logo.jpg', 'subject' => $customer_subject,'msg_detail' => $msg_detail,'order_detail' => $order_detail,'link' => $admin_order_link,'signature_text'=>$signature,'customer_text'=>$customer_text,'mail_footer_text'=>$mail_footer,'url' => HOST));
		
		$from = $arraySMTPSERVER["user"];
		$fromName = $arraySMTPSERVER["from"];
		
		$customer_param = array('EmailFrom'=>$from,'FromName'=>$fromName,'ReplyTo'=>$admin_email,'ReplyName'=>'VietQuocLab','EmailTo'=>$customer_email,'ToName'=>$response_person,'Subject'=>$customer_header,'Content'=>$customer_message);
		
		$admin_param = array('EmailFrom'=>$from,'FromName'=>$fromName,'ReplyTo'=>$admin_email,'ReplyName'=>'VietQuocLab','EmailTo'=>$admin_email,'ToName'=>'VietquocLab','Subject'=>$subject,'Content'=>$admin_message);
		
		$admin_mail = $dbf->sendmail($admin_param,$mail );
		if ($admin_mail && $infor_account_edit) 
		{
			$customer_mail = $dbf->sendmail($customer_param,$mail );
		}
	}

}
 
?>
<link href="vendors/bootstrap-daterangepicker/css/daterangepicker.min.css" rel="stylesheet" />

<link href="vendors/select2/css/select2.min.css" rel="stylesheet" />
<!-- Plugins and scripts required by this view-->
<script src="vendors/jquery.maskedinput/js/jquery.maskedinput.js"></script>
<script src="vendors/moment/js/moment.min.js"></script>
<script src="vendors/select2/js/select2.min.js"></script>
<script src="vendors/bootstrap-daterangepicker/js/daterangepicker.js"></script>

<link rel="stylesheet" href="css/custom/jquery-ui.css">
<script src="js/custom/jquery-ui.js"></script>
<?php if(isset($_SESSION['language']) && $_SESSION['language'] != 'en_US') { ?>
<script src="js/custom/datepicker-<?php echo $datepicker_lang[$_SESSION['language']];?>.js"></script>
<?php } ?>

<link rel="stylesheet" type="text/css" href="/js/fancybox/jquery.fancybox.min.css" media="screen" />
<script type="text/javascript" src="/js/fancybox/jquery.fancybox.min.js"></script>
<script src="js/custom/jquery.fileDownload.js"></script>
<script src="js/custom/order_detail.js"></script>

<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item">
		<a href="payment.aspx"><?php echo T_('Payment');?></a>
	  </li>
	  <li class="breadcrumb-item active"><?php echo T_('Order Detail');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">

		<div class="row">
		  <div class="col-md-12">
		  
			<div class="card">
			  <div class="card-header"><?php echo T_('Order Detail');?>
				<div class="card-header-actions">
					<small class="text-muted"><?php echo sprintf( T_('Last updated: %s'),date($date_format,$dateupdated));?></small>
					<a href="/modum/member/_data_logs.php" id="data-log" class="btn btn-sm btn-warning"><?php echo T_('View history update');?></a>
				</div>
			  </div>
			  <form>
			  <div class="card-body">
				<?php //printf("<pre>%s</pre>",print_r($_SESSION["order"],true)); 
				//echo $v;?>
					<div class="form-group row">
						<fieldset class="col-3 form-group">
							<div class="form-group row">
								<div class="col-12">
									<h4><?php echo T_('Customer Infomation');?></h4>
								</div>
							</div>
							<div class="form-horizontal">
								<div class="form-group row">
								  <label class="col-md-4"><?php echo T_('Customer Number');?></label>
								  <div class="col-md-8">
									<p class="form-control-static"><?php echo $member_id;?></p>
								  </div>
								
								  <label class="col-md-4"><?php echo T_('Company Name');?></label>
								  <div class="col-md-8">
									<p class="form-control-static"><?php echo $company;?></p>
								  </div>
								
								  <label class="col-md-4"><?php echo T_('Contact Person');?></label>
								  <div class="col-md-8">
									<p class="form-control-static"><?php echo $response_person;?></p>
								  </div>
								</div>
							</div>
						</fieldset>
						<fieldset class="col-3 form-group">
							<div class="mb-2">
							  <label><?php echo T_('Order Number');?></label>
							  <div class="input-group">
								<input class="form-control" id="order_date" type="text" placeholder="Order Number" value="<?php echo $order_number;?>"/>
							  </div>
						  	</div>
							<div>
								<label><?php echo T_('Operating Status');?></label>
								<select class="form-control select2-single" name="status" id="select2-1" disabled>
								  <option>---</option>
								  <option value="0" <?php echo $utl->selected(0,$status);?>><?php echo T_('Pending');?></option>
								  <option value="1" <?php echo $utl->selected(1,$status);?>><?php echo T_('Assigning');?></option>
								  <option value="2" <?php echo $utl->selected(2,$status);?>><?php echo T_('Assigning');?></option>
								  <option value="3" <?php echo $utl->selected(3,$status);?>><?php echo T_('Processing');?></option>
								  <option value="4" <?php echo $utl->selected(4,$status);?>><?php echo T_('Completed');?></option>
								  <option value="5" <?php echo $utl->selected(5,$status);?>><?php echo T_('Delivered');?></option>
								</select>
							    
						  </div>
						</fieldset>
						<fieldset class="col-3 form-group">
							<div class="mb-2">
								<label><?php echo T_('Order Date');?></label>
							  	<div class="input-group">
								<span class="input-group-prepend">
								  <span class="input-group-text">
									<i class="fa fa-calendar"></i>
								  </span>
								</span>
								<input id="orderdate" class="form-control date" type="text" value="<?php echo date($date_format,$order_date);?>"/>
							   </div>
							   <!--<small class="text-muted">ex. 2019/04/25 21:00</small>-->
							</div>
							<div>
								<label><?php echo T_('Shipping Date');?></label>
								<div class="input-group">
									<span class="input-group-prepend">
									  <span class="input-group-text">
										<i class="fa fa-calendar"></i>
									  </span>
									</span>
									<?php if($status < 4) { ?>
										<input id="delidate" class="form-control date" type="text" value="<?php echo $shipping_date ? date($short_date_format,$shipping_date) : '';?>"/>
									<?php } else { ?>
										<input id="delidate" class="form-control date" type="text" value="<?php echo $shipping_date ? date($date_format,$shipping_date) : '';?>"/>
									<?php } ?>
								</div>
								 <!--<small class="text-muted">ex. 2019/04/25 21:00</small>-->
							</div>
						</fieldset>
						<fieldset class="col-3 form-group">
							<?php if($rowgetInfo["roles_id"]<6) { ?>
								<div class="mb-2 <?php echo $manager_class;?>">
									<label><?php echo T_('Manager');?></label>
									<select class="form-control select2-single" name="manager_id" id="select2-2">
									  <option value="">---</option>
									  <?php
									  $result = $dbf->getDynamic("member", "roles_id=6", "id DESC");
										if ($dbf->totalRows($result) > 0) {
												$list = array();
											  while ($row = $dbf->nextData($result)) {
												  echo '<option value="'.$row["id"].'" '.$utl->selected($manager_id,$row["id"]).'>'.$row["hovaten"].'</option>';
											  }
										}
									  ?>
									</select>
									<small class="permission text-danger"><?php echo T_('Choose authorized people');?></small>
								</div>
							<?php } elseif($rowgetInfo["roles_id"]==6) { ?>
								<input type="hidden" name="manager_id" value="<?php echo $_SESSION['member_id'];?>">
							<?php } ?>
							<div class="<?php echo $operator_class;?>"> 
								<label><?php echo T_('Operator');?></label>
								<select class="form-control select2-single" name="operator_id" id="select2-3">
								  <option value="">---</option>
								  <?php
								  $result = $dbf->getDynamic("member", "roles_id=7", "id DESC");
									if ($dbf->totalRows($result) > 0) {
											$list = array();
										  while ($row = $dbf->nextData($result)) {
											  echo '<option value="'.$row["id"].'" '.$utl->selected($operator_id,$row["id"]).'>'.$row["hovaten"].'</option>';
										  }
									}
								  ?>
								</select>
								<small class="permission text-danger"><?php echo T_('Choose authorized people');?></small>
							</div>
						</fieldset>
						
					</div>
					<?php if($rowgetInfo["roles_id"]<6 || $rowgetInfo["roles_id"]==8) { ?>
					<div class="form-group row">
						
						<div class="col-sm-4">
							<div class="form-group row">
								<div class="col-12">
									<h4><?php echo T_('Billing Information');?></h4>
								</div>
							</div>
							<div class="form-horizontal">
								<div class="form-group row">
								  <label class="col-md-4 col-form-label"><?php echo T_('Paid Date');?></label>
								  <div class="col-md-8">
									<input class="form-control" id="paid_date" type="text" name="paid_date" autocomplete="off" placeholder="..." value="<?php echo $paid_date;?>" />
								  </div>
							    </div>
								<div class="form-group row">
								  <label class="col-md-4 col-form-label"><?php echo T_('Payment');?></label>
								  <div class="col-md-8">
									<select class="form-control payment_method" id="select2-5" name="payment_method" disabled>
									  <option value=""><?php echo T_('Please select');?></option>
									  <option value="credit" <?php echo $utl->selected('credit',$payment_method);?>><?php echo T_('Credit Card');?></option>
									  <option value="invoice" <?php echo $utl->selected('invoice',$payment_method);?>><?php echo T_('Payment on invoice');?></option>
									  <option value="deposit" <?php echo $utl->selected('deposit',$payment_method);?>><?php echo T_('Deposit');?></option>
									</select>
								  </div>
								</div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-horizontal">
								<div class="form-group row">
								  <label class="col-md-4 col-form-label"><?php echo T_('Total Amount');?></label>
									<div class="col-md-8">
										<div class="input-group">
											<input class="form-control total_amount" id="total" type="number" name="total" value="<?php echo $total;?>" disabled/>
											<div class="input-group-append">
												<span class="input-group-text"><?php echo T_('JPY');?></span>
											</div>
										</div>
									</div>
							    </div>
								<div class="form-group row">
								  <label class="col-md-4 col-form-label"><?php echo T_('Subtotal');?></label>
									  <div class="col-md-8">
									  <div class="input-group">
										<input class="form-control sub_total" id="novat" type="number" name="subtotal" value="<?php echo $subtotal;?>" disabled/>
										<div class="input-group-append">
											<span class="input-group-text"><?php echo T_('JPY');?></span>
										</div>
									  </div>
								  </div>
							    </div>
								<div class="form-group row">
								  <label class="col-md-4 col-form-label"><?php echo T_('Tax');?></label>
								  <div class="col-md-8">
									  <div class="input-group">
										<input class="form-control vat_field" id="vat" type="number" name="tax" value="<?php echo $tax;?>" disabled/>
										<div class="input-group-append">
											<span class="input-group-text"><?php echo T_('JPY');?></span>
										</div>
									  </div>
								  </div>
							    </div>
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-horizontal">
								<div class="form-group row">
								  <label class="col-md-4 col-form-label"><?php echo T_('Total QTY');?></label>
									<div class="col-md-8">
										<div class="input-group">
											<input class="form-control quantity_field" id="toothtotal" type="number" name="quantity" value="<?php echo $quantity;?>" disabled/>
											<div class="input-group-append">
												<span class="input-group-text"><?php echo ($quantity>1) ? T_("teeths") : T_("teeth");?></span>
											</div>
										</div>
									</div>
							    </div>
								<div class="form-group row">
								  <label class="col-md-4 col-form-label"> <?php echo T_('Paid');?></label>
									  <div class="col-md-8">
									  <div class="input-group">
										<input class="form-control paid_field" id="paid" type="number" name="paid" value="<?php echo $paid;?>" disabled/>
										<div class="input-group-append">
											<span class="input-group-text"><?php echo T_('JPY');?></span>
										</div>
									  </div>
								  </div>
							    </div>
								<div class="form-group row">
								  <label class="col-md-4"><?php echo T_('Mark as paid');?></label>
									  <div class="col-md-8">
									  <label class="switch switch-label switch-outline-primary">
										<input class="switch-input markaspaid" type="checkbox" value="2" name="downloadable" <?php echo ($utl->checked(array($downloadable),1)) ? 'checked' : '';?>>
										<span class="switch-slider" data-checked="✓" data-unchecked="✕"></span>
										</label>
										
									  
								  </div>
							    </div>
							</div>
						</div>
					</div>
					<?php } else { ?>
						<input class="form-control" id="paid_date" type="hidden" name="paid_date" />
					<?php } ?>
			  <div id="bookingdetailAccordion" data-children=".item">
				  <!-- Loop -->
				  <?php $key=0;
				  foreach($order_array as $order_detail) {
					  //printf("<pre>%s</pre>",print_r($order_detail,true));
						$position = implode(',',$order_detail['position']);
						$patient_gender = $order_detail['gender'];
						$_SESSION['production'][$key] = $order_detail['production'];
				  ?>
				  
				  <div class="item">
					  <a data-toggle="collapse" data-parent="#bookingdetailAccordion" href="#patientAccordion<?php echo $key;?>" aria-expanded="true" aria-controls="patientAccordion<?php echo $key;?>" class="order-accordion mb-3"><i class="accordion-icon rotate icon-arrow-up-circle font-2xl"></i></a>
						<input type="hidden" class="form-control" name="detail_id[]" value="<?php echo $order_detail['detail_id'];?>">
						<div class="collapse show form-group row" id="patientAccordion<?php echo $key;?>" role="tabpanel">
							<div class="col-sm-4">
								<div class="custom-bg">
									<div class="card-header custom-header"><?php echo T_('Patient Information');?></div>
									<div class="form-group row mt-3">
										<label class="col-md-3 col-form-label ml-3"><?php echo T_('Gender');?></label>
										<div class="col-md-8">
										  <input type="text" class="form-control" value="<?php echo T_($patient_gender);?>">
										  <input type="hidden" name="patient_gender[]" class="form-control" value="<?php echo $patient_gender;?>">
										</div>
									</div>
									<div class="form-group row custom-form-group">
										<label class="col-sm-3 col-form-label ml-3"><?php echo T_('Patient Name');?></label>
										<div class="col-sm-8">
										  <input type="text" class="form-control" value="<?php echo $order_detail['patient_name'];?>">
										  <input type="hidden" name="patient_name[]" class="form-control" value="<?php echo $order_detail['patient_name'];?>">
										</div>
									</div>
									<div class="card-header custom-header"><?php echo T_('Comments');?></div>
									<div class="form-group custom-textarea">
										<textarea class="form-control" rows="5" name="remarks[]" ><?php echo $order_detail['remarks'];?></textarea>
									</div>
								</div>
							</div>
							<div class="col-sm-8">
								<div class="custom-bg custom-bg-2">
									<div class="card-header custom-header"><?php echo sprintf( T_('Order Details (#%s)'),$order_detail['detail_id']);?></div>
									<div class="form-group row info_group mt-3">
										<div class="col-12 col-xl-6 col-md-12 col-lg-12">
											<div class="form-group row">
												<label class="col-md-3 col-form-label "><?php echo T_('Manufacturing');?></label>
												<div class="col-md-9">
												<input type="text" class="form-control" name="production[]" value="<?php echo $product_item[$order_detail['production']];?>">
												</div>
											</div>
											<div class="form-group row">
												<label class="col-md-3 col-form-label "><?php echo T_('VITA Shade');?></label>
												<div class="col-md-9">
												<input type="text" class="form-control" name="shade[]" value="<?php echo T_($order_detail['shade']);?>">
												</div>
											</div>
											<div class="form-group row">
												<label class="col-md-3 col-form-label"><?php echo T_('Tooth Numbering');?></label>
												<div class="col-md-9">
												<input type="text" class="form-control" name="position[]" value="<?php echo $position;?>">
												</div>
											</div>
											<div class="form-group row">
												<label class="col-md-3 col-form-label"><?php echo T_('Quantity');?></label>
												<div class="col-md-9">
												<input type="number" class="form-control" name="detail_quantity[]" value="<?php echo $order_detail['detail_quantity'];?>">
												</div>
											</div>
											
											<div class="form-group row">
												<label class="col-md-3 col-form-label"><i class="icon-tag"></i></label>
												<div class="col-md-9">
													<label class="col-form-label">
														<input id="delivery_time" class="form-control date" type="hidden" name="delivery_time[]" value="<?php echo $order_detail['delivery_time'];?>"/>
														<?php echo T_($delivery_text[$order_detail['delivery_time']]);?>
													</label>
												</div>
											</div>

											<div class="form-group row">
												<label class="col-md-3 col-form-label"><?php echo T_('Shipping Request Date');?></label>
												<div class="col-md-9">
													<div class="input-group">
														<span class="input-group-prepend">
														  <span class="input-group-text">
															<i class="fa fa-calendar"></i>
														  </span>
														</span>
														<input id="date" class="form-control date" type="text" name="desired_date[]" value="<?php echo $order_detail['desired_date'];?>"/>
														
												  </div>
												</div>
											</div>
											
										</div>
										<div class="col-sm-3 mb-3 custom-proce-data download_file<?php echo $download_file_class;?>">
											<div class="proce-data">
												<h5 class="proce-data-title">
													<?php echo T_('Manufacturing Data');?>
												</h5>
												<div class="proce-data-content">
													<p class="proce-data-content-txt">
														<?php echo $order_detail['process_file'];?>
													</p>
													<p class="btn-text text-center">
														<a href="javascript:void(0)" class="btn btn-primary btn-custom download_file download_file_manufacture" data-id="<?php echo $order_id ;?>" data-user="<?php echo $operator_id;?>" data-file="<?php echo $order_detail['process_file'];?>"><?php echo T_('Download');?></a>
														<!--<a href="<?php echo HOST;?>upload/orders/<?php echo $order_detail['process_file'];?>" class="btn btn-primary btn-custom download_file" data-id="<?php echo $order_id ;?>" data-user="<?php echo $operator_id;?>" download><?php echo T_('Download');?></a>-->
														<input type="hidden" name="process_file[]" value="<?php echo $order_detail['process_file'];?>">
													</p>
													<p class="proce-data-txt">
														<?php echo T_('Expired Date');?>
														<span class="date"><?php 
														$given_date = date("Y/m/d H:i",$order_date);
														$date = date($date_format, strtotime($given_date . ' +90 days'));
														echo $date;?></span>
													</p>
												</div>
											</div>
											<small class="permission text-danger"><?php echo T_('Download this file');?></small>
										</div>

										<div class="col-sm-3 custom-proce-data  mb-3 ship_file<?php echo $file_class;?>">
											<div class="proce-data">
												<h5 class="proce-data-title">
													<?php echo T_('Shippable Data');?>
												</h5>
												<div class="proce-data-content">
														<?php if(isset($_SESSION["order"]["fileerror"][$key]) && $_SESSION["order"]["fileerror"][$key] != '0' && $rowgetInfo["roles_id"]==7) { ?>
														<div class="col">
														<div class="alert alert-danger alert-dismissible fade show file_field_error_<?php echo $key;?>" role="alert">
															<?php echo T_($_SESSION["order"]["fileerror"][$key]);?>
															<button class="close" type="button" data-dismiss="alert" aria-label="Close">
															<span aria-hidden="true">×</span>
															</button>
														</div>
														</div>
													<?php } else { ?>
														<p class="proce-data-content-txt ship_file_uploaded_<?php echo $key;?>">
															<?php 
															if($rowgetInfo["roles_id"]<7) {
																$ship_downloaded = '';
																if($checker_id) $ship_downloaded = '✓';
																echo ($order_detail['ship_file']) ? '<a href="javascript:void(0)" class="btn btn-primary btn-custom download_file download_file_shippable" data-id="'. $order_id .'" data-user="'.$_SESSION['member_id'].'" data-ship="'.$order_detail['ship_file'].'">'. T_('Check it').'</a> ' . $ship_downloaded : T_('No file uploaded');
															} else {
																echo ($order_detail['ship_file']) ? $order_detail['ship_file'] : T_('No file uploaded');
															} ?>
														</p>
													<?php } ?>
														<p class="btn-text text-center">
															<label for="stlinput_<?php echo $key;?>" class="btn btn-primary btn-custom"><?php echo ($order_detail['ship_file']) ? T_('Edit') : T_('Upload');?></label>
															<input type="hidden" name="stlfile[<?php echo $key;?>]" class="stl_input_<?php echo $key;?> input full upload form-control" placeholder="<?php echo T_('No file chosen');?>" value="<?php echo (isset($_SESSION["order"]["stlfile"][$key])) ? $_SESSION["order"]["stlfile"][$key] : $order_detail['ship_file'];?>" autocomplete="off" style="padding: 3px !important;background: #fff;">
															<input id="stlinput_<?php echo $key;?>" type="file" name="ship_file[]" style="visibility:hidden;">
														</p>
														<?php if($order_detail['ship_file']) { ?>
															<p class="proce-data-txt">
																<?php echo T_('Expired Date');?>
																<span class="date">
																<?php 
																$given_date = date("Y/m/d H:i",$shipping_date);
																$date = date($date_format, strtotime($given_date . ' +90 days'));
																echo $date;?>
																</span>
															</p>
														<?php } ?>
													
												</div>
											</div>
											<small class="permission text-danger"><?php echo T_('Upload the final file here');?></small>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div> <!-- .item -->
					<script type="text/javascript">
						$(document).ready(function () {
							
							var fileSelectEle = document.getElementById('stlinput_<?php echo $key;?>');
							fileSelectEle.onchange = function ()
							{
								if(fileSelectEle.value.length == 0) {
									$('.ship_file_uploaded_<?php echo $key;?>').text('');
									$('.stl_input_<?php echo $key;?>').val('');
								} else {
									$('.ship_file_uploaded_<?php echo $key;?>').text(fileSelectEle.files[0].name);
									$('.stl_input_<?php echo $key;?>').val(fileSelectEle.files[0].name);
									$('.file_field_error_<?php echo $key;?>').remove();
								}
							}
						});
						</script>
				  <?php $key++; } ?>
					<!-- End loop -->
				</div> <!-- #bookingdetailAccordion -->
			  </div>
			   <div class="card-footer">
			   
				  <a href="javascript:void(0);" onclick="goBack(-1)" class="btn btn-link btn-lg active" role="button" aria-pressed="true"><i class="fa fa-angle-double-left" aria-hidden="true"></i> <?php echo T_('Go back to Order List');?></a>
				  
				</div>
			</form>
			</div>
		  </div>
		  <!-- /.col-->
		  </div>
		  <!-- /.row-->
	  </div>
	</div>
  </main>
  <script src="js/coreui/advanced-forms.js"></script>
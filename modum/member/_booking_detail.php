<?php
session_start();
$current_member_id = $_SESSION['member_id'];
$infor_account_edit = $dbf->getInfoColum("member",$current_member_id);
$show_price = $infor_account_edit["show_price"];
if(isset($_GET['order_id'])) {
	$_SESSION["order_id"] = $_GET['order_id'];
}
if(isset($_SESSION["order_id"])) {
	$order_number = $_SESSION["order_id"];
	$orders_query = $dbf->getjoinDynamic("orders","order_detail","tb1.id = tb2.order_id","tb1.order_number=$order_number AND tb1.member_id=$current_member_id","","tb2.id desc");
}
$order_array = array();
while( $orders = $dbf->nextData($orders_query)){
	//printf("<pre>%s</pre>",print_r($orders,true));
	$read_list = $orders['read_list'];
	$status=$orders["status"];
	$id=$orders["order_id"];
	$shipping_date=$orders["shipping_date"];
	$payment_status=$orders["payment_status"];
	$payment_method=$orders["payment_method"];
	$downloadable=$orders["downloadable"];
	$order_array[] = array(
				"detail_id"=>$orders["detail_id"],
				"order_date"=>$orders["order_date"],
				"patient_name"=>$orders["patient_name"],
				"production"=>$orders["production"],
				"shade"=>$orders["shade"],
				"position"=>unserialize($orders["position"]),
				"status"=>$orders["status"],
				"detail_total_fee"=>$orders["detail_total_fee"],
				"detail_quantity"=>$orders["detail_quantity"],
				"desired_date"=>$orders["desired_date"],
				"remarks"=>$orders["remarks"],
				"quantity"=>$orders["quantity"],
				"subtotal"=>$orders["subtotal"],
				"tax"=>$orders["tax"],
				"basic_price"=>$orders["basic_price"],
				"total"=>$orders["total"],
				"ship_file"=>$orders["ship_file"],
				"priority"=>$orders["delivery_time"],
		);
}
$base_url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http' ) . '://' .  $_SERVER['HTTP_HOST'];
$url = $base_url . $_SERVER["REQUEST_URI"];
$current_member_id=$rowgetInfo["id"];

switch(true) { // in case notification clicked
	case ($status == 5) :
		if($read_list=='') {
			$read_list = $current_member_id;
		} else {
			$read_list = $read_list . ',' . $current_member_id;
			$read_array = explode(',',$read_list);
			$read_array = array_unique($read_array);
			$read_list = implode(',',$read_array);
		}
		$array_read = array("read_list"=>$read_list);
		$dbf->updateTable("orders", $array_read, "id='" . $id . "'","NOSAVELOG",$id);
		break;
	default:
		//do nothing
		break;
}
?>
<link rel="stylesheet" href="js/jconfirm/jquery-confirm.css">
<script src="js/jconfirm/jquery-confirm.js"></script>
<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item">
		<a href="booking-history.aspx"><?php echo T_('Order list');?></a>
	  </li>
	  <li class="breadcrumb-item active"><?php echo T_('Order detail');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">
		<div class="row">
		  <div class="col-md-12 mb-5">
		  
		  <div class="card">
			<div class="card-header"><?php echo T_('Order number');?>
				<strong><?php echo $order_number;?></strong>
				<!--<button class="btn btn-sm btn-warning btn-warn float-right reorder_btn"><i class="icon-reload"></i> <?php echo T_('Re-order');?></button>-->
				<a class="btn btn-sm btn-secondary float-right mr-1 d-print-none" href="<?php echo $url;?>#" onclick="javascript:window.print();">
				<i class="fa fa-print"></i> <?php echo T_('Print');?></a>
				<span class="btn btn-sm float-right mr-1"><?php echo sprintf( T_('Order date: %s'),date($date_format,$order_array[0]['order_date']));?></span>
				<!--<a class="btn btn-sm btn-info float-right mr-1 d-print-none" href="#">
				<i class="fa fa-save"></i> Save</a>-->
			</div>
			<div class="card-body">
			<?php 
			$status_arr = array('0'=>__('Pending'),'1'=>__('Assigning'),'2'=>__('Assigning'),'3'=>__('Processing'),'4'=>__('Completed'),'5'=>__('Delivered'));
			//$payment_arr = array('0'=>__('Unpaid'),'1'=>__('Paid'),'2'=>__('Invoice'));
			$payment_arr = array('0'=>__('Unpaid'),'1'=>__('Paid'),'2'=>__('Unpaid'),'3'=>__('Partial payment'));
			$payment_method_arr = array('credit'=>__('Credit Card'),'invoice'=>__('Payment on invoice'),'deposit'=>__('Deposit'));
			$is_paid = $payment_status;
			$downloadable = $downloadable;
			$downloadable_text = T_('Please wait...');
			$status=$status_arr[$order_array[0]['status']];
			$payment_status=$payment_arr[$is_paid];
			$payment_method_text=($payment_method!='') ? $payment_method_arr[$payment_method] : T_('Unselected');
			
			?>
				<div class="row mb-4">
					<div class="col-sm-4">
						<h6 class="mb-3 font-weight-bold"><?php echo T_('Order Status');?></h6>
						<div><?php echo T_($status);?></div>
					</div>
					<div class="col-sm-4">
						<h6 class="mb-3 font-weight-bold"><?php echo T_('Order payment');?></h6>
						<div><?php echo T_($payment_status);?></div>
					</div>

					<div class="col-sm-4">
						<h6 class="mb-3 font-weight-bold"><?php echo T_('Payment method');?></h6>
						<div><?php echo T_($payment_method_text);?></div>
					</div>

				</div>
				<div class="table-responsive-sm">
					<table class="table table-striped datatable table-vcenter">
						<thead>
						  <tr>
							<th><?php echo T_('Patient name');?></th>
							<th><?php echo T_('Item');?></th>
							<?php if($show_price=='yes') { ?><th><?php echo T_('Price / Unit (Inc.Tax)');?></th><?php } ?>
							<th><?php echo T_('Quantity');?></th>
							<?php if($show_price=='yes') { ?><th><?php echo T_('Subtotal (Inc. Tax)');?></th><?php } ?>
							<th><?php echo T_('Expedited Shipping');?></th>
							<th><?php echo T_('Shipping date');?></th>
							<th><?php echo T_('Remarks');?></th>
							<th><?php echo T_('Shippable data');?></th>
						  </tr>
						</thead>
						<tbody>
						<?php foreach($order_array as $key=>$value) {
							if($downloadable && $order_array[0]['status'] >= 4) {
								$given_date = date("Y/m/d H:i",$shipping_date);
								$date = date('Y/m/d H:i', strtotime($given_date . ' +90 days'));
								$downloadable_text = '<button class="btn btn-primary btn-custom download_file download_file_able" data-id="'.$value['detail_id'].'">'. T_('Download') . '</button><br>' . T_('Exp: ') . $date;
							}
							$teeth = ($value['detail_quantity']>1) ? sprintf( T_("%d teeths"),(int) $value['detail_quantity']): sprintf( T_("%d tooth"),(int) $value['detail_quantity']);
							$shipping_date_text = ($shipping_date) ? date($short_date_format,$shipping_date) : date($short_date_format,$row['nearest_date']);
							$priority = $delivery_icon[$value['priority']];
							if($show_price=='yes') {
								echo '<tr>
									<td>'.$value['patient_name'].'</td>
									<td>'.T_('Item').' '.$product_item[$value['production']].'<br>'.T_('VITA Shade').' '.T_($value['shade']).'<br>'.T_('Position').' '.implode(',',$value['position']).'</td>
									<td>'.sprintf( T_('%s JPY'),number_format($value['basic_price'],0)).'</td>
									<td>'.$teeth.'</td>
									<td>'.sprintf( T_('%s JPY'),number_format($value['detail_total_fee'],0)).'</td>
									<td>'.$priority.'</td>
									<td>'.$shipping_date_text.'</td>
									<td>'.$utl->shorten_text($value['remarks'],30,'...',true).'</td>
									<td>'.$downloadable_text.'</td>
								  </tr>';
							} else {
								echo '<tr>
									<td>'.$value['patient_name'].'</td>
									<td>'.T_('Item').' '.$product_item[$value['production']].'<br>'.T_('VITA Shade').' '.T_($value['shade']).'<br>'.T_('Position').' '.implode(',',$value['position']).'</td>
									<td>'.$teeth.'</td>
									<td>'.$priority.'</td>
									<td>'.$shipping_date_text.'</td>
									<td>'.$utl->shorten_text($value['remarks'],30,'...',true).'</td>
									<td>'.$downloadable_text.'</td>
								  </tr>';
							}
						} ?>
						  
						</tbody>
					</table>
				</div>
				<?php if($show_price=='yes') { ?>
				<div class="row">
					<div class="col-lg-4 col-sm-5"></div>
					<div class="col-lg-4 col-sm-5 ml-auto">
						<table class="table table-clear">
							<tbody>
								<tr>
									<td class="left">
										<strong><?php echo T_('Total quantity');?></strong>
									</td>
									<td class="right"><?php echo ($order_array[0]['quantity']>1) ? sprintf( T_("%d teeths"), (int) $order_array[0]['quantity']) : sprintf( T_("%d tooth"), (int) $order_array[0]['quantity']);?></td>
								</tr>
								<tr>
									<td class="left">
										<strong><?php echo T_('Subtotal');?></strong>
									</td>
									<td class="right"><?php echo sprintf( T_('%s JPY'),number_format($order_array[0]['subtotal'],0));?></td>
								</tr>
								<tr>
									<td class="left">
										<strong><?php echo T_('VAT');?></strong>
									</td>
									<td class="right"><?php echo sprintf( T_('%s JPY'),number_format($order_array[0]['tax'],0));?></td>
								</tr>
								<tr>
									<td class="left">
										<strong><?php echo T_('Total');?>	</strong>
									</td>
									<td class="right">
										<strong><?php echo sprintf( T_('%s JPY'),number_format($order_array[0]['total'],0));?></strong>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<?php } ?>
			</div>
			<div class="card-footer d-print-none">
						<form class="d-print-none d-inline" name="contact" action="contact.aspx" method="post">
						<input type="hidden" name="order_id" value="<?php echo $order_number;?>">
						<!-- <button class="btn btn-warning btn-warn d-print-none" type="submit">
						<?php echo T_('Contact');?></button> -->
						</form>
					<a href="booking-history.aspx" id="goback" class="btn btn-link btn-lg active" role="button" aria-pressed="true"><i class="fa fa-angle-double-left" aria-hidden="true"></i> <?php echo T_('Go Back To The List');?></a>
					  
					  
					</div>
			</div> <!-- card -->
		  </div> <!-- col-md-12 -->
		  <!-- /.col-->
		  </div>
		  <!-- /.row-->
	  </div>
	</div>
  </main>
  <script type="text/javascript">
	var translate = {
		password_to_download:"<?php echo T_('Enter password to download this file');?>",
		yes_btn:"<?php echo T_('Yes');?>",
		cancel_btn:"<?php echo T_('Cancel');?>",
		close_btn:"<?php echo T_('Close');?>",
		wrong_password:"<?php echo T_('The password you entered was wrong!');?>",
		invalid_password:"<?php echo T_('Please enter valid password');?>",
		enter_password:"<?php echo T_('Enter password to unlock file');?>",
		password_form:"<?php echo T_('Password');?>",
		password_placeholder:"<?php echo T_('Enter password here');?>",
		submit_btn:"<?php echo T_('Submit');?>",
	};
  </script>
  <script src="js/custom/jquery.fileDownload.js"></script>
  <script src="js/custom/custom.js"></script>
  
  <div id="modalOrder" class="modal fade"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          
          <h4 class="modal-title" id="myModalLabel"><?php echo T_('Reorder');?></h4>
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        </div>
        <div class="modal-body">
			<div id="accordion" role="tablist">
			<?php foreach($order_array as $key=>$value) {
				$show = '';
				if($key==0) $show = 'show';
				$remarks = ($value['remarks']) ? $value['remarks'] : '...';
				?>
					<div class="card mb-0">
					  <div class="card-header" id="heading_<?php echo $key;?>" role="tab">
						<h6 class="mb-0">
						  <a data-toggle="collapse" href="#collapse_<?php echo $key;?>" aria-expanded="true" aria-controls="collapse_<?php echo $key;?>" class="order-accordion"><?php echo sprintf(T_('%d. Patient name: %s'),$key+1,$value['patient_name']);?></a>
						</h6>
					  </div>
					  <div class="collapse <?php echo $show;?>" id="collapse_<?php echo $key;?>" role="tabpanel" aria-labelledby="heading_<?php echo $key;?>" data-parent="#accordion">
						<div class="card-body">
							<?php echo T_('Item').': '.$product_item[$value['production']].'<br>'.T_('VITA Shade').': '.T_($value['shade']).'<br>'.T_('Position').': '.implode(',',$value['position']).'<br>'.T_('Comments').': '.$remarks;?>
						</div>
					  </div>
					</div>
				
			<?php } ?>
			</div>
        </div>
		<div class="modal-footer">
			<button class="btn btn-secondary" type="button" data-dismiss="modal"><?php echo T_('Cancel');?></button>
			<button class="btn btn-primary" type="button"><?php echo T_('Save');?></button>
		</div>
      </div>
    </div>
</div>
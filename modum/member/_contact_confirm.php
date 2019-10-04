<?php
	session_start();
	
	include_once 'modum/class.template.php';
	
	if(!isset($_SESSION["contact"]) || empty($_SESSION["contact"]))
	{
		echo "<script>window.location.href='contact.aspx';</script>";
		Header( "Location: contact.aspx" ); 
	}
	
	if($_SESSION["contact"]["token"] != $_SESSION['token'])
	{
		echo "<script>window.location.href='contact.aspx';</script>";
		Header( "Location: contact.aspx" );
	}
	
	if(isset($_POST['submit_send']))
	{
		$_POST = $_SESSION["contact"];
			
		$default_admin_email_info = $dbf->getInfoColum("setting",24);
		$admin_email = $default_admin_email_info['value'];
		
		$contact_id = $_SESSION["contact"]["contact_id"];
		$username = sprintf(T_('Mrs/Mr %s'),$_SESSION["contact"]["contact_name"]);
		$contact_company = $_SESSION["contact"]["contact_company"];
		$contact_name = $_SESSION["contact"]["contact_name"];
		$contact_message = $_SESSION["contact"]["message"];
		$order_id = 'none';
		if(isset($_SESSION["contact"]["order_id"])) {
			$order_id = $_SESSION["contact"]["order_id"];
		}
		
		$contact_email = $_SESSION["contact"]["email"];
		
		
		switch($_SESSION['language']) {
			case 'ja_JP' :
				$customer_subject = '[VietQuocLab]お問い合わせありがとうございます';
				break;
			case 'vi_VN' :
				$customer_subject = 'Thong tin lien he';
				break;
			default :
				$customer_subject = 'Vietquoc contact form';
				break;
		}
		$admin_subject ="［VietQuocLab］お問い合わせがありました";
		
		$thankyou = T_('Thank you for your inquiry');
		$custmer_id_text = T_('Customer ID');
		$username_text = T_('Username');
		$company_text = T_('Company');
		$contact_name_text = T_('Contact name');
		$message_text = T_('Message');
		$order_id_text = T_('Order ID');
		$signature_text = $signature;
		$body_message = T_('We received the following content');
		$contact_signature_text = T_('We will confirm the contents and contact you. <br>※ Depending on the content of the inquiry, it may take some time to answer.');
		$mail_footer_text = $mail_footer;

		$customer_message = Template::get_contents("modum/mail_template/contact_form.tpl", array('logo' => HOST.'images/logo.jpg', 'contact_id' => $contact_id, 'subject' => $customer_subject, 'contact_company' => $contact_company,'contact_name' => $contact_name,'message' => $contact_message,'order_id' => $order_id, 'url' => HOST,'thankyou'=>$thankyou,'body_message'=>$body_message,'username_text'=>$username_text,'username'=>$username,'custmer_id_text'=>$custmer_id_text,'company_text'=>$company_text,'contact_name_text'=>$contact_name_text,'message_text'=>$message_text,'order_id_text'=>$order_id_text,'contact_signature_text'=>$contact_signature_text,'signature_text'=>$signature_text,'mail_footer_text'=>$mail_footer_text, 'url' => HOST));
		
		$admin_message = Template::get_contents("modum/mail_template/admin_contact_form.tpl", array('logo' => HOST.'images/logo.jpg', 'contact_id' => $contact_id, 'subject' => $admin_subject, 'contact_company' => $contact_company,'contact_name' => $contact_name,'message' => $contact_message,'order_id' => $order_id,'mail_footer_text'=>$mail_footer_text, 'url' => HOST));
		
		$from = $arraySMTPSERVER["user"];
		$fromName = $arraySMTPSERVER["from"];
		
		$customer_param = array('EmailFrom'=>$from,'FromName'=>$fromName,'ReplyTo'=>$admin_email,'ReplyName'=>'VietQuocLab','EmailTo'=>$contact_email,'ToName'=>$contact_name,'Subject'=>$customer_subject,'Content'=>$customer_message);
		
		$admin_param = array('EmailFrom'=>$from,'FromName'=>$fromName,'ReplyTo'=>$contact_email,'ReplyName'=>$contact_company,'EmailTo'=>$admin_email,'ToName'=>'VietquocLab','Subject'=>$admin_subject,'Content'=>$admin_message);
		/*printf("<pre>%s</pre>",print_r($admin_param,true));*/
		require("modum/class.phpmailer.php");
		$mail = new PHPMailer();
	  
		$admin_mail = $dbf->sendmail($admin_param,$mail );

		if ($admin_mail) 
		{
			$customer_mail = $dbf->sendmail($customer_param,$mail );
			$datecreated = time();
			$array_contact = array("user_id"=>$_SESSION["contact"]["contact_user_id"],"contact_id"=>$_SESSION["contact"]["contact_id"],"contact_company"=>$_SESSION["contact"]["contact_company"],"contact_name"=>$_SESSION["contact"]["contact_name"],"message"=>$_SESSION["contact"]["message"],"datecreated"=>$datecreated);
			$array_contact['title'] = $_SESSION["contact"]["contact_name"];
			if(isset($_SESSION["contact"]["order_id"])) {
				$array_contact['title'] = '#' . $order_id;
				$array_contact['status'] = 2;
			}
			$affect = $dbf->insertTable_2("contact_form", $array_contact);
			if($affect > 0)
			{
				$flag_status = 1;
				$success_change = true;
				unset($_POST);
				unset($_SESSION["contact"]);
				
				$token             = md5(uniqid(rand(), TRUE));
				$_SESSION['token'] = $token;
				
				echo "<script>window.location.href='contact-complete.aspx';</script>";
				Header( "Location: contact-complete.aspx" ); 
				exit;
			}	  
			
		}	  
	
 }			
?><main class="main">
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
			  <div class="card-header"><?php echo T_('Contact confirm');?></div>
			  <form id="fcontact_send" name="fcontact_send" action="" method="post">
			  
			  <div class="card-body">
					<div class="form-group row">
					    <label class="col-md-3 col-form-label"><?php echo T_('User ID');?></label>						  
						<div class="col-md-5">
							<label><?php echo $_SESSION["contact"]["contact_id"]; ?></label>
						</div>
					</div>

					
					<div class="form-group row">
					    <label class="col-md-3 col-form-label"><?php echo T_('Company name');?></label>						  
						<div class="col-md-5">
							<label><?php echo $_SESSION["contact"]["contact_company"]; ?></label>
						</div>
					</div>
					
					<div class="form-group row">
					    <label class="col-md-3 col-form-label"><?php echo T_('Contact person');?></label>						  
						<div class="col-md-5">
							<label><?php echo $_SESSION["contact"]["contact_name"]; ?></label>
						</div>
					</div>
					
					<div class="form-group row">
					    <label class="col-md-3 col-form-label"><?php echo T_('Message');?></label>						  
						<div class="col-md-5">								
							<label><?php echo $_SESSION["contact"]["message"]; ?></label>
						</div>
					</div>

					
					</div> <!-- card-body -->	
				   	<div class="card-footer">
					
						<a href="contact.aspx" class="btn btn-link btn-lg active d-print-none" role="button" aria-pressed="true"><i class="fa fa-angle-double-left" aria-hidden="true"></i> <?php echo T_('Go Back');?></a>
						<button class="btn btn-warning btn-warn" type="submit" name="submit_send">
						 <?php echo T_('Send');?></button>
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

<div class="post login" style="text-align: center; position: absolute; top: 20%;">
<style type="text/css">
/*<![CDATA[*/
#captchaimage img {
   border:1px solid red;
   border-radius:5px;
}
/*]]>*/
</style>
<?php

require ("modum/recaptcha-master/src/autoload.php");
// Register API keys at https://www.google.com/recaptcha/admin

$siteKey = '6LcGwyUTAAAAAL1RitEkGpxpq4eGo9rRz4VT50g9';
$secret = '6LcGwyUTAAAAAO29u4bTUhjdFQSaHJm6TdyFbR00';
// reCAPTCHA supported 40+ languages listed here: https://developers.google.com/recaptcha/docs/language
$lang = 'en';


if (isset ($_POST["subForget"])) {
  $account_id = $_POST["account_id"];
  $email = $_POST["email"];
  $email = $dbf->escapeStr($email);
  $recaptcha = new \ReCaptcha\ReCaptcha($secret);
  $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
  if ($resp->isSuccess()) {

    $rstcheck = $dbf->getDynamic("member", "email='" . $email . "' and ma_id='".$account_id."'", "");
    if ($dbf->totalRows($rstcheck) > 0) {
      $rowcheck = $dbf->nextData($rstcheck);
      $firstname = $rowcheck["hovaten"];
      $random = rand(1, 100);
      $password = $username . date("His_") . $random;
      $affect = $dbf->updateTable("member", array("password" => md5($password),"password2" => md5($password),"password3" => md5($password)), "ma_id='" . $account_id . "'");
      if ($affect > 0) {
        /* Get email address
        *****************************/
        /* Get template
        *****************************/
        $subject = "FORGOT PASSWORD";
        $body  = 'Dear <b>' . $rowcheck["hovaten"] . '</b>,<br/><br/>';
        $body .= '<b>Login Details:</b><br/>';
        $body .= 'URL: https://' . HOST . 'system<br/>';
        $body .= 'Acount ID:' . $rowcheck["ma_id"] . '<br/>';
        $body .= 'Email:' . $email . '<br/>';
        $body .= 'Password1:' . $password . '<br/>';
        $body .= 'Password2:' . $password . '<br/>';        
        require ("modum/class.phpmailer.php");
        $mail = new PHPMailer();
        $SMTP_Host = $arraySMTPSERVER["host"];
        $SMTP_Port = 25;
        $SMTP_UserName = $arraySMTPSERVER["user"];
        $SMTP_Password = $arraySMTPSERVER["password"];
        $from = $SMTP_UserName;
        $fromName = $yourname;
        $to = $email;
        $mail->IsSMTP();
        $mail->Host = $SMTP_Host;
        $mail->SMTPAuth = true;
        $mail->Username = $SMTP_UserName;
        $mail->Password = $SMTP_Password;
        $mail->From = $from;
        $mail->FromName = $fromName;
        $mail->AddAddress($to);
        $mail->AddReplyTo($from, $fromName);
        $mail->WordWrap = 50;
        $mail->IsHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = "This is the text-only body";
        if ($mail->Send()) {
          $html->redirectURL("/forgot-password/success");
        }
        else {
          $html->redirectURL("/forgot-password/error");
        }
      }
    }
    else {
      $msg .= "Email does not exist in the system";
    }
  }else
  {
    $msg = "<p><strong>Note:</strong> Error code <tt>missing-input-response</tt> may mean the user just didn't complete the reCAPTCHA.</p>";
  }
}
//require 'captcharand.php';
//$_SESSION['captcha_id'] = strtoupper($strcaptcha);
echo $html->normalForm("frmForget", array("class" => "jNice", "action" => "", "method" => "post"));
if (in_array($URL[1], array("success", "error"))) {
  if ($URL[1] == "success")
    $msg = "Password has been changed. Please check your mail for information";
  else
    $msg = "Forgot your password failure. Please again";
}
?>
    <div class='title_header'>FORGET PASSWORD</div>
    <div class="pro_c">
    <div  style="text-align:left;padding-left:15px"><span class="saodo" style="font-size: 14px;">
        <?=$msg?>
</span></div>
    <div id="clear"></div>
    <div class="productall1">
    <div class="clear" style="padding-top:5px;"></div>
    <div class="right_row1">ACOUNT ID</div>
	<div class="right_row2" style="width: 100%">
	<input class="form-control" type="text" value="<?=$ma_id?>" onFocus="this.select()" name="account_id" id="account_id" required/>
	</div>
    <div class="clear"></div>
	<div class="right_row1">E-MAIL</div>
	<div class="right_row2" style="width: 100%">
	<input class="form-control" type="email" value="<?=$email?>" onFocus="this.select()" name="email" id="email" required  />
	</div>
    <div class="clear"></div>
    </div>
	<div class="clear" style="padding-top:7px;"></div>
    <div class="productall1">
	<div class="clear" style="padding-top:5px;"></div>
	<div class="right_row1">SECURITY CODE</div>
	<div class="right_row2" style="width: 100%; overflow: hidden">
        <div class="g-recaptcha" data-sitekey="<?php echo $siteKey; ?>"></div>
        <script type="text/javascript" src="https://www.google.com/recaptcha/api.js?hl=<?php echo $lang; ?>"></script>
	</div>
	<div class="clear"></div>
	<div class="right_row2" style="width: 100%">
	<input type="submit" class="btn-primary_sb" style="width: 45%" value="Submit" name="subForget" id="subForget"  />
    <input type="button" class="btn-primary_sb" style="width: 45%" onclick="window.location.href='/system'" value="Back Login" name="retype" id="retype" />
	</div>
    <div class="clear"></div>
    </div>
    <div id="clear"></div>
</div>
<div class='box_bottom_main'></div>
<?php
echo $html->closeForm();
?>
</div>
<div class="clear"></div>

<?php

if(isset($_SESSION["member_id"]) && $_SESSION["member_id"]!="")
{
  $html->redirectURL("account.aspx");
}

if (isset ($_POST["submitlogin"])) {

  $username = $_POST["username"];
  $password = $_POST["password"];
  $username = $dbf->escapeStr($username);
  $password = $dbf->escapeStr($password);
  $password = md5($password);
  $query = $dbf->getDynamic("member", "(tendangnhap='" . $username . "' or ma_id='" . $username . "') and password='" . $password . "' and is_del=0 ", "");
  $total = $dbf->totalRows($query);
  if ($total > 0) {
    if ($total >= 2) {
      $msg = "Username ID or duplicate. Please contact Admin";
    }
    else {
      $row = $dbf->nextData($query);
      $_SESSION["member_id"] = stripslashes($row["id"]);
      $_SESSION["member_email"] = $email;
      $_SESSION["member_hovaten"] = stripslashes($row["hovaten"]);
      $_SESSION["Free"] = 0;
      $_SESSION["currentmember"]  = 1;
      $html->redirectURL("account.aspx");
    }
  }
  else {
    $msg = "Acount ID or password wrong. Please again !";
  }
}
?>

<div class="post login" style="text-align: center; position: absolute; top: 20%;">
<div class='title_header' style="text-align: left">
    <table border="0" width="100%">
    <tr>
    <td width="120">
        <img src="style/images/icon-login.png" alt="" align="absmiddle" width="90">
    </td>
    <td vertical-align="middle">
    LOGIN TO YOUR ACCOUNT<br>
    <span style="font-size: 12px; line-height: 100%; text-transform: capitalize">Manage your investments and review financial activities online.</span>
    </td>
    </tr>
    </table>
</div>
<div class="pro_c">
      <form name="frmLogin" id="frmLogin" action="/system" method="post">
      <div style="text-align:left">
            <div class="lblError" style="font-size: 14px;"><?=$msg ?></div>
            <div style="font-size:12px;text-align:left;width:260px;padding:5px"><span align="left" style="padding-left:0px;" id="lblError" class="saodo"></span></div>
            <div id="clear"></div>
            <div id="labelLogin">ACOUNT ID</div>
            <div id="fieldLogin">
            <input type="text" placeholder="ACOUNT ID"  onfocus="this.select();" class="form-control" id="username" name="username" value="" required>
            <span class="lblError" id="lblemail"></span></div>

            <div class="clear" style="padding-top:2px"></div>
            <div id="labelLogin">PASSWORD</div>
            <div id="fieldLogin">
            <input id="password" placeholder="PASSWORD"  class="form-control" type="password" onfocus="this.select();" maxlength="30" name="password" value="" required ><span class="lblError" id="lblpassword"></span>
            </div>

            <div id="clear" style="padding-top:2px"></div>
            <div id="labelLogin"></div>
            <div id="fieldLogin">
                 <input type="submit" class="btn-primary_sb login__submit" name="submitlogin" id="submit" value="SIGNIN" />
            </div>
            <div id="clear"></div>


            <div style="border-bottom:2px dotted #1a8aca;height:15px"></div>
            <table border="0" width="100%">
            <tr>
            <td width="40%">
                <div style="text-align:left;padding-top:8px">
                    Forgot Password?  <a class="itempathhome" href="forgot-password.aspx" style="color:#fff;"><u>Click Here</u></a>
                </div>
            </td>
            <td vertical-align="middle">
                    <div style="text-align:left;padding-top:8px">
                    Don't have an account yet? <a class="itempathhome" href="/register" style="color:#fff;"><u>Create an account</u></a>
                    </div>
            </td>
            </tr>
            </table>



      </div>
      </form>
      <div id="clear"></div>
</div>
<div class='box_bottom_main'></div>
<!--note !-->
<a id="notes_website" href="/notes.php" style="font-size: 0px;">&nbsp;</a>
<link rel="stylesheet"  type="text/css" href="/js/fancybox/jquery.fancybox-1.3.1.css"/>
<script type="text/javascript" src="/js/fancybox/jquery.fancybox-1.3.1.pack.js"></script>
<script language="javascript">
jQuery().ready(function() {

         $("#notes_website").fancybox({
            maxWidth    : 800,
            minHeight   : 100,
            fitToView   : false,
            autoSize    : true,
            autoScale   : true,
            closeClick  : true,
            openEffect  : 'fade',
            closeEffect : 'fade',
            scrolling   : false,
            padding     : 0,
            type		: 'iframe'
        });
});

</script>




<?php
    if(trim(stripcslashes(strip_tags($info["on_off"])))=="on")
    {
        echo '<script>';
        echo 'setTimeout(function(){';
        echo  "$('#notes_website').trigger( 'click' );";
        echo '}, 2000);';
        echo "</script>";
    }
 ?>

</div>
<style>
#captchaimage img {
   border: 1px solid #ccc;
}

</style>

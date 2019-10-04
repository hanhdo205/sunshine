<?php
date_default_timezone_set('Asia/Bangkok');
if( $_SESSION["Free"]==1)
{
  $html->redirectURL("/system");
}else
{



?>

<div id="page-wrapper">
     <div class="hearder"><?php include("inc/header.php");?></div>
     <div class="left">
          <?php include("inc/sidebar.php");?>
     </div>
     <div class="right">
            <div id="main-container">
                  <div id="page-content" style="min-height: 318px;">
                        <div class="block">
                            <div class="block-title">
                                <h2>Don't Created Account</h2>
                            </div>

                            <p>Don't created acount. Because system block after 20:00 </p>

                        </div>
                    </div>
            </div>
     </div>
</div>
<?php
}
?>


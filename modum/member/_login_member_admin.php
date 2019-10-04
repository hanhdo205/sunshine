<?php
 $_SESSION["Free"]==0;
?>

<div id="page-wrapper">
     <div class="hearder"><?php //include("inc/header.php");?></div>
     <div class="left">
          <?php //include("inc/sidebar.php");?>
     </div>
     <div class="right">
            <div id="main-container">
                  <div id="page-content" style="min-height: 318px;"> 
                        <div class="block">
                            <div class="block-title">
                                <h2>Account</h2>
                            </div>
                            <?php                               
                               $id_root = base64_decode($_GET["id"]);
                               $admin_login_by  = base64_decode($_GET["admin-login-by"]);							   
                              
                               if($id_root==1 && isset($_GET["admin-login-by"]) && (int) $admin_login_by!=0)
                               {
                                  
								  $member_login               = $dbf->getInfoColum("member",$admin_login_by);
								  $_SESSION["roles_id"] 	  = stripslashes($member_login["roles_id"]);
								  $_SESSION["member_id"]      = stripslashes($member_login["id"]);
								  $_SESSION["member_email"]   = $member_login["email"];
								  $_SESSION["member_hovaten"] = stripslashes($member_login["hovaten"]);
								  $_SESSION["Free"] = 0;
								  $_SESSION["currentmember"]  = 1;
								  $html->redirectURL("ranking.aspx");
								   
								   //$admin_login_by = $_GET["admin-login-by"];
								   /*
                                    if($dbf->checkEditMember($id_root,$admin_login_by))
                                    {

                                             

                                    }else
                                    {
                                       echo '<div class="alert alert-danger alert-dismissable">
                                           <h4><strong>Notice</strong></h4>
                                           <p>You can not login this member !!!</p>
                                        </div>';
                                    }
									*/
                               }else
                               {
                                   echo '<div class="alert alert-danger alert-dismissable">
                                       <h4><strong>Notice</strong></h4>
                                       <p>You can not login this member !!!</p>
                                    </div>';
                               }
                               
                            ?>
                        </div>
                    </div>
            </div>
     </div>
</div>
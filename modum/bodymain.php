<?php
switch ($page) {
  case "home" :
	include ("home.php");
    break;

  case "member-list" :
	if($rowgetInfo["roles_id"]<6)
		include ("member/_member_list.php");
	else
		include ("member/_no_authorize.php");
    break;
  
  case "member-create":
	if($rowgetInfo["roles_id"]<6)
		include ("member/_member_create.php");
	else
		include ("member/_no_authorize.php");
	break;
	
  case "member-edit":
	if($rowgetInfo["roles_id"]<6)
		include ("member/_member_edit.php");
	else
		include ("member/_no_authorize.php");
	break;
	
  case "revenue-expenditure-management" :
	if($rowgetInfo["roles_id"]<6)
		include ("member/_revenue_expenditure_management.php");
	else
		include ("member/_no_authorize.php");
    break;

  case "responsible-person" :
	if($rowgetInfo["roles_id"]<6)
		include ("member/_responsible_person_list.php");
	else
		include ("member/_no_authorize.php");
    break;

  case "goal-setting" :
	if($rowgetInfo["roles_id"]<6)
		include ("member/_goal_setting.php");
	else
		include ("member/_no_authorize.php");
    break;

  case "setting-system" :
	if($rowgetInfo["roles_id"]<6)
		include ("member/_global_setting_system.php");
	else
		include ("member/_no_authorize.php");
    break;
	
  case "default" :
	include ("member/_default.php");
    break;
  
  case "account_update_info":
    if($rowgetInfo["roles_id"]==15)
		include ("member/_updateAccount_customer.php");
	else
		include ("member/_updateAccount.php");
    break;
	
  case "account_change_password":
    include ("member/_account_change_password.php");
    break;

  case "member-login-admin":
    include ("member/_login_member_admin.php");
    break;
	
  case "confirm_by_password":
    include ("member/_confirm_by_password.php");
    break;	
  
  default :    
    $html->redirectURL("home");
    exit();
    break;
}
?>


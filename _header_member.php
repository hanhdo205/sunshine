<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
if( $_SESSION["Free"]==1)
{
  $html->redirectURL("/home");
}

$key = 0;
$logo_exists = false;

$getlogo_info = $dbf->getInfoColum("setting",24);
$set_logo = $getlogo_info['value'];
if(file_exists($set_logo)) {
	   $logo_exists = true;
}
?>
<script type="text/javascript">
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

<script type="text/javascript">
		var ajax_url = "<?php echo url() . '/modum/member/do_ajax.php';?>";
		var datatables_language = "<?php echo $datatable[$locale];?>";
		var baseUrl = "<?php echo url() . '/';?>";
		var js_host = "<?php echo HOST;?>";
</script>

<header class="app-header navbar">
      <button class="navbar-toggler sidebar-toggler d-lg-none mr-auto" type="button" data-toggle="sidebar-show">
        <span class="navbar-toggler-icon"></span>
      </button>
      
	  <?php if($logo_exists) { ?>
      <a class="navbar-brand" href="#">
        <img class="navbar-brand-full" src="<?php echo $set_logo;?>" height="40" alt="logo">
		<img class="navbar-brand-minimized" src="images/square_logo.png" width="30" height="30" alt="logo">
      </a>
      <?php } ?>
		<button class="navbar-toggler sidebar-toggler d-md-down-none" type="button" data-toggle="sidebar-lg-show">
			<span class="navbar-toggler-icon"></span>
		</button>
      <!--<ul class="nav navbar-nav d-md-down-none">
        <li class="nav-item px-3">
          <a class="nav-link" href="#">Dashboard</a>
        </li>
        <li class="nav-item px-3">
          <a class="nav-link" href="#">Users</a>
        </li>
        <li class="nav-item px-3">
          <a class="nav-link" href="#">Settings</a>
        </li>
      </ul>-->
      <ul class="nav navbar-nav ml-auto">
		<li class="nav-item dropdown">
          <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
            <?php echo $lang_text[$locale];?> <i class="flag-icon flag-icon-<?php echo $lang[$locale];?>" id="<?php echo $lang[$locale];?>" title="<?php echo $lang_text[$locale];?>"></i>
          </a>
		  <div class="dropdown-menu dropdown-menu-right">
            <div class="dropdown-header text-center">
              <strong><?php echo T_('Select language');?></strong>
            </div>
			<?php foreach($lang as $key=>$value) {
				if($locale != $key) { ?>
					<a class="dropdown-item" href="<?php echo strtok($_SERVER["REQUEST_URI"],'/?');?>/?lang=<?php echo $key;?>">
					  <i class="flag-icon flag-icon-<?php echo $value;?>" id="<?php echo $value;?>" title="<?php echo $lang_text[$key];?>"></i> <?php echo $lang_text[$key];?>
					</a>
				<?php }
			}
			?>
          </div>
        </li>
        <li class="nav-item dropdown">
		<?php 
		$totalNotifi = 0;
		$current_member_id=$rowgetInfo["id"];
		
		$getreminder_info = $dbf->getInfoColum("setting",26);
		$set_reminder = $getreminder_info['value'];
		
		$longtime = $dbf->getCount("member", "id", "dateupdated >= NOW() - INTERVAL $set_reminder MONTH", "dateupdated DESC");
		$str=array();
		
		$total = $dbf->nextData($longtime);
		if($total['value_count'] > 0) {
			$item = $total['value_count'] > 1 ? sprintf(T_('%d items need update'),$total['value_count']) : sprintf(T_('%d item need update'),$total['value_count']);
			$str[]=array('icon'=>'<i class="icon-refresh text-warning"></i>','content'=>$item);
			$totalNotifi++;
		}
		?>
          <a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
            <i class="icon-bell"></i>
            <?php if($totalNotifi>0) { ?><span class="badge badge-pill badge-danger"><?php echo $totalNotifi;?></span><?php } ?>
          </a>
		  <?php if($totalNotifi>0) { ?>
				<div class="dropdown-menu dropdown-menu-right dropdown-menu-lg">
					<div class="dropdown-header text-center">
					<strong><?php echo ($totalNotifi>1) ? sprintf( T_("You have %d notifications"),(int) $totalNotifi) : sprintf( T_("You have %d notification"),(int) $totalNotifi);?></strong>
					</div>
					<?php
						foreach($str as $noti) {
							echo '<a class="dropdown-item" href="member-list.aspx">' . $noti['icon'] . T_($noti['content']) . '</a>';
						}
					?>

				</div>
			<?php } else { ?>
				<div class="dropdown-menu dropdown-menu-right dropdown-menu-lg">
					<div class="dropdown-header text-center">
					<strong><?php echo T_('You have no notification');?></strong>
					</div>
				</div>
			<?php } ?>
        </li>
			
		<li class="nav-item custom-nav-item " style="border-left: 1px solid #ccc; padding-left: 17px;">
			<a class="nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
			<div class="div-avatar circle">
				<?php 
					$picture = $rowgetInfo["picture"] ? $rowgetInfo["picture"] : HOST . '/style/images/packages/user.png';
					$display_name = $rowgetInfo["company"] ? $rowgetInfo["company"] : $rowgetInfo["hovaten"];
					echo '<img class="img-avatar" onload="fixAspect(this);" src="'. $picture .'" alt="'.$rowgetInfo["hovaten"].'" />';
				?>
				
			</div>
         	<!--<div class="authur-text">
				<?php 
					echo $display_name;
				?>
         	</div>-->
         	</a>
         	 <div class="dropdown-menu dropdown-menu-right">
	            <div class="dropdown-header text-center">
	              <strong><?php echo T_('Account');?></strong>
	            </div>
	             <a class="dropdown-item" href="account_update_info.aspx">
              	<i class="fa fa-user"></i> <?php echo T_('Profile');?></a>
				<a class="dropdown-item" href="account_change_password.aspx">
             	 <i class="fa fa-key"></i> <?php echo T_('Change password');?></a>
				 <a class="dropdown-item" href="<?php echo md5("signout".date("dmH"))?>" onclick="return confirm('<?php echo T_('Do you really want to logout?');?>');">
              <i class="fa fa-lock"></i> <?php echo T_('Logout');?></a>
          </div>
        </li>
      </ul>
	  <?php if($rowgetInfo["roles_id"]<=7) { ?>
      <button class="navbar-toggler aside-menu-toggler d-md-down-none" type="button" data-toggle="aside-menu-lg-show">
        <span class="navbar-toggler-icon"></span>
      </button>
      <button class="navbar-toggler aside-menu-toggler d-lg-none" type="button" data-toggle="aside-menu-show">
        <span class="navbar-toggler-icon"></span>
      </button>
	  <?php } ?>
</header>
<?php
session_start();
if(isset($_GET['mess_id'])) {
	$_SESSION["mess_id"] = $_GET['mess_id'];
}
if(isset($_SESSION["mess_id"])) {
	$mess_id = $_SESSION["mess_id"];
	$result = $dbf->getDynamic("contact_form", "id=$mess_id", "");
	$messages = $dbf->getDynamic("contact_form", "", "id DESC");
	if ($dbf->totalRows($messages) > 0) {
		$read = array();
		$unread = array();
		while ($mes_row = $dbf->nextData($messages)) {
			if($mes_row['is_read'] == 'no') $unread[] = $mes_row['id'];
			if($mes_row['is_read'] == 'yes') $read[] = $mes_row['id'];
		}
	}
}

?><main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item">
		<a href="messages.aspx"><?php echo T_('Inbox');?></a>
	  </li>
	  <li class="breadcrumb-item active"><?php echo T_('Message');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">

		<div class="email-app mb-4">
			<nav>
			<ul class="nav">
				<li class="nav-item">
					<a class="nav-link" href="messages.aspx">
						<i class="icons cui-inbox"></i> <?php echo T_('Inbox');?>
						<span class="badge badge-primary"><?php echo count($read) + count($unread);?></span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="messages.aspx/?mark=unread">
						<i class="icon-envelope"></i> <?php echo T_('Unread');?>
						<span class="badge badge-danger"><?php echo count($unread);?></span>
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="messages.aspx/?mark=read">
						<i class="icon-envelope-open"></i> <?php echo T_('Read');?>
						<span class="badge badge-success"><?php echo count($read);?></span>
						</a>
				</li>
			</ul>
			</nav>
			<main class="message">
			<div class="toolbar">
				<div class="btn-group"></div>
				<div class="btn-group float-right">
					<div id="pagination-messages"></div>
				</div>
			</div>
			<div class="details">
			<?php if ($dbf->totalRows($result) > 0) {
					while ($row = $dbf->nextData($result)) {
						$info_account = $dbf->getInfoColum("member",$row['user_id']);
						$User_Email = $info_account["email"];
						$is_open = "-open";
						if($row['is_read']=="no") {
							$array_read = array("is_read"=>'yes');
							$dbf->updateTable("contact_form", $array_read, "id='" . $_SESSION['mess_id'] . "'");
							$is_open = "";
						}
						
						?>
						<div class="title"><?php echo $row['title'];?></div>
						<div class="header">
						<i class="icon-envelope<?php echo $is_open;?> icons font-2xl mr-2"></i>
						<div class="from">
						<span><?php echo $row['contact_company'];?></span> <?php echo $User_Email;?></div>
						<div class="date"><?php echo $utl->time_ago($row['datecreated']);?></div>
						</div>
						<div class="content">
						<p><?php echo $row['message'];?></p>
						</div>
						
						
						
					<?php
						}
					}
					?>
				</div>
			</main>
			</div>
		  <!-- /.row-->
	  </div>
  </main>
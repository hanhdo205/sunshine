<?php
$result = $dbf->getDynamic("contact_form", "", "id DESC");
if ($dbf->totalRows($result) > 0) {
	  $list = array();
	  $read = array();
	  $unread = array();
	  while ($row = $dbf->nextData($result)) {
		  $icon = 'fa fa-star-o';
		  if($row['status'] == 2) $icon = 'fa fa-shopping-cart';
		  switch($row['is_read']) {
			  case 'yes':
				$mark = "read";
				$is_open = "-open";
				break;
			default:
				$mark = "unread";
				$is_open = "";
				break;
		  }
		  if($row['is_read'] == 'no') {
			  $unread[] = '<li class="message '.$mark.'"><a href="message-detail.aspx/?mess_id='.$row['id'].'"><div class="actions"><span class="action"><i class="icon-envelope'.$is_open.'"></i></span><span class="action"><i class="'.$icon.'"></i></span></div><div class="header"><span class="from">'.$row['contact_company'].'</span><span class="date"><span class="fa fa-paper-clip"></span> '.$utl->time_ago($row['datecreated']).'</span></div><div class="title">'.$row['title'].'</div><div class="description">'.$utl->shorten_text(strip_tags($row['message']),100,'...',true).'</div></a></li>';
		  }
		  
		  if($row['is_read'] == 'yes') {
			  $read[] = '<li class="message '.$mark.'"><a href="message-detail.aspx/?mess_id='.$row['id'].'"><div class="actions"><span class="action"><i class="icon-envelope'.$is_open.'"></i></span><span class="action"><i class="'.$icon.'"></i></span></div><div class="header"><span class="from">'.$row['contact_company'].'</span><span class="date"><span class="fa fa-paper-clip"></span> '.$utl->time_ago($row['datecreated']).'</span></div><div class="title">'.$row['title'].'</div><div class="description">'.$utl->shorten_text(strip_tags($row['message']),100,'...',true).'</div></a></li>';
		  }
		  
		  $data[] = '<li class="message '.$mark.'"><a href="message-detail.aspx/?mess_id='.$row['id'].'"><div class="actions"><span class="action"><i class="icon-envelope'.$is_open.'"></i></span><span class="action"><i class="'.$icon.'"></i></span></div><div class="header"><span class="from">'.$row['contact_company'].'</span><span class="date"><span class="fa fa-paper-clip"></span> '.$utl->time_ago($row['datecreated']).'</span></div><div class="title">'.$row['title'].'</div><div class="description">'.$utl->shorten_text(strip_tags($row['message']),100,'...',true).'</div></a></li>';
	  }
}
if(isset($_GET['mark']) && $_GET['mark'] != "") {
	$data = $$_GET['mark'];
}
//printf("<pre>%s</pre>",print_r($data,true));
?>
<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item active"><?php echo T_('Inbox');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">

		<div class="email-app mb-4">
			<nav>
			<ul class="nav">
				<li class="nav-item">
					<a class="nav-link" href="messages.aspx">
						<i class="icons cui-inbox"></i> <?php echo T_('Inbox');?>
						<span class="badge badge-primary"><?php echo $dbf->totalRows($result);?></span>
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
			<main class="inbox">
			<div class="toolbar">
				<div class="btn-group"></div>
				<div class="btn-group float-right">
					<div id="pagination-messages"></div>
				</div>
			</div>
			<ul class="messages data-container">
			</ul>
			</main>
			</div>
		  <!-- /.row-->
	  </div>
	</div>
  </main>
  <!--<script src="js/coreui/advanced-forms.js"></script>-->
  <script src="js/custom/pagination.js"></script>
  <script type="text/javascript">
	$(function() {
 
	  (function(name) {
		var container = $('#pagination-' + name);
		var current = 1;
		var size = 10;
		var source = [<?php echo "'" . implode ( "','", $data ) . "'";?>];

		var options = {
			dataSource: source,
			totalNumber: <?php echo count($data);?>,
			pageSize: size,
			pageNumber: current,
			className: 'lst-paging',
			showNavigator: true,
			//formatNavigator: '<span style="color: #f00"><%= currentPage %></span> st/rd/th, <%= totalPage %> pages, <%= totalNumber %> entries',
			showPageNumbers: false,
			callback: function (response, pagination) {
				var dataHtml = '';
				$.each(response, function (index, item) {
					dataHtml +=  item ;
					$('#pagination-messages').find('.paginationjs-next a').addClass('fa fa-chevron-right');
					$('#pagination-messages').find('.paginationjs-next a').text('');
					$('#pagination-messages').find('.paginationjs-prev a').addClass('fa fa-chevron-left');
					$('#pagination-messages').find('.paginationjs-prev a').text('');
					$('#pagination-messages').find('.paginationjs-pages a').text('');
				});
				$('.data-container').html(dataHtml);
			},
		};

		
		container.pagination(options);
		
	  })('messages');
})
  </script>
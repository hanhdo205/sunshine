<?php
session_start();
if(isset($_GET['id'])) {
	$_SESSION["news_id"] = $_GET['id'];
}
if(isset($_SESSION["news_id"])) {
	$news_id = $_SESSION["news_id"];
	$result = $dbf->getDynamic("informations", "id=$news_id AND status=1", "id DESC");
}
$CurrentPostTitle = '';
if ($dbf->totalRows($result) > 0) {
	$trans = $lang[$_SESSION['language']];
	  while ($row = $dbf->nextData($result)) {
			$infor_author = $dbf->getInfoColum("member",$row["member_id"]);
			$author_name = $infor_author["hovaten"];
			  $content = array(
				'datecreated'=>$utl->time_ago($row["datecreated"]),
				'title'=>unserialize($row["title"]),
				'author'=>$author_name,
				'content'=>unserialize($row["content"]),
				'datecreated2'=>date($short_date_format,$row["datecreated"])
			  );
		  
	  }
}
?>

<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item active"><?php echo T_('News');?></li>
	  <li class="breadcrumb-item active"><?php echo (!empty($content)) ? $content['title'][$trans] : '';?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">
		<?php //if($rowgetInfo["roles_id"]==15) { ?>
		<div class="row">
		  <div class="col-md-12 mb-5">
			<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-md-2">
						
					</div>
					<div class="col-md-8">
					<?php if(!empty($content)) { 
					$CurrentPostTitle = $row["title"];?>
						<h3><?php echo $content['title'][$trans];?></h3>
						<p class="card-text"><?php echo $content['datecreated2'];?></p>
						
						<?php echo $content['content'][$trans];?>
						
						<div class="col-md-12 custome-magin-top">
							<h5><?php echo T_('Related News');?></h5>
							<?php 
								$related = $dbf->getRelatedNews("informations","status=1 AND id<>$news_id","","0,3");
								$informations = $dbf->totalRows($related);
								
								if($informations>0)
								{
									$str="";
									$i=1;
									while( $row = $dbf->nextData($related))
									{
										$get_the_date = $utl->time_ago($row["datecreated"]);
										$title = unserialize($row["title"]);
										$content = unserialize($row["content"]);
										preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $content, $image);
										$featured_image = $image['src'];
									?>
										<?php if($title[$trans]!='') { ?>
												<div class="customer-detail-border custom-border-bottom">
													<a href="news-detail.aspx?id=<?php echo $row["id"];?>">
													<div class="media">
														<?php echo $featured_image ? '<img class="mr-3" src="'.$featured_image.'" alt="'.$title.'" width="100px">' : '';?>
															<div class="media-body ml-4">
																<h5 class="mt-0"><?php echo $title[$trans];?></h5>
																<?php echo $utl->shorten_text($content[$trans],200,'...',true);?>
															</div>
													</div>
													<p class="card-text text-right"><small class="text-muted"><?php echo $get_the_date;?></small></p>
													</a>
												</div>
										<?php } ?>
											

									<?php								
										
									}
								}
							?>
							
					 	</div>
					<?php } ?>
					</div>
					<div class=" col-md-2">
					</div>
				</div>
			</div>
			
		  </div>
		  <!-- /.col-->
		  </div>
		  <!-- /.row-->
		  
		<?php //} ?>
		
	  </div>
	</div>
  </main>

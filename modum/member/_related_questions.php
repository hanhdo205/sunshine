<?php
    $trans = $lang[$_SESSION['language']];
	/*
    $result = $dbf->getDynamic("frequently_asked_questions", "status=1", "id DESC");
	$order_tab = array();
	$tool_tab = array();
	if ($dbf->totalRows($result) > 0) {
		$trans = $lang[$_SESSION['language']];
		  while ($row = $dbf->nextData($result)) {
			  if($row["category"]=="order") {
				  $order_tab[] = array(
					'title' => unserialize($row["title"]),
					'content' => unserialize($row["content"])
				  );
			  } else {
				  $tool_tab[] = array(
					'title' => unserialize($row["title"]),
					'content' => unserialize($row["content"])
				  );
			  }
			   
		  }
	}
	*/
?>
<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item active"><?php echo T_('FAQ');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">
		<div class="row">
		  <div class="col-md-12 mb-5">
		  
			
						<ul class="nav nav-tabs" role="tablist">
						    <?php 
								    $result = $dbf->getDynamic("category_questions", "status=1", "id DESC");
									if ($dbf->totalRows($result) > 0) {
										  $no = 1;
										  $str_content = "";
										  while ($row = $dbf->nextData($result)) {
											   $cateogry_title = unserialize($row["title"]);
											   $status = $row["status"];
							
							?>
						
											<li class="nav-item">
												<a class="nav-link <?php echo ($no==1)?"active show":"";?>" data-toggle="tab" href="#cate_<?php echo $row["id"]?>" role="tab" aria-controls="cate_<?php echo $row["id"]?>" aria-selected="<?php echo (($no==1)?"true":"false")?>"><?php echo $cateogry_title[$trans];?></a>
											</li>
							
										  <?php
												/**************/
												$str_content.='<div class="tab-pane '.(($no==1)?"active show":"").'" id="cate_'.$row["id"].'" role="tabpanel">
												<div id="content_cate_'.$row["id"].'" role="tablist" class="col-sm-12">';
                              
												$result_faq = $dbf->getDynamic("frequently_asked_questions", "status=1 and category='".$row["id"]."'", "id DESC");
												if ($dbf->totalRows($result_faq) > 0) 
												{										 
													  while ($row_faq = $dbf->nextData($result_faq)) 
													  {
														   $faq_title 	= unserialize($row_faq["title"]);
														   $faq_content = unserialize($row_faq["content"]);
														   
														   $str_content.='<div class="mb-3">
															<div class="card-title" id="orderheading_'.$row_faq["id"].'" role="tab">
																<a data-toggle="collapse" href="#order_'.$row_faq["id"].'" aria-expanded="false" aria-controls="order_'.$row_faq["id"].'" class="collapsed">
																	<h5 class="mb-0 faq-tit">
																	   '.$faq_title[$trans].'<span class="_arrow"></span>
																	</h5>
																</a>
															</div>
															<div class="collapse" id="order_'.$row_faq["id"].'" role="tabpanel" aria-labelledby="orderheading_'.$row_faq["id"].'" data-parent="#cate_'.$row["id"].'" style="">
																<div class="card-body faq-answer">'.$faq_content[$trans].'
																</div>
															</div>
														</div>';
												
													}
												}	  

												$str_content.='</div></div>';												
                                                /**************/
												$no++; 
											} 
									}	
							?>
						</ul>
						<div class="tab-content">
						    <?php echo $str_content;?>
						</div>
					
				
		  </div> <!-- col-md-12 -->
		  <!-- /.col-->
		  </div>
		  <!-- /.row-->
	  </div>
	</div>
  </main>
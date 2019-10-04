<link rel="stylesheet" href="css/custom/jquery-ui.css">
<script src="js/custom/jquery-ui.js"></script>
<?php if(isset($_SESSION['language']) && $_SESSION['language'] != 'en_US') { ?>
<script src="js/custom/datepicker-<?php echo $datepicker_lang[$_SESSION['language']];?>.js"></script>
<?php } ?>

<!-- include summernote css/js -->
<link href="//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote-bs4.css" rel="stylesheet">
<script src="//cdnjs.cloudflare.com/ajax/libs/summernote/0.8.11/summernote-bs4.js"></script>
<script src="js/custom/summernote-<?php echo $summernote[$_SESSION['language']];?>.js"></script>
<script type="text/javascript">
	var summernote_lang = '<?php echo $summernote[$_SESSION['language']];?>';
</script>
<script src="js/custom/jquery-faq.js"></script>

<main class="main">
	<!-- Breadcrumb-->
	<ol class="breadcrumb">
	  <li class="breadcrumb-item"><a href="default.aspx"><?php echo T_('Home');?></a></li>
	  <li class="breadcrumb-item active"><?php echo T_('FAQ');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">
	  <form id="faq-form" class="form-horizontal form-bordered" method="post" action="" enctype="multipart/form-data">
		<div class="row">
		  <div class="col-md-9">
					<?php
						$edit_id = (int)$_GET["id"];
						$infor_post_edit = $dbf->getInfoColum("frequently_asked_questions",$edit_id);
						$title = unserialize($infor_post_edit['title']);
						$postcontent = unserialize($infor_post_edit['content']);
						$status = $infor_post_edit['status'];
						$datecreate = date('d-m-Y H:i:s',$infor_post_edit['datecreated']);
							if (isset($_POST["edit_post"])) {
							  foreach ($_POST as $key => $value) {
								//$$key = $dbf->filter($value);
								if(!is_array($value)){
								   $$key = $dbf->filter($value);	
								}else{
								   $$key = $value;
								   
								}
							  }
							  //$postcontent = stripslashes($_POST['postcontent']);
																				
									 $array_col = array("title" => serialize($title),"content" => serialize($postcontent),"status" => $status,"category" => $category,"datecreated" => strtotime($datecreate));
									 //var_dump($array_col);die();
									 $affect = $dbf->updateTable("frequently_asked_questions", $array_col, "id='" . $edit_id . "'");
									 if ($affect > 0)
									 {
										  echo '<div class="alert alert-dismissable alert-success">'.T_('Post updated!').'</div>';
										  
									 }
									  
							}
						
					  ?>
					<ul class="nav nav-tabs" role="tablist">
						  <?php foreach($datatable as $key=>$value) {?>
							<li class="nav-item">
							  <a class="nav-link <?php echo ($_SESSION['language'] == $key) ? 'active' : '';?>" data-toggle="tab" href="#<?php echo $datepicker_lang[$key];?>" role="tab" aria-controls="home">
								<i class="flag-icon flag-icon-<?php echo $editor_input[$key];?>" title="<?php echo T_($lang_text[$key]);?>"></i> <?php echo T_($lang_text[$key]);?>
							  </a>
							</li>
						  <?php } ?>
						
					</ul>
								
					<div class="tab-content">
						<?php foreach($datatable as $key=>$value) {?>
							<div class="tab-pane <?php echo ($_SESSION['language'] == $key) ? 'active' : '';?>" id="<?php echo $datepicker_lang[$key];?>" role="tabpanel">
								<div class="form-group row">
									<div class="col">
									   <input type="text" tabindex="-1" placeholder="<?php echo T_('Enter title here');?>" value="<?php echo $title[$editor_input[$key]] ? $title[$editor_input[$key]] : '';?>" class="form-control" name="title[<?php echo $editor_input[$key];?>]" id="title">
									</div>
								</div>
								<div class="row">
									<div class="col">
										<textarea class="summernote" name="postcontent[<?php echo $editor_input[$key];?>]"><?php echo $postcontent[$editor_input[$key]];?></textarea>
									</div>
								</div>
							</div>
							
						<?php } ?>
					</div> <!-- tab content -->
			  </div> <!-- col-md-9 -->
			  <!-- /.col-->
			  <div class="col-md-3">
				<div class="card">
				  <div class="card-header">
						<?php echo T_('Publish');?>
				  </div>
				  <div class="card-body">
				  
					<p><?php echo T_('Status FAQ:');?>
						<select name="status">
							<option value="1" <?php echo $utl->selected($status,1);?>><?php echo T_('Publish FAQ');?></option>
							<option value="0" <?php echo $utl->selected($status,0);?>><?php echo T_('Pending FAQ');?></option>
						</select>
					</p>
					<p><?php echo T_('Date post:');?> <input type="text" tabindex="-1" value="<?php echo (($datecreate!="") ? $datecreate : date("d-m-Y H:i:s",time()));?>" class="form-control" name="datecreate" id="datecreate"></p>
				  </div>
				  <div class="card-footer text-muted text-right">
					<button class="btn btn-effect-ripple btn-primary" name="edit_post" type="submit" style="overflow: hidden; position: relative;"><?php echo T_('Update');?></button>
				  </div>
				</div> <!-- card -->
				
				<div class="card">
				  <div class="card-header">
						<?php echo T_('Category');?>
						<div class="card-header-actions">
							<a class="card-header-action btn-minimize" href="#" data-toggle="collapse" data-target="#collapseCategory" aria-expanded="true">
							  <i class="icon-arrow-up"></i>
							</a>
						</div>
				  </div>
				  <div class="collapse show" id="collapseCategory">
					  <div class="card-body">
						<fieldset class="form-group">
							<select class="form-control select2-multiple" id="select2-2" name="category">
							  <?php 
								    $trans = $lang[$_SESSION['language']];
									$result = $dbf->getDynamic("category_questions", "1=1", "id DESC");
									if ($dbf->totalRows($result) > 0) {										 
										  while ($row = $dbf->nextData($result)) {
											   $title_category = unserialize($row["title"]);											  
							  ?> 
									<option value="<?php echo $row["id"];?>" <?php echo $utl->selected($row["id"],$category);?>><?php echo $title_category[$trans];?></option>
									<?php 
										}
									}
									?>	
									
							  
							</select>
							
							
						  </fieldset>
					  </div>
				  </div>
				</div> <!-- card -->
			  </div> <!-- col-md-3 -->
			  <!-- /.col-->
		  </div>
		  <form>
		  <!-- /.row-->
	  </div>
	</div>
  </main>
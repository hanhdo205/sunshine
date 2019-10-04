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
	  <li class="breadcrumb-item active"><?php echo T_('FAQ Category');?></li>
	</ol>
	<div class="container-fluid">
	  <div class="animated fadeIn">
	  <form id="faq-form" class="form-horizontal form-bordered" method="post" action="" enctype="multipart/form-data">
		<div class="row">
		  <div class="col-md-5">
				<?php
					if (isset($_POST["created_post"])) 
					{
					   foreach ($_POST as $key => $value) {
						//$$key = $dbf->filter($value);
						if(!is_array($value)){
						   $$key = $dbf->filter($value);	
						}else{
						   $$key = $value;
						   
						}
					  }
					  //$postcontent = stripslashes($_POST['postcontent']);
																		
							 $array_col = array("title" => serialize($title),"status" => $status,"position"=>0,"datecreated" => strtotime($datecreated));
							 //var_dump($array_col);die();
							 $affect = $dbf->insertTable_2("category_questions", $array_col);
							 if ($affect > 0)
							 {
								  echo '<div class="alert alert-dismissable alert-success">'.T_('Category inserted!').'</div>';
								  foreach ($_POST as $key => $value) {
									$$key = "";
								  }	
							 }
							  
					}
					
					$id_edit = 0;
					$is_edit = false;
					if(isset($_GET["id"]) && (int)$_GET["id"]>0)
					{
						$id_edit = (int)$_GET["id"];
							
						if (isset($_POST["edit_post"])) 
						{
						  foreach ($_POST as $key => $value) {
							//$$key = $dbf->filter($value);
							if(!is_array($value)){
							   $$key = $dbf->filter($value);	
							}else{
							   $$key = $value;
							   
							}
						  }
						 
																			
								 $array_col = array("title" => serialize($title),"status" => $status,"datecreated" => strtotime($datecreated));
								
								 $affect = $dbf->updateTable("category_questions", $array_col, "id='" . $id_edit . "'");
								 if ($affect > 0) {
									  echo '<div class="alert alert-dismissable alert-success">'.T_('Category updated!').'</div>';
									}
								  
						}
						
						
						$info_category_faq = $dbf->getInfoColum("category_questions",$id_edit);
						$is_edit = true;
						$title = unserialize($info_category_faq['title']);	
						
						$status = $info_category_faq['status'];
						$datecreated = date('d-m-Y H:i:s',$info_category_faq['datecreated']);
						
						
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
		  
				<div class="tab-content mb-4">
						<?php foreach($datatable as $key=>$value) {?>
							<div class="tab-pane <?php echo ($_SESSION['language'] == $key) ? 'active' : '';?>" id="<?php echo $datepicker_lang[$key];?>" role="tabpanel">
								<div class="form-group row">
									<div class="col">
									   <input type="text" tabindex="-1" placeholder="<?php echo T_('Enter title here');?>" value="<?php echo $title ? $title[$editor_input[$key]] : '';?>" class="form-control" name="title[<?php echo $editor_input[$key];?>]" id="title">
									</div>
								</div>										
							</div>
							
						<?php } ?>
				</div> <!-- tab content -->
				<div class="card">
				  <div class="card-body">
					<p><?php echo T_('Status FAQ:');?>
						<select name="status">
							<option value="1" <?php echo $utl->selected($status,1);?>><?php echo T_('Publish FAQ');?></option>
							<option value="0" <?php echo $utl->selected($status,0);?>><?php echo T_('Pending FAQ');?></option>
						</select>
					</p>
				  </div>
				  <div class="card-footer text-muted text-right">
				    <?php if(!$is_edit) { ?>
					<button class="btn btn-effect-ripple btn-primary" name="created_post" type="submit" style="overflow: hidden; position: relative;"><?php echo T_('Publish');?></button>
					<?php }?>
					
					<?php if($is_edit) { ?>
						<button class="btn btn-effect-ripple btn-primary" name="edit_post" type="submit" style="overflow: hidden; position: relative;"><?php echo T_('Update');?></button>
					<?php }?>
					
				  </div>
				</div> <!-- card -->
				
			  </div> <!-- col-md-5 -->
			  <!-- /.col-->
			  <div class="col-md-7">
					<?php include("_faq_category_list.php");?>
			  </div> <!-- col-md-7 -->
			  <!-- /.col-->
		  </div>
		  <form>
		  <!-- /.row-->
	  </div>
	</div>
	
	
  </main>
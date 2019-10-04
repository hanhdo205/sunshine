<?php

if( $_SESSION["Free"]==1)
{
  $html->redirectURL("/ranking");
}else
{

   if($rowgetInfo["roles_id"]==15)                                                       
       {
          $html->redirectURL("/ranking");
          exit();
       }
	   
function selected($value,$pattern) {
	if($value==$pattern) return 'selected';
	return '';
}
             
?>

<script src="/css/system/template/js/vendor/modernizr-2.8.3.min.js"></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
	
	function getHora() {
	   date = new Date();   
	   return " "+date.getHours()+':'+date.getMinutes()+':'+date.getSeconds();
	};

	$( function() {
		$( "#datecreate" ).datepicker(
	   {	
			changeMonth: true,
			changeYear: true,
			dateFormat: 'dd-mm-yy' + getHora(),
		});
	  });
	  
</script>

<section id="main">
	<!-- WRAP -->
	<div class="wrap">

     <!-- USERMENU -->
		
		<!-- /USERMENU -->
		<!-- CONTENT -->
	    <section id="content">
            <div id="main-container">
                <div id="page-content">
					<h3 class=""><?php echo _EDITPOST;?></h3>
					<?php
						$edit_id = (int)$_GET["id"];
						$infor_post_edit = $dbf->getInfoColum("case_studies",$edit_id);
						$title = $infor_post_edit['title'];
						$postcontent = $infor_post_edit['content'];
						$status = $infor_post_edit['status'];
						$datecreate = date('d-m-Y H:i:s',$infor_post_edit['datecreated']);
                            if (isset($_POST["edit_post"])) {
                              foreach ($_POST as $key => $value) {
                                $$key = $dbf->filter($value);
                              }
							  $postcontent = stripslashes($_POST['postcontent']);
                              													
									 $array_col = array("title" => $title,"content" => $postcontent,"status" => $status,"datecreated" => strtotime($datecreate));
									 //var_dump($array_col);die();
									 $affect = $dbf->updateTable("case_studies", $array_col, "id='" . $edit_id . "'");
									 if ($affect > 0)
									 {
										  echo '<div class="alert alert-dismissable alert-success">
												   <h4><strong>Notice</strong></h4>
												   <p>Post updated !!!</p>
												</div>';

										  foreach ($_POST as $key => $value) {
											$$key = "";
										  }	
									 }
                                      
                            }
						include $_SERVER["DOCUMENT_ROOT"] . '/_system_admins/content_spaw/spaw.inc.php';
						$sw = new SPAW_Wysiwyg('postcontent' /*name*/,$postcontent/*value*/,
                       $_SESSION['lang'] /*language*/, 'full' /*toolbar mode*/, '' /*theme*/,
                       '100%' /*width*/, '400px' /*height*/);
                          ?>
					<form id="update_information" class="form-horizontal form-bordered" method="post" action="" enctype="multipart/form-data">
						<div class="row">
							<div class="col-md-9">
									<div class="form-group">
										<div class="row">
										   <input type="text" tabindex="-1" placeholder="<?php echo _ENTERTITLEHERE;?>" value="<?php echo $title ? $title : '';?>" class="form-control" name="title" id="title">
										</div>
									</div>
									<div class="form-group">
										<div class="row">
											<!--<textarea id="summernote" class="editor" name="content"></textarea>-->
											<?php $sw->show();?>
										</div>
									</div>
								
							</div>
							<div class="col-md-3">
								<div class="card">
								  <div class="card-header">
										<?php echo _PUBLISH;?>
								  </div>
								  <div class="card-body">
									<div>Status:
										<select name="status">
											<option value="1" <?php echo selected($status,1);?>><?php echo _PUBLISH;?></option>
											<option value="0" <?php echo selected($status,0);?>><?php echo _PENDING;?></option>
										</select>
									</div>
									<div>Date post: <input type="text" tabindex="-1" placeholder="<?php echo _TITLE;?>" value="<?php echo (($datecreate!="") ? $datecreate : date("d-m-Y H:i:s",time()));?>" class="form-control" name="datecreate" id="datecreate"></div>
								  </div>
								  <div class="card-footer text-muted text-right">
									<button class="btn btn-effect-ripple btn-primary" name="edit_post" type="submit" style="overflow: hidden; position: relative;"><?php echo _UPDATE;?></button>
								  </div>
								</div>
								
							</div>
						</div>
					<form>
                </div>
            </div>

			<div class="clearfix"></div>
		</section>
	</div>
	<div class="clearfix"></div>
</section>
<div class="clearfix"></div>
<?php
}
?>
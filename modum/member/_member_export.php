<?php
date_default_timezone_set('Asia/Bangkok');
if( $_SESSION["Free"]==1)
{
  $html->redirectURL("/system");
}else
{



?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<div id="page-wrapper">
     <div class="hearder"><?php include("inc/header.php");?></div>
     <div class="left">
          <?php include("inc/sidebar.php");?>
     </div>
     <div class="right">
            <div id="main-container">
                  <div id="page-content" style="min-height: 318px;">
                        <div class="block">
                            <div class="block-title">
                                <h2><?php echo _EXPORT2CSV;?></h2>
                            </div>
                            <form method="post" action="/modum/member/export.php" style="max-width: 300px">
                            <div class="form-group" >
                                <label for="date"><?php echo _FROMDATE;?></label>
                                <input type="text"  placeholder="dd-mm-yyyy" id="date" name="date" class="form-control input-datepicker" value="<?php echo date("d-m-Y",time());?>" required>
                                 <script type="text/javascript">
                      		$(function() {
                      			$('#date').datepicker({
                      				changeMonth: true,
                      				changeYear: true,
                      				dateFormat: 'dd-mm-yy'
                      			});
                      		});
                      	  </script>
                            </div>
                            <div class="form-group">
                             <label for="date"><?php echo _TODATE;?></label>
                             <input type="text" placeholder="dd-mm-yyyy" id="dateto" name="dateto" class="form-control input-datepicker" value="<?php echo date("d-m-Y",time());?>" required>

                               <script type="text/javascript">
                      		$(function() {
                      			$('#dateto').datepicker({
                      				changeMonth: true,
                      				changeYear: true,
                      				dateFormat: 'dd-mm-yy'
                      			});
                      		});
                      	  </script>

                            </div>
                            <div class="form-group form-actions">
                            <button type="submit" name="export_users" class="btn btn-effect-ripple btn-primary" style="overflow: hidden; position: relative;"><?php echo _EXPORT;?></button> </div>
                            </form>
                        </div>
                  </div>
            </div>
     </div>
</div>
<?php
}
?>
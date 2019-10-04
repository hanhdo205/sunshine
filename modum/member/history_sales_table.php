<?php
if( $_SESSION["Free"]==1)
{
  $html->redirectURL("/system");
}else
{ 
	if(isset($_GET['id']))
		$member_id = $_GET['id'];
	else $member_id = $_SESSION["member_id"];
	$memberInfo         = $dbf->getInfoColum("member",$member_id);
	$lang = array(
		'en' => 'English',
		'vi' => 'Vietnamese',
		'ja' => 'Japanese',
	);
function lessthanzero($number) {
	if($number<0) return true;
	return false;
}
?>
<link rel="stylesheet"  type="text/css" href="/css/jquery.dataTables.min.css"/>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

<style>
 td.details-control {
    background: url('http://www.datatables.net/examples/resources/details_open.png') no-repeat center center;
    cursor: pointer;
}
tr.shown td.details-control {
    background: url('http://www.datatables.net/examples/resources/details_close.png') no-repeat center center;
}input {
    border: 1px solid #e2e2e2 !important;
padding: 2px !important;}
.redcolor {color:red !important;}
</style>

<section id="main">
	<!-- WRAP -->
	<div class="wrap">

	 <!-- CONTENT -->
	 <section id="content">

            <div id="main-container">
<div id="page-content" style="min-height: 973px;">
<div class="block">
<div class="block-title">
<h2><?php echo _HISTORY;?></h2>
</div>
<div class="table-responsive">
			 <div class="dataTables_wrapper form-inline no-footer" style="width:100%">
				<table id="transaction_history" class="table table-striped table-bordered table-vcenter table-hover dataTable no-footer" role="grid" aria-describedby="example-datatable_info" style="width:100%">
				   <thead>
					  <tr role="row">
						 <th class="text-center sorting"><span style="color:#2c3e50"><?php echo _DATE;?></span></th>
						 <th class="text-center sorting"><span style="color:#2c3e50"><?php echo _TOTALSHIPMENTQTY;?></span></th>
						 <th class="text-center sorting"><span style="color:#2c3e50"><?php echo $rowgetInfo["roles_id"]==15  ? _USERTOTALPAIDAMOUNT : _TOTALPAIDAMOUNT;?>(VND)</span></th>
						 <th class="text-center sorting"><span style="color:#2c3e50"><?php echo $rowgetInfo["roles_id"]==15 ? _USERTOTALREMAIN : _TOTALREMAIN;?>(VND)</span></th>
						 <th class="text-center sorting"><span style="color:#2c3e50"><?php echo _ACTUALQTY;?></span></th>
						 <th class="text-center sorting"><span style="color:#2c3e50"><?php echo _NOTE;?></span></th>
					  </tr>
				   </thead>
				   <tbody>
					  <?php														
						 $no = 1;
						 $total_sales_quantity = 0;
						 $total_paid_amount = 0;
						 $array_sale = $dbf->TotalTransactionHistory("history_sales",$member_id);
						 $array_paid = $dbf->TotalTransactionHistory("history_payment",$member_id);
						 $actual_paid = $dbf->TotalTransactionHistory("actual_sales",$member_id);
						
						 if(count($array_sale)>= count($array_paid))
						 {
							$no=0;
							foreach($array_sale as $sale)
							{	
								$sale_quantity = lessthanzero($sale["quantity"]) ? "redcolor" : "";
								echo'<tr role="row" data-comment="'. $sale["comment"] .'">
									 <td class="text-center">' . date("Y-m-d H:i:s",$sale['datecreated']) . '</td>
									 <td class="text-center '.$sale_quantity.'"> ' . number_format($sale["quantity"]).'</td>                                                 
									 <td class="text-center"></td>
									 <td class="text-center"></td>
									 <td class="text-center"></td>
									 <td class="text-center">'.$sale["comment"].'</td>
								  </tr>';
								$total_sales_quantity+=$sale["quantity"];
								  
							  if(isset($array_paid[$no]))
							  {	 
								$array_paid_price = lessthanzero($array_paid[$no]['price']) ? "redcolor" : "";
								
								$total_paid_amount+=$array_paid[$no]['price'];
								$array_paid_remain = lessthanzero(($total_sales_quantity * $memberInfo["price"]) - $total_paid_amount) ? "redcolor" : "";
								 echo'<tr role="row"  data-comment="'. $array_paid[$no]["comment"] .'">
									 <td class="text-center">' . date("Y-m-d H:i:s",$array_paid[$no]['datecreated']) . '</td>
									 <td class="text-center"></td>                                                 
									 <td class="text-center '.$array_paid_price.'">' . number_format($array_paid[$no]['price']).'</td>
									 <td class="text-center '.$array_paid_remain.'">' . number_format(($total_sales_quantity * $memberInfo["price"]) - $total_paid_amount) . '</td>
									 <td class="text-center"></td>
									 <td class="text-center">'.$array_paid[$no]["comment"].'</td>
								  </tr>'; 
								  
							  }
							  if(isset($actual_paid[$no]))
								  {
									$actual_sale_quantity = lessthanzero($actual_paid[$no]['quantity']) ? "redcolor" : "";
									 echo'<tr role="row" data-comment="'. $actual_paid[$no]["comment"] .'">
										 <td class="text-center">' .date("Y-m-d H:i:s",$actual_paid[$no]['datecreated']). '</td>
										 <td class="text-center"></td>
										 <td class="text-center"></td>
										 <td class="text-center"></td>
										 <td class="text-center '.$actual_sale_quantity.'"> ' . number_format($actual_paid[$no]['quantity']).'</td> 
										 <td class="text-center">'.$actual_paid[$no]["comment"].'</td>
									  </tr>'; 
									  $total_actual_sales_quantity+=$actual_paid[$no]['quantity'];
								  }
								$no++;
							} 
							
						 }
						 else
						 {
							$no=0;
							foreach($array_paid as $paid)
							{
								 if(isset($array_sale[$no]))
								  {
									$array_sale_quantity = lessthanzero($array_sale[$no]['quantity']) ? "redcolor" : "";
									 echo'<tr role="row" data-comment="'. $array_sale[$no]["comment"] .'">
										 <td class="text-center">' .date("Y-m-d H:i:s",$array_sale[$no]['datecreated']). '</td>
										 <td class="text-center '.$array_sale_quantity.'"> ' . number_format($array_sale[$no]['quantity']).'</td> 
										 <td class="text-center"></td>
										 <td class="text-center"></td>
										 <td class="text-center"></td>
										 <td class="text-center">'.$array_sale[$no]["comment"].'</td>
									  </tr>'; 
									  $total_sales_quantity+=$array_sale[$no]['quantity'];
								  }
								  
									$paid_price = lessthanzero($paid["price"]) ? "redcolor" : "";
									
									$total_paid_amount+=$paid["price"];
									$array_sale_color = lessthanzero(($total_sales_quantity * $memberInfo["price"]) - $total_paid_amount) ? "redcolor" : "";
								  echo'<tr role="row" data-comment="'. $paid["comment"] .'">
									 <td class="text-center">' . date("Y-m-d H:i:s",$paid['datecreated']) . '</td>
									 <td class="text-center"> </td>                                                 
									 <td class="text-center '.$paid_price.'">' . number_format($paid["price"]).'</td>
									 <td class="text-center '.$array_sale_color.'">' . number_format(($total_sales_quantity * $memberInfo["price"]) - $total_paid_amount) . '</td>
									 <td class="text-center"></td>
									 <td class="text-center">'.$paid["comment"].'</td>
								  </tr>'; 
								  if(isset($actual_paid[$no]))
								  {
									$actual_sale_quantity = lessthanzero($actual_paid[$no]['quantity']) ? "redcolor" : "";
									 echo'<tr role="row" data-comment="'. $actual_paid[$no]["comment"] .'">
										 <td class="text-center">' .date("Y-m-d H:i:s",$actual_paid[$no]['datecreated']). '</td>
										 
										 <td class="text-center"></td>
										 <td class="text-center"></td>
										 <td class="text-center"></td>
										 <td class="text-center '.$actual_sale_quantity.'"> ' . number_format($actual_paid[$no]['quantity']).'</td> 
										 <td class="text-center">'.$actual_paid[$no]["comment"].'</td>
									  </tr>'; 
									  $total_actual_sales_quantity+=$actual_paid[$no]['quantity'];
								  }
								$no++;
							}  
						 }
						
						?>
					 </tbody>
					 <tfoot>									
					 <tr role="row">
							 <td class="text-center"><?php echo _TOTAL;?></td>
							 <td class="text-center"><span style="color:#2c3e50"><b><?php echo number_format($total_sales_quantity,0);?></b></td> 																 										 
							 <td class="text-center"><span style="color:#2c3e50"><b><?php echo number_format($total_paid_amount,0);?></b></span></td>
							 <?php $style_color = lessthanzero(($total_sales_quantity * $memberInfo["price"]) - $total_paid_amount) ? "red" : "#2c3e50";?>
							 <td class="text-center"><span style="color:<?php echo $style_color;?>"><b><?php echo number_format(($total_sales_quantity * $memberInfo["price"]) - $total_paid_amount);?></b></span></td>
							  <td class="text-center"><span style="color:#2c3e50"><b><?php echo number_format($total_actual_sales_quantity,0);?></b></span></td>
							 <td>&nbsp;</td>
							
						  </tr>
					 </tfoot>
				</table>
			 </div>
		  </div>
   </div>
</div>

<script type="text/javascript">
//<![CDATA[
	function format ( dataSource ) {
		var html = '';
		for (var key in dataSource){
			html += '<?php echo _NOTE;?>: ' + dataSource[key];
		}        
		return html;  
	}
    jQuery(document).ready(function($) {
		
		var table = $('#transaction_history').DataTable( {
			"pagingType": "full_numbers",
			"bFilter": true,
			"responsive": true,
			"pageLength": 25,
			"language": {
				 //"url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/<?php echo $lang[$_SESSION['lang']];?>.json"
				"sProcessing":   "<?php echo _SPROCESSING;?>",
				"sLengthMenu":   "<?php echo _SLENGTHMENU;?>",
				"sZeroRecords":  "<?php echo _SZERORECORDS;?>",
				"sInfo":         "<?php echo _SINFO;?>",
				"sInfoEmpty":    "<?php echo _SINFOEMPTY;?>",
				"sInfoFiltered": "<?php echo _SINFOFILTERED;?>",
				"sInfoPostFix":  "",
				"sSearch":       "<?php echo _SSEARCH;?>",
				"sUrl":          "",
				"oPaginate": {
					"sFirst":    "<?php echo _SFIRST;?>",
					"sPrevious": "<?php echo _SPREVIOUS;?>",
					"sNext":     "<?php echo _SNEXT;?>",
					"sLast":     "<?php echo _SLAST;?>"
				}
			}
		});

	});

	
 //]]>
</script>
</div>
                    <div class="clearfix"></div>
			</section>
		</div>
<div class="clearfix"></div>
</section>
<div class="clearfix"></div>
<?php include("inc/footer.php");?>
<?php
}
?>
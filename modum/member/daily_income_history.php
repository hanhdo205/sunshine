<?php

if( $_SESSION["Free"]==1)
{
  $html->redirectURL("/system");
}else
{

?>

<link href="/css/system/template/css/main.css" rel="stylesheet">
<script src="/css/system/template/js/vendor/modernizr-2.8.3.min.js"></script>

<link rel="stylesheet"  type="text/css" href="/css/jquery.dataTables.min.css"/>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

<link rel="stylesheet" type="text/css" href="/js/fancybox/jquery.fancybox.min.css" media="screen" />
<script type="text/javascript" src="/js/fancybox/jquery.fancybox.min.js"></script>

<section id="main">
	<!-- WRAP -->
	<div class="wrap">
		<!-- CONTENT -->
	 <section id="content">
            <div id="main-container">
                <div id="page-content" style="min-height: 973px;">
   <div class="block">
      <div class="block-title">
         <h2>Earning</h2>
      </div>

      <div class="table-responsive">
                         <div class="dataTables_wrapper form-inline no-footer" style="width:100%">
                            <table id="daily_income_history" class="table table-striped table-bordered table-vcenter table-hover dataTable no-footer" role="grid" aria-describedby="example-datatable_info" style="width:100%">
                               <thead>
                                  <tr role="row">
                                     <th class="text-center sorting"><span style="color:#2c3e50">No.</span></th>
									 <th class="text-center sorting"><span style="color:#2c3e50">Date</span></th>
                                     <th class="text-center sorting"><span style="color:#2c3e50">% Principal & Interest </span></th> 
                                     <th class="text-center sorting"><span style="color:#2c3e50">Total</span></th>                                    
                                  </tr>
                               </thead>
                               <tbody>
                                  <?php
                                     $currentDate = date("d-m-Y",time());
                                     $now = date("d-m-Y",time());
                                     $now = strtotime($now);
                                     $your_date = date('d-m-Y',$rowgetInfo["datecreated"]);
                                     $your_date = strtotime($your_date);
                                     $datediff = $now - $your_date;
                                     $date_re = floor($datediff/(60*60*24));

                                     $no = 1;
                                     $total_daily_wallet = 0;
                                     $total_income_direct = 0;
                                     $total_income_indirect = 0;
                                     $total_income_fee = 0;
                                     $total_income_withdraw = 0;
                                     $date_start = 0;

                                     for($i=$date_re;$i>0;$i--)
                                     {
                                         $daily_wallet   = 0;
                                         $m_circle120    = 0;
                                         $m_direct       = 0;
                                         $m_indirect     = 0;
                                         $m_withdraw     = 0;
                                         $infowithdrawdaily = array();
                                         if($rowgetInfo["status"])
                                         {
                                            
                                            $date = new DateTime($currentDate);
                                            $date->sub(new DateInterval('P'.$date_start.'D'));
                                            $datenow = $date->format('d-m-Y');

                                             $m_circle120   = $dbf->TotalPriceRoiByDate($rowgetInfo["id"],strtotime($datenow));
                                             $m_direct   = $dbf->TotalPriceIncomeDirectByDate($rowgetInfo["id"],strtotime($datenow));
                                             $m_indirect = $dbf->TotalPriceIncomeInDirectByDate($rowgetInfo["id"],strtotime($datenow));
											 //$m_indirect=0;
                                             $daily_wallet = $m_circle120;
											 
											 $pecent = (round($m_circle120,2)*100)/$info_packages["price"];

                                             echo'<tr role="row">
                                                     <td class="text-center">' . $no . '</td>
                                                     ';
                                                     
                                                     echo '<td class="text-center">' . $datenow . '</td>
													 <td class="text-center"> ' . round($pecent,2).'%</td>
                                                     <td class="text-center"> $' . number_format(round($daily_wallet,2)).' </td>';

                                                     $infowithdrawdaily = $dbf->getInfoWithdrawdaily($rowgetInfo["id"],strtotime($datenow));
                                                     if(isset($infowithdrawdaily["price3"]) && (int)$infowithdrawdaily["price3"]!=0)
                                                     {
                                                         /*echo'<td class="text-center">-$' . round($infowithdrawdaily["price3"],2).' </td>';*/
                                                         $total_income_withdraw+= $infowithdrawdaily["price3"];
                                                     }
                                                  echo'</tr>';
                                            }

                                       $total_daily_wallet+=$m_circle120;
                                       $total_income_direct+=$m_direct;
                                       $total_income_indirect+=$m_indirect;
                                       $no++;
                                       $date_start++;
                                     }
									 
									 

                                     echo  "</tbody>";
                                     echo "<tfoot>";									
                                     echo'<tr role="row">
									         <td class="text-center">&nbsp;</td>
                                             <td class="text-center">&nbsp;</td> 
											 <td class="text-center">&nbsp;</td>  											 
                                             <td class="text-center"><span style="color:#2c3e50">$<b>' .  number_format(round(($total_daily_wallet),2)).'</b></span></td>                                             
                                          </tr>';
                                    echo "</tfoot>";
                                  ?>
                              
                            </table>
                         </div>
                      </div>
               </div>
            </div>
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



<script type="text/javascript">
//<![CDATA[
    jQuery(document).ready(function() {
		
		jQuery('#daily_income_history').DataTable( {
			"pagingType": "full_numbers"
		} );
		
        jQuery(".detail_direct").fancybox({
        maxWidth    : 800,
        minHeight   : 100,
        fitToView   : false,
        autoSize    : true,
        autoScale   : true,
        closeClick  : true,
        openEffect  : 'fade',
        closeEffect : 'fade',
        scrolling   : false,
        padding     : 0,
        type		: 'iframe'
        });

        jQuery(".detail_indirect").fancybox({
        'titleShow'		     : false,
        'width'				 : 800,
        'Height'             : 600,
        'autoScale'			 : false,
        'overlayOpacity'     : 0.8,
        'overlayColor'       : '#000',
        'transitionIn'	: 'none',
        'transitionOut'	: 'none',
        'type'				: 'iframe'
        });
    });
 //]]>
</script>


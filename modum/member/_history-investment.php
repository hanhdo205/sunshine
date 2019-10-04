<?php
if( $_SESSION["Free"]==1)
{
  $html->redirectURL("/system");
}else
{
    if (!isset($_SESSION['token'])) {
		$token = md5(uniqid(rand(), TRUE));
		$_SESSION['token'] = $token;
	}else
	{
		$token = $_SESSION['token'];
	}
?>

<link href="/css/system/template/css/main.css" rel="stylesheet">
<section id="main">
	<!-- WRAP -->
	<div class="wrap">
	 <section id="content">
            <div id="main-container">
            <div id="page-content" style="min-height: 318px;">
               <div class="block">
                  <div class="block-title">
                     <h2>Investment History</h2>
                  </div>
                   <div class="table-responsive">
                         <div class="dataTables_wrapper form-inline no-footer">
                            <table class="table table-striped table-bordered table-vcenter table-hover dataTable no-footer" role="grid" aria-describedby="example-datatable_info">
                               <thead>
                                  <tr role="row">
                                     <th class="text-center sorting"><span style="color:#2c3e50">No.</span></th>									 
                                     <th class="text-center sorting"><span style="color:#2c3e50">Amount</span></th>                                     
                                     <th class="text-center sorting"><span style="color:#2c3e50">Datetime</span></th>
                                  </tr>
                               </thead>
                               <tbody>
                                  <?php

                                           $result2 = $dbf->getDynamic("member_invest", "member_id =" . $_SESSION["member_id"] . "", "id desc");
                                           $total2 = $dbf->totalRows($result2);
                                           if ($total2 > 0) {
                                           $i=1;
										   $total_eth = 0;
										   $total_usd = 0;
                                           while ($row2 = $dbf->nextData($result2)) 
										   {
											    $total_eth+= $row2["quantity"];
										        $total_usd+= $row2["quantity_usd"];
                                           echo'<tr role="row">
                                                 <td class="text-center">' . $i . '</td>
                                                 <td class="text-center"> $' . number_format($row2["quantity_usd"]).'</td>                                                 
                                                 <td class="text-center">' . date("Y-m-d H:i:s",$row2['datecreated']) . '</td>
                                              </tr>';
                                            $i++;
                                            }
                                            }

                                  ?>

                               </tbody>
							   
							   <thead>
                                  <tr role="row">
                                     <th class="text-center sorting"><span style="color:#2c3e50">Total </span></th>									 
                                     <th class="text-center sorting"><span style="color:#2c3e50">$<?php echo number_format($total_usd);?></span></th>                                    
                                     <th class="text-center sorting"><span style="color:#2c3e50"></span></th>
                                  </tr>
                               </thead>
							   
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



<?php
}
?>
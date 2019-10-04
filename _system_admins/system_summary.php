<?php
date_default_timezone_set('Asia/Bangkok');
include ("index_table.php");
?>
<div class="right" style="text-align: left">
            <div id="main-container">
                  <div id="page-content" style="min-height: 318px;">
                      <div class="block">
                           <div class="block-title">
                              <h2>System summary</h2>
                           </div>

                           <div class="clear"></div>
                           <br /><span style="float: left; width:100px">From date</span><input type="text" onfocus="fo(this)" onblur="lo(this)" maxlength="12" value="<?php echo (($_POST["tungay"]!="")?$_POST["tungay"]:date("d-m-Y",time()))?>" id="tungay" name="tungay" >
                          	  <script type="text/javascript">
                          		$(function() {
                          			$('#tungay').datepicker({
                          				changeMonth: true,
                          				changeYear: true,
                          				dateFormat: 'dd-mm-yy'
                          			});
                          		});
                          	  </script>

                              To date<input type="text" onfocus="fo(this)" onblur="lo(this)" maxlength="12" value="<?php echo (($_POST["denngay"]!="")?$_POST["denngay"]:date("d-m-Y",time()))?>" id="denngay" name="denngay">
                          	  <script type="text/javascript">
                          		$(function() {
                          			$('#denngay').datepicker({
                          				changeMonth: true,
                          				changeYear: true,
                          				dateFormat: 'dd-mm-yy'
                          			});
                          		});
                          	  </script>
                            &nbsp;<input type="submit" name="cmdTK" id="cmdTK" value="Submit" />

                           <?php
                           if(isset($_POST["cmdTK"]))
                             {
                                 $from_date  = strtotime($_POST["tungay"]);
                                 $from_to  = strtotime($_POST["denngay"]);
                                 $currentnow =  strtotime(date("d-m-Y",time()));
                                 if (($from_date > $from_to) || ($from_to>$currentnow))  {
                                        echo '<meta http-equiv="content-type" content="text/html;charset=UTF-8"/>';
                                        echo "<h3 style='text-align:left'>Please select the number of days (from <b>". $_POST["date"]."</b> to day <b> ". $_POST["dateto"]."). Data mismatch</h3>";
                                        exit();
                                 }else
                                 {
                                ?>
                                    <br>
                                    <div class="col-md-9">
                                       <div class="widget-content themed-background-dark-social text-light-op"> <i class="fa fa-fw fa-chevron-right"></i> <strong>Member Active</strong> </div>
                                       <div class="block">
                                          <div class="table-responsive">
                                             <table class="table table-borderless table-vcenter " id="mainTable">
                                                <thead class="titleBottom">
                                                   <tr>
                                                      <th>Package</th>
                                                      <th class="text-right">Money</th>
                                                      <th class="text-right">Count</th>
                                                      <th class="text-right">Total</th>
                                                   </tr>
                                                </thead>
                                                <tbody>
                                                   <?php
                                                     $rst_packages = $dbf->getDynamic("packages", "1=1", "price asc");
                                                     $i = 1;
                                                          $total_price = 0;
                                                          $total_tv = 0;
                                                          $total_price_tv = 0;
                                                          while ($rows_packages = $dbf->nextdata($rst_packages)) {
                                                            $id    = $rows_packages['id'];
                                                            $title = $rows_packages['title'];
                                                            $price = $rows_packages['price'];

                                                           $rst_nhanvien = $dbf->getDynamic("member", "packages_id=" . $id . " and FROM_UNIXTIME(datecreated,'%Y-%m-%d') >='".date("Y-m-d",$from_date)."' and FROM_UNIXTIME(datecreated,'%Y-%m-%d') <='".date("Y-m-d",$from_to)."'  and status=1", "");
                                                            $toal_nhanvien = $dbf->totalRows($rst_nhanvien);
                                                            $total_tv+= $toal_nhanvien;
                                                            $total_price = $toal_nhanvien * $price;
                                                            $total_price_tv+= $toal_nhanvien * $price;

                                                   ?>
                                                   <tr class="cell1">
                                                      <td><strong><?php echo $title?></strong></td>
                                                      <td class="text-right"><?php echo $utl->format($price); ?><sup>$</sup></td>
                                                      <td class="text-right"><?php echo $toal_nhanvien?></td>
                                                      <td class="text-right"><?php echo $utl->format($total_price); ?><sup>$</sup></td>
                                                   </tr>
                                                   <?php
                                                        }

                                                   ?>

                                                   <tr class="cell1">
                                                      <td colspan="2" class="text-right"><strong>TOTAL MONEY FROM MEMBER</strong></td>
                                                      <td class="text-right"><?php echo $total_tv?></td>
                                                      <td class="text-right"><?php echo $utl->format($total_price_tv); ?><sup>$</sup></td>
                                                   </tr>
                                                   <!--
                                                   <tr>
                                                      <td class="text-right" colspan="4"><i><span class="text-info">* Exclude admin</span></i></td>
                                                   </tr>
                                                   !-->

                                                </tbody>
                                             </table>
                                          </div>
                                       </div>
                                    </div>
                                    <div style="clear: both"></div>
                                <?php
                                 }

                             }else
                             {
                           ?>

                           <div class="col-md-9">
                               <div class="widget-content themed-background-dark-social text-light-op"> <i class="fa fa-fw fa-chevron-right"></i> <strong>Member All</strong> </div>
                               <div class="block">
                                  <div class="table-responsive">
                                     <table id="mainTable" class="table table-borderless table-vcenter">
                                        <thead>
                                           <tr class="titleBottom">
                                              <th>Package</th>
                                              <th class="text-right">Money</th>
                                              <th class="text-right">Count</th>
                                              <th class="text-right">Total</th>
                                           </tr>
                                        </thead>
                                        <tbody>

                                           <?php

                                             $rst_packages = $dbf->getDynamic("packages", "1=1", "price asc");
                                             $i = 1;
                                                  $total_price = 0;
                                                  $total_tv = 0;
                                                  $total_price_tv = 0;
                                                  while ($rows_packages = $dbf->nextdata($rst_packages)) {
                                                    $id    = $rows_packages['id'];
                                                    $title = $rows_packages['title'];
                                                    $price = $rows_packages['price'];

                                                    $rst_nhanvien = $dbf->getDynamic("member", "packages_id=" . $id . "", "");
                                                    $toal_nhanvien = $dbf->totalRows($rst_nhanvien);
                                                    $total_tv+= $toal_nhanvien;
                                                    $total_price = $toal_nhanvien * $price;
                                                    $total_price_tv+= $toal_nhanvien * $price;

                                           ?>
                                           <tr class="cell1">
                                              <td><strong><?php echo $title?></strong></td>
                                              <td class="text-right"><?php echo $utl->format($price); ?><sup>$</sup></td>
                                              <td class="text-right"><?php echo $toal_nhanvien?></td>
                                              <td class="text-right"><?php echo $utl->format($total_price); ?><sup>$</sup></td>
                                           </tr>
                                           <?php
                                                }

                                           ?>

                                           <tr class="cell2">
                                              <td colspan="2" class="text-right"><strong>TOTAL MONEY FROM MEMBER</strong></td>
                                              <td class="text-right"><?php echo $total_tv?></td>
                                              <td class="text-right"><?php echo $utl->format($total_price_tv); ?><sup>$</sup></td>
                                           </tr>
                                           <!--
                                           <tr>
                                              <td class="text-right" colspan="4"><i><span class="text-info">* Exclude admin</span></i></td>
                                           </tr>
                                           !-->

                                        </tbody>
                                     </table>
                                  </div>
                               </div>
                            </div>
                            <div style="clear: both"></div>
                            <br>
                            <div class="col-md-9">
                               <div class="widget-content themed-background-dark-social text-light-op"> <i class="fa fa-fw fa-chevron-right"></i> <strong>Member Not Active</strong> </div>
                               <div class="block">
                                  <div class="table-responsive">
                                     <table class="table table-borderless table-vcenter" id="mainTable">
                                        <thead>
                                           <tr class="titleBottom">
                                              <th>Package</th>
                                              <th class="text-right">Money</th>
                                              <th class="text-right">Count</th>
                                              <th class="text-right">Total</th>
                                           </tr>
                                        </thead>
                                        <tbody>
                                           <?php
                                             $rst_packages = $dbf->getDynamic("packages", "1=1", "price asc");
                                             $i = 1;
                                                  $total_price = 0;
                                                  $total_tv = 0;
                                                  $total_price_tv = 0;
                                                  while ($rows_packages = $dbf->nextdata($rst_packages)) {
                                                    $id    = $rows_packages['id'];
                                                    $title = $rows_packages['title'];
                                                    $price = $rows_packages['price'];

                                                    $rst_nhanvien = $dbf->getDynamic("member", "packages_id=" . $id . " and status=0", "");
                                                    $toal_nhanvien = $dbf->totalRows($rst_nhanvien);
                                                    $total_tv+= $toal_nhanvien;
                                                    $total_price = $toal_nhanvien * $price;
                                                    $total_price_tv+= $toal_nhanvien * $price;

                                           ?>
                                           <tr class="cell2">
                                              <td><strong><?php echo $title?></strong></td>
                                              <td class="text-right"><?php echo $utl->format($price); ?><sup>$</sup></td>
                                              <td class="text-right"><?php echo $toal_nhanvien?></td>
                                              <td class="text-right"><?php echo $utl->format($total_price); ?><sup>$</sup></td>
                                           </tr>
                                           <?php
                                                }

                                           ?>

                                           <tr class="cell2">
                                              <td colspan="2" class="text-right"><strong>TOTAL MONEY FROM MEMBER</strong></td>
                                              <td class="text-right"><?php echo $total_tv?></td>
                                              <td class="text-right"><?php echo $utl->format($total_price_tv); ?><sup>$</sup></td>
                                           </tr>
                                           <!--
                                           <tr>
                                              <td class="text-right" colspan="4"><i><span class="text-info">* Exclude admin</span></i></td>
                                           </tr>
                                           !-->

                                        </tbody>
                                     </table>
                                  </div>
                               </div>
                            </div>
                            <div style="clear: both"></div>
                            <br>
                            <div class="col-md-9">
                               <div class="widget-content themed-background-dark-social text-light-op"> <i class="fa fa-fw fa-chevron-right"></i> <strong>Member Active</strong> </div>
                               <div class="block">
                                  <div class="table-responsive">
                                     <table class="table table-borderless table-vcenter " id="mainTable">
                                        <thead class="titleBottom">
                                           <tr>
                                              <th>Package</th>
                                              <th class="text-right">Money</th>
                                              <th class="text-right">Count</th>
                                              <th class="text-right">Total</th>
                                           </tr>
                                        </thead>
                                        <tbody>
                                           <?php
                                             $rst_packages = $dbf->getDynamic("packages", "1=1", "price asc");
                                             $i = 1;
                                                  $total_price = 0;
                                                  $total_tv = 0;
                                                  $total_price_tv = 0;
                                                  while ($rows_packages = $dbf->nextdata($rst_packages)) {
                                                    $id    = $rows_packages['id'];
                                                    $title = $rows_packages['title'];
                                                    $price = $rows_packages['price'];

                                                    $rst_nhanvien = $dbf->getDynamic("member", "packages_id=" . $id . " and status=1", "");
                                                    $toal_nhanvien = $dbf->totalRows($rst_nhanvien);
                                                    $total_tv+= $toal_nhanvien;
                                                    $total_price = $toal_nhanvien * $price;
                                                    $total_price_tv+= $toal_nhanvien * $price;

                                           ?>
                                           <tr class="cell1">
                                              <td><strong><?php echo $title?></strong></td>
                                              <td class="text-right"><?php echo $utl->format($price); ?><sup>$</sup></td>
                                              <td class="text-right"><?php echo $toal_nhanvien?></td>
                                              <td class="text-right"><?php echo $utl->format($total_price); ?><sup>$</sup></td>
                                           </tr>
                                           <?php
                                                }

                                           ?>

                                           <tr class="cell1">
                                              <td colspan="2" class="text-right"><strong>TOTAL MONEY FROM MEMBER</strong></td>
                                              <td class="text-right"><?php echo $total_tv?></td>
                                              <td class="text-right"><?php echo $utl->format($total_price_tv); ?><sup>$</sup></td>
                                           </tr>
                                           <!--
                                           <tr>
                                              <td class="text-right" colspan="4"><i><span class="text-info">* Exclude admin</span></i></td>
                                           </tr>
                                           !-->

                                        </tbody>
                                     </table>
                                  </div>
                               </div>
                            </div>
                            <div style="clear: both"></div>
                            <?php } ?>
                        </div>
                  </div>
            </div>

     </div>
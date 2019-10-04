<?php

if( $_SESSION["Free"]==1)
{
  $html->redirectURL("/system");
}else
{

	$lang = array(
		'en' => 'English',
		'vi' => 'Vietnamese',
		'ja' => 'Japanese',
	);
	$gender = array(
		'male' => _MALE,
		'female' => _FEMALE,
	);

?>


<script src="/css/system/template/js/vendor/modernizr-2.8.3.min.js"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>

<link rel="stylesheet" type="text/css" href="/js/fancybox/jquery.fancybox.min.css" media="screen" />
<script type="text/javascript" src="/js/fancybox/jquery.fancybox.min.js"></script>

<link rel="stylesheet"  type="text/css" href="/css/jquery.dataTables.min.css"/>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
function getHora() {
   date = new Date();   
   return " "+date.getHours()+':'+date.getMinutes()+':'+date.getSeconds();
};

$( function() {
   $( "#User_RegisteredDatetime" ).datepicker(
   {	
        changeMonth: true,
		changeYear: true,
		dateFormat: 'dd-mm-yy',
	});
 });
	
</script>

<script>

function fixAspect(img) {
  var $img = $(img),
    width = $img.width(),
    height = $img.height(),
    tallAndNarrow = width / height < 1;
  if (tallAndNarrow) {
    $img.addClass('tallAndNarrow');
  }
  $img.addClass('loaded');
}
</script>
<style>
table.dataTable tbody th,
table.dataTable tbody td {
	padding: 5px;
}
.fancybox-close-small {
  background-image: url('https://cdn.jsdelivr.net/fancybox/1.3.4/fancybox.png');
  background-position: -40px 0px;
  width: 30px;
  height: 30px;
  top: -15px !important;
  right: -15px !important;
  text-indent: -9999px;
}
.jconfirm-buttons{
/*display: none*/
}
.btn-neo.transaction-update {
	margin-right: 20px;
}
.active_filter:hover {
	color: blue;
}
.active_filter {cursor:pointer;}
span button.btn {margin-right:3px;}

</style>
<section id="main">
	<!-- WRAP -->
	<div class="wrap">    
		<!-- CONTENT -->
	    <section id="content">
            <div id="main-container">
                <div id="page-content" style="min-height: 973px;">
                   <div class="block full">
                      <div class="block-title">
                         <h2><?php echo _MEMBERLIST;?></h2>
                      </div>
                      <?php
                        if(isset($_GET["id_del"]) && isset($_GET["delete"]) && (int)$_GET["id_del"]!=0 )
                        {
                            $id_del = (int)$_GET["id_del"];
                            if($dbf->checkEditMember($rowgetInfo["id"],$id_del))
                            {
                                 $array_col = array("status" =>0,"is_del"=>1);
                                 $affect = $dbf->updateTable("member", $array_col, "id='" . $id_del . "'");
                                 if ($affect > 0)
                                 {
                                    echo '<div class="alert alert-success alert-dismissable">
                                       <h4><strong>Notice</strong></h4>
                                       <p>Delete member successfull !!!</p>
                                    </div>';

                                 } else
                                 {
                                      echo '<div class="alert alert-danger alert-dismissable">
                                       <h4><strong>Notice</strong></h4>
                                       <p>Delete member wrong !!!</p>
                                    </div>';
                                 }

                            }else
                            {
                                 echo '<div class="alert alert-danger alert-dismissable">
                                       <h4><strong>Notice</strong></h4>
                                       <p>You can not delete this member !!!</p>
                                    </div>';
                            }
                        }
						if(isset($_GET["active"]) && $_GET["active"]!='') {
							$eye = array(
								'active' => 'fa-eye-slash',
								'all' => 'fa-eye'
							);
							$eye_class = $eye[$_GET["active"]];
						} else $eye_class = 'fa-eye';
                      ?>

                      <div class="table-responsive tbl-mem-list">
                         <div class="dataTables_wrapper no-footer">
						    <table class="table table-striped table-vcenter table-hover" role="grid" aria-describedby="example-datatable_info" style="min-width:1000px; margin-bottom: 15px;">
                               <form method="get" action="<?=HOST?>member-list.aspx" id="user_search" autocomplete="off">
                                  <thead>
								  <tr role="row">
                                     <th class="text-center">
									 <input class="form-control" type="hidden" name="active" id="active_user" value="<?php echo $_GET["active"];?>">
									 <input class="form-control" type="text" name="User_ID" placeholder="<?php echo _ID;?>" value="<?php echo $_GET["User_ID"];?>"> </th>
                                     <th class="text-center"><input class="form-control" type="text" name="User_Login" placeholder="<?php echo _USERNAME;?>" value="<?php echo $_GET["User_Login"];?>"> </th>
									 <th class="text-center"> <input class="form-control" type="text" name="User_Name" placeholder="<?php echo _FULLNAME;?>" value="<?php echo $_GET["User_Name"];?>"> </th>
                                     <th class="text-center"> <input class="form-control" type="text" name="User_Email" placeholder="<?php echo _EMAIL;?>" value="<?php echo $_GET["User_Email"];?>"> </th>
                                     <th class="text-center"> <input class="form-control" placeholder="<?php echo _DATEENROLL;?>" type="text" name="User_RegisteredDatetime" id="User_RegisteredDatetime" value="<?php echo $_GET["User_RegisteredDatetime"];?>"> </th>
                                     <th class="text-center"> <button type="submit" class="btn btn-effect-ripple btn-secondary" style="overflow: hidden; position: relative;"><?php echo _SEARCH;?></button> </th>
									 <th class="text-center"> <a href="<?php echo HOST?>member_create.aspx" class="btn btn-effect-ripple btn-neo" style="overflow: hidden; position: relative;"><i class="fa fa-user-plus" aria-hidden="true"></i> <?php echo _ADDUSER;?></a></th>
									<th class="text-center"> <a href="/modum/member/_data_logs.php" id="data-log" class="btn btn-effect-ripple btn-secondary" style="overflow: hidden; position: relative;"><i class="fa fa-file-text-o" aria-hidden="true"></i> <?php echo _LOGS;?></a></th>
									<th class="text-center"> <a href="<?php echo HOST?>member-export.aspx" id="csv" class="btn btn-effect-ripple btn-secondary" style="overflow: hidden; position: relative;"><i class="fa fa-download" aria-hidden="true"></i> <?php echo _CSV;?></a></th>
                                  </tr>
								</thead>								  
                               </form> 
							 </table>
                           
                            <table id="member_list_all" class="table table-striped table-bordered table-vcenter table-hover dataTable no-footer" role="grid" aria-describedby="example-datatable_info" style="min-width:1000px;">
                              

                               <thead>
                                  <tr>
                                     <th class="text-center sorting_disabled no-sort" rowspan=2><span><?php echo _ID;?></span></th>
                                     <th class="text-center sorting" rowspan=2><span><?php echo _USERNAME;?></span></th>
                                     <th class="text-center sorting" rowspan=2><span><?php echo _FULLNAME;?></span></th>
                                     
                                     <th class="text-center sorting" rowspan=2><span><?php echo _SEX;?></span></th>
                                     <th class="text-center sorting" rowspan=2><span><?php echo _AGE;?></span></th>
                                     <th class="text-center sorting" rowspan=2><span><?php echo _DATEENROLL;?></span></th>
                                     <th class="text-center sorting" rowspan=2><span><?php echo _DATEWITHDRAWAL;?></span></th>
                  									 <th class="text-center sorting" rowspan=2><span><?php echo _ACTUALQTY;?></span></th>
                  									 <th class="text-center sorting_disabled no-sort" colspan=2><span><?php echo _SHIPMENTSTS;?></span></th>
                  									 <th class="text-center sorting_disabled no-sort" colspan=2><span><?php echo _RECEIVEDSTS;?></span></th>
                  									 <th class="text-center sorting_disabled no-sort" colspan=2><span><?php echo _TXTREMAIN;?></span></th>
                  									 <th class="text-center sorting_disabled no-sort" rowspan=2><span><?php echo _STATUS;?><br><button type="button" class="btn btn-sm"><i class="active_filter fa fa-users text-primary" aria-hidden="true"></i></button><button type="button" class="btn btn-sm"><i class="active_filter fa fa-user text-success" aria-hidden="true"></i></button><button type="button" class="btn btn-sm"><i class="active_filter fa fa-user-times text-danger" aria-hidden="true"></i></button></span></th>
                  									 <th class="text-center sorting_disabled no-sort" rowspan=2><span><?php echo _TRANSACTIONUPD;?></span></th>
                  									 
                  									 <th class="text-center sorting_disabled no-sort" style="min-width:60px;" rowspan=2><span>&nbsp;</span></th>
                  									  
                  									</tr>
                  									<tr>									  
                  										 <th class="text-center sorting"><span><?php echo _TOTALQTY;?></span></th>
                  										 <th class="text-center sorting"><span><?php echo _TOTALAMT;?></span></th>
                  										 <th class="text-center sorting"><span><?php echo _TOTALAMT;?></span></th>
                  										 <th class="text-center sorting"><span><?php echo _TOTALQTY;?></span></th>
                  										 <th class="text-center sorting"><span><?php echo _UNTOTALQTY;?></span></th>
                  										 <th class="text-center sorting"><span><?php echo _UNTOTALAMT;?></span></th>
                  									</tr>
                  								</thead>
                  								<tbody>
                                  <?php
                                    $array_search= array("User_ID"=>(int)$_GET["User_ID"],"User_Name"=>$dbf->filter($_GET["User_Name"]),"User_Email"=>$dbf->filter($_GET["User_Email"]),"User_RegisteredDatetime"=>$dbf->filter($_GET["User_RegisteredDatetime"]),"User_UserGroup"=>(int)$dbf->filter($_GET["User_UserGroup"]),"User_Parent"=>(int)$dbf->filter($_GET["User_Parent"]));
                                    //$dbf->getMemberList($rowgetInfo["id"],$rowgetInfo);

                                    $where = "is_del<>1";
                                    if(isset($_GET["active"])) {
										if ($_GET["active"]=='active')
										{
										   $where.= " and (date_end IS NULL OR FROM_UNIXTIME(date_end,'%Y-%m-%d')>CURDATE())";
										} elseif ($_GET["active"]=='withdrawal')
										{
										   $where.= " and (date_end<>'' AND FROM_UNIXTIME(date_end,'%Y-%m-%d')<CURDATE())";
										}
									}
									
									if(isset($_GET["User_ID"]) && $_GET["User_ID"]!='')
                                    {
                                       $where.= " and ma_id='".$dbf->filter($_GET["User_ID"])."'";
                                    }

                                    if(isset($_GET["User_Name"]) && $_GET["User_Name"]!='')
                                    {
                                       $where.= " and hovaten like'%".$dbf->filter($_GET["User_Name"])."%'";
                                    }
									
									if(isset($_GET["User_Login"]) && $_GET["User_Login"]!='')
                                    {
                                       $where.= " and tendangnhap like'%".$dbf->filter($_GET["User_Login"])."%'";
                                    }

                                    if(isset($_GET["User_Email"]) && $_GET["User_Email"]!='')
                                    {
                                       $where.= " and email='".$dbf->filter($_GET["User_Email"])."'";
                                    }

                                    if(isset($_GET["User_RegisteredDatetime"]) && $_GET["User_RegisteredDatetime"]!='')
                                    {
                                       $where.= " and FROM_UNIXTIME(datecreated,'%Y-%m-%d') like'%".date("Y-m-d",strtotime($dbf->filter($_GET["User_RegisteredDatetime"])))."%'";
                                    }                                    

                                    if($where!="is_del<>1")
                                    {
                                          date_default_timezone_set('Asia/Bangkok');
                                          $arrayMemberCurrent= array();
                                          $arrayMemberCurrent = $dbf->getMemberListArray($rowgetInfo["id"],$rowgetInfo,$arrayMemberCurrent);
                                          /*
                                          echo date("d-m-Y",1459814400);
                                          echo "<br>";
                                          echo date("d-m-Y",1459789200);
                                          echo "<br>";
                                          echo $where;
                                          */
                                          $rstmb = $dbf->getDynamic("member", "status=1 and ".$where."", "");
                                          if($dbf->totalRows($rstmb)>0)
                                          {
                                               while ($row = $dbf->nextData($rstmb)) {
                                                     $ponser = $dbf->getInfoColum("member", $row['parentid']);
													 $rowgetActual = $dbf->getActualColum("actual_sales",$row['id'],"quantity");
                                                     if((int)$arrayMemberCurrent[$row["id"]]["id"]!=0)
                                                     {
														 $picture = $row['picture'] ? $row['picture'] : HOST . '/style/images/packages/user.png';
														 if($row['date_end']) {
															$date = new DateTime(date('Y-m-d H:i:s',$row['date_end']));
															$now = new DateTime();
														}
														$tax = $row['tax'] ? $row['tax'] : 0;
														$is_active = ($row['date_end'] && $date < $now) ? '<i class="fa fa-user-times text-danger" aria-hidden="true"></i>' : '<i class="fa fa-user text-success" aria-hidden="true"></i>';
                                                         echo '<tr role="row" class="row_member '.$row['ma_id'].'">
                                                             <td class="text-center">' . $row['ma_id'] . '</td>
                                                             <td class="text-center"> <a class ="detail_member"  href="/modum/member/_detail_member.php?id='.$row['id'].'">' . $row['tendangnhap'] . '</a></td>
                                                             <td class="text-center"> <a class ="detail_member"  href="_detail_member.php?id='.$row['id'].'">' . $row['hovaten'] . '</a></td>
                                                             <td class="text-center"> ' . $gender[$row['gioitinh']] . ' </td>
															 <td class="text-center">' . $row['age'] . '</td>
                                                             <td class="text-center">' . (($row["status"]==1)?date("d-m-Y",$row['datecreated']):"Not Active") . '</td><td class="text-center">' . (($row['date_end'] && $date < $now)?date("d-m-Y",$row['date_end']):"") . '</td>
															 <td class="text-center">'.number_format($rowgetActual).'</td>
                                                             <td class="text-center"  style="width:100px">'.number_format($dbf->getMemberDelivery("history_sales",$row['id'],"quantity"),2).'</td>
															 <td class="text-center fix-width"  style="width:100px">'.number_format($dbf->getMemberDelivery("history_sales",$row['id'],"price"),0).'</td>
															 <td class="text-center">'.number_format($dbf->getMemberDelivery("history_payment",$row['id'],"price"),0).'</td>
															 <td class="text-center">'.number_format($dbf->getMemberDelivery("history_payment",$row['id'],"quantity"),2).'</td>
															 
															 <td class="text-center"  style="width:100px">'.number_format(($dbf->getMemberDelivery("history_sales",$row['id'],"quantity")-$dbf->getMemberDelivery("history_payment",$row['id'],"quantity")),2).'</td>
															 <td class="text-center fix-width"  style="width:100px">'.number_format(($dbf->getMemberDelivery("history_sales",$row['id'],"price")-$dbf->getMemberDelivery("history_payment",$row['id'],"price")),0).'</td>
															 <td class="text-center"> ' . $is_active . '</td>
                                                             <td class="text-center"><button type="button" class="btn btn-neo mb-1 add_sales" data-id="'.$row['id'].'" data-price="'.$row['price'].'" data-tax="'.$tax.'" >
											<i class="fa fa-plus" aria-hidden="true"></i> '._ADD.'
										</button></td>
                                                             <td class="text-center">';
                                                                if($rowgetInfo["roles_id"]!=15)
                                                                {
                                                                echo '<a href="' .HOST. 'edit_member_create.aspx?id=' . $row['id'] . '" class="btn btn-effect-ripple btn-xs btn-secondary" data-toggle="tooltip" title="" style="overflow: hidden; position: relative;" data-original-title="'._EDIT.'"><i class="fa fa-pencil" aria-hidden="true"></i></a>
																<a href="' .HOST. 'member-list.aspx?id_del=' . $row['id'] . '&delete=true" class="btn btn-effect-ripple btn-xs btn-danger" data-toggle="tooltip" title="" onclick="return confirm(\'Are you really want to delete?\');" style="overflow: hidden; position: relative;" data-original-title="'._DELETE.'"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
                                                                }
                                                             echo'</td>
                                                          </tr>';
                                                    }
                                                }
                                          }else
                                          {
                                              echo '<tbody> <tr role="row"> <td colspan="8" class="text-center">No data</td> </tr> </tbody>';
                                          }

                                    }else
                                    {

                                    $arrayMemberCurrent= array();
                                    $arrayMemberCurrent = $dbf->getMemberListArray($rowgetInfo["id"],$rowgetInfo,$arrayMemberCurrent);
                                    $arrayMemberCurrent = $dbf->array_sort_by_column($arrayMemberCurrent,"datecreated");


                                        foreach($arrayMemberCurrent as $row)
                                        {
                                            if($row["is_del"]!=1)
                                            {
											$picture = $row['picture'] ? $row['picture'] : HOST . '/style/images/packages/user.png';
											if($row['date_end']) {
												$date = new DateTime(date('Y-m-d H:i:s',$row['date_end']));
												$now = new DateTime();
											}
											$rowgetActual = $dbf->getActualColum("actual_sales",$row['id'],"quantity");
											$tax = $row['tax'] ? $row['tax'] : 0;
											$is_active = ($row['date_end'] && $date < $now) ? '<i class="fa fa-user-times text-danger" aria-hidden="true"></i>' : '<i class="fa fa-user text-success" aria-hidden="true"></i>';
                                            echo '<tr role="row" class="row_member '.$row['ma_id'].'">
														 
														 <td class="text-center">' . $row['ma_id'] . '</td>
														 <td class="text-center"> <a class ="detail_member"  href="/modum/member/_detail_member.php?id='.$row['id'].'">' . $row['tendangnhap'] . '</a></td>
														 <td class="text-center"> <a class ="detail_member"  href="/modum/member/_detail_member.php?id='.$row['id'].'">' . $row['hovaten'] . '</a></td>
														 <td class="text-center"> ' . $gender[$row['gioitinh']] . ' </td>
														 <td class="text-center">' . $row['age'] . '</td>
														 <td class="text-center">' . (($row["status"]==1)?date("d-m-Y",$row['datecreated']):"Not Active") . '</td><td class="text-center">' . (($row['date_end'] && $date < $now)? date("d-m-Y",$row['date_end']):"") . '</td>
														 <td class="text-center">'.number_format($rowgetActual).'</td>
														 <td class="text-center">'.number_format($dbf->getMemberDelivery("history_sales",$row['id'],"quantity"),0).'</td>
														 <td class="text-center">'.number_format($dbf->getMemberDelivery("history_sales",$row['id'],"price"),0).'</td>
														 <td class="text-center">'.number_format($dbf->getMemberDelivery("history_payment",$row['id'],"price"),0).'</td>
														 <td class="text-center">'.number_format($dbf->getMemberDelivery("history_payment",$row['id'],"quantity"),0).'</td>
														 
														 <td class="text-center"  style="width:100px">'.number_format(($dbf->getMemberDelivery("history_sales",$row['id'],"quantity")-$dbf->getMemberDelivery("history_payment",$row['id'],"quantity")),0).'</td>
															 <td class="text-center fix-width"  style="width:100px">'.number_format(($dbf->getMemberDelivery("history_sales",$row['id'],"price")-$dbf->getMemberDelivery("history_payment",$row['id'],"price")),0).'</td>
														 <td class="text-center"> ' . $is_active . '</td>
														 <td class="text-center"><button type="button" class="btn btn-neo mb-1 add_sales" data-id="'.$row['id'].'" data-price="'.$row['price'].'" data-tax="'.$tax.'">
											<i class="fa fa-plus" aria-hidden="true"></i> '._ADD.'
										</button></td>
														 <td class="text-center">';
                                                        if($rowgetInfo["roles_id"]!=15)
                                                        {
                                                        echo '<a href="' .HOST. 'edit_member_create.aspx?id=' . $row['id'] . '" class="btn btn-effect-ripple btn-xs btn-secondary" data-toggle="tooltip" title="" style="overflow: hidden; position: relative;" data-original-title="'._EDIT.'"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                                        <a href="' .HOST. 'member-list.aspx?id_del=' . $row['id'] . '&delete=true" class="btn btn-effect-ripple btn-xs btn-danger" data-toggle="tooltip" title="" onclick="return confirm(\'Are you really want to delete?\');" style="overflow: hidden; position: relative;" data-original-title="'._DELETE.'"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
                                                        }
                                                     echo'</td>
                                                  </tr>';
                                            }
                                        }
                                    }
                                  ?>
								</tbody>
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

<script type="text/javascript">
//<![CDATA[
    jQuery(document).ready(function($) {
		
		$('#member_list_all').DataTable( {
			"pagingType": "full_numbers",
			"bFilter": false,
			"language":
				{
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
				},
			"columnDefs": [ {
				"targets": 'no-sort',
				"orderable": false,
			} ]
		} );
		
    });
 //]]>
</script>

<script>
function commaSeparateNumber(val){
    while (/(\d+)(\d{3})/.test(val.toString())){
      val = val.toString().replace(/(\d+)(\d{3})/, '$1'+','+'$2');
    }
    return val;
}

$( ".active_filter.fa-users" ).click(function(){
		$("#active_user").val("all");
		$("#user_search").submit();
});
$( ".active_filter.fa-user" ).click(function(){
		$("#active_user").val("active");
		$("#user_search").submit();
});
$( ".active_filter.fa-user-times" ).click(function(){
		$("#active_user").val("withdrawal");
		$("#user_search").submit();
});

$( ".add_sales" ).click(function()
{
var member_id = $(this).attr("data-id");
var price_without_tax =  parseFloat($(this).attr("data-price"));
var tax =  parseFloat($(this).attr("data-tax"));
var price  =  (price_without_tax * tax)/100 + price_without_tax;
var flag = true;
$.confirm({
	boxWidth: '50%',
	useBootstrap: false,
	closeIcon: false,
    title: '<?php echo _TRANSACTIONUPD;?>',
    content: '' +
    '<form action="" class="formName">' +
    '<div class="form-group">' +
    '<div class="form-group row"><div class="col-sm-12 border-bottom mb-3 pb-3"><?php echo _SHIPMENTSTS;?></div><label class="col-sm-2 col-form-label"><?php echo _DATE;?></label>' +
    '<div class="col-sm-10"><input type="text" value="<?php echo date("d-m-Y H:i:s",time()); ?>" class="form-control input-datepicker" name="orderdate" id="orderdate"></div></div>' +
    '<div class="form-group row"><label class="col-sm-2 col-form-label"><?php echo _SHIPMENT;?></label>' +
    '<div class="col-sm-3"><input class="form-control" id="totalsale" name="totalsale" type="text" value="" placeholder="<?php echo _TOTALQTY;?>" autocomplete="off"></div>' +
	'<div class="col-sm-3"><input type="text" class="totalamount form-control" value="" placeholder="<?php echo _TOTALAMT;?> "autocomplete="off" readonly><input class="form-control" name="totalamount" id="totalamount" type="hidden" ></div>' +
	'<div class="col-sm-4"><input class="form-control" id="deliverycomment" name="deliverycomment" type="text" value="" placeholder="<?php echo _NOTE;?>" autocomplete="off"></div></div>' +
	'<div class="form-group row"><div class="col-sm-12 mb-3 pb-3 text-center"><a href="javascript:void(0)" class="btn btn-effect-ripple btn-neo transaction-update shipment-update"><?php echo _ADDORDER;?></a><a href="javascript:void(0)" class="btn btn-effect-ripple btn-secondary shipment-cancel"><?php echo _REFRESH;?></a></div></div>'+
	'<div class="form-group row"><div class="col-sm-12 border-bottom mb-3 pb-3"><?php echo _RECEIVEDSTS;?></div><label class="col-sm-2 col-form-label"><?php echo _DATE;?></label>' +
    '<div class="col-sm-10"><input type="text" value="<?php echo date("d-m-Y H:i:s",time()); ?>" class="form-control input-datepicker" name="paiddate" id="paiddate"></div></div>' +
	'<div class="form-group row"><label class="col-sm-2 col-form-label"><?php echo _RECEIVED;?></label>' +
    '<div class="col-sm-3"><input class="form-control salepaid" type="text" value="" placeholder="<?php echo _TOTALQTY;?>" autocomplete="off" readonly><input class="form-control" id="salepaid" name="salepaid" type="hidden" value=""></div>' +
	'<div class="col-sm-3"><input class="form-control" id="amountpaid" name="amountpaid" type="text" value="" placeholder="<?php echo _TOTALAMT;?>" autocomplete="off"></div>' +
	'<div class="col-sm-4"><input class="form-control" id="paidcomment" name="paidcomment" type="text" value="" placeholder="<?php echo _NOTE;?>" autocomplete="off"></div></div>' +
	'<div class="form-group row"><div class="col-sm-12 mb-3 pb-3 text-center"><a href="javascript:void(0)" class="btn btn-effect-ripple btn-neo transaction-update received-update"><?php echo _ADDORDER;?></a><a href="javascript:void(0)" class="btn btn-effect-ripple btn-secondary received-cancel"><?php echo _REFRESH;?></a></div></div>'+
	'<div class="form-group row"><div class="col-sm-12 border-bottom mb-3 pb-3"><?php echo _ACTUALSTATUS;?></div><label class="col-sm-2 col-form-label"><?php echo _DATE;?></label>' +
    '<div class="col-sm-10"><input type="text" value="<?php echo date("d-m-Y H:i:s",time()); ?>" class="form-control input-datepicker" name="actualpaiddate" id="actualpaiddate"></div></div>' +
	'<div class="form-group row"><label class="col-sm-2 col-form-label"><?php echo _RECEIVED;?></label>' +
    '<div class="col-sm-3"><input class="form-control" id="actualsalepaid" name="actualsalepaid" type="text" value="" placeholder="<?php echo _TOTALQTY;?>" autocomplete="off"></div>' +
	'<div class="col-sm-3"><input class="form-control actualsalepaid" type="text" value="" placeholder="<?php echo _TOTALAMT;?>" autocomplete="off" readonly><input class="form-control" name="actualamountpaid" id="actualamountpaid" type="hidden" ></div>' +
	'<div class="col-sm-4"><input class="form-control" id="actualpaidcomment" name="actualpaidcomment" type="text" value="" placeholder="<?php echo _NOTE;?>" autocomplete="off"></div></div>' +
	'<div class="form-group row"><div class="col-sm-12 mb-3 pb-3 text-center"><a href="javascript:void(0)" class="btn btn-effect-ripple btn-neo transaction-update actual-update"><?php echo _ADDORDER;?></a><a href="javascript:void(0)" class="btn btn-effect-ripple btn-secondary actual-cancel"><?php echo _REFRESH;?></a></div></div>'+
	'</div>' +
    '</form>',
    buttons: {
        /*formSubmit: {
            text: '<?php echo _SUBMIT;?>',
            btnClass: 'btc--deposit btn-primary d-none',
            action: function () {
                var orderdate = this.$content.find('#orderdate').val();
                var paiddate = this.$content.find('#paiddate').val();
                var totalsale = this.$content.find('#totalsale').val();
                var totalamount = this.$content.find('#totalamount').val();
                var deliverycomment = this.$content.find('#deliverycomment').val();
                var salepaid = this.$content.find('#salepaid').val();
                var amountpaid = this.$content.find('#amountpaid').val();
                var paidcomment = this.$content.find('#paidcomment').val();
                if(!orderdate){
                    $.alert('Please select order date');
                    return false;
                }
				$.ajax({
					type: "GET",
					data:{orderdate:orderdate,paiddate:paiddate,totalsale:totalsale,totalamount:totalamount,deliverycomment:deliverycomment,salepaid:salepaid,amountpaid:amountpaid,paidcomment:paidcomment,member_id:member_id},  
					dataType: 'json',
					
					url: "/modum/member/created_sales.php",
					success: function(response) {
						
						if(response["status"]==1)
						{ 						
							$.confirm({
								title: false,
								content: response["data"],
								buttons: {
									confirm: {
										text: '<?php echo _OK;?>',
										action: function(){
											window.location.reload(true);
										}
									}
								}
							});
						}else
						{
							$.alert({
								title: false,
								autoClose: 'confirm|6000',
								content: response["data"],
								buttons: {
									confirm: {
										text: '<?php echo _CLOSE;?>',
									}
								}
								});						
						}
					}
				});		
				
				
            }
        },*/
        cancel: {
            text: '<?php echo _CLOSE;?>',
			action: function(){
				window.location.reload(true);
			}
        },
    },
    onContentReady: function () {
        // bind to events
        var jc = this;
		$(document).on('input', '#totalsale', function() {
			var totalsale = parseInt($(this).val());
			var totalamount = price * totalsale;
			 $('.totalamount').val(commaSeparateNumber(totalamount));
			 $('#totalamount').val(totalamount);
		});
		$(document).on('input', '#amountpaid', function() {
			var amountpaid = $(this).val();
			var salepaid = amountpaid / price;
			 $('.salepaid').val(commaSeparateNumber(salepaid));
			 $('#salepaid').val(salepaid);
		});
		$(document).on('input', '#actualsalepaid', function() {
			var actualamountpaid = parseInt($(this).val());
			var actualsalepaid = price * actualamountpaid;
			 $('.actualsalepaid').val(commaSeparateNumber(actualsalepaid));
			 $('#actualamountpaid').val(actualsalepaid);
		});
		
		
		$(document).on('click', '.shipment-update', function(e) {
			e.preventDefault(); // prevent submit button from firing and submit form
			if(flag) {
				flag = false;
				var orderdate = $('.formName').find('#orderdate').val();
				var totalsale = $('.formName').find('#totalsale').val();
				var totalamount = $('.formName').find('#totalamount').val();
				var deliverycomment = $('.formName').find('#deliverycomment').val();
				if(!orderdate){
					flag = true;
					$.alert({title: false,content:'Please select order date',closeIcon: true,});
					return false;
				}
				
				if(totalsale==""){
					flag = true;
					$.alert({title: false,content:'<?php echo _NOSAVE;?>',closeIcon: true,});
					return false;
				}
				$.confirm({
								title: false,
								closeIcon: false,
								//autoClose: 'confirm|6000',
								content: '<?php echo _RUSURE;?>',
								buttons: {
									confirm: {
										text: '<?php echo _YES;?>',
										action: function(){

											/*begin ajax >>*/
											$.ajax({
												type: "GET",
												data:{orderdate:orderdate,totalsale:totalsale,totalamount:totalamount,deliverycomment:deliverycomment,member_id:member_id},  
												dataType: 'json',
												
												url: "/modum/member/created_sales.php",
												success: function(response) {						
													if(response["status"]==1)
													{ 	
														$.confirm({
															title: false,
															closeIcon: false,
															//autoClose: 'confirm|6000',
															content: response["data"],
															buttons: {
																confirm: {
																	text: '<?php echo _CLOSED;?>',
																	action: function(){
																		window.location.reload(true);
																	}
																}
															}
														});							
													}else
													{
														$.alert({
															title: false,
															closeIcon: false,
															//autoClose: 'confirm|6000',
															content: response["data"],
															buttons: {
																confirm: {
																	text: '<?php echo _CLOSE;?>',
																}
															}
															});						
													}
													flag = true;
												}
											});
											/*End ajax << */
											}
													},
				 cancel: {
							text: '<?php echo _NOT;?>',
							action: function(){
								flag = true;
							}
						}
								}
							}); /*End confirm << */
			}
		});
		
		$(document).on('click', '.received-update', function(e) {
			e.preventDefault(); // prevent submit button from firing and submit form
			if(flag) {
				flag = false;
				var paiddate = $('.formName').find('#paiddate').val();
				var salepaid = $('.formName').find('#salepaid').val();
				var amountpaid = $('.formName').find('#amountpaid').val();
				var paidcomment = $('.formName').find('#paidcomment').val();
				if(!paiddate){
					flag = true;
					$.alert({title: false,content:'Please select order date',closeIcon: true,});					
					return false;
				}
				
				if(amountpaid =="" ){
					flag = true;
					$.alert({title: false,content:'<?php echo _NOSAVE;?>',closeIcon: true,});					
					return false;
				}

					$.confirm({
								title: false,
								closeIcon: false,
								//autoClose: 'confirm|6000',
								content: '<?php echo _RUSURE;?>',
								buttons: {
									confirm: {
										text: '<?php echo _YES;?>',
										action: function(){

												/*begin ajax >>*/
												$.ajax({
													type: "GET",
													data:{paiddate:paiddate,salepaid:salepaid,amountpaid:amountpaid,paidcomment:paidcomment,member_id:member_id},  
													dataType: 'json',
													
													url: "/modum/member/created_sales.php",
													success: function(response) {
														
														if(response["status"]==1)
														{ 						
															$.confirm({
																title: false,
																closeIcon: false,
																//autoClose: 'confirm|6000',
																content: response["data"],
																buttons: {
																	confirm: {
																		text: '<?php echo _CLOSED;?>',
																		action: function(){
																			window.location.reload(true);
																		}
																	}
																}
															});
														}else
														{
															$.alert({
																title: false,
																closeIcon: false,
																//autoClose: 'confirm|6000',
																content: response["data"],
																buttons: {
																	confirm: {
																		text: '<?php echo _CLOSE;?>',
																	}
																}
																});						
														}
														
														flag = true;
														
													}
												});
												/*End ajax << */
								}
																	},
								 cancel: {
											text: '<?php echo _NOT;?>',
											action: function(){
												flag = true;
											}
										}
								}
							}); /*End confirm << */

			}
		});
		$(document).on('click', '.actual-update', function(e) {
			e.preventDefault(); // prevent submit button from firing and submit form
			if(flag) {
				flag = false;
				var actualpaiddate = $('.formName').find('#actualpaiddate').val();
				var actualsalepaid = $('.formName').find('#actualsalepaid').val();
				var actualamountpaid = $('.formName').find('#actualamountpaid').val();
				var actualpaidcomment = $('.formName').find('#actualpaidcomment').val();
				if(!actualpaiddate){
					flag = true;
					$.alert({title: false,content:'Please select a date',closeIcon: true,});					
					return false;
				}
				
				if(actualamountpaid =="" ){
					flag = true;
					$.alert({title: false,content:'<?php echo _NOSAVE;?>',closeIcon: true,});					
					return false;
				}

					$.confirm({
								title: false,
								closeIcon: false,
								//autoClose: 'confirm|6000',
								content: '<?php echo _RUSURE;?>',
								buttons: {
									confirm: {
										text: '<?php echo _YES;?>',
										action: function(){

												/*begin ajax >>*/
												$.ajax({
													type: "GET",
													data:{actualpaiddate:actualpaiddate,actualsalepaid:actualsalepaid,actualamountpaid:actualamountpaid,actualpaidcomment:actualpaidcomment,member_id:member_id},  
													dataType: 'json',
													
													url: "/modum/member/created_sales.php",
													success: function(response) {
														
														if(response["status"]==1)
														{ 						
															$.confirm({
																title: false,
																closeIcon: false,
																//autoClose: 'confirm|6000',
																content: response["data"],
																buttons: {
																	confirm: {
																		text: '<?php echo _CLOSED;?>',
																		action: function(){
																			window.location.reload(true);
																		}
																	}
																}
															});
														}else
														{
															$.alert({
																title: false,
																closeIcon: false,
																//autoClose: 'confirm|6000',
																content: response["data"],
																buttons: {
																	confirm: {
																		text: '<?php echo _CLOSE;?>',
																	}
																}
																});						
														}
														
														flag = true;
														
													}
												});
												/*End ajax << */
								}
																	},
								 cancel: {
											text: '<?php echo _NOT;?>',
											action: function(){
												flag = true;
											}
										}
								}
							}); /*End confirm << */

			}
		});
		$(document).on('click', '.shipment-cancel', function() {
			$('.formName').find('#totalsale').val('');
			$('.formName').find('#totalamount').val('');
			$('.formName').find('.totalamount').val('');
			$('.formName').find('#deliverycomment').val('');
		});
		$(document).on('click', '.received-cancel', function() {
			$('.formName').find('#salepaid').val('');
			$('.formName').find('.salepaid').val('');
			$('.formName').find('#amountpaid').val('');
			$('.formName').find('#paidcomment').val('');
		});	
		$(document).on('click', '.actual-cancel', function() {
			$('.formName').find('#actualsalepaid').val('');
			$('.formName').find('.actualsalepaid').val('');
			$('.formName').find('#actualamountpaid').val('');
			$('.formName').find('#actualpaidcomment').val('');
		});	
        /*this.$content.find('form').on('submit', function (e) {
            // if the user submits the form by pressing enter in the field.
            e.preventDefault();
            jc.$$formSubmit.trigger('click'); // reference the button and click it
        });*/
		
		$( "#orderdate,#paiddate,#actualpaiddate" ).datepicker(
	    {	
			changeMonth: true,
			changeYear: true,
			dateFormat: 'dd-mm-yy' + getHora(),
		});
		
    }
});

});
</script>

<script type="text/javascript">
//<![CDATA[
    jQuery(document).ready(function($) {
        $(".detail_member").fancybox({
        closeClick  : true,
        openEffect  : 'fade',
        closeEffect : 'fade',
        scrolling   : false,
        padding     : 0,
        type		: 'iframe',		
		autoScale: false,
		smallBtn : true,
		toolbar  : false
        });

		$("#data-log").fancybox({
        closeClick  : true,
        openEffect  : 'fade',
        closeEffect : 'fade',
        scrolling   : false,
        padding     : 0,
        type		: 'iframe',		
		autoScale: false,
		smallBtn : true,
		toolbar  : false
        });
    });
 //]]>
</script>

<style>
.fancybox-slide--iframe .fancybox-content {
    max-width  : 80%;
    max-height : 80%;
    margin: 0;
}
</style>
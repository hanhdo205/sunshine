    <link rel="stylesheet" href="style/jquery.treeview.css" />
	<script src="js/lib/jquery.cookie.js" type="text/javascript"></script>
	<script src="js/jquery.treeview.js" type="text/javascript"></script>
	<script type="text/javascript" src="js/script_function.js"></script>
<div class="post block">
<?php
if( $_SESSION["Free"]==1)
{
  $html->redirectURL("/login.html");

}else
{


        $rstgetInfo=$dbf->getDynamic("member","id='".$_SESSION["member_id"]."'","");
        $rowgetInfo=$dbf->nextObject($rstgetInfo);

        $infonguoibaotro = $dbf->getInfoColum("member",(int)$rowgetInfo->parentid);
        $infotinhthanh = $dbf->getInfoColum("city_vietnam",(int)$rowgetInfo->tinhthanh);

        if(isset($_POST["cmdSearch"]) && $_POST["keyword"]!='')
        {
            $_SESSION["search"]=$_POST['keyword'];
        }


?>
<div style="float: left; padding:20px;">
<?php include("linkmember.php");?>
</div>
<div class="clear"></div>

  <div class="accordion-group">
     <div style="background: #2D82C3; color: white; font-size:20px; padding:10px;cursor: Pointer; text-transform: uppercase; text-align:left; font-weight:bold" class="accordion-toggle">
      DANH SÁCH Thành Viên CỦA <?php echo $rowgetInfo->hovaten; ?>
      <span class="search"><?php include("_search.php");?></span>
      <span class="clear"></span>
      </div>
  </div>
  <?php
    if(isset($_SESSION["search"]) && $_SESSION["search"]!='')
    {
        /*
        $MaID   = explode($info["MS"],$_SESSION["search"]);
        array_shift($MaID);
        if((int)$MaID[0] > 0 )
        {
          $MaID_Current = $MaID[0];

        }else
        {
           $MaID_Current="-1";
        }
        */

       $result = $dbf->getDynamic("member","hovaten like'%".$_SESSION["search"]."%' or ma_id='".$_SESSION["search"]."'","");
       $total  = $dbf->totalRows($result);
       if( $total > 0)
       {
        echo "<h1 class='h_1'>Kết quả tìm kiếm với từ khóa '".$_SESSION["search"]."'</h1>";
        echo '<table id="mainTable">
                            <thead>
                              <tr style="background:#848484; color: #fff;">
                               <th class="itemText" width="50"><b>STT</b></th>
                               <th class="itemText" width="100"><b>MSTV</b></th>
                               <th class="itemText" width="250"><b>TÊN THÀNH VIÊN</b></th>
                               <th class="itemText" width="250"><b>XẾP SAU</b></th>
                               <th class="itemText" width="250"><b>NGÀY THAM GIA</b></th>
                               <th class="itemText" width="100" align="center"><b>CẤP ĐỘ</b></th>
                              </tr>
                            </thead>
                            <tbody>';

                   $strParentId = "";
                   $i=1;
                   while( $row = $dbf->nextData($result))
                   {
                         $strParentId.=$row['id'].",";
                         $ponser = $dbf->getInfoColum("member",$row['parentid']);
                         $level  = $dbf->getLevel($row['parentid']);
                         echo '<tr class="cell1">
                            <td class="itemText">'.$i.'</td>
                            <td class="itemText">'.$row['ma_id'].'</td>
                            <td class="itemText">'.$row['hovaten'].'</td>
                            <td class="itemText">'.$info["MS"].$ponser['id'].'-'.$ponser['hovaten'].'</td>
                            <td class="itemText">'.date('d-m-Y',$row['datecreated']).'</td>
                            <td class="itemText">'.$level.'</td>
                         </tr>';
                   }
           echo "</tbody></table>";
         } // end if
         else
         {
           echo "<h1 class='h_1'>Không tìm thấy thành viên này '".$_SESSION["search"]."'</h1>";
         }

  }
  ?>
  <div style="padding: 0px 20px;">
    <h1 class='h_1' style="text-align: left; text-indent: 0px;">Cây phả hệ</h1>
    <ul id="navigation" class="ul_<?php echo $rowgetInfo->id?>">
		<li id="<?php echo $rowgetInfo->id?>">
            <?php echo $rowgetInfo->hovaten; ?>
            <?php $dbf->getTreeMember($rowgetInfo->id,$info["MS"]); ?>
		</li>
	</ul>
    </div>

<?php } ?>
<div class="clear"></div>
</div>

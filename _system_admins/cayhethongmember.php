<?php
include ("index_table.php");
?>
<div class="panelAction" id="panelAction">
   <div style="float:left;" class="boxRedInside"></div>
   <div style="float:right">
    <table cellspacing="1" cellpadding="1" id="panelTable">
    <tbody><tr>
        <td class="cellAction1"><a href="?table_name=member&amp;insert" title="Insert"><img border="0" title="Insert" src="themes/theme_default/images/new.jpg"></a></td><td><a href="mngMain.php?table_name=member&amp;insert" id="lnkaction" title="Insert">Thêm mới thành viên</a></td>

        <td class="cellAction1"><a href="mngMain.php?table_name=log_payment_member&member_id=<?=$_GET["member_id"]?>&insert" title="Insert"><img border="0" title="Insert" src="themes/theme_default/images/new.jpg"></a></td><td><a href="mngMain.php?table_name=log_payment_member&member_id=<?=$_GET["member_id"]?>&insert" id="lnkaction" title="Insert">Thanh toán</a></td>


        </tr>
    </tbody></table>
    </div><div id="clear"></div>
 </div>
 
<div class="panelAction" id="panelAction" style="height: 150px;">
    <fieldset style="width: 600px">
       <legend style="color: #000080; padding-bottom: 10px"><b>Xem từng thành viên</b></legend>
      <span style="float: left; width:100px">Thành viên</span>
      <select name="member_id" id="member_id" style="width:180px;">
    	<option value="">-- Tất cả -- </option>
        <?php
        $rst_nhanvien = $dbf->getDynamic("member", "status=1", "id asc");
        $toal_nhanvien = $dbf->totalRows($rst_nhanvien);
        if ($toal_nhanvien > 0) {
          while ($rows_nhanvien = $dbf->nextdata($rst_nhanvien)) {
            $id_nhanvien = $rows_nhanvien['id'];
            $ma_id = $rows_nhanvien['ma_id'];
            $title_nhanvien = stripcslashes($rows_nhanvien['hovaten']);
            echo '<option ' . (($_POST["member_id"] == $id_nhanvien) ? "selected" : "") . ' value="' . $id_nhanvien . '">' . $ma_id . '-' . $title_nhanvien . '</option>';
          }
        }
        ?>

       </select>

        <!--Số tầng <input type="text" name="tang" style="width:50px;" value="<?php echo (((int) $_POST["tang"] != 0) ? $_POST["tang"] : 3) ?>" />!-->

        <div class="clear"></div>
        <input type="button" name="cmdSearch" id="cmdSearch" value="Xem giá trị" />
        </fieldset>
      <!--    
       <div style="padding: 5px">
          <img src="images/goi/1.png" alt="" width="40" height="40" align="absmiddle" />Cấp 1   <img src="images/goi/2.png" alt="" width="40" height="40" align="absmiddle" />Cấp 2  <img src="images/goi/3.png" alt="" width="40" height="40" align="absmiddle" />Cấp 3  <img src="images/goi/4.png" alt="" width="40" height="40" align="absmiddle" />Cấp 4
       </div>
      !--> 
</div>


<?php
$_SESSION["str_member"] = "";
$_SESSION["total_all"] = 0;

$member_current = 0;
$str = "";
//$tang = (int)$_POST["tang"];
$tang = 10;
$array_info_memter = array();
$arrayPackeges = array();
$result = $dbf->getDynamic("packages","status=1","id asc");
while( $row = $dbf->nextData($result))
{
   $arrayPackeges[$row["id"]] = $row;
}

if (isset($_POST["member_id"]) || isset($_GET["member_id"])) {
  if ($_POST["member_id"] != "" || isset($_GET["member_id"])) {
    if (isset($_POST["member_id"])) {
      $member_id = $_POST["member_id"];
    }
    else {
      $member_id = (int) $_GET["member_id"];
      $tang = 10;
    }
    $rst_member_all = $dbf->getDynamic("member", "id=" . $member_id . "", "");
    $str = "";
    $total = $dbf->totalRows($rst_member_all);
    while ($row_member_all = $dbf->nextData($rst_member_all)) {
      $array_info_memter = $row_member_all;
	  
	  
      $id = $row_member_all["id"];
      $ma_id = $row_member_all["ma_id"];
      //member tính tiền
      $member_current = $ma_id;
      //$count = $dbf->countmember($id, 3, 1, 0);
      if($row_member_all["status"]==1)
	  {
		  $images = "<a href=\'cayhethongmember.php?member_id=" . $id . "\'> <img src=\'images/goi/2.png\' width=\'40\' height=\'40\' /></a>";
     
	  }else
	  {
		  $images = "<a href=\'cayhethongmember.php?member_id=" . $id . "\'> <img src=\'images/goi/2.png\' width=\'40\' height=\'40\' /></a>";
	  }
           //$str.= "['".$ma_id."-".$row_member_all["tendangnhap"]."', '', ''],";
      $str .= "[{v:'" . $ma_id . "-" . $row_member_all["hovaten"] . "',f:'" .  $ma_id."-".$row_member_all["hovaten"] . "-".date("d-m-Y",$row_member_all["datecreated"]) . "<br/>" . $images . "'}, '', ''],";
      //get total item member
      $dbf->cayhethongmember($id, 10, 1);
      $str .= $_SESSION["str_member"];
    }
    $total += $_SESSION["total_all"];
  }
  else {
    $rst_member_all = $dbf->getDynamic("member", "status=1", "id asc limit 0,1");
    $str = "";
    $total = $dbf->totalRows($rst_member_all);
    while ($row_member_all = $dbf->nextData($rst_member_all)) {
      $array_info_memter = $row_member_all;
      $id = $row_member_all["id"];
      $ma_id = $row_member_all["ma_id"];
//member tính tiền
      $member_current = $ma_id;
      //$count = $dbf->countmember($id, 3, 1, 0);
      
          $images = "<a href=\'cayhethongmember.php?member_id=" . $id . "\'> <img src=\'images/goi/4.png\' width=\'40\' height=\'40\' /></a>";

      $str .= "[{v:'" . $ma_id . "-" . $row_member_all["hovaten"] . "',f:'" . $ma_id."-". $row_member_all["hovaten"] . "-".date("d-m-Y",$row_member_all["datecreated"]) . "<br/>" . $images . "'}, '', ''],";
      //get total item member
      $dbf->cayhethongmember($id, 10, 1);
      $str .= $_SESSION["str_member"];
    }
    $total += $_SESSION["total_all"];
  }
}
else {
  $rst_member_all = $dbf->getDynamic("member", "status=1", "id asc limit 0,1");
  $str = "";
  $total = $dbf->totalRows($rst_member_all);
  while ($row_member_all = $dbf->nextData($rst_member_all)) {
    $array_info_memter = $row_member_all;
    $id = $row_member_all["id"];
    $ma_id = $row_member_all["ma_id"];
    //$count = $dbf->countmember($id, 3, 1, 0);
    
        
	  if($row_member_all["status"]==1)
	  {
		  $images = "<a href=\'cayhethongmember.php?member_id=" . $id . "\'> <img src=\'images/goi/2.png\' width=\'40\' height=\'40\' /></a>";
     
	  }else
	  {
		  $images = "<a href=\'cayhethongmember.php?member_id=" . $id . "\'> <img src=\'images/goi/2.png\' width=\'40\' height=\'40\' /></a>";
	  }
//member tính tiền
    $member_current = $ma_id;
    $str .= "[{v:'" . $ma_id . "-" . $row_member_all["hovaten"] . "',f:'" . $ma_id."-".$row_member_all["hovaten"] . "-".date("d-m-Y",$row_member_all["datecreated"]) . "<br/>" . $images . "'}, '', ''],";
//get total item member
    $dbf->cayhethongmember($id, 10, 1);
    $str .= $_SESSION["str_member"];
  }
  $total += $_SESSION["total_all"];
}
?>



<script type="text/javascript" src="https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization','version':'1.1','packages':['orgchart']}]}"></script>
<script type='text/javascript' src='https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js'></script>
<script type="text/javascript">

      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Name');
        data.addColumn('string', 'Manager');
        data.addColumn('string', 'ToolTip');

        data.addRows([
          <?php echo $str; ?>
        ]);
        var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
        chart.draw(data, {allowHtml:true});
      }

</script>


<div class="is_bg" style="position:relative;overflow-x:scroll; width:100%">
    <div class="chart_div" id="chart_div"></div>
</div>



<script type="text/javascript">

    var currZoom = 1;
    $(document).ready(function () {
        $("#ZoomIn").click(function () {
           currZoom *= 1.2;
           $("#chart_div").css("zoom", currZoom);
           $("#chart_div").css("-moz-transform", "Scale(" + currZoom + ")");
           $("#chart_div").css("-moz-transform-origin", "0 0 0");
           $("#chart_div").css("-moz-transform", "scale(" + currZoom + " , " + currZoom + ")");
        });

        $("#ZoomOut").click(function () {
            currZoom *= .8;
            $("#chart_div").css("zoom", currZoom);
            $("#chart_div").css("-moz-transform", "Scale(" + currZoom + ")");
            $("#chart_div").css("-moz-transform", "scale(" + currZoom + " , " + currZoom + ")");
            $("#chart_div").css("-moz-transform-origin", "0 0 0");
        });
    });

</script>



<script language="JavaScript" type="text/javascript">
/*<![CDATA[*/
$('#cmdSearch').click(function(){
    $('form[name=frm]').attr('action','cayhethongmember.php');
    $('form[name=frm]').submit();
});
/*]]>*/
</script>
<?php
include ("index_table.php");
?>

<table width="100%" cellspacing="0" cellpadding="0" align="center"><tbody><tr><td class="boxRedInside" colspan="5"><div class="boxRedInside">Member đăng ký thành viên tự động <span style="float:right"><a style="background: #C20000; padding: 5px; border:1px soild #000; margin:5px; color:#fff" href="export.php"> >>Export Danh sách đăng ký</a></span></div></td></tr></tbody></table>

<script>
var foo = "bar";
	var xmlRequest = null;

	function initRequest(url){
		if (window.ActiveXObject){
			xmlRequest = new ActiveXObject("Microsoft.XMLHTTP");
		}
		else if(window.XMLHttpRequest) {
			xmlRequest = new XMLHttpRequest();
		}
}

function kickhoat(id,id_input)
{

      if(jQuery('#'+id_input).is(':checked')){
        status = 1;
      }else
      {
        status = 0;
      }

      var url = "updateStatus.php";
      url+='?id='+id+'&status='+status;
      initRequest(url);
      xmlRequest.open("GET", url, true);
      xmlRequest.onreadystatechange = callback;
      xmlRequest.send(null);
}
function callback(){
		if (xmlRequest.readyState == 4) {
			if (xmlRequest.status == 200) {
                var data = xmlRequest.responseText;
                if(data==1)
                {
                  alert("Đã cập nhật được trạng thái thành viên thành công");
                }else
                {
                  alert("Bị lỗi! Không cập nhật được trạng thái thành viên");
                }
			 } else if (xmlRequest.status == 204){
				alert("Bị lỗi! Không cập nhật được trạng thái thành viên");
			}
		}

	}
</script>

<div class="post block">

<?php
//$mang = $dbf->paging("member","member_re=0",'id desc', $PageNo, $PageSize, $Pagenumber, $ModePaging);
$rs_member=$dbf->getDynamic("member","member_re=1","id asc");
if($dbf->totalRows($rs_member)>0)
{
 echo '<table id="mainTable">
                        <thead>
                          <tr style="background:#848484; color: #fff;">
                           <th class="itemText" width="50"><b>STT</b></th>
                           <th class="itemText" width="100"><b>MSTV</b></th>
                           <th class="itemText" width="250"><b>TÊN Thành Viên</b></th>
                           <th class="itemText" width="250"><b>XẾP SAU</b></th>
                           <th class="itemText"><b>NGÀY THAM GIA</b></th>
                           <th class="itemText"><b>Kích hoạt tài khoản (Chính thức)</b></th>
                          </tr>
                        </thead>
                        <tbody>';
$i=1;
while ($rowgetInfo = $dbf->nextData($rs_member)) {

       $ponser = $dbf->getInfoColum("member", $rowgetInfo['parentid']);
        echo '<tr class="cell1">
                        <td class="itemText">' . $i . '</td>
                        <td class="itemText"><a target="_blank" href="mngMain.php?edit='.$rowgetInfo["id"].'&table_name=member" id="itemText">' . $rowgetInfo['ma_id'] . '</a></td>
                        <td class="itemText">' . $rowgetInfo['hovaten'] . '</td>
                        <td class="itemText">' . $ponser['ma_id'] . '-' . $ponser['hovaten'] . '</td>
                        <td class="itemText">' . date('d-m-Y', $rowgetInfo['datecreated']) . '</td>
                        <td class="itemText"><input style="cursor:pointer" id="check_status_'.$rowgetInfo["id"].'" onClick="kickhoat('.$rowgetInfo["id"].',\'check_status_'.$rowgetInfo["id"].'\')" type="checkbox" '.(($rowgetInfo['status']==1)?"checked='true'":"").' value="" name="status"></td>
                     </tr>';
        $i++;

  ?>
<?php
}
echo "</tbody></table>";
}else
{
  echo "<h1 style='text-align:left'> <b>&nbsp;&rsaquo;&rsaquo;&nbsp;</b> Không có thành viên nào !!!</h1>";
}
?>
<div class="clear"></div>
</div>
<?php
$dbf->Footer();
?>
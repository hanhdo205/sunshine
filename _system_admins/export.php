<?php
/** Error reporting */
error_reporting(E_ALL);
date_default_timezone_set('Asia/Bangkok');
session_start();
if(empty($_SESSION["user_login"])) {
        session_unregister("user_login");
		echo "<script>window.location.href='login.php'</script>";
        exit;
}

include str_replace('\\','/',dirname(__FILE__)).'/class/class.DEFINE.php';
include str_replace('\\','/',dirname(__FILE__)).'/class/class.HTML.php';
include str_replace('\\','/',dirname(__FILE__)).'/class/class.JAVASCRIPT.php';
include str_replace('\\','/',dirname(__FILE__)).'/class/class.UTILITIES.php';
include str_replace('\\','/',dirname(__FILE__)).'/class/class.CSS.php';
include str_replace('\\','/',dirname(__FILE__)).'/class/class.SINGLETON_MODEL.php';
include str_replace('\\','/',dirname(__FILE__)).'/class/simple_html_dom.php';
include str_replace('\\','/',dirname(__FILE__)).'/class/class.BUSINESSLOGIC.php';
include str_replace('\\','/',dirname(__FILE__)).'/class/template.php';
include str_replace('\\','/',dirname(__FILE__)).'/Cache_Lite/Lite/Function.php';
$dbf_ex = new BUSINESSLOGIC();

$member_id  = $_POST["member_id"];
$from_date  = strtotime($_POST["tungay"]);

if($_POST["denngay"]==""){
 $from_to     = strtotime(date("d-m-Y",time()));
}else
{
  $from_to    = strtotime($_POST["denngay"]);
}

$currentnow =  strtotime(date("d-m-Y",time()));

if($_POST["tungay"]!="")
{
    if (($from_date > $from_to) || ($from_to>$currentnow))  {
        echo '<meta http-equiv="content-type" content="text/html;charset=UTF-8"/>';
        echo "<h3 style='text-align:left'>Please select the number of days (from <b>". $_POST["date"]."</b> to day <b> ". $_POST["dateto"]."). Data mismatch</h3>";
        exit();
    }
}

$arrayPackeges = array();
$result = $dbf_ex->getDynamic("packages","status=1","id asc");
while( $row = $dbf_ex->nextData($result))
{
   $arrayPackeges[$row["id"]] = $row;
}

$arrayWithdrawcircle = array();
$result = $dbf_ex->getDynamic("withdrawcircle","status=1","id asc");
while( $row = $dbf_ex->nextData($result))
{
   $arrayWithdrawcircle[$row["id"]] = $row;
}



/** Include PHPExcel */
require_once 'PHPExcel/Classes/PHPExcel.php';


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Thuy TQ")
							 ->setLastModifiedBy("Thuy TQ")
							 ->setTitle("Office 2007 XLSX Member List")
							 ->setSubject("Office 2007 XLSX Member List")
							 ->setDescription("Member List for Office 2007 XLSX.")
							 ->setKeywords("")
							 ->setCategory("");


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'STT')
                    ->setCellValue('B1', 'MÃ ID')
		            ->setCellValue('C1', 'HỌ VÀ TÊN')
		            ->setCellValue('D1', 'EMAIL')
   		            ->setCellValue('E1', 'CMND')
                    ->setCellValue('F1', 'MÃ ID NGƯỜI BẢO TRỢ')
                    ->setCellValue('G1', 'TRẠNG THÁI KÍCH HOẠT')
                    ->setCellValue('H1', 'GÓI THAM GIA')
                    ->setCellValue('I1', 'PRICE')
                    ->setCellValue('J1', 'TÊN CHỦ TÀI KHOẢN')
                    ->setCellValue('K1', 'SỐ TÀI KHOẢN')
                    ->setCellValue('L1', 'NGÂN HÀNG')
                    ->setCellValue('M1', 'CHI NHÁNH')
                    ->setCellValue('N1', 'CHU KỲ THANH KHOẢN')
                    ->setCellValue('O1', 'NGÀY ĐĂNG KÝ')
                    ;

        $sheet = $objPHPExcel->getActiveSheet();
        $row = 2;


        $rowgetInfo = $dbf_ex->getInfoColum("member", $member_id);
        $ponser = $dbf_ex->getInfoColum("member", $rowgetInfo['parentid']);
        $sheet->setCellValue('A' . $row, $row-1);
        $sheet->setCellValue('B' . $row, stripcslashes($rowgetInfo['ma_id']));
        $sheet->setCellValue('C' . $row, stripcslashes($rowgetInfo['hovaten']));
        $sheet->setCellValue('D' . $row, stripcslashes($rowgetInfo['email']));
        $sheet->setCellValue('E' . $row, stripcslashes($rowgetInfo['cmnd']));
        $sheet->setCellValue('F' . $row, $ponser['ma_id']."-".$ponser['hovaten']);
        $sheet->setCellValue('G' . $row, $rowgetInfo['status']);
        $sheet->setCellValue('H' . $row, $arrayPackeges[$rowgetInfo["packages_id"]]["title"]);
        $sheet->setCellValue('I' . $row, $arrayPackeges[$rowgetInfo["packages_id"]]["price"]);
        $sheet->setCellValue('J' . $row, stripcslashes($rowgetInfo['tenchutaikhoan']));
        $sheet->setCellValue('K' . $row, stripcslashes($rowgetInfo['sotaikhoan']));
        $sheet->setCellValue('L' . $row, stripcslashes($rowgetInfo['nganhang']));
        $sheet->setCellValue('M' . $row, stripcslashes($rowgetInfo['chinhanh']));
        $sheet->setCellValue('N' . $row, $arrayWithdrawcircle[$rowgetInfo['withdrawcircle_id']]["title"]);
        $sheet->setCellValue('O' . $row, date("d-m-Y",$rowgetInfo['datecreated']));
		$row++;

        $arrayMember = array();
        $arrayMemberlist =  $dbf_ex->getMemberListArray($member_id,$rowgetInfo,$arrayMember);

        if($_POST["tungay"]=="")
        {
            foreach($arrayMemberlist as $value)
            {
                $ponser = $dbf_ex->getInfoColum("member", $value['parentid']);

                $sheet->setCellValue('A' . $row, $row-1);
                $sheet->setCellValue('B' . $row, stripcslashes($value['ma_id']));
                $sheet->setCellValue('C' . $row, stripcslashes($value['hovaten']));
                $sheet->setCellValue('D' . $row, stripcslashes($value['email']));
                $sheet->setCellValue('E' . $row, stripcslashes($value['cmnd']));
                $sheet->setCellValue('F' . $row, $ponser['ma_id']."-".$ponser['hovaten']);
                $sheet->setCellValue('G' . $row, $value['status']);
                $sheet->setCellValue('H' . $row, $arrayPackeges[$value["packages_id"]]["title"]);
                $sheet->setCellValue('I' . $row, $arrayPackeges[$value["packages_id"]]["price"]);
                $sheet->setCellValue('J' . $row, stripcslashes($value['tenchutaikhoan']));
                $sheet->setCellValue('K' . $row, stripcslashes($value['sotaikhoan']));
                $sheet->setCellValue('L' . $row, stripcslashes($value['nganhang']));
                $sheet->setCellValue('M' . $row, stripcslashes($value['chinhanh']));
                $sheet->setCellValue('N' . $row, $arrayWithdrawcircle[$value['withdrawcircle_id']]["title"]);
                $sheet->setCellValue('O' . $row, date("d-m-Y",$value['datecreated']));

        		$row++;
            }
        }else
        {
             if($from_date == $from_to)
             {
                 foreach($arrayMemberlist as $value)
                 {
                        if(date("d-m-Y",$value["datecreated"]) == date("d-m-Y",$from_date))
                        {
                            $ponser = $dbf_ex->getInfoColum("member", $value['parentid']);
                            $sheet->setCellValue('A' . $row, $row-1);
                            $sheet->setCellValue('B' . $row, stripcslashes($value['ma_id']));
                            $sheet->setCellValue('C' . $row, stripcslashes($value['hovaten']));
                            $sheet->setCellValue('D' . $row, stripcslashes($value['email']));
                            $sheet->setCellValue('E' . $row, stripcslashes($value['cmnd']));
                            $sheet->setCellValue('F' . $row, stripcslashes($value['tendangnhap']));
                            $sheet->setCellValue('G' . $row, $ponser['ma_id']."-".$ponser['hovaten']);
                            $sheet->setCellValue('H' . $row, $arrayPackeges[$value["packages_id"]]["title"]);
                            $sheet->setCellValue('I' . $row, $arrayPackeges[$value["packages_id"]]["price"]);
                            $sheet->setCellValue('J' . $row, stripcslashes($value['tenchutaikhoan']));
                            $sheet->setCellValue('K' . $row, stripcslashes($value['sotaikhoan']));
                            $sheet->setCellValue('L' . $row, stripcslashes($value['nganhang']));
                            $sheet->setCellValue('M' . $row, stripcslashes($value['chinhanh']));
                            $sheet->setCellValue('N' . $row, $arrayWithdrawcircle[$value['withdrawcircle_id']]["title"]);
                            $sheet->setCellValue('O' . $row, date("d-m-Y",$value['datecreated']));
                    		$row++;
                        }
                  }
             }else
             {
                 foreach($arrayMemberlist as $value)
                 {
                     if((date("d-m-Y",$value["datecreated"]) >= date("d-m-Y",$from_date)) && ((date("d-m-Y",$value["datecreated"]) <= date("d-m-Y",$from_to))))
                    {
                        $ponser = $dbf_ex->getInfoColum("member", $value['parentid']);
                        $sheet->setCellValue('A' . $row, $row-1);
                        $sheet->setCellValue('B' . $row, stripcslashes($value['ma_id']));
                        $sheet->setCellValue('C' . $row, stripcslashes($value['hovaten']));
                        $sheet->setCellValue('D' . $row, stripcslashes($value['email']));
                        $sheet->setCellValue('E' . $row, stripcslashes($value['cmnd']));
                        $sheet->setCellValue('F' . $row, stripcslashes($value['tendangnhap']));
                        $sheet->setCellValue('G' . $row, $ponser['ma_id']."-".$ponser['hovaten']);
                        $sheet->setCellValue('H' . $row, $arrayPackeges[$value["packages_id"]]["title"]);
                        $sheet->setCellValue('I' . $row, $arrayPackeges[$value["packages_id"]]["price"]);
                        $sheet->setCellValue('J' . $row, stripcslashes($value['tenchutaikhoan']));
                        $sheet->setCellValue('K' . $row, stripcslashes($value['sotaikhoan']));
                        $sheet->setCellValue('L' . $row, stripcslashes($value['nganhang']));
                        $sheet->setCellValue('M' . $row, stripcslashes($value['chinhanh']));
                        $sheet->setCellValue('N' . $row, $arrayWithdrawcircle[$value['withdrawcircle_id']]["title"]);
                        $sheet->setCellValue('O' . $row, date("d-m-Y",$value['datecreated']));
                		$row++;
                    }
                  }
                }
           }

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Member List Register ');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
$file_name = 'MemberList_' . date('YmdHis') .'.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="'.$file_name.'"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;

?>
<?php
error_reporting(0);
session_start();
date_default_timezone_set('Asia/Bangkok');
include ('../../class/class.BUSINESSLOGIC.php');
$dbf = new BUSINESSLOGIC();
$rowgetInfo = $dbf->getInfoColum("member",$_SESSION["member_id"]);

	/** Include PHPExcel */
	include('PHPExcel/Classes/PHPExcel.php');
	
	if(isset($_POST["export_users"]))
    {
	    $from_date = strtotime($_POST["date"]);
        $from_to   = strtotime($_POST["dateto"]);
	

	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();

	// Set document properties
	$objPHPExcel->getProperties()->setCreator("Neonagashima")
								 ->setLastModifiedBy("Neonagashima")
								 ->setTitle("Office 2005 XLS Member List")
								 ->setSubject("Office 2005 XLS Member List")
								 ->setDescription("Neonagashima Member List for Office 2005 XLS.")
								 ->setKeywords("")
								 ->setCategory("");


	// Add some data
	$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A1', 'ID')
						->setCellValue('B1', 'Username')
						->setCellValue('C1', 'Full name')
						->setCellValue('D1', 'Sex')
						->setCellValue('E1', 'Age')
						->setCellValue('F1', 'Date of Enrollment')
						->setCellValue('G1', 'Date of Withdrawal')
						->setCellValue('H1', 'Shipment Total QTY')
						->setCellValue('I1', 'Shipment Total Amount')
						->setCellValue('J1', 'Received Total Amount')                               
						->setCellValue('K1', 'Received Total QTY')
						->setCellValue('L1', 'Unreceived Total QTY')
						->setCellValue('M1', 'Unreceived Total Amount')
						->setCellValue('N1', 'Status')						
						;

			$sheet = $objPHPExcel->getActiveSheet();
			$no = 2;
			$arrayMemberCurrent= array();
			$arrayMemberCurrent = $dbf->getMemberListArrayByDate($rowgetInfo["id"],$rowgetInfo,$arrayMemberCurrent,$from_date,$from_to);
			$arrayMemberCurrent = $dbf->array_sort_by_column($arrayMemberCurrent,"datecreated");			
           
            if(count($arrayMemberCurrent) >0 )
			{
				
				foreach($arrayMemberCurrent as $row)
				{
						if($row['date_end']) 
						{
							$date = new DateTime(date('Y-m-d H:i:s',$row['date_end']));
							$now = new DateTime();
						}
						
						if($row["status"]==1) {
							
							$ship_qty = $dbf->getMemberDelivery("history_sales",$row['id'],"quantity");
							$ship_amt = $dbf->getMemberDelivery("history_sales",$row['id'],"price");
							$pay_qty = $dbf->getMemberDelivery("history_payment",$row['id'],"quantity");
							$pay_amt = $dbf->getMemberDelivery("history_payment",$row['id'],"price");
							$un_aqy = $ship_qty - $pay_qty;
							$un_amt = $ship_amt - $pay_amt;
							$is_active = ($row['date_end'] && $date < $now) ? 'Withdrawal' : 'Active';	
							$sheet->setCellValue('A' . $no, stripcslashes($row['ma_id']));						
							$sheet->setCellValue('B' . $no, stripcslashes($row['tendangnhap']));							
							$sheet->setCellValue('C' . $no, stripcslashes($row['hovaten']));
							$sheet->setCellValue('D' . $no, stripcslashes($row['gioitinh']));
							$sheet->setCellValue('E' . $no, stripcslashes($row['age']));					
							$sheet->setCellValue('F' . $no, (($row["status"]==1)?date("d-m-Y",$row['datecreated']):"Not Active"));
							$sheet->setCellValue('G' . $no, (($row['date_end'] && $date < $now)?date("d-m-Y",$row['date_end']):""));						
							$sheet->setCellValue('H' . $no, $dbf->getMemberDelivery("history_sales",$row['id'],"quantity"));
							$sheet->setCellValue('I' . $no, $dbf->getMemberDelivery("history_sales",$row['id'],"price"));
							$sheet->setCellValue('J' . $no, $dbf->getMemberDelivery("history_payment",$row['id'],"price"));                                    
							$sheet->setCellValue('K' . $no, $dbf->getMemberDelivery("history_payment",$row['id'],"quantity"));                                    
							$sheet->setCellValue('L' . $no, $un_aqy);
							$sheet->setCellValue('M' . $no, $un_amt);
							$sheet->setCellValue('N' . $no, $is_active);
							$no++;
						}						
						
				}
			}


	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle('Member List');

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	// Redirect output to a client’s web browser (Excel2007)
	$file_name = 'Member List' . $_POST["date"]."_".$_POST["dateto"].'.csv';
	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.$file_name.'"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
	$objWriter->save('php://output');
	exit;
	}
?>
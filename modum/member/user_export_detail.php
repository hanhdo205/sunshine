<?php
session_start();
date_default_timezone_set('Asia/Bangkok');
include ('../../class/class.BUSINESSLOGIC.php');
$dbf = new BUSINESSLOGIC();



    $arrayPackeges = array();
    $result = $dbf->getDynamic("packages","status=1","id asc");
    while( $row = $dbf->nextData($result))
    {
       $arrayPackeges[$row["id"]] = $row;
    }

    $arrayWithdrawcircle = array();
    $result = $dbf->getDynamic("withdrawcircle","status=1","id asc");
    while( $row = $dbf->nextData($result))
    {
       $arrayWithdrawcircle[$row["id"]] = $row;
    }

    if(isset($_POST["export_users"]))
    {
        $member_id = $_SESSION["member_id"];
        $from_date = strtotime($_POST["date"]);
        $from_to   = strtotime($_POST["dateto"]);

        $currentnow =  strtotime(date("d-m-Y",time()));
         if (($from_date > $from_to) || ($from_to>$currentnow))  {
            echo '<meta http-equiv="content-type" content="text/html;charset=UTF-8"/>';
            echo "<h3 style='text-align:left'>Please select the number of days (from <b>". $_POST["date"]."</b> to day <b> ". $_POST["dateto"]."). Data mismatch</h3>";
         }else
         {
            $arrayPackeges = array();
            $result = $dbf->getDynamic("packages","status=1","id asc");
            while( $row = $dbf->nextData($result))
            {
               $arrayPackeges[$row["id"]] = $row;
            }

            $arrayWithdrawcircle = array();
            $result = $dbf->getDynamic("withdrawcircle","status=1","id asc");
            while( $row = $dbf->nextData($result))
            {
               $arrayWithdrawcircle[$row["id"]] = $row;
            }

            /** Include PHPExcel */
            require_once 'PHPExcel/Classes/PHPExcel.php';


            // Create new PHPExcel object
            $objPHPExcel = new PHPExcel();

            // Set document properties
            $objPHPExcel->getProperties()->setCreator("BTCPG")
            							 ->setLastModifiedBy("BTCPG")
            							 ->setTitle("Office 2005 XLSX Member List")
            							 ->setSubject("Office 2005 XLSX Member List")
            							 ->setDescription("Member List for Office 2005 XLS.")
            							 ->setKeywords("")
            							 ->setCategory("");


            // Add some data
            $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('A1', 'STT')
                                ->setCellValue('B1', 'ID CODE')
            		            ->setCellValue('C1', 'FULLNAME')
            		            ->setCellValue('D1', 'EMAIL')
               		            ->setCellValue('E1', 'ID INTERNATIONAL')
                                ->setCellValue('F1', 'PONSER')
                                ->setCellValue('G1', 'PACKAGE')
                                ->setCellValue('H1', 'PRICE')
                                ->setCellValue('I1', 'NAME ACOUNT BANK')
                                ->setCellValue('J1', 'ACCOUNT BANK')
                                ->setCellValue('K1', 'NAME BANK')
                                ->setCellValue('L1', 'WITHDRAM')
                                ->setCellValue('M1', 'DATE JOIN')
                                ;

                    $sheet = $objPHPExcel->getActiveSheet();
                    $row = 2;


                    /*
                    $rowgetInfo = $dbf->getInfoColum("member", $member_id);
                    $ponser = $dbf->getInfoColum("member", $rowgetInfo['parentid']);
                    $sheet->setCellValue('A' . $row, $row-1);
                    $sheet->setCellValue('B' . $row, stripcslashes($rowgetInfo['ma_id']));
                    $sheet->setCellValue('C' . $row, stripcslashes($rowgetInfo['hovaten']));
                    $sheet->setCellValue('D' . $row, stripcslashes($rowgetInfo['email']));
                    $sheet->setCellValue('E' . $row, stripcslashes($rowgetInfo['cmnd']));
                    $sheet->setCellValue('F' . $row, $ponser['ma_id']."-".$ponser['hovaten']);
                    $sheet->setCellValue('G' . $row, $arrayPackeges[$rowgetInfo["packages_id"]]["title"]);
                    $sheet->setCellValue('H' . $row, $arrayPackeges[$rowgetInfo["packages_id"]]["price"]);
                    $sheet->setCellValue('I' . $row, stripcslashes($rowgetInfo['tenchutaikhoan']));
                    $sheet->setCellValue('J' . $row, stripcslashes($rowgetInfo['sotaikhoan']));
                    $sheet->setCellValue('K' . $row, stripcslashes($rowgetInfo['nganhang']));
                    $sheet->setCellValue('L' . $row, $arrayWithdrawcircle[$rowgetInfo['withdrawcircle_id']]["title"]);
                    $sheet->setCellValue('M' . $row, date("d-m-Y",$rowgetInfo['datecreated']));
            		$row++;
                    */
                    $arrayMember = array();
                    $arrayMemberlist =  $dbf->getMemberListArray($member_id,$rowgetInfo,$arrayMember);

                    $arrayMemberCurrent = $dbf->array_sort_by_column($arrayMemberlist,"datecreated");

                         if($from_date == $from_to)
                         {
                             foreach($arrayMemberCurrent as $value)
                             {
                                    //echo date("d-m-Y",$value["datecreated"])." = " . date("d-m-Y",$from_date);
                                    //echo "<br>";
                                    if(date("d-m-Y",$value["datecreated"]) == date("d-m-Y",$from_date))
                                    {
                                        $ponser = $dbf->getInfoColum("member", $value['parentid']);
                                        $sheet->setCellValue('A' . $row, $row-1);
                                        $sheet->setCellValue('B' . $row, stripcslashes($value['ma_id']));
                                        $sheet->setCellValue('C' . $row, stripcslashes($value['hovaten']));
                                        $sheet->setCellValue('D' . $row, stripcslashes($value['email']));
                                        $sheet->setCellValue('E' . $row, stripcslashes($value['cmnd']));
                                        $sheet->setCellValue('F' . $row, $ponser['ma_id']."-".$ponser['hovaten']);
                                        $sheet->setCellValue('G' . $row, $arrayPackeges[$value["packages_id"]]["title"]);
                                        $sheet->setCellValue('H' . $row, $arrayPackeges[$value["packages_id"]]["price"]);
                                        $sheet->setCellValue('I' . $row, stripcslashes($value['tenchutaikhoan']));
                                        $sheet->setCellValue('J' . $row, stripcslashes($value['sotaikhoan']));
                                        $sheet->setCellValue('K' . $row, stripcslashes($value['nganhang']));
                                        $sheet->setCellValue('L' . $row, $arrayWithdrawcircle[$value['withdrawcircle_id']]["title"]);
                                        $sheet->setCellValue('M' . $row, date("d-m-Y",$value['datecreated']));
                                		$row++;
                                    }
                              }
                              //die();
                         }else
                         {
                             foreach($arrayMemberCurrent as $value)
                             {
                                $datecreated = date("d-m-Y",$value["datecreated"]);
                                $datecreated = strtotime($datecreated);
                                if(($datecreated >= $from_date) && ($datecreated <= $from_to))
                                {
                                    $ponser = $dbf->getInfoColum("member", $value['parentid']);
                                    $sheet->setCellValue('A' . $row, $row-1);
                                    $sheet->setCellValue('B' . $row, stripcslashes($value['ma_id']));
                                    $sheet->setCellValue('C' . $row, stripcslashes($value['hovaten']));
                                    $sheet->setCellValue('D' . $row, stripcslashes($value['email']));
                                    $sheet->setCellValue('E' . $row, stripcslashes($value['cmnd']));
                                    $sheet->setCellValue('F' . $row, $ponser['ma_id']."-".$ponser['hovaten']);
                                    $sheet->setCellValue('G' . $row, $arrayPackeges[$value["packages_id"]]["title"]);
                                    $sheet->setCellValue('H' . $row, $arrayPackeges[$value["packages_id"]]["price"]);
                                    $sheet->setCellValue('I' . $row, stripcslashes($value['tenchutaikhoan']));
                                    $sheet->setCellValue('J' . $row, stripcslashes($value['sotaikhoan']));
                                    $sheet->setCellValue('K' . $row, stripcslashes($value['nganhang']));
                                    $sheet->setCellValue('L' . $row, $arrayWithdrawcircle[$value['withdrawcircle_id']]["title"]);
                                    $sheet->setCellValue('M' . $row, date("d-m-Y",$value['datecreated']));
                            		$row++;
                                }
                              }
                            }


            // Rename worksheet
            $objPHPExcel->getActiveSheet()->setTitle('Member List Register ');

            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $objPHPExcel->setActiveSheetIndex(0);

            // Redirect output to a client’s web browser (Excel2007)
            $file_name = 'MemberList_' . date('YmdHis') .'.xls';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$file_name.'"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
         }
    }
?>
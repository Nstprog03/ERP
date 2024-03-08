<?php

session_start();

include('../db.php');



require_once "../PHPLibraries/PHPSpreadSheet/vendor/autoload.php";

 

use PhpOffice\PhpSpreadsheet\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Style\Border;

use PhpOffice\PhpSpreadsheet\Style\Color;



	$data = $_SESSION['daily_payment_export_data'];



	$spreadsheet = new Spreadsheet();

	$Excel_writer = new Xlsx($spreadsheet);



	$spreadsheet->setActiveSheetIndex(0);

	$activeSheet = $spreadsheet->getActiveSheet();





	$columnArr=array();





	

	//check availble column

	$columnArr=['Sr. No.','Date','Ref/Inv/Deliver No.','Firm','External Party / Broker','External Party / Broker Address','Amount','Remark'];





	//generate excel header from avilable column

	$alphas = range('A', 'Z');



	foreach ($columnArr as $key => $columnHeader) {



		

		$activeSheet->setCellValue($alphas[$key].'1',$columnHeader);

		

		$activeSheet->getColumnDimension($alphas[$key])->setAutoSize(true);

		$activeSheet->getStyle($alphas[$key])->getAlignment()->setHorizontal('center');

		$activeSheet->getStyle($alphas[$key].'1')->getFont()->setBold( true );

	}





		$i=2;

		$sr_no=0;

		foreach ($data as $row) 

		{

			$k=0;

			$sr_no+=1;



			$activeSheet->setCellValue($alphas[$k++].$i,$sr_no);



			$activeSheet->setCellValue($alphas[$k++].$i,$row['date']);



			$activeSheet->setCellValue($alphas[$k++].$i,$row['invoice_no']);



			$activeSheet->setCellValue($alphas[$k++].$i,$row['firm']);



			$activeSheet->setCellValue($alphas[$k++].$i,$row['party_name']);



			$activeSheet->setCellValue($alphas[$k++].$i,$row['party_address']);



			$activeSheet->setCellValue($alphas[$k++].$i,$row['amount']);



			$activeSheet->setCellValue($alphas[$k++].$i,$row['remark']);



			

			$i++;



		}





$filename = "daily_payment_report_".date('d_m_Y') . ".xlsx";

header('Content-Disposition: attachment;filename='. $filename);

header('Cache-Control: max-age=0');

$Excel_writer->save('php://output');

exit;

?>	
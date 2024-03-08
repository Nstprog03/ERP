<?php

session_start();

include('../db.php');



require_once "../PHPLibraries/PHPSpreadSheet/vendor/autoload.php";

 

use PhpOffice\PhpSpreadsheet\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Style\Border;

use PhpOffice\PhpSpreadsheet\Style\Color;



	$data = $_SESSION['purchase_register_export_data'];



	$spreadsheet = new Spreadsheet();

	$Excel_writer = new Xlsx($spreadsheet);



	$spreadsheet->setActiveSheetIndex(0);

	$activeSheet = $spreadsheet->getActiveSheet();





	$columnArr=array();





	

	//check availble column

	$columnArr=['Sr. No.','Firm Name','External Party','Report Date','Invoice No','Total Amount','Weight','No. Of Bales','Lot No.','Start PR','End PR','Broker','Transport','Transport Vehicle No.','Candy Rate','Ad-Hoc Amount','Out Standing Amount','Total Paid Payment'];





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



			$activeSheet->setCellValue($alphas[$k++].$i,$row['firm']);



			$activeSheet->setCellValue($alphas[$k++].$i,$row['party_name']);



			$activeSheet->setCellValue($alphas[$k++].$i,$row['report_date']);



			$activeSheet->setCellValue($alphas[$k++].$i,$row['invoice_no']);



			$activeSheet->setCellValue($alphas[$k++].$i,$row['total_amount']);



			$activeSheet->setCellValue($alphas[$k++].$i,$row['weight']);



			$activeSheet->setCellValue($alphas[$k++].$i,$row['no_of_bales']);



			$activeSheet->setCellValue($alphas[$k++].$i,$row['lot_no']);



			$activeSheet->setCellValue($alphas[$k++].$i,$row['start_pr']);



			$activeSheet->setCellValue($alphas[$k++].$i,$row['end_pr']);



			$activeSheet->setCellValue($alphas[$k++].$i,$row['broker']);



			$activeSheet->setCellValue($alphas[$k++].$i,$row['transport']);



			$activeSheet->setCellValue($alphas[$k++].$i,$row['trans_veh_no']);



			$activeSheet->setCellValue($alphas[$k++].$i,$row['candy_rate']);



			$activeSheet->setCellValue($alphas[$k++].$i,$row['ad_hoc']);



			$activeSheet->setCellValue($alphas[$k++].$i,$row['out_standing_amt']);



			$activeSheet->setCellValue($alphas[$k++].$i,$row['total_paid_amt']);







			

			$i++;



		}





$filename = "purchase_register".date('d_m_Y') . ".xlsx";

header('Content-Disposition: attachment;filename='. $filename);

header('Cache-Control: max-age=0');

$Excel_writer->save('php://output');

exit;

?>	
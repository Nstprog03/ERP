<?php

session_start();

include('../db.php');



require_once "../PHPLibraries/PHPSpreadSheet/vendor/autoload.php";

 

use PhpOffice\PhpSpreadsheet\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Style\Border;

use PhpOffice\PhpSpreadsheet\Style\Color;



	$data = $_SESSION['rd_kapas_export_data'];

	

/*	echo '<pre>';

	print_r($data);

	exit;*/



	$spreadsheet = new Spreadsheet();

	$Excel_writer = new Xlsx($spreadsheet);



	$spreadsheet->setActiveSheetIndex(0);

	$activeSheet = $spreadsheet->getActiveSheet();









	$activeSheet->mergeCells("A2:B2");

    $activeSheet->setCellValue('A2', 'RD KAPAS REGISTER');

    $activeSheet->getStyle('A2')->getFont()->setBold( true );











	$columnArr=array();



	//check availble column

	$columnArr=['FIRM NAME','PARTY NAME','BROKER','BILL DATE','INVOICE NO','BASIC AMO','TAX AMOUNT','TCS AMO','TOTAL AMO','DEBIT NOTE AMO','TDS AMO','PAYMENT DATE','PAYMENT AMO','OUT STANDING'];



	



	//generate excel header from avilable column

	$alphas = range('A', 'Z');



	foreach ($columnArr as $key => $columnHeader) 

	{

		

		$activeSheet->setCellValue($alphas[$key].'4',$columnHeader);

		$activeSheet->getColumnDimension($alphas[$key])->setAutoSize(true);

		$activeSheet->getStyle($alphas[$key])->getAlignment()->setHorizontal('center');

		$activeSheet->getStyle($alphas[$key].'4')->getFont()->setBold( true );

	}





		$i=5;

		$k=0;

		foreach ($data as $row) 

		{

		

			

			$activeSheet->setCellValue('A'.$i,$row['firm']);

			$activeSheet->setCellValue('B'.$i,$row['ex_party']);

			$activeSheet->setCellValue('C'.$i,$row['broker']);

			$activeSheet->setCellValue('D'.$i,$row['bill_date']);

			$activeSheet->setCellValue('E'.$i,$row['invoice_no']);

			$activeSheet->setCellValue('F'.$i,$row['basic_amt']);

			$activeSheet->setCellValue('G'.$i,$row['tax_amt']);

			$activeSheet->setCellValue('H'.$i,$row['tcs_amt']);

			$activeSheet->setCellValue('I'.$i,$row['total_amt']);

			$activeSheet->setCellValue('J'.$i,$row['debit_amt']);

			$activeSheet->setCellValue('K'.$i,$row['tds_amt']);

			$activeSheet->setCellValue('L'.$i,$row['pay_date']);

			$activeSheet->setCellValue('M'.$i,$row['pay_amt']);

			$activeSheet->setCellValue('N'.$i,$row['outstanding_amt']);

			

			

			$i++;



		}





$filename = "rd_kapas_report_".date('d_m_Y') . ".xlsx";

header('Content-Disposition: attachment;filename='. $filename);

header('Cache-Control: max-age=0');

$Excel_writer->save('php://output');

exit;

?>	
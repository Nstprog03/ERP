<?php

session_start();

include('../db.php');



require_once "../PHPLibraries/PHPSpreadSheet/vendor/autoload.php";

 

use PhpOffice\PhpSpreadsheet\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Style\Border;

use PhpOffice\PhpSpreadsheet\Style\Color;





//dd/mm/yyy

function convertDate($date)

{

  $final_date='';

  if($date!='' && $date!='0000-00-00')

  {

    $final_date = str_replace('-', '/', $date);

    $final_date = date('d/m/Y', strtotime($final_date));

  }





    return $final_date;



}



$data = $_SESSION['kapasiya_register_export_data'];





	



	$spreadsheet = new Spreadsheet();

	$Excel_writer = new Xlsx($spreadsheet);



	$spreadsheet->setActiveSheetIndex(0);

	$activeSheet = $spreadsheet->getActiveSheet();





	$columnArr=array();





	

	//check availble column

	$columnArr=['Sr. No.','Firm Name','External Party','Broker','Sales Date','Invoice No','Weight','Final Amount','Payment Status'];





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



			$activeSheet->setCellValue($alphas[$k++].$i,$row['ext_party']);



			$activeSheet->setCellValue($alphas[$k++].$i,$row['broker']);



			$activeSheet->setCellValue($alphas[$k++].$i,convertDate($row['sales_date']));



			$activeSheet->setCellValue($alphas[$k++].$i,$row['invoice_no']);





			$activeSheet->setCellValue($alphas[$k++].$i,$row['weight']);



			$activeSheet->setCellValue($alphas[$k++].$i,$row['final_amount']);



			$activeSheet->setCellValue($alphas[$k++].$i,$row['pay_status']);

			

			$i++;



		}





$filename = "kapasiya_sales_register_".date('d_m_Y') . ".xlsx";

header('Content-Disposition: attachment;filename='. $filename);

header('Cache-Control: max-age=0');

$Excel_writer->save('php://output');

exit;

?>	
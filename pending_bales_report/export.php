<?php
session_start();
include('../db.php');
require_once "../PHPLibraries/PHPSpreadSheet/vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
	$data = $_SESSION['pending_bales_report_export_data'];
/*	echo '<pre>';
	print_r($data);
	exit;*/
	$spreadsheet = new Spreadsheet();
	$Excel_writer = new Xlsx($spreadsheet);
	$spreadsheet->setActiveSheetIndex(0);
	$activeSheet = $spreadsheet->getActiveSheet();
	$columnArr=array();
	//check availble column
	$columnArr=['Index No.','External Party','Sales Confirmation No','Sales Confirmation Date','Firm Name','Candy Rate','Variety','Total Bales','Sales Bales','Pending Bales','Sub Variety'];
	//generate excel header from avilable column
	$alphas = range('A', 'Z');
	foreach ($columnArr as $key => $columnHeader) 
	{
		$activeSheet->setCellValue($alphas[$key].'1',$columnHeader);
		$activeSheet->getColumnDimension($alphas[$key])->setAutoSize(true);
		$activeSheet->getStyle($alphas[$key])->getAlignment()->setHorizontal('center');
		$activeSheet->getStyle($alphas[$key].'1')->getFont()->setBold( true );
	}
		$i=2;
		$k=0;
		foreach ($data as $row) 
		{
			$activeSheet->setCellValue('A'.$i,$row['index_no'])
						->setCellValue('B'.$i,$row['conf_ext_party'])
						->setCellValue('C'.$i,$row['conf_no'])
						->setCellValue('D'.$i,$row['conf_date'])
						->setCellValue('E'.$i,$row['firm'])
						->setCellValue('F'.$i,$row['candy_rate'])
						->setCellValue('G'.$i,$row['variety'])
						->setCellValue('H'.$i,$row['total_bales'])
						->setCellValue('I'.$i,$row['sales_bales'])
						->setCellValue('J'.$i,$row['pending_bales'])						
						->setCellValue('K'.$i,$row['sub_variety']);
			$i++;
		}
$filename = "pending_bales_report_".date('d_m_Y') . ".xlsx";
header('Content-Disposition: attachment;filename='. $filename);
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;
?>	
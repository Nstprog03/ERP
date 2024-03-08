<?php
session_start();
include('../db.php');
require_once "../PHPLibraries/PHPSpreadSheet/vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

$data = $_SESSION['sales_register_export_data'];
$spreadsheet = new Spreadsheet();
$Excel_writer = new Xlsx($spreadsheet);
$spreadsheet->setActiveSheetIndex(0);
$activeSheet = $spreadsheet->getActiveSheet();
$column_data = $_SESSION['sales_register_column_data'];
$columnArr=array();

	//check availble column
$columnArr[]='Sr. No.';
if(isset($column_data['col_invoice_date'])){
	$columnArr[]='Invoice Date';
}
if(isset($column_data['col_invoice_no'])){
	$columnArr[]='Invoice No';
}
if(isset($column_data['col_firm'])){
	$columnArr[]='Firm';
}
if(isset($column_data['col_external_party'])){
	$columnArr[]='External Party';
}
if(isset($column_data['col_shipping_party'])){
	$columnArr[]='Shipping Party';
}
if(isset($column_data['col_delivery_city'])){
	$columnArr[]='Delivery City';
}
if(isset($column_data['col_variety'])){
	$columnArr[]='Variety';
}
if(isset($column_data['col_sub_variety'])){
	$columnArr[]='Sub Variety';
}
if(isset($column_data['col_truck_veh_no'])){
	$columnArr[]='Truck/Vehicle No.';
}
if(isset($column_data['col_lot_no'])){
	$columnArr[]='LOT No.';
}
if(isset($column_data['col_lot_bales'])){
	$columnArr[]='Lot Bales';
}
if(isset($column_data['col_pr_no_start'])){
	$columnArr[]='PR. No. Start';
}
if(isset($column_data['col_pr_no_end'])){
	$columnArr[]='PR. No. End';
}
if(isset($column_data['col_candy_rate'])){
	$columnArr[]='Candy Rate';
}
if(isset($column_data['col_total_amount'])){
	$columnArr[]='Total Amount';
}
if(isset($column_data['col_length'])){
	$columnArr[]='Length';
}
if(isset($column_data['col_strength'])){
	$columnArr[]='Strength';
}
if(isset($column_data['col_mic'])){
	$columnArr[]='Mic';
}
if(isset($column_data['col_trash'])){
	$columnArr[]='Trash';
}
if(isset($column_data['col_mois'])){
	$columnArr[]='Moisture';
}
if(isset($column_data['col_rd'])){
	$columnArr[]='RD';
}
		//generate excel header from avilable column
$alphas = range('A', 'Z');
foreach ($columnArr as $key => $columnHeader) {
	$activeSheet->setCellValue($alphas[$key].'1',$columnHeader);
		//$activeSheet->getStyle($alphas[$key])->getAlignment()->setWrapText(true); 
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
	if(isset($column_data['col_invoice_date'])){
		$activeSheet->setCellValue($alphas[$k++].$i,$row['invoice_date']);
	}
	if(isset($column_data['col_invoice_no'])){
		$activeSheet->setCellValue($alphas[$k++].$i,$row['invoice_no']);
	}
	if(isset($column_data['col_firm'])){
		$activeSheet->setCellValue($alphas[$k++].$i,$row['firm']);
	}
	if(isset($column_data['col_external_party'])){
		$activeSheet->setCellValue($alphas[$k++].$i,$row['external_party']);
	}
	if(isset($column_data['col_shipping_party'])){
		$activeSheet->setCellValue($alphas[$k++].$i,$row['shipping_party']);
	}
	if(isset($column_data['col_delivery_city'])){
		$activeSheet->setCellValue($alphas[$k++].$i,$row['delivery_city']);
	}
	if(isset($column_data['col_variety'])){
		$activeSheet->setCellValue($alphas[$k++].$i,$row['variety']);
	}
	if(isset($column_data['col_sub_variety'])){
		$activeSheet->setCellValue($alphas[$k++].$i,$row['sub_variety']);
	}
	if(isset($column_data['col_truck_veh_no'])){
		$activeSheet->setCellValue($alphas[$k++].$i,$row['truck_no']);
	}
	if(isset($column_data['col_lot_no'])){
		$activeSheet->setCellValue($alphas[$k++].$i,$row['lot_no']);
	}
	if(isset($column_data['col_lot_bales'])){
		$activeSheet->setCellValue($alphas[$k++].$i,$row['lot_bales']);
	}
	if(isset($column_data['col_pr_no_start'])){
		$activeSheet->setCellValue($alphas[$k++].$i,$row['start_pr']);
	}
	if(isset($column_data['col_pr_no_end'])){
		$activeSheet->setCellValue($alphas[$k++].$i,$row['end_pr']);
	}
	if(isset($column_data['col_candy_rate'])){
		$activeSheet->setCellValue($alphas[$k++].$i,$row['candy_rate']);
	}
	if(isset($column_data['col_total_amount'])){
		$activeSheet->setCellValue($alphas[$k++].$i,$row['total_amount']);
	}
	if(isset($column_data['col_length'])){
		$activeSheet->setCellValue($alphas[$k++].$i,$row['length']);
	}
	if(isset($column_data['col_strength'])){
		$activeSheet->setCellValue($alphas[$k++].$i,$row['strength']);
	}
	if(isset($column_data['col_mic'])){
		$activeSheet->setCellValue($alphas[$k++].$i,$row['mic']);
	}
	if(isset($column_data['col_trash'])){
		$activeSheet->setCellValue($alphas[$k++].$i,$row['trash']);
	}
	if(isset($column_data['col_mois'])){
		$activeSheet->setCellValue($alphas[$k++].$i,$row['moi']);
	}
	if(isset($column_data['col_rd'])){
		$activeSheet->setCellValue($alphas[$k++].$i,$row['rd']);
	}	
	$i++;
}
$filename = "sales_register_".date('d_m_Y') . ".xlsx";
header('Content-Disposition: attachment;filename='. $filename);
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;
?>	
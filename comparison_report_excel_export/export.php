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


	$data = $_SESSION['comparison_report_export_data'];

    


        $spreadsheet = new Spreadsheet();
        $Excel_writer = new Xlsx($spreadsheet);
 
        $spreadsheet->setActiveSheetIndex(0);
        $activeSheet = $spreadsheet->getActiveSheet();

       

       //check availble column
        $columnArr=['Sr. No.','Sales Party','Sales Invoice No.','Sales Invoice Date','Sales Vehicle No','Sales LOT No.','Sales LOT Bales','Own Bales','Use of External Bales','External Party','Delivery At','Invoice Raise in the Name'];


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

    		
      $sr_no+=1;

      $use_external_bales='';
      if(isset($row['use_external_bales']))
      {
        $use_external_bales=$row['use_external_bales'];
      }

      $pur_ext_party='';
      if(isset($row['pur_ext_party']))
      {
        $pur_ext_party=$row['pur_ext_party'];
      }


             

			 $activeSheet->setCellValue('A'.$i,$sr_no);

			 $activeSheet->setCellValue('B'.$i,$row['sales_party']);
			 $activeSheet->setCellValue('C'.$i,$row['sales_invoice_no']);

       $date=convertDate($row['sales_invoice_date']);
       $activeSheet->setCellValue('D'.$i,$date);

			 $activeSheet->setCellValue('E'.$i,$row['sales_veh_no']);
       $activeSheet->setCellValue('F'.$i,$row['sales_lot_no']);
			 $activeSheet->setCellValue('G'.$i,$row['sales_lot_bales']);
			 $activeSheet->setCellValue('H'.$i,$row['own_bales']);
			 $activeSheet->setCellValue('I'.$i,$use_external_bales);
			 $activeSheet->setCellValue('J'.$i,$pur_ext_party);
			 $activeSheet->setCellValue('K'.$i,$row['delivery_at']);
			 $activeSheet->setCellValue('L'.$i,$row['invoice_raise_name']);

			

			$i++;

		}


$filename = "comparison_report_".date('d_m_Y') . ".xlsx";
header('Content-Disposition: attachment;filename='. $filename);
header('Cache-Control: max-age=0');
$Excel_writer->save('php://output');
exit;
?>	
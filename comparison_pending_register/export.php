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





	$data = $_SESSION['comparison_pending_register_export_data'];



    





        $spreadsheet = new Spreadsheet();

        $Excel_writer = new Xlsx($spreadsheet);

 

        $spreadsheet->setActiveSheetIndex(0);

        $activeSheet = $spreadsheet->getActiveSheet();



       



       //check availble column

        $columnArr=['Sr. No.','Firm','External Party & Conf. No.','Invoice No.','Lot No.','Total Bales','Used Bales','Available Bales'];





        //generate excel header from avilable column

        $alphas = range('A', 'Z');



        foreach ($columnArr as $key => $columnHeader) {



            

            $activeSheet->setCellValue($alphas[$key].'1',$columnHeader);

            

            $activeSheet->getColumnDimension($alphas[$key])->setAutoSize(true);

            $activeSheet->getStyle($alphas[$key])->getAlignment()->setHorizontal('left');

            $activeSheet->getStyle($alphas[$key].'1')->getFont()->setBold( true );

        }







		$i=2;

		$sr_no=0;

		foreach ($data as $row) 

		{

      $sr_no+=1;  



      $total_bales=(int)$row['bales'];

      $used_bales=(int)$row['total_dispatch_bales'];

      $avl_bales=$total_bales-$used_bales;

     



			 $activeSheet->setCellValue('A'.$i,$sr_no)

			             ->setCellValue('B'.$i,$row['firm_name'])

			             ->setCellValue('C'.$i,$row['ext_conf_no'])

                   ->setCellValue('D'.$i,$row['invoice_no'])

			             ->setCellValue('E'.$i,$row['lot_no'])

                   ->setCellValue('F'.$i,$total_bales)

			             ->setCellValue('G'.$i,$used_bales)

			             ->setCellValue('H'.$i,$avl_bales);



			



			$i++;



		}





$filename = "comparison_pending_register_".date('d_m_Y') . ".xlsx";

header('Content-Disposition: attachment;filename='. $filename);

header('Cache-Control: max-age=0');

$Excel_writer->save('php://output');

exit;

?>	
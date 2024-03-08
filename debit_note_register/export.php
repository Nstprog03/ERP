<?php

session_start();

include('../db.php');



require_once "../PHPLibraries/PHPSpreadSheet/vendor/autoload.php";

 

use PhpOffice\PhpSpreadsheet\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Style\Border;

use PhpOffice\PhpSpreadsheet\Style\Color;



	$data = $_SESSION['debit_note_register_export_data'];







        $spreadsheet = new Spreadsheet();

        $Excel_writer = new Xlsx($spreadsheet);

 

        $spreadsheet->setActiveSheetIndex(0);

        $activeSheet = $spreadsheet->getActiveSheet();



        $activeSheet->getStyle('A:BA')->getAlignment()->setHorizontal('center');



        foreach(range('A','BA') as $columnID) 

        {

            $activeSheet->getColumnDimension($columnID)->setAutoSize(true);



        }



       $activeSheet

        ->getStyle('A1:BA2')

        ->getFill()

        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)

        ->getStartColor()

        ->setARGB('5ee660');



        $activeSheet->getStyle('A1:BA1')->getFont()->setBold( true );









        $activeSheet->mergeCells("A1:A2");

        $activeSheet->setCellValue('A1', 'Sr. No.');



        $activeSheet->mergeCells("B1:B2");

        $activeSheet->setCellValue('B1', "Firm");



        $activeSheet->mergeCells("C1:C2");

        $activeSheet->setCellValue('C1', "External Party");



         $activeSheet->mergeCells("D1:D2");

        $activeSheet->setCellValue('D1', "Broker");



        $activeSheet->mergeCells("E1:E2");

        $activeSheet->setCellValue('E1', "Debit\nReort Date");



        $activeSheet->mergeCells("F1:F2");

        $activeSheet->setCellValue('F1', "Bill Date");



        $activeSheet->mergeCells("G1:G2");

        $activeSheet->setCellValue('G1', "Invoice No");



        $activeSheet->mergeCells("H1:H2");

        $activeSheet->setCellValue('H1', "Total Amount");



        $activeSheet->mergeCells("I1:I2");

        $activeSheet->setCellValue('I1', "Weight");



        $activeSheet->mergeCells("J1:J2");

        $activeSheet->setCellValue('J1', "No. Of Bales");



        $activeSheet->mergeCells("K1:K2");

        $activeSheet->setCellValue('K1', "Lot No.");





        $activeSheet->mergeCells("L1:L2");

        $activeSheet->setCellValue('L1', "Start PR");



        $activeSheet->mergeCells("M1:M2");

        $activeSheet->setCellValue('M1', "End PR");







        /*RD*/

         $activeSheet->mergeCells("N1:R1");

         $activeSheet->setCellValue('N1', "RD");



        $activeSheet->setCellValue('N2', "Condition");

        $activeSheet->setCellValue('O2', "Lab");

        $activeSheet->setCellValue('P2', "Difference");

        $activeSheet->setCellValue('Q2', "Deduction");

        $activeSheet->setCellValue('R2', "Amount");







        /*Length*/

         $activeSheet->mergeCells("S1:W1");

         $activeSheet->setCellValue('S1', "Length");





        $activeSheet->setCellValue('S2', "Condition");

        $activeSheet->setCellValue('T2', "Lab");

        $activeSheet->setCellValue('U2', "Difference");

        $activeSheet->setCellValue('V2', "Deduction");

        $activeSheet->setCellValue('W2', "Amount");





         /*MIC*/

         $activeSheet->mergeCells("X1:AB1");

         $activeSheet->setCellValue('X1', "MIC");



        $activeSheet->setCellValue('X2', "Condition");

        $activeSheet->setCellValue('Y2', "Lab");

        $activeSheet->setCellValue('Z2', "Difference");

        $activeSheet->setCellValue('AA2', "Deduction");

        $activeSheet->setCellValue('AB2', "Amount");





        /*Trash*/

         $activeSheet->mergeCells("AC1:AF1");

         $activeSheet->setCellValue('AC1', "Trash");



        $activeSheet->setCellValue('AC2', "Condition");

        $activeSheet->setCellValue('AD2', "Lab");

        $activeSheet->setCellValue('AE2', "Difference");

        $activeSheet->setCellValue('AF2', "Amount");





        /*Moisture*/

         $activeSheet->mergeCells("AG1:AJ1");

         $activeSheet->setCellValue('AG1', "Moisture");



        $activeSheet->setCellValue('AG2', "Condition");

        $activeSheet->setCellValue('AH2', "Lab");

        $activeSheet->setCellValue('AI2', "Difference");

        $activeSheet->setCellValue('AJ2', "Amount");





         /*Sample*/

         $activeSheet->mergeCells("AK1:AL1");

         $activeSheet->setCellValue('AK1', "Sample");



        $activeSheet->setCellValue('AK2', "KG");

        $activeSheet->setCellValue('AL2', "Amount");

       



         /*Tare*/

         $activeSheet->mergeCells("AM1:AN1");

         $activeSheet->setCellValue('AM1', "Tare");



        $activeSheet->setCellValue('AM2', "KG");

        $activeSheet->setCellValue('AN2', "Amount");





         /*Brokerage*/

         $activeSheet->mergeCells("AO1:AP1");

         $activeSheet->setCellValue('AO1', "Brokerage");



        $activeSheet->setCellValue('AO2', "Per Bales");

        $activeSheet->setCellValue('AP2', "Amount");



         /*Weight Shortage*/

         $activeSheet->mergeCells("AQ1:AR1");

          $activeSheet->setCellValue('AQ1', "Weight Shortage");

        $activeSheet->setCellValue('AQ2', "Shortage");

        $activeSheet->setCellValue('AR2', "Shortage Amount");





         /*Interest*/

         $activeSheet->mergeCells("AS1:AT1");

         $activeSheet->setCellValue('AS1', "Interest");



        $activeSheet->setCellValue('AS2', "Days");

        $activeSheet->setCellValue('AT2', "Amount");





         /*RePressing*/

         $activeSheet->mergeCells("AU1:AV1");

         $activeSheet->setCellValue('AU1', "Rate Difference");



        $activeSheet->setCellValue('AU2', "Deduct");

        $activeSheet->setCellValue('AV2', "Amount");





         /*RePressing*/

         $activeSheet->mergeCells("AW1:AX1");

         $activeSheet->setCellValue('AW1', "RePressing");



        $activeSheet->setCellValue('AW2', "Deduct");

        $activeSheet->setCellValue('AX2', "Amount");





         /*Other*/

         $activeSheet->mergeCells("AY1:AZ1");

         $activeSheet->setCellValue('AY1', "Other");



        $activeSheet->setCellValue('AY2', "Reason");

        $activeSheet->setCellValue('AZ2', "Amount");





        $activeSheet->mergeCells("BA1:BA2");

        $activeSheet->setCellValue('BA1', "Total Debit Amount");

       





        

        $activeSheet->getStyle('A:BA')->getAlignment()->setWrapText(true); 







		$i=3;

		$sr_no=0;

		foreach ($data as $row) 

		{





			 $styleArrayTabel = array(

    'alignment' => array(

            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,

            'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,

             'rotation'   => 0,

             'wrap'       => true

    ),

    'borders' => array(

        'allBorders' => array(

              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, //BORDER_THIN BORDER_MEDIUM BORDER_HAIR

              'color' => array('rgb' => '000000')

        )

      )

    );

$activeSheet->getStyle('A1:BA'.$i)->applyFromArray($styleArrayTabel);











			$k=0;

			$sr_no+=1;



			 $activeSheet->setCellValue('A'.$i,$sr_no);

			 $activeSheet->setCellValue('B'.$i,$row['firm']);

			 $activeSheet->setCellValue('C'.$i,$row['party_name']);

             $activeSheet->setCellValue('D'.$i,$row['broker']);

			 $activeSheet->setCellValue('E'.$i,$row['debit_date']);

             $activeSheet->setCellValue('F'.$i,$row['bill_date']);

			 $activeSheet->setCellValue('G'.$i,$row['invoice_no']);

			 $activeSheet->setCellValue('H'.$i,$row['total_amount']);

			 $activeSheet->setCellValue('I'.$i,$row['weight']);

			 $activeSheet->setCellValue('J'.$i,$row['no_of_bales']);

			 $activeSheet->setCellValue('K'.$i,$row['lot_no']);

			 $activeSheet->setCellValue('L'.$i,$row['start_pr']);

			 $activeSheet->setCellValue('M'.$i,$row['end_pr']);





			 $activeSheet->setCellValue('N'.$i,$row['rd_con']);

			 $activeSheet->setCellValue('O'.$i,$row['rd_lab']);

			 $activeSheet->setCellValue('P'.$i,$row['rd_diff']);

			 $activeSheet->setCellValue('Q'.$i,$row['rd_cndy']);

			 $activeSheet->setCellValue('R'.$i,$row['rd_amt']);



			 $activeSheet->setCellValue('S'.$i,$row['len_con']);

			 $activeSheet->setCellValue('T'.$i,$row['len_lab']);

			 $activeSheet->setCellValue('U'.$i,$row['len_diff']);

			 $activeSheet->setCellValue('V'.$i,$row['len_cndy']);

			 $activeSheet->setCellValue('W'.$i,$row['len_amt']);





			 $activeSheet->setCellValue('X'.$i,$row['mic_con']);

			 $activeSheet->setCellValue('Y'.$i,$row['mic_lab']);

			 $activeSheet->setCellValue('Z'.$i,$row['mic_diff']);

			 $activeSheet->setCellValue('AA'.$i,$row['mic_cndy']);

			 $activeSheet->setCellValue('AB'.$i,$row['mic_amt']);





			 $activeSheet->setCellValue('AC'.$i,$row['trs_con']);

			 $activeSheet->setCellValue('AD'.$i,$row['trs_lab']);

			 $activeSheet->setCellValue('AE'.$i,$row['trs_diff']);

			 $activeSheet->setCellValue('AF'.$i,$row['trs_amt']);



			 $activeSheet->setCellValue('AG'.$i,$row['mois_con']);

			 $activeSheet->setCellValue('AH'.$i,$row['mois_lab']);

			 $activeSheet->setCellValue('AI'.$i,$row['mois_diff']);

			 $activeSheet->setCellValue('AJ'.$i,$row['mois_amt']);



			 $activeSheet->setCellValue('AK'.$i,$row['sample_kg']);

			 $activeSheet->setCellValue('AL'.$i,$row['sample_amt']);



			 $activeSheet->setCellValue('AM'.$i,$row['tare_kg']);

			 $activeSheet->setCellValue('AN'.$i,$row['tare_amt']);



			 $activeSheet->setCellValue('AO'.$i,$row['brok_per_bales']);

			 $activeSheet->setCellValue('AP'.$i,$row['brok_amt']);



            $activeSheet->setCellValue('AQ'.$i,$row['shortage']);

            $activeSheet->setCellValue('AR'.$i,$row['shortage_amt']);



			 $activeSheet->setCellValue('AS'.$i,$row['int_days']);

			 $activeSheet->setCellValue('AT'.$i,$row['interest']);



			 $activeSheet->setCellValue('AU'.$i,$row['rate_diff_candy']);

			 $activeSheet->setCellValue('AV'.$i,$row['rate_diff_amount']);



        



			 $activeSheet->setCellValue('AW'.$i,$row['repress_per_bales']);

			 $activeSheet->setCellValue('AX'.$i,$row['repress_total']);





			 $activeSheet->setCellValue('AY'.$i,$row['other_reason']);

			 $activeSheet->setCellValue('AZ'.$i,$row['other_amount']);





			 $activeSheet->setCellValue('BA'.$i,$row['total_debit_amount']);







			

			$i++;



		}





$filename = "debit_note_register".date('d_m_Y') . ".xlsx";

header('Content-Disposition: attachment;filename='. $filename);

header('Cache-Control: max-age=0');

$Excel_writer->save('php://output');

exit;

?>	
<?php

session_start();

ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

ini_set('memory_limit', '-1');

ini_set("max_execution_time", 0);

include('../db.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){

  header("location: ../login.php");

  exit;

}

// Get Firm Name
function GetFirmName($id = 0){
    include('../db.php');

    $firm_sql = "SELECT * FROM party WHERE id = ".$id;
    $firm_result = mysqli_query($conn,$firm_sql);
    if(mysqli_num_rows($firm_result) > 0){
        $firm_row = mysqli_fetch_assoc($firm_result);
        $firm_name = $firm_row['party_name'];
    }else{
        $firm_name = "";
    }
    return $firm_name;
}

// Get Firm Name
function GetPartyName($id = 0){
    include('../db.php');

    $prt_sql = "SELECT * FROM external_party WHERE id = ".$id;
    $prt_result = mysqli_query($conn,$prt_sql);
    if(mysqli_num_rows($prt_result) > 0){
        $prt_row = mysqli_fetch_assoc($prt_result);
        $prt_name = $prt_row['partyname'];
    }else{
        $prt_name = "";
    }

    return $prt_name;
}

function GetBrokerName($id = 0){
    include('../db.php');

    $prt_sql = "SELECT * FROM broker WHERE id = ".$id;
    $prt_result = mysqli_query($conn,$prt_sql);
    if(mysqli_num_rows($prt_result) > 0){
        $prt_row = mysqli_fetch_assoc($prt_result);
        $prt_name = $prt_row['name'];
    }else{
        $prt_name = "";
    }

    return $prt_name; 
}

function GetTransportName($id = 0){
    include('../db.php');
    $trans_sql = "SELECT * FROM transport WHERE id = ".$id;
    $trans_result = mysqli_query($conn,$trans_sql);
    if(mysqli_num_rows($trans_result) > 0){
        $trans_row = mysqli_fetch_assoc($trans_result);
        $trans_name = $trans_row['trans_name'];
    }else{
        $trans_name = "";
    }

    return $trans_name; 
}

// Change date formet
function ChangeDateFormat($date = "0000-00-00"){
    if($date != "" && $date != "0000-00-00"){
        $date = explode("-",$date);

        $date = $date[2]."/".$date[1]."/".$date[0];
    }else{
        $date = "";
    }
   
    return $date;
}

require_once "../PHPLibraries/PHPSpreadSheet/vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Spreadsheet;

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Style\Border;

use PhpOffice\PhpSpreadsheet\Style\Color;





if(isset($_POST['submit']))
{

    $main_query="select br.*,ep.partyname from bank_receipt br LEFT JOIN external_party ep ON ep.id = br.party ";
    $where_cond = array();

    if(isset($_POST['submit']))
    {
        if(isset($_POST['table']))
        {
            $where_cond[] = " br.table_indicator = '".$_POST['table']."' ";
        }

        if(isset($_POST['bank']))
        {
            $bank = implode("','",$_POST['bank']);
            $where_cond[] = " br.bank IN ('".$bank."') ";
        }

        if(isset($_POST['start_date']) && $_POST['start_date'] != "" && isset($_POST['end_date']) && $_POST['end_date'] != ""){
            $start_date = str_replace("/","-",$_POST['start_date']);
            $end_date = str_replace("/","-",$_POST['end_date']);
            $start_date = date("Y-m-d",strtotime($start_date));
            $end_date = date("Y-m-d", strtotime($end_date)); 
        }
        
        if(isset($start_date) && isset($end_date))
        {
            $where_cond[] = " DATE(br.date) >= '$start_date'";
            $where_cond[] = " DATE(br.date) <= '$end_date'";
        }

        $data = array();
        if(isset($where_cond)){
            $i = 0;
            if(!empty($where_cond)){
                $where = implode(' AND',$where_cond);
                $main_query = $main_query." where".$where." AND br.firm ='".$_SESSION['bank_transaction_firm_id']."' AND br.financial_year ='".$_SESSION['bank_transaction_financial_year_id']."' ORDER BY br.date ASC , br.id ASC";
            }else{
                $main_query = $main_query." where br.firm ='".$_SESSION['bank_transaction_firm_id']."' AND br.financial_year ='".$_SESSION['bank_transaction_financial_year_id']."' ORDER BY br.date ASC , br.id ASC";
            }
            

            $result = mysqli_query($conn, $main_query);
            if(mysqli_num_rows($result) > 0){
                while($row = mysqli_fetch_assoc($result)){
                    $data[] = $row;
                }
            }
        }
    }
    
    $data = array_unique($data,SORT_REGULAR);
    
	$spreadsheet = new Spreadsheet();

	$Excel_writer = new Xlsx($spreadsheet);


	$spreadsheet->setActiveSheetIndex(0);

	$activeSheet = $spreadsheet->getActiveSheet();

    $activeSheet->getStyle('A1:Z1')->getFont()->setBold( true );

    $activeSheet->getStyle('A:Z')->getAlignment()->setWrapText(true); 

	//check availble column
    if(isset($_POST['table']) && $_POST['table'] == "Sales Recievable"){
        $columnArr=['Sr No.','Date','External Party','Amount','Invoice No. & Payment','Remark'];
    }else{
        $columnArr=['Sr No.','Date','External Party','Amount','Remark'];
    }

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

        $k = 1;

        if(isset($_POST['table']) && $_POST['table'] == "Sales Recievable"){
            foreach ($data as $row) 
            {
                $invoiceData = array();
                $invoice_sql = "SELECT * FROM bank_receipt_payment_data WHERE bank_receipt_id = '".$row['id']."'";
                $invoice_result = mysqli_query($conn,$invoice_sql);
                if(mysqli_num_rows($invoice_result) > 0 ){
                    while($invoice_row = mysqli_fetch_assoc($invoice_result)){
                        $invoiceData[] = $invoice_row;
                    }
                }
                
                    if(count($invoiceData) > 0){
                        $end = ($i + count($invoiceData)) - 1;
                    }else{
                        $end = $i;
                    }
                    $spreadsheet->getActiveSheet()->mergeCells('A'.$i.':A'.$end.'');
                    $spreadsheet->getActiveSheet()->mergeCells('B'.$i.':B'.$end.'');
                    $spreadsheet->getActiveSheet()->mergeCells('C'.$i.':C'.$end.'');  
                    $spreadsheet->getActiveSheet()->mergeCells('D'.$i.':D'.$end.''); 
                    $spreadsheet->getActiveSheet()->mergeCells('F'.$i.':F'.$end.''); 
    
                    $activeSheet->getStyle('A'.$i.'')->getAlignment()->setVertical('center');
                    $activeSheet->getStyle('B'.$i.'')->getAlignment()->setVertical('center');
                    $activeSheet->getStyle('C'.$i.'')->getAlignment()->setVertical('center');
                    $activeSheet->getStyle('D'.$i.'')->getAlignment()->setVertical('center');
                    $activeSheet->getStyle('F'.$i.'')->getAlignment()->setVertical('center');
    
                    $activeSheet->setCellValue('A'.$i,$k)
    
                                ->setCellValue('B'.$i,ChangeDateFormat($row['date']))
    
                                ->setCellValue('C'.$i,$row['partyname'])
    
                                ->setCellValue('D'.$i,$row['total_payment'])
    
                                ->setCellValue('F'.$i,$row['remark']);
                    $j = 1; 
                    foreach($invoiceData as $key => $iData){
                        $invNumber = "";
                        if($iData['invoice_no'] != "Select Option"){
                            $invNumber .= $iData['invoice_no']." - ";
                        }
                        
                        $invNumber .= $iData['payment'];
                        $activeSheet->setCellValue('E'.$i,$invNumber);
                        // echo $j ." - ". count($invoiceData);
                        // echo "<br>";
                        if($j == count($invoiceData)){
                        }else{
                            $i++;
                        }
                        $j++;
                    }      
                    
                $i++;
                $k++; 
            }
        }
        else{
            foreach ($data as $row) 
            {
                $activeSheet->setCellValue('A'.$i,$k)

                            ->setCellValue('B'.$i,ChangeDateFormat($row['date']))

                            ->setCellValue('C'.$i,$row['partyname'])

                            ->setCellValue('D'.$i,$row['total_payment'])

                            ->setCellValue('E'.$i,$row['remark']);
                $i++;
                $k++;
            }
        }

$filename = "Bank_Receipt_".date('d_m_Y') . ".xlsx";

header('Content-Disposition: attachment;filename='. $filename);

header('Cache-Control: max-age=0');

$Excel_writer->save('php://output');

exit;

}

?>
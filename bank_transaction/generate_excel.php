<?php

session_start();

// ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);

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
    $main_query="select bt.* from bank_transaction bt ";
    $where_cond = array();

    if(isset($_POST['submit']))
    {
        if(isset($_POST['table']))
        {
            $where_cond[] = " bt.table_indicator = '".$_POST['table']."' ";
        }

        if(isset($_POST['bank']))
        {
            $bank = implode("','",$_POST['bank']);
            $where_cond[] = " bt.bank IN ('".$bank."') ";
        }

        if(isset($_POST['start_date']) && $_POST['start_date'] != "" && isset($_POST['end_date']) && $_POST['end_date'] != ""){
            $start_date = str_replace("/","-",$_POST['start_date']);
            $end_date = str_replace("/","-",$_POST['end_date']);
            $start_date = date("Y-m-d",strtotime($start_date));
            $end_date = date("Y-m-d", strtotime($end_date)); 
        }
        
        if(isset($start_date) && isset($end_date))
        {
            $where_cond[] = " DATE(bt.date) >= '$start_date'";
            $where_cond[] = " DATE(bt.date) <= '$end_date'";
        }

        $data = array();
        if(isset($where_cond)){
            $i = 0;
            if(!empty($where_cond)){
                $where = implode(' AND',$where_cond);
                $main_query = $main_query." where".$where." AND bt.firm ='".$_SESSION['bank_transaction_firm_id']."' AND bt.financial_year ='".$_SESSION['bank_transaction_financial_year_id']."' ORDER BY bt.date ASC , bt.id ASC";
            }else{
                $main_query = $main_query." where bt.firm ='".$_SESSION['bank_transaction_firm_id']."' AND bt.financial_year ='".$_SESSION['bank_transaction_financial_year_id']."' ORDER BY bt.date ASC , bt.id ASC";
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
    if(isset($_POST['table']) && $_POST['table'] == "Debit Note Ad-Hoc" || $_POST['table'] == "Bales Payout" || $_POST['table'] == "RD Kapas purchase Payment"){
        $columnArr=['Sr No.','Date','External Party','Amount','Invoice No. & Payment','Remark'];
    }elseif(isset($_POST['table']) && $_POST['table'] == "Transport Payout"){
        $columnArr=['Sr No.','Date','Transport Party','Amount','Invoice No. & Payment','Remark'];
    }elseif(isset($_POST['table']) && $_POST['table'] == "URD Kapas purchase Payment"){
        $columnArr=['Sr No.','Date','Farmer Name And Village','Quantity','Rate','Amount','Remark'];
    }elseif(isset($_POST['table']) && $_POST['table'] == "Other Payout"){
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

        if(isset($_POST['table']) && $_POST['table'] == "Debit Note Ad-Hoc" || $_POST['table'] == "Bales Payout" || $_POST['table'] == "Transport Payout" || $_POST['table'] == "RD Kapas purchase Payment"){
            foreach ($data as $row) 
            {
                if(isset($row['ext_party']) && $row['ext_party'] != '0'){
                    if($_POST['table'] == "Transport Payout"){
                        $party_name = GetTransportName($row['ext_party']);
                    }else{
                        $party_name = GetPartyName($row['ext_party']);
                    }
                }else{
                    $party_name = "";
                }
                $invoiceData = array();
                $invoice_sql = "SELECT * FROM bank_transaction_history WHERE bank_transaction_id = '".$row['id']."'";
                $invoice_result = mysqli_query($conn,$invoice_sql);
                if(mysqli_num_rows($invoice_result) > 0 ){
                    while($invoice_row = mysqli_fetch_assoc($invoice_result)){
                        $invoiceData[] = $invoice_row;
                    }
                }
                if(count($invoiceData) > 1){
                    $end = ($i+count($invoiceData)) - 1;
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
                }
                
                $activeSheet->setCellValue('A'.$i,$k)

                            ->setCellValue('B'.$i,ChangeDateFormat($row['date']))

                            ->setCellValue('C'.$i,$party_name)

                            ->setCellValue('D'.$i,number_format($row['total_payment'],2))

                            ->setCellValue('F'.$i,$row['remark']);
                $j = 1; 
                foreach($invoiceData as $key => $iData){
                    $inno = "";
                    if($iData['invoice_no'] != 'undefined'){
                        $inno .= $iData['invoice_no']." - ";
                    }
                    
                    $inno .= $iData['payment'];
                    $activeSheet->setCellValue('E'.$i,$inno);
                    if($j == count($invoiceData)){
                    }else{
                        $i++;
                    }
                    $j++;
                }      
                $i++;
                $k++;
            }
        }elseif(isset($_POST['table']) && $_POST['table'] == "URD Kapas purchase Payment"){
            foreach ($data as $row) 
            {
                $invoiceData = array();
                $invoice_sql = "SELECT bth.*,frm.vlg_name as village FROM bank_transaction_history bth LEFT JOIN farmer frm ON frm.id = bth.invoice_id WHERE  bth.bank_transaction_id = '".$row['id']."'";
                $invoice_result = mysqli_query($conn,$invoice_sql);
                if(mysqli_num_rows($invoice_result) > 0 ){
                    while($invoice_row = mysqli_fetch_assoc($invoice_result)){
                        $invoiceData[] = $invoice_row;
                    }
                }
                if(count($invoiceData) > 1){
                    $end = ($i+count($invoiceData)) - 1;
                    $spreadsheet->getActiveSheet()->mergeCells('A'.$i.':A'.$end.'');
                    $spreadsheet->getActiveSheet()->mergeCells('B'.$i.':B'.$end.'');
                    $spreadsheet->getActiveSheet()->mergeCells('D'.$i.':D'.$end.'');  
                    $spreadsheet->getActiveSheet()->mergeCells('E'.$i.':E'.$end.''); 
                    $spreadsheet->getActiveSheet()->mergeCells('F'.$i.':F'.$end.''); 
                    $spreadsheet->getActiveSheet()->mergeCells('G'.$i.':G'.$end.''); 

                    $activeSheet->getStyle('A'.$i.'')->getAlignment()->setVertical('center');
                    $activeSheet->getStyle('B'.$i.'')->getAlignment()->setVertical('center');
                    $activeSheet->getStyle('D'.$i.'')->getAlignment()->setVertical('center');
                    $activeSheet->getStyle('E'.$i.'')->getAlignment()->setVertical('center');
                    $activeSheet->getStyle('F'.$i.'')->getAlignment()->setVertical('center');
                    $activeSheet->getStyle('G'.$i.'')->getAlignment()->setVertical('center');
                }
                
                $activeSheet->setCellValue('A'.$i,$k)

                            ->setCellValue('B'.$i,ChangeDateFormat($row['date']))

                            ->setCellValue('D'.$i,$row['quantity'])

                            ->setCellValue('E'.$i,$row['rate'])

                            ->setCellValue('F'.$i, number_format($row['total_payment'],2))

                            ->setCellValue('G'.$i,$row['remark']);
                $j = 1; 
                foreach($invoiceData as $key => $iData){
                    $activeSheet->setCellValue('C'.$i,($iData['invoice_no']." (".$iData['village'].")"));
                    if($j == count($invoiceData)){
                    }else{
                        $i++;
                    }
                    $j++;
                }      
                $i++;
                $k++;
            }
        }elseif(isset($_POST['table']) && $_POST['table'] == "Other Payout"){
            foreach ($data as $row) 
            {
                $party_name = "";
                if($row['pay_to'] == "0"){
                    $party_name = GetPartyName($row['ext_party']);
                }elseif($row['pay_to'] == "1"){
                    $party_name = GetBrokerName($row['ext_party']);
                }else{
                    $party_name = "";
                }

                $activeSheet->setCellValue('A'.$i,$k)

                            ->setCellValue('B'.$i,ChangeDateFormat($row['date']))

                            ->setCellValue('C'.$i,$party_name)

                            ->setCellValue('D'.$i,number_format($row['total_payment'],2))

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
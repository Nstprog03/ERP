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

    $main_query="select bb.* from bank_balance bb ";
    $where_cond = array();

    if(isset($_POST['submit']))
    {


        $join = "";
        if(isset($_POST['table']))
        {
            $table = implode("','",$_POST['table']);
            $join = " LEFT JOIN bank_transaction bt ON bt.id = bb.bank_peyout LEFT JOIN bank_receipt br ON br.id = bb.bank_receipt ";
            $where_cond[] = " (br.table_indicator IN ('".$table."') OR bt.table_indicator IN ('".$table."')) ";
        }

        if(isset($_POST['party']))
        {
            $party = implode(",",$_POST['party']);
            $join = " LEFT JOIN bank_transaction bt ON bt.id = bb.bank_peyout LEFT JOIN bank_receipt br ON br.id = bb.bank_receipt ";
            $where_cond[] = " (br.party IN (".$party.") OR bt.ext_party IN (".$party.")) ";
        }

        if(isset($_POST['start_date']) && $_POST['start_date'] != "" && isset($_POST['end_date']) && $_POST['end_date'] != ""){
            $start_date = str_replace("/","-",$_POST['start_date']);
            $end_date = str_replace("/","-",$_POST['end_date']);
            $start_date = date("Y-m-d",strtotime($start_date));
            $end_date = date("Y-m-d", strtotime($end_date)); 
        }
        
        if(isset($start_date) && isset($end_date))
        {
            $where_cond[] = " DATE(bb.date) >= '$start_date'";
            $where_cond[] = " DATE(bb.date) <= '$end_date'";
        }

        $data = array();
        if(isset($where_cond)){
            $i = 0;
            if(!empty($where_cond)){
                $where = implode(' AND',$where_cond);
                $main_query = $main_query.$join." where".$where." AND bb.firm ='".$_SESSION['bank_transaction_firm_id']."' AND bb.financial_year ='".$_SESSION['bank_transaction_financial_year_id']."' AND bb.bank = '".$_SESSION['bank']."' ORDER BY bb.date ASC , bb.id ASC";
            }else{
                $main_query = $main_query.$join." where bb.firm ='".$_SESSION['bank_transaction_firm_id']."' AND bb.financial_year ='".$_SESSION['bank_transaction_financial_year_id']."' AND bb.bank = '".$_SESSION['bank']."' ORDER BY bb.date ASC , bb.id ASC";
            }
            

            $result = mysqli_query($conn, $main_query);
            if(mysqli_num_rows($result) > 0){
                while($row = mysqli_fetch_assoc($result)){

                        if(isset($row['bank_peyout']) && $row['bank_peyout'] != 0 && $row['bank_peyout'] != null && $row['bank_peyout'] != ""){
                            $sql_payout = "SELECT * FROM bank_transaction WHERE id = ".$row['bank_peyout'];
                            $payout_result = mysqli_query($conn,$sql_payout);
                            if(mysqli_num_rows($payout_result) > 0){
                                while($payment_row = mysqli_fetch_assoc($payout_result)){
                                    if(isset($payment_row['ext_party']) && $payment_row['ext_party'] != "" && $payment_row['ext_party'] != null){
                                        $party = $payment_row['ext_party'];
                                    }else{
                                        $party = 0;
                                    }

                                    if($payment_row['table_indicator'] == "Transport Payout"){
                                        $party = GetTransportName($party);
                                    }else{
                                        if($payment_row['pay_to'] == "0"){
                                            $party = GetPartyName($party);
                                        }else{
                                            $party = GetBrokerName($party);
                                        }
                                    }
                                    $data[$i]['date'] = $row['date'];
                                    $data[$i]['table'] = $payment_row['table_indicator'];
                                    $data[$i]['ext_party'] = $party;
                                    $data[$i]['sales_payment'] = "";
                                    $data[$i]['pur_payment'] = $payment_row['total_payment'];
                                    $data[$i]['closing'] = $row['total_balance'];
                                    $i++;
                                }
                            }
                        }
                   
                    if(isset($row['bank_receipt']) && $row['bank_receipt'] != 0 && $row['bank_receipt'] != null && $row['bank_receipt'] != ""){
                        $sales_sql = "SELECT * FROM bank_receipt WHERE id = ".$row['bank_receipt'];
                        $sales_result = mysqli_query($conn,$sales_sql);
                        if(mysqli_num_rows($sales_result) > 0){
                            $sales_row = mysqli_fetch_assoc($sales_result);
                            $data[$i]['date'] = $row['date'];
                            $data[$i]['table'] = $sales_row['table_indicator'];
                            $data[$i]['ext_party'] = GetPartyName($sales_row['party']);
                            $data[$i]['sales_payment'] = $sales_row['total_payment'];
                            $data[$i]['pur_payment'] = "";
                            $data[$i]['closing'] = $row['total_balance'];
                            $i++;
                        }
                    }

                    if(isset($row['bank_receipt']) && isset($row['bank_peyout']) && $row['bank_receipt'] == 0 && $row['bank_peyout'] == 0){
                        $data[$i]['date'] = $row['date'];
                        $data[$i]['table'] = "";
                        $data[$i]['ext_party'] = "";
                        $data[$i]['sales_payment'] = $row['balance'];
                        $data[$i]['pur_payment'] = "";
                        $data[$i]['closing'] = $row['total_balance'];
                        $i++;
                    }
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

	$columnArr=['Sr No.','Date','Table','Party Name','Recevable','Payment','Closing'];

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

		foreach ($data as $row) 
		{
			$activeSheet->setCellValue('A'.$i,$k)

						->setCellValue('B'.$i,ChangeDateFormat($row['date']))

						->setCellValue('C'.$i,$row['table'])

						->setCellValue('D'.$i,$row['ext_party'])

                        ->setCellValue('E'.$i,$row['sales_payment'])

                        ->setCellValue('F'.$i,$row['pur_payment'])
                        
                        ->setCellValue('G'.$i,$row['closing']);
			$i++;
            $k++;
		}



$filename = "Bank_Balance_".date('d_m_Y') . ".xlsx";

header('Content-Disposition: attachment;filename='. $filename);

header('Cache-Control: max-age=0');

$Excel_writer->save('php://output');

exit;

}

?>
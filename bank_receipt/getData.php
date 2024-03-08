<?php
 session_start();
include('../db.php');

function ChangeDateFormat($date){
    $date = explode("/",$date);

    $date = $date[2]."-".$date[1]."-".$date[0];
    return $date;
}

if(isset($_POST['Checkpayment']) && $_POST['Checkpayment'] == true){

    $data = array();
    $data['status'] = "0";

    $total_payment = $_POST['total_payment'];
    if(isset($_POST['invoicepayment']) && $_POST['invoicepayment'] != ""){
        $invoicepayment = $_POST['invoicepayment'];
    }else{
        $invoicepayment = "0";
    }
    

    if(isset($_POST['payment'])){
        $payment = $_POST['payment'];
        $total_invoice_pay = array_sum($payment);
    }else{
        $total_invoice_pay = "0";
    }

    if(isset($invoicepayment) && isset($total_invoice_pay)){
        $total = $invoicepayment + $total_invoice_pay;
    }else{
        $total = 0;
    }

    if(isset($total_payment) && isset($total) && $total_payment >= $total){
        $data['status'] = "1";
    }else{
        $data['status'] = "0";
    }
    echo json_encode($data);
 }

if(isset($_POST['getBalance']) && isset($_POST['bank'])){
    $firm =$_SESSION["bank_transaction_firm_id"];
    $f_year = $_SESSION["bank_transaction_financial_year_id"];

    if(isset($_POST['bank']) && $_POST['bank'] != ""){
        $bank = $_POST['bank'];
    }else{
        $bank = "";
    }

    if(isset($_POST['date']) && $_POST['date'] != ""){
        $date = str_replace("/","-",$_POST['date']);
        $date = date("Y-m-d",strtotime($date));
    }else{
        $date = "0000-00-00";
    }

    $data = array();
    $sql = "SELECT * FROM bank_balance WHERE date <= '".$date."' AND `bank` = '".$bank."' AND firm = '".$firm."' AND financial_year = '".$f_year."' ORDER BY `bank_balance`.`date` DESC , `bank_balance`.`id` DESC";  

    // $sql = "SELECT * FROM bank_balance WHERE `bank` = '".$bank."' AND firm = '".$firm."' AND financial_year = '".$f_year."' ORDER BY `bank_balance`.`id` DESC";
    $result = mysqli_query($conn,$sql);
    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        $data['balance'] = $row['total_balance'];
    }else{
        $data['balance'] = "0";
    }

    echo json_encode($data);
}

if(isset($_POST['getParty']) && isset($_POST['table']))
{
    $firm =$_SESSION["bank_transaction_firm_id"];
    $f_year = $_SESSION["bank_transaction_financial_year_id"];
    $table = $_POST['table'];

    $ExrPrtArr = array();
    $data = array();

    if(isset($table) && $table == "Sales Recievable"){
        $sql = "SELECT * FROM `sales_report` WHERE `firm` = '".$firm."' AND `financial_year_id` = '". $f_year."'";
        $result = mysqli_query($conn,$sql);
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_assoc($result)){
                $ExrPrtArr[] = $row['party_name'];
            }
        }
       
    }elseif(isset($table) && $table == "Kapasiya Sales" || $table == "Other"){
        $sql="select * from external_party";
        $result = mysqli_query($conn, $sql);
        foreach ($conn->query($sql) as $key => $row) 
        {
            $ExrPrtArr[] = $row['id'];
        }
    }

    $ExtParty =  array_unique($ExrPrtArr); 

   

    $sql = "select * from external_party";
    $result = mysqli_query($conn, $sql);
    foreach ($conn->query($sql) as $result) 
    {
        if(in_array($result['id'],$ExtParty)){
            $data[] = $result['id']."/".$result['partyname'];
        } 
    }

    echo json_encode($data);
}

//get Invoice data
if(isset($_POST['getInvoiceNo']) && isset($_POST['ext_prt_id']) && isset($_POST['table']))
{
    $firm =$_SESSION["bank_transaction_firm_id"];
    $f_year = $_SESSION["bank_transaction_financial_year_id"];
    $ext_prt_id = $_POST['ext_prt_id'];
    $table = $_POST['table'];

    $use_invoice = array();
    $banksql = "SELECT * FROM `bank_receipt_payment_data` ";
    $bank_result = mysqli_query($conn,$banksql);
    if(mysqli_num_rows($bank_result) > 0){
        while($bank_row = mysqli_fetch_assoc($bank_result)){
            $use_invoice[] =  $bank_row['invoice_id'];
        }
    }

    $data = array();

    if(isset($table) && $table == "Sales Recievable"){
        $sql = "SELECT * FROM `sales_report` WHERE `firm` = '".$firm."' AND `financial_year_id` = '".$f_year."' AND `party_name` = '".$ext_prt_id."'";
        $result = mysqli_query($conn,$sql);
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_assoc($result)){
                if(!in_array($row['id'],$use_invoice)){
                    $data[] = $row['id']."/".$row['invice_no'];
                }
            }
        }
    }
    echo json_encode($data);
}

//get Invoice data
if(isset($_POST['getInvoicePayment']) && isset($_POST['invoice']))
{
    $data = array();
    $invoice_id = $_POST['invoice'];
    $sql = "SELECT * FROM `sales_report` WHERE `id` = '".$invoice_id."' ";
    $result = mysqli_query($conn,$sql);
    if(mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        $data['payment'] = $row['total_value'];
        $data['status'] = true;
    }else{
        $data['payment'] = 0;
        $data['status'] = false; 
    }
    echo json_encode($data);
}

?>
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
    exit;
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

    $sql = "SELECT * FROM bank_balance WHERE date <= '".$date."' AND `bank` = '".$bank."' AND firm = '".$firm."' AND financial_year = '".$f_year."' ORDER BY `bank_balance`.`date` ASC";
    

    $result = mysqli_query($conn,$sql);
    if(mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_assoc($result)){
            $data['id'] = $row['id'];
            $data['bankbalance'] = $row['total_balance'];
        }
    }else{
        $data['id'] = "0";
        $data['bankbalance'] = "0";
    }
    echo json_encode($data);
    exit;
}

if(isset($_POST['getParty']) && $_POST['getParty'] == true && isset($_POST['table']))
{
    $table = $_POST['table'];
    $firm =$_SESSION["bank_transaction_firm_id"];
    $f_year = $_SESSION["bank_transaction_financial_year_id"];

    $ExrPrtArr = array();
    $data = array();

    if(isset($table) && $table == "Debit Note Ad-Hoc" || $table == "Bales Payout"){
        $sql = "SELECT * FROM `debit_report` WHERE `firm` = '".$_SESSION["bank_transaction_firm_id"]."' AND `financial_year` = '". $_SESSION["bank_transaction_financial_year_id"]."'";
        $result = mysqli_query($conn,$sql);
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_assoc($result)){
                $ExrPrtArr[] = $row['party'];
            }
        }
    }elseif(isset($table) && $table == "Transport Payout"){
        $sql="select * from pur_report where `firm` = '".$_SESSION["bank_transaction_firm_id"]."' AND `financial_year` = '". $_SESSION["bank_transaction_financial_year_id"]."'";
        $result = mysqli_query($conn, $sql);
        foreach ($conn->query($sql) as $key => $row) 
        {
            $ExrPrtArr[] = $row['trans_id'];
        }
    }elseif(isset($table) && $table == "RD Kapas purchase Payment"){
        $sql="select * from rd_kapas_report where `firm` = '".$_SESSION["bank_transaction_firm_id"]."' AND `financial_year_id` = '". $_SESSION["bank_transaction_financial_year_id"]."'";
        $result = mysqli_query($conn, $sql);
        foreach ($conn->query($sql) as $key => $row) 
        {
            $ExrPrtArr[] = $row['external_party'];
        }
    }

    $ExtParty =  array_unique($ExrPrtArr); 

    if(isset($table) && $table != "Transport Payout"){
        $sql = "select * from external_party";
        $result = mysqli_query($conn, $sql);
        foreach ($conn->query($sql) as $result) 
        {
            if($table != "Other Payout"){
                if(in_array($result['id'],$ExtParty)){
                    $data[] = $result['id']."/".$result['partyname'];
                } 
            }else{
                if(isset($_POST['pay_to']) && $_POST['pay_to'] == '0'){
                    $data[] = $result['id']."/".$result['partyname'];
                }
            }
        }

        if($table == "Other Payout" && isset($_POST['pay_to']) && $_POST['pay_to'] == '1'){
            $sql2 = "select * from broker";
            $result2 = mysqli_query($conn,$sql2);
            if(mysqli_num_rows($result2) > 0){
                foreach($conn->query($sql2) as $row2){
                    $data[] = $row2['id']."/".$row2['name'];
                }
            }
        }

    }else{
        $sql = "select * from transport";
        $result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result) > 0){
            foreach ($conn->query($sql) as $result) 
            {
                if(in_array($result['id'],$ExtParty)){
                    $data[] = $result['id']."/".$result['trans_name'];
                }
            }
        }
        
    }

    echo json_encode($data);
    exit;
}

//get Invoice data
if(isset($_POST['getInvoiceNo']) && isset($_POST['ext_prt_id']) && isset($_POST['table']))
{
    $firm =$_SESSION["bank_transaction_firm_id"];
    $f_year = $_SESSION["bank_transaction_financial_year_id"];
    $ext_prt_id = $_POST['ext_prt_id'];
    $table = $_POST['table'];

    $use_invoice = array();
    $banksql = "SELECT * FROM `bank_transaction` WHERE table_indicator = '".$table."' AND firm = '".$firm."' AND financial_year = '".$f_year."'";
    $bank_result = mysqli_query($conn,$banksql);
    if(mysqli_num_rows($bank_result) > 0){
        while($bank_row = mysqli_fetch_assoc($bank_result)){

            $sub_tran_sql = "SELECT * FROM `bank_transaction_history` WHERE `bank_transaction_id` = ".$bank_row['id'];
            $sub_tran_result = mysqli_query($conn,$sub_tran_sql);
            if(mysqli_num_rows($sub_tran_result) > 0){
                while($val = mysqli_fetch_assoc($sub_tran_result)){
                    $use_invoice[] =  $val['invoice_id'];
                }
            }
        }
    }

    $data = array();

    if(isset($table) && $table == "Debit Note Ad-Hoc" || $table == "Bales Payout"){
        $sql = "SELECT * FROM `debit_report` WHERE `party` ='".$ext_prt_id."' AND firm = '".$firm."' AND financial_year = '".$f_year."' ";
        $result = mysqli_query($conn,$sql);
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_assoc($result)){
                if(!in_array($row['id'],$use_invoice)){
                    $data[] = $row['id']."/".$row['invoice_no'];
                }
            }
        }
    }elseif(isset($table) && $table == "Transport Payout"){
        $sql="select * from pur_report where trans_id='".$ext_prt_id."' AND firm='".$firm."' AND financial_year='".$f_year."'";
        $result = mysqli_query($conn, $sql);
        $Invoice=array();
        foreach ($conn->query($sql) as $key => $row) 
        {
            $sql_trans="SELECT * FROM external_party WHERE id='".$row['party']."'";
            $result_trans = mysqli_query($conn, $sql_trans);
            if(mysqli_num_rows($result_trans)> 0 ){
                $row_trans = $result_trans->fetch_assoc();
                if(!in_array($row['id'],$use_invoice)){
                    $data[] = $row['id']."/".$row['invoice_no']." - ".$row_trans['partyname']." - ".$row['trans_veh_no'];
                }
            }
        }
    }elseif(isset($table) && $table == "RD Kapas purchase Payment"){
        $sql="select * from rd_kapas_report where external_party = '".$ext_prt_id."' AND `firm` = '".$_SESSION["bank_transaction_firm_id"]."' AND `financial_year_id` = '". $_SESSION["bank_transaction_financial_year_id"]."'";
        $result = mysqli_query($conn, $sql);
        foreach ($conn->query($sql) as $key => $row) 
        {
            if(!in_array($row['id'],$use_invoice)){
                $data[] = $row['id']."/".$row['invoice_no'];
            }
        }
    }

    echo json_encode($data);
}

//get Farmer data
if(isset($_POST['GetFarmer']))
{
    $firm =$_SESSION["bank_transaction_firm_id"];
    $f_year = $_SESSION["bank_transaction_financial_year_id"];

    $data = array();

    $Arr = array();
    $farmer = array();
    $bank_sql = "SELECT brh.*,fr.farmer_name FROM bank_transaction_history brh LEFT JOIN bank_transaction br ON br.id = brh.bank_transaction_id LEFT JOIN farmer fr ON fr.id = brh.invoice_id WHERE br.table_indicator = 'URD Kapas purchase Payment' AND br.firm ='".$firm."' AND br.financial_year = '".$f_year."'";;
    $bank_result = mysqli_query($conn,$bank_sql);
    if(mysqli_num_rows($bank_result) > 0){
        while($bank_row = mysqli_fetch_assoc($bank_result)){
            $Arr[] = $bank_row['invoice_id'];
            $farmer[] = $bank_row['farmer_name'];
        }
    }

    $sql = "SELECT * FROM farmer ";
    $result = mysqli_query($conn,$sql);
    if(mysqli_num_rows($result) > 0){
        $data['status'] = true;
        while($row = mysqli_fetch_assoc($result)){
            // if(!in_array($row['id'],$Arr)){
                $data[] = $row;
            // }
        }
    }else{
        $data['status'] = false;
    }
    echo json_encode($data);
}

?>
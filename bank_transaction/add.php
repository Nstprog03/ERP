<?php
session_start();
include('../db.php');
// ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location:../login.php");
    exit;
}

if (isset($_POST['Submit'])) {
    
    $firm_id                     = $_SESSION["bank_transaction_firm_id"];
    $bank_tran_financial_year_id = $_SESSION["bank_transaction_financial_year_id"];
    $username                    = $_SESSION['username'];
    
    $date = explode("/", $_POST['date']);
    $date = $date[2] . "-" . $date[1] . "-" . $date[0];
    
    $bank        = $_POST['bank'];
    $balance_id  = $_POST['bank_balance_id'];
    $bankbalance = $_POST['bankbalance'];
    
    if (isset($_POST['total_payment'])) {
        $total_pay = $_POST['total_payment'];
    } else {
        $total_pay = "0";
    }
    
    if (isset($_POST['ext_party'])) {
        $ext_party = $_POST['ext_party'];
    } else {
        $ext_party = 0;
    }
    
    $table = $_POST['table'];
    if (isset($_POST['pay_to']) && $table == "Other Payout") {
        $pay_to = $_POST['pay_to'];
    } else {
        $pay_to = "0";
    }

    $quantity = 0;
    if(isset($_POST['quantity']) && $table == "URD Kapas purchase Payment"){
        $quantity = $_POST['quantity'];
    }

    $rate = 0;
    if(isset($_POST['rate']) && $table == "URD Kapas purchase Payment"){
        $rate = $_POST['rate'];
    }

    $remark = "";
    if(isset($_POST['remark'])){
        $remark = $_POST['remark'];
    }

    date_default_timezone_set('Asia/Kolkata');
    $timestamp = date("Y-m-d H:i:s");
    
    if (!isset($errorMsg)) {
        
        if (isset($_POST['invoice_id'])) {
            $invoice_id = $_POST['invoice_id'];
        } else {
            $invoice_id = array();
        }
        
        if (isset($_POST['invoice_no'])) {
            $invoice_no = $_POST['invoice_no'];
        } else {
            $invoice_no = array();
        }
        
        if (isset($_POST['payment'])) {
            $payment = $_POST['payment'];
        } else {
            $payment = array();
        }
        
        
        $sql = "insert into bank_transaction(date, bank,bank_balance_id,bankbalance,ext_party,table_indicator,total_payment,pay_to,quantity,rate,remark,firm,financial_year,username,created_at,updated_at)
        values('" . $date . "', '" . $bank . "','" . $balance_id . "','" . $bankbalance . "','" . $ext_party . "','" . $table . "','" . $total_pay . "','" . $pay_to . "','".$quantity."','".$rate."','".$remark."','" . $firm_id . "','" . $bank_tran_financial_year_id . "','" . $username . "','" . $timestamp . "','" . $timestamp . "')";
        if($result  = mysqli_query($conn, $sql)){
            $last_id = $conn->insert_id;

            $sql2  = "SELECT * FROM `bank_transaction` WHERE date >= '" . $date . "' AND `bank` = '" . $bank . "' AND `firm` = '" . $firm_id . "' AND `financial_year` = '" . $bank_tran_financial_year_id . "' ORDER BY  `date` ASC,`id` ASC";
            $result2 = mysqli_query($conn,$sql2);
            if(mysqli_num_rows($result2) > 0){
                while($row2 = mysqli_fetch_assoc($result2)){
                    if($date == $row2['date'] && $last_id >= $row2['id']){
                    }else{
                        $bal = $row2['bankbalance'] - $total_pay;
                        $sql3        = "UPDATE `bank_transaction` SET `bankbalance`='" . $bal . "' WHERE id = '" . $row2['id'] . "'";
                        $result3 = mysqli_query($conn, $sql3);
                    }
                }
            }
        
            if ($table != "Other Payout") {
                if (isset($invoice_id) && count($invoice_id) != 0) {
                    foreach ($invoice_id as $k => $val) {
                        $sql_sub_tran = "insert into bank_transaction_history(bank_transaction_id, invoice_id,invoice_no,payment,created_at,updated_at)
                        values('" . $last_id . "', '" . $val . "','" . $invoice_no[$k] . "','" . $payment[$k] . "','" . $timestamp . "','" . $timestamp . "')";
                        $result       = mysqli_query($conn, $sql_sub_tran);
                    }
                }
            }else{
                $sql_sub_tran = "insert into bank_transaction_history(bank_transaction_id,payment,created_at,updated_at)
                values('" . $last_id . "', '" . $total_pay . "','" . $timestamp . "','" . $timestamp . "')";
                $result       = mysqli_query($conn, $sql_sub_tran);
            }

            $sqlbank     = "SELECT * FROM `bank_balance` WHERE date <= '" . $date . "' AND `bank` = '" . $bank . "' AND `firm` = '" . $firm_id . "' AND `financial_year` = '" . $bank_tran_financial_year_id . "' ORDER BY `bank_balance`.`date` DESC , `bank_balance`.`id` DESC ";
            $result_bank = mysqli_query($conn, $sqlbank);
            if (mysqli_num_rows($result_bank)) {
                $bank_row = mysqli_fetch_assoc($result_bank);
                
                $b_balance = $bank_row['total_balance'];
                
                $bank_payment = $total_pay;
                
                $bank_total_belence = $b_balance - $bank_payment;
                
                $sql_bal    = "SELECT * FROM `bank_balance` WHERE date > '" . $date . "' AND `bank` = '" . $bank . "' AND `firm` = '" . $firm_id . "' AND `financial_year` = '" . $bank_tran_financial_year_id . "' ORDER BY `bank_balance`.`date` ASC";
                $result_bal = mysqli_query($conn, $sql_bal);
                if (mysqli_num_rows($result_bal) > 0) {
                    while ($bal_row = mysqli_fetch_assoc($result_bal)) {
                        $pre_bal       = $bal_row['previous_balance'] - $bank_payment;
                        $clo_bal       = $bal_row['total_balance'] - $bank_payment;
                        $update        = "UPDATE `bank_balance` SET `previous_balance`='" . $pre_bal . "',`total_balance`='" . $clo_bal . "' WHERE id = '" . $bal_row['id'] . "'";
                        $update_result = mysqli_query($conn, $update);
                    }
                }
                
                
                $bank_insert = "insert into bank_balance(date , bank , previous_balance , total_balance , payment , firm , financial_year , bank_peyout , username , created_at,updated_at)
                values('" . $date . "', '" . $bank . "','" . $b_balance . "','" . $bank_total_belence . "','" . $bank_payment . "','" . $firm_id . "','" . $bank_tran_financial_year_id . "','" . $last_id . "','" . $username . "','" . $timestamp . "','" . $timestamp . "')";
                if ($bank_result = mysqli_query($conn, $bank_insert)) {
                    $successMsg = 'New record added successfully';
                    header('Location: home.php');
                } else {
                    $errorMsg = 'Error ' . mysqli_error($conn);
                }
            } else {
                $b_balance          = 0;
                $bank_total_belence = $b_balance - $total_pay;
                
                $sql_bal    = "SELECT * FROM `bank_balance` WHERE date > '" . $date . "' AND `bank` = '" . $bank . "' AND `firm` = '" . $firm_id . "' AND `financial_year` = '" . $bank_tran_financial_year_id . "' ORDER BY `bank_balance`.`date` ASC";
                $result_bal = mysqli_query($conn, $sql_bal);
                if (mysqli_num_rows($result_bal) > 0) {
                    while ($bal_row = mysqli_fetch_assoc($result_bal)) {
                        $pre_bal       = $bal_row['previous_balance'] - $total_pay;
                        $clo_bal       = $bal_row['total_balance'] - $total_pay;
                        $update        = "UPDATE `bank_balance` SET `previous_balance`='" . $pre_bal . "',`total_balance`='" . $clo_bal . "' WHERE id = '" . $bal_row['id'] . "'";
                        $update_result = mysqli_query($conn, $update);
                    }
                }
                
                $bank_insert = "insert into bank_balance(date , bank , previous_balance , total_balance , payment , firm , financial_year , bank_peyout , username , created_at,updated_at)
                values('" . $date . "', '" . $bank . "','" . $b_balance . "','" . $bank_total_belence . "','" . $total_pay . "','" . $firm_id . "','" . $bank_tran_financial_year_id . "','" . $last_id . "','" . $username . "','" . $timestamp . "','" . $timestamp . "')";
                if ($bank_result = mysqli_query($conn, $bank_insert)) {
                    $successMsg = 'New record added successfully';
                    header('Location: home.php');
                } else {
                    $errorMsg = 'Error ' . mysqli_error($conn);
                }
            }
        }
    }
}
?>
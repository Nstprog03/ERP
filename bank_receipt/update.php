<?php

session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location:../login.php");
    exit;
}


if(isset($_POST['Submit'])){
    
    $firm_id = $_SESSION["bank_transaction_firm_id"];
    $financial_year_id = $_SESSION["bank_transaction_financial_year_id"];
    $username=$_SESSION['username'];
    
    $id = $_POST['id'];

    $date =explode("/",$_POST['date']);
    $date = $date[2]."-".$date[1]."-".$date[0];
    
    $bank = $_POST['bank'];
    $balance = $_POST['bankbalance'];
    $ext_party = $_POST['ext_party'];
    $table = $_POST['table'];
    $total_pay = $_POST['total_payment'];
    $remark = "";
    if(isset($_POST['remark'])){
       $remark = $_POST['remark'];
    }


    if($table == "Sales Recievable"){
        $paymentData = array();
        $useinvoice = array();
        if(isset($_POST['invoice_id']) && isset($_POST['invoice_no']) && isset($_POST['payment'])){
            $receipt_id = $_POST['receipt_id'];
            $invoice_id = $_POST['invoice_id'];
            $invoice_no = $_POST['invoice_no'];
            $payment=$_POST['payment'];

            foreach($invoice_id as $k => $val){
                if(isset($receipt_id[$k])){
                    $useinvoice[] = $receipt_id[$k];
                    $paymentData[$k]['receipt_id'] = $receipt_id[$k];
                }else{
                    $paymentData[$k]['receipt_id'] = 0;
                }
                $paymentData[$k]['invoice_id'] = $val;
                $paymentData[$k]['invoice_no'] = $invoice_no[$k];
                $paymentData[$k]['payment'] = $payment[$k];
            }
        }
    } 
    
    date_default_timezone_set('Asia/Kolkata');
    $timestamp = date("Y-m-d H:i:s");

    if(!isset($errorMsg)){

        $select = "SELECT * FROM bank_receipt WHERE id = ".$id;
        $select_result = mysqli_query($conn,$select);
        if(mysqli_num_rows($select_result) > 0) {
            $select_row = mysqli_fetch_assoc($select_result);
            $minus = false;
            $diff = 0;
            $old_pay = $select_row['total_payment'];
            if($old_pay <= $total_pay){
               $diff = $total_pay + $old_pay;
               $minus = true;
            }else{
               $diff = $old_pay + $total_pay;
            }

            $old_payment = $select_row['total_payment'];

            $sql = "update bank_receipt
                set date = '".$date."',
                bank = '".$bank."',
                balance = '".$balance."',
                table_indicator = '".$table."',
                party='".$ext_party."',
                total_payment = '".$total_pay."',
                remark = '".$remark."',
                firm='".$firm_id."',
                financial_year='".$financial_year_id."',
                username='".$username."',
                updated_at='".$timestamp."'
                where id=".$select_row['id'];
            $result = mysqli_query($conn, $sql);

            if($result){

                $sql2  = "SELECT * FROM `bank_receipt` WHERE date >= '" . $date . "' AND `bank` = '" . $bank . "' AND `firm` = '" . $firm_id . "' AND `financial_year` = '" . $financial_year_id . "' ORDER BY  `date` ASC,`id` ASC";
                $result2 = mysqli_query($conn,$sql2);
                if(mysqli_num_rows($result2) > 0){
                   while($row2 = mysqli_fetch_assoc($result2)){
                      if($date == $row2['date'] && $id >= $row2['id']){
                      }else{
                         if($minus == true){
                            $bal = $row2['balance'] + $diff;
                         }else{
                            $bal = $row2['balance'] - $diff;
                         }
                      
                      $sql3        = "UPDATE `bank_receipt` SET `balance`='" . $bal . "' WHERE id = '" . $row2['id'] . "'";
                      $result3 = mysqli_query($conn, $sql3);
                      }
                   }
                }

                $paymentArr = array();
                if($table == "Sales Recievable" && $select_row['table_indicator'] == $table){
                    $pay_select = "SELECT * FROM bank_receipt_payment_data WHERE bank_receipt_id = ".$select_row['id'];
                    $pay_result = mysqli_query($conn,$pay_select);
                    if(mysqli_num_rows($pay_result) > 0){
                        while($pay_row = mysqli_fetch_assoc($pay_result)){
                            $paymentArr[] = $pay_row['id'];
                            if(!in_array($pay_row['id'],$useinvoice)){
                                $delete = "DELETE FROM `bank_receipt_payment_data` WHERE id = ".$pay_row['id'];
                                $delete_result = mysqli_query($conn,$delete);
                            }
                        }
                    }
                }else{
                    $delete = "DELETE FROM `bank_receipt_payment_data` WHERE bank_receipt_id = ".$select_row['id'];
                    $delete_result = mysqli_query($conn,$delete);
                }

                if($table == "Sales Recievable"){
                    foreach($paymentData as $val){
                        if(!in_array($val['receipt_id'],$paymentArr)){
                            $sql = "insert into bank_receipt_payment_data(bank_receipt_id, invoice_id,invoice_no,payment,created_at,updated_at)
                            values('".$select_row['id']."', '".$val['invoice_id']."','".$val['invoice_no']."', '".$val['payment']."','".$timestamp."','".$timestamp."')";
                            $result = mysqli_query($conn, $sql);
                        }
                    }   
                }

                $bank_select = "SELECT * FROM bank_balance WHERE `bank_receipt` = '".$select_row['id']."' AND `bank` = '".$bank."' AND `firm` = '".$firm_id."' AND `financial_year` = '".$financial_year_id."' ORDER BY `bank_balance`.`id` DESC";
                $bank_result = mysqli_query($conn,$bank_select);
                if(mysqli_num_rows($bank_result) > 0){
                    $bank_row = mysqli_fetch_assoc($bank_result);
                    $sql_bal    = "SELECT * FROM `bank_balance` WHERE date >= '" . $bank_row['date'] . "' AND `bank` = '" . $bank_row['bank'] . "' AND `firm` = '" . $bank_row['firm'] . "' AND `financial_year` = '" . $bank_row['financial_year'] . "' ORDER BY  `bank_balance`.`date` ASC,`bank_balance`.`id` ASC";
                    $result_bal = mysqli_query($conn, $sql_bal);
                    if (mysqli_num_rows($result_bal) > 0) {
                        while ($bal_row = mysqli_fetch_assoc($result_bal)) {
                            if($date == $bal_row['date'] && $bank_row['id'] >= $bal_row['id']){
                            }else{
                                if($bank_row['balance'] <=  $total_pay){
                                    $diffra = $total_pay - $bank_row['balance'];
                                    $pre_bal       = $bal_row['previous_balance'] + $diffra;
                                    $clo_bal       = $bal_row['total_balance'] + $diffra;
                                }else{
                                    $diffra = $bank_row['balance'] - $total_pay;
                                    $pre_bal       = $bal_row['previous_balance'] - $diffra;
                                    $clo_bal       = $bal_row['total_balance'] - $diffra;
                                }
                                $update        = "UPDATE `bank_balance` SET `previous_balance`='" . $pre_bal . "',`total_balance`='" . $clo_bal . "' WHERE id = '" . $bal_row['id'] . "'";
                                $update_result = mysqli_query($conn, $update);
                            }
                        }
                    }

                    $avl_bal = $bank_row['total_balance'];
                    $total_balance = $bank_row['total_balance'];
                    $balance = $bank_row['balance'];

                    if($old_payment <= $total_pay){
                        $difference = $total_pay - $old_payment;
                        $avl_balance = $avl_bal + $difference;
                        $total_balance = $total_balance + $difference;
                        $balance = $balance + $difference;
                    }else{
                        $difference = $old_payment - $total_pay;
                        $avl_balance = $avl_bal - $difference;
                        $total_balance = $total_balance - $difference;
                        $balance = $balance - $difference;
                    }

                    $bank_update = "update bank_balance
                    set 
                    balance = '".$balance."',
                    total_balance = '".$total_balance."',
                    available_balance = '".$avl_balance."',
                    username='".$username."',
                    updated_at='".$timestamp."'
                    where id=".$bank_row['id'];

                    $update_result = mysqli_query($conn,$bank_update);
                }
                header('Location: home.php');
            }else{
                $errorMsg = 'Error '.mysqli_error($conn);
            }
        }
    }

}
?>
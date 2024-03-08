<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location:../login.php");
    exit;
}

  if (isset($_POST['Submit'])) {
      
    $firm_id = $_SESSION["bank_transaction_firm_id"];
    $financial_year_id = $_SESSION["bank_transaction_financial_year_id"];
    $username=$_SESSION['username'];

    $date =explode("/",$_POST['date']);
    $date = $date[2]."-".$date[1]."-".$date[0];

    $bank = $_POST['bank'];
    $balance = $_POST['bankbalance'];

    $table = $_POST['table'];
    $ext_party = $_POST['ext_party'];
    $total_pay = $_POST['total_payment'];
    $remark = "";
    if(isset($_POST['remark'])){
      $remark = $_POST['remark'];
    }
    
    if($table == "Sales Recievable"){
      if(isset($_POST['invoice_id']) && isset($_POST['invoice_no']) && isset($_POST['payment'])){
        $invoice_id = $_POST['invoice_id'];
        $invoice_no = $_POST['invoice_no'];
        $payment=$_POST['payment'];
  
        $paymentData = array();
        foreach($invoice_id as $k => $val){
            $paymentData[$k]['invoice_id'] = $val;
            $paymentData[$k]['invoice_no'] = $invoice_no[$k];
            $paymentData[$k]['payment'] = $payment[$k];
        }
      }
    }

    
    date_default_timezone_set('Asia/Kolkata');
    $timestamp = date("Y-m-d H:i:s");

		if(!isset($errorMsg)){
      $sql = "insert into bank_receipt(date, bank,balance,table_indicator,party,total_payment,remark,firm,financial_year,username,created_at,updated_at)
        values('".$date."', '".$bank."','".$balance."', '".$table."','".$ext_party."','".$total_pay."','".$remark."','".$firm_id."','".$financial_year_id."','".$username."','".$timestamp."','".$timestamp."')";
      $result = mysqli_query($conn, $sql);

      if($result){
        $last_id = $conn->insert_id;

        $sql2  = "SELECT * FROM `bank_receipt` WHERE date >= '" . $date . "' AND `bank` = '" . $bank . "' AND `firm` = '" . $firm_id . "' AND `financial_year` = '" . $financial_year_id . "' ORDER BY  `date` ASC,`id` ASC";
        $result2 = mysqli_query($conn,$sql2);
        if(mysqli_num_rows($result2) > 0){
            while($row2 = mysqli_fetch_assoc($result2)){
                if($date == $row2['date'] && $last_id >= $row2['id']){
                }else{
                    $bal = $row2['balance'] + $total_pay;
                    $sql3        = "UPDATE `bank_receipt` SET `balance`='" . $bal . "' WHERE id = '" . $row2['id'] . "'";
                    $result3 = mysqli_query($conn, $sql3);
                }
            }
        }

        if($table == "Sales Recievable"){
          if(isset($paymentData) && !empty($paymentData)){
            foreach($paymentData as $val){
              $sql = "insert into bank_receipt_payment_data(bank_receipt_id, invoice_id,invoice_no,payment,created_at,updated_at)
              values('".$last_id."', '".$val['invoice_id']."','".$val['invoice_no']."', '".$val['payment']."','".$timestamp."','".$timestamp."')";
              $result = mysqli_query($conn, $sql);
            }
          }
        }
        
        $sqlbank = "SELECT * FROM `bank_balance` WHERE date <= '" . $date . "' AND `bank` = '".$bank."' AND `firm` = '".$firm_id."' AND `financial_year` = '".$financial_year_id."' ORDER BY `bank_balance`.`date` DESC ,`bank_balance`.`id` DESC";
        $result = mysqli_query($conn,$sqlbank);
        if(mysqli_num_rows($result) > 0){
          $bank_row = mysqli_fetch_assoc($result);
          
          $avl_balance = $bank_row['total_balance'];
          
          $total_balance = $avl_balance + $total_pay;

          $sql_bal    = "SELECT * FROM `bank_balance` WHERE date >= '" . $date . "' AND `bank` = '" . $bank . "' AND `firm` = '" . $firm_id . "' AND `financial_year` = '" . $financial_year_id . "' ORDER BY `bank_balance`.`date` ASC,`bank_balance`.`id` ASC";
          $result_bal = mysqli_query($conn, $sql_bal);
          if (mysqli_num_rows($result_bal) > 0) {
              while ($bal_row = mysqli_fetch_assoc($result_bal)) {
                if($date == $bal_row['date'] && $bank_row['id'] >= $bal_row['id']){
                }else{
                  $pre_bal       = $bal_row['previous_balance'] + $total_pay;
                  $clo_bal       = $bal_row['total_balance'] + $total_pay;
                  $update        = "UPDATE `bank_balance` SET `previous_balance`='" . $pre_bal . "',`total_balance`='" . $clo_bal . "' WHERE id = '" . $bal_row['id'] . "'";
                  $update_result = mysqli_query($conn, $update);
                }
              }
          }

          $bank_insert = "insert into bank_balance(date, bank, previous_balance,balance,total_balance,available_balance,firm,financial_year,bank_receipt,username,created_at,updated_at)
          values('".$date."', '".$bank."', '".$avl_balance."','".$total_pay."','".$total_balance."','".$total_balance."','".$firm_id."','".$financial_year_id."','".$last_id."','".$username."','".$timestamp."','".$timestamp."')";

          if($bank_result = mysqli_query($conn,$bank_insert)){
            $successMsg = 'New record added successfully';
            header('Location: home.php');
          }else{
            $errorMsg = 'Error '.mysqli_error($conn);
          }
        }else{
            $b_balance          = 0;
            $bank_total_belence = $b_balance + $total_pay;
            
            $sql_bal    = "SELECT * FROM `bank_balance` WHERE date >= '" . $date . "' AND `bank` = '" . $bank . "' AND `firm` = '" . $firm_id . "' AND `financial_year` = '" . $financial_year_id . "' ORDER BY `bank_balance`.`date` ASC,`bank_balance`.`id` ASC";
            $result_bal = mysqli_query($conn, $sql_bal);
            if (mysqli_num_rows($result_bal) > 0) {
                while ($bal_row = mysqli_fetch_assoc($result_bal)) {
                  if($date == $bal_row['date'] && $bank_row['id'] >= $bal_row['id']){
                  }else{
                    $pre_bal       = $bal_row['previous_balance'] + $total_pay;
                    $clo_bal       = $bal_row['total_balance'] + $total_pay;
                    $update        = "UPDATE `bank_balance` SET `previous_balance`='" . $pre_bal . "',`total_balance`='" . $clo_bal . "' WHERE id = '" . $bal_row['id'] . "'";
                    $update_result = mysqli_query($conn, $update);
                  }
                }
            }
                
          $bank_insert = "insert into bank_balance(date, bank, previous_balance,balance,total_balance,available_balance,firm,financial_year,bank_receipt,username,created_at,updated_at)
          values('".$date."', '".$bank."', '".$b_balance."','".$total_pay."','".$bank_total_belence."','".$bank_total_belence."','".$firm_id."','".$financial_year_id."','".$last_id."','".$username."','".$timestamp."','".$timestamp."')";

          if($bank_result = mysqli_query($conn,$bank_insert)){
            $successMsg = 'New record added successfully';
            header('Location: home.php');
          }else{
            $errorMsg = 'Error '.mysqli_error($conn);
          }
        }
      }else{
        $errorMsg = 'Error '.mysqli_error($conn);
      }
		}
  }
?>

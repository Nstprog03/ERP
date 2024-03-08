<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location:../login.php");
    exit;
}

  if (isset($_POST['Submit'])) {

   

    $firm_id = $_SESSION["bank_transaction_firm_id"];
    $bank_tran_financial_year_id = $_SESSION["bank_transaction_financial_year_id"];
    $username=$_SESSION['username'];
    
    $date =explode("/",$_POST['date']);

    $date = $date[2]."-".$date[1]."-".$date[0];
    
    
    $bank = $_POST['bank'];
    $previous_balance = $_POST['previous_balance'];
    $balance = $_POST['balance'];
    $total_balance = $_POST['total_balance'];
    
    date_default_timezone_set('Asia/Kolkata');
    $timestamp = date("Y-m-d H:i:s");
    
		if(!isset($errorMsg)){

      

        $sql = "insert into bank_balance(date, bank, previous_balance,balance,total_balance,available_balance,firm,financial_year,username,created_at,updated_at)
        values('".$date."', '".$bank."', '".$previous_balance."','".$balance."','".$total_balance."','".$total_balance."','".$firm_id."','".$bank_tran_financial_year_id."','".$username."','".$timestamp."','".$timestamp."')";
        $result = mysqli_query($conn, $sql);
        if($result){
          $last_id = $conn->insert_id;
          $sql2  = "SELECT * FROM `bank_balance` WHERE date >= '" . $date . "' AND `bank` = '" . $bank . "' AND `firm` = '" . $firm_id . "' AND `financial_year` = '" . $bank_tran_financial_year_id . "' ORDER BY  `date` ASC,`id` ASC";

          $result2 = mysqli_query($conn,$sql2);
          if(mysqli_num_rows($result2) > 0){
            while($row3 = mysqli_fetch_assoc($result2)){
                if($date == $row3['date'] && $last_id >= $row3['id']){
                }else{
                  $pre_bal = $row3['previous_balance'] + $balance;
                  $total_bal = $row3['total_balance'] + $balance;
                  
                  $sql3        = "UPDATE `bank_balance` SET `previous_balance`='" . $pre_bal . "',`total_balance` = '".$total_bal."' WHERE id = '" . $row3['id'] . "'";
                  $result3 = mysqli_query($conn, $sql3);
                }
            }
          }
          $successMsg = 'New record added successfully';
          header('Location: home.php');
        }else{
          $errorMsg = 'Error '.mysqli_error($conn);
        }
      
		}
  }
?>

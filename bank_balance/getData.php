<?php
 session_start();
include('../db.php');

function ChangeDateFormat($date){
    $date = explode("/",$date);
    $date = $date[2]."-".$date[1]."-".$date[0];
    return $date;
}

function GetBankBalance($date,$bank){
    include('../db.php');

    $firm =$_SESSION["bank_transaction_firm_id"];
    $f_year = $_SESSION["bank_transaction_financial_year_id"];
    
    $BankData = array();
    $bank_sql = "SELECT * FROM `bank_balance` WHERE `bank` ='".$bank."' AND firm = '".$firm."' AND financial_year = '".$f_year."'";
    $bank_result = mysqli_query($conn,$bank_sql);
    if(mysqli_num_rows($bank_result) > 0){
       
        $select = "SELECT * FROM `bank_balance` WHERE `date` = '".$date."' AND `bank` ='".$bank."' AND firm = '".$firm."' AND financial_year = '".$f_year."' ORDER BY `bank_balance`.`id` DESC";
        $result = mysqli_query($conn,$select);
        if(mysqli_num_rows($result) > 0){
            
            $BankData = mysqli_fetch_assoc($result);
            $BankData['status'] = true;
    
            return $BankData;
        }else{
            $date = date('Y-m-d', strtotime('-1 day', strtotime($date)));
            GetBankBalance($date,$bank);
        }
    }else{
        $BankData['status'] = false;
        return  $BankData;
    }
}

//get lot data
if(isset($_POST['getPreviousBalance']) && isset($_POST['date']) && isset($_POST['bank']))
{
    $firm =$_SESSION["bank_transaction_firm_id"];
    $f_year = $_SESSION["bank_transaction_financial_year_id"];
    $balance_id = $_POST['bank_id'];
    $data = array();
    $data['PreviousBalance'] = "0";
    $date = ChangeDateFormat($_POST['date']);

    $bank = $_POST['bank'];

    $bank_sql = "SELECT * FROM `bank_balance` WHERE `bank` ='".$bank."' AND firm = '".$firm."' AND financial_year = '".$f_year."'";
    $bank_result = mysqli_query($conn,$bank_sql);
    if(mysqli_num_rows($bank_result) > 0){
        $select = "SELECT * FROM `bank_balance` WHERE `date` <= '".$date."' AND `bank` ='".$bank."' AND firm = '".$firm."' AND financial_year = '".$f_year."' ORDER BY `bank_balance`.`date` DESC,`bank_balance`.`id` DESC";
        $result = mysqli_query($conn,$select);
        if(mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_assoc($result);
            $data['PreviousBalance'] = $row['total_balance'];
            $data['status'] = true;
        }else{
            $data['PreviousBalance'] = "0";
            $data['status'] = false;
        }
    }
    
    echo json_encode($data);
}
?>
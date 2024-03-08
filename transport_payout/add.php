<?php
session_start();
include('../db.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
if(!isset($_SESSION['pur_firm_id']) && !isset($_SESSION['pur_financial_year_id']))
{
  header('Location: ../purchase_index.php');
}

  if (isset($_POST['submit'])) {



  	$pur_report_id = $_POST['pur_report_id'];
  	$firm_id= $_SESSION["pur_firm_id"];
  	$financial_year_id=$_SESSION['pur_financial_year_id'];
  	$trans_id = $_POST['trans_id'];
  	$ext_party_id = $_POST['ext_party_id'];
  	$invoice_no = $_POST['invoice_no'];
  	$trans_veh_no = $_POST['trans_veh_no'];


  	 $trans_lr_date = '';
  	if($_POST['trans_lr_date']!='')
      {
        $trans_lr_date = str_replace('/', '-', $_POST['trans_lr_date']);
        $trans_lr_date = date('Y-m-d', strtotime($trans_lr_date));
      }


    $trans_lr_no = $_POST['trans_lr_no'];

    $trans_amount = $_POST['trans_amount'];


	$pay_date = '';
	if($_POST['pay_date']!='')
    {
      $pay_date = str_replace('/', '-', $_POST['pay_date']);
      $pay_date = date('Y-m-d', strtotime($pay_date));
    }

	$tds_per = $_POST['tds_per'];
	$tds_amount = $_POST['tds_amount'];
	$total_amount = $_POST['total_amount'];

    $username= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");


    $sql="INSERT INTO `transport_payout`(`pur_report_id`, `firm_id`, `financial_year_id`, `trans_id`, `ext_party_id`, `invoice_no`, `trans_veh_no`, `trans_lr_date`, `trans_lr_no`, `trans_amount`, `pay_date`, `tds_per`, `tds_amount`, `total_amount`, `username`, `created_at`, `updated_at`) VALUES ('".$pur_report_id."','".$firm_id."','".$financial_year_id."','".$trans_id."','".$ext_party_id."','".$invoice_no."','".$trans_veh_no."','".$trans_lr_date."','".$trans_lr_no."','".$trans_amount."','".$pay_date."','".$tds_per."','".$tds_amount."','".$total_amount."','".$username."','".$timestamp."','".$timestamp."')";

					
			$result = mysqli_query($conn, $sql);
			if($result){
				$successMsg = 'New record added successfully';
				header('Location: index.php');
			}else{
				$errorMsg = 'Error '.mysqli_error($conn);
				echo $errorMsg;
			}
		
  }
?>

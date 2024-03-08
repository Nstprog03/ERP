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
  if (isset($_POST['Submit'])) {

    $farmer = $_POST['farmer'];
    $firm = $_POST['firm'];
    $weight = $_POST['weight'];
    $rate = $_POST['rate'];
    $amount = $_POST['amount'];
    $broker = $_POST['broker'];
    $payment_status = $_POST['payment'];
    $village = $_POST['village'];
    $taluka = $_POST['taluka'];
    $district = $_POST['district'];
    $pur_financial_year = $_POST['pur_financial_year'];


     $date = '';
    if($_POST['date']!='')
    {
      $date = str_replace('/', '-', $_POST['date']);
      $date = date('Y-m-d', strtotime($date));
    }




    $username= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");

    
   

		if(!isset($errorMsg)){
			$sql = "insert into urd_purchase_payment(farmer, firm, weight, rate, amount, broker, payment_status, village, taluka, district,pur_financial_year,username,created_at,updated_at,date)
					values('".$farmer."', '".$firm."', '".$weight."', '".$rate."', '".$amount."', '".$broker."', '".$payment_status."', '".$village."', '".$taluka."', '".$district."', '".$pur_financial_year."', '".$username."', '".$timestamp."', '".$timestamp."', '".$date."')";
			$result = mysqli_query($conn, $sql);
			if($result){
				$successMsg = 'New record added successfully';
				header('Location: index.php');
			}else{
				$errorMsg = 'Error '.mysqli_error($conn);
				echo $errorMsg;
			}
		}
  }
?>

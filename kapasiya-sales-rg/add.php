<?php
session_start();
include('../db.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
if(!isset($_SESSION['kap_firm_id']) && !isset($_SESSION['kap_seasonal_year_id']))
{
  header('Location: index.php');
}

//$assreport_dir = 'files/assessment/';

  if (isset($_POST['Submit'])) {


$srno = $_POST['sr-no'];
$party_name = $_POST['party_name'];
$firm_name = $_POST['firm'];
$notruck = $_POST['no-truck'];
$rate = $_POST['rate'];
$credit = $_POST['credit'];
$broker = $_POST['broker'];
$prod_name = $_POST['prod_name'];

$trucks = $_POST['trucks'];
$sldate = $_POST['sl-date'];
$weight = $_POST['weight'];
$basicamt = $_POST['basic-amt'];
$gst = $_POST['gst'];
$gst_amount = $_POST['gst_amount'];
$tcsper = $_POST['tcs-per'];
$tcsamt = $_POST['tcs-amt'];
$tdsper = $_POST['tds-per'];
$tdsamt = $_POST['tds-amt'];
$finalamt = $_POST['final-amt'];
$invoiceno = $_POST['invoice-no'];
$truck_no = $_POST['truck_no'];
$paymentst = $_POST['payment-st'];

$conf_date='';
if($_POST['conf_date']!='')
{
    $conf_date = str_replace('/', '-',$_POST['conf_date']);
    $conf_date = date('Y-m-d', strtotime($conf_date));
}


 $username= $_SESSION["username"];
date_default_timezone_set('Asia/Kolkata');
$timestamp=date("Y-m-d H:i:s");

$seasonal_yearGet=$_SESSION['kap_seasonal_year_id'];


$truckDataArr=array();
foreach ($trucks as $key => $truck_id) 
{
	$sales_date='';
	if($sldate[$key]!='')
    {
      $sales_date = str_replace('/', '-', $sldate[$key]);
      $sales_date = date('Y-m-d', strtotime($sales_date));
    }

	$truckDataArr[$key]['truck_id']=$truck_id;
	$truckDataArr[$key]['sales_date']=$sales_date;
	$truckDataArr[$key]['weight']=$weight[$key];
	$truckDataArr[$key]['basic_amt']=$basicamt[$key];
	$truckDataArr[$key]['gst_per']=$gst[$key];
	$truckDataArr[$key]['gst_amount']=$gst_amount[$key];
	$truckDataArr[$key]['tcs_per']=$tcsper[$key];
	$truckDataArr[$key]['tcs_amt']=$tcsamt[$key];
	$truckDataArr[$key]['tds_per']=$tdsper[$key];
	$truckDataArr[$key]['tds_amt']=$tdsamt[$key];
	$truckDataArr[$key]['final_amt']=$finalamt[$key];
	$truckDataArr[$key]['invoice_no']=$invoiceno[$key];
	$truckDataArr[$key]['truck_no']=$truck_no[$key];
	$truckDataArr[$key]['payment_status']=$paymentst[$key];

	$truck_complete=0;
	if(isset($_POST['truck_complete'][$key]))
	{
		$truck_complete=1;
	}
	$truckDataArr[$key]['truck_complete']=$truck_complete;

}

$truckDataArr=json_encode($truckDataArr);

	
                      

	
if (isset($_POST['other_day'])){
	$other_day = $_POST['other_day'];
}





$sql = "insert into kapasiya(serialno,firm,party,pro_name,no_of_truck,rate,credit,broker,seasonal_year,conf_date,other_day,truck,username,created_at,updated_at) values('".$srno."', '".$firm_name."', '".$party_name."', '".$prod_name."', '".$notruck."','".$rate."','".$credit."', '".$broker."','".$seasonal_yearGet."','".$conf_date."','".$other_day."','".$truckDataArr."','".$username."','".$timestamp."','".$timestamp."')";
$result = mysqli_query($conn, $sql);
if($result){
	$successMsg = 'New record added successfully';
	header('Location: index1.php');
}else{
	$errorMsg = 'Error '.mysqli_error($conn);
	echo $errorMsg;
}

	
  }
?>

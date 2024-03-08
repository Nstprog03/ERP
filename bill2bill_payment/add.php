<?php
session_start();
include('../db.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
if(!isset($_SESSION['b2bp_firm_id']) && !isset($_SESSION['b2bp_financial_year_id']))
{
  header('Location: index.php');
}
function convertDate($date)
{
  	$final_date='';
	if($date!='')
	{
	    $final_date = str_replace('/', '-',$date);
	    $final_date = date('Y-m-d', strtotime($final_date));
	}
    return $final_date;
}



  if (isset($_POST['Submit'])) {


		$firm_id = $_SESSION['b2bp_firm_id'];
		$financial_year_id = $_SESSION['b2bp_financial_year_id'];

		$total_payment = $_POST['total_payment'];
		$main_label = $_POST['main_label'];
		$main_date = convertDate($_POST['main_date']);


		$name = $_POST['name'];


		$username= $_SESSION["username"];
		date_default_timezone_set('Asia/Kolkata');
		$timestamp=date("Y-m-d H:i:s");


	$dataArr=array();
	if(isset($_POST['report_id']))
	{
		$report_id=$_POST['report_id'];
		$invoice_no=$_POST['invoice_no'];
		$amt_to_be_pay=$_POST['amt_to_be_pay'];
		$payment=$_POST['payment'];
		$label=$_POST['label'];
		$date=$_POST['date'];
		$table=$_POST['table'];
		$party_id=$_POST['party_id'];



		//if table = transport payout then tds per, tds amount get otherwise it will blank.
		$tds_per=$_POST['tds_per'];
		$tds_amount=$_POST['tds_amount'];

		foreach ($report_id as $key => $item) 
		{
			$dataArr[$key]['report_id']=$item;
			$dataArr[$key]['invoice_no']=$invoice_no[$key];
			$dataArr[$key]['amt_to_be_pay']=$amt_to_be_pay[$key];
			$dataArr[$key]['payment']=$payment[$key];
			$dataArr[$key]['label']=$label[$key];
			$dataArr[$key]['date']=convertDate($date[$key]);
			$dataArr[$key]['party_id']=$party_id[$key];
			$dataArr[$key]['table']=$table[$key];

			$dataArr[$key]['tds_per']=$tds_per[$key];
			$dataArr[$key]['tds_amount']=$tds_amount[$key];

		}
	}
	


	
		$sql = "INSERT INTO `bill2bill_payment`(`firm_id`, `financial_year_id`, `total_payment`,`main_label`,`name`,`main_date`, `username`, `created_at`, `updated_at`) VALUES ('".$firm_id."', '".$financial_year_id."', '".$total_payment."','".$main_label."','".$name."','".$main_date."','".$username."','".$timestamp."','".$timestamp."')";
		$result = mysqli_query($conn, $sql);
		if($result){


			$last_id = $conn->insert_id;

			foreach ($dataArr as $key => $item) 
			{
				$sqlSub="INSERT INTO `bill2bill_sub_data`(`table_indicator`, `report_id`, `party_id`, `invoice_no`, `amt_to_be_pay`, `tds_per`, `tds_amount`, `payment`, `label`, `date`, `bill2bill_id`) VALUES ('".$item['table']."', '".$item['report_id']."', '".$item['party_id']."','".$item['invoice_no']."','".$item['amt_to_be_pay']."','".$item['tds_per']."','".$item['tds_amount']."','".$item['payment']."','".$item['label']."','".$item['date']."','".$last_id."')";
				$resultSub = mysqli_query($conn, $sqlSub);

				if($resultSub)
				{
					header('Location: index1.php');
				}

			}
			
		}else{
			$errorMsg = 'Error '.mysqli_error($conn);
			echo $errorMsg;
		}

	
  }
?>

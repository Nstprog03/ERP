<?php
session_start();
include('../db.php');

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}
if(!isset($_SESSION['sales_conf_firm_id']) && !isset($_SESSION['sales_financial_year_id']))
{
  header('Location: ../sales_conf_index.php');
}
//covert To yyyy-mm-dd
function convertDate2($date)
{
  $final_date='';
  if($date!='' && $date!='0000-00-00')
  {
    $final_date = str_replace('/', '-', $date);
    $final_date = date('Y-m-d', strtotime($final_date));
  }
    return $final_date;
}
  if (isset($_POST['submit'])) {
  	
  $firm = $_POST['firm'];
  $financial_year_id = $_SESSION["sales_financial_year_id"];
  $sale_report_id= $_POST['sale_report_id'];
  $conf_no = $_POST['conf_no'];
  $pur_party = $_POST['pur_party'];
	$pur_invoice_no = $_POST['pur_invoice_no'];

	$total_value = $_POST['total_value'];
	$credit_amt = $_POST['credit_amt'];
	$net_amt = $_POST['net_amt'];

	$gross_amt = $_POST['gross_amt'];
	$tax_amt = $_POST['tax_amt'];
	$other_amt_tcs = $_POST['other_amt_tcs'];

	

	$adhoc_data = array();
	$debit_data = array();
	$gst_data= array();
	$tcs_data = array();
	$tds_data = array();
	$other_data = array();

	$adhoc_amount = $_POST['adhoc_amount'];
	$debit_amount = $_POST['debit_amount'];
	$gst_amount = $_POST['gst_amount'];
	$tcs_amount = $_POST['tcs_amount'];
	$tds_amount = $_POST['tds_amount'];
	$other_amount = $_POST['other_amount'];

	$adhoc_date = $_POST['adhoc_date'];
	$debit_date = $_POST['debit_date'];
	$gst_date = $_POST['gst_date'];
	$tcs_date = $_POST['tcs_date'];
	$tds_date = $_POST['tds_date'];
	$other_date = $_POST['other_date'];


	//adhoc
	foreach ($adhoc_amount as $key => $value) 
	{
		if($value!='')
		{
			$date = '';
			if($adhoc_date[$key]!='')
	        {
	          $date = str_replace('/', '-', $adhoc_date[$key]);
	          $date = date('Y-m-d', strtotime($date));
	        }
	        $adhoc_data[$key]['adhoc_amount']=$value;
	        $adhoc_data[$key]['date']=$date;
		}
		
	}

	//debit amount
	foreach ($debit_amount as $key => $value) 
	{
		if($value!='')
		{
			$date = '';
			if($debit_date[$key]!='')
	        {
	          $date = str_replace('/', '-', $debit_date[$key]);
	          $date = date('Y-m-d', strtotime($date));
	        }
	        $debit_data[$key]['debit_amount']=$value;
	        $debit_data[$key]['date']=$date;
		}
		
	}

	//gst amount
	foreach ($gst_amount as $key => $value) 
	{
		if($value!='')
		{
			$date = '';
			if($gst_date[$key]!='')
	        {
	          $date = str_replace('/', '-', $gst_date[$key]);
	          $date = date('Y-m-d', strtotime($date));
	        }
	        $gst_data[$key]['gst_amount']=$value;
	        $gst_data[$key]['date']=$date;
		}
		
	}

	//tcs amount
	foreach ($tcs_amount as $key => $value) 
	{
		if($value!='')
		{
			$date = '';
			if($tcs_date[$key]!='')
	        {
	          $date = str_replace('/', '-', $tcs_date[$key]);
	          $date = date('Y-m-d', strtotime($date));
	        }
	        $tcs_data[$key]['tcs_amount']=$value;
	        $tcs_data[$key]['date']=$date;
		}
		
	}

	//tds amount
	foreach ($tds_amount as $key => $value) 
	{
		if($value!='')
		{
			$date = '';
			if($tds_date[$key]!='')
	        {
	          $date = str_replace('/', '-', $tds_date[$key]);
	          $date = date('Y-m-d', strtotime($date));
	        }
	        $tds_data[$key]['tds_amount']=$value;
	        $tds_data[$key]['date']=$date;
		}
		
	}

	//other amount
	foreach ($other_amount as $key => $value) 
	{
		if($value!='')
		{
			$date = '';
			if($other_date[$key]!='')
	        {
	          $date = str_replace('/', '-', $other_date[$key]);
	          $date = date('Y-m-d', strtotime($date));
	        }
	        $other_data[$key]['other_amount']=$value;
	        $other_data[$key]['date']=$date;
		}
		
	}


	$adhoc_data=json_encode($adhoc_data);
	$debit_data=json_encode($debit_data);
	$gst_data=json_encode($gst_data);
	$tcs_data=json_encode($tcs_data);
	$tds_data=json_encode($tds_data);
	$other_data=json_encode($other_data);


	//bill 2 bill payment dynamic data
	 $b2bArr=array();
	 if(isset($_POST['b2b_id']))
	 {
	 	foreach ($_POST['b2b_id'] as $key => $id) 
	 	{
	 		$b2bArr[$key]['b2b_id']=$id;
	 		$b2bArr[$key]['b2b_label']=$_POST['b2b_label'][$key];
	 		$b2bArr[$key]['b2b_amount']=$_POST['b2b_amount'][$key];
	 		$b2bArr[$key]['b2b_date']=convertDate2($_POST['b2b_date'][$key]);
	 	}
	 }
	 $b2bArr= json_encode($b2bArr);





	
	$total_received = $_POST['total_received'];
	$OSAmount = $_POST['OSAmount'];

	$parakh_date=$_POST['parakh_date'];

	$bill_date = $_POST['bill_date'];
	$bill_no = $_POST['bill_no'];
	$delivery_city = $_POST['delivery_city'];
	$credit_days = $_POST['credit_days'];
	$due_date='';
	$received_date='';
	if($_POST['due_date']!='')
    {
      $due_date = str_replace('/', '-', $_POST['due_date']);
      $due_date = date('Y-m-d', strtotime($due_date));
    }
    if($_POST['received_date']!='')
    {
      $received_date = str_replace('/', '-', $_POST['received_date']);
      $received_date = date('Y-m-d', strtotime($received_date));
    }

    
 
 
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");
    $username=$_SESSION['username'];


     
		
			$sql = "insert into sales_rcvble(pur_party,financial_year_id, pur_invoice_no, total_value, credit_amt, net_amt, adhoc_data, debit_data,gst_data,tcs_data,tds_data,other_data,total_received,OSAmount,firm,credit_days,due_date,received_date,username,create_at,update_at,bill_date,bill_no,delivery_city,conf_no,parakh_date,sale_report_id,gross_amt,tax_amt,other_amt_tcs,bill2bill_dynamic_data)
					values('".$pur_party."','".$financial_year_id."', '".$pur_invoice_no."','".$total_value."','".$credit_amt."','".$net_amt."','".$adhoc_data."','".$debit_data."','".$gst_data."','".$tcs_data."','".$tds_data."','".$other_data."','".$total_received."','".$OSAmount."','".$firm."','".$credit_days."','".$due_date."','".$received_date."','".$username."','".$timestamp."','".$timestamp."','".$bill_date."','".$bill_no."','".$delivery_city."','".$conf_no."','".$parakh_date."','".$sale_report_id."','".$gross_amt."','".$tax_amt."','".$other_amt_tcs."','".$b2bArr."')";
					
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

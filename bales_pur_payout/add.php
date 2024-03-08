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

      $party = $_POST['pur_party'];
      $pur_report_id = $_POST['pur_report_id'];
    $debit_report_id = $_POST['debit_report_id'];
	$pur_invoice_no = $_POST['pur_invoice_no'];
	$invoice_amt = $_POST['invoice_amt'];
	$final_debit_amount = $_POST['final_debit_amount'];
	$tds_amount = $_POST['tds_amount'];
	$net_amt = $_POST['net_amt'];
	$pay_amt = $_POST['pay_amt'];

	$ad_hoc = $_POST['ad_hoc'];

	$gross_amt = $_POST['gross_amt'];
	$tax_amt = $_POST['tax_amt'];
	$tcs_amt = $_POST['tcs_amt'];
	$other_amt = $_POST['other_amt'];

  $debit_report_date='';
  if($_POST['debit_report_date']!='')
  {
    $debit_report_date = str_replace('/', '-',$_POST['debit_report_date']);
	$debit_report_date = date('Y-m-d', strtotime($debit_report_date));
  }


  $ad_hoc_date='';
  if($_POST['ad_hoc_date']!='')
  {
    $ad_hoc_date = str_replace('/', '-',$_POST['ad_hoc_date']);
    $ad_hoc_date = date('Y-m-d', strtotime($ad_hoc_date));
  }
  

	$getFinancialYearID=$_SESSION['pur_financial_year_id'];
    $firm_id= $_SESSION["pur_firm_id"];
    $user= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");

    $dynamic_field=array();
	$label=$_POST['lable'];
	$amt=$_POST['amt'];
	$date=$_POST['dyn_date'];

	foreach ($label as $key => $value) {
			$final_date = ''; 		
			if($date[$key]!='')
    {
      $final_date = str_replace('/', '-', $date[$key]);
      $final_date = date('Y-m-d', strtotime($final_date));
    }
    $dynamic_field[$key]['lable'] = $label[$key];
    $dynamic_field[$key]['amt'] = $amt[$key];
    $dynamic_field[$key]['date'] = $final_date;
	 }
	 $dynamic_field= json_encode($dynamic_field);




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




			$sql = "insert into pur_pay(party, invoice_no,invoice_amt,final_debit_amount,net_amt,dynamic_field,bill2bill_dynamic_data,pur_report_id,firm_id,financial_year_id,username,created_at,updated_at,pay_amt,ad_hoc,ad_hoc_date,gross_amt,tax_amt,tcs_amt,other_amt,tds_amount,debit_report_date,debit_report_id
			)
					values('".$party."','".$pur_invoice_no."','".$invoice_amt."','".$final_debit_amount."','".$net_amt."','".$dynamic_field."','".$b2bArr."','".$pur_report_id."','".$firm_id."','".$getFinancialYearID."','".$user."','".$timestamp."','".$timestamp."','".$pay_amt."','".$ad_hoc."','".$ad_hoc_date."','".$gross_amt."','".$tax_amt."','".$tcs_amt."','".$other_amt."','".$tds_amount."','".$debit_report_date."','".$debit_report_id."')";
					
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

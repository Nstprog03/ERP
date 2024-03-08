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
		$invoice_no = $_POST['pur_invoice_no'];


		$firm_id = $_POST['firm_id'];
		$financial_year_id = $_POST['financial_year_id'];

		$rd_kapas_report_id = $_POST['rd_kapas_report_id'];
		$goods_value = $_POST['goods_value'];
		$debit_amt = $_POST['debit_amt'];
		$party_tds_amt = $_POST['party_tds_amt'];
		$net_amt = $_POST['net_amt'];
		$pay_amt = $_POST['pay_amt'];

		$report_date='';
		if($_POST['report_date']!='')
		{
		  $report_date = str_replace('/', '-', $_POST['report_date']);
          $report_date = date('Y-m-d', strtotime($report_date));
		}
		$tax_amt = $_POST['tax_amt'];
		$tcs_amt = $_POST['tcs_amt'];



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

		 
		 $dynamic_field= json_encode($dynamic_field);
		
			$sql = "insert into rd_kapas_payment(party, invoice_no, firm_id, financial_year_id, goods_value, debit_amt, net_amt,dynamic_field,bill2bill_dynamic_data,rd_kapas_report_id,username,created_at,updated_at,pay_amt,report_date,tcs_amt,tax_amt,party_tds_amt)
					values('".$party."', '".$invoice_no."', '".$firm_id."', '".$financial_year_id."', '".$goods_value."', '".$debit_amt."', '".$net_amt."', '".$dynamic_field."','".$b2bArr."','".$rd_kapas_report_id."', '".$user."', '".$timestamp."', '".$timestamp."', '".$pay_amt."', '".$report_date."', '".$tcs_amt."', '".$tax_amt."', '".$party_tds_amt."')";
					
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

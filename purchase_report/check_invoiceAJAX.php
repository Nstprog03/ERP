<?php
session_start();
require_once('../db.php');
if(isset($_POST['invoice_no']) && isset($_POST['ext_party']))
{
	
	$invoice_no=$_POST['invoice_no'];
	$ext_pary=explode("/",$_POST['ext_party'])[0];
	$sql="select * from pur_report where invoice_no='".$invoice_no."' AND firm='".$_SESSION['pur_firm_id']."' AND financial_year='".$_SESSION['pur_financial_year_id']."' AND party='".$ext_pary."'";
	$result = mysqli_query($conn, $sql);
	$row=mysqli_num_rows($result);

	if($row>0)
	{
		$response['invoice_exist']=true;
	}
	else
	{
		$response['invoice_exist']=false;
	}

	echo json_encode($response);
}

?>
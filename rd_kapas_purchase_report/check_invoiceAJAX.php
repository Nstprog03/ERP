<?php
session_start();
require_once('../db.php');
if(isset($_POST['invoice_no']) && isset($_POST['ext_party']))
{
	
	$invoice_no=$_POST['invoice_no'];
	$sql="select * from rd_kapas_report where invoice_no='".$invoice_no."' AND firm='".$_SESSION['pur_firm_id']."' AND financial_year_id='".$_SESSION['pur_financial_year_id']."' AND external_party='".$_POST['ext_party']."'";
	$result = mysqli_query($conn, $sql);
	$row=mysqli_num_rows($result);

	if($row>0)
	{
		$response['name_exist']=true;
	}
	else
	{
		$response['name_exist']=false;
	}

	echo json_encode($response);
}

?>
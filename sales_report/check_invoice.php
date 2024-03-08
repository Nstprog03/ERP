<?php
session_start();
require_once('../db.php');

if(isset($_POST['invoice_no']))
{
	
	$invoice_no=$_POST['invoice_no'];
	$firm_id=$_SESSION["sales_conf_firm_id"];


	$getDates=explode('/', $_SESSION["sales_conf_financial_year"]);
                          $start_date=$getDates[0];
                          $end_date=$getDates[1];

	$sql_count = "select * from sales_report where firm='".$firm_id."' AND invice_no='".$invoice_no."' AND invoice_date>='".$start_date."' AND invoice_date<='".$end_date."'";

    $result_count = mysqli_query($conn, $sql_count);

    $row_count=mysqli_num_rows($result_count);


	if($row_count>0)
	{
		$response['invoice_found']=true;
	}
	else
	{
		$response['invoice_found']=false;
	}

	echo json_encode($response);
}

?>
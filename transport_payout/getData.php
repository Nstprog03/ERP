<?php
session_start();
require_once('../db.php');

if (isset($_POST['transport_id'])) {


    $getFinancialYearID=$_SESSION['pur_financial_year_id'];
    $firm_id= $_SESSION["pur_firm_id"];
	
	


	//check transport payout
	$idArr=array();
	$sqlCheck="select * from transport_payout where firm_id='".$firm_id."' AND financial_year_id='".$getFinancialYearID."'";
	$resultCheck = mysqli_query($conn, $sqlCheck);
	if(mysqli_num_rows($resultCheck)>0)
	{
		while ($rowGet=mysqli_fetch_assoc($resultCheck)) 
		{
			$cal=$rowGet['trans_amount']-$rowGet['total_amount']-$rowGet['tds_amount'];

	    	if($cal==0)
	    	{
	    		$idArr[]=$rowGet['pur_report_id'];
	    	}
			
		}
	}



	$sql="select id,trans_lr_no,invoice_no from pur_report where trans_id='".$_POST['transport_id']."' AND firm='".$firm_id."' AND financial_year='".$getFinancialYearID."' AND trans_pay_type='to_be_pay' AND trans_lr_no!= '' ";

	$result = mysqli_query($conn, $sql);
	$Invoice=array();
	foreach ($conn->query($sql) as $key => $row) 
    {
    		if(!in_array($row['id'], $idArr))
    		{
    			$Invoice[$key]['trans_lr_no'] =  $row['trans_lr_no']; 
    			$Invoice[$key]['invoice_no'] =  $row['invoice_no'];	
				$Invoice[$key]['pur_report_id'] =  $row['id']; 	
    		}
	}
	 echo json_encode($Invoice);
}
?>
<?php
session_start();
require_once('../db.php');

if (isset($_POST['party'])) 
{

	$party= $_POST['party'];
	//get data from bales payout
	$idArr=array();
	$sqlCheck="select debit_report_id from pur_pay where pay_amt IN('0.00','0') AND party='".$party."'";
	$resultCheck = mysqli_query($conn, $sqlCheck);
	if(mysqli_num_rows($resultCheck)>0)
	{
		while ($rowGet=mysqli_fetch_assoc($resultCheck)) 
		{
			$idArr[]=$rowGet['debit_report_id'];
		}
	}

	
	$sql="select id,invoice_no from debit_report where party='".$party."' AND invoice_no != '' AND firm='".$_SESSION['pur_firm_id']."' AND financial_year='".$_SESSION['pur_financial_year_id']."'";

	$result = mysqli_query($conn, $sql);
	
	foreach ($conn->query($sql) as $result) 
    {

    	if(!in_array($result['id'], $idArr))
    	{
    		$Invoice[] =  $result['invoice_no']; 
    	}

	}
	 echo json_encode($Invoice);
}


//get bill 2 bill data

if(isset($_POST['report_id']) && isset($_POST['getB2Bdata']))
{
  $data=array();
  $sqlb2b="select * from bill2bill_sub_data where table_indicator='pur_bales_payout' AND report_id='".$_POST['report_id']."'";
  $resultb2b=mysqli_query($conn,$sqlb2b);
  if(mysqli_num_rows($resultb2b)>0)
  {
    while ($rowb2b=mysqli_fetch_assoc($resultb2b)) 
    {
        $data[]=$rowb2b;                    
    }
  }
   echo json_encode($data);
}



?>
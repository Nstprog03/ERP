<?php
session_start();
require_once('../db.php');

if(isset($_POST['party']))
{

	$party=$_POST['party'];


	//get data from rd kapas payment if full payment is done
	$idArr=array();
	$sqlCheck="select rd_kapas_report_id from rd_kapas_payment where pay_amt IN('0.00','0') AND party='".$party."'";
	$resultCheck = mysqli_query($conn, $sqlCheck);
	if(mysqli_num_rows($resultCheck)>0)
	{
		while ($rowGet=mysqli_fetch_assoc($resultCheck)) 
		{
			$idArr[]=$rowGet['rd_kapas_report_id'];
		}
	}




	$sql = "select id,invoice_no from rd_kapas_report where external_party='".$party."' AND firm='".$_SESSION['pur_firm_id']."' AND financial_year_id='".$_SESSION['pur_financial_year_id']."'";

	 $row=array();
	  foreach ($conn->query($sql) as $key=> $result) 
	  {
	 	if(!in_array($result['id'], $idArr))
    	{
    		$row[] =  $result; 
    	}
	  	
	  }
	  
	echo json_encode($row);

}



//get bill 2 bill data

if(isset($_POST['report_id']) && isset($_POST['getB2Bdata']))
{
  $data=array();
  $sqlb2b="select * from bill2bill_sub_data where table_indicator='rd_kapas_pur_payment' AND report_id='".$_POST['report_id']."'";
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
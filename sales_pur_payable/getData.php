<?php
session_start();
require_once('../db.php');
$firm_id=$_SESSION["sales_conf_firm_id"];
$year_array=explode("/",$_SESSION['sales_conf_financial_year']);

$start_date=$year_array[0];
$end_date=$year_array[1];

if (isset($_POST['party'])) 
{

	$party= $_POST['party'];


	//get data from  sales_rcvble
	$idArr=array();
	$sqlCheck="select sale_report_id from sales_rcvble where OSAmount IN('0.00','0') AND pur_party='".$party."'";
	$resultCheck = mysqli_query($conn, $sqlCheck);
	if(mysqli_num_rows($resultCheck)>0)
	{
		while ($rowGet=mysqli_fetch_assoc($resultCheck)) 
		{
			$idArr[]=$rowGet['sale_report_id'];
		}
	}
	

	

	$sql="select id,invice_no from sales_report where party_name='".$party."' AND invice_no != '' AND financial_year_id='".$_SESSION['sales_financial_year_id']."' AND firm='".$firm_id."' ";

	

	$result = mysqli_query($conn, $sql);
	
	foreach ($conn->query($sql) as $result) 
    {
    	if(!in_array($result['id'], $idArr))
    	{
    		$Invoice[] = $result['invice_no']; 		
    	}
	}
	
	echo json_encode($Invoice);
}



//get bill 2 bill data

if(isset($_POST['report_id']) && isset($_POST['getB2Bdata']))
{
  $data=array();
  $sqlb2b="select * from bill2bill_sub_data where table_indicator='sales_receivable' AND report_id='".$_POST['report_id']."'";
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
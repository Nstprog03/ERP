<?php
session_start();
require_once('../db.php');

if(isset($_POST['party']))
{

	$party=$_POST['party'];

	 $sql = "SELECT invoice_no,id,conf_no FROM pur_report where party='".$party."' AND firm='".$_SESSION['pur_firm_id']."' AND financial_year='".$_SESSION['pur_financial_year_id']."'";

	$result = mysqli_query($conn, $sql);
	$row =array();


	 $sql2 = "select invoice_no from debit_report where party='".$party."' AND firm='".$_SESSION['pur_firm_id']."' AND financial_year='".$_SESSION['pur_financial_year_id']."'";

    $result2 = mysqli_query($conn, $sql2);
    $InvoiceArr=array();
    while($row2 = mysqli_fetch_assoc($result2)){
           $InvoiceArr[] = $row2['invoice_no'];
                
    }


	  foreach ($conn->query($sql) as $key=> $result) 
	  {
	  	 if (!in_array($result['invoice_no'],$InvoiceArr)) 
	  	 {
  	 		$row[$key]['pur_report_id']=$result['id'];
		  	$row[$key]['invoice_no']=$result['invoice_no'];
		  	$row[$key]['pur_conf_no']=$result['conf_no'];
	  	 }

	  	
	  }
	echo json_encode($row);

}

?>
<?php
session_start();
require_once('../db.php');

if(isset($_POST['invoice_no']) && isset($_POST['party_id']))
{
	
	$invoice_no=$_POST['invoice_no'];
	$party_id=$_POST['party_id'];

	$sql="select * from debit_report where invoice_no='".$party_id."' AND party='".$party_id."'";
	
	$result = mysqli_query($conn, $sql);

	$row=mysqli_num_rows($result);


	echo json_encode($row);

}

?>
<?php
require_once('../db.php');
if (isset($_POST['conf_no']) && isset($_POST['table'])) {

	$selectedLOT=$_POST['sel_lot_no'];

	if($_POST['table'] == 'conf') {

		$conf_no=$_POST['conf_no'];
		$sql="select lot_no,lot_bales from seller_conf where sales_conf = '".$conf_no."'";
		$result = mysqli_query($conn, $sql);
		$row = $result->fetch_assoc();

		$data=json_decode($row['lot_bales']);

		$response['bales']=$data[$selectedLOT-1];

		echo json_encode($response);
		
	}else if ($_POST['table'] == 'confsplit') {

		$conf_no=$_POST['conf_no'];
		$sql="select lot_no,lot_bales from sales_conf_split where conf_split_no = '".$conf_no."'";
		$result = mysqli_query($conn, $sql);
		$row = $result->fetch_assoc();
		
		$data=json_decode($row['lot_bales']);

		$response['bales']=$data[$selectedLOT-1];

		echo json_encode($response);
	}

}		
?>
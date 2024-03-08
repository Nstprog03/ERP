<?php
require_once('../db.php');
if (isset($_POST['conf_no']) && isset($_POST['table'])) {
	


	if($_POST['table'] == 'conf') {

		$conf_no=$_POST['conf_no'];
		$sql="select * from seller_conf where sales_conf = '".$conf_no."'";
		$result = mysqli_query($conn, $sql);
		$row = $result->fetch_assoc();
		echo json_encode($row);
		
	}elseif ($_POST['table'] == 'confsplit') {

		$conf_no=$_POST['conf_no'];
		$sql="select sp.*,s.candy_rate from sales_conf_split sp, seller_conf s where sp.conf_no=s.sales_conf AND sp.conf_split_no = '".$conf_no."'";
		$result = mysqli_query($conn, $sql);
		$row = $result->fetch_assoc();
		echo json_encode($row);
	}

	


}		




?>
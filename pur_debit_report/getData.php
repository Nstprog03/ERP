<?php

require_once('../db.php');

if(isset($_POST['conf_no']))
{
	

	$id=$_POST['id'];

	$sql="select * from pur_report where id='".$id."'";
	$result = mysqli_query($conn, $sql);
	$row['pur_data'] = $result->fetch_assoc();


	$conf_no=$_POST['conf_no'];
	$sql2="select * from pur_conf where pur_conf='".$conf_no."'";
	$result2 = mysqli_query($conn, $sql2);
	$row['conf_data'] = $result2->fetch_assoc();

	$sql3="select id,name from broker where id='".$row['pur_data']['broker']."'";
	$result3 = mysqli_query($conn, $sql3);
	

	foreach ($result3 as $key => $row_broker) {
	 	$row['pur_data']['broker_id']=$row_broker['id'];
	 	$row['pur_data']['broker_name']=$row_broker['name'];
	 } 
	

	echo json_encode($row);

}

?>
<?php

require_once('../db.php');

if(isset($_POST['record_id']))
{
	
	$pur_conf_id=$_POST['record_id'];

	$sql="select * from pur_conf where id='".$pur_conf_id."'";

	$sql2="SELECT SUM(bales) as used_bales FROM pur_report WHERE pur_conf_ids='".$pur_conf_id."'";


	$result = mysqli_query($conn, $sql);
	$result2 = mysqli_query($conn, $sql2);

	$row = $result->fetch_assoc();

	$sql3="select id,name from broker where id='".$row['broker']."'";
	$result3 = mysqli_query($conn, $sql3);
	$row3=array();

	foreach ($result3 as $key => $row_broker) {
	 	$row3['broker_id']=$row_broker['id'];
	 	$row3['broker_name']=$row_broker['name'];
	 } 

	$row+=$row3;
	$row += $result2->fetch_assoc();




	//get transport name
	$sql_trans="select id,trans_name from transport where id='".$row['trans_name']."'";
	$result_trans = mysqli_query($conn, $sql_trans);


	$row4=array();
	foreach ($result_trans as $key => $row_trans) {
	 	$row4['transport_name']=$row_trans['trans_name'];
	 } 

	$row+=$row4;




	echo json_encode($row);
}

?>
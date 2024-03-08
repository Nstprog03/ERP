<?php

require_once('../db.php');

if(isset($_POST['firm_id']) && isset($_POST['fin_year']))
{
	
	$sql="select party_shortform from party where id='".$_POST['firm_id']."'";
	$result = mysqli_query($conn, $sql);
	$row = $result->fetch_assoc();
	$getShortForm=$row['party_shortform'];

	/*$query_GetLastID="SELECT max(id) as last_id FROM pur_conf";
	$result_GetLastID = mysqli_query($conn, $query_GetLastID);
	$row_GetLastID = mysqli_fetch_assoc($result_GetLastID);
	$nextID=$row_GetLastID['last_id']+1;*/

	$getMainConfNo=explode("-",$_POST['main_conf_no'])[2];

	$sql="select * from financial_year where id='".$_POST['fin_year']."'";
	$result = mysqli_query($conn, $sql);
	$rowYear = $result->fetch_assoc();
	

	$syr = date("y", strtotime($rowYear['startdate']));
    $eyr = date("y", strtotime($rowYear['enddate']));


    $response['new_conf_no']=$getShortForm.'-'.$syr.$eyr.'-'.$getMainConfNo;



	echo json_encode($response);
}

?>
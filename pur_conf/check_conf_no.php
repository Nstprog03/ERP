<?php

require_once('../db.php');

if(isset($_POST['conf_no']))
{
	$conf_no=$_POST['conf_no'];

	$sql="select * from pur_conf where pur_conf='".$conf_no."'";

	$result = mysqli_query($conn, $sql);

	$rowcount=mysqli_num_rows($result);

	if($rowcount>0)
	{
		$response['status']=true;
		
	}
	else
	{
		$response['status']=false;
	}

	echo json_encode($response);
}

?>
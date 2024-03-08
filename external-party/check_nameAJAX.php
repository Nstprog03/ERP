<?php

require_once('../db.php');

if(isset($_POST['name']))
{
	

	$name=$_POST['name'];

	$sql="select * from external_party where partyname='".$name."'";
	
	$result = mysqli_query($conn, $sql);

	$row=mysqli_num_rows($result);


	if($row>0)
	{
		$response['name_exist']=true;
	}
	else
	{
		$response['name_exist']=false;
	}

	echo json_encode($response);
}

?>
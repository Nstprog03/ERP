<?php

require_once('../db.php');

if(isset($_POST['prod_name']))
{
	

	$prod_name=$_POST['prod_name'];

	$sql="select * from products where prod_name='".$prod_name."'";
	
	$result = mysqli_query($conn, $sql);

	$row=mysqli_num_rows($result);


	if($row>0)
	{
		$response['product_found']=true;
	}
	else
	{
		$response['product_found']=false;
	}

	echo json_encode($response);
}

?>
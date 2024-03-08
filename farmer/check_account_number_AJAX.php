<?php



require_once('../db.php');



if(isset($_POST['ac_no']))

{

	$ac_no=$_POST['ac_no'];

	$sql="select * from farmer where ac_no='".$ac_no."'";

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
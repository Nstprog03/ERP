<?php
require_once('../db.php');

if(isset($_POST['party_id']))

{
	$party_id=$_POST['party_id'];

	$sql="select gstin from external_party where id='".$party_id."'";

	$result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result)>0)
	{
        $rowData=mysqli_fetch_assoc($result);
        $response['gstin_data']=$rowData['gstin'];
		$response['status']=true;
	}
	else
	{
		$response['status']=false;
	}

	echo json_encode($response);

}

?>
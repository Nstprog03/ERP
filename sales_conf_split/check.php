<?php
require_once('../db.php');
 if (isset($_POST['conf_no'])) {
 	$data = $_POST['conf_no'];
	$conf_no =explode('/',$data)[1];
	$alphas = range('A', 'Z');
	$sql="select conf_split_no from sales_conf_split where conf_no = '".$conf_no."'";
	$sub=[];
	$result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
	if ($row>0) {
		foreach ($conn->query($sql) as $row)    
	    {
			foreach ($row as $value) {
				$sub = substr($value, -1 );
			}
			$i = $sub;
			$i++;
			$alpha = $i;
		}
		$final['conf_split_no'] =  $conf_no.'-'.$alpha;
		echo json_encode($final);
	}else{
		// $i = 'A';
		// $i++;
		// $alpha = $i;
		$final['conf_split_no'] =  $conf_no.'-A';
		echo json_encode($final);
	}

}


?>
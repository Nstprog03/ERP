<?php
session_start();
include('../db.php');
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../login.php");
    exit;
}

  if (isset($_POST['Submit'])) {
    $prod_name = $_POST['prod_name'];
    $prod_hsn = $_POST['prod_hsn'];
    $prod_rate = $_POST['prod_rate'];

   
    $prod_quality = $_POST['prod_quality'];
    $prod_variety = $_POST['prod_variety'];
    $prod_sub_variety = $_POST['prod_sub_variety'];


    $username= $_SESSION["username"];
    date_default_timezone_set('Asia/Kolkata');
    $timestamp=date("Y-m-d H:i:s");

   
		if(!isset($errorMsg)){
			$sql = "insert into products(prod_name, prod_hsn, prod_rate,username,created_at,updated_at)
					values('".$prod_name."', '".$prod_hsn."', '".$prod_rate."','".$username."','".$timestamp."', '".$timestamp."')";

			$result = mysqli_query($conn, $sql);

			if($result)
			{

				$last_id = $conn->insert_id;

				foreach ($prod_quality as $key => $value) 
				{
					$sql_pq="insert into product_sub_items(product_id,indicator,value) values('".$last_id."','1','".$value."')";
					$result_pq = mysqli_query($conn, $sql_pq);
				}
				foreach ($prod_variety as $key => $value) {

					$sql_pv="insert into product_sub_items(product_id,indicator,value) values('".$last_id."','2','".$value."')";
					$result_pv = mysqli_query($conn, $sql_pv);
					
				}
				foreach ($prod_sub_variety as $key => $value) {

					$sql_sv="insert into product_sub_items(product_id,indicator,value) values('".$last_id."','3','".$value."')";
					$result_sv = mysqli_query($conn, $sql_sv);
					
				}


				$successMsg = 'New record added successfully';
				header('Location: index.php');



			}else{
				$errorMsg = 'Error '.mysqli_error($conn);
				echo $errorMsg;
			}
		}
  }
?>
